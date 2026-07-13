@php $status = $status ?? 'queued'; @endphp
<div class="chat-row chat-row--agent dc17-chat-row--skeleton">
    <div class="dc17-chat-status-badge dc17-chat-status-badge--{{ $status }}">
        <span class="dc17-chat-status-badge__text">
            @if ($status === 'queued')
                {{ __('text.chat_status_queued') }}
            @elseif ($status === 'processing')
                {{ __('text.chat_status_processing') }}
            @else
                {{ __('text.chat_status_queued') }}
            @endif
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
