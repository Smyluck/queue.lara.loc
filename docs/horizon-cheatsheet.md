# 📋 Laravel Horizon - Шпаргалка

## ⚡ Быстрый старт (30 секунд)

```bash
# 1. Запустить Horizon
php artisan horizon

# 2. В другом терминале - отправить задачи
php artisan queue:generate-load 50

# 3. Открыть Dashboard
http://localhost/horizon

# 4. Смотреть как задачи обрабатываются в реальном времени
```

---

## 🎯 Основные вкладки Horizon

| Вкладка | Что показывает | Для чего |
|---------|---|---|
| **Мониторинг** | Теги для фильтрации | Быстрый поиск задач |
| **Метрики** | Графики и статистика | Анализ производительности |
| **Пакеты** | Батчи задач | Отслеживание групп |
| **Ожидающие выполнения задания** | Задачи в очереди | Видеть что ждёт обработки |
| **Выполненные задания** | Успешные задачи | Проверить результаты |
| **Отключенные задания** | Паузированные | Управление |
| **Неудачные задания** | Ошибки | Отладка и переотправка |

---

## 🏷️ Теги - Как использовать

### Добавить теги в Job
```php
class ProcessEvent implements ShouldQueue
{
    public function tags(): array
    {
        return [
            'event:' . $this->data['event'],
            'user:' . $this->data['user_id'],
            'type:' . $this->data['type'],
        ];
    }
}
```

### Фильтровать в Horizon
```
1. Откройте http://localhost/horizon
2. Вкладка "Мониторинг"
3. Нажмите на тег (например, user:42)
4. Видите только задачи этого пользователя
```

---

## 📤 Отправить задачу в очередь

### Способ 1: Через dispatch()
```php
ProcessEvent::dispatch($data)->onQueue('default');
```

### Способ 2: Через контроллер
```php
// routes/api.php
Route::post('/api/horizon/demo/single-event', [HorizonDemoController::class, 'singleEvent']);

// Отправить
curl -X POST http://localhost/api/horizon/demo/single-event
```

### Способ 3: Через команду
```bash
php artisan queue:generate-load 100 --type=mixed
```

---

## 🔄 Жизненный цикл задачи

```
1. Dispatch (отправка)
   ↓
2. Ожидающие выполнения задания (в очереди)
   ↓
3. Выполнение (рабочий берёт задачу)
   ↓
4. Успех ✅ → Выполненные задания
   ИЛИ
   Ошибка ❌ → Неудачные задания (с повторами)
```

---

## 🚀 Команды для работы

```bash
# Запустить Horizon
php artisan horizon

# Остановить (Ctrl+C)
# или
php artisan horizon:terminate

# Перезагрузить
php artisan horizon:restart

# Снимок метрик
php artisan horizon:snapshot

# Очистить неудачные
php artisan horizon:forget-failed

# Генерировать нагрузку
php artisan queue:generate-load 50 --type=normal
php artisan queue:generate-load 50 --type=heavy
php artisan queue:generate-load 100 --type=mixed

# Переотправить неудачную задачу
php artisan queue:retry {id}

# Удалить неудачную задачу
php artisan queue:forget {id}
```

---

## 📊 Что видите в Horizon

### Ожидающие выполнения задания
```
ProcessEvent #1 - event: "Demo Event", user: 123
ProcessEvent #2 - event: "Demo Event", user: 124
SendReport - email: admin@example.com
MonitorQueueLoad - monitoring queue stats
```

### Выполненные задания
```
✅ ProcessEvent - 245ms - 16:45:30
✅ SendReport - 1.2s - 16:45:45
✅ MonitorQueueLoad - 450ms - 16:46:00
```

### Неудачные задания
```
❌ SendReport - SMTP timeout - 1/3 попыток
❌ ProcessEvent - Email invalid - 0/3 попыток
```

### Метрики
```
Всего обработано: 1,245
Успешно: 1,200
Ошибок: 45
Среднее время: 1.2s
Пиковая нагрузка: 156 задач
```

---

## 🎯 Практические примеры

### Пример 1: Одна задача
```bash
curl -X POST http://localhost/api/horizon/demo/single-event
```
**Результат:** 1 ProcessEvent в очереди

### Пример 2: Пакет задач
```bash
curl -X POST "http://localhost/api/horizon/demo/batch-events?count=10"
```
**Результат:** 10 ProcessEvent с разными пользователями

### Пример 3: Отчёты
```bash
curl -X POST "http://localhost/api/horizon/demo/send-reports?type=daily"
```
**Результат:** 3 SendReport для разных адресов

### Пример 4: Мониторинг
```bash
curl -X POST http://localhost/api/horizon/demo/monitor-now
```
**Результат:** MonitorQueueLoad проверяет пороги

### Пример 5: Всё вместе
```bash
curl -X POST http://localhost/api/horizon/demo/complex-scenario
```
**Результат:** 22 задачи разных типов

---

## 🔧 Конфигурация

### config/horizon.php
```php
// Пороги для уведомлений
'waits' => [
    'redis:default' => 60,  // Если ждёт >60 сек
],

// Максимум рабочих
'maxProcesses' => 1,

// Минимум рабочих
'minProcesses' => 1,

// Балансировка нагрузки
'balance' => 'simple',  // или 'auto'
```

### config/queue.php
```php
// Драйвер очереди
'default' => env('QUEUE_CONNECTION', 'redis'),

// Redis соединение
'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
    'queue' => 'default',
    'retry_after' => 90,
],
```

