<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;

class BlogPostController extends Controller
{
    /**
 * Public index - show only published posts (paginated), with search support.
 */
public function index(Request $request)
{
    $query = BlogPost::where('status', 'published');

    // 🔍 If user entered a search keyword
    if ($search = $request->input('q')) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('blogSummary', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%");
              
        });
    }

    // 📝 Order by published date (newest first)
    $posts = $query->orderBy('published_at', 'desc')->paginate(10);

    // 🧭 Keep search keyword in pagination links
    $posts->appends($request->only('q'));

    return view('volunteer.blogs.index', compact('posts'));
}


    /**
     * Show single post.
     *
     * - If post is 'published' => anyone can view.
     * - If post is 'draft' => only the owner (author) can view (403 otherwise).
     *
     * @param string $id  blogPost_id (UUID)
     */
    public function show($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->status === 'published') {
            return view('volunteer.blogs.show', compact('post'));
        }

        // Post is draft => only owner (author) can view it
        $this->ensureOwnerOnly($post);

        return view('volunteer.blogs.show', compact('post'));
    }

    /**
     * Allow only the owner (author) of the post.
     *
     * Aborts 403 if the authenticated user is not the post owner.
     */
    protected function ensureOwnerOnly(BlogPost $post)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        // Owner allowed
        if ($user->id === $post->user_id) {
            return true;
        }

        abort(403);
    }
}
