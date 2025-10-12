<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventComment;

class EventCommentController extends Controller
{
    public function store(Request $request, $eventId)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $event = Event::where('event_id', $eventId)->firstOrFail();

        $comment = EventComment::create([
            'eventComment_id' => (string) Str::uuid(),
            'event_id'        => $event->event_id,
            'user_id'         => Auth::id(),
            'content'         => $request->input('content'),
        ]);

        return redirect()->to(url()->previous() . '#event-comments')->with('success', 'Comment posted.');
    }

    public function update(Request $request, $eventId, $commentId)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $comment = EventComment::where('eventComment_id', $commentId)
            ->where('event_id', $eventId)
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

        return redirect()->to(url()->previous() . '#comment-' . $comment->eventComment_id)
            ->with('success', 'Comment updated.');
    }

    public function destroy(Request $request, $eventId, $commentId)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $comment = EventComment::where('eventComment_id', $commentId)
            ->where('event_id', $eventId)
            ->firstOrFail();

        $user = Auth::user();

        // Admins can delete any comment; owner can delete their own
        if ($user->id !== $comment->user_id && ! $this->isAdmin($user)) {
            abort(403);
        }

        $comment->delete();

        return redirect()->to(url()->previous() . '#event-comments')->with('success', 'Comment deleted.');
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
}
