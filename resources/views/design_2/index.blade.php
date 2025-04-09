@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)
@section('title_2', __('text.main_best_selling_title'))

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

    <section class="page__hero hero">
        <h1 class="visually-hidden">Some title for seo</h1>
        <div class="hero__container">
            <div class="hero__body">
                <div class="hero__content">
                    <div class="hero__top">
                        <div class="hero__digits">1 000 000</div>
                        <div class="hero__info">
                            <div class="hero__stars">
                                <img loading="lazy" src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18" alt="">
                            </div>
                            <p class="hero__text">{{__('text.common_customers')}}</p>
                        </div>
                    </div>
                    <div class="hero__items">
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img loading="lazy" src="{{ asset("$design/images/icons/pref-01.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_save')}}</h2>
                                <p class="item-hero__text">{{__('text.common_discount')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img loading="lazy" src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_delivery')}}</h2>
                                <p class="item-hero__text">{{__('text.common_receive')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img loading="lazy" src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_prescription')}}</h2>
                                <p class="item-hero__text">{{__('text.common_restrictions')}}</p>
                            </div>
                        </div>
                        <div class="hero__item item-hero">
                            <div class="item-hero__icon">
                                <img loading="lazy" src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                            </div>
                            <div class="item-hero__info">
                                <h2 class="item-hero__label">{{__('text.common_moneyback')}}</h2>
                                <p class="item-hero__text">{{__('text.common_refund')}}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero__image">
                    <picture><source srcset="{{ asset("$design/images/hero/01.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/hero/01.png") }}" alt=""></picture>
                </div>
                <div class="hero__bg">
                    <picture>
                        <source srcset="{{ asset("$design/images/hero/bg-xs.png") }}" media="(max-width: 479px)" type="image/webp">
                        <source srcset="{{ asset("$design/images/hero/bg.png") }}" type="image/webp">
                        <img loading="lazy" src="{{ asset("$design/images/hero/hero.jpg") }}" alt="">
                    </picture>
                </div>
            </div>
        </div>
    </section>

    <section class="page__verifed verifed">
        <div class="verifed__container">
            <h2 class="visually-hidden">Our parners</h2>
            <div class="verifed__body">
                <div class="verifed__about">
                    <img loading="lazy" src="{{ asset("$design/images/icons/verified.svg") }}" alt="">
                </div>
                <div class="verifed__items">
                    <div class="verifed__item">
                        <img loading="lazy" src="{{ asset("$design/images/partners/fda.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <picture><source srcset="{{ asset("$design/images/partners/pgeu.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/partners/pgeu.png") }}" alt=""></picture>
                    </div>
                    <div class="verifed__item">
                        <picture><source srcset="{{ asset("$design/images/partners/cipa.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/partners/cipa.png") }}" alt=""></picture>
                    </div>
                    <div class="verifed__item">
                        <img loading="lazy" src="{{ asset("$design/images/partners/mastercard.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <img loading="lazy" src="{{ asset("$design/images/partners/visa.svg") }}" alt="">
                    </div>
                    <div class="verifed__item">
                        <img loading="lazy" src="{{ asset("$design/images/partners/mcafee.svg") }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="christmas img__container" style="display: none"  onclick="location.href='{{ route('home.checkup') }}'">
        <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
    </div>

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
                            <button type="button" data-spoller class="spollers__title @if ($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
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
        <div class="bestsellers__container">
            <h2 class="bestsellers__title title">{{__('text.main_best_selling_title')}}</h2>
            <div class="bestsellers__body">
                @foreach ($bestsellers as $product)
                    @if ($loop->iteration == 2)
                        <div class="product-card product-card--offers">
                            <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                        </div>

                        <div class="product-card product-card--offers">
                            <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                        </div>
                    @endif
                    <div class="product-card">
                        @if ($product['image'] != 'gift-card' && $product['discount'] != 0)
                            <span class="card__label">-{{ $product['discount'] }}%</span>
                        @endif
                        <a href="{{ route('home.product', $product['url']) }}" class="product-card__image">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['alt'] }}">
                                </picture>
                            @endif
                            {{-- <img loading="lazy" src="{{ $product['image'] != "gift-card" ? asset("images/" . $product['image'] . ".webp") : asset($design . '/images/gift_card_img.svg') }}" width="140" height="140" alt="{{ $product['name'] }}"> --}}
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
                            <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product['url']) }}'">
                                <svg width="24" height="24">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
                                <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
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