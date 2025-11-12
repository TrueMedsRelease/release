<!DOCTYPE html>
<html
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
        <meta name="description" content="@yield('description', 'Description')">
        <meta name="keywords" content="@yield('keywords', 'Keywords')">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#14151a" />

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

        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

        @if (env('APP_PWA', 0))
            <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
            <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
        @endif

        {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

        {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

        <link href="{{ asset($design . '/fonts/rubik-variable.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

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
        <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"></script>
        {{-- <script defer src="{{ asset($design . '/vendor/just-validate.min.js') }}"> </script> --}}
        {!! isset($pixel) ? $pixel : '' !!}
    </head>

    <body class="@yield('page_name')">
        <script>
            let flagc = false;
            let flagp = false;
            const design = 13;
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
        </div> --}}

        <header class="header">
            <div class="topbar">
                <div class="container">
                    <button class="link link--white topbar-categories-button" data-cat-nav-opener>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                            </svg>
                        </span>
                    </button>

                    <nav class="nav cat-nav">
                        <div class="nav-container">
                            <div class="nav__heading">{{ __('text.common_categories_menu') }}</div>
                            <button class="nav__close-button" aria-label="Close categories"></button>
                            <div class="mobile-cat-nav">
                                <div class="accordion aside-nav" data-accordion>
                                    <details class="accordion-item" data-accordion-item>
                                        <summary class="accordion-button" data-accordion-button>
                                            <span class="button-text">{{ __('text.common_best_selling_title') }}</span>
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                </svg>
                                            </span>
                                        </summary>
                                        <div class="accordion-panel" data-accordion-panel>
                                            <div class="accordion-content content">
                                                <ul class="aside-nav__list">
                                                    @foreach ($bestsellers as $bestseller)
                                                        <li class="aside-nav__item">
                                                            <a class="aside-nav__link" href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }} <span class="aside-nav__price">{{ $Currency::convert($bestseller['price'], false, true) }}</span></a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </details>
                                    @foreach ($menu as $category)
                                        <details class="accordion-item" data-accordion-item>
                                            <summary class="accordion-button" data-accordion-button>
                                                <span class="button-text">{{ $category['name'] }}</span>
                                                <span class="icon">
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                    </svg>
                                                </span>
                                            </summary>
                                            <div class="accordion-panel" data-accordion-panel>
                                                <div class="accordion-content content">
                                                    <ul class="aside-nav__list">
                                                        @foreach ($category['products'] as $item)
                                                            <li class="aside-nav__item">
                                                                <a class="aside-nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }} <span class="aside-nav__price">{{ $Currency::convert($item['price'], false, true) }}</span></a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </details>
                                    @endforeach
                                </div>
                            </div>
                            <ul class="nav__list">
                                <li class="nav__item">
                                    <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                        <span class="icon">
                                            <svg width="1em" height="1em"  fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                            </svg>
                                        </span>
                                    </a>
                                </li>
                                @foreach ($menu as $category)
                                    <li class="nav__item">
                                        <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                </svg>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="categories-sublists">
                                <ul class="nav__sublist categories-sublist" data-sublist-index="0">
                                    <li class="nav__item nav__item--return">
                                        <button class="nav__mobile-return">
                                            <span class="icon">
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#fi-rr-angle-small-down") }}"></use>
                                                </svg>
                                            </span>
                                            {{ __('text.common_best_selling_title') }}
                                        </button>
                                    </li>
                                    @foreach ($bestsellers as $bestseller)
                                        <li class="nav__item">
                                            <a class="nav__link" href="{{ route('home.product', $bestseller['url']) }}">
                                                {{ $bestseller['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                @foreach ($menu as $category)
                                    <ul class="nav__sublist categories-sublist" data-sublist-index="{{ $loop->iteration }}">
                                        <li class="nav__item nav__item--return">
                                            <button class="nav__mobile-return">
                                                <span class="icon">
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#fi-rr-angle-small-down") }}"></use>
                                                    </svg>
                                                </span>
                                                {{ $category['name'] }}
                                            </button>
                                        </li>
                                        @foreach ($category['products'] as $item)
                                            <li class="nav__item">
                                                <a class="nav__link" href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </nav>

                    <nav class="nav navbar drawer drawer--rtl" id="main-nav" data-drawer="navbar" aria-label="Main Menu">
                        <div class="nav-container greedy-nav">
                            <ul class="nav__list greedy-items">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @else
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @endif
                            </ul>
                            <button class="navbar-close-button" data-drawer-toggle="navbar" aria-label="Close Main Menu">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown" data-dropdown data-fixed-dropdown>
                                <button class="link greedy-button" data-dropdown-button aria-label="Show dropdown">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#fi-sr-menu-dots-vertical") }}"></use>
                                        </svg>
                                    </span>
                                    <span class="icon is-hidden">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                        </svg>
                                    </span>
                                </button>
                                <div class="dropdown-container navbar-greedy-items-content" data-dropdown-container>
                                    <div class="dropdown-content greedy-hidden-items"></div>
                                </div>
                            </div>
                        </div>
                    </nav>

                    <div class="header-phones">
                        <div class="header-phones__request">
                            <button class="link link--primary" data-dialog-open="call">{{ __('text.common_callback') }}</button><span>&nbsp;{{ __('text.common_call_us_top') }}</span>
                        </div>
                        <a class="link link--white" href="tel:{{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}">{{ __('text.phones_title_phone_' . array_key_first($phone_arr) . '_code') }} {{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}</a>
                        <div class="dropdown header-phones__dropdown" data-dropdown data-dropdown-select>
                            <button class="dropdown-button link dropdown-button--select link--white" data-dropdown-button aria-expanded="false" aria-label="Call us dropdown" type="button">
                                <span class="button-text">&nbsp;{{ __('text.common_call_us_top') }}</span>
                                <span class="icon dropdown-button__arrow">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                    </svg>
                                </span>
                            </button>
                            <div class="dropdown-container" data-dropdown-container>
                                <div class="dropdown-content">
                                    @foreach ($phone_arr as $id_phone => $phones)
                                        <a class="link" href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}">{{ __('text.phones_title_phone_' . $id_phone . '_code') }} {{ __('text.phones_title_phone_' . $id_phone) }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="header-settings">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="header-lang header-select-wrapper">
                                <select class="header-select" name="lang-select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" data-flag="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                                <span class="icon header-select-wrapper__chevron">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                    </svg>
                                </span>
                            </div>
                        @endif
                        @if (count($Currency::GetAllCurrency()) > 1)
                            <div class="header-currency header-select-wrapper">
                                <select class="header-select" name="currency-select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif>{{ Str::upper($item['code']) }}</option>
                                    @endforeach
                                </select>
                                <span class="icon header-select-wrapper__chevron">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                    </svg>
                                </span>
                            </div>
                        @endif
                        <a class="header-auth link link--white" href="{{ route('home.login') }}" target="_blank">{{ __('text.common_profile') }}</a>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="header__wrapper">
                    <a class="header__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
                        @endif
                    </a>
                    <button class="navbar-toggler" data-drawer-toggle="navbar" aria-label="Toggle Main Menu" aria-controls="main-nav" aria-expanded="false">
                        <span class="navbar-burger"></span>
                    </button>
                </div>
                <div class="header-search-wrapper">
                    <div class="dropdown index-dropdown" data-dropdown>
                        <button class="dropdown-button" data-dropdown-button aria-expanded="false" aria-label="Drug index">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#candy-box") }}"></use>
                                </svg>
                            </span>
                            <span class="icon is-hidden">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                </svg>
                            </span>
                        </button>
                        <div class="dropdown-container" data-dropdown-container>
                            <div class="dropdown-content">
                                <span class="drug-index-caption">{{ __('text.common_first_letter') }}:</span>
                                <ul class="drug-index">
                                    @foreach ($first_letters as $key => $active_letter)
                                        <li class="drug-index__item">
                                            @if ($active_letter)
                                                <div class="drug-index__link">
                                                    <a href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                                                </div>
                                            @else
                                                <div class="drug-index__link">
                                                    {{ $key }}
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form class="search-form form" action="{{ route('search.search_product') }}" method="post">
                        @csrf
                        <div class="search search-bar" style="width: 100%; display: flex;">
                            <label class="search-form__label">
                                <input class="search-form__input form__text-input input-text ac_input" id="autocomplete" type="text" placeholder="{{ __('text.common_search') }}" name="search_text" required>
                            </label>
                            <button class="button search-form__button" aria-label="Search">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#search") }}"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
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

                <a class="cart-button button" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                        </svg>
                    </span>
                    <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                    <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                </a>
            </div>

            <div class="container checkup" onclick="location.href='{{ route('home.checkup') }}'">
                <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
            </div>
        </header>

        @yield('content')

        @yield('rewies')

        <footer class="footer">
            <div class="container">
                <div class="footer__wrapper">
                    <div class="footer-nav-wrapper">
                        <div class="footer-nav-title h4">True Pharmacy</div>
                        <nav class="nav footer-nav">
                            <div class="nav-container">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <ul class="nav__list">
                                        <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a></li>

                                    </ul>
                                @else
                                    <ul class="nav__list">
                                        <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a></li>
                                    </ul>
                                @endif
                            </div>
                             @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <div class="sitemap_menu">
                                    <a class="nav__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                                </div>
                            @endif
                        </nav>
                    </div>

                    <div class="footer-brands">
                        <div class="footer-brands__title h4">{{ __('text.common_verified') }}</div>
                        <div class="footer-brands__wrapper">
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-1-48w.webp") }} 1x, {{ asset("$design/img/brands/brand-1-97w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/brands/brand-1-48w.png") }}" srcset="{{ asset("$design/img/brands/brand-1-48w.png") }} 1x, {{ asset("$design/img/brands/brand-1-97w.png 2x") }}" width="49" height="32" alt="Brand">
                                </picture>
                            </div>
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-2-46w.webp") }} 1x, {{ asset("$design/img/brands/brand-2-93w.webp 2x") }}">
                                    <img  src="{{ asset("$design/img/brands/brand-2-46w.png") }}" srcset="{{ asset("$design/img/brands/brand-2-46w.png") }} 1x, {{ asset("$design/img/brands/brand-2-93w.png 2x") }}" width="47" height="36" alt="Brand">
                                </picture>
                            </div>
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-3-57w.webp") }} 1x, {{ asset("$design/img/brands/brand-3-115w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/brands/brand-3-57w.png") }}" srcset="{{ asset("$design/img/brands/brand-3-57w.png") }} 1x, {{ asset("$design/img/brands/brand-3-115w.png 2x") }}" width="58" height="38" alt="Brand">
                                </picture>
                            </div>
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-4-38w.webp") }} 1x, {{ asset("$design/img/brands/brand-4-77w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/brands/brand-4-38w.png") }}" srcset="{{ asset("$design/img/brands/brand-4-38w.png") }} 1x, {{ asset("$design/img/brands/brand-4-77w.png 2x") }}" width="39" height="40" alt="Brand">
                                </picture>
                            </div>
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-5-56w.webp") }} 1x, {{ asset("$design/img/brands/brand-5-113w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/brands/brand-5-56w.png") }}" srcset="{{ asset("$design/img/brands/brand-5-56w.png") }} 1x, {{ asset("$design/img/brands/brand-5-113w.png 2x") }}" width="57" height="32" alt="Brand">
                                </picture>
                            </div>
                            <div class="footer-brand">
                                <picture>
                                    <source type="image/webp" srcset="{{ asset("$design/img/brands/brand-6-72w.webp") }} 1x, {{ asset("$design/img/brands/brand-6-145w.webp 2x") }}">
                                    <img src="{{ asset("$design/img/brands/brand-6-72w.png") }}" srcset="{{ asset("$design/img/brands/brand-6-72w.png") }} 1x, {{ asset("$design/img/brands/brand-6-145w.png 2x") }}" width="73" height="30" alt="Brand">
                                </picture>
                            </div>
                        </div>
                    </div>
                    <div class="subscribe">
                        <div class="subscribe__title h4">{{ __('text.subscribe_full_text') }}</div>
                        <form class="subscribe-form form">
                            <label class="form__label subscribe-form__label">
                                <input class="form__text-input input-email subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                            </label>
                            <button class="subscribe-form__button button button_sub" type="button">{{ __('text.common_subscribe') }}</button>
                        </form>
                    </div>
                </div>
                <div class="footer__copyrights">
                    {{ __('text.license_text_license1_1') }}
                    {{ $domain }}
                    {{ __('text.license_text_license1_2') }}
                    {{ __('text.license_text_license2_d13') }}
                </div>
                <div class="footer-buttons">
                    <div class="footer-buttons__container">
                        <a class="footer-button" href="{{ route('home.index') }}">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#home") }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                        </a>
                        <button class="footer-button footer-button--cat" data-cat-nav-opener>
                            <span class="icon button__category-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#category") }}"></use>
                                </svg>
                            </span>
                            <span class="icon button__close-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#user") }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ __('text.common_profile') }}</span>
                        </a>
                        <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                                </svg>
                            </span>
                            <span class="button__text">{{ $Currency::Convert($cart_total, true) }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <div class="cat-overlay overlay"></div>

        <dialog class="dialog-container" data-dialog="call" data-modal closedby="none">
            <div class="dialog">
                <header class="dialog__header">
                    <div class="dialog__title">{{ __('text.common_callback') }}</div>
                </header>
                <form class="form callback-form" method="dialog">
                    <div class="form__field text-field">
                        <input class="form__text-input input-tel intl-phone" type="tel" id="callback-phone" name="callback-phone" placeholder="000 000 00 00" required>
                        {{-- <label class="form__label label-tel" for="callback-phone">Mobile phone:</label> --}}
                    </div>
                    <div class="form__field submit-field">
                        <input class="button form__submit button--secondary button--dialog button_request_call" type="button" value="{{ __('text.common_callback') }}">
                    </div>
                </form>
                <button class="dialog__close-button" data-dialog-close="call" aria-label="Close dialog">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
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

        {{-- <dialog class="dialog-container" data-dialog="call-push" data-modal closedby="none">
            <div class="dialog">
                <header class="dialog__header">
                    <div class="dialog__title">Subscribing to&nbsp;a&nbsp;notification</div>
                    <div class="dialog__note">
                        Allow the site mysite.com send you a notification&nbsp;to&nbsp;your&nbsp;desktop
                    </div>
                </header>
                <form class="form callback-push-form" method="dialog">
                    <div class="form__field text-field">
                        <input class="form__text-input input-tel intl-phone" type="tel" id="callback-push-push-phone" name="callback-push-push-phone" placeholder="000 000 00 00" required>
                        <label class="form__label label-tel" for="callback-push-push-phone">Mobile phone:</label>
                    </div>
                    <div class="form__field custom-field callback-push-submit">
                        <button class="button button--outlined button--dialog" type="button">Decline</button>
                        <button class="button button--secondary button--dialog" type="submit">Allow</button>
                    </div>
                </form>
                <button class="dialog__close-button" data-dialog-close="call-push" aria-label="Close dialog">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                        </svg>
                    </span>
                </button>
            </div>
        </dialog> --}}

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

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close_message">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                    </svg>
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

        <script defer src="{{ asset_ver("$design/js/main.7dfb0a3d.js") }}"></script>
        <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
        <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>