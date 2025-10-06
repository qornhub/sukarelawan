<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Carbon\Carbon;

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
        // Validate request (use controller validation because textarea is handled by TinyMCE)
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'required|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ]);

        // Normalize published_at (datetime-local from browser -> Y-m-d H:i:s)
        if ($request->filled('published_at')) {
            try {
                $validated['published_at'] = Carbon::parse($request->input('published_at'))->toDateTimeString();
            } catch (\Throwable $e) {
                // leave as-is; validation above should have caught invalid dates
            }
        } else {
            $validated['published_at'] = null;
        }

        // Image handling (same style as your Event controller)
        $imageFileName = null;
        if ($request->hasFile('image') || $request->hasFile('blogImage') || $request->hasFile('blog_image')) {
            $file = $request->hasFile('image') ? $request->file('image')
                  : ($request->hasFile('blogImage') ? $request->file('blogImage') : $request->file('blog_image'));

            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $destFolder = public_path('images/Blog');
            if (! is_dir($destFolder)) {
                mkdir($destFolder, 0755, true);
            }

            $file->move($destFolder, $imageFileName);
        }

        // Prepare payload
        $payload = [
            'blogPost_id'  => (string) Str::uuid(),
            'user_id'      => Auth::id(),
            'category_id'  => $validated['category_id'],
            'title'        => $validated['title'],
            'summary'      => $validated['summary'] ?? null,
            'content'      => $validated['content'],
            'image'        => $imageFileName,
            'status'       => $validated['status'],
            'published_at' => ($validated['status'] === 'published' && empty($validated['published_at'])) ? now() : $validated['published_at'],
        ];

        BlogPost::create($payload);

        return redirect()->route('blogs.index')->with('success', 'Blog post created.');
    }

    // Show edit form
    public function edit($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        // owner only (volunteer) — volunteers can only edit their own posts
        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('volunteer.blogs.edit', compact('post','categories'));
    }

    // Update
    public function update(Request $request, $id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        // Validate input
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'nullable|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ]);

        // Normalize published_at if provided
        if ($request->filled('published_at')) {
            try {
                $validated['published_at'] = Carbon::parse($request->input('published_at'))->toDateTimeString();
            } catch (\Throwable $e) {
                // ignore; validator should have prevented invalid date
            }
        } else {
            // keep existing published_at if not provided and not changing to draft
            if (!isset($validated['published_at'])) {
                $validated['published_at'] = $post->published_at;
            }
        }

        // Image handling (replace if uploaded)
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
            if (! is_dir($destFolder)) {
                mkdir($destFolder, 0755, true);
            }
            $file->move($destFolder, $imageFileName);
            $post->image = $imageFileName;
        }

        // Update fields
        $post->category_id = $validated['category_id'] ?? $post->category_id;
        $post->title       = $validated['title'];
        $post->summary     = $validated['summary'] ?? $post->summary;
        $post->content     = $validated['content'];
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

    // Delete own post
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if (Auth::id() !== $post->user_id) {
            abort(403);
        }

        $defaultBlogImage = 'default-blog.jpg';
        if ($post->image) {
            $basename = basename($post->image);
            $path = public_path('images/Blog/' . $basename);
            if ($basename !== $defaultBlogImage && file_exists($path)) {
                @unlink($path);
            }
        }

        $post->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog post deleted.');
    }
}
