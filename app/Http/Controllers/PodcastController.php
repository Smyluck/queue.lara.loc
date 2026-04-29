<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPodcast;
use App\Jobs\SendReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\Client\Batch;

class PodcastController extends Controller
{
    public function store(Request $request)
    {
        // Создание группы задач
        $batch = Bus::batch([
            new ProcessPodcast("Podkast1"),
            new SendReport("admin@domain.com", "Еженедельный отчёт", ['podcast' => "Podkast1"]),
            new SendReport("admin@domaincom", "Еженедельный отчёт", ['podcast' => "Podkast1"]),
        ])->then(function (Batch $batch) {
            // Этот код выполнится после завершения всех задач в пакетной задаче
            info('Все задачи в пакетной задаче завершены!');
        })->catch(function (Batch $batch, \Throwable $e) {
            // Этот код выполнится, если одна из задач в пакетной задаче завершится с ошибкой
            info('Одна из задач в пакетной задаче завершилась с ошибкой: ' . $e->getMessage());
        })->finally(function (Batch $batch) {
            // Этот код выполнится в любом случае после завершения всех задач
            info('Пакетная задача завершена!');
        })->dispatch();

        // Получение ID пакетной задачи
        $batchId = $batch->id;

        // Возвращаем ID пакетной задачи для отслеживания
        return response()->json(['batch_id' => $batchId]);
    }

    public function progress($batchId)
    {
        // Получение прогресса выполнения пакетной задачи
        $batch = Bus::findBatch($batchId);

        if (!$batch) {
            return response()->json(['error' => 'Пакетная задача не найдена'], 404);
        }

        // Возвращаем информацию о прогрессе выполнения пакетной задачи
        return response()->json([
            'totalJobs' => $batch->totalJobs,
            'pendingJobs' => $batch->pendingJobs,
            'failedJobs' => $batch->failedJobs,
            'processedJobs' => $batch->processedJobs(),
            'progress' => $batch->progress(),
            'finishedAt' => $batch->finishedAt,
            'createdAt' => $batch->createdAt,
            'cancelled' => $batch->cancelled(),
        ]);
    }
}