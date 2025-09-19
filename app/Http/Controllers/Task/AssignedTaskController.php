<?php

namespace App\Http\Controllers\Task;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Http\Controllers\Controller;

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
            'task_id' => $task->task_id,   // important: your Task PK is task_id
            'user_id' => $uid
        ], [
            'assignedDate' => now(),
        ]);
    }

    // return assigned ids for UI convenience
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

    return response()->json(['success' => true, 'message' => 'Participant unassigned']);
}

}
