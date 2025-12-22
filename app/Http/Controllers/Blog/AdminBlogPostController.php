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
        'custom_category' => 'nullable|string|max:255|required_if:category_id,other',

        'status'          => 'required|in:draft,published',
        'published_at'    => 'nullable|date',
        'image'           => 'nullable|image|max:5120',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    /* ============================
       CATEGORY LOGIC (FK SAFE)
       ============================ */
   // CATEGORY LOGIC (FK SAFE)
if ($request->category_id === 'other') {
    $categoryId     = null;
    $customCategory = $request->custom_category;
} else {
    // ONLY look up blogCategory_id — your table has no "id"
    $category = BlogCategory::where('blogCategory_id', $request->category_id)->first();

    if ($category) {
        $categoryId     = $category->blogCategory_id;
        $customCategory = null;
    } else {
        // Safety fallback to avoid FK error
        $categoryId     = null;
        $customCategory = null;
    }
}


    // Publish logic
    $publishedAt = ($request->status === 'published')
        ? ($request->published_at ?: now())
        : null;

    // DEFAULT IMAGE
    $imageFileName = "default_blog.jpg";

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $safeName = time() . '_blog_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $dest = public_path('images/Blog');
        if (!is_dir($dest)) mkdir($dest, 0755, true);

        $file->move($dest, $safeName);
        $imageFileName = $safeName;
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

    return redirect()->route('admin.blogs.drafts')->with('success', 'Blog post created.');

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
        'custom_category' => 'nullable|string|max:255|required_if:category_id,other',

        'status'          => 'required|in:draft,published',
        'published_at'    => 'nullable|date',
        'image'           => 'nullable|image|max:5120',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

 
if ($request->category_id === 'other') {
    $post->category_id     = null;
    $post->custom_category = $request->custom_category;
} else {
    // ONLY lookup using blogCategory_id
    $category = BlogCategory::where('blogCategory_id', $request->category_id)->first();

    if ($category) {
        $post->category_id     = $category->blogCategory_id;
        $post->custom_category = null;
    } else {
        $post->category_id     = null;
        $post->custom_category = null;
    }
}


    /* ============================
       IMAGE REPLACEMENT
       ============================ */
    if ($request->hasFile('image')) {
        if ($post->image && $post->image !== "default_blog.jpg") {
            $oldPath = public_path("images/Blog/" . $post->image);
            if (file_exists($oldPath)) unlink($oldPath);
        }

        $file = $request->file('image');
        $safeName = time() . '_blog_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

        $dest = public_path('images/Blog');
        if (!is_dir($dest)) mkdir($dest, 0755, true);

        $file->move($dest, $safeName);
        $post->image = $safeName;
    }

    if (!$post->image) {
        $post->image = "default_blog.jpg";
    }

    // Normal fields
    $post->title       = $request->title;
    $post->blogSummary = $request->blogSummary;
    $post->content     = $request->content;
    $post->status      = $request->status;

    // Publish logic
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

return redirect()->route('admin.blogs.drafts')->with('success', 'Blog post updated.');

}



  public function draftList()
{
    $drafts = BlogPost::where('user_id', Auth::id())
        ->orderBy('updated_at', 'desc')
        ->paginate(10);

    return view('admin.blogs.drafts', compact('drafts'));
}

    /**
     * Admin permanent delete
     */
  /**
 * Admin permanent delete
 */
public function adminDestroy($id)
{
    $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

    // Check if this post belongs to the admin
    $isOwner = Auth::check() && Auth::id() === $post->user_id;

    // Delete image if not default
    if ($post->image && $post->image !== "default_blog.jpg") {
        $path = public_path("images/Blog/" . $post->image);
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    // Delete the post
    $post->delete();

    // Redirect based on ownership
    if ($isOwner) {
        // Admin deleted their OWN blog post → return to drafts page
        return redirect()->route('admin.blogs.drafts')
            ->with('success', 'Your blog post has been deleted.');
    }

    // Admin deleted someone else's blog post → return to blog index
    return redirect()->route('admin.blogs.index')
        ->with('success', 'Blog post removed by Admin.');
}


    /**
     * Normal delete (non-admin context)
     */
    public function destroy($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->image && $post->image !== "default_blog.jpg") {
            $path = public_path("images/Blog/" . $post->image);
            if (file_exists($path)) unlink($path);
        }

        $post->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog post deleted.');
    }
}
