<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Attendance;

class AdminEventManagementController extends Controller
{
    public function view($event_id)
    {
        // Load event
        $event = Event::where('event_id', $event_id)->firstOrFail();

        // Load participants
        $registered = EventRegistration::where('event_id', $event->event_id)->orderBy('created_at')->get();
        $confirmed  = EventRegistration::where('event_id', $event->event_id)->where('status', 'approved')->get();
        $rejected   = EventRegistration::where('event_id', $event->event_id)->where('status', 'rejected')->get();

        // Load users for confirmed participants
        $userIds = $confirmed->pluck('user_id')->filter()->unique();
        $confirmedParticipants = User::whereIn('id', $userIds)->get();

        // Tasks
        $tasks = Task::with(['assignments.user'])->where('event_id', $event->event_id)->get();
        $assignedMap = TaskAssignment::whereIn('user_id', $userIds)
                        ->get()
                        ->groupBy('user_id')
                        ->map(fn($rows) => $rows->pluck('task_id')->implode(','))
                        ->toArray();

        // Attendance
        $attendances = Attendance::with(['user.volunteerProfile'])
                        ->where('event_id', $event->event_id)
                        ->get();

        // Admin read-only flag
        $isAdminReadonly = true;

        return view('admin.events.view_event_management', compact(
            'event',
            'registered',
            'confirmed',
            'rejected',
            'confirmedParticipants',
            'tasks',
            'assignedMap',
            'attendances',
            'isAdminReadonly'
        ));
    }
}
