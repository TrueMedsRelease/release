@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<main class="page">
<section class="pay-index">
    <div class="pay-index__container">
        <ul class="pay-index__list">
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#visa">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#mastercard">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#maestro">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#discover">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amex">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#jsb">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#unionpay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#dinners-club">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#apple-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#google-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amazon-pay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#stripe">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#paypal">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#sepa">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#cashapp">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#adyen">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#skrill">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#worldpay">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#payline">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#bitcoin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#binance-coin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#ethereum">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#litecoin">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#tron">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(erc20)">
                </svg>
            </li>
            <li class="pay-index__item">
                <svg>
                    <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(trc20)">
                </svg>
            </li>
        </ul>
    </div>
</section>
<section class="page__bestsellers bestsellers">
    <aside class="categories-sidebar">
        <div class="categories-sidebar__inner">
            <div data-spollers class="categories-sidebar__spollers spollers">
                <div class="spollers__item">
                    <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.main_best_selling_title')}}</button>
                    <ul class="spollers__body main_bestsellers" id="main_bestsellers_body">
                        @foreach ($bestsellers as $bestseller)
                            <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                        @endforeach
                    </ul>
                </div>
                @foreach ($menu as $category)
                    <div class="spollers__item">
                        <button type="button" data-spoller class="spollers__title @if($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                        <ul class="spollers__body" id="this_product_category">
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
    @foreach ($products as $category)
        <div class="bestsellers__container">
            <h2 class="bestsellers__title title">{{ $category['name'] }}</h2>
            <div class="bestsellers__body">
                @foreach ($category['products'] as $product)
                    <div class="product-card">
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                </picture>
                            @endif
                            {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['name'] }}"> --}}
                        </a>
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__info">
                            <h3 class="product-card__label">{{ $product['name'] }}</h3>
                            <h4 class="product-card__company">
                                @foreach ($product['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </h4>
                        </a>
                        <div class="product-card__bottom">
                            <div class="product-card__left">
                                <div class="product-card__price">{{ $Currency::convert($product['price'], false, true) }}</div>
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
    @endforeach
</section>
</main>
@endsection

@section('reviews')

<section class="reviews">
    <div class="reviews__container">
        <div class="reviews__body">
            <div class="reviews__slider">
                <div class="reviews__swiper">
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_1')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_1')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_2')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_2')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_3')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_3')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_4')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_4')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_5')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_5')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_6')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_6')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_7')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_7')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_8')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_8')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_9')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_9')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_10')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_10')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_11')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_11')}}</div>
                    </div>
                    <div class="reviews__slide">
                        <div class="reviews__top">
                            <div class="reviews__name">{!!__('text.testimonials_author_t_12')!!}</div>
                            <div class="reviews__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="108" height="20" alt="">
                            </div>
                        </div>
                        <div class="reviews__text">{{__('text.testimonials_t_12')}}</div>
                    </div>
                </div>
            </div>
            <div class="reviews__controls">
                <button type="button" class="reviews__arrow reviews__arrow--prev">
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                    </svg>
                    <span>{{__('text.testimonials_prev')}}</span>
                </button>
                <button type="button" class="reviews__arrow reviews__arrow--next">
                    <span>{{__('text.testimonials_next')}}</span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

@endsection