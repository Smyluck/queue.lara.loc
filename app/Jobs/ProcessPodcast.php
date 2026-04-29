<?php

namespace App\Jobs;

use App\Mail\PodcastProcessed;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessPodcast implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $podcastName)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Processing podcast: '.$this->podcastName);
        Mail::to('admin@example.com')->send(new PodcastProcessed($this->podcastName));
    }
}
