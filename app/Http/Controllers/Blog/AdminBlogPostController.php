<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class AdminBlogPostController extends Controller
{
    public function create()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $input = [
            'title'        => $request->input('title'),
            'summary'      => $request->input('summary'),
            'content'      => $request->input('content'),
            'category_id'  => $request->input('category_id'),
            'status'       => $request->input('status', 'draft'),
            'published_at' => $request->input('published_at', null),
        ];

        $rules = [
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'required|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ];

        $validator = Validator::make($input + $request->only('image','blogImage','blog_image'), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle image upload
        $imageFileName = null;
        if ($request->hasFile('image') || $request->hasFile('blogImage') || $request->hasFile('blog_image')) {
            $file = $request->hasFile('image') ? $request->file('image')
                  : ($request->hasFile('blogImage') ? $request->file('blogImage') : $request->file('blog_image'));
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;
            $destFolder = public_path('images/Blog');
            if (! is_dir($destFolder)) mkdir($destFolder, 0755, true);
            $file->move($destFolder, $imageFileName);
        }

        $payload = [
            'blogPost_id'  => (string) Str::uuid(),
            'user_id'      => Auth::id(),
            'category_id'  => $input['category_id'],
            'title'        => $input['title'],
            'summary'      => $input['summary'],
            'content'      => $input['content'],
            'image'        => $imageFileName,
            'status'       => $input['status'],
            'published_at' => ($input['status'] === 'published' && empty($input['published_at'])) ? now() : $input['published_at'],
        ];

        BlogPost::create($payload);

        return redirect()->route('blogs.index')->with('success', 'Blog post created.');
    }

    public function edit($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('admin.blogs.edit', compact('post','categories'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        $input = [
            'title'        => $request->input('title'),
            'summary'      => $request->input('summary'),
            'content'      => $request->input('content'),
            'category_id'  => $request->input('category_id'),
            'status'       => $request->input('status', $post->status),
            'published_at' => $request->input('published_at', $post->published_at),
        ];

        $rules = [
            'title'        => 'required|string|max:255',
            'summary'      => 'nullable|string|max:500',
            'content'      => 'required|string',
            'category_id'  => 'nullable|exists:blog_categories,blogCategory_id',
            'status'       => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'image'        => 'nullable|image|max:5120',
        ];

        $validator = Validator::make($input + $request->only('image','blogImage','blog_image'), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Image upload (replace old if any)
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
            if (! is_dir($destFolder)) mkdir($destFolder, 0755, true);
            $file->move($destFolder, $imageFileName);
            $post->image = $imageFileName;
        }

        $post->category_id = $input['category_id'];
        $post->title       = $input['title'];
        $post->summary     = $input['summary'];
        $post->content     = $input['content'];
        $post->status      = $input['status'];

        if ($post->status === 'published' && empty($input['published_at'])) {
            if (empty($post->published_at)) $post->published_at = now();
        } elseif ($post->status === 'draft') {
            $post->published_at = null;
        } else {
            $post->published_at = $input['published_at'];
        }

        $post->save();

        return redirect()->route('blogs.show', $post->blogPost_id)->with('success', 'Blog post updated.');
    }

    public function adminDestroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->image) {
            $basename = basename($post->image);
            $path = public_path('images/Blog/' . $basename);
            if ($basename !== 'default-blog.jpg' && file_exists($path)) {
                @unlink($path);
            }
        }

        $post->delete();

        return back()->with('success', 'Blog post removed by Admin.');
    }

    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

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
