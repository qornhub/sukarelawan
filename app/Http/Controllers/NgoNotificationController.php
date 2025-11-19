<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NgoNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

       

        $notifications = $user->notifications()->latest()->paginate(20);

        return view('ngo.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();

        $expectsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            if ($expectsJson) {
                return response()->json(['success' => false], 404);
            }
            return back()->with('error','Notification not found');
        }

        $notification->markAsRead();

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'unread'  => $user->unreadNotifications()->count()
            ]);
        }

        return back()->with('success','Notification marked as read');
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();

        foreach ($user->unreadNotifications as $n) {
            $n->markAsRead();
        }

        $expectsJson = $request->ajax() || $request->wantsJson() || $request->expectsJson();

        if ($expectsJson) {
            return response()->json([
                'success' => true,
                'unread'  => 0
            ]);
        }

        return back()->with('success','All notifications marked as read');
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications()->count()
        ]);
    }
}
