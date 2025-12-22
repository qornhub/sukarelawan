<?php

namespace App\Http\Controllers\Blog;

use Carbon\Carbon;
use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NGOBlogPostController extends Controller
{
    // Show create form
    public function create()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('ngo.blogs.create', compact('categories'));
    }

    // Store new post
    public function store(Request $request)
    {
        // FIXED VALIDATION
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'blogSummary'      => 'nullable|string|max:300',
            'content'          => 'required|string',

            'category_id'      => 'required',
            'custom_category'  => 'nullable|string|max:255|required_if:category_id,other',

            'status'           => 'required|in:draft,published',
            'published_at'     => 'nullable|date',
            'image'            => 'nullable|image|max:5120',
        ]);

        // CATEGORY LOGIC (FK SAFE)
        if ($validated['category_id'] === 'other') {
            $categoryId     = null;
            $customCategory = $validated['custom_category'];
        } else {
            // SAFELY CHECK DB using `blogCategory_id`
            $category = BlogCategory::where('blogCategory_id', $validated['category_id'])->first();

            if ($category) {
                $categoryId     = $category->blogCategory_id;
                $customCategory = null;
            } else {
                // fallback to prevent FK error
                $categoryId     = null;
                $customCategory = null;
            }
        }

        // Normalize published_at
        $validated['published_at'] = $request->filled('published_at')
            ? Carbon::parse($request->published_at)->toDateTimeString()
            : null;

        // DEFAULT IMAGE HANDLING
        $imageFileName = "default_blog.jpg";

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $dest = public_path('images/Blog');
            if (!is_dir($dest)) mkdir($dest, 0755, true);

            $file->move($dest, $imageFileName);
        }

        // Create post
        BlogPost::create([
            'blogPost_id'     => (string) Str::uuid(),
            'user_id'         => Auth::id(),
            'category_id'     => $categoryId,
            'custom_category' => $customCategory,
            'title'           => $validated['title'],
            'blogSummary'     => $validated['blogSummary'] ?? null,
            'content'         => $validated['content'],
            'image'           => $imageFileName,
            'status'          => $validated['status'],
            'published_at'    => ($validated['status'] === 'published' && empty($validated['published_at']))
                ? now()
                : $validated['published_at'],
        ]);

        return redirect()->route('blogs.index')->with('success', 'Blog post created.');
    }

    // Show edit form
    public function edit($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('ngo.blogs.edit', compact('post', 'categories'));
    }

    // Update existing post
    public function update(Request $request, $id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        // FIXED VALIDATION
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'blogSummary'      => 'nullable|string|max:300',
            'content'          => 'required|string',

            'category_id'      => 'required',
            'custom_category'  => 'nullable|string|max:255|required_if:category_id,other',

            'status'           => 'required|in:draft,published',
            'published_at'     => 'nullable|date',
            'image'            => 'nullable|image|max:5120',
        ]);

        // CATEGORY LOGIC (FK SAFE)
        if ($validated['category_id'] === 'other') {
            $post->category_id     = null;
            $post->custom_category = $validated['custom_category'];
        } else {
            $category = BlogCategory::where('blogCategory_id', $validated['category_id'])->first();

            if ($category) {
                $post->category_id     = $category->blogCategory_id;
                $post->custom_category = null;
            } else {
                $post->category_id     = null;
                $post->custom_category = null;
            }
        }

        // Normalize published_at
        if ($request->filled('published_at')) {
            $validated['published_at'] =
                Carbon::parse($request->published_at)->toDateTimeString();
        }

        // IMAGE HANDLING
        if ($request->hasFile('image')) {

            if ($post->image && $post->image !== "default_blog.jpg") {
                $oldPath = public_path('images/Blog/' . $post->image);
                if (file_exists($oldPath)) unlink($oldPath);
            }

            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $dest = public_path('images/Blog');
            if (!is_dir($dest)) mkdir($dest, 0755, true);

            $file->move($dest, $imageFileName);
            $post->image = $imageFileName;
        }

        if (!$post->image) {
            $post->image = "default_blog.jpg";
        }

        // Update normal fields
        $post->title       = $validated['title'];
        $post->blogSummary = $validated['blogSummary'];
        $post->content     = $validated['content'];
        $post->status      = $validated['status'];

        // Published/draft logic
        if ($post->status === 'published' && empty($validated['published_at'])) {
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
        } elseif ($post->status === 'draft') {
            $post->published_at = null;
        } else {
            $post->published_at = $validated['published_at'];
        }

        $post->save();

        return redirect()->route('blogs.show', $post->blogPost_id)
            ->with('success', 'Blog post updated.');
    }

    // Delete
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        if ($post->image && $post->image !== "default_blog.jpg") {
            $path = public_path('images/Blog/' . $post->image);
            if (file_exists($path)) unlink($path);
        }

        $post->delete();

        return redirect()->route('blogs.index')
            ->with('success', 'Blog post deleted.');
    }

    public function manage($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();
        $comments = BlogComment::where('blogPost_id', $post->blogPost_id)
            ->orderBy('created_at', 'asc')
            ->paginate(3, ['*'], 'comments_page')
            ->withQueryString();

        if (!Auth::check() || Auth::id() !== $post->user_id) {
            return redirect()->route('blogs.show', $post->blogPost_id);
        }

        if ($post->status === 'draft') {
            return redirect()->route('ngo.blogs.edit', $post->blogPost_id);
        }

        return view('ngo.blogs.blogEditDelete', compact('post', 'comments'));
    }
}
