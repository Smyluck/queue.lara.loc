<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PodcastProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $podcastName) {}

    public function build(): self
    {
        return $this->subject('Podcast processed')
            ->view('emails.podcast_processed')
            ->with([
                'podcastName' => $this->podcastName,
            ]);
    }
}