---

## 🐛 Решение проблем

### Задачи не выполняются
```bash
# Проверьте Horizon запущен
ps aux | grep horizon

# Если нет, запустите
php artisan horizon

# Проверьте Redis
redis-cli ping
# Должно вернуть: PONG
```

### "Вы не отслеживаете теги"
```php
// Добавьте в Job класс
public function tags(): array
{
    return ['tag1', 'tag2'];
}
```

### Много ошибок
```
1. Вкладка "Неудачные задания"
2. Нажмите на задачу
3. Смотрите stack trace
4. Исправьте ошибку
5. Нажмите "Retry"
```

### Horizon не обновляется
```bash
# Очистите Redis
redis-cli FLUSHDB

# Перезагрузите Horizon
php artisan horizon:restart
```

---

## 📈 Оптимизация

### Увеличить производительность
```php
// config/horizon.php
'maxProcesses' => 10,  // Больше рабочих
'minProcesses' => 5,   // Минимум рабочих
```

### Использовать разные очереди
```php
// Высокий приоритет
ProcessEvent::dispatch($data)->onQueue('high');

// Низкий приоритет
ProcessEvent::dispatch($data)->onQueue('low');
```

### Оптимизировать handle()
```php
// ❌ Плохо
public function handle(): void
{
    sleep(10);  // Долго!
}

// ✅ Хорошо
public function handle(): void
{
    // Быстро выполнить
}
```

---

## 🎓 Структура Job класса

```php
class MyJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public array $data) {}

    // Основная логика
    public function handle(): void
    {
        // Ваш код
    }

    // Теги для фильтрации
    public function tags(): array
    {
        return ['tag1', 'tag2'];
    }

    // Количество попыток
    public function tries(): int
    {
        return 3;
    }

    // Задержка между попытками
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    // Максимальное время выполнения
    public function timeout(): int
    {
        return 120;  // 2 минуты
    }

    // Обработка ошибки
    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed', ['error' => $exception->getMessage()]);
    }
}
```

---

## 📱 API Endpoints

```bash
# Информация
GET /api/horizon/demo/info

# Одна задача
POST /api/horizon/demo/single-event

# Пакет задач
POST /api/horizon/demo/batch-events?count=10

# Отчёты
POST /api/horizon/demo/send-reports?type=daily

# Мониторинг
POST /api/horizon/demo/monitor-now

# Комплексный сценарий
POST /api/horizon/demo/complex-scenario
```

---

## 🔍 Как отследить задачу

```
1. Отправьте задачу
   curl -X POST http://localhost/api/horizon/demo/single-event

2. Откройте Horizon
   http://localhost/horizon

3. Вкладка "Мониторинг" → видите теги

4. Нажмите на тег (например, event:Demo Event)

5. Видите задачу в списке

6. Нажмите на задачу → детали

7. Запустите рабочих
   php artisan horizon

8. Смотрите как задача переходит в "Выполненные"

9. Проверьте логи
   tail -f storage/logs/daily-*.log
```

---

## 📚 Файлы проекта

| Файл | Назначение |
|------|-----------|
| `app/Jobs/ProcessEvent.php` | Обработка событий |
| `app/Jobs/SendReport.php` | Отправка отчётов |
| `app/Jobs/MonitorQueueLoad.php` | Мониторинг нагрузки |
| `app/Console/Commands/GenerateQueueLoad.php` | Генерация нагрузки |
| `app/Http/Controllers/HorizonDemoController.php` | API для демонстрации |
| `routes/api.php` | API маршруты |
| `routes/schedule.php` | Расписание задач |
| `config/horizon.php` | Конфигурация Horizon |
| `config/queue.php` | Конфигурация очереди |
| `docs/horizon-monitoring-guide.md` | Полное руководство |
| `docs/horizon-practical-examples.md` | Практические примеры |

---

## 🎯 Чек-лист для начинающих

- [ ] Запустил `php artisan horizon`
- [ ] Открыл `http://localhost/horizon`
- [ ] Отправил первую задачу через API
- [ ] Видю задачу в "Ожидающие выполнения задания"
- [ ] Видю как задача переходит в "Выполненные"
- [ ] Отфильтровал задачи по тегам
- [ ] Проверил "Метрики"
- [ ] Отправил пакет задач (batch)
- [ ] Запустил комплексный сценарий
- [ ] Прочитал полное руководство

---

## 💡 Полезные советы

1. **Всегда добавляйте теги** - они помогают быстро найти задачи
2. **Используйте разные очереди** - для приоритизации
3. **Логируйте всё** - помогает при отладке
4. **Проверяйте пороги** - настройте уведомления
5. **Мониторьте метрики** - видите проблемы рано
6. **Переотправляйте ошибки** - не теряйте данные
7. **Оптимизируйте handle()** - быстрее = лучше
8. **Используйте backoff** - умные повторы при ошибках

---

## 🚀 Следующие шаги

1. Прочитайте `docs/horizon-monitoring-guide.md` - полное объяснение
2. Изучите `docs/horizon-practical-examples.md` - примеры использования
3. Экспериментируйте с API endpoints
4. Добавьте свои Job классы с тегами
5. Настройте пороги в `config/horizon.php`
6. Интегрируйте в свой проект

---

**Готово! Теперь вы знаете как работает Laravel Horizon Monitoring! 🎉**
