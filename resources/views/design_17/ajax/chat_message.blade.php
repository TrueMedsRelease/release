@php
$type = $type ?? 'agent';
$status = $status ?? null;
$hasProducts = !empty($products);
@endphp

@if ($type === 'user')
<div class="chat-row chat-row--user">
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble">
                {{ $message ?? '' }}
            </div>
        </div>
    </div>
</div>
@elseif ($type === 'skeleton')
<div class="chat-row chat-row--agent dc17-chat-row--skeleton">
    <div class="dc17-chat-status-badge dc17-chat-status-badge--{{ $status ?? 'queued' }}">
        <span class="dc17-chat-status-badge__text">
            {{ $status === 'processing' ? __('text.chat_status_processing') : __('text.chat_status_queued') }}
        </span>
    </div>
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__pending">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="48" height="48" rx="24" fill="white"/>
                    <circle cx="17" cy="24" r="2" fill="#13163F"/>
                    <circle cx="24" cy="24" r="2" fill="#13163F"/>
                    <circle cx="31" cy="24" r="2" fill="#13163F"/>
                </svg>
            </div>
            <div class="dc17-chat-skeleton-lines dc17-chat-skeleton-lines--fallback">
                <div class="dc17-chat-skeleton-line"></div>
                <div class="dc17-chat-skeleton-line dc17-chat-skeleton-line--short"></div>
                <div class="dc17-chat-skeleton-line dc17-chat-skeleton-line--medium"></div>
            </div>
        </div>
    </div>
</div>
@elseif ($type === 'agent')
<div class="chat-row chat-row--agent{{ $hasProducts ? ' chat-row--product' : '' }} chat-message--appear">
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble{{ $hasProducts ? ' chat-message__bubble--agent' : '' }}">
                <div class="dc17-chat-answer-text">{!! nl2br(e($message ?? '')) !!}</div>
            </div>
        </div>
        @if ($hasProducts)
            <div class="chat-message__page">
                @include($design . '.ajax.chat_product_card', ['products' => $products])
            </div>
        @endif
    </div>
</div>
@elseif ($type === 'error')
<div class="chat-row chat-row--agent dc17-chat-row--error">
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble dc17-chat-message__bubble--error">
                {{ $message ?? __('text.chat_error_unknown') }}
            </div>
        </div>
    </div>
</div>
@endif
