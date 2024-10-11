@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<main class="page">
    <section class="page__products home-grid-items">
        <aside class="categories-sidebar">
            <div class="categories-sidebar__inner">
                <div data-spollers class="categories-sidebar__spollers spollers">
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
                        <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                            @foreach ($bestsellers as $bestseller)
                                <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach ($menu as $category)
                        <div class="spollers__item">
                            <button type="button" data-spoller class="spollers__title">{{ $category['name'] }}</button>
                            <ul class="spollers__body">
                                @foreach ($category['products'] as $item)
                                    <li class="spollers__item-list">
                                        <a href="{{ route('home.product', $item['url']) }}">
                                            {{ $item['name'] }}
                                        </a>
                                        <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </aside>
        <div class="home-grid-items__container">
            <h2 class="home-grid-items__title title">{{__('text.aktiv_aktiv_result_title')}} {{ ucwords(str_replace('-', ' ', $active)) }}</h2>
            <div class="home-grid-items__body">
                @foreach ($products as $product)
                    <div class="product-card">
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                            @if ($product['image'] == 'gift-card')
                                <img src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                </picture>
                            @endif
                            {{-- <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}"> --}}
                        </a>
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__info">
                            <h3 class="product-card__label">{{ $product['name'] }}</h3>
                            <h4 class="product-card__company">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </h4>
                            <h4 class="product-card__descr">{{ $product['desc'] }}</h4>
                        </a>
                        <div class="product-card__bottom">
                            <button type="button" class="product-card__button button del_text" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                <svg width="30" height="30">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-2") }}"></use>
                                </svg>
                                {{-- <span>{if !in_array($data.language.code, ['de', 'it', 'nl', 'hu'])}{#add_to_cart_text_d2#}{/if}</span> --}}
                            </button>
                            <div class="product-card__left">
                                <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="offers">
                    <div class="offers__item">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/01.png") }}" alt=""></picture>
                    </div>
                    <div class="offers__item">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img src="{{ asset("$design/images/offers/02.png") }}" alt=""></picture>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="subscribe__container">
        <div class="subscribe_body">
            <div class="left_block">
                <div class="subscribe_img">
                    <img src="{{ asset("$design/images/icons/subscribe.svg") }}">
                </div>
                <div class="text_subscribe">
                    <span class="top_text">{{__('text.common_subscribe')}}</span>
                    <span class="bottom_text">{{__('text.common_spec_offer')}}</span>
                </div>
            </div>
            <div class="right_block">
                <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                <div class="button_sub">
                    <img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                    <span class="button_text">{{__('text.common_subscribe')}}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="page__testimonials testimonials">
        <div class="testimonials__container">
            <h2 class="visually-hidden">Title for seo</h2>
            <div class="testimonials__body">
                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_1')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_1')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_2')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_2')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_3')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_13')}}</p>
                </div>

                <div class="testimonials__item">
                    <div class="testimonials__row">
                        <div class="testimonials__name">{!!__('text.testimonials_author_t_4')!!}</div>
                        <div class="testimonials__stars">
                            <svg width="90" height="14">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                            </svg>
                        </div>
                    </div>
                    <p class="testimonials__text">{{__('text.testimonials_t_4')}}</p>
                </div>
            </div>
            <a href="{{ route('home.testimonials') }}" class="testimonials__button button">
                <span>{{__('text.common_testimonials_main_menu_item')}}</span>
                <svg width="16" height="16">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-right") }}"></use>
                </svg>
            </a>
        </div>
    </section>
</main>

@endsection
