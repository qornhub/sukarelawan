<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Notifications\VolunteerTaskResponseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerTaskResponseController extends Controller
{
    public function accept(Request $request, Task $task)
    {
        $user = Auth::user();

        TaskAssignment::where('task_id', $task->task_id)
            ->where('user_id', $user->id)
            ->update([
                'status' => 'accepted',
                'reject_reason' => null,
                'responded_at' => now(),
            ]);

        // ✅ notify NGO organizer
        $organizer = $task->event?->organizer;
        if ($organizer) {
            $organizer->notify(new VolunteerTaskResponseNotification(
                $task,
                'accepted',
                $user
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Task accepted.',
        ]);
    }

    public function reject(Request $request, Task $task)
    {
        $request->validate([
            'reason' => 'required|string|min:3|max:500',
        ]);

        $user = Auth::user();

        TaskAssignment::where('task_id', $task->task_id)
            ->where('user_id', $user->id)
            ->update([
                'status' => 'rejected',
                'reject_reason' => $request->reason,
                'responded_at' => now(),
            ]);

        // ✅ notify NGO organizer
        $organizer = $task->event?->organizer;
        if ($organizer) {
            $organizer->notify(new VolunteerTaskResponseNotification(
                $task,
                'rejected',
                $user,
                $request->reason
            ));
        }

        return response()->json([
            'success' => true,
            'message' => 'Task rejected.',
        ]);
    }
}
