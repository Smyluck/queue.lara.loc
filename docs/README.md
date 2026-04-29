# 🎯 Laravel Horizon Monitoring - Полная документация

## 📚 Добро пожаловать!

Это полная документация по **Laravel Horizon Monitoring** на примере вашего проекта. Здесь вы найдёте всё что нужно для работы с очередями и мониторинга в Laravel.

---

## 🚀 Быстрый старт (5 минут)

### 1. Запустить Horizon
```bash
php artisan horizon
```

### 2. Открыть Dashboard
```
http://localhost/horizon
```

### 3. Отправить задачи
```bash
curl -X POST http://localhost/api/horizon/demo/single-event
```

### 4. Смотреть результаты
- Вкладка "Ожидающие выполнения задания" → новые задачи
- Вкладка "Выполненные задания" → завершённые
- Вкладка "Метрики" → статистика

---

## 📖 Документация

### 🎯 [INDEX.md](INDEX.md) - Полный индекс
**Начните отсюда!** Полный индекс всей документации с рекомендуемым путём обучения.

### ⚡ [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Быстрая справка (5 мин)
Шпаргалка с основными командами, примерами и решением проблем.

**Содержит:**
- Быстрый старт за 5 минут
- Визуальные схемы вкладок
- Как работают теги
- API endpoints
- Жизненный цикл задачи
- Основные команды
- Структура Job класса

### 📘 [HORIZON_README.md](HORIZON_README.md) - Полное введение (30 мин)
Подробное введение в Horizon с объяснением всех компонентов.

**Содержит:**
- Что такое Horizon Monitoring
- Структура проекта
- Быстрый старт
- Описание всех 7 вкладок
- Теги и фильтрация
- Примеры использования
- API для демонстрации
- Отладка и решение проблем

### 🎯 [horizon-practical-examples.md](horizon-practical-examples.md) - Практические примеры (1 час)
Реальные примеры использования с подробными объяснениями.

**Содержит:**
- 5 практических примеров
- Сценарии использования
- Как читать Horizon Dashboard
- Отладка и решение проблем
- Оптимизация производительности
- Полезные команды

### 📋 [horizon-cheatsheet.md](horizon-cheatsheet.md) - Шпаргалка (справочник)
Быстрая справка по всем аспектам Horizon.

**Содержит:**
- Основные вкладки (таблица)
- Теги - как использовать
- Отправить задачу в очередь
- Жизненный цикл задачи
- Команды для работы
- Что видите в Horizon
- Конфигурация
- Решение проблем

### 🎓 [horizon-monitoring-guide.md](horizon-monitoring-guide.md) - Подробное руководство (2 часа)
Полное руководство с глубоким разбором всех аспектов.

**Содержит:**
- Что такое Horizon Monitoring
- Структура вашего проекта
- Вкладки Horizon (подробно)
- Теги - ключевая фишка
- Метрики
- Как работает ваш мониторинг
- Как использовать Horizon
- Пример реального сценария
- Конфигурация
- Логирование
- Практические советы
- Частые проблемы

### 🏗️ [ARCHITECTURE.md](ARCHITECTURE.md) - Архитектура и диаграммы
Визуальные диаграммы архитектуры системы.

**Содержит:**
- Общая архитектура системы
- Жизненный цикл задачи (детально)
- Структура компонентов
- Поток данных
- Взаимодействие компонентов
- Масштабирование
- Безопасность
- Мониторинг и метрики
- Production архитектура

---

## 🗂️ Структура проекта

```
docs/
├── README.md                         ← Вы здесь
├── INDEX.md                          ← Полный индекс
├── QUICK_REFERENCE.md                ← Быстрая справка
├── HORIZON_README.md                 ← Полное введение
├── horizon-monitoring-guide.md       ← Подробное руководство
├── horizon-practical-examples.md     ← Практические примеры
├── horizon-cheatsheet.md             ← Шпаргалка
└── ARCHITECTURE.md                   ← Архитектура

app/Jobs/
├── ProcessEvent.php                  ← Обработка событий
├── SendReport.php                    ← Отправка отчётов
└── MonitorQueueLoad.php              ← Мониторинг нагрузки

app/Console/Commands/
└── GenerateQueueLoad.php             ← Генерация нагрузки

app/Http/Controllers/
└── HorizonDemoController.php         ← API для демонстрации

routes/
├── api.php                           ← API маршруты
└── schedule.php                      ← Расписание задач

config/
├── horizon.php                       ← Конфигурация Horizon
└── queue.php                         ← Конфигурация очереди
```

---

## 🎯 Рекомендуемый путь обучения

### Для нетерпеливых (5 минут)
1. Прочитайте [QUICK_REFERENCE.md](QUICK_REFERENCE.md)
2. Запустите `php artisan horizon`
3. Откройте `http://localhost/horizon`
4. Отправьте первую задачу

### Для новичков (1 час)
1. Прочитайте [QUICK_REFERENCE.md](QUICK_REFERENCE.md) (5 мин)
2. Запустите примеры (15 мин)
3. Прочитайте [HORIZON_README.md](HORIZON_README.md) (30 мин)
4. Экспериментируйте (10 мин)

