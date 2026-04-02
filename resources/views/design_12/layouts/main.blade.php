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

        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
        <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

        @if (env('APP_PWA', 0))
            <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
            <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
        @endif

        {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

        {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

        <link href="{{ asset($design . '/fonts/dm-sans-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/dm-sans-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/dm-sans-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/futura-pt-demi.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/futura-pt-book.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/marcellus-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/marcellus-sc-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/neue-machina-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/plus-jakarta-sans-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

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

        <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"></script>
        <script defer src="{{ asset($design . '/vendor/just-validate.min.js') }}"> </script>
        <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>
        {!! isset($pixel) ? $pixel : '' !!}
    </head>

    <body class="webp @yield('page_name')">
        <script>
            let flagc = false;
            let flagp = false;
            const design = 12;
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

        <header class="header">
            <div class="container">
                <nav class="nav cat-nav">
                    <div class="nav-container">
                        <div class="nav__heading">{{ __('text.common_categories_menu') }}</div>
                        <button class="nav__close-button" aria-label="Close categories"></button>
                        <ul class="nav__list">
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em"  fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
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
                                                <svg width="1em" height="1em"  fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                    <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
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
                                                <svg width="1em" height="1em"  fill="currentColor">
                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                    <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
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
                                                    <svg width="1em" height="1em"  fill="currentColor">
                                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                        <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
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
                <div class="header-controls">
                    <a class="header__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
                        @endif
                    </a>
                    <div class="header-settings">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="header-lang header-select-wrapper">
                                <select class="header-select" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif>{{ $item['name'] }}</option>
                                    @endforeach
                                </select>
                                <span class="icon header-select-wrapper__icon">
                                    <img src="{{ asset($design . '/images/icons/planet.svg') }}">
                                </span>
                                <span class="icon header-select-wrapper__chevron">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em"  fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                            <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
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
                                    <img src="{{ asset($design . '/images/icons/wallet.svg') }}">
                                </span>
                                <span class="icon header-select-wrapper__chevron">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em"  fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-rr-angle-small-down') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                            <path d="M12.4726 6.27337C12.4106 6.21088 12.3369 6.16129 12.2556 6.12744C12.1744 6.0936 12.0872 6.07617 11.9992 6.07617C11.9112 6.07617 11.8241 6.0936 11.7428 6.12744C11.6616 6.16129 11.5879 6.21088 11.5259 6.27337L8.47256 9.3267C8.41058 9.38918 8.33685 9.43878 8.25561 9.47262C8.17437 9.50647 8.08723 9.5239 7.99923 9.5239C7.91122 9.5239 7.82408 9.50647 7.74284 9.47262C7.6616 9.43878 7.58787 9.38918 7.52589 9.3267L4.47256 6.27337C4.41058 6.21088 4.33685 6.16129 4.25561 6.12744C4.17437 6.0936 4.08723 6.07617 3.99923 6.07617C3.91122 6.07617 3.82408 6.0936 3.74284 6.12744C3.6616 6.16129 3.58787 6.21088 3.52589 6.27337C3.40173 6.39828 3.33203 6.56725 3.33203 6.74337C3.33203 6.91949 3.40173 7.08846 3.52589 7.21337L6.58589 10.2734C6.96089 10.6479 7.46922 10.8583 7.99923 10.8583C8.52923 10.8583 9.03756 10.6479 9.41256 10.2734L12.4726 7.21337C12.5967 7.08846 12.6664 6.91949 12.6664 6.74337C12.6664 6.56725 12.5967 6.39828 12.4726 6.27337Z" fill="currentColor"/>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                        @endif
                        <a class="header-auth header-nav-link" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#profile-circle-2') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M1.0415 10C1.0415 5.05245 5.05229 1.04167 9.99984 1.04167C14.9474 1.04167 18.9582 5.05245 18.9582 10C18.9582 12.5404 17.8999 14.8347 16.2019 16.4642C14.5929 18.0084 12.4067 18.9583 9.99984 18.9583C7.59302 18.9583 5.40678 18.0084 3.79776 16.4642C2.09975 14.8347 1.0415 12.5404 1.0415 10ZM15.0691 15.8071C14.7099 14.7327 13.6947 13.9583 12.4998 13.9583H7.49984C6.30495 13.9583 5.28976 14.7327 4.93056 15.8071C6.28611 16.9915 8.05869 17.7083 9.99984 17.7083C11.941 17.7083 13.7136 16.9915 15.0691 15.8071ZM9.99984 3.54167C7.81371 3.54167 6.0415 5.31388 6.0415 7.5C6.0415 9.68613 7.81371 11.4583 9.99984 11.4583C12.186 11.4583 13.9582 9.68613 13.9582 7.5C13.9582 5.31388 12.186 3.54167 9.99984 3.54167Z"/>
                                    </svg>
                                @endif
                            </span>
                            {{ __('text.common_profile') }}
                        </a>
                    </div>
                    <div class="header-caption">
                        <span class="icon icon--grad">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_4223827') }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 20 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                    <path d="M18.5554 4.10755C14.2309 4.10755 10.5209 1.19866 10.4821 1.16755C10.3447 1.05904 10.1748 1.00002 9.99984 1.00002C9.82483 1.00002 9.65493 1.05904 9.51761 1.16755C9.47873 1.19866 5.78428 4.10755 1.44428 4.10755C1.238 4.10755 1.04017 4.18949 0.89431 4.33535C0.748448 4.48122 0.666504 4.67905 0.666504 4.88533V11.8853C0.666504 17.1042 2.77428 19.6631 9.68095 22.7042C9.78122 22.7493 9.8899 22.7726 9.99984 22.7726C10.1098 22.7726 10.2185 22.7493 10.3187 22.7042C17.2254 19.6631 19.3332 17.1042 19.3332 11.8853V4.88533C19.3332 4.67905 19.2512 4.48122 19.1054 4.33535C18.9595 4.18949 18.7617 4.10755 18.5554 4.10755ZM14.4409 9.32644L8.9965 14.7709C8.9242 14.8438 8.83818 14.9016 8.7434 14.9411C8.64862 14.9806 8.54696 15.0009 8.44428 15.0009C8.34161 15.0009 8.23995 14.9806 8.14517 14.9411C8.05039 14.9016 7.96436 14.8438 7.89206 14.7709L5.55873 12.4375C5.41227 12.2911 5.32999 12.0925 5.32999 11.8853C5.32999 11.6782 5.41227 11.4796 5.55873 11.3331C5.70518 11.1866 5.90382 11.1044 6.11095 11.1044C6.31807 11.1044 6.51671 11.1866 6.66317 11.3331L8.44428 13.122L13.3365 8.22199C13.483 8.07553 13.6816 7.99325 13.8887 7.99325C14.0958 7.99325 14.2945 8.07553 14.4409 8.22199C14.5874 8.36845 14.6697 8.56709 14.6697 8.77421C14.6697 8.98134 14.5874 9.17998 14.4409 9.32644Z"/>
                                </svg>
                            @endif
                        </span>
                        <div class="header-caption__title">{{ __('text.common_verified') }}</div>
                        <div class="header-caption__text">{{ __('text.common_approved_d4') }}</div>
                    </div>
                    <div class="header-brands">
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-01-49w.webp 1x, ' . $design . '/images/brands/brand-01-98w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-01-49w.png') }}" srcset="{{ asset($design . '/images/brands/brand-01-49w.png 1x, ' . $design . '/images/brands/brand-01-98w.png 2x') }}" width="49" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fda' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-02-46w.webp 1x, ' . $design . '/images/brands/brand-02-93w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-02-46w.png') }}" srcset="{{ asset($design . '/images/brands/brand-02-46w.png 1x, ' . $design . '/images/brands/brand-02-93w.png 2x') }}" width="47" height="36" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' pgue' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-03-45w.webp 1x, ' . $design . '/images/brands/brand-03-91w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-03-45w.png') }}" srcset="{{ asset($design . '/images/brands/brand-03-45w.png 1x, ' . $design . '/images/brands/brand-03-91w.png 2x') }}" width="46" height="28" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-04-69w.webp 1x, ' . $design . '/images/brands/brand-04-139w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-04-69w.png') }}" srcset="{{ asset($design . '/images/brands/brand-04-69w.png 1x, ' . $design . '/images/brands/brand-04-139w.png 2x') }}" width="70" height="36" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cipa' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-05-56w.webp 1x, ' . $design . '/images/brands/brand-05-113w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-05-56w.png') }}" srcset="{{ asset($design . '/images/brands/brand-05-56w.png 1x, ' . $design . '/images/brands/brand-05-113w.png 2x') }}" width="57" height="32" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @else alt="Brand" @endif>
                            </picture>
                        </div>
                        <div class="header-brand">
                            <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/images/brands/brand-06-72w.webp 1x, ' . $design . '/images/brands/brand-06-145w.webp 2x') }}">
                                <img src="{{ asset($design . '/images/brands/brand-06-72w.png') }}" srcset="{{ asset($design . '/images/brands/brand-06-72w.png 1x, ' . $design . '/images/brands/brand-06-145w.png 2x') }}" width="73" height="30" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mcafree' }}" @else alt="Brand" @endif>
                            </picture>
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

                    <a class="cart-button button button--outlined" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                        <span class="icon icon--grad">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#shopping_cart') }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                    <path d="M8 18C6.9 18 6.01 18.9 6.01 20C6.01 21.1 6.9 22 8 22C9.1 22 10 21.1 10 20C10 18.9 9.1 18 8 18ZM2 2V4H4L7.6 11.59L6.25 14.04C6.09 14.32 6 14.65 6 15C6 16.1 6.9 17 8 17H20V15H8.42C8.28 15 8.17 14.89 8.17 14.75L8.2 14.63L9.1 13H16.55C17.3 13 17.96 12.59 18.3 11.97L21.88 5.48C21.96 5.34 22 5.17 22 5C22 4.45 21.55 4 21 4H6.21L5.27 2H2ZM18 18C16.9 18 16.01 18.9 16.01 20C16.01 21.1 16.9 22 18 22C19.1 22 20 21.1 20 20C20 18.9 19.1 18 18 18Z"/>
                                </svg>
                            @endif
                        </span>
                        <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                        <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                    <div class="header-controls__nav-row">
                        <button class="button categories-button">
                            <span class="icon button__fries-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#menu-fries') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M5 5.25C4.58579 5.25 4.25 5.58579 4.25 6C4.25 6.41421 4.58579 6.75 5 6.75H19C19.4142 6.75 19.75 6.41421 19.75 6C19.75 5.58579 19.4142 5.25 19 5.25H5Z" fill="currentColor"/>
                                        <path d="M5 11.25C4.58579 11.25 4.25 11.5858 4.25 12C4.25 12.4142 4.58579 12.75 5 12.75H11C11.4142 12.75 11.75 12.4142 11.75 12C11.75 11.5858 11.4142 11.25 11 11.25H5Z" fill="currentColor"/>
                                        <path d="M5 17.25C4.58579 17.25 4.25 17.5858 4.25 18C4.25 18.4142 4.58579 18.75 5 18.75H19C19.4142 18.75 19.75 18.4142 19.75 18C19.75 17.5858 19.4142 17.25 19 17.25H5Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon button__close-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close-thin') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
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
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi-sr-menu-dots-vertical') }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 4 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#clip0)">
                                                    <path d="M2.00002 3.00002C2.82845 3.00002 3.50003 2.32845 3.50003 1.50001C3.50003 0.671578 2.82845 0 2.00002 0C1.17158 0 0.5 0.671578 0.5 1.50001C0.5 2.32845 1.17158 3.00002 2.00002 3.00002Z" fill="#B2B2B2"/>
                                                    <path d="M2.00002 10.5C2.82845 10.5 3.50003 9.82842 3.50003 8.99999C3.50003 8.17155 2.82845 7.49997 2.00002 7.49997C1.17158 7.49997 0.5 8.17155 0.5 8.99999C0.5 9.82842 1.17158 10.5 2.00002 10.5Z" fill="#B2B2B2"/>
                                                    <path d="M2.00002 18C2.82845 18 3.50003 17.3284 3.50003 16.5C3.50003 15.6715 2.82845 15 2.00002 15C1.17158 15 0.5 15.6715 0.5 16.5C0.5 17.3284 1.17158 18 2.00002 18Z" fill="#B2B2B2"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0">
                                                    <rect width="4" height="18" fill="white"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="icon is-hidden">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close') }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <path d="M6 6L18.7742 18.7742" stroke="#B2B2B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 18.7742L18.7742 5.99998" stroke="#B2B2B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
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
                </div>
            </div>
            <div class="header-info">
                <div class="container">
                    <div class="header-promo">
                        <div class="header-promo__title">
                            <span class="icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#star') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 22 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M10.3934 0.515887C10.5073 0.285127 10.7423 0.139038 10.9996 0.139038C11.257 0.138993 11.492 0.285082 11.606 0.515843L14.2431 5.85846C14.4911 6.36082 14.9704 6.70911 15.5248 6.78963L21.421 7.64674C21.6757 7.68379 21.8873 7.86215 21.9669 8.10693C22.0463 8.35166 21.9801 8.62036 21.7958 8.80001L17.5296 12.9591C17.1284 13.3502 16.9454 13.9137 17.04 14.4658L18.0466 20.3373C18.0901 20.591 17.9858 20.8473 17.7776 20.9984C17.5694 21.1498 17.2934 21.1697 17.0656 21.0499L11.7918 18.2778C11.2959 18.0172 10.7035 18.0172 10.2075 18.2779L4.93416 21.0499C4.70634 21.1696 4.43033 21.1496 4.22215 20.9984C4.01393 20.8471 3.90971 20.5907 3.95318 20.3371L4.96027 14.4661C5.05503 13.9137 4.87194 13.3502 4.47067 12.959L0.204194 8.80006C0.0199426 8.6204 -0.046429 8.3517 0.0331098 8.10697C0.112648 7.86224 0.324216 7.68388 0.578901 7.64679L6.47494 6.78972C7.02939 6.70911 7.50872 6.36087 7.75671 5.85842L10.3934 0.515887Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="header-promo__title-text">1 000 000</span>
                        </div>
                        <div class="header-promo__text">{{ __('text.common_customers') }}</div>
                    </div>
                    <div class="header-items">
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_1685179') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#clip0)">
                                                <path d="M10.7758 11.9188C10.6235 11.9188 10.4712 12.0573 10.4712 12.5212C10.4712 12.9851 10.6235 13.1236 10.7758 13.1236C10.9282 13.1236 11.0805 12.9851 11.0805 12.5212C11.0805 12.0573 10.9282 11.9188 10.7758 11.9188Z"/>
                                                <path d="M7.22408 9.63383C7.07176 9.63383 6.91943 9.77233 6.91943 10.2362C6.91943 10.7002 7.07176 10.8386 7.22408 10.8386C7.37641 10.8386 7.52874 10.7002 7.52874 10.2362C7.52874 9.77233 7.37641 9.63383 7.22408 9.63383Z"/>
                                                <path d="M14.2706 6.38579C12.7445 4.70022 11.8375 3.2164 11.3454 2.26842C10.8124 1.24162 10.6377 0.604905 10.6363 0.599838L10.4777 0L9.91103 0.25275C9.84481 0.282273 8.27918 0.996259 7.40193 2.75073C6.96156 3.63151 6.87348 4.61214 6.87718 5.27965C6.87932 5.66811 6.60409 6.00542 6.22276 6.08167C5.95541 6.13508 5.68039 6.05197 5.48742 5.859L4.43191 4.80349L4.06881 5.30417C4.03112 5.35614 3.14377 6.57992 2.98222 6.82444C2.19336 8.01859 1.78202 9.40965 1.79268 10.8472C1.80697 12.7672 2.56202 14.566 3.91874 15.9124C5.27538 17.2586 7.07996 18 9.00006 18C12.9743 18 16.2076 14.7667 16.2076 10.7925C16.2076 9.35152 15.5016 7.74536 14.2706 6.38579ZM10.7761 13.8714C10.0421 13.8714 9.48818 13.3867 9.48818 12.5212C9.48818 11.6626 10.0421 11.171 10.7761 11.171C11.51 11.171 12.064 11.6626 12.064 12.5212C12.064 13.3867 11.51 13.8714 10.7761 13.8714ZM10.0698 8.95528H11.24L7.93026 13.8021H6.76011L10.0698 8.95528ZM5.93616 10.2362C5.93616 9.37767 6.49008 8.88606 7.22406 8.88606C7.95803 8.88606 8.51195 9.37767 8.51195 10.2362C8.51195 11.1017 7.95803 11.5864 7.22406 11.5864C6.49008 11.5864 5.93616 11.1017 5.93616 10.2362Z"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0">
                                                <rect width="18" height="18" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>{{ __('text.common_save') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_discount') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_15566232') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M10.2113 6.25098L11.3685 4.87234C11.5084 4.70535 11.5978 4.50197 11.6262 4.28602C11.6546 4.07007 11.6209 3.85049 11.529 3.65302C11.4371 3.45554 11.2908 3.28834 11.1073 3.17101C10.9238 3.05368 10.7106 2.99107 10.4928 2.99052C10.4589 2.99052 10.4251 2.9918 10.3906 2.99499C10.2408 3.00768 10.095 3.04997 9.96167 3.11943C9.82835 3.18889 9.71015 3.28412 9.61392 3.39961L8.45648 4.7785L10.2113 6.25098Z"/>
                                            <path d="M3.94545 3.6C4.119 3.59983 4.28538 3.53081 4.4081 3.4081C4.53081 3.28538 4.59983 3.119 4.6 2.94545V0.116692C4.42409 0.190155 4.2641 0.297054 4.1289 0.431463L1.43146 3.1289C1.29706 3.26411 1.19014 3.4241 1.11663 3.6H3.94545Z"/>
                                            <path d="M7.64581 8.78683C7.7956 8.77414 7.94138 8.73184 8.0747 8.66239C8.20801 8.59293 8.32621 8.4977 8.42244 8.38221L9.57988 7.00332L7.82511 5.53084L6.66783 6.90948C6.57116 7.02474 6.49816 7.15792 6.45297 7.3014C6.40779 7.44488 6.39131 7.59586 6.40449 7.74571C6.41767 7.89556 6.46024 8.04135 6.52977 8.17474C6.59931 8.30814 6.69444 8.42653 6.80973 8.52315C6.92432 8.62087 7.05738 8.69456 7.20101 8.73986C7.34463 8.78515 7.49589 8.80112 7.64581 8.78683Z"/>
                                            <path d="M14.9091 0H5.58182V2.94545C5.58136 3.37931 5.40881 3.79526 5.10204 4.10204C4.79526 4.40881 4.37931 4.58136 3.94545 4.58182H1V16.5273C1.00048 16.9177 1.1558 17.292 1.43188 17.5681C1.70797 17.8442 2.08228 17.9995 2.47273 18H14.9091C15.2995 17.9995 15.6739 17.8442 15.9499 17.5681C16.226 17.292 16.3813 16.9177 16.3818 16.5273V1.47273C16.3813 1.08228 16.226 0.707969 15.9499 0.431884C15.6739 0.155798 15.2995 0.000481523 14.9091 0ZM5.91612 6.27859L8.86158 2.76871C9.2242 2.33659 9.74362 2.06621 10.3056 2.01706C10.8676 1.96791 11.426 2.14402 11.8582 2.50664C12.2903 2.86926 12.5607 3.38869 12.6098 3.95065C12.659 4.51262 12.4829 5.0711 12.1202 5.50323L9.17479 9.0131C8.99591 9.22769 8.77623 9.40466 8.52848 9.53375C8.28073 9.66284 8.00983 9.7415 7.73146 9.76516C7.66818 9.7706 7.6049 9.77347 7.54226 9.77347C7.1376 9.7728 6.74152 9.65674 6.40051 9.43889C6.05949 9.22104 5.78768 8.91045 5.61696 8.54356C5.44625 8.17667 5.38372 7.7687 5.43671 7.36753C5.4897 6.96635 5.65601 6.58857 5.91612 6.27859ZM12.6182 16.0364H4.76364C4.63344 16.0364 4.50857 15.9846 4.41651 15.8926C4.32445 15.8005 4.27273 15.6757 4.27273 15.5455C4.27273 15.4153 4.32445 15.2904 4.41651 15.1983C4.50857 15.1063 4.63344 15.0545 4.76364 15.0545H12.6182C12.7484 15.0545 12.8732 15.1063 12.9653 15.1983C13.0574 15.2904 13.1091 15.4153 13.1091 15.5455C13.1091 15.6757 13.0574 15.8005 12.9653 15.8926C12.8732 15.9846 12.7484 16.0364 12.6182 16.0364ZM12.6182 14.0727H4.76364C4.63344 14.0727 4.50857 14.021 4.41651 13.9289C4.32445 13.8369 4.27273 13.712 4.27273 13.5818C4.27273 13.4516 4.32445 13.3268 4.41651 13.2347C4.50857 13.1426 4.63344 13.0909 4.76364 13.0909H12.6182C12.7484 13.0909 12.8732 13.1426 12.9653 13.2347C13.0574 13.3268 13.1091 13.4516 13.1091 13.5818C13.1091 13.712 13.0574 13.8369 12.9653 13.9289C12.8732 14.021 12.7484 14.0727 12.6182 14.0727ZM12.6182 12.1091H4.76364C4.63344 12.1091 4.50857 12.0574 4.41651 11.9653C4.32445 11.8732 4.27273 11.7484 4.27273 11.6182C4.27273 11.488 4.32445 11.3631 4.41651 11.2711C4.50857 11.179 4.63344 11.1273 4.76364 11.1273H12.6182C12.7484 11.1273 12.8732 11.179 12.9653 11.2711C13.0574 11.3631 13.1091 11.488 13.1091 11.6182C13.1091 11.7484 13.0574 11.8732 12.9653 11.9653C12.8732 12.0574 12.7484 12.1091 12.6182 12.1091Z"/>
                                        </svg>
                                    @endif
                                </span>{{ __('text.common_prescription') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_restrictions') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_9356319') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#clip0)">
                                                <path d="M14.8463 14.1755C14.8504 14.1254 14.8504 14.075 14.8463 14.0249C14.8463 13.5813 14.6701 13.1558 14.3563 12.8421C14.0426 12.5284 13.6171 12.3521 13.1735 12.3521C12.7298 12.3521 12.3043 12.5284 11.9906 12.8421C11.6769 13.1558 11.5007 13.5813 11.5007 14.0249C11.4965 14.075 11.4965 14.1254 11.5007 14.1755C11.5007 14.6191 11.6769 15.0446 11.9906 15.3583C12.3043 15.672 12.7298 15.8483 13.1735 15.8483C13.6171 15.8483 14.0426 15.672 14.3563 15.3583C14.6701 15.0446 14.8463 14.6191 14.8463 14.1755Z"/>
                                                <path d="M8.15489 14.1755C8.15904 14.1254 8.15904 14.075 8.15489 14.0249C8.15489 13.5813 7.97865 13.1558 7.66494 12.8421C7.35122 12.5284 6.92574 12.3521 6.48208 12.3521C6.03842 12.3521 5.61293 12.5284 5.29922 12.8421C4.98551 13.1558 4.80927 13.5813 4.80927 14.0249C4.80511 14.075 4.80511 14.1254 4.80927 14.1755C4.80927 14.6191 4.98551 15.0446 5.29922 15.3583C5.61293 15.672 6.03842 15.8483 6.48208 15.8483C6.92574 15.8483 7.35122 15.672 7.66494 15.3583C7.97865 15.0446 8.15489 14.6191 8.15489 14.1755Z"/>
                                                <path d="M1.88192 10.2025H0.627306C0.51213 10.2023 0.399162 10.2341 0.301107 10.2946C0.2159 10.3471 0.144501 10.4192 0.0928733 10.505C0.0412456 10.5907 0.010889 10.6876 0.00433933 10.7875C-0.00221033 10.8874 0.0152372 10.9874 0.0552243 11.0791C0.0952114 11.1709 0.156577 11.2518 0.234194 11.315C0.343667 11.4084 0.483366 11.459 0.627306 11.4572H2.71832C2.88469 11.4572 3.04425 11.5232 3.16189 11.6409C3.27954 11.7585 3.34563 11.9181 3.34563 12.0845C3.34563 12.2508 3.27954 12.4104 3.16189 12.528C3.04425 12.6457 2.88469 12.7118 2.71832 12.7118H1.88192C1.71554 12.7118 1.55599 12.7779 1.43834 12.8955C1.3207 13.0131 1.25461 13.1727 1.25461 13.3391C1.25461 13.5054 1.3207 13.665 1.43834 13.7826C1.55599 13.9003 1.71554 13.9664 1.88192 13.9664H4.1904C4.22638 13.541 4.38308 13.1348 4.64206 12.7954C4.85851 12.5121 5.13678 12.282 5.45567 12.1225C5.77456 11.9631 6.12564 11.8785 6.48215 11.8754C7.05673 11.8732 7.61118 12.0869 8.03563 12.4741C8.46009 12.8614 8.72358 13.394 8.77391 13.9664H10.8817C10.9307 13.3924 11.1934 12.8577 11.6178 12.4681C12.0422 12.0784 12.5973 11.8622 13.1734 11.8622C13.7495 11.8622 14.3046 12.0784 14.729 12.4681C15.1534 12.8577 15.4161 13.3924 15.4652 13.9664H16.6027C16.7983 13.9703 16.9892 13.9055 17.142 13.7833C17.2949 13.661 17.4 13.489 17.4391 13.2973L17.9911 10.5371C17.9868 10.5178 17.9868 10.4978 17.9911 10.4786C18.003 10.4066 18.003 10.3331 17.9911 10.2611L16.4187 5.6274C16.333 5.38132 16.1723 5.16829 15.9593 5.01826C15.7462 4.86823 15.4915 4.78875 15.231 4.791H13.7254L13.8174 4.12187C13.8399 3.94458 13.8245 3.76454 13.7723 3.59364C13.72 3.42273 13.6321 3.26487 13.5143 3.13047C13.3965 2.99608 13.2515 2.88821 13.089 2.814C12.9264 2.73979 12.7499 2.70093 12.5712 2.69998H3.13653C2.97015 2.69998 2.8106 2.76607 2.69295 2.88372C2.57531 3.00136 2.50922 3.16092 2.50922 3.32729C2.50922 3.49366 2.57531 3.65322 2.69295 3.77086C2.8106 3.8885 2.97015 3.95459 3.13653 3.95459H4.18203C4.34841 3.95459 4.50796 4.02068 4.62561 4.13832C4.74325 4.25597 4.80934 4.41552 4.80934 4.5819C4.80934 4.74827 4.74325 4.90783 4.62561 5.02547C4.50796 5.14311 4.34841 5.2092 4.18203 5.2092H0.627306C0.461608 5.21137 0.303306 5.27815 0.18613 5.39533C0.0689533 5.51251 0.00216693 5.67081 6.62243e-07 5.83651C-0.00023108 5.99502 0.0603663 6.14759 0.1693 6.26275C0.278233 6.37791 0.427199 6.44688 0.585485 6.45545H2.71832C2.88469 6.45545 3.04425 6.52154 3.16189 6.63918C3.27954 6.75682 3.34563 6.91638 3.34563 7.08275C3.34563 7.24912 3.27954 7.40868 3.16189 7.52632C3.04425 7.64397 2.88469 7.71006 2.71832 7.71006H1.46371H1.32989C1.19044 7.74051 1.06555 7.8176 0.975827 7.92861C0.886108 8.03962 0.836928 8.1779 0.836407 8.32063C0.836407 8.48701 0.902498 8.64656 1.02014 8.7642C1.13778 8.88185 1.29734 8.94794 1.46371 8.94794H1.88192C2.04829 8.94794 2.20784 9.01403 2.32549 9.13167C2.44313 9.24931 2.50922 9.40887 2.50922 9.57524C2.50922 9.74162 2.44313 9.90117 2.32549 10.0188C2.20784 10.1365 2.04829 10.2025 1.88192 10.2025ZM13.5916 6.02052H15.1139C15.1578 6.02118 15.2004 6.03512 15.2363 6.0605C15.2721 6.08588 15.2994 6.12151 15.3146 6.1627L16.519 9.78435H13.0898L13.5916 6.02052Z"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0">
                                                <rect width="18" height="18" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>{{ __('text.common_delivery') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_receive') }}</div>
                        </div>
                        <div class="header-item">
                            <div class="header-item__title">
                                <span class="icon icon--grad">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fi_4544406') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#clip0)">
                                                <path d="M2.90251 2.9025L2.25001 2.25C1.96126 4.2675 2.06251 3.555 1.87501 4.875L4.68751 4.6875L3.96376 3.96375C5.12983 2.79855 6.66385 2.07374 8.30443 1.91281C9.94501 1.75188 11.5906 2.16479 12.9609 3.08119C14.3312 3.99759 15.3413 5.36076 15.8191 6.93844C16.2969 8.51612 16.2129 10.2107 15.5814 11.7334C14.9499 13.2561 13.8099 14.5127 12.3558 15.289C10.9016 16.0654 9.22319 16.3136 7.60657 15.9912C5.98994 15.6688 4.53513 14.7958 3.49001 13.521C2.44489 12.2462 1.87414 10.6485 1.87501 9H0.375013C0.301913 11.2875 1.14051 13.5103 2.70632 15.1795C4.27213 16.8487 6.4369 17.8275 8.72439 17.9006C11.0119 17.9737 13.2347 17.1351 14.9039 15.5693C16.5731 14.0035 17.5519 11.8387 17.625 9.55125C18.105 1.59375 8.47126 -2.66625 2.90251 2.9025Z"/>
                                                <path d="M4.73626 5.4375L3.66751 5.505C2.7881 6.842 2.44369 8.46085 2.70269 10.0401C2.96169 11.6192 3.80516 13.0433 5.06557 14.0293C6.32599 15.0154 7.91116 15.4913 9.50628 15.3626C11.1014 15.234 12.5898 14.5101 13.6758 13.3347C14.7619 12.1593 15.3661 10.6185 15.3686 9.01823C15.3711 7.41793 14.7717 5.87523 13.6893 4.6965C12.6069 3.51776 11.1208 2.78922 9.52611 2.65556C7.9314 2.52189 6.34475 2.99289 5.08126 3.975C5.03626 3.975 5.04751 3.975 5.21626 4.15875C5.31816 4.2606 5.38856 4.38965 5.41905 4.53046C5.44954 4.67126 5.43883 4.81787 5.3882 4.95275C5.33757 5.08763 5.24916 5.20508 5.13355 5.29104C5.01794 5.37701 4.88001 5.42785 4.73626 5.4375ZM10.125 10.3725C10.0088 9.6675 9.18751 9.75 8.73751 9.75C8.14451 9.76322 7.569 9.54857 7.12951 9.15023C6.69001 8.75189 6.41998 8.2002 6.37501 7.60875C6.34907 7.05895 6.52556 6.51871 6.87108 6.09026C7.21661 5.66181 7.7072 5.37487 8.25001 5.28375C8.25001 4.4325 8.20126 4.23375 8.47126 3.97125C8.57614 3.86699 8.70949 3.79607 8.85456 3.76739C8.99964 3.73871 9.14995 3.75356 9.2866 3.81006C9.42326 3.86657 9.54016 3.96221 9.62262 4.08497C9.70507 4.20773 9.74939 4.35212 9.75001 4.5V5.25H10.875C10.9735 5.25271 11.0705 5.27479 11.1605 5.31498C11.2504 5.35518 11.3316 5.41269 11.3993 5.48425C11.467 5.55581 11.52 5.64001 11.5552 5.73204C11.5904 5.82407 11.6071 5.92213 11.6044 6.02063C11.6017 6.11912 11.5796 6.21611 11.5394 6.30607C11.4992 6.39603 11.4417 6.47719 11.3701 6.54491C11.2986 6.61264 11.2144 6.66561 11.1223 6.7008C11.0303 6.73599 10.9323 6.75271 10.8338 6.75H8.62501C8.51481 6.74839 8.40561 6.77109 8.30518 6.81649C8.20475 6.86188 8.11556 6.92885 8.04395 7.01263C7.97235 7.09641 7.92008 7.19494 7.89088 7.30122C7.86168 7.40749 7.85627 7.51889 7.87501 7.6275C7.99126 8.3325 8.81251 8.25 9.26251 8.25C9.85552 8.23678 10.431 8.45143 10.8705 8.84977C11.31 9.24811 11.5801 9.7998 11.625 10.3912C11.651 10.9411 11.4745 11.4813 11.1289 11.9097C10.7834 12.3382 10.2928 12.6251 9.75001 12.7163C9.75001 13.5675 9.79876 13.7662 9.52876 14.0288C9.42389 14.133 9.29053 14.2039 9.14546 14.2326C9.00039 14.2613 8.85008 14.2464 8.71342 14.1899C8.57677 14.1334 8.45986 14.0378 8.37741 13.915C8.29496 13.7923 8.25063 13.6479 8.25001 13.5V12.75H7.12501C7.02652 12.7473 6.92953 12.7252 6.83957 12.685C6.74961 12.6448 6.66845 12.5873 6.60072 12.5157C6.533 12.4442 6.48002 12.36 6.44484 12.268C6.40965 12.1759 6.39293 12.0779 6.39564 11.9794C6.39835 11.8809 6.42043 11.7839 6.46062 11.6939C6.50082 11.604 6.55833 11.5228 6.62989 11.4551C6.70145 11.3874 6.78565 11.3344 6.87768 11.2992C6.96971 11.264 7.06777 11.2473 7.16626 11.25H9.37501C9.48521 11.2516 9.59441 11.2289 9.69484 11.1835C9.79527 11.1381 9.88447 11.0712 9.95607 10.9874C10.0277 10.9036 10.0799 10.8051 10.1091 10.6988C10.1383 10.5925 10.1438 10.4811 10.125 10.3725Z"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0">
                                                <rect width="18" height="18" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>{{ __('text.common_moneyback') }}
                            </div>
                            <div class="header-item__text">{{ __('text.common_refund') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-bottom-row">
                <div class="container">
                    <form class="search-form" action="{{ route('search.search_product') }}" method="post">
                        @csrf
                        <div class="search search-bar" style="width: 100%">
                            <label class="search-form__label">
                                <input class="search-form__input input-text ac_input" id="autocomplete" type="text" placeholder="{{ __('text.common_search') }}" name="search_text" required>
                            </label>
                            <button class="search-form__button" aria-label="Search"></button>
                        </div>
                    </form>

                    <div class="dropdown index-dropdown">
                        <button class="dropdown-toggler index-button" aria-label="Show dropdown">
                            <span class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#candy-box') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M4.99984 9.99999C4.99984 10.9205 4.25365 11.6667 3.33317 11.6667C2.4127 11.6667 1.6665 10.9205 1.6665 9.99999C1.6665 9.07951 2.4127 8.33332 3.33317 8.33332C4.25365 8.33332 4.99984 9.07951 4.99984 9.99999Z" fill="#B2B2B2"/>
                                        <path d="M11.6665 9.99999C11.6665 10.9205 10.9203 11.6667 9.99984 11.6667C9.07937 11.6667 8.33317 10.9205 8.33317 9.99999C8.33317 9.07951 9.07937 8.33332 9.99984 8.33332C10.9203 8.33332 11.6665 9.07951 11.6665 9.99999Z" fill="#B2B2B2"/>
                                        <path d="M18.3332 9.99999C18.3332 10.9205 17.587 11.6667 16.6665 11.6667C15.746 11.6667 14.9998 10.9205 14.9998 9.99999C14.9998 9.07952 15.746 8.33332 16.6665 8.33332C17.587 8.33332 18.3332 9.07952 18.3332 9.99999Z" fill="#B2B2B2"/>
                                        <path d="M4.99984 3.33332C4.99984 4.2538 4.25365 4.99999 3.33317 4.99999C2.4127 4.99999 1.6665 4.2538 1.6665 3.33332C1.6665 2.41285 2.4127 1.66666 3.33317 1.66666C4.25365 1.66666 4.99984 2.41285 4.99984 3.33332Z" fill="#B2B2B2"/>
                                        <path d="M11.6665 3.33332C11.6665 4.2538 10.9203 4.99999 9.99984 4.99999C9.07937 4.99999 8.33318 4.2538 8.33318 3.33332C8.33318 2.41285 9.07937 1.66666 9.99984 1.66666C10.9203 1.66666 11.6665 2.41285 11.6665 3.33332Z" fill="#B2B2B2"/>
                                        <path d="M18.3332 3.33332C18.3332 4.2538 17.587 4.99999 16.6665 4.99999C15.746 4.99999 14.9998 4.2538 14.9998 3.33332C14.9998 2.41285 15.746 1.66666 16.6665 1.66666C17.587 1.66666 18.3332 2.41285 18.3332 3.33332Z" fill="#B2B2B2"/>
                                        <path d="M4.99984 16.6667C4.99984 17.5871 4.25365 18.3333 3.33317 18.3333C2.4127 18.3333 1.6665 17.5871 1.6665 16.6667C1.6665 15.7462 2.4127 15 3.33317 15C4.25365 15 4.99984 15.7462 4.99984 16.6667Z" fill="#B2B2B2"/>
                                        <path d="M11.6665 16.6667C11.6665 17.5871 10.9203 18.3333 9.99984 18.3333C9.07937 18.3333 8.33318 17.5871 8.33318 16.6667C8.33318 15.7462 9.07937 15 9.99984 15C10.9203 15 11.6665 15.7462 11.6665 16.6667Z" fill="#B2B2B2"/>
                                        <path d="M18.3332 16.6667C18.3332 17.5871 17.587 18.3333 16.6665 18.3333C15.746 18.3333 14.9998 17.5871 14.9998 16.6667C14.9998 15.7462 15.746 15 16.6665 15C17.587 15 18.3332 15.7462 18.3332 16.6667Z" fill="#B2B2B2"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon is-hidden">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6L18.7742 18.7742" stroke="#B2B2B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 18.7742L18.7742 5.99998" stroke="#B2B2B2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </span>
                        </button>
                        <div class="dropdown-container">
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
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#visa') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/visa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#mastercard') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/mastercard.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#maestro') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/maestro.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#discover') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/discover.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#amex') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/amex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#jsb') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/jcb.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jsb' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#unionpay') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/union-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' unionpay' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#dinners-club') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/dinners-club.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#apple-pay') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/apple-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#google-pay') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/google-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#amazon-pay') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/amazon-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#stripe') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/stripe.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#paypal') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/paypal.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#sepa') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/sepa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#cashapp') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/cashapp.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#adyen') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/adyen.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#skrill') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/skrill.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#worldpay') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/worldpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#payline') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/payline.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#bitcoin') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/bitcoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#binance-coin') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/binance-coin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#ethereum') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/ethereum.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#litecoin') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/litecoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#tron') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/tron.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#usdt(erc20)') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/usdt_erc20.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                            @endif
                        </div>
                        <div class="promos-payment-method">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                                    <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite_gray.svg#usdt(trc20)') }}">
                                </svg>
                            @else
                                <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/gray/usdt_trc20.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("pub_images/checkup_img/black/checkup_big.png") }}">
        </div>

        @yield('content')

        <div class="sup-footer">
            <div class="subscribe">
                <div class="container">
                    <div class="subscribe__caption">
                        <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                        <div class="subscribe__text">{{ __('text.common_spec_offer') }}</div>
                    </div>
                    <form class="subscribe-form">
                        <label class="subscribe-form__label">
                            <input class="subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                        </label>
                        <button class="subscribe-form__button button button_sub" type="button">
                            <img src="{{ asset($design . '/images/icons/subscribe_mini.svg') }}" class="sub_mini">
                            <div class="sub_text">
                                {{ __('text.common_subscribe') }}
                            </div>
                        </button>
                    </form>
                </div>
            </div>

            @yield('rewies')
        </div>

        <footer class="footer">
            <div class="container">
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="footer__wrapper">
                        <a class="footer__logo" href="{{ route('home.index') }}">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                            @else
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
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
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="{{ $domainWithoutZone }}">
                            @else
                                <img src="{{ asset($design . '/svg/logo.svg') }}" width="152" height="32" alt="Logo">
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

                <div class="footer__copyrights vw-container">
                    {{ __('text.license_text_license1_1') }}
                    {{ $domain }}
                    {{ __('text.license_text_license1_2') }}
                    {{ __('text.license_text_license2_d12') }}
                </div>
                <div class="footer-buttons">
                    <div class="footer-buttons__container">
                        <a class="footer-button" href="{{ route('home.index') }}">
                            <span class="icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#home') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 19 19" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M7.95 16.8V11.4H11.55V16.8H16.05V9.6H18.75L9.75 1.5L0.75 9.6H3.45V16.8H7.95Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                        </a>
                        <button class="footer-button footer-button--cat">
                            <span class="icon button__fries-icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#fries') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 21 21" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M3 3.5C2.58579 3.5 2.25 3.83579 2.25 4.25C2.25 4.66421 2.58579 5 3 5H17C17.4142 5 17.75 4.66421 17.75 4.25C17.75 3.83579 17.4142 3.5 17 3.5H3Z"/>
                                        <path d="M3 9.5C2.58579 9.5 2.25 9.83579 2.25 10.25C2.25 10.6642 2.58579 11 3 11H9C9.41421 11 9.75 10.6642 9.75 10.25C9.75 9.83579 9.41421 9.5 9 9.5H3Z"/>
                                        <path d="M3 15.5C2.58579 15.5 2.25 15.8358 2.25 16.25C2.25 16.6642 2.58579 17 3 17H17C17.4142 17 17.75 16.6642 17.75 16.25C17.75 15.8358 17.4142 15.5 17 15.5H3Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon button__close-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#close-thin') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#profile-circle-2') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M1.0415 10C1.0415 5.05245 5.05229 1.04167 9.99984 1.04167C14.9474 1.04167 18.9582 5.05245 18.9582 10C18.9582 12.5404 17.8999 14.8347 16.2019 16.4642C14.5929 18.0084 12.4067 18.9583 9.99984 18.9583C7.59302 18.9583 5.40678 18.0084 3.79776 16.4642C2.09975 14.8347 1.0415 12.5404 1.0415 10ZM15.0691 15.8071C14.7099 14.7327 13.6947 13.9583 12.4998 13.9583H7.49984C6.30495 13.9583 5.28976 14.7327 4.93056 15.8071C6.28611 16.9915 8.05869 17.7083 9.99984 17.7083C11.941 17.7083 13.7136 16.9915 15.0691 15.8071ZM9.99984 3.54167C7.81371 3.54167 6.0415 5.31388 6.0415 7.5C6.0415 9.68613 7.81371 11.4583 9.99984 11.4583C12.186 11.4583 13.9582 9.68613 13.9582 7.5C13.9582 5.31388 12.186 3.54167 9.99984 3.54167Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_profile') }}</span>
                        </a>
                        <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                            <span class="icon icon--grad">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?t0q3xoa5#shopping_cart') }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M8 18C6.9 18 6.01 18.9 6.01 20C6.01 21.1 6.9 22 8 22C9.1 22 10 21.1 10 20C10 18.9 9.1 18 8 18ZM2 2V4H4L7.6 11.59L6.25 14.04C6.09 14.32 6 14.65 6 15C6 16.1 6.9 17 8 17H20V15H8.42C8.28 15 8.17 14.89 8.17 14.75L8.2 14.63L9.1 13H16.55C17.3 13 17.96 12.59 18.3 11.97L21.88 5.48C21.96 5.34 22 5.17 22 5C22 4.45 21.55 4 21 4H6.21L5.27 2H2ZM18 18C16.9 18 16.01 18.9 16.01 20C16.01 21.1 16.9 22 18 22C19.1 22 20 21.1 20 20C20 18.9 19.1 18 18 18Z"/>
                                    </svg>
                                @endif
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
                        <button class="button form__submit button_request_call" type="submit">{{ __('text.common_callback') }}</button>
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

            const pathImageCheckupBiggest = "{{ asset('pub_images/checkup_img/black/checkup_biggest.png') }}";
            const pathImageCheckupBig = "{{ asset('pub_images/checkup_img/black/checkup_big.png') }}";
            const pathImageCheckupMiddle = "{{ asset('pub_images/checkup_img/black/checkup_middle.png') }}";
            const pathImageCheckupSmall = "{{ asset('pub_images/checkup_img/black/checkup_small.png') }}";

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