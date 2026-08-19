# AI-чат (Design 17)

Функция поиска товаров через чат-интерфейс с интеграцией AI-ассистента для дизайна 17.

## Обзор

AI-чат заменяет классический поиск на главной странице дизайна 17 чат-подобным интерфейсом. Пользователь вводит запрос (название препарата, симптом), и AI-ассистент возвращает текстовый ответ с подходящими товарами из каталога.

## Компоненты

### Backend

| Компонент | Путь | Назначение |
|-----------|------|-----------|
| ChatController | `app/Http/Controllers/ChatController.php` | Обработка sendMessage, pollMessage, getHistory |
| MedicalAssistantService | `app/Services/MedicalAssistantService.php` | HTTP-клиент для внешнего AI API |

### Frontend

| Компонент | Путь | Назначение |
|-----------|------|-----------|
| chat.js | `public/design_17/js/chat.js` | Vanilla JS логика чата (Design17Chat) |
| chat_message | `resources/views/design_17/ajax/chat_message.blade.php` | Blade-шаблон сообщения |
| chat_product_card | `resources/views/design_17/ajax/chat_product_card.blade.php` | Карточка товара |
| chat_product_drawer | `resources/views/design_17/ajax/chat_product_drawer.blade.php` | Детали товара в drawer |
| chat_skeleton | `resources/views/design_17/ajax/chat_skeleton.blade.php` | Скелетон загрузки |
| main.blade.php | `resources/views/design_17/layouts/main.blade.php` | Интеграция чата в layout |

## API-роуты

| Метод | Путь | Назначение |
|-------|------|-----------|
| POST | `/chat/send` | Отправить сообщение (без CSRF) |
| GET | `/chat/poll/{message_id}` | Проверить статус ответа |
| GET | `/chat/history` | Получить историю чата |

## Переменные окружения

| Переменная | По умолчанию | Описание |
|-----------|-------------|----------|
| `MEDICAL_API_URL` | `http://localhost:8999` | URL внешнего AI API |
| `MEDICAL_API_KEY` | (пусто) | Ключ аутентификации AI API |
| `MEDICAL_API_TIMEOUT` | `15` | Таймаут запросов в секундах |

## Архитектура взаимодействия

```
Пользователь → chat.js (Vanilla JS) → POST /chat/send → ChatController
                                                              ↓
                                              MedicalAssistantService → AI API (Python FastAPI)
                                                              ↓
Пользователь ← chat.js (polling) ← GET /chat/poll/{id} ← ChatController
```

1. Пользователь вводит сообщение в `textarea.js-chat-input`
2. `chat.js` отправляет POST `/chat/send` и начинает polling
3. ChatController сохраняет запрос в кэш (Cache) и сессию
4. При первом poll-запросе MedicalAssistantService отправляет запрос к внешнему AI API
5. Последующие poll-запросы проверяют статус через `/v1/ask/async/{id}`
6. При статусе `done` ChatController загружает товары из БД и возвращает ответ с product cards

## Совместимость

- Старый jQuery-код чата в `app.js` отключается через флаг `window.design17ChatV2 = true`
- Только для дизайна 17 (задаётся в `main.blade.php`)
- Внешний AI-сервис (`medical-assistant-api/`) должен быть запущен отдельно

## Fallback-механизм

При недоступности внешнего AI API чат автоматически переключается на локальный поиск товаров.

### Условия активации

Fallback срабатывает при следующих ошибках MedicalAssistantService:

| Код ошибки | Описание | Пример |
|-----------|----------|--------|
| `connection_refused` | API недоступен (нет соединения) | Сервер AI API не запущен |
| `request_failed` | Ошибка HTTP-запроса | Таймаут соединения |
| `server_error` | Ошибка на стороне AI API (5xx) | Внутренняя ошибка сервера |

### Точки активации в ChatController::pollMessage()

1. **Исключение ConnectionException** при вызове `sendQuery()` — fallback срабатывает немедленно
2. **Ошибка `sendQuery()`** (connection_refused, request_failed, server_error) — fallback вместо retry
3. **Ошибка `pollStatus()`** (connection_refused, request_failed, server_error) — fallback вместо ошибки

### Логика fallback

1. Вызывается `ProductServices::SearchProduct($query, false, 'design_17')` — тот же поиск, что и в других дизайнах
2. Результаты ограничиваются **30 товарами**
3. Результаты конвертируются в формат чата через `fetchShopProducts()`
4. Ответ возвращается с `status: "done"` (как при успешном AI-ответе), поэтому `chat.js` не требует изменений
5. На фронтенде товары отображаются с пагинацией по **6 штук** — кнопки «Show more» / «Collapse»

### Формат fallback-ответа

```json
{
    "success": true,
    "status": "done",
    "answer": "Search results for «query».",
    "products": [
        {
            "id": 123,
            "name": "Название товара",
            "slug": "product-slug",
            "image": "/images/product.jpg",
            "dosage": "10mg",
            "min_price": 9.99,
            "packs": [...]
        }
    ],
    "currency": {
        "prefix": "$",
        "code": "usd",
        "coef": 1.0
    },
    "steps": []
}
```

### Хардкод-тексты

| Ключ | Источник |
|------|----------|
| Fallback-ответ | `text.search_result_title_page` + «query». (локализован во всех 22 языках) |

### Ошибки, не вызывающие fallback

Следующие ошибки по-прежнему возвращают стандартное сообщение об ошибке:
- `unauthorized` — проблема аутентификации
- `rate_limited` — превышен лимит запросов
- `not_found` — сообщение не найдено
- `bad_request` — некорректный запрос
- `validation_error` — ошибка валидации

## Синхронизация вкладок

Чат использует модель лидер/ведомый для нескольких вкладок браузера. Коммуникация между вкладками — через `CrossTabBus` (BroadcastChannel с fallback на localStorage).

### Принцип работы

1. При открытии вкладка пытается стать лидером: `chat:claim` → ждёт 200ms
2. Если никто не ответил — вкладка становится **лидером**, шлёт `chat:leader`
3. Если другая вкладка уже лидер — становится **ведомым**

### Лидер
- Отправляет запросы к `/chat/send` и делает polling `/chat/poll`
- Принимает `chat:query` от ведомых
- После получения ответа — транслирует `chat:response` всем ведомым
- Раз в 5 секунд шлёт `chat:heartbeat`

### Ведомый
- **Не** отправляет запросы к API
- При вводе сообщения — рендерит его локально и шлёт `chat:query` лидеру
- Получает `chat:response` от лидера и рендерит ответ
- Ждёт heartbeat от лидера. Если нет 15 секунд — считает лидера мёртвым, перевыборы

### Перевыборы
- При закрытии вкладки лидера — `chat:leader-gone`
- При таймауте heartbeat — перевыборы
- Ведомые соревнуются за лидерство через `chat:claim`
