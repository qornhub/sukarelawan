<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $status;

    public function __construct($event, $status)
    {
        $this->event = $event;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        // send both database and real-time (broadcast)
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'event_id' => $this->event->event_id,
            'event_name' => $this->event->eventName ?? 'Unknown Event',
            'status' => $this->status,
            'message' => match ($this->status) {
                'approved' => "You have been approved to join '{$this->event->eventName}'.",
                'rejected' => "Your registration for '{$this->event->eventName}' was rejected.",
                'attended' => "Your attendance for '{$this->event->eventName}' has been recorded.",
                default => "Update for '{$this->event->eventName}'.",
            },
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
