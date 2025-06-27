<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4494DE" />
    <meta name="format-detection" content="telephone=no">

    {{-- <link rel="alternate" href="{{ route('home.language', 'arb') }}" hreflang="ar" />
    <link rel="alternate" href="{{ route('home.language', 'cs') }}" hreflang="cs" />
    <link rel="alternate" href="{{ route('home.language', 'da') }}" hreflang="da" />
    <link rel="alternate" href="{{ route('home.language', 'de') }}" hreflang="de" />
    <link rel="alternate" href="{{ route('home.language', 'en') }}" hreflang="en" />
    <link rel="alternate" href="{{ route('home.language', 'es') }}" hreflang="es" />
    <link rel="alternate" href="{{ route('home.language', 'fi') }}" hreflang="fi" />
    <link rel="alternate" href="{{ route('home.language', 'fr') }}" hreflang="fr" />
    <link rel="alternate" href="{{ route('home.language', 'gr') }}" hreflang="el" />
    <link rel="alternate" href="{{ route('home.language', 'hans') }}" hreflang="zh-Hans" />
    <link rel="alternate" href="{{ route('home.language', 'hant') }}" hreflang="zh-Hant" />
    <link rel="alternate" href="{{ route('home.language', 'hu') }}" hreflang="hu" />
    <link rel="alternate" href="{{ route('home.language', 'it') }}" hreflang="it" />
    <link rel="alternate" href="{{ route('home.language', 'ja') }}" hreflang="ja" />
    <link rel="alternate" href="{{ route('home.language', 'ms') }}" hreflang="ms" />
    <link rel="alternate" href="{{ route('home.language', 'nl') }}" hreflang="nl" />
    <link rel="alternate" href="{{ route('home.language', 'no') }}" hreflang="no" />
    <link rel="alternate" href="{{ route('home.language', 'pl') }}" hreflang="pl" />
    <link rel="alternate" href="{{ route('home.language', 'pt') }}" hreflang="pt" />
    <link rel="alternate" href="{{ route('home.language', 'ro') }}" hreflang="ro" />
    <link rel="alternate" href="{{ route('home.language', 'sk') }}" hreflang="sk" />
    <link rel="alternate" href="{{ route('home.language', 'sv') }}" hreflang="sv" /> --}}

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
        <script defer type="text/javascript" src="{{ asset("/js/sw-setup.js") }}"></script>
    @endif

    <script type="text/javascript" src="{{ asset("/js/delete_cache.js") }}"></script>

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        const design = 9;
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

    <input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
    <input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
    <input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">

    <div class="wrapper">

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("/pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("/pub_images/christmas_big.png") }}">
        </div> --}}

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
        <header class="header">
            <div class="header__phones-top top-phones-header">
                <div class="header__container">
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
            <div class="container header__container">
                <div class="header__top">
                    <div class="header__inner">
                        <a href="{{ route('home.index') }}" class="header__logo logo">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <img class="logo__icon" src="{{ asset("$design/images/logo.svg") }}" alt="{{ $domainWithoutZone }}">
                            @else
                                <img class="logo__icon" src="{{ asset("$design/images/logo.svg") }}" alt="Logo">
                            @endif
                        </a>
                        <form class="header__search" data-da=".header__top, 1024, last"
                            action="{{ route('search.search_product') }}" method = "POST" data-dev>
                            @csrf
                            <div class="search search-bar">
                                <div class="search__input">
                                    <input id="autocomplete" autocomplete="off" type="text" name="search_text"
                                        placeholder="{{ __('text.common_search') }}">
                                </div>
                                <button class="search__icon" type="submit">
                                    <span class="visually-hidden">search</span>
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}">
                                        </use>
                                    </svg>
                                </button>
                            </div>
                        </form>
                        <div class="header__actions actions">
                            @if (count($Language::GetAllLanuages()) > 1)
                                <div class="actions__item">
                                    <div class="actions__icon">
                                        <svg width="20" height="20">
                                            <use
                                                xlink:href="{{ asset("$design/images/icons/icons.svg#svg-global") }}">
                                            </use>
                                        </svg>
                                    </div>
                                    <div class="actions__select">
                                        <select name="form[]" class="form" id="lang_select"
                                            onchange="location.href=this.options[this.selectedIndex].value"
                                            data-scroll>
                                            @foreach ($Language::GetAllLanuages() as $item)
                                                <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                                    @if (App::currentLocale() == $item['code']) selected @endif>
                                                    {{ $item['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if (count($Currency::GetAllCurrency()) > 1)
                                <div class="actions__item">
                                    <div class="actions__icon">
                                        <svg width="20" height="20">
                                            <use
                                                xlink:href="{{ asset("$design/images/icons/icons.svg#svg-wallet") }}">
                                            </use>
                                        </svg>
                                    </div>
                                    <div class="actions__select">
                                        <select name="form[]" class="form" id="curr_select"
                                            onchange="location.href=this.options[this.selectedIndex].value"
                                            data-scroll>
                                            @foreach ($Currency::GetAllCurrency() as $item)
                                                <option value="{{ route('home.currency', $item['code']) }}"
                                                    @if (session('currency') == $item['code']) selected @endif>
                                                    {{ Str::upper($item['code']) }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="actions__item actions__item--order" data-da=".fixed-bar, 600, last">
                                <a href='{{ route('home.login') }}' target="_blank">
                                    <div class="actions__icon">
                                        <svg width="20" height="20">
                                            <use
                                                xlink:href="{{ asset("$design/images/icons/icons.svg#svg-profile") }}">
                                            </use>
                                        </svg>
                                    </div>
                                    <div class="actions__label">{{ __('text.common_profile') }}</div>
                                </a>
                            </div>
                        </div>
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
                        <a href="{{ route('cart.index') }}" type="button" class="header__cart cart"
                            data-da=".fixed-bar, 600, 2">
                            @if ($cart_count != 0)
                                <span class="cart__icon">
                                    <svg width="24" height="24">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}">
                                        </use>
                                    </svg>
                                    <span class="cart__quantity">{{ $cart_count }}</span>
                                </span>
                                <span class="cart__text">{{ __('text.common_cart_text_d2') }}</span>
                                <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                            @else
                                <span class="cart__icon">
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}">
                                        </use>
                                    </svg>
                                </span>
                                <span class="cart__text">{{ __('text.common_cart_text_d2') }}</span>
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
                            <span class="button-categories__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <div data-tabs class="categories__tabs tabs">
                            <nav data-tabs-titles class="tabs__navigation">
                                <button type="button"
                                    class="tabs__title _tab-active">{{ __('text.common_best_selling_title') }}</button>
                                @foreach ($menu as $category)
                                    <button type="button" class="tabs__title">{{ $category['name'] }}</button>
                                @endforeach
                            </nav>
                            <div data-tabs-body class="tabs__content">
                                <ul class="tabs__body">
                                    @foreach ($bestsellers as $bestseller)
                                        <li class="tabs__item">
                                            <a
                                                href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                                @foreach ($menu as $category)
                                    <ul class="tabs__body">
                                        @foreach ($category['products'] as $item)
                                            <li class="tabs__item">
                                                <a
                                                    href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
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
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <li class="menu__item"><a class="menu__link"
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 900, first"><a class="menu__link"
                                            href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 950, first"><a class="menu__link"
                                            href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1000, first"><a class="menu__link"
                                            href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1050, first"><a class="menu__link"
                                            href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @else
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item"><a class="menu__link"
                                            href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 900, first"><a class="menu__link"
                                            href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 950, first"><a class="menu__link"
                                            href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1000, first"><a class="menu__link"
                                            href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="menu__item" data-da=".menu__subslist, 1050, first"><a class="menu__link"
                                            href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @endif
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
                        <div class="popup_head">{{ __('text.common_push_head') }}</div>
                        <div class="popup_push_text">{{ __('text.common_push_text') }}</div>
                        <div class="push_buttons">
                            <div class="push_decline">{{ __('text.common_decline') }}</div>
                            <div class="push_allow">{{ __('text.common_allow') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="top_block first_block">
                <div class="left_top_block">
                    <div class="discounts_info_block">
                        <div class="num_block">
                            <div class="block_stars">
                                <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98"
                                    height="18" alt="">
                            </div>
                            <div class="block_num">
                                1 000 000
                            </div>
                            <div class="num_text">
                                {{ __('text.common_customers') }}
                            </div>
                        </div>
                        <div class="discounts_block" data-da=".verified_info_block, 950, last">
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-05.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_save') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_discount') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_delivery') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_receive') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_prescription') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_restrictions') }}</div>
                            </div>
                            <div class="discount_block">
                                <div class="discount_top">
                                    <div>
                                        <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                                    </div>
                                    <div class="discount_label">{{ __('text.common_moneyback') }}</div>
                                </div>
                                <div class="discount_text">{{ __('text.common_refund') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="white_line"></div>
                    <div class="verified_info_block">
                        <div class="verified_imgs" data-da=".discounts_info_block, 950, last">
                            <div>
                                <img src="{{ asset("$design/images/icons/verified.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @else alt="" @endif>
                            </div>
                            <div>
                                <img src="{{ asset("$design/images/icons/partners.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                    class="img_support_first">
                                <img src="{{ asset("$design/images/icons/partners_small.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                    class="img_support_second">
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
                        <img src="{{ asset("$design/images/icons/stars.svg") }}" width="98" height="18"
                            alt="">
                    </div>
                    <div class="block_num">
                        1 000 000
                    </div>
                    <div class="num_text">
                        {{ __('text.common_customers') }}
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
                            <img src="{{ asset("$design/images/icons/verified.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @else alt="" @endif>
                        </div>
                        <div>
                            <img src="{{ asset("$design/images/icons/partners_small.svg") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif
                                class="img_support_second">
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
                                <div class="discount_label">{{ __('text.common_save') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_discount') }}</div>
                        </div>
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-02.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_prescription') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_restrictions') }}</div>
                        </div>
                    </div>
                    <div class="discount_line">
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_delivery') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_receive') }}</div>
                        </div>
                        <div class="discount_block">
                            <div class="discount_top">
                                <div>
                                    <img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
                                </div>
                                <div class="discount_label">{{ __('text.common_moneyback') }}</div>
                            </div>
                            <div class="discount_text">{{ __('text.common_refund') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="pay-index">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#visa">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#mastercard">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#maestro">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#discover">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amex">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#jsb">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#unionpay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#dinners-club">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#apple-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#google-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amazon-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#stripe">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#paypal">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#sepa">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#cashapp">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#adyen">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#skrill">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#worldpay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#payline">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#bitcoin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#binance-coin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#ethereum">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#litecoin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#tron">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(erc20)">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(trc20)">
                        </svg>
                    </li>
                </ul>
            </div>
        </section>

        <div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
        </div>

        </header>

        @yield('content')

        <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#usps" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ems" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dhl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ups" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#fedex" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#tnt" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#postnl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#deutsche_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dpd" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#gls" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                        <use width="100%" height="100%" width="100%" href="/pub_images/shipping/sprite.svg#australia_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#colissimo" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#correos" preserveAspectRatio="xMinYMin">
                    </svg>
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

        @yield('testimonial', '')

        <footer class="footer">
            <div class="footer_container">
                <div class="footer_left">
                    <div>
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset("$design/images/logo_bottom.svg") }}" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset("$design/images/logo_bottom.svg") }}" alt="Logo">
                        @endif
                    </div>
                    <div class="footer_copyright">
                        <p>
                            {{ __('text.license_text_license1_1') }}
                            {{ $domain }}
                            {{ __('text.license_text_license1_2') }}
                            {{ __('text.license_text_license2_d10') }}
                        </p>
                    </div>
                </div>
                <div class="footer_right" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) style="flex-direction: column; gap: 20px;" @endif>
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div style="display: flex; gap: 25px;">
                            <ul class="footer__menu">
                                <li class="footer__item"><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                            <a href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}" class="footer__button">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @else
                        <div style="display: flex; gap: 25px;">
                            <ul class="footer__menu">
                                <li class="footer__item"><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li class="footer__item"><a
                                        href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                            <a href="{{ route('home.affiliate', '') }}" class="footer__button">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @endif
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="sitemap_menu">
                            <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                        </div>
                    @endif
                </div>
                <div class="footer_copyright bottom_license">
                    <p>
                        {{ __('text.license_text_license1_1') }}
                        {{ $domain }}
                        {{ __('text.license_text_license1_2') }}
                        {{ __('text.license_text_license2_d10') }}
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
                    <div class="fixed-bar__label">{{ __('text.common_home_main_menu_item') }}</div>
                </a>
            </div>
        </footer>
    </div>

    <script defer src="{{ asset("$design/js/app.js") }}"></script>
    <script defer src="{{ asset('/js/all_js.js') }}"></script>
    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif
</body>

</html>
