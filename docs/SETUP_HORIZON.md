# 🚀 Как запустить Horizon и увидеть теги

## ✅ Проверка предварительных условий

### 1. Проверить что Redis запущен
```bash
docker ps | grep redis
```

Должно вывести:
```
redis:alpine ... Up ... 0.0.0.0:6379->6379/tcp
```

### 2. Проверить что Laravel контейнер запущен
```bash
docker ps | grep laravel
```

Должно вывести:
```
sail-8.5/app ... Up ... 0.0.0.0:80->80/tcp
```

### 3. Проверить конфигурацию очереди
```bash
cat .env | grep QUEUE_CONNECTION
```

Должно быть:
```
QUEUE_CONNECTION=redis
```

---

## 🎯 Запуск Horizon

### Способ 1: Через Docker (рекомендуется)

#### Шаг 1: Войти в контейнер
```bash
docker exec -it queuelaraloc-laravel.test-1 bash
```

#### Шаг 2: Запустить Horizon
```bash
php artisan horizon
```

Вы должны увидеть:
```
Starting Horizon...
Horizon started successfully.
```

#### Шаг 3: Открыть Horizon Dashboard
```
http://localhost/horizon
```

---

### Способ 2: Через Sail (если установлен)

```bash
./vendor/bin/sail artisan horizon
```

---

## 📤 Отправить задачи в очередь

### Способ 1: Через API (в другом терминале)

```bash
# Одна задача
curl -X POST http://localhost/api/horizon/demo/single-event

# Пакет задач
curl -X POST "http://localhost/api/horizon/demo/batch-events?count=10"

# Комплексный сценарий
curl -X POST http://localhost/api/horizon/demo/complex-scenario
```

### Способ 2: Через команду (в контейнере)

```bash
# Войти в контейнер
docker exec -it queuelaraloc-laravel.test-1 bash

# Генерировать нагрузку
php artisan queue:generate-load 50 --type=normal
php artisan queue:generate-load 50 --type=heavy
php artisan queue:generate-load 100 --type=mixed
```

---

## 🔍 Что вы должны увидеть в Horizon

### Вкладка "Мониторинг"
```
event:Demo Event          (1)
user:1                    (5)
user:2                    (3)
type:normal               (7)
type:heavy                (1)
report:daily              (1)
monitoring                (1)
```

### Вкладка "Ожидающие выполнения задания"
```
ProcessEvent (user:1)
ProcessEvent (user:2)
SendReport (email:admin)
MonitorQueueLoad (monitoring)
```

### Вкладка "Выполненные задания"
```
✅ ProcessEvent - 245ms - 16:45
✅ SendReport - 1.2s - 16:46
✅ MonitorQueueLoad - 450ms - 16:46
```

---

## 🐛 Решение проблем

### Проблема: "Вы не отслеживаете теги"

**Решение:** Это нормально, если нет задач в очереди. Отправьте задачи:
```bash
curl -X POST http://localhost/api/horizon/demo/single-event
```

### Проблема: Horizon не обновляется

**Решение:** Перезагрузите страницу или перезагрузите Horizon:
```bash
# В контейнере
php artisan horizon:restart
```

### Проблема: Задачи не выполняются

**Решение:** Убедитесь что Horizon запущен:
```bash
# Проверить процессы
docker exec queuelaraloc-laravel.test-1 ps aux | grep horizon
```

### Проблема: Redis недоступен

**Решение:** Проверьте что Redis запущен:
```bash
docker ps | grep redis
```

Если не запущен, запустите контейнеры:
```bash
docker-compose up -d
```

---

## 📊 Полный цикл (пошагово)

### Терминал 1: Запустить Horizon
```bash
docker exec -it queuelaraloc-laravel.test-1 bash
php artisan horizon
```

Вы должны увидеть:
```
Starting Horizon...
Horizon started successfully.
```

### Терминал 2: Отправить задачи
```bash
# Одна задача
curl -X POST http://localhost/api/horizon/demo/single-event

# Или пакет
curl -X POST "http://localhost/api/horizon/demo/batch-events?count=10"

# Или комплексный сценарий
curl -X POST http://localhost/api/horizon/demo/complex-scenario
```

### Браузер: Открыть Horizon
```
http://localhost/horizon
```

Вы должны увидеть:
1. Вкладка "Мониторинг" → теги с количеством задач
2. Вкладка "Ожидающие выполнения задания" → новые задачи
3. Вкладка "Выполненные задания" → завершённые задачи
4. Вкладка "Метрики" → статистика

---

## 🎯 Проверка конфигурации

### Проверить что очередь использует Redis
```bash
docker exec queuelaraloc-laravel.test-1 php artisan config:show queue
```

Должно быть:
```
default: redis
connections.redis.driver: redis
connections.redis.connection: default
```

### Проверить что Horizon настроен
```bash
docker exec queuelaraloc-laravel.test-1 php artisan config:show horizon
```

Должно быть:
```
use: default
prefix: queue_horizon:
```

---

## 📝 Команды для быстрого старта

```bash
# 1. Запустить контейнеры (если не запущены)
docker-compose up -d

# 2. Войти в контейнер
docker exec -it queuelaraloc-laravel.test-1 bash

# 3. Запустить Horizon
php artisan horizon

# 4. В другом терминале - отправить задачи
curl -X POST http://localhost/api/horizon/demo/complex-scenario

# 5. Открыть Horizon Dashboard
# http://localhost/horizon
```

---

## ✅ Чек-лист

- [x] Redis запущен (`docker ps | grep redis`)
- [x] Laravel контейнер запущен (`docker ps | grep laravel`)
- [x] QUEUE_CONNECTION=redis в .env
- [x] Horizon запущен (`php artisan horizon`)
- [x] Задачи отправлены (curl запрос)
- [x] Horizon Dashboard открыт (http://localhost/horizon)
- [ ] Видны теги в вкладке "Мониторинг"
- [ ] Видны задачи в вкладке "Ожидающие выполнения задания"

---

## 🚀 Готово!

Теперь вы должны видеть теги в Horizon Monitoring!
