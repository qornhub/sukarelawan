<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

 
class VolunteerNotificationController extends Controller
{
   

    /**
     * Show list of notifications (paginated).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Use database notifications and paginate for large lists
        $notifications = $user->notifications()->latest()->paginate(20);

        // If you want unread first, you could order differently or split into two collections.
        return view('volunteer.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read (AJAX).
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all unread notifications as read (AJAX).
     */
    public function markAllRead(Request $request)
    {
        $user = $request->user();

        foreach ($user->unreadNotifications as $n) {
            $n->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Return unread notification count (AJAX).
     * Useful to initialize the badge on page load.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();

        return response()->json(['unread' => (int) $count]);
    }
}
