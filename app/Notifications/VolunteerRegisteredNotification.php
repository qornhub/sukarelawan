<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class VolunteerRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $registration;
    protected string $volunteerName;
    protected $cachedEventId;
    protected ?string $cachedEventName;

    /**
     * $registration can be the EventRegistration model or an array snapshot.
     */
    public function __construct($event, $registration)
    {
        $this->event = $event;
        $this->registration = $registration;
        $this->volunteerName = $registration->name ?? ($registration['name'] ?? 'Volunteer');
        $this->cachedEventId = $event?->event_id ?? $event?->id ?? null;
        $this->cachedEventName = $event?->eventTitle ?? $event?->eventName ?? $event?->name ?? $event?->title ?? null;
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
        $eventName = $this->getEventName();

        return [
            'event_id'       => $this->cachedEventId,
            'event_name'     => $eventName,
            'registration_id'=> $this->registration->registration_id ?? ($this->registration['registration_id'] ?? null),
            'volunteer_name' => $this->volunteerName,
            'status'         => 'new_registration',
            'message'        => "{$this->volunteerName} has registered for '{$eventName}' and awaits approval.",
            // you can add any extra fields you need (contact, email, snapshot, etc.)
            'meta'           => [
                'email' => $this->registration->email ?? ($this->registration['email'] ?? null),
                'contact' => $this->registration->contactNumber ?? ($this->registration['contactNumber'] ?? null),
            ],
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
