@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="cmcmodal hidden">
    <div class="bloktext">
       <p><b>{{random_int(2, 30)}}{{__('text.common_product1')}}</b>{{__('text.common_product2')}}</p>
    </div>
</div>
<script>
    flagp = true;
</script>
<main class="product">
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
    <div class="product__container">
        <div class="product__body">
            <aside class="product__aside">
                <div class="product__descr">
                    <div class="product__image">
                        <div class="product__image-wrapper">
                            @if ($product['image'] == 'gift-card')
                                <img loading="lazy" src="{{ asset($design . '/images/gift_card_img.svg') }}" alt="{{ $product['image'] }}">
                            @else
                                <picture>
                                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                                </picture>
                            @endif
                        </div>
                    </div>
                    <div class="product__details details-product">
                        @if ($product['image'] != 'gift-card')
                            @if (count($product['aktiv']) > 0)
                                <p class="details-product__row">{!!__('text.product_active')!!}
                                    @foreach ($product['aktiv'] as $aktiv)
                                        <a href="{{ route('home.active', $aktiv['url']) }}">
                                            {{ $aktiv['name'] }}
                                        </a>
                                    @endforeach
                                </p>
                            @endif

                            <p class="details-product__row">
                                {!!__('text.product_pack1_1')!!}
                                <b style="color: #f2d43a;">{{__('text.product_pack2_1')}}{{ random_int(10, 40) }}{{__('text.product_pack3_1')}}</b>
                            </p>
                        @endif

                        <p class="details-product__descr">{{ $product['desc'] }}</p>

                        @if (count($product['disease']) > 0)
                            <div class="details-product__block-links">
                                <h2 class="details-product__label">{{__('text.product_diseases')}}</h2>
                                <div class="details-product__links">
                                    @foreach ($product['disease'] as $disease)
                                        <a href="{{ route('home.disease', str_replace(' ', '-', $disease)) }}">
                                            {{ ucfirst($disease) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if (!empty($product['analog']))
                            @if (count($product['analog']) > 10)
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                    <input type="checkbox" class="read-more-checker" id="read-more-checker" />
                                    <div class="details-product__links limiter">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}">
                                                {{ $analog['name'] }}
                                            </a>
                                        @endforeach
                                        <div class="bottom"></div>
                                    </div>
                                    <label for="read-more-checker" class="read-more-button"></label>
                                </div>
                            @else
                                <div class="details-product__block-links">
                                    <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_analogs')!!}</h2>
                                    <div class="details-product__links">
                                        @foreach ($product['analog'] as $analog)
                                            <a href="{{ route('home.product', $analog['url']) }}">
                                                {{ $analog['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if ($product['image'] != 'gift-card')
                            @if (!empty($product['sinonim']))
                                @if (count($product['sinonim']) > 10)
                                    <div class="details-product__block-links">
                                        <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                        <input type="checkbox" class="read-more-checker" id="read-more-checker" />
                                        <div class="details-product__links limiter">
                                            @foreach ($product['sinonim'] as $sinonim)
                                                <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                    {{ $sinonim['name'] }}
                                                </a>
                                            @endforeach
                                            <div class="bottom"></div>
                                        </div>
                                        <label for="read-more-checker" class="read-more-button"></label>
                                    </div>
                                @else
                                    <div class="details-product__block-links">
                                        <h2 class="details-product__label">{{ $product['name'] }} {!!__('text.product_others')!!}</h2>
                                        <div class="details-product__links">
                                            @foreach ($product['sinonim'] as $sinonim)
                                                <a href = "{{ route('home.product', $sinonim['url']) }}">
                                                    {{ $sinonim['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
                <div class="product__offers" data-da=".product__content, 650, last">
                    <a class="product__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/01.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/01.jpg") }}" alt=""></picture>
                    </a>
                    <a class="product__item-offer">
                        <picture><source srcset="{{ asset("$design/images/offers/02.webp") }}" type="image/webp"><img loading="lazy" src="{{ asset("$design/images/offers/02.jpg") }}" alt=""></picture>
                    </a>
                </div>
            </aside>

            <div class="product__content">
                <div class="product__top-line" data-da=".product__image, 650, last">
                    <h1 class="product__title">
                        {{ $product['name'] }}
                    </h1>
                    <span class="product__group">
                        @foreach ($product['categories'] as $category)
                            <a href="{{ route('home.category', $category['url']) }}">{{ $category['name'] }}</a> <br>
                        @endforeach
                    </span>
                </div>
                <div class="product__items  item-product-info">
                @foreach ($product['packs'] as $key => $dosage)
                    @php
                        $prev_dosage = 0;
                    @endphp
                    @foreach ($dosage as $item)
                        @if ($loop->last)
                            @continue
                        @endif
                        @if ($loop->iteration != 1 && $key != $prev_dosage)
                            </tbody>
                            </table>
                        @endif
                        @if ($key != $prev_dosage)
                            <div class="product__item">
                            <h3 class="item-product-info__name">
                                @if (!in_array($product['id'], [616, 619, 620, 483, 484, 501, 615]))
                                    {{ "{$product['name']} $key" }}@if ($loop->parent->iteration == 1 && $product['rec_name'] != 'none')<span style="font-weight:lighter;">, {{__('text.product_need_more')}}</span> <span class="details-product__links"><a href="{{route('home.product', $product['rec_url'])}}">{{ $product['rec_name'] }}</a></span> @endif
                                @else
                                    {{ $product['name'] }}
                                @endif
                            </h3>
                            <table class="item-product-info__table">
                            <thead>
                            <tr class="item-product-info__row item-product-info__row--top">
                                <th class="item-product-info__package">{{__('text.product_package_title')}}</th>
                                <th class="item-product-info__per-pill">{{__('text.product_price_per_pill_title')}}</th>
                                <th class="item-product-info__price">{{__('text.product_price_title')}}</th>
                                <th class="item-product-info__btn"></th>
                            </tr>
                            </thead>
                            @php
                                $prev_dosage = $key;
                            @endphp
                        @endif
                        <tbody @if ($loop->iteration == 1 && $product['image'] != 'gift-card') class="item-product-info__row--discount" @endif>
                        <tr class="item-product-info__row">
                            <th class="item-product-info__package">
                                {{ "{$item['num']} {$product['type']}" }}
                                @if ($product['image'] != 'gift-card')
                                    @if ($item['price'] >= 300)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_express')}}</span>
                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                        <span class="item-product-info__delivery">{{__('text.cart_free_regular')}}</span>
                                    @endif
                                @endif
                            </th>
                            <th class="item-product-info__per-pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</th>
                            <th class="item-product-info__price">
                                @if ($loop->remaining != 1 && $product['image'] != 'gift-card')
                                    <span class="item-product-info__old-price">
                                        <span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }}</span>
                                        <span>-{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%</span>
                                    </span>
                                @endif
                                <span class="item-product-info__new-price">
                                    @if ($product['image'] != 'gift-card')
                                        @if (ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) == 0)
                                            {{ $Currency::convert($item['price']) }}
                                        @else
                                            {!!__('text.product_only')!!}<span>{{ $Currency::convert($item['price']) }}</span>
                                        @endif

                                    @else
                                        {{ $Currency::convert($item['price']) }}
                                    @endif
                                </span>
                            </th>
                            <th class="item-product-info__btn">
                                <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                    @csrf
                                    <button type="submit" class="item-product-info__add-to-cart">
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                        </svg>
                                        <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                                    </button>
                                </form>
                            </th>
                        </tr>
                        </div>
                    @endforeach
                    </tbody>
                    </table>
                @endforeach
                </div>
            </div>
    <div class="product__info info-product">
        @if ($product['full_desc'])
            {!! $product['full_desc'] !!}
        @endif
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
