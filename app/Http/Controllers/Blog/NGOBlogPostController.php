<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Carbon\Carbon;

class NGOBlogPostController extends Controller
{
    // List NGO posts
    public function index()
    {
        $posts = BlogPost::where('user_id', Auth::id())
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('blogs.index', compact('posts'));
    }

    // Show create form
    public function create()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('ngo.blogs.create', compact('categories'));
    }

    // Store new post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'required|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ]);

        // Normalize publish date
        if ($request->filled('published_at')) {
            try {
                $validated['published_at'] = Carbon::parse($request->input('published_at'))->toDateTimeString();
            } catch (\Throwable $e) {
                $validated['published_at'] = null;
            }
        } else {
            $validated['published_at'] = null;
        }

        // Handle image upload
        $imageFileName = null;
        if ($request->hasFile('image') || $request->hasFile('blogImage') || $request->hasFile('blog_image')) {
            $file = $request->hasFile('image') ? $request->file('image')
                  : ($request->hasFile('blogImage') ? $request->file('blogImage') : $request->file('blog_image'));

            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $destFolder = public_path('images/Blog');
            if (!is_dir($destFolder)) {
                mkdir($destFolder, 0755, true);
            }
            $file->move($destFolder, $imageFileName);
        }

        // Create post
        BlogPost::create([
            'blogPost_id'  => (string) Str::uuid(),
            'user_id'      => Auth::id(),
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'image'        => $imageFileName,
            'status'       => $validated['status'],
            'published_at' => ($validated['status'] === 'published' && empty($validated['published_at']))
                ? now()
                : $validated['published_at'],
        ]);

        return redirect()->route('ngo.blogs.index')->with('success', 'Blog post created.');
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

        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'nullable|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ]);

        // Normalize published_at
        if ($request->filled('published_at')) {
            try {
                $validated['published_at'] = Carbon::parse($request->input('published_at'))->toDateTimeString();
            } catch (\Throwable $e) {
                $validated['published_at'] = null;
            }
        } else {
            $validated['published_at'] = $post->published_at;
        }

        // Handle image replacement
        if ($request->hasFile('image') || $request->hasFile('blogImage') || $request->hasFile('blog_image')) {
            if ($post->image) {
                $oldBasename = basename($post->image);
                $oldPath = public_path('images/Blog/' . $oldBasename);
                if ($oldBasename !== 'default-blog.jpg' && file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->hasFile('image') ? $request->file('image')
                  : ($request->hasFile('blogImage') ? $request->file('blogImage') : $request->file('blog_image'));

            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $destFolder = public_path('images/Blog');
            if (!is_dir($destFolder)) {
                mkdir($destFolder, 0755, true);
            }
            $file->move($destFolder, $imageFileName);

            $post->image = $imageFileName;
        }

        $post->title       = $validated['title'];
        $post->summary     = $validated['summary'] ?? $post->summary;
        $post->content     = $validated['content'];
        $post->category_id = $validated['category_id'] ?? $post->category_id;
        $post->status      = $validated['status'];

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

        return redirect()->route('blogs.show', $post->blogPost_id)->with('success', 'Blog post updated.');
    }

    // Delete
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->user_id !== Auth::id()) {
            abort(403);
        }

        if ($post->image) {
            $basename = basename($post->image);
            $path = public_path('images/Blog/' . $basename);
            if ($basename !== 'default-blog.jpg' && file_exists($path)) {
                @unlink($path);
            }
        }

        $post->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog post deleted.');
    }
}
