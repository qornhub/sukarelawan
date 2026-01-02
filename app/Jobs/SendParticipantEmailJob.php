<?php

namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventParticipantMail;

class SendParticipantEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public string $email;
    public array $payload;

    public function __construct(string $email, array $payload)
    {
        $this->email = $email;
        $this->payload = $payload;

     
        $this->onConnection('database');
        $this->onQueue('emails');
    }

    public function handle()
    {
        Mail::to($this->email)->send(
            new EventParticipantMail($this->payload)
        );
    }
}
