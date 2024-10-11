<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#54a8c2" />
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

    <script src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        let flagm = false;
        const design = 4;
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
                    <div class="popup_text">{{ __('text.common_callback') }}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                        @if (empty(session('form'))) @selected($item['iso'] == session('location.country', ''))
                                @else
                                    @selected($item['iso'] == session('form.phone_code', '')) @endif
                                        data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                        value="+{{ $item['phonecode'] }}">
                                        +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="enter-info__input enter-info__input--country">
                            <input required autocomplete="off" type="number" id="phone" name="phone"
                                placeholder="000 000 00 00" class="input" maxlength = "14"
                                oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{ __('text.common_callback') }}</div>
                </div>
                <div class="message_sended hidden">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
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
                    <div class="popup_head">{{ __('text.common_push_head') }}</div>
                    <div class="popup_push_text">{{ __('text.common_push_text') }}</div>
                    <div class="push_buttons">
                        <div class="push_decline">{{ __('text.common_decline') }}</div>
                        <div class="push_allow">{{ __('text.common_allow') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <header class="header">
            <div class="header__phones-top top-phones-header">
                <div class="top-phones-header__container header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none">
                            <a class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_1') }}">{{ __('text.phones_title_phone_1_code') }}{{ __('text.phones_title_phone_1') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_2') }}">{{ __('text.phones_title_phone_2_code') }}{{ __('text.phones_title_phone_2') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_3') }}">{{ __('text.phones_title_phone_3_code') }}{{ __('text.phones_title_phone_3') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_4') }}">{{ __('text.phones_title_phone_4_code') }}{{ __('text.phones_title_phone_4') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_5') }}">{{ __('text.phones_title_phone_5_code') }}{{ __('text.phones_title_phone_5') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_6') }}">{{ __('text.phones_title_phone_6_code') }}{{ __('text.phones_title_phone_6') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_7') }}">{{ __('text.phones_title_phone_7_code') }}{{ __('text.phones_title_phone_7') }}</a>
                    </div>
                </div>
            </div>
            <div class="header__container">
                <div class="header__body">
                    <div class="header__bg">
                        <picture>
                            <source srcset="{{ asset("$design/images/hero/hero-bg-xs.webp") }}" type="image/webp"
                                media="(max-width: 650px)">
                            <source srcset="{{ asset("$design/images/hero/hero-bg-s.webp") }}" type="image/webp"
                                media="(max-width: 991px)">
                            <source srcset="{{ asset("$design/images/hero/hero-bg.webp") }}" type="image/webp">
                            <img src="{{ asset("$design/images/hero/hero-bg.png") }}" decoding="async"
                                alt="" width="1000" height="330">
                        </picture>
                    </div>
                    <div class="header__content">
                        <div class="header__top">
                            <a href="{{ route('home.index') }}" class="header__logo">
                                <img src="{{ asset("$design/images/logo.svg") }}" alt="">
                            </a>
                            <div class="header__phone1">

                            </div>
                            <div class="header__phone2">
                                <div class="profile">
                                    <img src="{{ asset("$design/images/user_blue_2.png") }}">
                                    <a href="{{ route('home.login') }}"
                                        target="_blank">{{ __('text.common_profile') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="header__menu">
                            <div class="menu">
                                <nav class="menu__body">
                                    <ul class="menu__list">
                                        <li class="menu__item"><a href="{{ route('home.about') }}"
                                                class="menu__link">{{ __('text.common_about_us_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.help') }}"
                                                class="menu__link">{{ __('text.common_help_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.testimonials') }}"
                                                class="menu__link">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.index') }}"
                                                class="menu__link">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.delivery') }}"
                                                class="menu__link">{{ __('text.common_shipping_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.moneyback') }}"
                                                class="menu__link">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item"><a href="{{ route('home.contact_us') }}"
                                                class="menu__link">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                        </li>
                                        <li class="menu__item menu__item--action">
                                            <svg width="3" height="16">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-dots") }}">
                                                </use>
                                            </svg>
                                            <ul class="menu__sublist">
                                            </ul>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="header__hero hero-header">
                            <div class="hero-header__about">
                                <div class="hero-header__reviews reviews-hero">
                                    <div class="reviews-hero__quantity">1 000 000</div>
                                    <div class="reviews-hero__icon">
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                            </use>
                                        </svg>
                                    </div>
                                    <p class="reviews-hero__descr">{{ __('text.common_customers') }}</p>
                                </div>
                                <div class="hero-header__events events-hero">
                                    <div class="events-hero__item">
                                        <div class="events-hero__desrc">
                                            <h2 class="events-hero__title">{{ __('text.common_banner1_text1') }} <br>
                                                {{ __('text.common_banner1_text2') }}</h2>
                                            <p class="events-hero__text">{{ __('text.common_banner1_text3') }} <br>
                                                {{ __('text.common_banner1_text4') }}</p>
                                        </div>
                                        <div class="events-hero__icon">
                                            <picture>
                                                <source srcset="{{ asset("$design/images/icons/h-01.webp") }}"
                                                    type="image/webp">
                                                <img decoding="async" loading="lazy"
                                                    src="{{ asset("$design/images/icons/h-01.png") }}" width="45"
                                                    height="45" alt="">
                                            </picture>
                                        </div>
                                    </div>
                                    <div class="events-hero__item">
                                        <div class="events-hero__desrc">
                                            <h2 class="events-hero__title">{{ __('text.common_banner2_text1') }} <br>
                                                {!! __('text.common_banner2_text2') !!}</h2>
                                            <p class="events-hero__text">{{ __('text.common_banner2_text3') }} <br>
                                                {{ __('text.common_banner2_text4') }}</p>
                                        </div>
                                        <div class="events-hero__icon">
                                            <picture>
                                                <source srcset="{{ asset("$design/images/icons/h-02.webp") }}"
                                                    type="image/webp"><img decoding="async" loading="lazy"
                                                    src="{{ asset("$design/images/icons/h-02.png") }}"
                                                    width="45" height="45" alt="">
                                            </picture>
                                        </div>
                                    </div>
                                </div>
                                <div class="hero-header__cart cart-hero">
                                    <div class="cart-hero__inner">
                                        <div class="cart-hero__image">
                                            <picture>
                                                <source srcset="{{ asset("$design/images/doctor-s.webp") }}"
                                                    type="image/webp" media="(max-width:991px)">
                                                <source srcset="{{ asset("$design/images/doctor.webp") }}"
                                                    type="image/webp">
                                                <img src="{{ asset("$design/images/doctor.png") }}" decoding="async"
                                                    alt="" width="298" height="181">
                                            </picture>
                                        </div>
                                        <div class="cart-hero__body">
                                            <div class="cart-hero__icon">
                                                <svg width="18.5" height="21.5">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                            <div class="cart-hero__row">
                                                <h2 class="cart-hero__label">
                                                    {{ __('text.common_cart_text') }}
                                                </h2>
                                                <div class="cart-hero__row">
                                                    @php
                                                        $cart_count = 0;
                                                        $cart_total = 0;
                                                        if (!empty(session('cart'))) {
                                                            foreach (session('cart') as $value) {
                                                                $cart_count += $value['q'];
                                                                $cart_total += $value['price'] * $value['q'];
                                                            }
                                                        }
                                                    @endphp
                                                    @if ($cart_count != 0)
                                                        <a href="{{ route('cart.index') }}"
                                                            class="cart-hero__data"><span>{{ $cart_count }}</span>
                                                            {{ __('text.common_items_d4') }}</a>
                                                        <a href="{{ route('cart.index') }}"
                                                            class="cart-hero__data">{{ $Currency::convert($cart_total) }}</a>
                                                    @else
                                                        <a class="cart-hero__data"><span>{{ $cart_count }}</span>
                                                            {{ __('text.common_items_d4') }}</a>
                                                        <a
                                                            class="cart-hero__data">{{ $Currency::convert($cart_total) }}</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-header__middle" data-da=".hero-header, 650, first">
                                <div class="hero-header__bonus bonus-hero">
                                    <div class="bonus-hero__image">
                                        <picture>
                                            <source srcset="{{ asset("$design/images/products/p-bonus.webp") }}"
                                                type="image/webp"><img decoding="async" loading="lazy"
                                                src="{{ asset("$design/images/products/p-bonus.png") }}"
                                                width="84" height="60" alt="">
                                        </picture>
                                    </div>
                                    <div class="bonus-hero__descr">
                                        <h3 class="bonus-hero__title">{{ __('text.common_bonus_text1') }}</h3>
                                        <p class="bonus-hero__text">{{ __('text.common_bonus_text2') }}</p>
                                    </div>
                                </div>
                                <div class="hero-header__selects" data-one-select>
                                    @if (count($Language::GetAllLanuages()) > 1)
                                        <div class="hero-header__select">
                                            <select name="form[]" class="form"
                                                onchange="location.href=this.options[this.selectedIndex].value">
                                                @foreach ($Language::GetAllLanuages() as $language)
                                                    <option value="/lang={{ $language['code'] }}"
                                                        @if (App::currentLocale() == $language['code']) selected @endif>
                                                        {{ $language['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if (count($Currency::GetAllCurrency()) > 1)
                                        <div class="hero-header__select">
                                            <select name="form[]" class="form"
                                                onchange="location.href=this.options[this.selectedIndex].value">
                                                @foreach ($Currency::GetAllCurrency() as $item)
                                                    <option value="/curr={{ $item['code'] }}"
                                                        @if (session('currency') == $item['code']) selected @endif>
                                                        {{ Str::upper($item['code']) }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="hero-header__features features-hero">
                                <div class="features-hero__item">
                                    <div class="features-hero__icon">
                                        <picture>
                                            <source srcset="{{ asset("$design/images/icons/f-01.webp") }}"
                                                type="image/webp"><img decoding="async" loading="lazy"
                                                src="{{ asset("$design/images/icons/f-01.png") }}" width="50"
                                                height="50" alt="">
                                        </picture>
                                    </div>
                                    <p class="features-hero__descr">{{ __('text.common_save') }}</p>
                                </div>
                                <div class="features-hero__item">
                                    <div class="features-hero__icon">
                                        <picture>
                                            <source srcset="{{ asset("$design/images/icons/f-02.webp") }}"
                                                type="image/webp"><img decoding="async" loading="lazy"
                                                src="{{ asset("$design/images/icons/f-02.png") }}" width="50"
                                                height="50" alt="">
                                        </picture>
                                    </div>
                                    <p class="features-hero__descr">{{ __('text.common_prescription') }}</p>
                                </div>
                                <div class="features-hero__item">
                                    <div class="features-hero__icon">
                                        <picture>
                                            <source srcset="{{ asset("$design/images/icons/f-03.webp") }}"
                                                type="image/webp"><img decoding="async" loading="lazy"
                                                src="{{ asset("$design/images/icons/f-03.png") }}" width="50"
                                                height="50" alt="">
                                        </picture>
                                    </div>
                                    <p class="features-hero__descr">{{ __('text.common_moneyback') }}</p>
                                </div>
                                <div class="features-hero__item">
                                    <div class="features-hero__icon">
                                        <picture>
                                            <source srcset="{{ asset("$design/images/icons/f-04.webp") }}"
                                                type="image/webp"><img decoding="async" loading="lazy"
                                                src="{{ asset("$design/images/icons/f-04.png") }}" width="50"
                                                height="50" alt="">
                                        </picture>
                                    </div>
                                    <p class="features-hero__descr">{{ __('text.common_delivery') }}</p>
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
                            <div class="hero-header__search">
                                <div class="search-bar" data-dev>
                                    <form class="search-bar__input search-form" style="position: relative;"
                                        action="{{ route('search.search_product') }}" method = "POST">
                                        @csrf
                                        <input id="autocomplete" autocomplete="off" type="text"
                                            name="search_text" data-error="Error" class="input"
                                            placeholder="{{ __('text.common_search') }}">
                                        <button type="submit" class="search-bar__icon">
                                            <svg width="15" height="15">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}">
                                                </use>
                                            </svg>
                                            <span class="sr-only" style="display: none;">search</span>
                                        </button>
                                        <button type="button" class="search-bar__close">
                                            <svg width="13" height="13">
                                                <use
                                                    xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}">
                                                </use>
                                            </svg>
                                        </button>
                                        <ul class="search-bar__results" style="display: none;"></ul>
                                    </form>
                                    <div class="search-bar__nav" data-simplebar data-simplebar-auto-hide="false">
                                        <ul class="search-bar__letter-list">
                                            @foreach ($first_letters as $key => $active_letter)
                                                <li class="search-bar__item-list">
                                                    @if ($active_letter)
                                                        <a href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                                                    @else
                                                        {{ $key }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="page">
            <section class="page__content content">
                <div class="content__container">
                    <div class="content__inner" id="scroll">
                        <aside class="categories-sidebar" data-da=".header__menu, 991.98, first">
                            <div class="categories-sidebar__inner">
                                <div class="categories-sidebar__top-row">
                                    <div class="categories-sidebar__icon"><button type="button"
                                            class="icon-menu icon-menu--first"><span></span></button></div>
                                    <h3 class="categories-sidebar__title">{{ __('text.common_categories_menu') }}
                                    </h3>
                                </div>
                                <div data-spollers class="categories-sidebar__spollers spollers">
                                    <div class="spollers__item">
                                        <button type="button" data-spoller
                                            class="spollers__title _spoller-active">{{ __('text.common_best_selling_title') }}</button>
                                        <ul class="spollers__body main_bestsellers" id="main_bestsellers">
                                            @foreach ($bestsellers as $bestseller)
                                                <li class="spollers__item-list"><a
                                                        href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span
                                                        style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @foreach ($menu as $category)
                                        <div class="spollers__item">
                                            <button type="button" data-spoller
                                                class="spollers__title @if ($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                                            <ul class="spollers__body">
                                                @foreach ($category['products'] as $item)
                                                    <li class="spollers__item-list">
                                                        <a href="{{ route('home.product', $item['url']) }}">
                                                            {{ $item['name'] }}
                                                        </a>
                                                        <span
                                                            style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </aside>
                        <div class="content__body" id="scroll">

                            @yield('content')

                        </div>
            </section>

            <section class="subscribe__container">
                <div class="subscribe_body">
                    <div class="left_block">
                        <div class="subscribe_img">
                            <img src="{{ asset("$design/images/icons/subscribe.svg") }}">
                        </div>
                        <div class="text_subscribe">
                            <span class="top_text">{{ __('text.common_subscribe') }}</span>
                            <span class="bottom_text">{{ __('text.common_spec_offer') }}</span>
                        </div>
                    </div>
                    <div class="right_block">
                        <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                        <div class="button_sub">
                            <img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                            <span class="button_text">{{ __('text.common_subscribe') }}</span>
                        </div>
                    </div>
                </div>
            </section>

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

            <section class="page__partners partners">
                <div class="partners__container">
                    <div class="partners__body">
                        <div class="partners__row">
                            <div class="partners__icon">
                                <picture>
                                    <source srcset="{{ asset("$design/images/partners/verified.webp") }}"
                                        type="image/webp">
                                    <img decoding="async" loading="lazy"
                                        src="{{ asset("$design/images/partners/verified.png") }}" width="85"
                                        height="85" alt="">
                                </picture>
                            </div>
                            <div class="partners__info">
                                <h2 class="partners__label">{{ __('text.common_verified_d4') }}</h2>
                                <p class="partners__text">{{ __('text.common_approved_d4') }}</p>
                            </div>
                        </div>
                        <div class="partners__items">
                            <div class="partners__item">
                                <img src="{{ asset("$design/images/partners/fda.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <picture>
                                    <source srcset="{{ asset("$design/images/partners/pgeu.webp") }}"
                                        type="image/webp"><img decoding="async" loading="lazy"
                                        src="{{ asset("$design/images/partners/pgeu.png") }}" alt="">
                                </picture>
                            </div>
                            <div class="partners__item">
                                <picture>
                                    <source srcset="{{ asset("$design/images/partners/cipa.webp") }}"
                                        type="image/webp"><img decoding="async" loading="lazy"
                                        src="{{ asset("$design/images/partners/cipa.png") }}" alt="">
                                </picture>
                            </div>
                            <div class="partners__item">
                                <img src="{{ asset("$design/images/partners/mastercard.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <img src="{{ asset("$design/images/partners/visa.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <img src="{{ asset("$design/images/partners/mcafee.svg") }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="page__reviews reviews">
                <div class="reviews__container">
                    <div class="reviews__body">
                        <div class="reviews__slider">
                            <div class="reviews__swiper">
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_1') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_1') }}</div>
                                    </div>
                                </div>
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_2') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_2') }}</div>
                                    </div>
                                </div>
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_3') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_3') }}</div>
                                    </div>
                                </div>
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_4') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_4') }}</div>
                                    </div>
                                </div>
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_5') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_5') }}</div>
                                    </div>
                                </div>
                                <div class="reviews__slide">
                                    <div class="review-item">
                                        <div class="review-item__top">
                                            <div class="review-item__name">{!! __('text.testimonials_author_t_6') !!}</div>
                                            <div class="review-item__stars">
                                                <svg width="98" height="18">
                                                    <use
                                                        xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}">
                                                    </use>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="review-item__text">{{ __('text.testimonials_t_6') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reviews__controls">
                            <button type="button" class="reviews__arrow reviews__arrow--prev">
                                <svg width="19" height="17">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}">
                                    </use>
                                </svg>
                            </button>
                            <button type="button" class="reviews__arrow reviews__arrow--next">
                                <svg width="19" height="17">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}">
                                    </use>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="footer">
            <div class="footer__container">
                <ul class="footer__menu">

                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.affiliate') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.about') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.help') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.testimonials') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.delivery') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.moneyback') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                    </li>
                    <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.contact_us') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                    </li>
                </ul>
                <div class="licen">
                    <p class="footer__copyright">
                        {{ __('text.license_text_license1_1') }}
                        {{ $domain }}
                        {{ __('text.license_text_license1_2') }}
                        {{ __('text.license_text_license2_d4') }}
                    </p>
                </div>
            </div>
        </footer>

        <div class="actions-mobile">
            <div class="actions-mobile__container">
                <div class="actions-mobile__items">
                    <div class="actions-mobile__item">
                        <a href="{{ route('home.index') }}">
                            <div class="actions-mobile__icon">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-home") }}"></use>
                                </svg>
                            </div>
                            <h3 class="actions-mobile__label">{{ __('text.common_home_main_menu_item') }}</h3>
                        </a>
                    </div>
                    <div class="actions-mobile__item actions-mobile__item--categories">
                        <div class="actions-mobile__icon">
                            <button type="button" class="icon-menu icon-menu--second"><span></span></button>
                        </div>
                        <h3 class="actions-mobile__label">{{ __('text.common_categories_menu') }}</h3>
                    </div>
                    <div class="actions-mobile__item">
                        <a href="{{ route('home.login') }}" target="_blank">
                            <div class="actions-mobile__icon">
                                <img src="{{ asset("$design/images/user_blue_2.png") }}">
                            </div>
                            <h3 class="actions-mobile__label">{{ __('text.common_profile') }}</h3>
                        </a>
                    </div>
                    <div class="actions-mobile__item">
                        @if ($cart_count != 0)
                            <a href="{{ route('cart.index') }}">
                        @endif
                        <div class="actions-mobile__icon">
                            <svg width="20" height="20">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                            </svg>
                            <div class="actions-mobile__quantity">
                                {{ $cart_count }}
                            </div>
                        </div>
                        <h3 class="actions-mobile__label">{{ $Currency::convert($cart_total) }}</h3>
                        @if ($cart_count != 0)
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="announce">
        {{-- {if $data.is_product_page}
        <div class="announce__item announce__item--blue">
            <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="{$path.image}/icons/icons.svg#svg-checkmark"></use>
                </svg>
            </div>
            <div class="announce__text">
                <b>{$data.rand}{#product1#}</b>{#product2#}
            </div>
        </div>
    {/if}
    {if $data.is_cart_page}
        <div class="announce__item announce__item--yellow">
            <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="{$path.image}/icons/icons.svg#svg-clock"></use>
                </svg>
            </div>
            <div class="announce__text">
                {#cart1#}<b>{$data.customer.country}{#cart2#}</b>
            </div>
        </div>
    {/if} --}}
    </div>

    {{-- {if $data.page_name ne "checkout"}
    {if $data.web_statistic}
        <input hidden id="stattemp" value="{$data.web_statistic.params_string}">
    {/if}
{/if} --}}
    <input hidden id="stattemp" value="{$data.web_statistic.params_string}">
    <script src="{{ asset("$design/js/app.js") }}"></script>
    <script src="{{ asset('/js/all_js.js') }}"></script>

</body>

</html>
