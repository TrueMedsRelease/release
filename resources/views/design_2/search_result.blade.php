@extends($design . '.layouts.main')

@section('title', $search_text)

@section('content')
<section class="page__bestsellers bestsellers">
    <aside class="categories-sidebar">
        <div class="categories-sidebar__inner">
            <div data-spollers class="categories-sidebar__spollers spollers">
                <div class="spollers__item">
                    <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                    <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                        @foreach ($bestsellers as $bestseller)
                            <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price']) }}</span></li>
                        @endforeach
                    </ul>
                </div>
                @foreach ($menu as $category)
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{ $category['name'] }}</button>
                        <ul class="spollers__body" id="this_product_category">
                            @foreach ($category['products'] as $item)
                                <li class="spollers__item-list">
                                    <a href="{{ route('home.product', $item['url']) }}">
                                        {{ $item['name'] }}
                                    </a>
                                    <span style="font-size: 12px;">{{ $Currency::convert($item['price']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
    @if (count($products) == 0)
        <section class="page__bestsellers bestsellers">
            <div class="bestsellers__container">
            <h4 class="bestsellers__title title">{{__('text.search_result_nothing_found1')}} «{{ $search_text }}» {{__('text.search_result_nothing_found2')}}</h4>
                <h2 class="bestsellers__title title">{{__('text.search_result_best_for_search')}}</h2>
                <div class="bestsellers__body">
                    @foreach ($bestsellers as $product)
                        <div class="product-card">
                            <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                                <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}">
                            </a>
                            <a href="{{ route('home.product', $product['url']) }}" class="product-card__info">
                                <h3 class="product-card__label">{{ $product['name'] }}</h3>
                                <h4 class="product-card__company">
                                    @foreach ($product['aktiv'] as $aktiv)
                                        {{ $aktiv }}
                                    @endforeach
                                </h4>
                            </a>
                            <div class="product-card__bottom">
                                <div class="product-card__left">
                                    <div class="product-card__price">{{ $Currency::convert($product['price']) }}</div>
                                </div>
                                <button type="button" class="product-card__button button button--accent" title="Add to cart" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                    <svg width="24" height="24">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                    </svg>
                                    <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="bestsellers__container">
            <h2 class="bestsellers__title title">{{__('text.search_result_title_page')}} «{{ $search_text }}»</h2>
            <div class="bestsellers__body">
                @foreach ($products as $product)
                    <div class="product-card">
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                            <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}">
                        </a>
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__info">
                            <h3 class="product-card__label">{{ $product['name'] }}</h3>
                            <h4 class="product-card__company">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv }}
                                @endforeach
                            </h4>
                        </a>
                        <div class="product-card__bottom">
                            <div class="product-card__left">
                                <div class="product-card__price">{{ $Currency::convert($product['price']) }}</div>
                            </div>
                            <button type="button" class="product-card__button button button--accent" title="Add to cart" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                <svg width="24" height="24">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
                                <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

@endsection
