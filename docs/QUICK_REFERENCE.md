# ⚡ Laravel Horizon - Быстрая справка

## 🎯 За 5 минут

### Шаг 1: Запустить
```bash
php artisan horizon
```

### Шаг 2: Открыть
```
http://localhost/horizon
```

### Шаг 3: Отправить задачи
```bash
# Вариант 1
curl -X POST http://localhost/api/horizon/demo/single-event

# Вариант 2
php artisan queue:generate-load 50

# Вариант 3
curl -X POST http://localhost/api/horizon/demo/complex-scenario
```

### Шаг 4: Смотреть
- Вкладка "Ожидающие выполнения задания" → новые задачи
- Вкладка "Выполненные задания" → завершённые
- Вкладка "Метрики" → статистика

---

## 📊 Вкладки Horizon

```
┌─────────────────────────────────────────────────────────┐
│                    HORIZON DASHBOARD                    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  [Мониторинг] [Метрики] [Пакеты] [Ожидающие]          │
│  [Выполненные] [Отключенные] [Неудачные]              │
│                                                         │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 1. МОНИТОРИНГ - Теги для фильтрации                    │
├─────────────────────────────────────────────────────────┤
│ event:Demo Event          (1)                           │
│ user:1                    (5)                           │
│ user:2                    (3)                           │
│ type:normal               (7)                           │
│ type:heavy                (1)                           │
│ report:daily              (1)                           │
│ monitoring                (1)                           │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 2. МЕТРИКИ - Графики и статистика                      │
├─────────────────────────────────────────────────────────┤
│ Всего обработано: 1,245                                 │
│ Успешно: 1,200                                          │
│ Ошибок: 45                                              │
│ Среднее время: 1.2s                                     │
│ Пиковая нагрузка: 156 задач                             │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 3. ОЖИДАЮЩИЕ ВЫПОЛНЕНИЯ - Задачи в очереди             │
├─────────────────────────────────────────────────────────┤
│ ProcessEvent (user:1)                                   │
│ ProcessEvent (user:2)                                   │
│ SendReport (email:admin)                                │
│ MonitorQueueLoad (monitoring)                           │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 4. ВЫПОЛНЕННЫЕ - Успешные задачи                       │
├─────────────────────────────────────────────────────────┤
│ ✅ ProcessEvent - 245ms - 16:45                         │
│ ✅ SendReport - 1.2s - 16:46                            │
│ ✅ MonitorQueueLoad - 450ms - 16:46                     │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ 5. НЕУДАЧНЫЕ - Ошибки                                  │
├─────────────────────────────────────────────────────────┤
│ ❌ SendReport - SMTP timeout - 1/3 попыток             │
│ ❌ ProcessEvent - Email invalid - 0/3 попыток          │
│ [Retry] [Delete]                                        │
└─────────────────────────────────────────────────────────┘
```

---

## 🏷️ Теги - Как работают

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

### Использовать в Horizon
```
1. Откройте http://localhost/horizon
2. Вкладка "Мониторинг"
3. Нажмите на тег (например, user:42)
4. Видите только задачи этого пользователя
```

### Примеры тегов
```
event:Demo Event      ← Тип события
user:42               ← ID пользователя
type:normal           ← Тип задачи
report:daily          ← Тип отчёта
email:admin           ← Email адрес
monitoring            ← Категория
critical              ← Приоритет
```

---

## 📡 API Endpoints

```bash
# Информация
GET /api/horizon/demo/info

# Одна задача
POST /api/horizon/demo/single-event

# Пакет задач (10 штук)
POST /api/horizon/demo/batch-events?count=10

# Отчёты (daily/weekly)
POST /api/horizon/demo/send-reports?type=daily

# Мониторинг вручную
POST /api/horizon/demo/monitor-now

# Комплексный сценарий (22 задачи)
POST /api/horizon/demo/complex-scenario
```

---

## 🔄 Жизненный цикл задачи

```
1. DISPATCH (отправка)
   ↓
   ProcessEvent::dispatch($data)
   ↓
2. ОЖИДАНИЕ (в очереди)
   ↓
   Вкладка "Ожидающие выполнения задания"
   ↓
3. ВЫПОЛНЕНИЕ (рабочий берёт задачу)
   ↓
   php artisan horizon (запущен)
   ↓
4. РЕЗУЛЬТАТ
   ↓
   ├─ ✅ УСПЕХ → "Выполненные задания"
   │
   └─ ❌ ОШИБКА → "Неудачные задания"
      ├─ Попытка 1/3
      ├─ Попытка 2/3
      └─ Попытка 3/3 → Финальная ошибка
```

---

## 🛠️ Команды

```bash
# Запустить Horizon
php artisan horizon

# Остановить (Ctrl+C)

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

# Переотправить неудачную
php artisan queue:retry {id}

# Удалить неудачную
php artisan queue:forget {id}
```

---

## 📋 Job структура

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

    // Задержка между попытками (сек)
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    // Максимальное время выполнения (сек)
    public function timeout(): int
    {
        return 120;
    }

    // Обработка ошибки
    public function failed(\Throwable $exception): void
    {
        Log::error('Job failed', ['error' => $exception->getMessage()]);
    }
}
```

---

## 🐛 Решение проблем

### Задачи не выполняются
```bash
# Проверьте Horizon запущен
ps aux | grep horizon

# Если нет
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
redis-cli FLUSHDB
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

### Разные очереди
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

## 📚 Документация

| Файл | Содержание |
|------|-----------|
| `docs/HORIZON_README.md` | Полная документация |
| `docs/horizon-monitoring-guide.md` | Подробное руководство |
| `docs/horizon-practical-examples.md` | Практические примеры |
| `docs/horizon-cheatsheet.md` | Шпаргалка |
| `docs/QUICK_REFERENCE.md` | Эта справка |

---

## 🎯 Практический пример

### Сценарий: Обработка 100 событий

```bash
# 1. Запустить Horizon
php artisan horizon

# 2. В другом терминале - отправить события
curl -X POST "http://localhost/api/horizon/demo/batch-events?count=100"

# 3. Открыть Horizon
http://localhost/horizon

# 4. Смотреть результаты
# - Вкладка "Ожидающие выполнения задания" → 100 задач
# - Вкладка "Выполненные задания" → растёт количество
# - Вкладка "Метрики" → видите статистику

# 5. Фильтровать по пользователю
# - Нажмите на тег user:5
# - Видите только его события

# 6. Проверить ошибки
# - Вкладка "Неудачные задания"
# - Если есть → нажмите "Retry"
```

---

## 💡 Советы

1. **Всегда добавляйте теги** — помогают найти задачи
2. **Логируйте всё** — помогает при отладке
3. **Используйте разные очереди** — для приоритизации
4. **Мониторьте метрики** — видите проблемы рано
5. **Переотправляйте ошибки** — не теряйте данные
6. **Оптимизируйте handle()** — быстрее = лучше
7. **Используйте backoff** — умные повторы
8. **Проверяйте пороги** — настройте уведомления

---

## 🚀 Следующие шаги

1. Запустите `php artisan horizon`
2. Откройте `http://localhost/horizon`
3. Отправьте первую задачу
4. Смотрите как она выполняется
5. Прочитайте полную документацию
6. Добавьте свои Job классы
7. Интегрируйте в проект

---

**Готово! Начните с быстрого старта выше! 🎉**
