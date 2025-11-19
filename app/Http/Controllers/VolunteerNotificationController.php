<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        return view('volunteer.notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     * Returns JSON for AJAX; redirect + session flash for normal requests.
     */
   /**
 * Mark a single notification as read.
 */
public function markAsRead(Request $request, $id)
{
    $user = $request->user();
    $notification = $user->notifications()->where('id', $id)->first();

    // More strict AJAX detection
    $expectsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest';

    if (!$notification) {
        if ($expectsJson) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'unread'  => $user->unreadNotifications()->count(),
            ], 404);
        }
        return redirect()->back()->with('error', 'Notification not found');
    }

    // If already read
    if ($notification->read_at !== null) {
        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'message' => 'Notification already marked as read',
                'unread'  => $user->unreadNotifications()->count(),
            ], 200);
        }
        return redirect()->back()->with('info', 'Notification already marked as read');
    }

    // Mark as read
    $notification->markAsRead();

    // Always return JSON for API consistency
    return response()->json([
        'success' => true,
        'message' => 'Notification marked as read',
        'unread'  => $user->unreadNotifications()->count(),
    ], 200);
}

/**
 * Mark all unread notifications as read.
 */
public function markAllRead(Request $request)
{
    $user = $request->user();

    // More strict AJAX detection
    $expectsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') == 'XMLHttpRequest';

    $before = $user->unreadNotifications()->count();

    if ($before > 0) {
        $user->unreadNotifications->markAsRead(); // More efficient way
    }

    $after = $user->unreadNotifications()->count();

    // Always return JSON for API consistency
    return response()->json([
        'success' => true,
        'message' => $before > 0 ? 'All notifications marked as read' : 'No unread notifications',
        'unread'  => $after,
    ], 200);
}

    /**
     * Return unread notification count (AJAX).
     * Useful to initialize the badge on page load.
     * Always returns JSON.
     */
    public function unreadCount(Request $request)
    {
        $user = $request->user();
        $count = $user->unreadNotifications()->count();

        return response()->json(['unread' => (int) $count], 200);
    }
}
