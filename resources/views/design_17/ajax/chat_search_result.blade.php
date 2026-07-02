@php
    $productsCount = count($products);
@endphp

<div class="chat-row chat-row--agent js-chat-search-answer">
    <div class="chat-message">
        <div class="chat-message__content content">
            <div class="chat-message__bubble chat-message__bubble--agent">
                @if ($productsCount > 0)
                    {{ __('text.search_result_title_page') }} «{{ $search_text }}».
                @else
                    {{ __('text.common_product_text') }} «{{ $search_text }}» {{ __('text.search_not_found') }}.
                    <br>
                    {{ __('text.search_not_carry') }} «{{ $search_text }}» {{ __('text.search_this_time') }}
                @endif
            </div>
        </div>
    </div>
</div>

<div class="chat-row chat-row--page chat-row--results">
    <div class="chat-message">
        <div class="chat-message__content content"></div>
        <div class="chat-message__page chat-search-results">
            @if ($productsCount > 0)
                <div class="product-cards chat-search-results__cards">
                    <div class="cards">
                        @foreach ($products as $product)
                            @include($design . '.ajax.search_product_card', ['product' => $product])
                        @endforeach
                    </div>
                </div>
            @else
                <div class="chat-search-results__empty-actions">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        @php
                            $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                        @endphp
                        <a class="button" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">
                            {{ __('text.common_contact_us_main_menu_item') }}
                        </a>
                    @else
                        <a class="button" href="{{ route('home.contact_us', '') }}">
                            {{ __('text.common_contact_us_main_menu_item') }}
                        </a>
                    @endif
                </div>

                @if (!empty($bestsellers))
                    <h2 class="h2 chat-search-results__title">{{ __('text.search_result_best_for_search') }}</h2>
                    <div class="product-cards chat-search-results__cards">
                        <div class="cards">
                            @foreach (array_slice($bestsellers, 0, 6) as $product)
                                @include($design . '.ajax.search_product_card', ['product' => $product])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
