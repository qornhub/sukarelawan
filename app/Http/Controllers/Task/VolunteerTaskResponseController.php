<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerTaskResponseController extends Controller
{
    public function accept(Request $request, Task $task)
    {
        $userId = Auth::id();

        $assignment = TaskAssignment::where('task_id', $task->task_id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $assignment->update([
            'status' => 'accepted',
            'reject_reason' => null,
            'responded_at' => now(),
        ]);

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

        $userId = Auth::id();

        $assignment = TaskAssignment::where('task_id', $task->task_id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // ✅ save reason first (for audit)
        $assignment->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason,
            'responded_at' => now(),
        ]);

        // ✅ requirement: when rejected -> directly become unassigned
        // Means delete record after storing reason? (you will lose reason)
        // So better: move to archive table OR keep row and treat rejected as "unassigned".
        // But since you said direct unassigned, we can DELETE after saving reason IF YOU WANT.

        // Option A (recommended): KEEP record, treat rejected as not assigned
        // return response()->json(...)

        // Option B (your requirement literally): delete after storing reason (loses reason)
        // $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task rejected.',
        ]);
    }
}