### Для практиков (2 часа)
1. Прочитайте [horizon-practical-examples.md](horizon-practical-examples.md) (30 мин)
2. Попробуйте все примеры (30 мин)
3. Прочитайте [horizon-cheatsheet.md](horizon-cheatsheet.md) (30 мин)
4. Добавьте свои Job классы (30 мин)

### Для углубления (3 часа)
1. Прочитайте [horizon-monitoring-guide.md](horizon-monitoring-guide.md) (1 час)
2. Изучите [ARCHITECTURE.md](ARCHITECTURE.md) (30 мин)
3. Настройте конфигурацию (30 мин)
4. Интегрируйте в проект (1 час)

---

## 📡 API Endpoints для демонстрации

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

## 🛠️ Основные команды

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

# Переотправить неудачную задачу
php artisan queue:retry {id}

# Удалить неудачную задачу
php artisan queue:forget {id}
```

---

## 📊 Вкладки Horizon

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

---

## 🔄 Жизненный цикл задачи

```
1. DISPATCH (отправка)
   ↓
2. ОЖИДАНИЕ (в очереди)
   ↓
3. ВЫПОЛНЕНИЕ (рабочий берёт задачу)
   ↓
4. РЕЗУЛЬТАТ
   ├─ ✅ УСПЕХ → "Выполненные задания"
   └─ ❌ ОШИБКА → "Неудачные задания"
      ├─ Попытка 1/3
      ├─ Попытка 2/3
      └─ Попытка 3/3 → Финальная ошибка
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

---

## 💡 Полезные советы

1. **Всегда добавляйте теги** — помогают найти задачи
2. **Логируйте всё** — помогает при отладке
3. **Используйте разные очереди** — для приоритизации
4. **Мониторьте метрики** — видите проблемы рано
5. **Переотправляйте ошибки** — не теряйте данные
6. **Оптимизируйте handle()** — быстрее = лучше
7. **Используйте backoff** — умные повторы
8. **Проверяйте пороги** — настройте уведомления

---

## 🎓 Чек-лист для начинающих

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

## 📚 Файлы проекта

### Job классы
- [`app/Jobs/ProcessEvent.php`](../app/Jobs/ProcessEvent.php) — Обработка событий с тегами
- [`app/Jobs/SendReport.php`](../app/Jobs/SendReport.php) — Отправка отчётов с повторами
- [`app/Jobs/MonitorQueueLoad.php`](../app/Jobs/MonitorQueueLoad.php) — Мониторинг нагрузки

### Контроллеры и команды
- [`app/Http/Controllers/HorizonDemoController.php`](../app/Http/Controllers/HorizonDemoController.php) — API для демонстрации
- [`app/Console/Commands/GenerateQueueLoad.php`](../app/Console/Commands/GenerateQueueLoad.php) — Генерация нагрузки

### Конфигурация
- [`config/horizon.php`](../config/horizon.php) — Конфигурация Horizon
- [`config/queue.php`](../config/queue.php) — Конфигурация очереди
- [`routes/api.php`](../routes/api.php) — API маршруты
- [`routes/schedule.php`](../routes/schedule.php) — Расписание задач

---

## 🔗 Ссылки

### Документация в проекте
- [INDEX.md](INDEX.md) — Полный индекс
- [QUICK_REFERENCE.md](QUICK_REFERENCE.md) — Быстрая справка
- [HORIZON_README.md](HORIZON_README.md) — Полное введение
- [horizon-practical-examples.md](horizon-practical-examples.md) — Практические примеры
- [horizon-cheatsheet.md](horizon-cheatsheet.md) — Шпаргалка
- [horizon-monitoring-guide.md](horizon-monitoring-guide.md) — Подробное руководство
- [ARCHITECTURE.md](ARCHITECTURE.md) — Архитектура

### Официальная документация
- [Laravel Horizon Documentation](https://laravel.com/docs/horizon)
- [Queue Configuration](https://laravel.com/docs/queues)
- [Job Tags](https://laravel.com/docs/queues#job-tags)

---

## 🚀 Следующие шаги

1. **Выберите документ** по вашему уровню
2. **Запустите примеры** из документации
3. **Экспериментируйте** с API endpoints
4. **Добавьте свои Job классы** в проект
5. **Интегрируйте Horizon** в ваше приложение

---

## 📞 Поддержка

Если у вас есть вопросы:
1. Проверьте [INDEX.md](INDEX.md) — там есть быстрый поиск по темам
2. Смотрите раздел "Решение проблем" в документации
3. Проверьте логи: `tail -f storage/logs/daily-*.log`
4. Откройте Horizon Dashboard: `http://localhost/horizon`

---

## 🎉 Готово!

Вы готовы начать работу с Laravel Horizon Monitoring!

**Рекомендуем начать с:**
1. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) — займёт 5 минут
2. Запустить `php artisan horizon`
3. Открыть `http://localhost/horizon`
4. Отправить первую задачу

**Удачи! 🚀**
