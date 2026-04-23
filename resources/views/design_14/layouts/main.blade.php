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
        <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>
        {!! isset($pixel) ? $pixel : '' !!}
    </head>

    <body class="@yield('page_name')">
        <script>
            let flagc = false;
            let flagp = false;
            const design = 14;
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

        <header class="header">
            <div class="topbar">
                <div class="container">
                    <button class="link link--white topbar-categories-button" data-cat-nav-opener>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                    <path d="M10.7652 13.3384C10.3528 13.7848 9.64734 13.7848 9.23492 13.3384L5.84177 9.66516C5.22546 8.99799 5.69866 7.91667 6.60693 7.91667L13.3932 7.91667C14.3015 7.91667 14.7747 8.99799 14.1584 9.66516L10.7652 13.3384Z" fill="currentColor"/>
                                </svg>
                            @endif
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
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                        <path d="M7.34375 1.08594C6.80111 1.08594 6.30361 1.25789 5.8651 1.59703C5.44469 1.92217 5.16479 2.33629 5 2.63742C4.83521 2.33627 4.55531 1.92217 4.1349 1.59703C3.69639 1.25789 3.19889 1.08594 2.65625 1.08594C1.14195 1.08594 0 2.32455 0 3.96707C0 5.74156 1.42467 6.95564 3.58143 8.79359C3.94768 9.10572 4.36281 9.45951 4.7943 9.83684C4.85117 9.88664 4.92422 9.91406 5 9.91406C5.07578 9.91406 5.14883 9.88664 5.2057 9.83685C5.63723 9.45947 6.05234 9.1057 6.41881 8.7934C8.57533 6.95564 10 5.74156 10 3.96707C10 2.32455 8.85805 1.08594 7.34375 1.08594Z" fill="currentColor"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </summary>
                                        <div class="accordion-panel" data-accordion-panel>
                                            <div class="accordion-content content">
                                                <ul class="aside-nav__list">
                                                    @foreach ($bestsellers as $bestseller)
                                                        <li class="aside-nav__item">
                                                            <a class="aside-nav__link" rel="canonical" href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }} <span class="aside-nav__price">{{ $Currency::convert($bestseller['price'], false, true) }}</span></a>
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
                                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                        </svg>
                                                    @else
                                                        <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                            <path d="M7.34375 1.08594C6.80111 1.08594 6.30361 1.25789 5.8651 1.59703C5.44469 1.92217 5.16479 2.33629 5 2.63742C4.83521 2.33627 4.55531 1.92217 4.1349 1.59703C3.69639 1.25789 3.19889 1.08594 2.65625 1.08594C1.14195 1.08594 0 2.32455 0 3.96707C0 5.74156 1.42467 6.95564 3.58143 8.79359C3.94768 9.10572 4.36281 9.45951 4.7943 9.83684C4.85117 9.88664 4.92422 9.91406 5 9.91406C5.07578 9.91406 5.14883 9.88664 5.2057 9.83685C5.63723 9.45947 6.05234 9.1057 6.41881 8.7934C8.57533 6.95564 10 5.74156 10 3.96707C10 2.32455 8.85805 1.08594 7.34375 1.08594Z" fill="currentColor"/>
                                                        </svg>
                                                    @endif
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
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                    <path d="M7.34375 1.08594C6.80111 1.08594 6.30361 1.25789 5.8651 1.59703C5.44469 1.92217 5.16479 2.33629 5 2.63742C4.83521 2.33627 4.55531 1.92217 4.1349 1.59703C3.69639 1.25789 3.19889 1.08594 2.65625 1.08594C1.14195 1.08594 0 2.32455 0 3.96707C0 5.74156 1.42467 6.95564 3.58143 8.79359C3.94768 9.10572 4.36281 9.45951 4.7943 9.83684C4.85117 9.88664 4.92422 9.91406 5 9.91406C5.07578 9.91406 5.14883 9.88664 5.2057 9.83685C5.63723 9.45947 6.05234 9.1057 6.41881 8.7934C8.57533 6.95564 10 5.74156 10 3.96707C10 2.32455 8.85805 1.08594 7.34375 1.08594Z" fill="currentColor"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                </li>
                                @foreach ($menu as $category)
                                    <li class="nav__item">
                                        <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#heart") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                        <path d="M7.34375 1.08594C6.80111 1.08594 6.30361 1.25789 5.8651 1.59703C5.44469 1.92217 5.16479 2.33629 5 2.63742C4.83521 2.33627 4.55531 1.92217 4.1349 1.59703C3.69639 1.25789 3.19889 1.08594 2.65625 1.08594C1.14195 1.08594 0 2.32455 0 3.96707C0 5.74156 1.42467 6.95564 3.58143 8.79359C3.94768 9.10572 4.36281 9.45951 4.7943 9.83684C4.85117 9.88664 4.92422 9.91406 5 9.91406C5.07578 9.91406 5.14883 9.88664 5.2057 9.83685C5.63723 9.45947 6.05234 9.1057 6.41881 8.7934C8.57533 6.95564 10 5.74156 10 3.96707C10 2.32455 8.85805 1.08594 7.34375 1.08594Z" fill="currentColor"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="categories-sublists">
                                <ul class="nav__sublist categories-sublist" data-sublist-index="0">
                                    <li class="nav__item nav__item--return">
                                        <button class="nav__mobile-return">
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
                                        <a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a>
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
                                        <a class="nav__link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a>
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </span>
                            </button>
                            <div class="dropdown" data-dropdown data-fixed-dropdown>
                                <button class="link greedy-button" data-dropdown-button aria-label="Show dropdown">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#fi-sr-menu-dots-vertical") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 4 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#fi-sr-menu-dots-vertical_clip0_2031_2178)">
                                                    <path d="M2.00034 2.66669C2.73673 2.66669 3.33369 2.06973 3.33369 1.33334C3.33369 0.596958 2.73673 0 2.00034 0C1.26395 0 0.666992 0.596958 0.666992 1.33334C0.666992 2.06973 1.26395 2.66669 2.00034 2.66669Z" fill="#B2B2B2"/>
                                                    <path d="M2.00034 9.33334C2.73673 9.33334 3.33369 8.73638 3.33369 8C3.33369 7.26361 2.73673 6.66665 2.00034 6.66665C1.26395 6.66665 0.666992 7.26361 0.666992 8C0.666992 8.73638 1.26395 9.33334 2.00034 9.33334Z" fill="#B2B2B2"/>
                                                    <path d="M2.00034 16C2.73673 16 3.33369 15.403 3.33369 14.6666C3.33369 13.9303 2.73673 13.3333 2.00034 13.3333C1.26395 13.3333 0.666992 13.9303 0.666992 14.6666C0.666992 15.403 1.26395 16 2.00034 16Z" fill="#B2B2B2"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="fi-sr-menu-dots-vertical_clip0_2031_2178">
                                                    <rect width="3.55556" height="16" fill="white" transform="translate(0.222656)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="icon is-hidden">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @endif
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M10.7652 13.3384C10.3528 13.7848 9.64734 13.7848 9.23492 13.3384L5.84177 9.66516C5.22546 8.99799 5.69866 7.91667 6.60693 7.91667L13.3932 7.91667C14.3015 7.91667 14.7747 8.99799 14.1584 9.66516L10.7652 13.3384Z" fill="currentColor"/>
                                        </svg>
                                    @endif
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M10.7652 13.3384C10.3528 13.7848 9.64734 13.7848 9.23492 13.3384L5.84177 9.66516C5.22546 8.99799 5.69866 7.91667 6.60693 7.91667L13.3932 7.91667C14.3015 7.91667 14.7747 8.99799 14.1584 9.66516L10.7652 13.3384Z" fill="currentColor"/>
                                        </svg>
                                    @endif
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#caret-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M10.7652 13.3384C10.3528 13.7848 9.64734 13.7848 9.23492 13.3384L5.84177 9.66516C5.22546 8.99799 5.69866 7.91667 6.60693 7.91667L13.3932 7.91667C14.3015 7.91667 14.7747 8.99799 14.1584 9.66516L10.7652 13.3384Z" fill="currentColor"/>
                                        </svg>
                                    @endif
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#candy-box") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M5 7C4.60444 7 4.21776 6.8827 3.88886 6.66294C3.55996 6.44318 3.30362 6.13082 3.15224 5.76537C3.00087 5.39992 2.96126 4.99778 3.03843 4.60982C3.1156 4.22186 3.30608 3.86549 3.58579 3.58579C3.86549 3.30608 4.22186 3.1156 4.60982 3.03843C4.99778 2.96126 5.39992 3.00087 5.76537 3.15224C6.13082 3.30362 6.44318 3.55996 6.66294 3.88886C6.8827 4.21776 7 4.60444 7 5C7 5.53043 6.78929 6.03914 6.41421 6.41421C6.03914 6.78929 5.53043 7 5 7ZM14 5C14 4.60444 13.8827 4.21776 13.6629 3.88886C13.4432 3.55996 13.1308 3.30362 12.7654 3.15224C12.3999 3.00087 11.9978 2.96126 11.6098 3.03843C11.2219 3.1156 10.8655 3.30608 10.5858 3.58579C10.3061 3.86549 10.1156 4.22186 10.0384 4.60982C9.96126 4.99778 10.0009 5.39992 10.1522 5.76537C10.3036 6.13082 10.56 6.44318 10.8889 6.66294C11.2178 6.8827 11.6044 7 12 7C12.5304 7 13.0391 6.78929 13.4142 6.41421C13.7893 6.03914 14 5.53043 14 5ZM21 5C21 4.60444 20.8827 4.21776 20.6629 3.88886C20.4432 3.55996 20.1308 3.30362 19.7654 3.15224C19.3999 3.00087 18.9978 2.96126 18.6098 3.03843C18.2219 3.1156 17.8655 3.30608 17.5858 3.58579C17.3061 3.86549 17.1156 4.22186 17.0384 4.60982C16.9613 4.99778 17.0009 5.39992 17.1522 5.76537C17.3036 6.13082 17.56 6.44318 17.8889 6.66294C18.2178 6.8827 18.6044 7 19 7C19.5304 7 20.0391 6.78929 20.4142 6.41421C20.7893 6.03914 21 5.53043 21 5ZM7 12C7 11.6044 6.8827 11.2178 6.66294 10.8889C6.44318 10.56 6.13082 10.3036 5.76537 10.1522C5.39992 10.0009 4.99778 9.96126 4.60982 10.0384C4.22186 10.1156 3.86549 10.3061 3.58579 10.5858C3.30608 10.8655 3.1156 11.2219 3.03843 11.6098C2.96126 11.9978 3.00087 12.3999 3.15224 12.7654C3.30362 13.1308 3.55996 13.4432 3.88886 13.6629C4.21776 13.8827 4.60444 14 5 14C5.53043 14 6.03914 13.7893 6.41421 13.4142C6.78929 13.0391 7 12.5304 7 12ZM14 12C14 11.6044 13.8827 11.2178 13.6629 10.8889C13.4432 10.56 13.13082 10.3036 12.7654 10.1522C12.3999 10.0009 11.9978 9.96126 11.6098 10.0384C11.2219 10.1156 10.8655 10.3061 10.5858 10.5858C10.3061 10.8655 10.1156 11.2219 10.0384 11.6098C9.96126 11.9978 10.0009 12.3999 10.1522 12.7654C10.3036 13.1308 10.56 13.4432 10.8889 13.6629C11.2178 13.8827 11.6044 14 12 14C12.5304 14 13.0391 13.7893 13.4142 13.4142C13.7893 13.0391 14 12.5304 14 12ZM21 12C21 11.6044 20.8827 11.2178 20.6629 10.8889C20.4432 10.56 20.1308 10.3036 19.7654 10.1522C19.3999 10.0009 18.9978 9.96126 18.6098 10.0384C18.2219 10.1156 17.8655 10.3061 17.5858 10.5858C17.3061 10.8655 17.1156 11.2219 17.0384 11.6098C16.9613 11.9978 17.0009 12.3999 17.1522 12.7654C17.3036 13.1308 17.56 13.4432 17.8889 13.6629C18.2178 13.8827 18.6044 14 19 14C19.5304 14 20.0391 13.7893 20.4142 13.4142C20.7893 13.0391 21 12.5304 21 12ZM7 19C7 18.6044 6.8827 18.2178 6.66294 17.8889C6.44318 17.56 6.13082 17.3036 5.76537 17.1522C5.39992 17.0009 4.99778 16.9613 4.60982 17.0384C4.22186 17.1156 3.86549 17.3061 3.58579 17.5858C3.30608 17.8655 3.1156 18.2219 3.03843 18.6098C2.96126 18.9978 3.00087 19.3999 3.15224 19.7654C3.30362 20.1308 3.55996 20.4432 3.88886 20.6629C4.21776 20.8827 4.60444 21 5 21C5.53043 21 6.03914 20.7893 6.41421 20.4142C6.78929 20.0391 7 19.5304 7 19ZM14 19C14 18.6044 13.8827 18.2178 13.6629 17.8889C13.4432 17.56 13.13082 17.3036 12.7654 17.1522C12.3999 17.0009 11.9978 16.9613 11.6098 17.0384C11.2219 17.1156 10.8655 17.3061 10.5858 17.5858C10.3061 17.8655 10.1156 18.2219 10.0384 18.6098C9.96126 18.9978 10.0009 19.3999 10.1522 19.7654C10.3036 20.1308 10.56 20.4432 10.8889 20.6629C11.2178 20.8827 11.6044 21 12 21C12.5304 21 13.0391 20.7893 13.4142 20.4142C13.7893 20.0391 14 19.5304 14 19ZM21 19C21 18.6044 20.8827 18.2178 20.6629 17.8889C20.4432 17.56 20.1308 17.3036 19.7654 17.1522C19.3999 17.0009 18.9978 16.9613 18.6098 17.0384C18.2219 17.1156 17.8655 17.3061 17.5858 17.5858C17.3061 17.8655 17.1156 18.2219 17.0384 18.6098C16.9613 18.9978 17.0009 19.3999 17.1522 19.7654C17.3036 20.1308 17.56 20.4432 17.8889 20.6629C18.2178 20.8827 18.6044 21 19 21C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon is-hidden">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#search") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M8.25 15C11.9779 15 15 11.9779 15 8.25C15 4.52208 11.9779 1.5 8.25 1.5C4.52208 1.5 1.5 4.52208 1.5 8.25C1.5 11.9779 4.52208 15 8.25 15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.5 16.5L13.5 13.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
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
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.125 17.8926C8.52344 17.8926 8 18.4002 8 19.0711C8 19.742 8.52344 20.2496 9.125 20.2496C9.72656 20.2496 10.25 19.742 10.25 19.0711C10.25 18.4002 9.72656 17.8926 9.125 17.8926ZM6.5 19.0711C6.5 17.6118 7.65549 16.3926 9.125 16.3926C10.5945 16.3926 11.75 17.6118 11.75 19.0711C11.75 20.5304 10.5945 21.7496 9.125 21.7496C7.65549 21.7496 6.5 20.5304 6.5 19.0711Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.625 17.8926C16.0234 17.8926 15.5 18.4002 15.5 19.0711C15.5 19.742 16.0234 20.2496 16.625 20.2496C17.2266 20.2496 17.75 19.742 17.75 19.0711C17.75 18.4002 17.2266 17.8926 16.625 17.8926ZM14 19.0711C14 17.6118 15.1555 16.3926 16.625 16.3926C18.0945 16.3926 19.25 17.6118 19.25 19.0711C19.25 20.5304 18.0945 21.7496 16.625 21.7496C15.1555 21.7496 14 20.5304 14 19.0711Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.125 3C2.125 2.58579 2.46079 2.25 2.875 2.25H5.02856C5.66521 2.25 6.20014 2.72849 6.27083 3.3612L7.29587 12.5355H19.0229C19.1313 12.5355 19.2273 12.4656 19.2607 12.3625L21.1125 6.64844C21.1649 6.48695 21.0445 6.32137 20.8747 6.32137H9.125C8.71079 6.32137 8.375 5.98558 8.375 5.57137C8.375 5.15715 8.71079 4.82137 9.125 4.82137H20.8747C22.063 4.82137 22.9058 5.98043 22.5395 7.11088L20.6876 12.825C20.4538 13.5467 19.7815 14.0355 19.0229 14.0355H6.82908L5.7983 14.6413C5.72808 14.6826 5.70059 14.7267 5.68722 14.7629C5.67143 14.8058 5.66737 14.8623 5.68374 14.9225C5.70012 14.9826 5.73226 15.0293 5.76758 15.0582C5.79748 15.0827 5.84352 15.1068 5.92497 15.1068H18.5C18.9142 15.1068 19.25 15.4426 19.25 15.8568C19.25 16.271 18.9142 16.6068 18.5 16.6068H5.92497C4.14172 16.6068 3.50086 14.2517 5.03824 13.3481L5.82563 12.8853L4.80494 3.75H2.875C2.46079 3.75 2.125 3.41421 2.125 3Z" fill="currentColor"/>
                            </svg>
                        @endif
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
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_ref_menu') }}</a></li>
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
                                        <li class="nav__item"><a class="nav__link" href="{{ route('home.bonus_referral_program', '') }}">{{ __('text.bonus_ref_menu') }}</a></li>
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#home") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1517 2.22003C12.1078 1.53703 13.3923 1.53701 14.3485 2.21997L21.5113 7.33605C22.7596 8.22767 23.5005 9.66728 23.5005 11.2013V19C23.5005 21.071 21.8216 22.75 19.7505 22.75H16.8929C15.3741 22.75 14.1429 21.5187 14.1429 20V18.5C14.1429 18.0857 13.8071 17.75 13.3929 17.75H12.1072C11.693 17.75 11.3572 18.0857 11.3572 18.5V20C11.3572 21.5187 10.126 22.75 8.60721 22.75H5.75049C3.67942 22.75 2.00049 21.071 2.00049 19V11.2012C2.00049 9.66725 2.74129 8.22769 3.98953 7.33606L11.1517 2.22003ZM13.4766 3.44058C13.042 3.13015 12.4582 3.13016 12.0236 3.44061L4.86141 8.55664C4.00735 9.16671 3.50049 10.1517 3.50049 11.2012V19C3.50049 20.2426 4.50785 21.25 5.75049 21.25H8.60721C9.29757 21.25 9.85721 20.6903 9.85721 20V18.5C9.85721 17.2573 10.8646 16.25 12.1072 16.25H13.3929C14.6356 16.25 15.6429 17.2573 15.6429 18.5V20C15.6429 20.6903 16.2026 21.25 16.8929 21.25H19.7505C20.9931 21.25 22.0005 20.2426 22.0005 19V11.2013C22.0005 10.1517 21.4936 9.16672 20.6395 8.55666L13.4766 3.44058Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_home_main_menu_item') }}</span>
                        </a>
                        <button class="footer-button footer-button--cat" data-cat-nav-opener>
                            <span class="icon button__category-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#category") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 5C1.5 2.92893 3.17893 1.25 5.25 1.25H7.82143C9.8925 1.25 11.5714 2.92893 11.5714 5V7.57143C11.5714 9.6425 9.8925 11.3214 7.82143 11.3214H5.25C3.17893 11.3214 1.5 9.6425 1.5 7.57143V5ZM5.25 2.75C4.00736 2.75 3 3.75736 3 5V7.57143C3 8.81407 4.00736 9.82143 5.25 9.82143H7.82143C9.06407 9.82143 10.0714 8.81407 10.0714 7.57143V5C10.0714 3.75736 9.06407 2.75 7.82143 2.75H5.25Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9287 5C12.9287 2.92893 14.6076 1.25 16.6787 1.25H19.2501C21.3212 1.25 23.0001 2.92893 23.0001 5V7.57143C23.0001 9.6425 21.3212 11.3214 19.2501 11.3214H16.6787C14.6076 11.3214 12.9287 9.6425 12.9287 7.57143V5ZM16.6787 2.75C15.4361 2.75 14.4287 3.75736 14.4287 5V7.57143C14.4287 8.81407 15.4361 9.82143 16.6787 9.82143H19.2501C20.4928 9.82143 21.5001 8.81407 21.5001 7.57143V5C21.5001 3.75736 20.4928 2.75 19.2501 2.75H16.6787Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.9287 16.4285C12.9287 14.3574 14.6076 12.6785 16.6787 12.6785H19.2501C21.3212 12.6785 23.0001 14.3574 23.0001 16.4285V18.9999C23.0001 21.071 21.3212 22.7499 19.2501 22.7499H16.6787C14.6076 22.7499 12.9287 21.071 12.9287 18.9999V16.4285ZM16.6787 14.1785C15.4361 14.1785 14.4287 15.1858 14.4287 16.4285V18.9999C14.4287 20.2425 15.4361 21.2499 16.6787 21.2499H19.2501C20.4928 21.2499 21.5001 20.2425 21.5001 18.9999V16.4285C21.5001 15.1858 20.4928 14.1785 19.2501 14.1785H16.6787Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.5 16.4285C1.5 14.3574 3.17893 12.6785 5.25 12.6785H7.82143C9.8925 12.6785 11.5714 14.3574 11.5714 16.4285V18.9999C11.5714 21.071 9.8925 22.7499 7.82143 22.7499H5.25C3.17893 22.7499 1.5 21.071 1.5 18.9999V16.4285ZM5.25 14.1785C4.00736 14.1785 3 15.1858 3 16.4285V18.9999C3 20.2425 4.00736 21.2499 5.25 21.2499H7.82143C9.06407 21.2499 10.0714 20.2425 10.0714 18.9999V16.4285C10.0714 15.1858 9.06407 14.1785 7.82143 14.1785H5.25Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon button__close-icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        </button>
                        <a class="footer-button" href="{{ route('home.login') }}" target="_blank">
                            <span class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#user") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12.7496 2.75C10.7969 2.75 9.21387 4.33299 9.21387 6.28571C9.21387 8.23844 10.7969 9.82143 12.7496 9.82143C14.7023 9.82143 16.2853 8.23844 16.2853 6.28571C16.2853 4.33299 14.7023 2.75 12.7496 2.75ZM7.71387 6.28571C7.71387 3.50457 9.96843 1.25 12.7496 1.25C15.5307 1.25 17.7853 3.50457 17.7853 6.28571C17.7853 9.06686 15.5307 11.3214 12.7496 11.3214C9.96843 11.3214 7.71387 9.06686 7.71387 6.28571Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.05958 16.1661C6.61682 14.5023 9.14944 13.25 13.0357 13.25C16.922 13.25 19.4546 14.5023 21.0118 16.1661C22.5495 17.8088 23.0714 19.7847 23.0714 21.1429C23.0714 21.5571 22.7356 21.8929 22.3214 21.8929H3.75C3.33579 21.8929 3 21.5571 3 21.1429C3 19.7847 3.52196 17.8088 5.05958 16.1661ZM4.56378 20.3929H21.5076C21.3516 19.4301 20.89 18.2309 19.9167 17.1911C18.6882 15.8786 16.578 14.75 13.0357 14.75C9.49342 14.75 7.38318 15.8786 6.15471 17.1911C5.18143 18.2309 4.71986 19.4301 4.56378 20.3929Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="button__text">{{ __('text.common_profile') }}</span>
                        </a>
                        <a class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                            <span class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#cart") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.125 17.8926C8.52344 17.8926 8 18.4002 8 19.0711C8 19.742 8.52344 20.2496 9.125 20.2496C9.72656 20.2496 10.25 19.742 10.25 19.0711C10.25 18.4002 9.72656 17.8926 9.125 17.8926ZM6.5 19.0711C6.5 17.6118 7.65549 16.3926 9.125 16.3926C10.5945 16.3926 11.75 17.6118 11.75 19.0711C11.75 20.5304 10.5945 21.7496 9.125 21.7496C7.65549 21.7496 6.5 20.5304 6.5 19.0711Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.625 17.8926C16.0234 17.8926 15.5 18.4002 15.5 19.0711C15.5 19.742 16.0234 20.2496 16.625 20.2496C17.2266 20.2496 17.75 19.742 17.75 19.0711C17.75 18.4002 17.2266 17.8926 16.625 17.8926ZM14 19.0711C14 17.6118 15.1555 16.3926 16.625 16.3926C18.0945 16.3926 19.25 17.6118 19.25 19.0711C19.25 20.5304 18.0945 21.7496 16.625 21.7496C15.1555 21.7496 14 20.5304 14 19.0711Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.125 3C2.125 2.58579 2.46079 2.25 2.875 2.25H5.02856C5.66521 2.25 6.20014 2.72849 6.27083 3.3612L7.29587 12.5355H19.0229C19.1313 12.5355 19.2273 12.4656 19.2607 12.3625L21.1125 6.64844C21.1649 6.48695 21.0445 6.32137 20.8747 6.32137H9.125C8.71079 6.32137 8.375 5.98558 8.375 5.57137C8.375 5.15715 8.71079 4.82137 9.125 4.82137H20.8747C22.063 4.82137 22.9058 5.98043 22.5395 7.11088L20.6876 12.825C20.4538 13.5467 19.7815 14.0355 19.0229 14.0355H6.82908L5.7983 14.6413C5.72808 14.6826 5.70059 14.7267 5.68722 14.7629C5.67143 14.8058 5.66737 14.8623 5.68374 14.9225C5.70012 14.9826 5.73226 15.0293 5.76758 15.0582C5.79748 15.0827 5.84352 15.1068 5.92497 15.1068H18.5C18.9142 15.1068 19.25 15.4426 19.25 15.8568C19.25 16.271 18.9142 16.6068 18.5 16.6068H5.92497C4.14172 16.6068 3.50086 14.2517 5.03824 13.3481L5.82563 12.8853L4.80494 3.75H2.875C2.46079 3.75 2.125 3.41421 2.125 3Z" fill="currentColor"/>
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
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                <path d="M6 6.00003L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 18.7742L18.7742 6.00001" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        @endif
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
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?pqvdu970#close") }}"></use>
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

        <script defer src="{{ asset_ver("$design/js/main.7dfb0a3d.js") }}"></script>
        <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
        <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>