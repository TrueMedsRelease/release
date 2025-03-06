@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<script>
    flagm = true;
</script>
<main class="page">
    <div class="block_top">
        <div class="block_top_left">
            <img loading="lazy" src="{{ asset("$design/images/girl1.png") }}">
        </div>
        <div class="block_top_right">
            <div class="text_top">
                {{__('text.common_lose_weight')}}<span>{{__('text.common_guarantee')}}<span>
            </div>
            <span>
                <img loading="lazy" src="{{ asset("$design/images/line.svg") }}">
            </span>
            <div class="text_middle">
                {{__('text.common_rybel_text')}}
            </div>
            <div class="sale_button">
                <div>
                    <img loading="lazy" src="{{ asset("$design/images/icons/sale.svg") }}">
                </div>
                <div class="sale_button_text">
                    <span class="time" id="time"></span>
                    <span class="time_text">{{__('text.common_time_limit')}}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="preferences_block">
        <div class="banner_block">
            <span class="banner_top">1 000 000</span>
            <span><img loading="lazy" src="{{ asset("$design/images/stars.svg") }}" width="96"></span>
            <span class="banner_bottom">{{__('text.common_customers')}}</span>
        </div>
        <div class="preference_items">
            <div class="preference_item">
                <img loading="lazy" src="{{ asset("$design/images/icons/package.svg") }}" width="50" height="50">
                <div class="preference_block">
                    <span class="preference_top">{{__('text.common_delivery')}}</span>
                    <span class="preference_bottom">{{__('text.common_receive')}}</span>
                </div>
            </div>
            <div class="preference_item">
                <img loading="lazy" src="{{ asset("$design/images/icons/page.svg") }}" width="50" height="50">
                <div class="preference_block">
                    <span class="preference_top">{{__('text.common_prescription')}}</span>
                    <span class="preference_bottom">{{__('text.common_restrictions')}}</span>
                </div>
            </div>
            <div class="preference_item">
                <img loading="lazy" src="{{ asset("$design/images/icons/money.svg") }}" width="50" height="50">
                <div class="preference_block">
                    <span class="preference_top">{{__('text.common_moneyback')}}</span>
                    <span class="preference_bottom">{{__('text.common_refund')}}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="verified_block">
        <div class="verified_item_left">
            <img loading="lazy" src="{{ asset("$design/images/icons/img1.svg") }}">
        </div>
        <div class="verified_item_right">
            <img loading="lazy" src="{{ asset("$design/images/icons/companies.svg") }}">
        </div>
    </div>

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

    <div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
        <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
    </div>

    <div class="block_middle">
        <div class="block_middle_left">
            <div class="text_head">{{__('text.common_rybelsus')}}</div>
            <div class="text">{{__('text.common_medication')}}</div>
            <div class="text_table">
                <span><img loading="lazy" src="{{ asset("$design/images/icons/tire.svg") }}" width="24" height="24"></span>
                <span class="text_color">{{__('text.common_active_ingredient')}}</span>
                <span><img loading="lazy" src="{{ asset("$design/images/icons/molecule.svg") }}" width="50" height="50"></span>
            </div>
            <div class="text">{{__('text.common_rybelsus_desc')}}</div>
        </div>
        <div class="block_middle_right">
            <img loading="lazy" src="{{ asset("$design/images/girl2.png") }}">
        </div>
    </div>

    <div class="price_block">
        <div class="price_head">{{__('text.common_can_buy')}}</div>
        <div class="price_table">
            <div class="product_img">
                <picture>
                    <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                    <img loading="lazy" src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}">
                </picture>
            </div>
            <div class="product_price">
                <span class="product_name">{{$product['name']}}</span>
                @foreach ($product['packs'] as $key => $dosage)
                    @php
                        $prev_dosage = 0;
                    @endphp
                    @foreach ($dosage as $item)
                        @if ($loop->last)
                            @continue
                        @endif
                        @if ($loop->iteration != 1 && $key != $prev_dosage)
                                <span class="line_table"></span>
                            </div>
                        @endif
                        @if ($key != $prev_dosage)
                            <div class="prices">
                                <div class="product_dosage">{{ "{$product['name']} $key" }}</div>
                                <span class="line_table"></span>
                                <div class="table_row head">
                                    <span class="product_package">{{__('text.product_package_title')}}</span>
                                    <span class="product_per_pill">{{__('text.product_price_per_pill_title')}}</span>
                                    <span class="product_offer">{{__('text.product_price_title')}}</span>
                                    <span class="add_cart"></span>
                                </div>
                                @php
                                    $prev_dosage = $key;
                                @endphp
                        @endif
                        <span class="line_table"></span>
                        <div class="table_row">
                            <span class="product_package">
                                <div class="package_text @if ($item['num'] == 360) big_bonus @endif">
                                    {{ "{$item['num']} {$product['type']}" }}
                                    @if ($item['price'] >= 300)
                                        <span class="bonus">{{__('text.cart_free_express')}}</span>
                                    @elseif($item['price'] < 300 && $item['price'] >= 200)
                                        <span class="bonus">{{__('text.cart_free_regular')}}</span>
                                    @endif
                                </div>
                                @if ($item['num'] == 360)
                                    <div>
                                        <img loading="lazy" src="{{ asset("$design/images/icons/discount.svg") }}" width="24" height="24">
                                    </div>
                                @endif
                            </span>

                            <span class="product_per_pill">{{ $Currency::convert(round($item['price'] / $item['num'], 2), false, true) }}</span>

                            <span class="product_offer">
                                @if (ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) > 0)
                                    <span class="old_price">
                                        <span>{{ $Currency::convert($dosage['max_pill_price'] * $item['num']) }}</span>
                                        -{{ ceil(100 - ($item['price'] / ($dosage['max_pill_price'] * $item['num'])) * 100) }}%
                                    </span>{!!__('text.product_only')!!} <span>{{ $Currency::convert($item['price']) }}</span>
                                @else
                                    <span>{{ $Currency::convert($item['price']) }}</span>
                                @endif
                            </span>

                            <form method="POST" action="{{ route('cart.add', $item['id']) }}">
                                @csrf
                                <button class="add_cart add" type="submit">
                                    <img loading="lazy" src="{{ asset("$design/images/icons/cart_white.svg") }}" width="24" height="24">
                                    <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>


    <section class="subscribe__container">
        <div class="block_subscribe">
            <div class="left_block">
                <div class="subscribe_img">
                    <img loading="lazy" src="{{ asset("$design/images/icons/subscribe.svg") }}">
                </div>
                <div class="text_subscribe">
                    <span class="top_text">{{__('text.common_subscribe')}}</span>
                    <span class="bottom_text">{{__('text.common_spec_offer')}}</span>
                </div>
            </div>
            <div class="right_block">
                <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                <div class="button_sub">
                    <img loading="lazy" src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                    <span class="button_text">{{__('text.common_subscribe')}}</span>
                </div>
            </div>
        </div>
    </section>

   <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#usps" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ems" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dhl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ups" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#fedex" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#tnt" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#postnl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#deutsche_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dpd" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#gls" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" width="100%" href="/pub_images/shipping/sprite.svg#australia_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#colissimo" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#correos" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
            </ul>
        </div>
    </section>

    <div class="page_testimonials">
        <div class="testimonials_block">
            <div class="testimonials_head">
                {{__('text.common_testimonials_main_menu_item')}}
            </div>
            <div class="testimonials">
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/girl3.png") }}">
                    </div>
                    <div class="quotes">
                        <img loading="lazy" src="{{ asset("$design/images/icons/quotes.svg") }}">
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_1')}}
                    </div>
                    <div class="testimonial_grade">
                        <div class="testimonial_stars">
                            <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                        </div>
                        <div class="testimonial_name">
                            — {{__('text.common_term_name_1')}}
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/girl4.png") }}">
                    </div>
                    <div class="quotes">
                        <img loading="lazy" src="{{ asset("$design/images/icons/quotes.svg") }}">
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_4')}}
                    </div>
                    <div class="testimonial_grade">
                        <div class="testimonial_stars">
                            <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                        </div>
                        <div class="testimonial_name">
                            — {{__('text.common_term_name_2')}}
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/man.png") }}">
                    </div>
                    <div class="quotes">
                        <img loading="lazy" src="{{ asset("$design/images/icons/quotes.svg") }}">
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_9')}}
                    </div>
                    <div class="testimonial_grade">
                        <div class="testimonial_stars">
                            <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                        </div>
                        <div class="testimonial_name">
                            — {{__('text.common_term_name_3')}}
                        </div>
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img loading="lazy" src="{{ asset("$design/images/girl5.png") }}">
                    </div>
                    <div class="quotes">
                        <img loading="lazy" src="{{ asset("$design/images/icons/quotes.svg") }}">
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_5')}}
                    </div>
                    <div class="testimonial_grade">
                        <div class="testimonial_stars">
                            <img loading="lazy" src="{{ asset("$design/images/stars.svg") }}">
                        </div>
                        <div class="testimonial_name">
                            — {{__('text.common_term_name_4')}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
