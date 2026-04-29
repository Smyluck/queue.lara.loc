<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessEvent;
use App\Jobs\MonitorQueueLoad;

class GenerateQueueLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:generate-load
                            {count=50 : Количество задач для отправки}
                            {--type=normal : Тип нагрузки (normal|heavy|mixed)}
                            {--delay=0 : Задержка между отправками в секундах}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует нагрузку на очередь для тестирования мониторинга';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $type = $this->option('type');
        $delay = (int) $this->option('delay');

        $this->info("🚀 Генерация нагрузки на очередь:");
        $this->info("📊 Количество задач: {$count}");
        $this->info("🎯 Тип нагрузки: {$type}");
        $this->info("⏱️ Задержка: {$delay} сек");

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        for ($i = 1; $i <= $count; $i++) {
            $this->generateTask($i, $type);

            if ($delay > 0) {
                sleep($delay);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Отправлено {$count} задач в очередь!");
        $this->info("📈 Проверьте Horizon Dashboard для мониторинга нагрузки");
        $this->info("🔔 MonitorQueueLoad запустится автоматически через 5 минут");

        // Немедленно запустим мониторинг для проверки
        $this->info("🔍 Запускаю немедленную проверку нагрузки...");
        MonitorQueueLoad::dispatch();

        return Command::SUCCESS;
    }

    /**
     * Генерирует задачу в зависимости от типа нагрузки
     */
    private function generateTask(int $index, string $type): void
    {
        $data = match ($type) {
            'heavy' => $this->generateHeavyTask($index),
            'mixed' => rand(0, 1) ? $this->generateNormalTask($index) : $this->generateHeavyTask($index),
            default => $this->generateNormalTask($index),
        };

        ProcessEvent::dispatch($data)->onQueue('high');
    }

    /**
     * Генерирует обычную задачу
     */
    private function generateNormalTask(int $index): array
    {
        return [
            'event' => "Тестовое событие #{$index}",
            'data' => [
                'user_id' => rand(1, 1000),
                'email' => "user{$index}@example.com",
                'timestamp' => now(),
                'metadata' => [
                    'source' => 'queue_load_test',
                    'type' => 'normal',
                    'index' => $index,
                ]
            ]
        ];
    }

    /**
     * Генерирует тяжелую задачу (с большим объемом данных)
     */
    private function generateHeavyTask(int $index): array
    {
        $largeData = [];
        for ($j = 0; $j < 100; $j++) {
            $largeData["field_{$j}"] = "Тяжелые данные для нагрузки " . str_repeat("X", 100);
        }

        return [
            'event' => "Тяжелое тестовое событие #{$index} с большим объемом данных",
            'data' => [
                'user_id' => rand(1, 1000),
                'email' => "heavy_user{$index}@example.com",
                'timestamp' => now(),
                'large_payload' => $largeData,
                'metadata' => [
                    'source' => 'queue_load_test',
                    'type' => 'heavy',
                    'index' => $index,
                    'size' => 'large',
                ]
            ]
        ];
    }
}
