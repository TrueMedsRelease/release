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
            <div class="text_top">
                {{__('text.common_guarant')}}<span>{{__('text.common_erec_dys')}}<span>
            </div>
            <div class="text_middle">
                {{__('text.common_pills_text_main')}}
            </div>
        </div>
    </div>

    <div class="sale_block">
        <div class="sale_button">
            <div>
                <img src="{{ asset("$design/images/icons/sale.svg") }}">
            </div>
            <div class="sale_button_text">
                <span class="time" id="time"></span>
                <span class="time_text">{{__('text.common_time_limit')}}</span>
            </div>
        </div>
    </div>
    <div class="reclame">
        <div class="preferences_block">
            <div class="banner_block">
                <span class="banner_top">1 000 000</span>
                <span><img src="{{ asset("$design/images/stars.svg") }}" width="96"></span>
                <span class="banner_bottom">{{__('text.common_customers')}}</span>
            </div>
            <div class="preference_items">
                <div class="preference_item">
                    <img src="{{ asset("$design/images/icons/package.svg") }}" width="50" height="50">
                    <div class="preference_block">
                        <span class="preference_top">{{__('text.common_delivery')}}</span>
                        <span class="preference_bottom">{{__('text.common_receive')}}</span>
                    </div>
                </div>
                <div class="preference_item">
                    <img src="{{ asset("$design/images/icons/page.svg") }}" width="50" height="50">
                    <div class="preference_block">
                        <span class="preference_top">{{__('text.common_prescription')}}</span>
                        <span class="preference_bottom">{{__('text.common_restrictions')}}</span>
                    </div>
                </div>
                <div class="preference_item">
                    <img src="{{ asset("$design/images/icons/money.svg") }}" width="50" height="50">
                    <div class="preference_block">
                        <span class="preference_top">{{__('text.common_moneyback')}}</span>
                        <span class="preference_bottom">{{__('text.common_refund')}}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="verified_block">
            <div class="verified_item_left">
                <img src="{{ asset("$design/images/icons/img1.svg") }}">
            </div>
            <div class="verified_item_right">
                <img src="{{ asset("$design/images/icons/companies.svg") }}">
            </div>
        </div>

        <section class="pay-index">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/visa.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/mastercard.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/maestro.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/discover.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/amex.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/jsb.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/unionpay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/dinners-club.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/apple-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/google-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/amazon-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/stripe.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/paypal.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/sepa.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/cashapp.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/adyen.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/skrill.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/worldpay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/payline.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/bitcoin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/binance-coin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/ethereum.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/litecoin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/tron.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/usdt(erc20).svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/pub_images/pay_icons/usdt(trc20).svg" alt="">
                    </li>
                </ul>
            </div>
        </section>
    </div>

    <div class="block_middle">
        <div class="block_middle_left">
            <div class="text_head">{{__('text.common_vi_ci_le')}}</div>
            <div class="text">{{__('text.common_pills_desc')}}</div>
            <div class="text">
                <div class="top_list">
                    {{__('text.common_top_list_main')}}
                </div>
                <div class="list">
                    <img src="{{ asset("$design/images/icons/tire.svg") }}">
                    <span>{{__('text.common_sildenafil')}}</span>
                </div>
                <div class="list">
                    <img src="{{ asset("$design/images/icons/tire.svg") }}">
                    <span>{{__('text.common_tadalafil')}}</span>
                </div>
                <div class="list">
                    <img src="{{ asset("$design/images/icons/tire.svg") }}">
                    <span>{{__('text.common_vardenafil')}}</span>
                </div>
            </div>
        </div>
        <div class="block_middle_right">
            <img src="{{ asset("$design/images/pills.svg") }}">
        </div>
    </div>

    <div class="price_block">
        <div class="price_head">{{__('text.common_can_buy')}}</div>

        <div class="products">
            @foreach ($products as $key => $product)
                <div class="product_new @if ($loop->iteration == 1) active @endif" prod_name="{{$key}}">
                    <div class="product_img_top">
                        <picture>
                            <source srcset="{{ route('home.set_images', $product['image']) }}" type="image/webp">
                            <img src="{{ route('home.set_images', $product['image']) }}" alt="{{ $product['image'] }}" style="width: 100%">
                        </picture>
                    </div>
                    <div class="product_text">
                        <div class="product_name_top">{{$product['name']}}</div>
                        <div class="product_active">{!!__('text.product_active')!!}
                            @foreach ($product['aktiv'] as $aktiv)
                                {{ $aktiv['name'] }}
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="price_table">
            <div class="product_doses">
                @foreach ($products as $key => $product)
                    <div class="{{$key}} head_prod @if ($loop->iteration != 1) hide @endif">
                        <div class="product_name">{{$product['name']}}</div>
                        <div class="doses">
                            @foreach ($product['packs'] as $key => $dosage)
                                <div class="dose @if ($loop->iteration == 1) active @endif" dosage="@if ($key != '2.5mg'){{$key}}@else{{str_replace('.', '-', $key)}}@endif" dose_prod="{{$key}}">{{$key}}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="product_price">
                <div class="prices">
                    @foreach ($products as $key => $product)
                        <div class="{{$key}} prices_prod @if ($loop->iteration != 1) hide @endif">
                            @foreach ($product['packs'] as $key => $dosage)
                                <div class="@if ($key != '2.5mg'){{$key}}@else{{str_replace('.', '-', $key)}}@endif dose_prod @if ($loop->iteration != 1) hide @endif">
                                    <div class="table_row head">
                                        <span class="product_package">{{__('text.product_package_title')}}</span>
                                        <span class="product_per_pill">{{__('text.product_price_per_pill_title')}}</span>
                                        <span class="product_offer">{{__('text.product_price_title')}}</span>
                                        <span class="add_cart"></span>
                                    </div>
                                    @foreach ($dosage as $item)
                                        @if ($loop->last)
                                            @continue
                                        @endif
                                        <span class="line_table"></span>
                                        <div class="table_row">
                                            <span class="product_package" >
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
                                                        <img src="{{ asset("$design/images/icons/discount.svg") }}" width="24" height="24">
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
                                                    <img src="{{ asset("$design/images/icons/cart.svg") }}" width="24" height="24">
                                                    <span>{{__('text.product_add_to_cart_text_d2')}}</span>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <section class="subscribe_container">
        <div class="block_subscribe">
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

    <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/usps.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/ems.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/dhl.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/ups.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/fedex.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/tnt.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/postnl.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/deutsche_post.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/dpd.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/gls.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/australia_post.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/colissimo.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/pub_images/shipping/correos.svg" alt="">
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
                        <img src="{{ asset("$design/images/man1.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_18')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_1')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man2.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_16')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_4')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man3.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {{__('text.common_term_name_3')}}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_9')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
                <div class="testimonial">
                    <div class="testimonial_img">
                        <img src="{{ asset("$design/images/man4.png") }}">
                    </div>
                    <div class="testimonial_name">
                        {!!__('text.testimonials_author_t_12')!!}
                    </div>
                    <div class="testimonial_text">
                        {{__('text.testimonials_t_5')}}
                    </div>
                    <div class="testimonial_stars">
                        <img src="{{ asset("$design/images/stars.svg") }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
