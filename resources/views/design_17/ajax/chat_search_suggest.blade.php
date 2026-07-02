<div class="chat-row chat-row--agent js-chat-live-suggest">
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble chat-message__bubble--agent chat-message__bubble--suggest">
                @if (!empty($items))
                    <div class="chat-suggest">
                        <div class="chat-suggest__title">
                            I found matching options for «{{ $search_text }}»:
                        </div>
                        <div class="chat-suggest__items">
                            @foreach ($items as $item)
                                <a class="chat-suggest__item js-chat-suggest-item"
                                   href="{{ url($item['url']) }}"
                                   data-title="{{ $item['title'] }}">
                                    {{ $item['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="chat-suggest chat-suggest--empty">
                        <div class="chat-suggest__title">
                            I am checking «{{ $search_text }}», but there are no exact suggestions yet.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
