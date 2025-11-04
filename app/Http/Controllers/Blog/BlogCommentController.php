<?php

namespace App\Http\Controllers\Blog;

use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\SentimentAnalyzer;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    // Analyze sentiment (synchronous). For small testing it's fine.
    try {
        $analyzer = new SentimentAnalyzer();
        $result = $analyzer->analyze($comment->content);

        $comment->sentiment = $result['label'] ?? 'Negative';
        $comment->sentiment_confidence = $result['confidence'] ?? 0.0;
        $comment->save();
    } catch (\Exception $e) {
        // optional: log, but do not block user
        Log::error('Sentiment analysis failed: '.$e->getMessage());
    }

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

        // Re-run sentiment analysis after update (same pattern as store)
        try {
            $analyzer = new SentimentAnalyzer();
            $result = $analyzer->analyze($comment->content);

            $comment->sentiment = $result['label'] ?? 'Negative';
            $comment->sentiment_confidence = $result['confidence'] ?? 0.0;
            $comment->save();
        } catch (\Exception $e) {
            Log::error('Sentiment analysis failed on update: '.$e->getMessage());
            // don't block the user for analysis errors
        }

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
