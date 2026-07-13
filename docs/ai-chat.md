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
