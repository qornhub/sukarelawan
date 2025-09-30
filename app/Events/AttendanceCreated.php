<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Attendance $attendance;
    public ?string $html;

    public function __construct(Attendance $attendance, ?string $html = null)
    {
        // ensure user relation loaded for client convenience
        $this->attendance = $attendance->load('user.volunteerProfile');
        $this->html = $html;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ngo-event.' . $this->attendance->event_id);
    }

    public function broadcastWith()
    {
        return [
            'attendance' => $this->attendance->toArray(),
            'html' => $this->html,
        ];
    }

    public function broadcastAs()
    {
        return 'AttendanceCreated';
    }
}
