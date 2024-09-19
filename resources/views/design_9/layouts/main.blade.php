<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Defult')</title>
    <meta name="description" content="Verified Pharmacy Store">
    <meta name="keywords" content="key, words">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4494DE"/>
	<meta name="format-detection" content="telephone=no">

    <link rel="alternate" href="{{ config('app.url') }}/lang=arb" hreflang="ar" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=cs" hreflang="cs" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=da" hreflang="da" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=de" hreflang="de" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=en" hreflang="en" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=es" hreflang="es" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fi" hreflang="fi" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fr" hreflang="fr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=gr" hreflang="gr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hans" hreflang="zh-Hans" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hant" hreflang="zh-Hant" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hu" hreflang="hu" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=it" hreflang="it" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ja" hreflang="ja" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ms" hreflang="ms" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=nl" hreflang="nl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=no" hreflang="no" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pl" hreflang="pl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pt" hreflang="pt" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ro" hreflang="ro" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sk" hreflang="sk" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sv" hreflang="sv" />

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
</head>
<body>
    <script>
        const design = 9;
    </script>

<div class="wrapper">
    <div class="popup_gray" style="display: none">
        <div class="popup_call">
            <div class="button_close">
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
            </div>
            <div class="popup_bottom">
                <div class="popup_text">{{__('text.common_callback')}}</div>
                <div class="phone">
                    <div class="enter-info__country phone_code">
                        <select name="phone_code" class="form" data-scroll>
                            @foreach ($phone_codes as $item)
                                <option id=""
                                @if (empty(session('form')))
                                        @selected($item['iso'] == session('location.country', ''))
                                @else
                                    @selected($item['iso'] == session('form.phone_code', ''))
                                @endif
                                    data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                    value="+{{ $item['phonecode'] }}">
                                    +{{ $item['phonecode'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enter-info__input enter-info__input--country">
                        <input required autocomplete="off" type="number" id="phone" name="phone" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
                    </div>
                </div>
                <div class="button_request_call">{{__('text.common_callback')}}</div>
            </div>
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.phone_request_mes_text')}}</p>
            </div>
        </div>
    </div>
	<header class="header">
		<div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none">
                        <a class="request_call">{{ __('text.common_callback') }}</a>
                        <div class="request_text">{{__('text.common_call_us_top')}}</div>
                    </div>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a>
                </div>
            </div>
		</div>
        <div class="container header__container">
            <div class="header__top">
                <div class="header__inner">
                    <a href="{{ route('home.index') }}" class="header__logo logo">
                        <img class="logo__icon" src="{{ asset("$design/images/logo.svg") }}" alt="">
                    </a>
                    <form class="header__search" data-da=".header__top, 1024, last" action="{{ route('search.search_product') }}" method = "POST" data-dev>
                        @csrf
                        <div class="search search-bar">
                            <div class="search__input">
                                <input id="autocomplete" autocomplete="off" type="text" name="search_text" placeholder="{{__('text.common_search')}}">
                            </div>
                            <button class="search__icon" type="submit">
                                <span class="visually-hidden">search</span>
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}"></use>
                                </svg>
                            </button>
                        </div>
                    </form>
                    <div class="header__actions actions">
                        <div class="actions__item">
                            <div class="actions__icon">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-global") }}"></use>
                                </svg>
                            </div>
                            <div class="actions__select">
                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Language::GetAllLanuages() as $language)
                                        <option value="/lang={{$language['code']}}" @if (App::currentLocale() == $language['code']) selected @endif>{{$language['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="actions__item">
                            <div class="actions__icon">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-wallet") }}"></use>
                                </svg>
                            </div>
                            <div class="actions__select">
                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="actions__item actions__item--order" data-da=".fixed-bar, 600, last">
                            <a href='{{ route('home.login') }}' target="_blank">
                                <div class="actions__icon">
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-profile") }}"></use>
                                    </svg>
                                </div>
                                <div class="actions__label">{{__('text.common_profile')}}</div>
                            </a>
                        </div>
                    </div>
                    @php
                        $cart_count = 0;
                        $cart_total = 0;
                        if(!empty(session('cart')))
                        {
                            foreach (session('cart') as $value) {
                                $cart_count += $value['q'];
                                $cart_total += $value['price'] * $value['q'];
                            }
                        }
                    @endphp
                    <a href="{{ route('cart.index') }}" type="button" class="header__cart cart" data-da=".fixed-bar, 600, 2">
                        @if ($cart_count != 0)
                            <span class="cart__icon">
                                <svg width="24" height="24">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
                                <span class="cart__quantity">{{$cart_count}}</span>
                            </span>
                            <span class="cart__text">{{__('text.common_cart_text_d2')}}</span>
                            <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                        @else
                            <span class="cart__icon">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
                            </span>
                            <span class="cart__text">{{__('text.common_cart_text_d2')}}</span>
                            <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                        @endif
                    </a>
                </div>
            </div>
            <div class="header__bottom">
                <div class="header__categories categories" data-da=".fixed-bar, 600, 1">
                    <button class="button-categories icon-menu" type="button">
                        <span class="button-categories__menu">
                        </span>
                        <span class="button-categories__text">{{__('text.common_categories_menu')}}</span>
                    </button>
                    <div data-tabs class="categories__tabs tabs">
                        <nav data-tabs-titles class="tabs__navigation">
                            <button type="button" class="tabs__title _tab-active">{{__('text.common_best_selling_title')}}</button>
                            @foreach ($menu as $category)
                                <button type="button" class="tabs__title">{{ $category['name'] }}</button>
                            @endforeach
                        </nav>
                        <div data-tabs-body class="tabs__content">
                            <ul class="tabs__body">
                                @foreach ($bestsellers as $bestseller)
                                    <li class="tabs__item">
                                        <a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            @foreach ($menu as $category)
                                <ul class="tabs__body">
                                    @foreach ($category['products'] as $item)
                                        <li class="tabs__item">
                                            <a href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="header__menu menu">
                    <nav class="menu__body">
                        <ul class="menu__list">
                            <li class="menu__item"><a class="menu__link" href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                            <li class="menu__item"><a class="menu__link" href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                            <li class="menu__item"><a class="menu__link" href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                            <li class="menu__item" data-da=".menu__subslist, 900, first"><a class="menu__link" href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                            <li class="menu__item" data-da=".menu__subslist, 950, first"><a class="menu__link" href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                            <li class="menu__item" data-da=".menu__subslist, 1000, first"><a class="menu__link" href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu__item" data-da=".menu__subslist, 1050, first"><a class="menu__link" href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                            <li class="menu__dotts">
                                <span></span>
                                <ul class="menu__subslist">

                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
        </div>
    </div>
    <div class="popup_white hide">
        <div class="popup_push">
            <div class="button_close">
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
            </div>
            <div class="popup_block">
                <div class="popup_head">{{__('text.common_push_head')}}</div>
                <div class="popup_push_text">{{__('text.common_push_text')}}</div>
                <div class="push_buttons">
                    <div class="push_decline">{{__('text.common_decline')}}</div>
                    <div class="push_allow">{{__('text.common_allow')}}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="top_block first_block">
        <div class="left_top_block">
            <div class="discounts_info_block">
                <div class="num_block">
                    <div class="block_stars">
                        <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18" alt="">
                    </div>
                    <div class="block_num">
                        1 000 000
                    </div>
                     <div class="num_text">
                        {{__('text.common_customers')}}
                    </div>
                </div>
                <div class="discounts_block" data-da=".verified_info_block, 950, last">
                    <div class="discount_block">
                        <div class="discount_top">
                            <div>
                                <img src="{{ asset("$design/images/icons/pref-05.svg") }}" alt="">
                            </div>
                            <div class="discount_label">{{__('text.common_save')}}</div>
                        </div>
                        <div class="discount_text">{{__('text.common_discount')}}</div>
                    </div>
                    <div class="discount_block">
                        <div class="discount_top">
                            <div>
                                <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                            </div>
                            <div class="discount_label">{{__('text.common_delivery')}}</div>
                        </div>
                        <div class="discount_text">{{__('text.common_receive')}}</div>
                    </div>
                    <div class="discount_block">
                        <div class="discount_top">
                            <div>
                                <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                            </div>
                            <div class="discount_label">{{__('text.common_prescription')}}</div>
                        </div>
                        <div class="discount_text">{{__('text.common_restrictions')}}</div>
                    </div>
                    <div class="discount_block">
                        <div class="discount_top">
                            <div>
                                <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                            </div>
                            <div class="discount_label">{{__('text.common_moneyback')}}</div>
                        </div>
                        <div class="discount_text">{{__('text.common_refund')}}</div>
                    </div>
                </div>
            </div>
            <div class="white_line"></div>
            <div class="verified_info_block">
                <div class="verified_imgs" data-da=".discounts_info_block, 950, last">
                    <div>
                        <img src="{{ asset("$design/images/icons/verified.svg") }}" alt="">
                    </div>
                    <div>
                        <img src="{{ asset("$design/images/icons/partners.svg") }}" alt="" class="img_support_first">
                        <img src="{{ asset("$design/images/icons/partners_small.svg") }}" alt="" class="img_support_second">
                    </div>
                </div>
            </div>
        </div>
        <div class="right_top_block">
            <img src="{{ asset("$design/images/hero/01.png") }}" alt="">
        </div>
    </div>
    <div class="top_block second_block">
        <div class="num_block">
            <div class="block_stars">
                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18" alt="">
            </div>
            <div class="block_num">
                1 000 000
            </div>
            <div class="num_text">
                {{__('text.common_customers')}}
            </div>
        </div>
        <div class="right_top_block">
            <img src="{{ asset("$design/images/hero/01.png") }}" alt="">
        </div>
    </div>
    <div class="top_block third_block">
        <div class="verified_info_block">
            <div class="verified_imgs">
                <div>
                    <img src="{{ asset("$design/images/icons/verified.svg") }}" alt="">
                </div>
                <div>
                    <img src="{{ asset("$design/images/icons/partners_small.svg") }}" alt="" class="img_support_second">
                </div>
            </div>
        </div>
        <div class="white_line"></div>
        <div class="discounts_block">
            <div class="discount_line">
                <div class="discount_block">
                    <div class="discount_top">
                        <div>
                            <img src="{{ asset("$design/images/icons/pref-05.svg") }}" alt="">
                        </div>
                        <div class="discount_label">{{__('text.common_save')}}</div>
                    </div>
                    <div class="discount_text">{{__('text.common_discount')}}</div>
                </div>
                <div class="discount_block">
                    <div class="discount_top">
                        <div>
                            <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                        </div>
                        <div class="discount_label">{{__('text.common_prescription')}}</div>
                    </div>
                    <div class="discount_text">{{__('text.common_restrictions')}}</div>
                </div>
            </div>
            <div class="discount_line">
                <div class="discount_block">
                    <div class="discount_top">
                        <div>
                            <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                        </div>
                        <div class="discount_label">{{__('text.common_delivery')}}</div>
                    </div>
                    <div class="discount_text">{{__('text.common_receive')}}</div>
                </div>
                <div class="discount_block">
                    <div class="discount_top">
                        <div>
                            <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                        </div>
                        <div class="discount_label">{{__('text.common_moneyback')}}</div>
                    </div>
                    <div class="discount_text">{{__('text.common_refund')}}</div>
                </div>
            </div>
        </div>
    </div>

    <section class="pay-index">
        <div class="pay-index__container">
            <ul class="pay-index__list">
                <li class="pay-index__item">
                    <img src="/images/pay_icons/visa.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/mastercard.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/maestro.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/discover.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/amex.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/jsb.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/unionpay.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/dinners-club.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/apple-pay.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/google-pay.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/amazon-pay.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/stripe.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/paypal.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/sepa.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/cashapp.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/adyen.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/skrill.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/worldpay.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/payline.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/bitcoin.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/binance-coin.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/ethereum.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/litecoin.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/tron.svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/usdt(erc20).svg" alt="">
                </li>
                <li class="pay-index__item">
                    <img src="/images/pay_icons/usdt(trc20).svg" alt="">
                </li>
            </ul>
        </div>
    </section>
</header>

@yield('content')

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

@yield('testimonial', '')

<footer class="footer">
    <div class="footer_container">
        <div class="footer_left">
            <div>
                <img src="{{ asset("$design/images/logo_bottom.svg") }}">
            </div>
            <div class="footer_copyright">
                <p>
                    {{__('text.license_text_license1_1')}} {{str_replace(['http://', 'https://'], '', env('APP_URL'))}} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d10')}}
                </p>
            </div>
        </div>
        <div class="footer_right">
            <ul class="footer__menu">
                <li class="footer__item"><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                <li class="footer__item"><a href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
            </ul>
            <a href="{{ route('home.affiliate') }}" class="footer__button">{{__('text.common_affiliate_main_menu_button')}}</a>
        </div>
        <div class="footer_copyright bottom_license">
            <p>
                {{__('text.license_text_license1_1')}} {{str_replace(['http://', 'https://'], '', env('APP_URL'))}} {{__('text.license_text_license1_2')}}
                {{__('text.license_text_license2_d10')}}
            </p>
        </div>
    </div>
    <div class="social-share">
        <ul>
            <i class="fa-brands fa-facebook"></i>
            <i class="fa-brands fa-twitter"></i>
            <i class="fa-brands fa-telegram"></i>

        </ul>
    </div>
    <div class="fixed-bar">
        <a href="{$path.page}/{$smarty.const.PAGE_MAIN}" class="fixed-bar__item">
            <div class="fixed-bar__icon fixed-bar__icon--home">
                <svg width="22" height="20">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-home") }}"></use>
                </svg>
            </div>
            <div class="fixed-bar__label">{{__('text.common_home_main_menu_item')}}</div>
        </a>
    </div>
</footer>
</div>

<script src="{{ asset("$design/js/app.js") }}"></script>
<script src="{{ asset("/js/all_js.js") }}"></script>
<input hidden id="stattemp" value="{$data.web_statistic.params_string}">

</body>
</html>