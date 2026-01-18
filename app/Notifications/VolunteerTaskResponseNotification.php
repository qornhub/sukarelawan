<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class VolunteerTaskResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $taskId;
    public ?string $taskTitle;

    public string $action; // accepted | rejected
    public string $volunteerId;
    public ?string $volunteerName;

    public $eventId;
    public $eventName;

    public ?string $rejectReason;

    /**
     * @param mixed $task Task model
     * @param string $action accepted|rejected
     * @param mixed $volunteer User model
     * @param string|null $rejectReason
     */
    public function __construct($task, string $action, $volunteer, ?string $rejectReason = null)
    {
        $this->taskId = (string) ($task->task_id ?? $task->id ?? '');
        $this->taskTitle = $task->title ?? $task->name ?? null;

        $this->action = $action;

        $this->volunteerId = (string) ($volunteer->id ?? '');
        $this->volunteerName = $volunteer->name ?? null;

        $this->eventId = $task->event_id ?? $task->event?->event_id ?? $task->event?->id ?? null;
        $this->eventName = $task->event?->eventTitle
            ?? $task->event?->eventName
            ?? $task->event?->name
            ?? $task->event?->title
            ?? null;

        $this->rejectReason = $rejectReason;
    }

    public function via($notifiable)
    {
        // same style you already use
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $taskTitle = $this->taskTitle ?? 'Task';
        $eventTitle = $this->eventName ?? 'Event';
        $volName = $this->volunteerName ?? 'A volunteer';

        if ($this->action === 'accepted') {
            $message = "{$volName} accepted task '{$taskTitle}' for '{$eventTitle}'.";
        } else {
            $message = "{$volName} rejected task '{$taskTitle}' for '{$eventTitle}'.";
            if ($this->rejectReason) {
                $message .= " Reason: {$this->rejectReason}";
            }
        }

        return [
            'task_id'        => $this->taskId,
            'task_title'     => $this->taskTitle,

            'action'         => $this->action, // accepted|rejected

            'volunteer_id'   => $this->volunteerId,
            'volunteer_name' => $this->volunteerName,

            'event_id'       => $this->eventId,
            'event_name'     => $this->eventName,

            'reject_reason'  => $this->rejectReason,

            'message'        => $message,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
