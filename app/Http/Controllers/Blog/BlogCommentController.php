<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BlogPost;
use App\Models\BlogComment;

class BlogCommentController extends Controller
{
    // Store a new comment
    public function store(Request $request, $postId)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $post = BlogPost::where('blogPost_id', $postId)->firstOrFail();

        $comment = BlogComment::create([
            'blogComment_id' => (string) Str::uuid(),
            'blogPost_id'    => $post->blogPost_id,
            'user_id'        => Auth::id(),
            'content'        => $request->input('content'),
        ]);

        return redirect()->to(url()->previous() . '#comments')->with('success', 'Comment posted.');
    }

    // Show edit form (optional — we render inline edit in partial; still useful)
    public function edit($postId, $commentId)
    {
        $comment = BlogComment::where('blogComment_id', $commentId)
            ->where('blogPost_id', $postId)
            ->firstOrFail();

        $this->authorizeEdit($comment);

        return view('blog.comments.edit', compact('comment')); // optional
    }

    // Update comment
    public function update(Request $request, $postId, $commentId)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $comment = BlogComment::where('blogComment_id', $commentId)
            ->where('blogPost_id', $postId)
            ->firstOrFail();

        // Only owner can update
        if (Auth::id() !== $comment->user_id) {
            abort(403);
        }

        $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $comment->content = $request->input('content');
        $comment->save();

        return redirect()->to(url()->previous() . '#comment-' . $comment->blogComment_id)
            ->with('success', 'Comment updated.');
    }

    // Delete comment
    public function destroy(Request $request, $postId, $commentId)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $comment = BlogComment::where('blogComment_id', $commentId)
            ->where('blogPost_id', $postId)
            ->firstOrFail();

        $user = Auth::user();

        // Admins can delete any comment; owner can delete their own
        if ($user->id !== $comment->user_id && ! $this->isAdmin($user)) {
            abort(403);
        }

        $comment->delete();

        return redirect()->to(url()->previous() . '#comments')->with('success', 'Comment deleted.');
    }

    protected function isAdmin($user)
    {
        // support both relation role->roleName and direct role string and is_admin flag
        $roleName = null;
        if (isset($user->role) && is_object($user->role) && isset($user->role->roleName)) {
            $roleName = strtolower($user->role->roleName);
        } elseif (! empty($user->role) && is_string($user->role)) {
            $roleName = strtolower($user->role);
        } elseif (! empty($user->roleName) && is_string($user->roleName)) {
            $roleName = strtolower($user->roleName);
        }

        if ($roleName === 'admin') {
            return true;
        }

        if (property_exists($user, 'is_admin') && $user->is_admin) {
            return true;
        }

        return false;
    }

    protected function authorizeEdit(BlogComment $comment)
    {
        if (! Auth::check() || Auth::id() !== $comment->user_id) {
            abort(403);
        }
    }
}
