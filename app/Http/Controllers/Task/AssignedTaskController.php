<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TaskAssignmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class AssignedTaskController extends Controller
{
    public function assign(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        foreach ($validated['user_ids'] as $uid) {
            TaskAssignment::firstOrCreate([
                'task_id' => $task->task_id,
                'user_id' => $uid
            ], [
                'assignedDate' => now(),
            ]);
        }

        // fetch users and send notification
        $users = User::whereIn('id', $validated['user_ids'])->get();

        // Who performed the action
        $by = Auth::user()?->name ?? 'NGO';

        // Robustly pick event id / name from task or related event
        $eventId = $task->event_id
            ?? $task->event?->event_id
            ?? $task->event?->id
            ?? null;

        $eventName = $task->event?->eventTitle
            ?? $task->event?->eventName
            ?? $task->event?->name
            ?? $task->event?->title
            ?? null;

        // Notify users (notification will include event_id & event_name)
        Notification::send($users, new TaskAssignmentNotification(
            $task,
            'assigned',
            $by,
            $eventId,
            $eventName
        ));

        $assigned = TaskAssignment::where('task_id', $task->task_id)
                    ->pluck('user_id')
                    ->map(fn($v) => (string)$v)
                    ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Participants assigned successfully',
            'assigned' => $assigned
        ]);
    }

    public function unassign(Task $task, $userId)
    {
        TaskAssignment::where('task_id', $task->task_id)
            ->where('user_id', $userId)
            ->delete();

        // notify the single user that they were unassigned
        $user = User::find($userId);
        if ($user) {
            $by = Auth::user()?->name ?? 'NGO';

            $eventId = $task->event_id
                ?? $task->event?->event_id
                ?? $task->event?->id
                ?? null;

            $eventName = $task->event?->eventTitle
                ?? $task->event?->eventName
                ?? $task->event?->name
                ?? $task->event?->title
                ?? null;

            $user->notify(new TaskAssignmentNotification(
                $task,
                'unassigned',
                $by,
                $eventId,
                $eventName
            ));
        }

        return response()->json(['success' => true, 'message' => 'Participant unassigned']);
    }
}
