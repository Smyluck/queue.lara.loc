./vendor/bin/sail exec rabbitmq rabbitmqctl add_user sail password
./vendor/bin/sail exec rabbitmq rabbitmqctl set_user_tags sail administrator
./vendor/bin/sail exec rabbitmq rabbitmqctl set_permissions -p / sail ".*" ".*" ".*"


## генерация документации swagger

```bash
php artisan l5-swagger:generate


##Redis -Horizon


```.env
QUEUE_CONNECTION=redis


- Для того, чтобы очередь попала в очередь из состояния "Pending Jobs" в "Completed", нужно включить sail artisan queue:work
- Если нужно полностью перезапустить Horizon (включая очистку кэша и состояния), используйте:

```php
php artisan horizon:pause && php artisan horizon:terminate && php artisan horizon:continue

- для того, чтобы задача попадала в Silenced Jobs, нужно добавить в файл horizon.php 

```php
 'silenced' => [
    App\Jobs\ProcessEvent::class,
 ],

ProcessEvent в массиве silenced → не показывается в completed
throw new Exception → задача завершается с ошибкой
Failed jobs всегда видны (даже silenced)

🔇 Silenced Jobs - когда применяются:
Шумные/частые задачи
Массовые операции
Отправка email-рассылок
Очистка кэша
Синхронизация данных
Технические задачи
Логирование
Мониторинг метрик
Автоматические бэкапы

Completed Jobs - когда нужны:
Мониторинг важных бизнес-процессов
Отладка и статистика
Проверка, что критичные задачи выполнились


## Мониторинг очередей и Dashboard

### Создана задача MonitorQueueLoad для мониторинга нагрузки

Job `App\Jobs\MonitorQueueLoad` отслеживает:
- **Pending**: очередь ожидающих задач
- **Delayed**: отложенные задачи
- **Reserved**: зарезервированные/выполняемые задачи

### Статистика собирается:
```php
// Каждые 5 минут автоматически через планировщик

$schedule->job(new MonitorQueueLoad())
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
```

### Уведомления Horizon

Настроены в `app/Providers/AppServiceProvider.php`:

```php
// Email уведомления
Horizon::routeMailNotificationsTo('admin@example.com');

// SMS уведомления (требует Vonage/Twilio)
Horizon::routeSmsNotificationsTo('+1234567890');
```

### Пороги срабатывания алертов (в MonitorQueueLoad):
- **Pending > 100 задач** → Warning в логах
- **Delayed > 50 задач** → Warning в логах
- **Total > 150 задач** → Critical error в логах

### Как использовать

1. Запустить очередь воркер:
```bash
./vendor/bin/sail artisan queue:work --queue=default
```

2. Запустить планировщик (для мониторинга каждые 5 минут):
```bash
./vendor/bin/sail artisan schedule:work
```

3. Запустить Horizon Dashboard:
```bash
./vendor/bin/sail artisan horizon
```

4. Открыть Dashboard:
```
http://localhost/horizon
```

### Структура проекта

- **Job**: `app/Jobs/MonitorQueueLoad.php` - мониторинг и сбор метрик
- **Уведомления**: `app/Providers/AppServiceProvider.php` - Horizon notifications
- **Расписание**: `app/Console/Kernel.php` - планировщик задач
- **Конфиг**: `config/horizon.php` - настройки Horizon

## Тестирование перегрузки очереди

### Команда для генерации нагрузки

Используйте `queue:generate-load` для отправки множества задач ProcessEvent:

```bash
# Отправить 50 обычных задач
./vendor/bin/sail artisan queue:generate-load

# Отправить 200 задач с задержкой 1 сек между отправками
./vendor/bin/sail artisan queue:generate-load 200 --delay=1

# Отправить 100 тяжелых задач (с большим объемом данных)
./vendor/bin/sail artisan queue:generate-load 100 --type=heavy

# Отправить 150 смешанных задач (normal + heavy)
./vendor/bin/sail artisan queue:generate-load 150 --type=mixed
```

### Параметры команды:

- `count` — количество задач (по умолчанию 50)
- `--type` — тип нагрузки:
  - `normal` — обычные задачи
  - `heavy` — тяжелые задачи с большим объемом данных
  - `mixed` — смесь обычных и тяжелых
- `--delay` — задержка в секундах между отправками (по умолчанию 0)

### Мониторинг перегрузки:

1. **Horizon Dashboard** покажет рост Pending Jobs
2. **MonitorQueueLoad** каждые 5 минут проверит пороги:
   - Pending > 100 → Warning в логах
   - Total > 150 → Critical error
3. **Email уведомления** придут при превышении порогов
4. **Логи** в `storage/logs/laravel.log` с метриками