
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<main class="default">
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

    <a class="christmas" style="display: none"  href="{{ route('home.checkup') }}">
        <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
    </a>

    <div class="default__container">
        <div class="default__body">
            <div class="default__content">
                <h1 class="default__title title">{{__('text.about_us_title')}}</h1>
                <div class="default__text">
                    {!!__('text.about_us_text')!!}
                <h2 style="font-weight: bold;">{{__('text.about_us_title1')}}</h2>
                <p>{{__('text.about_us_text1')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title2')}}</h2>
                <ul>{{__('text.about_us_text2_1')}}</ul>
                    <li>{{__('text.about_us_text2_2')}}</li>
                    <li>{{__('text.about_us_text2_3')}}</li>
                    <p></p>
                <ul>{{__('text.about_us_text2_4')}}</ul>
                    <li>{{__('text.about_us_text2_5')}}</li>
                    <li>{{__('text.about_us_text2_6')}}</li>
                    <p></p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title3')}}</h2>
                <p>{{__('text.about_us_text3_1')}}</p>
                <p>{{__('text.about_us_text3_2')}}</p>
                <ul>{{__('text.about_us_text3_3')}}</ul>
                    <li>{{__('text.about_us_text3_4')}}</li>
                    <li>{{__('text.about_us_text3_5')}}</li>
                    <p></p>
                <p>{{__('text.about_us_text3_6')}}</p>
                <h2 style="font-weight: bold;">{{__('text.about_us_title4')}}</h2>
                <p>{{__('text.about_us_text4')}}</p>
                </div>
            </div>
            <aside class="default__aside">
                <div class="default__offers">
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a href="#" class="default__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
                </div>
            </aside>
        </div>
    </div>
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