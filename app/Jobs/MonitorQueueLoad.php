<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class MonitorQueueLoad implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Получаем статистику очередей из Redis
        $pendingCount = Redis::llen('queues:default');
        $delayedCount = Redis::zcard('queues:default:delayed');
        $reservedCount = Redis::zcard('queues:default:reserved');

        $queueStats = [
            'pending' => $pendingCount,
            'delayed' => $delayedCount,
            'reserved' => $reservedCount,
            'total' => $pendingCount + $delayedCount + $reservedCount,
            'timestamp' => now(),
        ];

        // Логируем статистику
        Log::channel('daily')->info('Queue Load Monitor', $queueStats);

        // Проверяем пороги для уведомлений
        $this->checkThresholds($queueStats);

        // Сохраняем последнюю метрику
        Redis::set('queue:last_stats', json_encode($queueStats));
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'monitoring2',
            'queue-stats',
            'critical',
        ];
    }

    /**
     * Проверка пороговых значений для отправки уведомлений
     */
    private function checkThresholds(array $stats): void
    {
        // Пороги срабатывания
        $thresholds = [
            'pending' => 100,      // 100+ задач в очереди
            'delayed' => 50,       // 50+ отложенных задач
            'total' => 150,        // 150+ всего
        ];

        // Проверяем pending
        if ($stats['pending'] > $thresholds['pending']) {
            $message = "⚠️ ВНИМАНИЕ: В очереди {$stats['pending']} ожидающих задач (порог: {$thresholds['pending']})";
            Log::warning($message, $stats);
            // Здесь сработает уведомление из AppServiceProvider
        }

        // Проверяем delayed
        if ($stats['delayed'] > $thresholds['delayed']) {
            $message = "⚠️ ВНИМАНИЕ: {$stats['delayed']} отложенных задач (порог: {$thresholds['delayed']})";
            Log::warning($message, $stats);
        }

        // Проверяем общее количество
        if ($stats['total'] > $thresholds['total']) {
            $message = "🔴 КРИТИЧНО: Всего {$stats['total']} задач в очереди (порог: {$thresholds['total']})";
            Log::error($message, $stats);
        }
    }
}
