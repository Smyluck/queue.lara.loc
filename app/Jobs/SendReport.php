<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Report;
use Illuminate\Bus\Batchable;

class SendReport implements ShouldQueue
{
    use Batchable, Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public string $reportType,
        public array $data = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Отправка отчёта {$this->reportType} на {$this->email}", $this->data);
        
        try {
            Mail::to($this->email)->send(new Report("Отчёт: {$this->reportType}"));
            Log::info("✅ Отчёт успешно отправлен на {$this->email}");
        } catch (\Exception $e) {
            Log::error("❌ Ошибка отправки отчёта: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     * 
     * Теги позволяют фильтровать задачи в Horizon Dashboard
     */
    public function tags(): array
    {
        return [
            'report:' . $this->reportType,
            'email:' . substr($this->email, 0, strpos($this->email, '@')),
            'mailing',
        ];
    }

    /**
     * Определяем, сколько раз повторять задачу при ошибке
     */
    public function tries(): int
    {
        return 3;
    }

    /**
     * Определяем задержку между попытками (в секундах)
     */
    public function backoff(): array
    {
        return [10, 30, 60];  // 10s, 30s, 60s
    }
}
