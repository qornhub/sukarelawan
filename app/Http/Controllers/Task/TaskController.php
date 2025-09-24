<?php

namespace App\Http\Controllers\Task;

use App\Models\Task;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TaskAssignment;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
   // LIST tasks for one event
   public function index($eventId)
{
    $event = Event::where('event_id', $eventId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $tasks = Task::with('event') // eager load event relationship
        ->where('event_id', $event->event_id)
        ->orderBy('created_at','desc')
        ->get();

    return view('ngo.tasks.task_list', compact('event','tasks'));
}


    // SHOW create form (always for this event)
    public function create($eventId)
    {
        $event = Event::where('event_id', $eventId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('ngo.tasks.task_create', compact('event'));
    }

public function assignedList($task_id)
{
    $task = Task::where('task_id', $task_id)->firstOrFail();
    $current = TaskAssignment::where('task_id', $task->task_id)
        ->pluck('user_id')
        ->map(fn($v) => (string)$v)
        ->all();
    return response()->json(['assigned' => $current], 200);
}



    public function store(Request $request, $eventId)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $event = Event::where('event_id', $eventId)
                      ->where('user_id', $request->user()->id)
                      ->firstOrFail();

        $task = Task::create([
            'task_id'     => (string) \Illuminate\Support\Str::uuid(),
            'event_id'    => $event->event_id,
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $task->load('event');
            return response()->json(['task' => $task], 201);
        }

        return redirect()->route('ngo.tasks.index', $event->event_id)->with('success', 'Task created successfully.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        throw $e;
    } catch (\Exception $e) {
        // Log the error and return JSON
        Log::error('Task store error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Server error', 'detail' => $e->getMessage()], 500);
        }
        throw $e;
    }
}


   // EDIT (event-scoped)
public function edit($eventId, $taskId)
{
    $event = Event::where('event_id', $eventId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    // load event relationship too in case view needs it
    $task = Task::where('task_id', $taskId)
        ->where('event_id', $event->event_id)
        ->with('event')
        ->firstOrFail();

    return view('ngo.tasks.task_edit', compact('event', 'task'));
}

// UPDATE (event-scoped) — supports AJAX JSON response
public function update(Request $request, $eventId, $taskId)
{
    $event = Event::where('event_id', $eventId)
        ->where('user_id', $request->user()->id)
        ->firstOrFail();

    $task = Task::where('task_id', $taskId)
        ->where('event_id', $event->event_id)
        ->firstOrFail();

    $data = $request->validate([
        'title'       => 'required|string|max:255',
        'description' => 'required|string',
    ]);

    $task->update($data);

    // refresh and load event relationship for JSON response
    $task->refresh()->load('event');

    // If AJAX request (fetch with X-Requested-With or expects JSON), return JSON
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json(['task' => $task]);
    }

    // fallback for non-AJAX (normal form submit)
    return redirect()->route('ngo.events.manage', $event->event_id)
                     ->with('success', 'Task updated successfully.');
}
public function destroy(Request $request, $eventId, $taskId)
{
    $event = Event::where('event_id', $eventId)
        ->where('user_id', Auth::id())
        ->firstOrFail();

    $task = Task::where('task_id', $taskId)
        ->where('event_id', $event->event_id)
        ->firstOrFail();

    $task->delete();

    
    if ($request->ajax() || $request->wantsJson()) {
    return response()->json(['success' => true, 'task_id' => $taskId, 'message' => 'Task deleted.']);
}

    return redirect()->route('ngo.tasks.index', $event->event_id)
                     ->with('success', 'Task deleted.');
}



}

