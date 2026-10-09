{{--
    Live: "Chat" — Alpine (shop-cabinet.js cabinetChat, Chat.vue 1:1).
    Message body is HTML (textHtml from the portal, already sanitized), rendered via x-html.
    V1 + HTML styles: public/css/cabinet-v1.css (shared across designs).
    Variables: $design.
--}}
<link rel="stylesheet" href="{{ asset_ver('css/cabinet-v1.css') }}">
@php
$cabLiveChatConfig = [
    'chatUrl' => route('account.api.chat'),
    'chatSendUrl' => route('account.api.chat.send'),
    'loginUrl' => route('cabinet.login', ['step' => 'email']),
    'tYou' => __('text.cabinet_chat_you'),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabLiveChatConfig);
window.CabinetMeUrl = @json(route('account.api.me'));
</script>

<div x-data="cabinetChat()" x-cloak>
    <p class="cabp-chat__subtitle">{{ __('text.cabinet_chat_subtitle') }}</p>

    <div class="cabp-loaderror" role="alert" x-show="failed">
        <p class="cabp-loaderror__text" x-text="failed"></p>
        <button type="button" class="button cabp-action cabp-action--dark" @click="reload()">{{ __('text.cabinet_reload') }}</button>
    </div>

    <template x-if="!failed">
        <div class="cabp-chat">
            <div class="cabp-chat__messages" x-ref="thread">
                <div class="cabp-skel-list" aria-hidden="true" x-show="loading">
                    <span class="cabp-skel" style="width:70%"></span><br>
                    <span class="cabp-skel" style="width:55%;margin-left:auto;display:block"></span><br>
                    <span class="cabp-skel" style="width:65%"></span>
                </div>
                <div class="cabp-chat__more" x-show="!loading && nextOffset !== null">
                    <span class="cabp-chat__more-btn" @click="loadMore()" style="cursor:pointer">
                        <span class="cabp-spinner" aria-hidden="true" x-show="loadingMore"></span>
                        <span>{{ __('text.cabinet_chat_load_more') }}</span>
                    </span>
                </div>
                <template x-for="message in messages" :key="message.id">
                    <div class="cabp-chat-msg" :class="message.from === 'me' ? 'cabp-chat-msg--me' : 'cabp-chat-msg--support'">
                        <div class="cabp-chat-msg__meta">
                            <span class="cabp-chat-msg__author" x-text="authorOf(message)"></span>
                            <span class="cabp-chat-msg__date" x-text="message.date"></span>
                        </div>
                        <p class="cabp-chat-msg__subject" x-show="message.subject" x-text="message.subject"></p>
                        <div class="cabp-chat-msg__text" x-html="msgHtml(message)"></div>
                    </div>
                </template>
                <p class="cabp-empty" x-show="empty">{{ __('text.cabinet_chat_no_messages') }}</p>
            </div>

            <form class="cabp-chat__composer" @submit.prevent="send()">
                <div class="contact-alert" role="alert" x-show="sendError" x-text="sendError"></div>
                <div class="cabp-chat__composer-row">
                    <input type="text" class="cabp-chat__input" x-model="subject" placeholder="{{ __('text.cabinet_chat_subject') }}" aria-label="{{ __('text.cabinet_chat_subject') }}">
                </div>
                <div class="cabp-chat__composer-row">
                    <div class="cabp-chat__field">
                        <textarea class="cabp-chat__input cabp-chat__textarea" rows="3" x-model="text" placeholder="{{ __('text.cabinet_chat_message') }}" aria-label="{{ __('text.cabinet_chat_message') }}"></textarea>
                        <button type="submit" class="cabp-chat__send" :disabled="sending || !text.trim()" title="{{ __('text.cabinet_chat_send') }}">
                            <svg width="15" height="15" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M12.5 1.5L6.6 7.4M12.5 1.5L8.6 12.5l-2-5.1-5.1-2 11-3.9z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
                <p class="cabp-chat__hint">{{ __('text.cabinet_chat_rate_hint', ['max' => 3]) }}</p>
            </form>
        </div>
    </template>
</div>
