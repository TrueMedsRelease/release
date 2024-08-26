@extends($design . '.layouts.main')

@section('title', 'TrueMeds')

@section('content')
<main class="page">
    <section class="page__bestsellers bestsellers">
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">${{ $bestseller['price'] }}</span></li>
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
                                        <span style="font-size: 12px;">${{ $item['price'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
        <div class="bestsellers__container">
            <h2 class="bestsellers__title title">{{__('text.aktiv_aktiv_result_title')}} {{ $active }}</h2>
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
                                <div class="product-card__price">${{ $product['price'] }}</div>
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
                <div class="product-card product-card--offers">
                    <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                </div>

                <div class="product-card product-card--offers">
                    <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection
