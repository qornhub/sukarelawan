<?php

namespace App\Http\Controllers\NGO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\EventRegistration; // assuming you have this model
use Carbon\Carbon;

class TaskAssignmentController extends Controller
{
    
    /**
     * Assign a task to a confirmed volunteer.
     * Requires: task_id, user_id
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required|exists:tasks,task_id',
            'user_id' => 'required|exists:users,id',
            'assignedDate' => 'nullable|date',
        ]);

        $task = Task::where('task_id', $data['task_id'])->firstOrFail();

        // Check the user is a confirmed/approved registrant for the same event
        $registration = EventRegistration::where('event_id', $task->event_id)
            ->where('user_id', $data['user_id'])
            ->where('status', 'approved') // business rule: only confirmed volunteers
            ->first();

        if (! $registration) {
            return redirect()->back()->withErrors(['user_id' => 'This user is not a confirmed volunteer for the event.']);
        }

        // Create (since composite PK, use firstOrCreate to avoid duplicates)
        TaskAssignment::firstOrCreate(
            [
                'task_id' => $task->task_id,
                'user_id' => $data['user_id'],
            ],
            [
                'assignedDate' => $data['assignedDate'] ?? Carbon::now(),
            ]
        );

        return redirect()->back()->with('success', 'Task assigned successfully.');
    }

    /**
     * Unassign (delete) assignment
     */
    public function destroy(Request $request, $taskId, $userId)
    {
        $assignment = TaskAssignment::where('task_id', $taskId)
            ->where('user_id', $userId)
            ->first();

        if (! $assignment) {
            return redirect()->back()->withErrors(['not_found' => 'Assignment not found.']);
        }

        $assignment->delete();

        return redirect()->back()->with('success', 'Assignment removed.');
    }

    /**
     * For NGO: list assignments for a task
     */
    public function indexForTask($taskId)
    {
        $task = Task::with('assignments.user')->where('task_id', $taskId)->firstOrFail();
        return view('ngo.tasks.assignments', compact('task'));
    }
}
