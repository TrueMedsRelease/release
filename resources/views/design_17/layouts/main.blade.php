<!DOCTYPE html>
<html class="webp no-js"
@if (session('locale', 'en') == 'arb')
    lang="ar"
@elseif (session('locale', 'en') == 'gr')
    lang="el"
@elseif (session('locale', 'en') == 'hans')
    lang="zh-Hans"
@elseif (session('locale', 'en') == 'hant')
    lang="zh-Hant"
@elseif (session('locale', 'en') == 'no')
    lang="nb"
@else
    lang="{{ session('locale', 'en') }}"
@endif
>
    <head>
        <meta charset="utf-8">
        <title>@yield('title', 'Title')</title>

        @if (env('APP_DEFAULT_META', 1))
            <meta name="Description" content="@yield('description', 'Description')">
            <meta name="Keywords" content="@yield('keywords', 'Keywords')">
        @else
            <meta name="Description" content="Description">
            <meta name="Keywords" content="Keywords">
        @endif

        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#14151a" />
        <link rel="canonical" href="{{ url()->current() }}">

        @php
            if (!function_exists('asset_ver')) {
                function asset_ver(string $path): string {
                    static $mtimes = [];
                    $full = public_path($path);
                    if (!isset($mtimes[$path])) {
                        $mtimes[$path] = is_file($full) ? filemtime($full) : null;
                    }
                    $url = asset($path);
                    $v = $mtimes[$path] ?? time();
                    return $url . '?v=' . $v;
                }
            }
        @endphp

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

        <link rel="icon" href="{{ asset($design . '/img/favicon/favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset($design . '/img/favicon/favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset($design . '/img/favicon/apple-touch-icon-180x180.png') }}">

        <script>
            const routeSavePush = "{{ route('home.save_push_data') }}";
            const routePwaInstallEvent = "{{ route('home.pwa_install_event') }}";
        </script>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if (env('APP_PWA', 0))
            <link rel="manifest" href="{{ asset($design . '/img/favicon/manifest.webmanifest') }}">
            <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
            {{-- <script defer type="text/javascript" src="{{ asset_ver("vendor/jquery/pwa.js") }}"></script> --}}
        @endif

        {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

        <link href="{{ asset($design . '/fonts/inter-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/inter-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/inter-semibold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/inter-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-semibold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

        <link href="{{ asset($design . '/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
        <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
        <link href="{{ asset_ver($design . '/css/style.css') }}" rel="stylesheet">
        <link href="{{ asset_ver($design . '/css/pages.css') }}" rel="stylesheet">

        <script>
            const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
            const routeCartContent = "{{ route('cart.content') }}";
        </script>

        <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
        <script defer src="{{ asset_ver('vendor/jquery/autocomplete.js') }}"></script>
        <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
        <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>

        <script defer src="{{ asset($design . '/vendor/floating-ui/core@1.6.9.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/floating-ui/dom@1.6.13.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/swiper/swiper-bundle.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"> </script>

        <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>
        {!! isset($pixel) ? $pixel : '' !!}
    </head>

    <body class="@yield('page_name')">
        <script>
            let flagc = false;
            let flagp = false;
            const design = 17;
        </script>

        @if (session('locale'))
            <input type="hidden" id="lang_session" value="{{ session('locale') }}">
        @endif

        @if (session('order'))
            <input type="hidden" id="order_info_session" value="{{ json_encode(session('order')) }}">
        @endif

        <input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
        <input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
        <input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">
        <input type="hidden" id="print_sprite" value="{{ env('APP_PRINT_SPRITE', 1) }}">
        <input type="hidden" id="country_iso" value="{{ $codes }}">
        <input type="hidden" id="initial_country" value="{{ strtolower(session('location.country')) }}">

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

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/new_year_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/valentine_day_big.png") }}">
        </div> --}}
        <div class="main-container">
            <header class="header" data-sticky="header" data-sticky-on-top>
                <div class="container">
                    <a class="logo header__logo" href="{{ route("home.index") }}">
                        <img src="{{ asset($design . '/svg/logo.svg') }}" width="160" height="30" alt="Site logo">
                    </a>
                    <button class="navbar-toggler" data-drawer-toggle="navbar" aria-label="Toggle Main Menu" aria-controls="main-nav" aria-expanded="false">
                        <span class="navbar-burger"></span>
                    </button>
                    <nav class="nav navbar drawer drawer--rtl" id="main-nav" data-drawer="navbar" aria-label="Main Menu">
                        <div class="nav-container">
                            <a class="navbar__auth-link link" href="{{ route("home.login") }}" target="_blank">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#user-round') }}"></use>
                                    </svg>
                                </span>
                                {{ __('text.common_profile') }}
                            </a>
                            <ul class="nav__list">
                                @if (count($Language::GetAllLanuages()) > 1)
                                    <li class="nav__item" data-dropdown data-dropdown-hover="toggler" data-dropdown-select>
                                        <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.language', session('locale', 'en')) }}" data-dropdown-button aria-expanded="false">
                                            <span class="button-text">{{ $Language::$languages_name[session('locale', 'en')] }}</span>
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="sublist-container sublist-container--level-1" data-dropdown-container>
                                            <div class="sublist-panel">
                                                <ul class="nav__sublist">
                                                    @foreach ($Language::GetAllLanuages() as $item)
                                                        <li class="nav__item" data-dropdown-hover="toggler">
                                                            <a class="nav__link" href="{{ route('home.language', $item['code']) }}" @if (App::currentLocale() == $item['code']) data-dropdown-select-item="selected" @endif>
                                                                <span class="button-text">{{ $item['name'] }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                @if (count($Currency::GetAllCurrency()) > 1)
                                    <li class="nav__item" data-dropdown data-dropdown-hover="toggler" data-dropdown-select>
                                        <a class="nav__link currency-toggler sublist-toggler sublist-toggler--level-1" href="#!" data-dropdown-button aria-expanded="false">
                                            <span class="button-text">{{ $Currency::GetAllCurrency()[0]['code'] }}</span>
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                                                </svg>
                                            </span>
                                        </a>
                                        <div class="sublist-container sublist-container--level-1" data-dropdown-container>
                                            <div class="sublist-panel">
                                                <ul class="nav__sublist">
                                                    @foreach ($Currency::GetAllCurrency() as $item)
                                                        <li class="nav__item" data-dropdown-hover="toggler">
                                                            <a class="nav__link" href="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) data-dropdown-select-item="selected" @endif>
                                                                <span class="button-text">{{ strtoupper($item['code']) }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                <li class="nav__item" data-dropdown data-dropdown-hover="toggler">
                                    <a class="nav__link sublist-toggler sublist-toggler--level-1" href="tel:{{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}" data-dropdown-button data-dropdown-split data-flag="{{ strtolower(rtrim(trim(__('text.phones_title_phone_' . array_key_first($phone_arr) . '_code')), ':')) }}" aria-expanded="false">
                                        <span class="button-text">{{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}</span>
                                        <span class="icon">
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                                            </svg>
                                        </span>
                                    </a>
                                    <div class="sublist-container sublist-container--level-1" data-dropdown-container>
                                        <div class="sublist-panel">
                                            <ul class="nav__sublist">
                                                @foreach ($phone_arr as $id_phone => $phones)
                                                    <li class="nav__item" data-dropdown-hover="toggler">
                                                        <a class="nav__link" href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}" data-dropdown-split data-flag="{{ strtolower(rtrim(trim(__('text.phones_title_phone_' . $id_phone . '_code')), ':')) }}">
                                                            <span class="button-text">{{ __('text.phones_title_phone_' . $id_phone) }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="navbar__controls">
                                <button class="navbar__callback-button button" data-dialog-open="call">{{ __('text.common_callback') }}</button>
                            </div>
                        </div>
                    </nav>
                </div>
            </header>

            <div class="header-placeholder"></div>

            <div class="page-wrapper container">
                <main class="main">
                    <div class="thread">

                        @yield('content')

                        <div class="thread-box search search-bar">
                            <form class="search-form form" action="{{ route('search.search_product') }}" method="post" style="width: 100%;">
                                @csrf
                                <label class="thread-box__label textarea-field">
                                    {{-- <textarea class="thread-box__input input-textarea ac_input input-text" rows="1" id="autocomplete" placeholder="Enter a drug name" name="search_text"></textarea> --}}
                                    <input class="search-form__input form__text-input input-text ac_input" id="autocomplete" type="text" placeholder="{{ __('text.common_search') }}" name="search_text" required>
                                    {{-- <span class="thread-box__placeholder">{{ __('text.common_search') }}</span> --}}
                                </label>
                                <button class="thread-box__submit button search-form__button" aria-label="Thred box submit">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-up') }}"></use>
                                        </svg>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </main>
            </div>

            <aside class="cart drawer drawer--ltr" data-drawer="cart">
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
                <div class="cart__header">
                    <div class="cart__hgroup">
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#cart') }}"></use>
                            </svg>
                        </span>
                        <h2 class="cart__title">
                            <a href="{{ route('cart.index') }}">{{ __('text.common_cart_text_d2') }}</a>
                        </h2>
                        <div class="cart__counter">{{ $cart_count }}</div>
                        <div class="cart__total-price ellipsis">{{ $Currency::Convert($cart_total, true) }}</div>
                    </div>
                    <button class="cart__close-button navbar-toggler" data-drawer-close="cart" aria-label="Close cart">
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#close') }}"></use>
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="cart__body">
                    <div class="cart-items">
                        @foreach (session('cart', []) as $product)
                            <article class="cart-item">
                                <a class="cart-item__cover" href="{{ route('home.product', $product['product_info']['url']) }}">
                                    @if ($product['product_id'] == 616)
                                        <picture>
                                            <source type="image/webp"
                                            srcset="{{ asset("$design/img/products/gift-product-175w.webp") }} 1x, {{ asset("$design/img/products/gift-product-350w.webp 2x") }}"><img
                                            src="{{ asset("$design/img/products/gift-product-175w.jpg") }}"
                                            srcset="{{ asset("$design/img/products/gift-product-175w.jpg") }} 1x, {{ asset("$design/img/products/gift-product-350w.jpg 2x") }}" width="200"
                                            height="200" alt="Gift">
                                        </picture>
                                    @else
                                        <picture>
                                            <source srcset="{{ route('home.set_images', $product['product_info']['image']) }}" type="image/webp">
                                            <img loading="lazy" src="{{ route('home.set_images', $product['product_info']['image']) }}" alt="{{ $product['product_info']['alt'] }}">
                                        </picture>
                                    @endif
                                </a>
                                <div class="cart-item__content">
                                    <h3 class="cart-item__title truncate-box">
                                        <a href="{{ route('home.product', $product['product_info']['url']) }}">{{ $product['product_info']['name'] }}</a>
                                    </h3>
                                    <p class="cart-item__description truncate-box">{{ $product['dosage'] }} x {{ $product['num'] }}</p>
                                </div>
                                <div class="cart-item__price">{{ $Currency::convert($product['q'] * $product['price'], true) }}</div>
                                <button class="cart-item__remove-button" onclick="remove({{ $product['pack_id'] }})">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#trash-round') }}"></use>
                                        </svg>
                                    </span>
                                </button>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="cart__footer">
                    <a class="cart__checkout-button button" href="{{ route('checkout.index') }}">
                        {{ __('text.cart_checkout_text') }}
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-right') }}"></use>
                            </svg>
                        </span>
                    </a>
                </div>
            </aside>
            <footer class="footer">
                <div class="sup-footer container">
                    <div class="footer-testimonials">
                        <div class="swiper footer-testimonials__swiper">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="testimonial">
                                        <div class="testimonial__header">
                                            <div class="testimonial__author">
                                                {!! __('text.testimonials_author_t_1') !!}
                                            </div>
                                            <div class="testimonial__rating">
                                                <div class="rating">
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="testimonial__text">
                                            {{ __('text.testimonials_t_1') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="testimonial">
                                        <div class="testimonial__header">
                                            <div class="testimonial__author">
                                                {!! __('text.testimonials_author_t_7') !!}
                                            </div>
                                            <div class="testimonial__rating">
                                                <div class="rating">
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="testimonial__text">
                                            {{ __('text.testimonials_t_7') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="testimonial">
                                        <div class="testimonial__header">
                                            <div class="testimonial__author">
                                                {!! __('text.testimonials_author_t_13') !!}
                                            </div>
                                            <div class="testimonial__rating">
                                                <div class="rating">
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="testimonial__text">
                                            {{ __('text.testimonials_t_13') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="testimonial">
                                        <div class="testimonial__header">
                                            <div class="testimonial__author">
                                                {!! __('text.testimonials_author_t_17') !!}
                                            </div>
                                            <div class="testimonial__rating">
                                                <div class="rating">
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                    <div class="rating__star"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="testimonial__text">
                                            {{ __('text.testimonials_t_17') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer__container container">
                    <div class="footer__wrapper">
                        <div class="footer__top">
                            <a class="logo header__logo" href="{{ route("home.index") }}">
                                <img src="{{ asset($design . '/svg/logo-dim.svg') }}" width="160" height="30" alt="Site logo">
                            </a>

                            <a class="footer__affiliate-button button button--sm" href="{{ route("home.affiliate") }}">{{ __('text.common_affiliate_main_menu_button') }}</a>

                            <nav class="nav footer-nav">
                                <div class="nav-container">
                                    <ul class="nav__list">
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.about") }}">
                                                <span class="button-text">{{ __('text.common_about_us_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.help") }}">
                                                <span class="button-text">{{ __('text.common_help_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.testimonials") }}">
                                                <span class="button-text">{{ __('text.common_testimonials_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.delivery") }}">
                                                <span class="button-text">{{ __('text.common_shipping_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.moneyback") }}">
                                                <span class="button-text">{{ __('text.common_moneyback_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.contact_us") }}">
                                                <span class="button-text">{{ __('text.common_contact_us_main_menu_item') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route("home.bonus_referral_program") }}">
                                                <span class="button-text">{{ __('text.bonus_ref_menu') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <div class="footer__bottom">
                            <div class="footer__copyrights">
                                {{ __('text.license_text_license1_1') }}
                                <br>
                                {{ $domain }}
                                {{ __('text.license_text_license1_2') }}
                                {{ __('text.license_text_license2_d13') }}
                            </div>
                            <div class="subscribe">
                                <div class="subscribe__header">
                                    <span class="icon subscribe__icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#subscribe') }}"></use>
                                        </svg>
                                    </span>
                                    <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                                    <div class="subscribe__caption">{{ __('text.subscribe_full_text') }}</div>
                                </div>
                                <form class="subscribe-form form">
                                    <label class="form__label subscribe-form__label">
                                        <input class="form__text-input input-email subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                                    </label>
                                    <button class="subscribe-form__button button button_sub" type="button">{{ __('text.common_subscribe') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="footer-buttons">
                        <div class="footer-buttons__container">
                            <button class="button button--tapbar footer-buttons__cart" data-drawer-toggle="cart" data-counter="{{ $cart_count }}">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#cart') }}"></use>
                                    </svg>
                                </span>
                                <span class="button__text">{{ __('text.common_cart_text_d2') }}</span>
                                <span class="button__price ellipsis">{{ $Currency::Convert($cart_total, true) }}</span>
                            </button>
                            <a class="button button--tapbar" href="{{ route("home.login") }}" target="_blank">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#user-round') }}"></use>
                                    </svg>
                                </span>
                                <span class="button__text">{{ __('text.common_profile') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <dialog class="dialog-container" data-dialog="call" data-modal>
            <div class="dialog">
                <header class="dialog__header">
                    <div class="dialog__title">{{ __('text.common_callback') }}</div>
                </header>
                <form class="form callback-form" method="dialog">
                    <div class="form__field text-field">
                        <label class="form__label label-tel">
                            <input class="form__text-input input-tel intl-phone" type="tel" id="callback-phone" name="callback-phone" placeholder="000 000 00 00" required>
                        </label>
                    </div>
                    <div class="form__field submit-field">
                        <input class="button form__submit button--dialog button_request_call" type="button" value="{{ __('text.common_callback') }}">
                    </div>
                </form>
                <button class="dialog__close-button link" data-dialog-close="call" aria-label="Close dialog">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#close') }}"></use>
                        </svg>
                    </span>
                </button>
                <div class="message_sended hidden">
                    <div style="text-align: center">
                        <h2>{{ __('text.contact_us_thanks') }}</h2>
                        <br>
                        <p>{{ __('text.phone_request_mes_text') }}</p>
                    </div>
                </div>
            </div>
        </dialog>

        {{-- <dialog class="dialog-container" data-dialog="call-push" data-modal>
            <div class="dialog">
                <header class="dialog__header">
                    <div class="dialog__title">Subscribing to&nbsp;a&nbsp;notification</div>
                    <div class="dialog__note">Allow the site mysite.com send you a notification&nbsp;to&nbsp;your&nbsp;desktop</div>
                </header>
                <form class="form callback-push-form" method="dialog">
                    <div class="form__field text-field">
                        <label class="form__label label-tel">
                            <input class="form__text-input input-tel intl-phone" type="tel" id="callback-push-push-phone" name="callback-push-push-phone" placeholder="000 000 00 00" required>
                        </label>
                    </div>
                    <div class="form__field custom-field callback-push-submit">
                        <button class="button button--secondary button--dialog" type="button">Decline</button>
                        <button class="button button--dialog" type="submit">Allow</button>
                    </div>
                </form>
                <button class="dialog__close-button link" data-dialog-close="call-push" aria-label="Close dialog">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="svg/icons/sprite.svg?vmxkaego#close"></use>
                        </svg>
                    </span>
                </button>
            </div>
        </dialog> --}}

        <div class="popup_white hide">
            <div class="popup_push">
                <div class="button_close">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                        </svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                            <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    @endif
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

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close_message">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg class="close_popup" width="15" height="15">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="close_popup" width="15" height="15">
                            <path d="M15.59 2.39A1.4 1.4 0 1 0 13.61.41L8 6.02 2.39.41A1.4 1.4 0 1 0 .41 2.39L6.02 8 .41 13.61a1.4 1.4 0 0 0 1.98 1.98L8 9.98l5.61 5.61a1.4 1.4 0 1 0 1.98-1.98L9.98 8l5.61-5.61Z"/>
                        </svg>
                    @endif
                </div>
                <div class="message_sended">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>

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

            const routeCart = "{{ route('cart.index') }}";
            const routePWAInfo = "{{ route('home.pwa_info') }}";

            const pathImageCheckupBiggest = "{{ asset('pub_images/checkup_img/white/checkup_biggest.png') }}";
            const pathImageCheckupBig = "{{ asset('pub_images/checkup_img/white/checkup_big.png') }}";
            const pathImageCheckupMiddle = "{{ asset('pub_images/checkup_img/white/checkup_middle.png') }}";
            const pathImageCheckupSmall = "{{ asset('pub_images/checkup_img/white/checkup_small.png') }}";

            const pathImagePayBiggest = "{{ asset('pub_images/pay_biggest.png') }}";
            const pathImagePayBig = "{{ asset('pub_images/pay_big.png') }}";
            const pathImagePayMiddle = "{{ asset('pub_images/pay_middle.png') }}";
            const pathImagePaySmall = "{{ asset('pub_images/pay_small.png') }}";

            const pathImageBlackFridayBiggest = "{{ asset('pub_images/black_friday_biggest.png') }}";
            const pathImageBlackFridayBig = "{{ asset('pub_images/black_friday_big.png') }}";
            const pathImageBlackFridayMiddle = "{{ asset('pub_images/black_friday_middle.png') }}";
            const pathImageBlackFridaySmall = "{{ asset('pub_images/black_friday_small.png') }}";

            const pathImageChristmasBiggest = "{{ asset('pub_images/christmas_biggest.png') }}";
            const pathImageChristmasBig = "{{ asset('pub_images/christmas_big.png') }}";
            const pathImageChristmasMiddle = "{{ asset('pub_images/christmas_middle.png') }}";
            const pathImageChristmasSmall = "{{ asset('pub_images/christmas_small.png') }}";

            const pathImageNewYearBiggest = "{{ asset('pub_images/new_year_biggest.png') }}";
            const pathImageNewYearBig = "{{ asset('pub_images/new_year_big.png') }}";
            const pathImageNewYearMiddle = "{{ asset('pub_images/new_year_middle.png') }}";
            const pathImageNewYearSmall = "{{ asset('pub_images/new_year_small.png') }}";

            const pathImageValentineDayBiggest = "{{ asset('pub_images/valentine_day_biggest.png') }}";
            const pathImageValentineDayBig = "{{ asset('pub_images/valentine_day_big.png') }}";
            const pathImageValentineDayMiddle = "{{ asset('pub_images/valentine_day_middle.png') }}";
            const pathImageValentineDaySmall = "{{ asset('pub_images/valentine_day_small.png') }}";

            const pathImageDownloadStoreBiggest = "{{ asset('pub_images/download_banners/white/download_banner_biggest.png') }}";
            const pathImageDownloadStoreBig = "{{ asset('pub_images/download_banners/white/download_banner_big.png') }}";
            const pathImageDownloadStoreMiddle = "{{ asset('pub_images/download_banners/white/download_banner_middle.png') }}";
            const pathImageDownloadStoreSmall = "{{ asset('pub_images/download_banners/white/download_banner_small.png') }}";
        </script>

        <script defer src="{{ asset_ver("$design/js/main.5b1e354c.js") }}"></script>
        <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
        <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>