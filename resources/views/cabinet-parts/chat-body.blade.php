{{--
    Shared: тело страницы "Chat" (email-мессенджер с поддержкой).
    Структура 1:1 с референсом (customerportal Chat.vue): подзаголовок,
    карточка диалога, кнопка старых сообщений, пузыри (я справа / поддержка
    слева), композер (тема + текст + круглая кнопка), лимит отправки.
    Статика для верстки. Стили — в per-design CSS.
    Переменные: $cabp_chat.
--}}
<p class="cabp-chat__subtitle">{{ __('text.cabinet_chat_subtitle') }}</p>

<div class="cabp-chat">
    <div class="cabp-chat__messages">
        <div class="cabp-chat__more">
            <span class="cabp-chat__more-btn">{{ __('text.cabinet_chat_load_more') }}</span>
        </div>
        @foreach ($cabp_chat['messages'] as $message)
            <div class="cabp-chat-msg @if ($message['from'] === 'me') cabp-chat-msg--me @else cabp-chat-msg--support @endif">
                <div class="cabp-chat-msg__meta">
                    <span class="cabp-chat-msg__author">@if ($message['from'] === 'me'){{ __('text.cabinet_chat_you') }}@else{{ $message['author'] }}@endif</span>
                    <span class="cabp-chat-msg__date">{{ $message['date'] }}</span>
                </div>
                @if (!empty($message['subject']))
                    <p class="cabp-chat-msg__subject">{{ $message['subject'] }}</p>
                @endif
                <p class="cabp-chat-msg__text">{{ $message['text'] }}</p>
            </div>
        @endforeach
    </div>

    <form class="cabp-chat__composer" onsubmit="return false;">
        <div class="cabp-chat__composer-row">
            <input type="text" class="cabp-chat__input" value="Change address" placeholder="{{ __('text.cabinet_chat_subject') }}" aria-label="{{ __('text.cabinet_chat_subject') }}">
        </div>
        <div class="cabp-chat__composer-row">
            <div class="cabp-chat__field">
                <textarea class="cabp-chat__input cabp-chat__textarea" rows="3" placeholder="{{ __('text.cabinet_chat_message') }}" aria-label="{{ __('text.cabinet_chat_message') }}"></textarea>
                <span class="cabp-chat__send" title="{{ __('text.cabinet_chat_send') }}">
                    <svg width="15" height="15" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <path d="M12.5 1.5L6.6 7.4M12.5 1.5L8.6 12.5l-2-5.1-5.1-2 11-3.9z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
        </div>
        <p class="cabp-chat__hint">{{ __('text.cabinet_chat_rate_hint', ['max' => $cabp_chat['send_limit']]) }}</p>
    </form>
</div>
