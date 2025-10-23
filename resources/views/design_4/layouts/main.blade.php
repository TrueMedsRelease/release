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

    @foreach ($Language::GetAllLanuages() as $item)
        <link rel="alternate" href="{{ route('home.language', $item['code']) }}"
            @if ($item['code'] == 'arb')
                hreflang="ar"
            @elseif ($item['code'] == 'gr')
                hreflang="el"
            @elseif ($item['code'] == 'hans')
                hreflang="zh-Hans"
            @elseif ($item['code'] == 'hant')
                hreflang="zh-Hant"
            @else
                hreflang={{ $item['code'] }}
            @endif
        />
    @endforeach

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        let flagm = false;
        const design = 4;
    </script>
@if (session('locale'))
    <input type="hidden" id="lang_session" value="{{ session('locale') }}">
@endif
@if (session('order'))
    <input type="hidden" id="order_info_session" value="{{ json_encode(session('order')) }}">
@endif

@php
    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
@endphp

@php
    $phone_arr = [
        1 => 'US',
        2 => 'CA',
        3 => 'AU',
        4 => 'UK',
        5 => 'FR',
        6 => 'ES',
        7 => 'NZ',
        8 => 'DK',
        9 => 'SE',
        10 => 'CH',
        11 => 'CZ',
        12 => 'FI',
        13 => 'GR',
        14 => 'PT',
        15 => 'DE',
        16 => 'IT',
        17 => 'NL'
    ];

    $country_code = session('location.country', 'US');

    if ($country_code && in_array($country_code, $phone_arr)) {
        $target_key = array_search($country_code, $phone_arr);
        $target_value = $phone_arr[$target_key];
        unset($phone_arr[$target_key]);

        $phone_arr = [$target_key => $target_value] + $phone_arr;
    }
@endphp

