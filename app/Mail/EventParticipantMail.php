<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build()
    {
        $subject = $this->payload['subject'] ?? 'Message from organizer';
        // Use your configured MAIL_FROM_ADDRESS for envelope; use replyTo set by job
        return $this->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->markdown('emails.event_participant')
                    ->with('payload', $this->payload);
    }
}
