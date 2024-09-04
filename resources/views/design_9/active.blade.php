@extends($design . '.layouts.main')

@section('title', __('text.aktiv_aktiv_result_title') . ' ' . $active)

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
        <div class="bestsellers__container">
            <div class="bonus_block">
                <div class="bonus1">
                    <img src="{{ asset("$design/images/bonus1_1.png") }}">
                </div>
                <div class="bonus2">
                    <img src="{{ asset("$design/images/bonus2_2.png") }}">
                </div>
            </div>
            <h2 class="bestsellers__title title">{{__('text.aktiv_aktiv_result_title')}} {{ $active }}</h2>
            <div class="bestsellers__body">
                <div class="product_list">
                    @foreach ($products as $product)
                        <div class="product_info">
                            <div class="product_info_top">
                                <a href="{{ route('home.product', $product['url']) }}">
                                    <div class="product_img">
                                        <img src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}">
                                    </div>
                                </a>
                                <a href="{{ route('home.product', $product['url']) }}" class="product_center">
                                    <div class="product_main">
                                        <div class="product_text">
                                            <span class="product_name">{{ $product['name'] }}</span>
                                            <span class="product_active">
                                                @foreach ($product['aktiv'] as $aktiv)
                                                    {{ $aktiv }}
                                                @endforeach
                                            </span>
                                        </div>
                                        <div class="product_desc top">{{ $product['desc'] }}</div>
                                    </div>
                                </a>
                                <div class="product_right_block">
                                    <div class="product_price">{{ $Currency::convert($product['price']) }}</div>
                                    <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                        </svg>
                                        <span>{{__('text.common_buy_button')}}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="product_info_bottom">
                                <div class="product_desc bottom">{{ $product['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</main>

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

<section class="ship-index">
    <div class="ship-index__container">
        <ul class="ship-index__list">
            <li class="ship-index__item">
                <img src="/images/shipping/usps.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ems.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dhl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/ups.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/fedex.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/tnt.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/postnl.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/deutsche_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/dpd.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/gls.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/australia_post.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/colissimo.svg" alt="">
            </li>
            <li class="ship-index__item">
                <img src="/images/shipping/correos.svg" alt="">
            </li>
        </ul>
    </div>
</section>

<div class="reviews_block">
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_1')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_1')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_7')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_7')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_13')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_13')}}</div>
    </div>
    <div class="review">
        <div class="review_top">
            <div class="person_name">{!!__('text.testimonials_author_t_17')!!}</div>
            <div class="stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" height="20" alt="">
            </div>
        </div>
        <div class="review_text">{{__('text.testimonials_t_17')}}</div>
    </div>
</div>

@endsection
