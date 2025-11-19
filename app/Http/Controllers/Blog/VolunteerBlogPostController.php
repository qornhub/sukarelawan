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

class VolunteerBlogPostController extends Controller
{
    // Show create form
    public function create()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('volunteer.blogs.create', compact('categories'));
    }

    // Store new blog post (volunteer)
    public function store(Request $request)
    {
        // Validation (fixed)
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'blogSummary'      => 'nullable|string|max:300',
            'content'          => 'required|string',
            'category_id'      => 'required|string',
            'custom_category'  => 'required_if:category_id,other|string|max:255',
            'status'           => 'required|in:draft,published',
            'published_at'     => 'nullable|date',
            'image'            => 'nullable|image|max:5120',
        ]);

        // Normalize published_at
        $validated['published_at'] =
            $request->filled('published_at')
            ? Carbon::parse($request->published_at)->toDateTimeString()
            : null;

        // Handle image upload
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $destFolder = public_path('images/Blog');
            if (!is_dir($destFolder)) mkdir($destFolder, 0755, true);
            $file->move($destFolder, $imageFileName);
        }

        // Build payload (PRIVATE CATEGORY LOGIC)
        $payload = [
            'blogPost_id'   => (string) Str::uuid(),
            'user_id'       => Auth::id(),
            'title'         => $validated['title'],
            'blogSummary'   => $validated['blogSummary'] ?? null,
            'content'       => $validated['content'],
            'image'         => $imageFileName,
            'status'        => $validated['status'],
        ];

        if ($validated['category_id'] === 'other') {
            $payload['category_id'] = null;
            $payload['custom_category'] = $validated['custom_category'];
        } else {
            $payload['category_id'] = $validated['category_id'];
            $payload['custom_category'] = null;
        }

        // Handle publish date
        $payload['published_at'] =
            ($validated['status'] === 'published' && empty($validated['published_at']))
            ? now()
            : $validated['published_at'];

        BlogPost::create($payload);

        return redirect()->route('blogs.index')->with('success', 'Blog post created.');
    }

    // Show edit form
    public function edit($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('volunteer.blogs.edit', compact('post','categories'));
    }

    // Update post
    public function update(Request $request, $id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        // Validation
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'blogSummary'      => 'nullable|string|max:300',
            'content'          => 'required|string',
            'category_id'      => 'required|string',
            'custom_category'  => 'required_if:category_id,other|string|max:255',
            'status'           => 'required|in:draft,published',
            'published_at'     => 'nullable|date',
            'image'            => 'nullable|image|max:5120',
        ]);

        // Normalize published_at
        if ($request->filled('published_at')) {
            $validated['published_at'] = Carbon::parse($request->published_at)->toDateTimeString();
        }

        // Handle image replacement
        if ($request->hasFile('image')) {
            if ($post->image) {
                $old = public_path('images/Blog/' . basename($post->image));
                if (file_exists($old)) unlink($old);
            }

            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $destFolder = public_path('images/Blog');
            if (!is_dir($destFolder)) mkdir($destFolder, 0755, true);

            $file->move($destFolder, $imageFileName);
            $post->image = $imageFileName;
        }

        // Update normal fields
        $post->title       = $validated['title'];
        $post->blogSummary = $validated['blogSummary'];
        $post->content     = $validated['content'];
        $post->status      = $validated['status'];

        // CATEGORY LOGIC (PRIVATE)
        if ($validated['category_id'] === 'other') {
            $post->category_id     = null;
            $post->custom_category = $validated['custom_category'];
        } else {
            $post->category_id     = $validated['category_id'];
            $post->custom_category = null;
        }

        // Publish logic
        if ($post->status === 'published' && empty($post->published_at)) {
            $post->published_at = now();
        } elseif ($post->status === 'draft') {
            $post->published_at = null;
        } else {
            $post->published_at = $validated['published_at'];
        }

        $post->save();

        return redirect()->route('blogs.show', $post->blogPost_id)->with('success', 'Blog post updated.');
    }

    // Delete
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        if ($post->image) {
            $path = public_path('images/Blog/' . basename($post->image));
            if (file_exists($path)) unlink($path);
        }

        $post->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog post deleted.');
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
            return redirect()->route('volunteer.blogs.edit', $post->blogPost_id);
        }

        return view('volunteer.blogs.blogEditDelete', compact('post', 'comments'));
    }
}
