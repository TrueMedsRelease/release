@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="thread-chat js-chat-search-thread">
    <div class="thread-chat__container">
        <div class="thread-chat__messages js-chat-search-messages">
            <div class="chat-row chat-row--user">
                <div class="chat-message">
                    <div class="chat-message__content content">
                        <div class="chat-message__bubble">{{ $search_text }}</div>
                    </div>
                </div>
            </div>

            @include($design . '.ajax.chat_search_result', [
                'design' => $design,
                'search_text' => $search_text,
                'products' => $products,
                'bestsellers' => $bestsellers,
                'Currency' => $Currency,
            ])
        </div>
    </div>
</div>
@endsection
