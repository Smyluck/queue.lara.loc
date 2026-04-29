<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessEvent;
use App\Jobs\SendReport;
use App\Jobs\MonitorQueueLoad;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class HorizonDemoController extends Controller
{
    /**
     * Демонстрация: Отправить одно событие
     * 
     * Пример: POST /api/horizon/demo/single-event
     * 
     * Результат в Horizon:
     * - Вкладка "Ожидающие выполнения задания" → видите ProcessEvent
     * - Теги: event:Demo Event, user:123, type:normal
     */
    public function singleEvent(): JsonResponse
    {
        $data = [
            'event' => 'Demo Event',
            'data' => [
                'user_id' => 123,
                'email' => 'demo@example.com',
                'timestamp' => now(),
                'metadata' => [
                    'source' => 'horizon_demo',
                    'type' => 'normal',
                    'index' => 1,
                ]
            ]
        ];

        ProcessEvent::dispatch($data)->onQueue('default');

        Log::info('📤 Отправлено одно событие в очередь');

        return response()->json([
            'status' => 'success',
            'message' => 'Событие отправлено в очередь',
            'data' => $data,
            'horizon_url' => url('/horizon'),
            'instructions' => [
                '1. Откройте http://localhost/horizon',
                '2. Перейдите на вкладку "Ожидающие выполнения задания"',
                '3. Найдите ProcessEvent с тегом event:Demo Event',
                '4. Запустите рабочих: php artisan horizon',
                '5. Смотрите как задача переместится в "Выполненные задания"',
            ]
        ]);
    }

    /**
     * Демонстрация: Отправить пакет событий
     * 
     * Пример: POST /api/horizon/demo/batch-events?count=10
     * 
     * Результат в Horizon:
     * - Вкладка "Ожидающие выполнения задания" → видите 10 ProcessEvent
     * - Можете фильтровать по тегам user:*
     */
    public function batchEvents(): JsonResponse
    {
        $count = request()->query('count', 5);
        $count = min($count, 100); // Максимум 100

        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'event' => "Batch Event #{$i}",
                'data' => [
                    'user_id' => rand(1, 10),
                    'email' => "user{$i}@example.com",
                    'timestamp' => now(),
                    'metadata' => [
                        'source' => 'horizon_demo_batch',
                        'type' => 'normal',
                        'batch_id' => 'batch_' . now()->timestamp,
                        'index' => $i,
                    ]
                ]
            ];

            ProcessEvent::dispatch($data)->onQueue('low');
        }

        Log::info("📤 Отправлено {$count} событий в очередь");

        return response()->json([
            'status' => 'success',
            'message' => "Отправлено {$count} событий в очередь",
            'count' => $count,
            'horizon_url' => url('/horizon'),
            'tips' => [
                'Откройте Horizon Dashboard',
                'Смотрите как растёт количество задач в "Ожидающие выполнения задания"',
                'Нажмите на тег user:5 чтобы увидеть только задачи пользователя 5',
                'Запустите php artisan horizon чтобы начать обработку',
            ]
        ]);
    }

    /**
     * Демонстрация: Отправить отчёты
     * 
     * Пример: POST /api/horizon/demo/send-reports?type=daily
     * 
     * Результат в Horizon:
     * - Вкладка "Ожидающие выполнения задания" → видите SendReport
     * - Теги: report:daily, email:admin, mailing
     * - Можете отследить попытки отправки (tries=3)
     */
    public function sendReports(): JsonResponse
    {
        $type = request()->query('type', 'daily');
        $emails = [
            'admin@example.com',
            'manager@example.com',
            'support@example.com',
        ];

        foreach ($emails as $email) {
            SendReport::dispatch(
                email: $email,
                reportType: $type,
                data: [
                    'period' => now()->format('Y-m-d'),
                    'generated_at' => now(),
                ]
            )->onQueue('default');
        }

        Log::info("📧 Отправлено {$type} отчётов для " . count($emails) . " адресов");

        return response()->json([
            'status' => 'success',
            'message' => "Отправлено {$type} отчётов",
            'report_type' => $type,
            'recipients' => $emails,
            'horizon_url' => url('/horizon'),
            'what_to_see' => [
                'Вкладка "Ожидающие выполнения задания" → SendReport задачи',
                'Теги: report:' . $type . ', email:admin, email:manager, email:support',
                'Каждая задача имеет 3 попытки (tries=3)',
                'Если ошибка → задача переместится в "Неудачные задания"',
            ]
        ]);
    }

    /**
     * Демонстрация: Запустить мониторинг вручную
     * 
     * Пример: POST /api/horizon/demo/monitor-now
     * 
     * Результат в Horizon:
     * - Вкладка "Ожидающие выполнения задания" → видите MonitorQueueLoad
     * - Теги: monitoring, queue-stats, critical
     * - Логирует статистику очереди
     */
    public function monitorNow(): JsonResponse
    {
        MonitorQueueLoad::dispatch();

        Log::info('🔍 Мониторинг очереди запущен вручную');

        return response()->json([
            'status' => 'success',
            'message' => 'Мониторинг запущен',
            'horizon_url' => url('/horizon'),
            'what_happens' => [
                'MonitorQueueLoad задача добавлена в очередь',
                'Она получит теги: monitoring, queue-stats, critical',
                'После выполнения → проверит пороги нагрузки',
                'Если pending > 100 → сработает warning',
                'Если total > 150 → сработает error',
            ]
        ]);
    }

    /**
     * Демонстрация: Комплексный сценарий
     * 
     * Пример: POST /api/horizon/demo/complex-scenario
     * 
     * Результат в Horizon:
     * - Множество разных типов задач
     * - Разные теги для фильтрации
     * - Полная демонстрация возможностей
     */
    public function complexScenario(): JsonResponse
    {
        $startTime = now();

        // 1. Отправляем события
        for ($i = 1; $i <= 20; $i++) {
            $data = [
                'event' => "Complex Event #{$i}",
                'data' => [
                    'user_id' => rand(1, 5),
                    'email' => "user{$i}@example.com",
                    'timestamp' => now(),
                    'metadata' => [
                        'source' => 'complex_scenario',
                        'type' => rand(0, 1) ? 'normal' : 'heavy',
                        'scenario' => 'complex',
                    ]
                ]
            ];

            ProcessEvent::dispatch($data)->onQueue('default');
        }

        // 2. Отправляем отчёты
        SendReport::dispatch(
            email: 'admin@example.com',
            reportType: 'complex_demo',
            data: ['scenario' => 'complex']
        )->onQueue('default');

        // 3. Запускаем мониторинг
        MonitorQueueLoad::dispatch();

        $duration = now()->diffInMilliseconds($startTime);

        Log::info('🎬 Комплексный сценарий запущен', [
            'events' => 20,
            'reports' => 1,
            'monitors' => 1,
            'duration_ms' => $duration,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Комплексный сценарий запущен',
            'tasks_created' => [
                'ProcessEvent' => 20,
                'SendReport' => 1,
                'MonitorQueueLoad' => 1,
            ],
            'total_tasks' => 22,
            'duration_ms' => $duration,
            'horizon_url' => url('/horizon'),
            'what_to_do' => [
                '1. Откройте http://localhost/horizon',
                '2. Смотрите вкладку "Ожидающие выполнения задания" - 22 задачи',
                '3. Нажмите на разные теги для фильтрации',
                '4. Запустите: php artisan horizon',
                '5. Смотрите как задачи переходят в "Выполненные"',
                '6. Проверьте "Метрики" для статистики',
                '7. Если есть ошибки → смотрите "Неудачные задания"',
            ],
            'tags_to_explore' => [
                'event:Complex Event #1',
                'user:1, user:2, user:3, user:4, user:5',
                'type:normal, type:heavy',
                'report:complex_demo',
                'email:admin',
                'monitoring',
                'queue-stats',
            ]
        ]);
    }

    /**
     * Демонстрация: Информация о Horizon
     * 
     * Пример: GET /api/horizon/demo/info
     */
    public function info(): JsonResponse
    {
        return response()->json([
            'horizon_enabled' => true,
            'horizon_url' => url('/horizon'),
            'queue_driver' => config('queue.default'),
            'redis_connection' => config('queue.connections.redis.connection'),
            'available_endpoints' => [
                'POST /api/horizon/demo/single-event' => 'Одно событие',
                'POST /api/horizon/demo/batch-events?count=10' => 'Пакет событий',
                'POST /api/horizon/demo/send-reports?type=daily' => 'Отправить отчёты',
                'POST /api/horizon/demo/monitor-now' => 'Запустить мониторинг',
                'POST /api/horizon/demo/complex-scenario' => 'Комплексный сценарий',
                'GET /api/horizon/demo/info' => 'Информация',
            ],
            'quick_start' => [
                '1. Откройте http://localhost/horizon',
                '2. Запустите: php artisan horizon',
                '3. Отправьте POST запрос на один из endpoints выше',
                '4. Смотрите результаты в Horizon Dashboard',
            ],
            'horizon_tabs' => [
                'Мониторинг' => 'Основная вкладка с тегами',
                'Метрики' => 'Статистика и графики',
                'Пакеты' => 'Батчи задач',
                'Ожидающие выполнения задания' => 'Задачи в очереди',
                'Выполненные задания' => 'Успешно завершённые',
                'Отключенные задания' => 'Паузированные',
                'Неудачные задания' => 'С ошибками',
            ]
        ]);
    }
}
