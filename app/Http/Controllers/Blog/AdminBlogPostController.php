<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Str;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminBlogPostController extends Controller
{
    /**
     * Display published blog posts.
     */
    public function index(Request $request)
    {
        $query = BlogPost::where('status', 'published');

        // Optional search
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('blogSummary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(10);
        $posts->appends($request->only('q'));

        return view('admin.blogs.index', compact('posts'));
    }

    /**
     * Show blog post
     */
    public function show($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        $comments = BlogComment::where('blogPost_id', $post->blogPost_id)
            ->orderBy('created_at', 'asc')
            ->paginate(5, ['*'], 'comments_page')
            ->withQueryString();

        return view('admin.blogs.show', compact('post', 'comments'));
    }

    /**
     * Create form
     */
    public function create()
    {
        $categories = BlogCategory::orderBy('categoryName')->get();
        return view('admin.blogs.create', compact('categories'));
    }

    /**
     * Store new post
     */
    public function store(Request $request)
    {
        $rules = [
            'title'           => 'required|string|max:255',
            'blogSummary'     => 'nullable|string|max:500',
            'content'         => 'required|string',
            'category_id'     => 'required',
            'custom_category' => 'required_if:category_id,other|string|max:255',
            'status'          => 'required|in:draft,published',
            'published_at'    => 'nullable|date',
            'image'           => 'nullable|image|max:5120',
        ];

        $validator = Validator::make($request->all() + $request->only('image'), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * PRIVATE CATEGORY LOGIC
         */
        if ($request->category_id === 'other') {
            $categoryId = null; // NO DB CATEGORY CREATED
            $customCategory = $request->custom_category;
        } else {
            $categoryId = $request->category_id;
            $customCategory = null;
        }

        // Publish date
        $publishedAt =
            ($request->status === 'published')
                ? ($request->published_at ?: now())
                : null;

        // Image handling
        $imageFileName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $dest = public_path('images/Blog');
            if (!is_dir($dest)) mkdir($dest, 0755, true);

            $file->move($dest, $imageFileName);
        }

        BlogPost::create([
            'blogPost_id'     => (string) Str::uuid(),
            'user_id'         => Auth::id(),
            'category_id'     => $categoryId,
            'custom_category' => $customCategory,
            'title'           => $request->title,
            'blogSummary'     => $request->blogSummary,
            'content'         => $request->content,
            'image'           => $imageFileName,
            'status'          => $request->status,
            'published_at'    => $publishedAt,
        ]);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created.');
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();
        $categories = BlogCategory::orderBy('categoryName')->get();

        return view('admin.blogs.edit', compact('post', 'categories'));
    }

    /**
     * Update post
     */
    public function update(Request $request, $id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        $rules = [
            'title'           => 'required|string|max:255',
            'blogSummary'     => 'nullable|string|max:500',
            'content'         => 'required|string',
            'category_id'     => 'required',
            'custom_category' => 'required_if:category_id,other|string|max:255',
            'status'          => 'required|in:draft,published',
            'published_at'    => 'nullable|date',
            'image'           => 'nullable|image|max:5120',
        ];

        $validator = Validator::make($request->all() + $request->only('image'), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * PRIVATE CATEGORY LOGIC
         */
        if ($request->category_id === 'other') {
            $post->category_id = null;
            $post->custom_category = $request->custom_category;
        } else {
            $post->category_id = $request->category_id;
            $post->custom_category = null;
        }

        // Image replacement
        if ($request->hasFile('image')) {
            if ($post->image) {
                $old = public_path('images/Blog/' . basename($post->image));
                if (file_exists($old) && basename($post->image) !== 'default-blog.jpg') {
                    unlink($old);
                }
            }

            $file = $request->file('image');
            $safeOriginal = preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $imageFileName = time() . '_blog_' . $safeOriginal;

            $dest = public_path('images/Blog');
            if (!is_dir($dest)) mkdir($dest, 0755, true);

            $file->move($dest, $imageFileName);
            $post->image = $imageFileName;
        }

        // Update fields
        $post->title       = $request->title;
        $post->blogSummary = $request->blogSummary;
        $post->content     = $request->content;
        $post->status      = $request->status;

        // Publish/draft timing
        if ($post->status === 'published' && empty($request->published_at)) {
            if (empty($post->published_at)) {
                $post->published_at = now();
            }
        } elseif ($post->status === 'draft') {
            $post->published_at = null;
        } else {
            $post->published_at = $request->published_at;
        }

        $post->save();

        return redirect()->route('admin.blogs.show', $post->blogPost_id)
            ->with('success', 'Blog post updated.');
    }

    /**
     * Admin delete permanently
     */
    public function adminDestroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->image) {
            $path = public_path('images/Blog/' . basename($post->image));
            if (file_exists($path) && basename($post->image) !== 'default-blog.jpg') {
                unlink($path);
            }
        }

        $post->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post removed by Admin.');
    }

    /**
     * Delete for non-admin context
     */
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->image) {
            $path = public_path('images/Blog/' . basename($post->image));
            if (file_exists($path) && basename($post->image) !== 'default-blog.jpg') {
                unlink($path);
            }
        }

        $post->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog post deleted.');
    }
}
