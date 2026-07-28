@php
$type = $type ?? 'agent';
$hasProducts = !empty($products);

if (!isset($status)) {
    $status = match ($type) {
        'skeleton' => 'queued',
        'error' => 'error',
        default => null,
    };
}

$statusMessage = null;
$allowedStatuses = ['queued', 'processing', 'done', 'error'];

if ($status && in_array($status, $allowedStatuses, true)) {
    $translator = app('translator');
    $locale = app()->getLocale();
    $variantsKey = 'text.chat_status_' . $status . '_variants';
    $fallbackKey = 'text.chat_status_' . $status;

    if ($translator->hasForLocale($variantsKey, $locale)) {
        $variants = $translator->get($variantsKey, [], $locale);
    } else {
        $variants = [$translator->get($fallbackKey, [], $locale)];
    }

    $variants = is_array($variants)
        ? array_values(array_filter($variants, static fn ($value) => is_string($value) && trim($value) !== ''))
        : [];

    if (!$variants) {
        $variants = [$translator->get($fallbackKey, [], $locale)];
    }

    $statusMessage = $variants[array_rand($variants)];
}
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
            {{ $statusMessage ?? __('text.chat_status_queued') }}
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
    @if ($status === 'done')
        <div class="dc17-chat-status-badge dc17-chat-status-badge--done">
            <span class="dc17-chat-status-badge__text">
                {{ $statusMessage ?? __('text.chat_status_done') }}
            </span>
        </div>
    @endif

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
    <div class="dc17-chat-status-badge dc17-chat-status-badge--error">
        <span class="dc17-chat-status-badge__text">
            {{ $statusMessage ?? __('text.chat_status_error') }}
        </span>
    </div>

    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble dc17-chat-message__bubble--error">
                {{ $message ?? __('text.chat_error_unknown') }}
            </div>
        </div>
    </div>
</div>
@endif