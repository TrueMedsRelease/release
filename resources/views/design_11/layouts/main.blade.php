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
    <meta name="robots" content="index, follow" />

    @if (env('APP_DEFAULT_META', 1))
        <meta name="Description" content="@yield('description', 'Description')">
        <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    @else
        <meta name="Description" content="Description">
        <meta name="Keywords" content="Keywords">
    @endif

    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#616ede" />
    <meta name="format-detection" content="telephone=no">
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

    {{-- <link rel="icon" href="//{{ request()->headers->get('host') }}/design_11/images/favicon/favicon.ico" sizes="any"> --}}

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

    <link href="{{ asset($design . '/fonts/neue-machina-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="{{ asset($design . '/fonts/neue-machina-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="{{ asset($design . '/fonts/neue-machina-ultrabold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
    <link href="{{ asset_ver($design . '/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/pages.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset_ver('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>

    <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
    <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"></script>
    <script defer src="{{ asset($design . '/vendor/just-validate.min.js') }}"></script>
    <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>

    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body class="webp @yield('page_name')">
    <script>
        let flagc = false;
        let flagp = false;
        const design = 11;
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

    <div class="topbar">
        <div class="container">
            <div class="header-phones drag-nav">
                <div class="drag-nav-container">
                    <div class="request-callback">
                        <button class="link" data-dialog="call">{{ __('text.common_callback') }}</button>
                        <span>&nbsp;{{ __('text.common_call_us_top') }}</span>
                    </div>
                    @foreach ($phone_arr as $id_phone => $phones)
                        <a href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}">{{ __('text.phones_title_phone_' . $id_phone . '_code') }} {{ __('text.phones_title_phone_' . $id_phone) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <header class="header @yield('header_class')">
        <div class="container">
                <div class="header-controls">
                    <div class="header-controls__nav-row">
                        <button class="button button--secondary categories-button">
                            <span class="icon button__fries-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#menu-fries') }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M5 5.25C4.58579 5.25 4.25 5.58579 4.25 6C4.25 6.41421 4.58579 6.75 5 6.75H19C19.4142 6.75 19.75 6.41421 19.75 6C19.75 5.58579 19.4142 5.25 19 5.25H5Z" fill="currentColor"/>
                                        <path d="M5 11.25C4.58579 11.25 4.25 11.5858 4.25 12C4.25 12.4142 4.58579 12.75 5 12.75H11C11.4142 12.75 11.75 12.4142 11.75 12C11.75 11.5858 11.4142 11.25 11 11.25H5Z" fill="currentColor"/>
                                        <path d="M5 17.25C4.58579 17.25 4.25 17.5858 4.25 18C4.25 18.4142 4.58579 18.75 5 18.75H19C19.4142 18.75 19.75 18.4142 19.75 18C19.75 17.5858 19.4142 17.25 19 17.25H5Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon button__close-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close-thin') }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <div class="header-nav greedy-nav">

                            <div class="greedy-items">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    @php
                                        $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                                    @endphp
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @else
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @endif
                            </div>
                            <div class="dropdown" data-fixed-dropdown>
                                <button class="dropdown-toggler link greedy-button" aria-label="Show dropdown">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-sr-menu-dots-vertical') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 18" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M2.00002 3.00002C2.82845 3.00002 3.50003 2.32845 3.50003 1.50001C3.50003 0.671578 2.82845 0 2.00002 0C1.17158 0 0.5 0.671578 0.5 1.50001C0.5 2.32845 1.17158 3.00002 2.00002 3.00002Z" fill="currentColor"/>
                                                <path d="M2.00002 10.5C2.82845 10.5 3.50003 9.82845 3.50003 9.00002C3.50003 8.17158 2.82845 7.5 2.00002 7.5C1.17158 7.5 0.5 8.17158 0.5 9.00002C0.5 9.82845 1.17158 10.5 2.00002 10.5Z" fill="currentColor"/>
                                                <path d="M2.00002 18C2.82845 18 3.50003 17.3285 3.50003 16.5C3.50003 15.6716 2.82845 15 2.00002 15C1.17158 15 0.5 15.6716 0.5 16.5C0.5 17.3285 1.17158 18 2.00002 18Z" fill="currentColor"/>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="icon is-hidden">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M6 6L18.7742 18.7742" stroke="#2A464D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 18.7742L18.7742 5.99998" stroke="#2A464D" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @endif
                                    </span>
                                </button>
                            <div class="dropdown-container">
                                <div class="dropdown-list greedy-hidden-items"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header-settings">
                    @if (count($Language::GetAllLanuages()) > 1)
                        <div class="header-lang header-select-wrapper">
                            <select class="header-select" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                                @foreach ($Language::GetAllLanuages() as $item)
                                    <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif>{{ $item['name'] }}</option>
                                @endforeach
                            </select>
                            <span class="icon shadow-icon header-select-wrapper__icon">
                                <img src="{{ asset($design . '/images/icons/planet.svg') }}" class="inline-svg">
                            </span>
                            <span class="icon header-select-wrapper__chevron">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                        </div>
                    @endif
                    @if (count($Currency::GetAllCurrency()) > 1)
                        <div class="header-currency header-select-wrapper">
                            <select class="header-select" id="curr_select" onchange="location.href=this.options[this.selectedIndex].value">
                                @foreach ($Currency::GetAllCurrency() as $item)
                                    <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif>{{ Str::upper($item['code']) }}</option>
                                @endforeach
                            </select>
                            <span class="icon header-select-wrapper__icon">
                                <img src="{{ asset($design . '/images/icons/wallet.svg') }}" class="inline-svg">
                            </span>
                            <span class="icon header-select-wrapper__chevron">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                        </div>
                    @endif
                    <a class="header-auth header-nav-link" href="{{ route('home.login') }}" target="_blank">
                        <span class="icon shadow-icon">
                            <img src="{{ asset($design . '/images/icons/profile.svg') }}" class="inline-svg">
                        </span>
                        {{ __('text.common_profile') }}
                    </a>
                </div>
                <a class="header__logo" href="{{ route('home.index') }}">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <img src="{{ asset($design . '/svg/logo.svg') }}" width="214" height="52" alt="{{ $domainWithoutZone }}">
                    @else
                        <img src="{{ asset($design . '/svg/logo.svg') }}" width="214" height="52" alt="Logo">
                    @endif
                </a>
                <form class="search-form" action="{{ route('search.search_product') }}" method="post">
                    @csrf
                    <div class="search search-bar" style="width: 100%">
                        <label class="search-form__label">
                            <input class="search-form__input input-text" id="autocomplete" type="text" placeholder="{{ __('text.common_search') }}" name="search_text" required>
                        </label>
                        <button class="search-form__button" aria-label="Search"></button>
                    </div>
                </form>

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

                <a class="cart-button" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                    <span class="icon">
                        <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                    </span>
                    <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                    <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                </a>
            </div>
            <div class="header-info">
                <div class="header-promo">
                    <div class="header-promo__title">
                        <span class="icon">
                            <img src="{{ asset($design . '/images/icons/star.svg') }}" class="inline-svg">
                        </span>
                        <span class="header-promo__title-text">1 000 000</span>
                    </div>
                    <div class="header-promo__text">{{ __('text.common_customers') }}</div>
                </div>
                <div class="header-items">
                    <div class="header-item">
                        <div class="header-item__title">
                            <span class="icon">
                                <img src="{{ asset($design . '/images/icons/discount.svg') }}" class="inline-svg">
                            </span>{{ __('text.common_save') }}
                        </div>
                        <div class="header-item__text">{{ __('text.common_discount') }}</div>
                    </div>
                    <div class="header-item">
                        <div class="header-item__title">
                            <span class="icon">
                                <img src="{{ asset($design . '/images/icons/no-precs.svg') }}" class="inline-svg">
                            </span>
                            {{ __('text.common_prescription') }}
                        </div>
                        <div class="header-item__text">{{ __('text.common_restrictions') }}</div>
                    </div>
                    <div class="header-item">
                        <div class="header-item__title">
                            <span class="icon">
                                <img src="{{ asset($design . '/images/icons/delivery.svg') }}" class="inline-svg">
                            </span>
                            {{ __('text.common_delivery') }}
                        </div>
                        <div class="header-item__text">{{ __('text.common_receive') }}</div>
                    </div>
                    <div class="header-item">
                        <div class="header-item__title">
                            <span class="icon">
                                <img src="{{ asset($design . '/images/icons/money.svg') }}" class="inline-svg">
                            </span>
                            {{ __('text.common_moneyback') }}
                        </div>
                        <div class="header-item__text">{{ __('text.common_refund') }}</div>
                    </div>
                </div>
            </div>
            <div class="header-bottom-row">
                <div class="header-caption">
                    <span class="icon">
                        <img src="{{ asset($design . '/images/icons/verified.svg') }}" class="inline-svg" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                    </span>
                    <div class="header-caption__title">{{ __('text.common_verified') }}</div>
                    <div class="header-caption__text">{{ __('text.common_approved_d4') }}</div>
                </div>
                <div class="header-brands">
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-1-48w.webp 1x, ' . $design . '/images/brands/white-brand-1-97w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-1-48w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-1-48w.png 1x, ' . $design . '/images/brands/white-brand-1-97w.png 2x') }}" width="49" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fda' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-2-46w.webp 1x, ' . $design . '/images/brands/white-brand-2-93w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-2-46w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-2-46w.png 1x, ' . $design . '/images/brands/white-brand-2-93w.png 2x') }}" width="47" height="36" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' pgue' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-3-56w.webp 1x, ' . $design . '/images/brands/white-brand-3-113w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-3-56w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-3-56w.png 1x, ' . $design . '/images/brands/white-brand-3-113w.png 2x') }}" width="57" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-4-45w.webp 1x, ' . $design . '/images/brands/white-brand-4-91w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-4-45w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-4-45w.png 1x, ' . $design . '/images/brands/white-brand-4-91w.png 2x') }}" width="46" height="28" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cipa' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-5-56w.webp 1x, ' . $design . '/images/brands/white-brand-5-113w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-5-56w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-5-56w.png 1x, ' . $design . '/images/brands/white-brand-5-113w.png 2x') }}" width="57" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" srcset="{{ asset($design . '/images/brands/white-brand-6-72w.webp 1x, ' . $design . '/images/brands/white-brand-6-145w.webp 2x') }}">
                            <img src="{{ asset($design . '/images/brands/white-brand-6-72w.png') }}" srcset="{{ asset($design . '/images/brands/white-brand-6-72w.png 1x, ' . $design . '/images/brands/white-brand-6-145w.png 2x') }}" width="73" height="30" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mcafree' }}" @else alt="Brand" @endif>
                        </picture>
                    </div>
                </div>
            </div>
            <div class="drug-index">
                <div class="drug-index__caption">{{ __('text.common_first_letter') }}:</div>
                <div class="drag-nav">
                    <ul class="drag-nav-container drug-index__list">
                        @foreach ($first_letters as $key => $active_letter)
                            <li class="drug-index__item drag-nav-item">
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
            <nav class="nav cat-nav">
                <div class="nav-container">
                    <div class="nav__heading">{{ __('text.common_categories_menu') }}</div>
                    <button class="nav__close-button" aria-label="Close categories"></button>
                    <ul class="nav__list">
                        <li class="nav__item">
                            <a class="nav__link nav__sublist-toggler" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                        </svg>
                                    @endif
                                </span>
                            </a>
                        </li>
                        @foreach ($menu as $category)
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                            </svg>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="categories-sublists">
                        <ul class="nav__sublist grid-sublist-4-col" data-sublist-index="0">
                            <li class="nav__item nav__item--return">
                                <button class="nav__mobile-return">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                            </svg>
                                        @endif
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
                            <ul class="nav__sublist grid-sublist-4-col" data-sublist-index="{{ $loop->iteration }}">
                                <li class="nav__item nav__item--return">
                                    <button class="nav__mobile-return">
                                        <span class="icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                                    <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                                </svg>
                                            @endif
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
        </div>
    </header>

    <div class="promos">
        <div class="container">

            @yield('promo_bonus')

            <div class="promos-payment-methods drag-nav">
                <div class="drag-nav-container">
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/visa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/mastercard.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/maestro.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/discover.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/jsb.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jsb' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/unionpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' unionpay' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/dinners-club.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/apple-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/google-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amazon-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/stripe.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/paypal.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/sepa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/cashapp.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/adyen.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/skrill.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/worldpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/payline.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/bitcoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/binance-coin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/ethereum.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/litecoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/tron.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(erc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                        @endif
                    </div>
                    <div class="promos-payment-method">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(trc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
        </div>
    </div>

    @yield('content')

    <div class="sup-footer container"><!-- Subscribe block-->
        <div class="subscribe">
            <div class="subscribe__caption">
                <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                <div class="subscribe__text">{{ __('text.common_spec_offer') }}</div>
            </div>
            <form class="subscribe-form">
                <label class="subscribe-form__label">
                    <input class="subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                </label>
                <button class="subscribe-form__button button button--secondary button_sub" type="button">
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#arrow-circle') }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M20 10C20 4.47715 15.5228 0 10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20C15.5228 20 20 15.5228 20 10ZM6 9.25C5.58579 9.25 5.25 9.58579 5.25 10C5.25 10.4142 5.58579 10.75 6 10.75H12.9362C12.9251 10.7642 12.9137 10.7787 12.9018 10.7935C12.6427 11.1161 12.2573 11.4997 11.6824 12.0695L10.272 13.4673C9.97784 13.7589 9.97573 14.2338 10.2673 14.528C10.5589 14.8222 11.0338 14.8243 11.328 14.5327L12.7691 13.1043C13.3053 12.573 13.7525 12.1298 14.0713 11.7328C14.4057 11.3163 14.6601 10.8787 14.7281 10.3455C14.7427 10.2308 14.75 10.1154 14.75 10C14.75 9.8846 14.7427 9.76921 14.7281 9.65451C14.6601 9.12131 14.4057 8.68367 14.0713 8.26724C13.7525 7.87022 13.3053 7.42704 12.7691 6.89566L11.328 5.46731C11.0338 5.17573 10.5589 5.17784 10.2673 5.47204C9.97573 5.76624 9.97784 6.24111 10.272 6.53269L11.6824 7.93054C12.2573 8.50033 12.6427 8.88386 12.9018 9.20649C12.9137 9.22128 12.9251 9.23579 12.9362 9.25H6Z" fill="currentColor"/>
                            </svg>
                        @endif
                    </span>
                    <span class="button-text">{{ __('text.common_subscribe') }}</span>
                </button>
            </form>
        </div>

        @yield('rewies')

        <div class="footer-delivery-methods drag-nav" style="@yield('delivery_style')">
            <div class="drag-nav-container" style="@yield('delivery_style_2')">
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#usps')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/usps.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ems')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ems.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dhl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dhl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ups')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ups.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#fedex')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/fedex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#tnt')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/tnt.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#postnl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/postnl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#deutsche_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/deutsche_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dpd')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dpd.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#gls')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/gls.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                            <use width="100%" height="100%" width="100%" href="{{ asset('pub_images/shipping/sprite.svg#australia_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/australia_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#colissimo')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/colissimo.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                    @endif
                </div>
                <div class="footer-delivery-method">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#correos')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/correos.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                <div class="footer__wrapper">
                    <a class="footer__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="Logo">
                        @endif
                    </a>
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                            </ul>
                        </div>
                    </nav>
                    <a class="footer__affiliate-button button" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                </div>
            @else
                <div class="footer__wrapper">
                    <a class="footer__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="Logo">
                        @endif
                    </a>
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></li>
                            </ul>
                        </div>
                    </nav>
                    <a class="footer__affiliate-button button" href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                </div>
            @endif

            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                <div class="sitemap_menu">
                    <a class="nav__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                </div>
            @endif

            <div class="footer__copyrights">
                {{ __('text.license_text_license1_1') }}
                {{ $domain }}
                {{ __('text.license_text_license1_2') }}
                {{ __('text.license_text_license2_d11') }}
            </div>
            <div class="footer-buttons">
                <div class="footer-buttons__container">
                    <a class="footer-button" href="{{ route('home.index') }}">
                        <span class="icon">
                            <img src="{{ asset($design . '/images/icons/home.svg') }}" class="inline-svg">
                        </span>
                        <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                    </a>
                    <button class="footer-button footer-button--cat">
                        <span class="icon button__fries-icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#menu-fries') }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M5 5.25C4.58579 5.25 4.25 5.58579 4.25 6C4.25 6.41421 4.58579 6.75 5 6.75H19C19.4142 6.75 19.75 6.41421 19.75 6C19.75 5.58579 19.4142 5.25 19 5.25H5Z" fill="currentColor"/>
                                    <path d="M5 11.25C4.58579 11.25 4.25 11.5858 4.25 12C4.25 12.4142 4.58579 12.75 5 12.75H11C11.4142 12.75 11.75 12.4142 11.75 12C11.75 11.5858 11.4142 11.25 11 11.25H5Z" fill="currentColor"/>
                                    <path d="M5 17.25C4.58579 17.25 4.25 17.5858 4.25 18C4.25 18.4142 4.58579 18.75 5 18.75H19C19.4142 18.75 19.75 18.4142 19.75 18C19.75 17.5858 19.4142 17.25 19 17.25H5Z" fill="currentColor"/>
                                </svg>
                            @endif
                        </span>
                        <span class="icon button__close-icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close-thin') }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20 10C20 4.47715 15.5228 0 10 0C4.47715 0 0 4.47715 0 10C0 15.5228 4.47715 20 10 20C15.5228 20 20 15.5228 20 10ZM6 9.25C5.58579 9.25 5.25 9.58579 5.25 10C5.25 10.4142 5.58579 10.75 6 10.75H12.9362C12.9251 10.7642 12.9137 10.7787 12.9018 10.7935C12.6427 11.1161 12.2573 11.4997 11.6824 12.0695L10.272 13.4673C9.97784 13.7589 9.97573 14.2338 10.2673 14.528C10.5589 14.8222 11.0338 14.8243 11.328 14.5327L12.7691 13.1043C13.3053 12.573 13.7525 12.1298 14.0713 11.7328C14.4057 11.3163 14.6601 10.8787 14.7281 10.3455C14.7427 10.2308 14.75 10.1154 14.75 10C14.75 9.8846 14.7427 9.76921 14.7281 9.65451C14.6601 9.12131 14.4057 8.68367 14.0713 8.26724C13.7525 7.87022 13.3053 7.42704 12.7691 6.89566L11.328 5.46731C11.0338 5.17573 10.5589 5.17784 10.2673 5.47204C9.97573 5.76624 9.97784 6.24111 10.272 6.53269L11.6824 7.93054C12.2573 8.50033 12.6427 8.88386 12.9018 9.20649C12.9137 9.22128 12.9251 9.23579 12.9362 9.25H6Z" fill="currentColor"/>
                                </svg>
                            @endif
                        </span>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                    </button>
                    <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                        <span class="icon">
                            <img src="{{ asset($design . '/images/icons/profile.svg') }}" class="inline-svg">
                        </span>
                        <span class="button__text">{{ __('text.common_profile') }}</span>
                    </a>
                    <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                        <span class="icon">
                            <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                        </span>
                        <span class="button__text">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <div class="cat-overlay overlay"></div>

    <dialog class="dialog-container" data-name="call" data-modal="true" data-clickable-backdrop="true">
        <div class="dialog">
            <header class="dialog__header">
                <div class="dialog__title">{{ __('text.common_callback') }}</div>
            </header>
            <form class="form callback-form" method="dialog">
                <div class="form__field text-field">
                    <input class="form__text-input input-tel intl-phone" type="tel" id="callback-phone" name="callback-phone" placeholder="000 000 00 00" required>
                    <label class="form__label label-tel" for="callback-phone"></label>
                </div>
                <div class="form__field submit-field">
                    <input class="button form__submit button_request_call" type="submit" value="{{ __('text.common_callback') }}">
                </div>
            </form>
            <button class="dialog__close-button close-button">Close</button>
            <div class="message_sended hidden">
                {{-- <button class="dialog__close-button close-button-message">Close</button> --}}
                <div style="text-align: center">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>
    </dialog>

    <div class="popup_white hide">
        <div class="popup_push">
            <div class="button_close">
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
    </script>

    <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
    <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif
</body>
</html>