<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEvent implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels; //Для того, чтобы попала в очередь во время обработки события и была видна в Horizon, а также для логирования и сериализации данных

    /**
     * Create a new job instance.
     */
    public function __construct(public array $data)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Обработка события: {$this->data['event']}", $this->data);
        Mail::to($this->data['data']['email'])->send(new Report("Событие: {$this->data['event']}, User ID: {$this->data['data']['user_id']}"));
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'event:' . ($this->data['event'] ?? 'unknown'),
            'user:' . ($this->data['data']['user_id'] ?? 'unknown'),
            'type:' . ($this->data['data']['metadata']['type'] ?? 'unknown'),
            'queue2:' . ($this->queue ?? 'default'),
        ];
    }
}
