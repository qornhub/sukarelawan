<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class TaskAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $taskId;
    public ?string $taskTitle;
    public string $action; // 'assigned' | 'unassigned'
    public ?string $byName;

    // NEW: event fields
    public $eventId;
    public $eventName;

    /**
     * @param mixed $task  - Task model or array-like
     * @param string $action
     * @param string|null $byName
     * @param mixed|null $eventId
     * @param string|null $eventName
     */
    public function __construct($task, string $action = 'assigned', ?string $byName = null, $eventId = null, ?string $eventName = null)
    {
        // normalize task fields
        $this->taskId = (string) ($task->task_id ?? $task->id ?? '');
        $this->taskTitle = $task->title ?? $task->name ?? $task->taskTitle ?? null;
        $this->action = $action;
        $this->byName = $byName;

        // set event fields: prefer provided values, otherwise attempt to get from task->event
        $this->eventId = $eventId
            ?? ($task->event_id ?? $task->event?->event_id ?? $task->event?->id ?? null);

        $this->eventName = $eventName
            ?? ($task->event?->eventTitle
                ?? $task->event?->eventName
                ?? $task->event?->name
                ?? $task->event?->title
                ?? null);
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $title = $this->taskTitle ?? 'Task';
        $target = $this->eventName ?? $title;

        $message = $this->action === 'assigned'
            ? "You were assigned to '{$target}'."
            : "You were unassigned from '{$target}'.";

        if ($this->byName) {
            $message .= " (by {$this->byName})";
        }

        return [
            'task_id'    => $this->taskId,
            'task_title' => $this->taskTitle,
            'action'     => $this->action,
            'by'         => $this->byName,
            'message'    => $message,

            // NEW: event info included for clickable link in blade
            'event_id'   => $this->eventId,
            'event_name' => $this->eventName,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
