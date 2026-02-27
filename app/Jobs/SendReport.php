<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Report;

class SendReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $email, public string $text)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Отчёт отправлен на почту {$this->email}");
        Mail::to($this->email)->send(new Report($this->text));
    }
}
