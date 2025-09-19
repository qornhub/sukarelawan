<?php

namespace App\Jobs;

use App\Mail\EventParticipantMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendParticipantEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Job properties (declare instead of setting on $this)
    public int $tries = 3;
    public int $timeout = 120;

    public string $email;
    public array $payload;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param array $payload
     */
    public function __construct(string $email, array $payload)
    {
        $this->email = $email;
        $this->payload = $payload;

        // you can still set the queue name for this instance:
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $mail = new EventParticipantMail($this->payload);

            if (!empty($this->payload['from_email'])) {
                $mail->replyTo($this->payload['from_email'], $this->payload['from_name'] ?? null);
            }

            Mail::to($this->email)->send($mail);

            Log::info('SendParticipantEmailJob: sent', [
                'to' => $this->email,
                'event_id' => $this->payload['event_id'] ?? null,
                'subject' => $this->payload['subject'] ?? null,
            ]);
        } catch (\Throwable $ex) {
            Log::error('SendParticipantEmailJob: failed', [
                'to' => $this->email,
                'event_id' => $this->payload['event_id'] ?? null,
                'subject' => $this->payload['subject'] ?? null,
                'error' => $ex->getMessage(),
            ]);
            throw $ex; // allow Laravel to handle retries / failed_jobs
        }
    }
}
