<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class EventStatusNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $event;
    protected $status;

    // cached scalar values (safe for queue serialization)
    protected ?string $cachedEventName = null;
    protected $cachedEventId = null;

    public function __construct($event, $status)
    {
        $this->event = $event;
        $this->status = $status;

        // Cache event id/name immediately so queued jobs will have the string
        $this->cachedEventId = $event?->event_id ?? $event?->id ?? null;

        // Try common field names used in your schema (including eventTitle)
        $this->cachedEventName =
            $event?->eventTitle
            ?? $event?->eventName
            ?? $event?->name
            ?? $event?->title
            ?? null;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    protected function getEventName(): string
    {
        return $this->cachedEventName ?? 'Unknown Event';
    }

    public function toArray($notifiable)
    {
        $name = $this->getEventName();

        return [
            'event_id'   => $this->cachedEventId,
            'event_name' => $name,
            'status'     => $this->status,
            'message'    => match ($this->status) {
                'approved' => "You have been approved to join '{$name}'.",
                'rejected' => "Your registration for '{$name}' was rejected.",
                'attended' => "Your attendance for '{$name}' has been recorded.",
                default    => "Update for '{$name}'.",
            },
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