<input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
<input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
<input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">

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
                                        data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
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

            <div class="christmas" style="display: none">
                <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
                {{-- <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}"> --}}
            </div>

            <div class="header__phones-top top-phones-header">
                <div class="top-phones-header__container header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none">
                            <a class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                        @endforeach
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
                            <img loading="lazy" src="{{ asset("$design/images/hero/hero-bg.png") }}" decoding="async"
                                alt="" width="1000" height="330">
                        </picture>
                    </div>
                    <div class="header__content">
                        <div class="header__top">
                            <a href="{{ route('home.index') }}" class="header__logo">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="{{ $domainWithoutZone }}">
                                @else
                                    <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="Logo">
                                @endif
                            </a>
                            <div class="header__phone1">

                            </div>
                            <div class="header__phone2">
                                <div class="profile">
                                    <img loading="lazy" src="{{ asset("$design/images/user_blue_2.png") }}">
                                    <a href="{{ route('home.login') }}"
                                        target="_blank">{{ __('text.common_profile') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="header__menu">
                            <div class="menu">
                                <nav class="menu__body">
                                    <ul class="menu__list">
                                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))

                                            <li class="menu__item"><a href="{{ route('home.about', '_' . $domainWithoutZone) }}"
                                                class="menu__link">{{ __('text.common_about_us_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.help', '_' . $domainWithoutZone) }}"
                                                    class="menu__link">{{ __('text.common_help_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}"
                                                    class="menu__link">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.index') }}"
                                                    class="menu__link">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.delivery', '_' . $domainWithoutZone) }}"
                                                    class="menu__link">{{ __('text.common_shipping_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}"
                                                    class="menu__link">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}"
                                                    class="menu__link">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                            </li>
                                        @else
                                            <li class="menu__item"><a href="{{ route('home.about', '') }}"
                                                    class="menu__link">{{ __('text.common_about_us_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.help', '') }}"
                                                    class="menu__link">{{ __('text.common_help_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.testimonials', '') }}"
                                                    class="menu__link">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.index') }}"
                                                    class="menu__link">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.delivery', '') }}"
                                                    class="menu__link">{{ __('text.common_shipping_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.moneyback', '') }}"
                                                    class="menu__link">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                            </li>
                                            <li class="menu__item"><a href="{{ route('home.contact_us', '') }}"
                                                    class="menu__link">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                            </li>
                                        @endif
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
                                                <img loading="lazy" decoding="async" loading="lazy"
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
                                                    type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                                <img loading="lazy" src="{{ asset("$design/images/doctor.png") }}" decoding="async"
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
                                                type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                            <select name="form[]" class="form" id="lang_select"
                                                onchange="location.href=this.options[this.selectedIndex].value">
                                                @foreach ($Language::GetAllLanuages() as $item)
                                                    <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                                        @if (App::currentLocale() == $item['code']) selected @endif>
                                                        {{ $item['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if (count($Currency::GetAllCurrency()) > 1)
                                        <div class="hero-header__select">
                                            <select name="form[]" class="form" id="curr_select"
                                                onchange="location.href=this.options[this.selectedIndex].value">
                                                @foreach ($Currency::GetAllCurrency() as $item)
                                                    <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif>
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
                                                type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                                type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                                type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                                type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
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
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                                            </svg>
                                        </li>
                                        <li class="pay-index__item">
                                            <svg>
                                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                                            </svg>
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

        <div class="checkup img__container" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
        </div>

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
                            <img loading="lazy" src="{{ asset("$design/images/icons/subscribe.svg") }}">
                        </div>
                        <div class="text_subscribe">
                            <span class="top_text">{{ __('text.common_subscribe') }}</span>
                            <span class="bottom_text">{{ __('text.common_spec_offer') }}</span>
                        </div>
                    </div>
                    <div class="right_block">
                        <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                        <div class="button_sub">
                            <img loading="lazy" src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
                            <span class="button_text">{{ __('text.common_subscribe') }}</span>
                        </div>
                    </div>
                </div>
            </section>

           <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#usps') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ems') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dhl') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ups') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#fedex') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#tnt') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#postnl') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#deutsche_post') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dpd') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#gls') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                        <use width="100%" height="100%" width="100%" href="{{ asset('pub_images/shipping/sprite.svg#australia_post') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#colissimo') }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#correos') }}" preserveAspectRatio="xMinYMin">
                    </svg>
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
                                    <img loading="lazy" decoding="async" loading="lazy"
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
                                <img loading="lazy" src="{{ asset("$design/images/partners/fda.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <picture>
                                    <source srcset="{{ asset("$design/images/partners/pgeu.webp") }}"
                                        type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
                                        src="{{ asset("$design/images/partners/pgeu.png") }}" alt="">
                                </picture>
                            </div>
                            <div class="partners__item">
                                <picture>
                                    <source srcset="{{ asset("$design/images/partners/cipa.webp") }}"
                                        type="image/webp"><img loading="lazy" decoding="async" loading="lazy"
                                        src="{{ asset("$design/images/partners/cipa.png") }}" alt="">
                                </picture>
                            </div>
                            <div class="partners__item">
                                <img loading="lazy" src="{{ asset("$design/images/partners/mastercard.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <img loading="lazy" src="{{ asset("$design/images/partners/visa.svg") }}" alt="">
                            </div>
                            <div class="partners__item">
                                <img loading="lazy" src="{{ asset("$design/images/partners/mcafee.svg") }}" alt="">
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
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                        </li>
                    @else
                        <li class="footer__item"><a class="footer__link"
                            href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                        </li>
                        <li class="footer__item"><a class="footer__link"
                                href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                        </li>
                    @endif
                </ul>
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="sitemap_menu">
                        <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                    </div>
                @endif
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
                                <img loading="lazy" src="{{ asset("$design/images/user_blue_2.png") }}">
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
        <div class="announce__item @yield('announce_color', 'announce__item--blue')">
            <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="@yield('announce_img', asset($design . '/images/icons/icons.svg#svg-checkmark'))"></use>
                </svg>
            </div>
            <div class="announce__text">
                <b>@yield('announce_text_1', random_int(2, 30) .' ' .__('text.common_product1'))</b>@yield('announce_text_2', __('text.common_product2'))
            </div>
        </div>
    </div>

    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif

    <script>
        const routeRequestCall = "{{ route('home.request_call') }}";
        const routeRequestSubscribe = "{{ route('home.request_subscribe') }}";
        const routeRequestContactUs = "{{ route('home.request_contact_us') }}";
        const routeRequestAffiliate = "{{ route('home.request_affiliate') }}";

        const routeCartUp = "{{ route('cart.up') }}";
        const routeCartDown = "{{ route('cart.down') }}";
        const routeCartRemove = "{{ route('cart.remove') }}";
        const routeCartUpgrade = "{{ route('cart.upgrade') }}";
        const routeCartShipping = "{{ route('cart.shipping') }}";
        const routeCartBonus = "{{ route('cart.bonus') }}";

        const routeCheckCode = "{{ route('home.check_code') }}";
        const routeRequestLogin = "{{ route('home.request_login') }}";

        const routeSavePush = "{{ route('home.save_push_data') }}";
        const routeCart = "{{ route('cart.index') }}";

        const pathImageCheckupBiggest = "{{ asset('pub_images/checkup_img/white/checkup_biggest.png') }}";
        const pathImageCheckupBig = "{{ asset('pub_images/checkup_img/white/checkup_big.png') }}";
        const pathImageCheckupMiddle = "{{ asset('pub_images/checkup_img/white/checkup_middle.png') }}";
        const pathImageCheckupSmall = "{{ asset('pub_images/checkup_img/white/checkup_small.png') }}";

        const pathImagePayBiggest = "{{ asset('pub_images/pay_biggest.png') }}";
        const pathImagePayBig = "{{ asset('pub_images/pay_big.png') }}";
        const pathImagePayMiddle = "{{ asset('pub_images/pay_middle.png') }}";
        const pathImagePaySmall = "{{ asset('pub_images/pay_small.png') }}";
    </script>

    <script defer src="{{ asset("$design/js/app.js") }}"></script>
    <script defer src="{{ asset('js/all_js.js') }}"></script>

</body>

</html>
