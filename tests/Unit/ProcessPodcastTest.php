<?php

namespace Tests\Unit;

use App\Jobs\ProcessPodcast;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessPodcastTest extends TestCase
{
    /** @test */
    public function it_dispatches_process_podcast_job()
    {
        // 1. Подмени очередь на fake
        Queue::fake(); джлл

        // 2. Диспетчеризируй Job
        ProcessPodcast::dispatch('Тестовый подкаст');

        // 3. Проверь, что Job добавлен в очередь
        Queue::assertPushed(ProcessPodcast::class, function ($job) {se
            return $job->podcastName === 'Тестовый подкаст';
        });
    }

    /** @test */
    public function it_processes_podcast_correctly()
    {
        // 1. Подмени очередь на sync для немедленного выполнения
        Queue::fake();
        Queue::assertNothingPushed();

        // 2. Вызови Job напрямую
        $job = new ProcessPodcast('Тестовый подкаст');
        $job->handle();

        // 3. Проверь логику (например, запись в лог)
        $this->assertTrue(true); // Замени на реальную проверку
    }
}
