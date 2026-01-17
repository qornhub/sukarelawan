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

    TaskAssignment::where('task_id', $task->task_id)
        ->where('user_id', $userId)
        ->update([
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

    TaskAssignment::where('task_id', $task->task_id)
        ->where('user_id', $userId)
        ->update([
            'status' => 'rejected',
            'reject_reason' => $request->reason,
            'responded_at' => now(),
        ]);

    return response()->json([
        'success' => true,
        'message' => 'Task rejected.',
    ]);
}

}
