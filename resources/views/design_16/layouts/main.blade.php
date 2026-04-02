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
        <meta name="description" content="@yield('description', 'Description')">
        <meta name="keywords" content="@yield('keywords', 'Keywords')">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#282828" />
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

        @if (env('APP_PWA', 0))
            <link rel="manifest" href="{{ asset($design . '/img/favicon/manifest.webmanifest') }}">
            <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
        @endif

        {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

        {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

        <link href="{{ asset($design . '/fonts/space-grotesk-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/space-grotesk-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/space-grotesk-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

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
            const design = 16;
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

        <header class="header" data-sticky>
            <div class="topbar" data-sticky-offset>
                <div class="container">
                    <button class="link link--white topbar-categories-button" data-cat-nav-opener>
                        <span class="button__text">{{ __('text.common_categories_menu') }}</span>
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-down-small") }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                    <path d="M12.4425 6.22125L9 9.65625L5.5575 6.22125L4.5 7.27875L9 11.7788L13.5 7.27875L12.4425 6.22125Z" fill="currentColor" />
                                </svg>
                            @endif
                        </span>
                    </button>

                    <nav class="nav cat-nav">
                        <div class="nav-container">
                            <div class="nav__heading">
                                <button class="nav__heading-button" data-cat-nav-close>
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-left-small") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <path d="M15.7051 7.41L11.1251 12L15.7051 16.59L14.2951 18L8.29508 12L14.2951 6L15.7051 7.41Z" fill="currentColor" />
                                            </svg>
                                        @endif
                                    </span>
                                    {{ __('text.common_categories_menu') }}
                                </button>
                            </div>
                            <button class="nav__close-button" aria-label="Close categories" data-cat-nav-close></button>
                            <ul class="nav__list">
                                <li class="nav__item">
                                    <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                        <span class="icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="1em" height="1em"  fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#paw") }}"></use>
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                    <g clip-path="url(#paw_clip0)">
                                                        <path d="M15.5154 6.7575C15.249 6.20402 14.8514 5.72436 14.3573 5.36045C13.8632 4.99654 13.2876 4.75948 12.6809 4.67C11.3186 4.495 8.95567 5.4075 7.85282 7.225C8.99903 7.60681 9.99433 8.34401 10.6948 9.33C10.7125 9.35769 10.7245 9.38863 10.7302 9.42101C10.7359 9.45339 10.7351 9.48658 10.7279 9.51866C10.7207 9.55074 10.7072 9.58107 10.6883 9.6079C10.6693 9.63474 10.6452 9.65754 10.6174 9.675C10.5613 9.70956 10.4939 9.7208 10.4295 9.70633C10.3652 9.69186 10.3091 9.65281 10.2731 9.5975C9.62844 8.67483 8.69371 7.9953 7.61828 7.6675C7.61828 7.67 7.6158 7.67 7.6158 7.6725C6.77867 7.43434 5.89443 7.41626 5.04831 7.62C5.04832 7.61967 5.04826 7.61934 5.04813 7.61903C5.04801 7.61873 5.04783 7.61845 5.04759 7.61822C5.04736 7.61798 5.04708 7.6178 5.04678 7.61768C5.04647 7.61755 5.04614 7.61749 5.04581 7.6175C4.6103 7.72074 4.18518 7.86384 3.7758 8.045C3.7458 8.05842 3.71345 8.06577 3.6806 8.06664C3.64776 8.06751 3.61507 8.06188 3.5844 8.05007C3.55374 8.03825 3.5257 8.02049 3.5019 7.9978C3.4781 7.9751 3.459 7.94793 3.4457 7.91783C3.4324 7.88772 3.42517 7.85529 3.4244 7.82238C3.42364 7.78947 3.42937 7.75673 3.44126 7.72604C3.45315 7.69535 3.47097 7.66732 3.49369 7.64355C3.51642 7.61977 3.5436 7.60073 3.57369 7.5875C3.97043 7.412 4.38105 7.26984 4.80129 7.1625C4.3437 6.10402 4.14769 4.95056 4.2299 3.8L5.17305 3.9875L4.76635 2.7225C4.74959 2.66997 4.75052 2.61337 4.76901 2.56143C4.78749 2.50948 4.8225 2.46506 4.86865 2.435L5.47247 2.04C5.52805 2.00419 5.59554 1.9919 5.66014 2.00582C5.72475 2.01975 5.78122 2.05876 5.81719 2.1143C5.85316 2.16985 5.8657 2.23741 5.85206 2.3022C5.83843 2.36699 5.79973 2.42373 5.74444 2.46L5.30031 2.75L5.63964 3.81L6.13867 3.51C6.38573 3.36321 6.58522 3.14805 6.71315 2.89037C6.84107 2.63269 6.89198 2.34348 6.85976 2.0575C6.73271 1.33036 6.41592 0.649833 5.94155 0.0849984C5.91327 0.0524463 5.87695 0.0279189 5.83622 0.0138773C5.79549 -0.00016441 5.75179 -0.00322645 5.70951 0.00499838L2.96487 0.504998C2.91113 0.51496 2.86209 0.542162 2.82514 0.582498C0.215237 3.46 0.0231124 10.4475 0.015627 10.745C0.0154028 10.8018 0.0338117 10.8572 0.0680247 10.9025C0.185296 11.0575 3.03972 14.7375 7.48604 15H7.50101C7.51609 14.9996 7.53111 14.9979 7.54592 14.995C8.39365 14.8085 9.17702 14.4005 9.81648 13.8125C9.24168 13.5564 8.70911 13.2141 8.23707 12.7975C8.18925 12.7521 8.16114 12.6897 8.15881 12.6238C8.15648 12.5579 8.1801 12.4936 8.22459 12.445C8.27038 12.3971 8.33303 12.3689 8.39921 12.3666C8.46539 12.3643 8.52987 12.3879 8.5789 12.4325C9.05354 12.8535 9.59603 13.1908 10.1833 13.43L10.1858 13.4275C11.5182 13.965 13.6615 14.155 15.3432 11.1325C15.7585 10.4033 15.9801 9.57954 15.9869 8.74C15.9877 8.05118 15.8261 7.37191 15.5154 6.7575ZM3.63606 12.41C3.61351 12.4444 3.58279 12.4727 3.54665 12.4924C3.5105 12.512 3.47007 12.5224 3.42896 12.5225C3.37925 12.5223 3.33068 12.5075 3.28923 12.48C2.48019 11.9353 1.74108 11.293 1.08853 10.5675C1.06674 10.5429 1.05001 10.5142 1.03928 10.4831C1.02855 10.452 1.02405 10.4191 1.02601 10.3862C1.02798 10.3534 1.03639 10.3212 1.05076 10.2916C1.06512 10.262 1.08517 10.2356 1.10974 10.2137C1.13431 10.1919 1.16294 10.1751 1.19398 10.1644C1.22503 10.1537 1.25788 10.1491 1.29066 10.1511C1.32345 10.1531 1.35552 10.1615 1.38506 10.1759C1.41459 10.1903 1.44101 10.2104 1.4628 10.235C2.08569 10.9298 2.79222 11.5445 3.56619 12.065C3.62107 12.1015 3.65924 12.1583 3.67234 12.223C3.68544 12.2877 3.67239 12.3549 3.63606 12.41ZM14.5148 7.545C14.48 7.56259 14.4416 7.572 14.4025 7.5725C14.3563 7.57291 14.311 7.56021 14.2717 7.53588C14.2324 7.51155 14.2008 7.47657 14.1805 7.435C14.0246 7.10038 13.7895 6.80904 13.4955 6.58637C13.2015 6.3637 12.8576 6.21647 12.4938 6.1575C12.4611 6.15366 12.4295 6.14338 12.4009 6.12725C12.3722 6.11112 12.347 6.08946 12.3267 6.06352C12.3064 6.03757 12.2915 6.00786 12.2827 5.9761C12.274 5.94434 12.2716 5.91116 12.2757 5.87847C12.2798 5.84578 12.2904 5.81423 12.3067 5.78565C12.3231 5.75706 12.3449 5.73201 12.371 5.71193C12.3971 5.69184 12.4269 5.67714 12.4586 5.66866C12.4904 5.66017 12.5236 5.65808 12.5561 5.6625C13.0012 5.73069 13.4226 5.90751 13.7833 6.17737C14.144 6.44724 14.4329 6.80187 14.6246 7.21C14.6543 7.26902 14.6594 7.33745 14.6389 7.40025C14.6183 7.46306 14.5737 7.51512 14.5148 7.545Z" fill="currentColor" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="paw_clip0">
                                                        <rect width="16" height="15" fill="white" />
                                                        </clipPath>
                                                    </defs>
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
                                                    <svg width="1em" height="1em"  fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#paw") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"  fill="currentColor">
                                                        <g clip-path="url(#paw_clip0)">
                                                            <path d="M15.5154 6.7575C15.249 6.20402 14.8514 5.72436 14.3573 5.36045C13.8632 4.99654 13.2876 4.75948 12.6809 4.67C11.3186 4.495 8.95567 5.4075 7.85282 7.225C8.99903 7.60681 9.99433 8.34401 10.6948 9.33C10.7125 9.35769 10.7245 9.38863 10.7302 9.42101C10.7359 9.45339 10.7351 9.48658 10.7279 9.51866C10.7207 9.55074 10.7072 9.58107 10.6883 9.6079C10.6693 9.63474 10.6452 9.65754 10.6174 9.675C10.5613 9.70956 10.4939 9.7208 10.4295 9.70633C10.3652 9.69186 10.3091 9.65281 10.2731 9.5975C9.62844 8.67483 8.69371 7.9953 7.61828 7.6675C7.61828 7.67 7.6158 7.67 7.6158 7.6725C6.77867 7.43434 5.89443 7.41626 5.04831 7.62C5.04832 7.61967 5.04826 7.61934 5.04813 7.61903C5.04801 7.61873 5.04783 7.61845 5.04759 7.61822C5.04736 7.61798 5.04708 7.6178 5.04678 7.61768C5.04647 7.61755 5.04614 7.61749 5.04581 7.6175C4.6103 7.72074 4.18518 7.86384 3.7758 8.045C3.7458 8.05842 3.71345 8.06577 3.6806 8.06664C3.64776 8.06751 3.61507 8.06188 3.5844 8.05007C3.55374 8.03825 3.5257 8.02049 3.5019 7.9978C3.4781 7.9751 3.459 7.94793 3.4457 7.91783C3.4324 7.88772 3.42517 7.85529 3.4244 7.82238C3.42364 7.78947 3.42937 7.75673 3.44126 7.72604C3.45315 7.69535 3.47097 7.66732 3.49369 7.64355C3.51642 7.61977 3.5436 7.60073 3.57369 7.5875C3.97043 7.412 4.38105 7.26984 4.80129 7.1625C4.3437 6.10402 4.14769 4.95056 4.2299 3.8L5.17305 3.9875L4.76635 2.7225C4.74959 2.66997 4.75052 2.61337 4.76901 2.56143C4.78749 2.50948 4.8225 2.46506 4.86865 2.435L5.47247 2.04C5.52805 2.00419 5.59554 1.9919 5.66014 2.00582C5.72475 2.01975 5.78122 2.05876 5.81719 2.1143C5.85316 2.16985 5.8657 2.23741 5.85206 2.3022C5.83843 2.36699 5.79973 2.42373 5.74444 2.46L5.30031 2.75L5.63964 3.81L6.13867 3.51C6.38573 3.36321 6.58522 3.14805 6.71315 2.89037C6.84107 2.63269 6.89198 2.34348 6.85976 2.0575C6.73271 1.33036 6.41592 0.649833 5.94155 0.0849984C5.91327 0.0524463 5.87695 0.0279189 5.83622 0.0138773C5.79549 -0.00016441 5.75179 -0.00322645 5.70951 0.00499838L2.96487 0.504998C2.91113 0.51496 2.86209 0.542162 2.82514 0.582498C0.215237 3.46 0.0231124 10.4475 0.015627 10.745C0.0154028 10.8018 0.0338117 10.8572 0.0680247 10.9025C0.185296 11.0575 3.03972 14.7375 7.48604 15H7.50101C7.51609 14.9996 7.53111 14.9979 7.54592 14.995C8.39365 14.8085 9.17702 14.4005 9.81648 13.8125C9.24168 13.5564 8.70911 13.2141 8.23707 12.7975C8.18925 12.7521 8.16114 12.6897 8.15881 12.6238C8.15648 12.5579 8.1801 12.4936 8.22459 12.445C8.27038 12.3971 8.33303 12.3689 8.39921 12.3666C8.46539 12.3643 8.52987 12.3879 8.5789 12.4325C9.05354 12.8535 9.59603 13.1908 10.1833 13.43L10.1858 13.4275C11.5182 13.965 13.6615 14.155 15.3432 11.1325C15.7585 10.4033 15.9801 9.57954 15.9869 8.74C15.9877 8.05118 15.8261 7.37191 15.5154 6.7575ZM3.63606 12.41C3.61351 12.4444 3.58279 12.4727 3.54665 12.4924C3.5105 12.512 3.47007 12.5224 3.42896 12.5225C3.37925 12.5223 3.33068 12.5075 3.28923 12.48C2.48019 11.9353 1.74108 11.293 1.08853 10.5675C1.06674 10.5429 1.05001 10.5142 1.03928 10.4831C1.02855 10.452 1.02405 10.4191 1.02601 10.3862C1.02798 10.3534 1.03639 10.3212 1.05076 10.2916C1.06512 10.262 1.08517 10.2356 1.10974 10.2137C1.13431 10.1919 1.16294 10.1751 1.19398 10.1644C1.22503 10.1537 1.25788 10.1491 1.29066 10.1511C1.32345 10.1531 1.35552 10.1615 1.38506 10.1759C1.41459 10.1903 1.44101 10.2104 1.4628 10.235C2.08569 10.9298 2.79222 11.5445 3.56619 12.065C3.62107 12.1015 3.65924 12.1583 3.67234 12.223C3.68544 12.2877 3.67239 12.3549 3.63606 12.41ZM14.5148 7.545C14.48 7.56259 14.4416 7.572 14.4025 7.5725C14.3563 7.57291 14.311 7.56021 14.2717 7.53588C14.2324 7.51155 14.2008 7.47657 14.1805 7.435C14.0246 7.10038 13.7895 6.80904 13.4955 6.58637C13.2015 6.3637 12.8576 6.21647 12.4938 6.1575C12.4611 6.15366 12.4295 6.14338 12.4009 6.12725C12.3722 6.11112 12.347 6.08946 12.3267 6.06352C12.3064 6.03757 12.2915 6.00786 12.2827 5.9761C12.274 5.94434 12.2716 5.91116 12.2757 5.87847C12.2798 5.84578 12.2904 5.81423 12.3067 5.78565C12.3231 5.75706 12.3449 5.73201 12.371 5.71193C12.3971 5.69184 12.4269 5.67714 12.4586 5.66866C12.4904 5.66017 12.5236 5.65808 12.5561 5.6625C13.0012 5.73069 13.4226 5.90751 13.7833 6.17737C14.144 6.44724 14.4329 6.80187 14.6246 7.21C14.6543 7.26902 14.6594 7.33745 14.6389 7.40025C14.6183 7.46306 14.5737 7.51512 14.5148 7.545Z" fill="currentColor" />
                                                        </g>
                                                        <defs>
                                                            <clipPath id="paw_clip0">
                                                            <rect width="16" height="15" fill="white" />
                                                            </clipPath>
                                                        </defs>
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
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-left-small") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                        <path d="M15.7051 7.41L11.1251 12L15.7051 16.59L14.2951 18L8.29508 12L14.2951 6L15.7051 7.41Z" fill="currentColor" />
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
                                    <ul class="nav__sublist categories-sublist" data-sublist-index="{{ $loop->iteration }}">
                                        <li class="nav__item nav__item--return">
                                            <button class="nav__mobile-return">
                                                <span class="icon">
                                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-left-small") }}"></use>
                                                        </svg>
                                                    @else
                                                        <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                            <path d="M15.7051 7.41L11.1251 12L15.7051 16.59L14.2951 18L8.29508 12L14.2951 6L15.7051 7.41Z" fill="currentColor" />
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

                    <nav class="nav navbar drawer drawer--rtl" id="main-nav" data-drawer="navbar" aria-label="Main Menu">
                        <div class="nav-container greedy-nav">
                            <ul class="nav__list greedy-items">
                                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="/" data-cat-nav-opener>
                                            {{ __('text.common_categories_menu') }}
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-right-small") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                        <path d="M6.22119 5.5575L9.65619 9L6.22119 12.4425L7.27869 13.5L11.7787 9L7.27869 4.5L6.22119 5.5575Z" fill="currentColor" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
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
                                        <a class="nav__link" href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{ __('text.bonus_card_ref_programm') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                    </li>
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                    </li>
                                @else
                                    <li class="nav__item greedy-item" data-dropdown-hover="toggler">
                                        <a class="nav__link" href="/" data-cat-nav-opener>
                                            {{ __('text.common_categories_menu') }}
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-right-small") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                        <path d="M6.22119 5.5575L9.65619 9L6.22119 12.4425L7.27869 13.5L11.7787 9L7.27869 4.5L6.22119 5.5575Z" fill="currentColor" />
                                                    </svg>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#close") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                            <div class="dropdown" data-dropdown data-fixed-dropdown>
                                <button class="link greedy-button" data-dropdown-button aria-label="Show dropdown">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#fi-sr-menu-dots-vertical") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#fi-sr-menu-dots-vertical_clip0)">
                                                    <path d="M9.00034 2.66669C9.73673 2.66669 10.3337 2.06973 10.3337 1.33334C10.3337 0.596958 9.73673 0 9.00034 0C8.26395 0 7.66699 0.596958 7.66699 1.33334C7.66699 2.06973 8.26395 2.66669 9.00034 2.66669Z" fill="#B4B5CE" />
                                                    <path d="M9.00034 9.33335C9.73673 9.33335 10.3337 8.73639 10.3337 8C10.3337 7.26361 9.73673 6.66665 9.00034 6.66665C8.26395 6.66665 7.66699 7.26361 7.66699 8C7.66699 8.73639 8.26395 9.33335 9.00034 9.33335Z" fill="#B4B5CE" />
                                                    <path d="M9.00034 16C9.73673 16 10.3337 15.403 10.3337 14.6666C10.3337 13.9303 9.73673 13.3333 9.00034 13.3333C8.26395 13.3333 7.66699 13.9303 7.66699 14.6666C7.66699 15.403 8.26395 16 9.00034 16Z" fill="#B4B5CE" />
                                                </g>
                                                <defs>
                                                    <clipPath id="fi-sr-menu-dots-vertical_clip0">
                                                    <rect width="3.55556" height="16" fill="white" transform="translate(7.22266)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="icon is-hidden">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#close") }}"></use>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                            <button class="link link--primary" data-dialog-open="call">{{ __('text.common_callback') }}</button>
                            <span>&nbsp;{{ __('text.common_call_us_top') }}</span>
                        </div>
                        <a class="link link--white" href="tel:{{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}">{{ __('text.phones_title_phone_' . array_key_first($phone_arr) . '_code') }} {{ __('text.phones_title_phone_' . array_key_first($phone_arr)) }}</a>
                        <div class="dropdown header-phones__dropdown" data-dropdown data-dropdown-select>
                            <button class="dropdown-button link dropdown-button--select link--white" data-dropdown-button aria-expanded="false" aria-label="Call us dropdown" type="button">
                                <span class="button-text">&nbsp;{{ __('text.common_call_us_top') }}</span>
                                <span class="icon dropdown-button__arrow">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M12.6176 5.66666L7.97544 10.3088L3.33333 5.66666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
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
                </div>
            </div>

            <div class="container">
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

                <div class="header__wrapper">
                    <a class="header__logo" href="{{ route('home.index') }}">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="171" height="48" alt="{{ $domainWithoutZone }}">
                        @else
                            <img src="{{ asset($design . '/svg/logo.svg') }}" width="171" height="48" alt="Logo">
                        @endif
                    </a>

                    <!-- Cart button-->
                    <a class="cart-button cart-button--mobile button" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#cart") }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                    <path d="M8 18C6.9 18 6.01 18.9 6.01 20C6.01 21.1 6.9 22 8 22C9.1 22 10 21.1 10 20C10 18.9 9.1 18 8 18ZM2 2V4H4L7.6 11.59L6.25 14.04C6.09 14.32 6 14.65 6 15C6 16.1 6.9 17 8 17H20V15H8.42C8.28 15 8.17 14.89 8.17 14.75L8.2 14.63L9.1 13H16.55C17.3 13 17.96 12.59 18.3 11.97L21.88 5.48C21.96 5.34 22 5.17 22 5C22 4.45 21.55 4 21 4H6.21L5.27 2H2ZM18 18C16.9 18 16.01 18.9 16.01 20C16.01 21.1 16.9 22 18 22C19.1 22 20 21.1 20 20C20 18.9 19.1 18 18 18Z" fill="currentColor" />
                                </svg>
                            @endif
                        </span>
                        <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                        <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>

                    <!-- Navbar toggler-->
                    <button class="button navbar-toggler" data-drawer-toggle="navbar" aria-label="Toggle Main Menu" aria-controls="main-nav" aria-expanded="false">
                        <span class="icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#burger") }}"></use>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8001 18C10.8001 17.3372 11.3374 16.8 12.0001 16.8H19.2001C19.8629 16.8 20.4001 17.3372 20.4001 18C20.4001 18.6628 19.8629 19.2 19.2001 19.2H12.0001C11.3374 19.2 10.8001 18.6628 10.8001 18ZM3.6001 6C3.6001 5.33726 4.13735 4.8 4.8001 4.8H19.2001C19.8629 4.8 20.4001 5.33726 20.4001 6C20.4001 6.66274 19.8629 7.2 19.2001 7.2H4.8001C4.13735 7.2 3.6001 6.66274 3.6001 6ZM3.6001 12C3.6001 11.3373 4.13735 10.8 4.8001 10.8H19.2001C19.8629 10.8 20.4001 11.3373 20.4001 12C20.4001 12.6628 19.8629 13.2 19.2001 13.2H4.8001C4.13735 13.2 3.6001 12.6628 3.6001 12Z" fill="currentColor" />
                                </svg>
                            @endif
                        </span>
                    </button>
                </div>

                <div class="header-search-wrapper">
                    <!-- Drug index dropdown-->
                    <div class="dropdown index-dropdown" data-dropdown>
                        <button class="dropdown-button" data-dropdown-button aria-expanded="false" aria-label="Drug index">
                            <span class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#candy-box") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M5 7C4.60444 7 4.21776 6.8827 3.88886 6.66294C3.55996 6.44318 3.30362 6.13082 3.15224 5.76537C3.00087 5.39992 2.96126 4.99778 3.03843 4.60982C3.1156 4.22186 3.30608 3.86549 3.58579 3.58579C3.86549 3.30608 4.22186 3.1156 4.60982 3.03843C4.99778 2.96126 5.39992 3.00087 5.76537 3.15224C6.13082 3.30362 6.44318 3.55996 6.66294 3.88886C6.8827 4.21776 7 4.60444 7 5C7 5.53043 6.78929 6.03914 6.41421 6.41421C6.03914 6.78929 5.53043 7 5 7ZM14 5C14 4.60444 13.8827 4.21776 13.6629 3.88886C13.4432 3.55996 13.1308 3.30362 12.7654 3.15224C12.3999 3.00087 11.9978 2.96126 11.6098 3.03843C11.2219 3.1156 10.8655 3.30608 10.5858 3.58579C10.3061 3.86549 10.1156 4.22186 10.0384 4.60982C9.96126 4.99778 10.0009 5.39992 10.1522 5.76537C10.3036 6.13082 10.56 6.44318 10.8889 6.66294C11.2178 6.8827 11.6044 7 12 7C12.5304 7 13.0391 6.78929 13.4142 6.41421C13.7893 6.03914 14 5.53043 14 5ZM21 5C21 4.60444 20.8827 4.21776 20.6629 3.88886C20.4432 3.55996 20.1308 3.30362 19.7654 3.15224C19.3999 3.00087 18.9978 2.96126 18.6098 3.03843C18.2219 3.1156 17.8655 3.30608 17.5858 3.58579C17.3061 3.86549 17.1156 4.22186 17.0384 4.60982C16.9613 4.99778 17.0009 5.39992 17.1522 5.76537C17.3036 6.13082 17.56 6.44318 17.8889 6.66294C18.2178 6.8827 18.6044 7 19 7C19.5304 7 20.0391 6.78929 20.4142 6.41421C20.7893 6.03914 21 5.53043 21 5ZM7 12C7 11.6044 6.8827 11.2178 6.66294 10.8889C6.44318 10.56 6.13082 10.3036 5.76537 10.1522C5.39992 10.0009 4.99778 9.96126 4.60982 10.0384C4.22186 10.1156 3.86549 10.3061 3.58579 10.5858C3.30608 10.8655 3.1156 11.2219 3.03843 11.6098C2.96126 11.9978 3.00087 12.3999 3.15224 12.7654C3.30362 13.1308 3.55996 13.4432 3.88886 13.6629C4.21776 13.8827 4.60444 14 5 14C5.53043 14 6.03914 13.7893 6.41421 13.4142C6.78929 13.0391 7 12.5304 7 12ZM14 12C14 11.6044 13.8827 11.2178 13.6629 10.8889C13.4432 10.56 13.1308 10.3036 12.7654 10.1522C12.3999 10.0009 11.9978 9.96126 11.6098 10.0384C11.2219 10.1156 10.8655 10.3061 10.5858 10.5858C10.3061 10.8655 10.1156 11.2219 10.0384 11.6098C9.96126 11.9978 10.0009 12.3999 10.1522 12.7654C10.3036 13.1308 10.56 13.4432 10.8889 13.6629C11.2178 13.8827 11.6044 14 12 14C12.5304 14 13.0391 13.7893 13.4142 13.4142C13.7893 13.0391 14 12.5304 14 12ZM21 12C21 11.6044 20.8827 11.2178 20.6629 10.8889C20.4432 10.56 20.1308 10.3036 19.7654 10.1522C19.3999 10.0009 18.9978 9.96126 18.6098 10.0384C18.2219 10.1156 17.8655 10.3061 17.5858 10.5858C17.3061 10.8655 17.1156 11.2219 17.0384 11.6098C16.9613 11.9978 17.0009 12.3999 17.1522 12.7654C17.3036 13.1308 17.56 13.4432 17.8889 13.6629C18.2178 13.8827 18.6044 14 19 14C19.5304 14 20.0391 13.7893 20.4142 13.4142C20.7893 13.0391 21 12.5304 21 12ZM7 19C7 18.6044 6.8827 18.2178 6.66294 17.8889C6.44318 17.56 6.13082 17.3036 5.76537 17.1522C5.39992 17.0009 4.99778 16.9613 4.60982 17.0384C4.22186 17.1156 3.86549 17.3061 3.58579 17.5858C3.30608 17.8655 3.1156 18.2219 3.03843 18.6098C2.96126 18.9978 3.00087 19.3999 3.15224 19.7654C3.30362 20.1308 3.55996 20.4432 3.88886 20.6629C4.21776 20.8827 4.60444 21 5 21C5.53043 21 6.03914 20.7893 6.41421 20.4142C6.78929 20.0391 7 19.5304 7 19ZM14 19C14 18.6044 13.8827 18.2178 13.6629 17.8889C13.4432 17.56 13.1308 17.3036 12.7654 17.1522C12.3999 17.0009 11.9978 16.9613 11.6098 17.0384C11.2219 17.1156 10.8655 17.3061 10.5858 17.5858C10.3061 17.8655 10.1156 18.2219 10.0384 18.6098C9.96126 18.9978 10.0009 19.3999 10.1522 19.7654C10.3036 20.1308 10.56 20.4432 10.8889 20.6629C11.2178 20.8827 11.6044 21 12 21C12.5304 21 13.0391 20.7893 13.4142 20.4142C13.7893 20.0391 14 19.5304 14 19ZM21 19C21 18.6044 20.8827 18.2178 20.6629 17.8889C20.4432 17.56 20.1308 17.3036 19.7654 17.1522C19.3999 17.0009 18.9978 16.9613 18.6098 17.0384C18.2219 17.1156 17.8655 17.3061 17.5858 17.5858C17.3061 17.8655 17.1156 18.2219 17.0384 18.6098C16.9613 18.9978 17.0009 19.3999 17.1522 19.7654C17.3036 20.1308 17.56 20.4432 17.8889 20.6629C18.2178 20.8827 18.6044 21 19 21C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19Z" fill="currentColor" />
                                    </svg>
                                @endif
                            </span>
                            <span class="icon is-hidden">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#close-thin") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                            </span>
                        </button>
                        <div class="dropdown-container" data-dropdown-container>
                            <div class="dropdown-content"><span class="drug-index-caption">{{ __('text.common_first_letter') }}:</span>
                                <ul class="drug-index">
                                    @foreach ($first_letters as $key => $active_letter)
                                        <li class="drug-index__item @if ($active_letter) active @endif">
                                            @if ($active_letter)
                                                <div class="drug-index__link active">
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

                    <!-- Search form-->
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#search") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.25 15C11.9779 15 15 11.9779 15 8.25C15 4.52208 11.9779 1.5 8.25 1.5C4.52208 1.5 1.5 4.52208 1.5 8.25C1.5 11.9779 4.52208 15 8.25 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M16.5 16.5L13.5 13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </div>
                    </form>
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
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-down") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M12.6176 5.66666L7.97544 10.3088L3.33333 5.66666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
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
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#chevron-down") }}"></use>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                        <path d="M12.6176 5.66666L7.97544 10.3088L3.33333 5.66666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                            </span>
                        </div>
                    @endif
                    <a class="header-auth link link--white" href="{{ route('home.login') }}" target="_blank">{{ __('text.common_profile') }}</a>
                </div>

                <a class="cart-button button" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                <path d="M8 18C6.9 18 6.01 18.9 6.01 20C6.01 21.1 6.9 22 8 22C9.1 22 10 21.1 10 20C10 18.9 9.1 18 8 18ZM2 2V4H4L7.6 11.59L6.25 14.04C6.09 14.32 6 14.65 6 15C6 16.1 6.9 17 8 17H20V15H8.42C8.28 15 8.17 14.89 8.17 14.75L8.2 14.63L9.1 13H16.55C17.3 13 17.96 12.59 18.3 11.97L21.88 5.48C21.96 5.34 22 5.17 22 5C22 4.45 21.55 4 21 4H6.21L5.27 2H2ZM18 18C16.9 18 16.01 18.9 16.01 20C16.01 21.1 16.9 22 18 22C19.1 22 20 21.1 20 20C20 18.9 19.1 18 18 18Z" fill="currentColor" />
                            </svg>
                        @endif
                    </span>
                    <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                    <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                </a>

                <div class="shop-info">
                    <div class="shop-highlight">
                        <div class="shop-highlight__numbers">1 000 000</div>
                        <div class="shop-highlight__text">{{ ucfirst(__('text.common_customers')) }}</div>
                    </div>
                    <div class="promos-items">
                        <div class="promos-item">
                            <div class="promos-item__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#fi_3126544") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#fi_3126544_clip0)">
                                                <path d="M5.34495 9.58167C4.94277 9.58167 4.60327 10.0247 4.60327 10.5485C4.60327 11.0727 4.94272 11.5157 5.34495 11.5157C5.74714 11.5157 6.08703 11.0727 6.08703 10.5485C6.08703 10.0247 5.74714 9.58167 5.34495 9.58167Z" fill="white" />
                                                <path d="M11.8489 14.6945C11.4467 14.6945 11.1072 15.1375 11.1072 15.6617C11.1072 16.1855 11.4466 16.6285 11.8489 16.6285C12.251 16.6285 12.591 16.1855 12.591 15.6617C12.591 15.1375 12.251 14.6945 11.8489 14.6945Z" fill="white" />
                                                <path d="M16.0639 6.86173C9.52909 0.84877 9.86335 1.07844 9.34381 0.876614C8.60548 0.583739 7.72694 0.73688 7.12009 1.29513L1.07039 6.86173C0.390156 7.48736 0 8.37767 0 9.30493V19.1624C0 20.3238 0.940199 21.2692 2.09563 21.2692H15.0382C16.1937 21.2692 17.1338 20.3238 17.1338 19.1624V9.30493C17.1339 8.37767 16.7437 7.48736 16.0639 6.86173ZM7.94342 4.74853C7.98248 4.37393 8.32605 4.10791 8.71045 4.18005C9.07809 4.25671 9.28516 4.60948 9.21663 4.93802C9.14379 5.28804 8.81452 5.51101 8.45866 5.44419C8.11877 5.37329 7.91132 5.06452 7.94342 4.74853ZM3.31427 10.5485C3.31427 9.30451 4.22525 8.29259 5.34505 8.29259C6.46529 8.29259 7.37623 9.30451 7.37623 10.5485C7.37623 11.7924 6.46525 12.8047 5.34505 12.8047C4.22525 12.8047 3.31427 11.7924 3.31427 10.5485ZM7.00928 17.4088C6.55381 17.4088 6.23593 16.9419 6.41545 16.5147L9.55311 9.03638C9.69104 8.7081 10.0687 8.55341 10.397 8.69134C10.7253 8.82884 10.8796 9.20697 10.7421 9.53525L7.60396 17.0135C7.50041 17.2602 7.2615 17.4088 7.00928 17.4088ZM11.849 17.9176C10.7292 17.9176 9.81823 16.9057 9.81823 15.6617C9.81823 14.4178 10.7292 13.4054 11.849 13.4054C12.9688 13.4054 13.8802 14.4178 13.8802 15.6617C13.8802 16.9057 12.9688 17.9176 11.849 17.9176Z" fill="white" />
                                                <path d="M21.8383 13.9404C19.7397 7.83135 18.574 4.43476 18.5575 4.39617C18.1927 3.55141 17.4837 2.89442 16.6131 2.59406C16.5632 2.57709 16.8579 2.64868 12.1833 1.53961L16.9363 5.91297C17.8811 6.7818 18.423 8.01801 18.423 9.30492V17.658L20.7417 16.6655C21.7906 16.2117 22.2783 14.9936 21.8383 13.9404Z" fill="#BCBCBC" />
                                            </g>
                                            <defs>
                                                <clipPath id="fi_3126544_clip0">
                                                <rect width="22" height="22" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-item__title">{{ __('text.common_save') }}</div>
                            <div class="promos-item__text">{{ __('text.common_discount') }}</div>
                        </div>
                        <div class="promos-item">
                            <div class="promos-item__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#fi_13122678") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 17 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <path d="M14.3438 0.069458H2.65633C2.20508 0.0721317 1.77303 0.260779 1.45394 0.594467C1.13486 0.928156 0.954461 1.37997 0.951904 1.85187V18.1482C0.954461 18.6201 1.13486 19.0719 1.45394 19.4056C1.77303 19.7392 2.20508 19.9279 2.65633 19.9306H14.3438C14.7951 19.9279 15.2271 19.7392 15.5462 19.4056C15.8653 19.0719 16.0457 18.6201 16.0483 18.1482V1.85187C16.0457 1.37997 15.8653 0.928156 15.5462 0.594467C15.2271 0.260779 14.7951 0.0721317 14.3438 0.069458ZM4.84774 3.8889C4.85026 3.68713 4.92803 3.49436 5.06448 3.35167C5.20092 3.20898 5.38526 3.12765 5.57821 3.12501H7.04888C7.6275 3.12501 8.18241 3.36538 8.59155 3.79324C9.0007 4.2211 9.23055 4.80141 9.23055 5.40649C9.22984 5.75927 9.15328 6.10736 9.00654 6.42501L9.9805 7.44353L10.9545 6.42501C11.0929 6.29008 11.2761 6.21662 11.4653 6.22011C11.6546 6.2236 11.8351 6.30377 11.969 6.44373C12.1028 6.58369 12.1794 6.77251 12.1828 6.97041C12.1861 7.16831 12.1159 7.35984 11.9869 7.50464L11.0129 8.52316L11.9869 9.54168C12.1236 9.68491 12.2005 9.87906 12.2005 10.0815C12.2005 10.2839 12.1236 10.4781 11.9869 10.6213C11.8499 10.7644 11.6642 10.8447 11.4707 10.8447C11.2771 10.8447 11.0914 10.7644 10.9545 10.6213L9.9805 9.60279L9.00654 10.6213C8.86958 10.7644 8.68392 10.8447 8.49034 10.8447C8.29677 10.8447 8.11111 10.7644 7.97414 10.6213C7.83735 10.4781 7.76052 10.2839 7.76052 10.0815C7.76052 9.87906 7.83735 9.68491 7.97414 9.54168L8.9481 8.52316L7.97414 7.50464C7.67062 7.65768 7.3371 7.73441 7.00019 7.72872H6.30867V10C6.30867 10.2026 6.23171 10.3969 6.09473 10.5402C5.95774 10.6834 5.77194 10.7639 5.57821 10.7639C5.38447 10.7639 5.19868 10.6834 5.06169 10.5402C4.9247 10.3969 4.84774 10.2026 4.84774 10V3.8889ZM12.3959 16.875H4.60425C4.41052 16.875 4.22472 16.7945 4.08773 16.6513C3.95074 16.508 3.87378 16.3137 3.87378 16.1111C3.87378 15.9085 3.95074 15.7142 4.08773 15.571C4.22472 15.4277 4.41052 15.3472 4.60425 15.3472H12.3959C12.5896 15.3472 12.7754 15.4277 12.9124 15.571C13.0494 15.7142 13.1264 15.9085 13.1264 16.1111C13.1264 16.3137 13.0494 16.508 12.9124 16.6513C12.7754 16.7945 12.5896 16.875 12.3959 16.875ZM12.3959 13.8195H4.60425C4.41052 13.8195 4.22472 13.739 4.08773 13.5957C3.95074 13.4525 3.87378 13.2582 3.87378 13.0556C3.87378 12.853 3.95074 12.6587 4.08773 12.5154C4.22472 12.3722 4.41052 12.2917 4.60425 12.2917H12.3959C12.5896 12.2917 12.7754 12.3722 12.9124 12.5154C13.0494 12.6587 13.1264 12.853 13.1264 13.0556C13.1264 13.2582 13.0494 13.4525 12.9124 13.5957C12.7754 13.739 12.5896 13.8195 12.3959 13.8195ZM7.04888 6.18057H6.30867V4.65279H7.04888C7.24262 4.65279 7.42841 4.73327 7.5654 4.87653C7.70239 5.01979 7.77935 5.21408 7.77935 5.41668C7.77935 5.61928 7.70239 5.81357 7.5654 5.95683C7.42841 6.10009 7.24262 6.18057 7.04888 6.18057Z" fill="white" />
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-item__title">{{ __('text.common_prescription') }}</div>
                            <div class="promos-item__text">{{ __('text.common_restrictions') }}</div>
                        </div>
                        <div class="promos-item">
                            <div class="promos-item__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#fi_4212257") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#fi_4212257_clip0)">
                                                <path d="M21.9292 7.68L20.1928 3.68C20.132 3.53814 20.0321 3.41729 19.9052 3.33209C19.7783 3.24689 19.6298 3.20101 19.4778 3.2H15.2742L15.5256 0.888C15.538 0.775765 15.5269 0.662134 15.4931 0.554583C15.4592 0.447033 15.4034 0.348 15.3292 0.264C15.2558 0.181126 15.1661 0.114823 15.0659 0.0693561C14.9658 0.023889 14.8574 0.000263863 14.7478 0H3.9285C3.72012 0 3.52027 0.0842856 3.37292 0.234315C3.22557 0.384344 3.14279 0.587826 3.14279 0.8C3.14279 1.01217 3.22557 1.21566 3.37292 1.36568C3.52027 1.51571 3.72012 1.6 3.9285 1.6H7.85707C8.06546 1.6 8.26531 1.68428 8.41266 1.83431C8.56001 1.98434 8.64279 2.18783 8.64279 2.4C8.64279 2.61217 8.56001 2.81565 8.41266 2.96568C8.26531 3.11571 8.06546 3.2 7.85707 3.2H2.35707C2.14869 3.2 1.94884 3.28428 1.80149 3.43431C1.65414 3.58434 1.57136 3.78782 1.57136 4C1.57136 4.21217 1.65414 4.41565 1.80149 4.56568C1.94884 4.71571 2.14869 4.8 2.35707 4.8H10.2142C10.4226 4.8 10.6224 4.88428 10.7698 5.03431C10.9172 5.18434 10.9999 5.38782 10.9999 5.6C10.9999 5.81217 10.9172 6.01565 10.7698 6.16568C10.6224 6.31571 10.4226 6.4 10.2142 6.4H4.71422C4.50583 6.4 4.30598 6.48428 4.15863 6.63431C4.01128 6.78434 3.9285 6.98782 3.9285 7.2C3.9285 7.41217 4.01128 7.61565 4.15863 7.76568C4.30598 7.91571 4.50583 8 4.71422 8H7.85707C8.06546 8 8.26531 8.08428 8.41266 8.23431C8.56001 8.38434 8.64279 8.58782 8.64279 8.8C8.64279 9.01217 8.56001 9.21565 8.41266 9.36568C8.26531 9.51571 8.06546 9.59999 7.85707 9.59999H1.57136C1.36297 9.59999 1.16312 9.68428 1.01577 9.83431C0.868425 9.98434 0.785645 10.1878 0.785645 10.4C0.785645 10.6122 0.868425 10.8157 1.01577 10.9657C1.16312 11.1157 1.36297 11.2 1.57136 11.2H3.14279C3.35117 11.2 3.55102 11.2843 3.69837 11.4343C3.84572 11.5843 3.9285 11.7878 3.9285 12C3.9285 12.2122 3.84572 12.4157 3.69837 12.5657C3.55102 12.7157 3.35117 12.8 3.14279 12.8H2.35707C2.14869 12.8 1.94884 12.8843 1.80149 13.0343C1.65414 13.1843 1.57136 13.3878 1.57136 13.6C1.57136 13.8122 1.65414 14.0156 1.80149 14.1657C1.94884 14.3157 2.14869 14.4 2.35707 14.4H4.59636C4.6812 14.7374 4.84268 15.0498 5.06779 15.312C5.25854 15.5291 5.49229 15.7027 5.75366 15.8213C6.01502 15.9399 6.29808 16.0008 6.58422 16C7.06841 15.9859 7.53774 15.8265 7.93332 15.5419C8.3289 15.2573 8.63311 14.86 8.80779 14.4H14.8106C14.8955 14.7374 15.057 15.0498 15.2821 15.312C15.4728 15.5291 15.7066 15.7027 15.9679 15.8213C16.2293 15.9399 16.5124 16.0008 16.7985 16C17.2827 15.9859 17.752 15.8265 18.1476 15.5419C18.5432 15.2573 18.8474 14.86 19.0221 14.4H20.5935C20.7877 14.4012 20.9755 14.3291 21.1206 14.1976C21.2657 14.0661 21.3579 13.8846 21.3792 13.688L21.9842 8.088C22.0058 7.94945 21.9866 7.80751 21.9292 7.68ZM6.58422 14.4C6.51906 14.4029 6.45411 14.3907 6.39424 14.3643C6.33437 14.338 6.28113 14.2982 6.2385 14.248C6.17682 14.1703 6.1317 14.0804 6.106 13.984C6.0803 13.8876 6.07458 13.7868 6.08922 13.688C6.10928 13.4634 6.20453 13.2526 6.35901 13.0909C6.51348 12.9293 6.71781 12.8266 6.93779 12.8C7.00294 12.7971 7.06789 12.8093 7.12776 12.8356C7.18763 12.862 7.24088 12.9017 7.2835 12.952C7.34518 13.0297 7.3903 13.1196 7.416 13.216C7.44171 13.3124 7.44742 13.4132 7.43279 13.512C7.41272 13.7366 7.31747 13.9474 7.163 14.109C7.00852 14.2707 6.80419 14.3734 6.58422 14.4ZM16.7985 14.4C16.7333 14.4029 16.6684 14.3907 16.6085 14.3643C16.5487 14.338 16.4954 14.2982 16.4528 14.248C16.3911 14.1703 16.346 14.0804 16.3203 13.984C16.2946 13.8876 16.2889 13.7868 16.3035 13.688C16.3236 13.4634 16.4188 13.2526 16.5733 13.0909C16.7278 12.9293 16.9321 12.8266 17.1521 12.8C17.2172 12.7971 17.2822 12.8093 17.342 12.8356C17.4019 12.862 17.4552 12.9017 17.4978 12.952C17.5595 13.0297 17.6046 13.1196 17.6303 13.216C17.656 13.3124 17.6617 13.4132 17.6471 13.512C17.627 13.7366 17.5318 13.9474 17.3773 14.109C17.2228 14.2707 17.0185 14.3734 16.7985 14.4ZM20.3342 8.8H16.8614C16.7517 8.79973 16.6433 8.77611 16.5432 8.73064C16.4431 8.68517 16.3534 8.61887 16.2799 8.536C16.2058 8.452 16.1499 8.35296 16.1161 8.24541C16.0822 8.13786 16.0711 8.02423 16.0835 7.912L16.3428 5.512C16.3659 5.31687 16.4588 5.13725 16.6038 5.00741C16.7487 4.87756 16.9355 4.80658 17.1285 4.808H18.9592L20.4285 8.128L20.3342 8.8Z" fill="white" />
                                                <path d="M1.57143 7.99999C1.77981 7.99999 1.97966 7.91571 2.12701 7.76568C2.27436 7.61565 2.35714 7.41217 2.35714 7.19999C2.35714 6.98782 2.27436 6.78434 2.12701 6.63431C1.97966 6.48428 1.77981 6.39999 1.57143 6.39999H0.785714C0.57733 6.39999 0.37748 6.48428 0.23013 6.63431C0.0827803 6.78434 0 6.98782 0 7.19999C0 7.41217 0.0827803 7.61565 0.23013 7.76568C0.37748 7.91571 0.57733 7.99999 0.785714 7.99999H1.57143Z" fill="white" />
                                            </g>
                                            <defs>
                                                <clipPath id="fi_4212257_clip0">
                                                <rect width="22" height="16" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-item__title">{{ __('text.common_delivery') }}</div>
                            <div class="promos-item__text">{{ __('text.common_receive') }}</div>
                        </div>
                        <div class="promos-item">
                            <div class="promos-item__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#fi_6948561") }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                            <g clip-path="url(#fi_6948561_clip0)">
                                                <path d="M19.0667 0H22V2.93333C20.3822 2.93333 19.0667 1.61777 19.0667 0ZM20.8413 10.5087C21.2738 10.8754 21.6627 11.2933 22 11.7405V10.2667C21.5896 10.2667 21.1936 10.3548 20.8413 10.5087ZM2.93333 0H0V2.93333C1.61777 2.93333 2.93333 1.61777 2.93333 0ZM0 10.2667V13.2H2.93333C2.93333 11.5822 1.61777 10.2667 0 10.2667ZM11 4.4C9.78685 4.4 8.8 5.38685 8.8 6.6C8.8 7.81315 9.78685 8.8 11 8.8C12.2132 8.8 13.2 7.81315 13.2 6.6C13.2 5.38685 12.2132 4.4 11 4.4ZM22 4.4V8.8C21.054 8.8 20.181 9.09326 19.4699 9.60674C18.473 9.0861 17.3357 8.8 16.1333 8.8C15.1945 8.8 14.3 8.97617 13.4786 9.2988C14.2048 8.63135 14.6667 7.67064 14.6667 6.6C14.6667 4.57617 13.0238 2.93333 11 2.93333C8.97617 2.93333 7.33333 4.57617 7.33333 6.6C7.33333 8.62383 8.97617 10.2667 11 10.2667C11.3079 10.2667 11.6087 10.2301 11.8945 10.1567C10.8167 10.9194 9.95156 11.9754 9.41589 13.2H4.4C4.4 10.7726 2.42702 8.8 0 8.8V4.4C2.42702 4.4 4.4 2.42738 4.4 0H17.6C17.6 2.42738 19.573 4.4 22 4.4ZM5.86667 6.6C5.86667 6.19681 5.53652 5.86667 5.13333 5.86667C4.73014 5.86667 4.4 6.19681 4.4 6.6C4.4 7.00319 4.73014 7.33333 5.13333 7.33333C5.53652 7.33333 5.86667 7.00319 5.86667 6.6ZM17.6 6.6C17.6 6.19681 17.2699 5.86667 16.8667 5.86667C16.4635 5.86667 16.1333 6.19681 16.1333 6.6C16.1333 7.00319 16.4635 7.33333 16.8667 7.33333C17.2699 7.33333 17.6 7.00319 17.6 6.6ZM22 16.1333C22 19.3739 19.3746 22 16.1333 22C12.8921 22 10.2667 19.3739 10.2667 16.1333C10.2667 12.8928 12.8921 10.2667 16.1333 10.2667C19.3746 10.2667 22 12.8928 22 16.1333ZM19.4333 16.8667C19.4333 15.2493 18.1178 13.9333 16.5 13.9333H15.337L16.0703 13.2L15.0333 12.163L12.5297 14.6667L15.0333 17.1703L16.0703 16.1333L15.337 15.4H16.5C17.3085 15.4 17.9667 16.0578 17.9667 16.8667C17.9667 17.6756 17.3085 18.3333 16.5 18.3333H14.3V19.8H16.5C18.1178 19.8 19.4333 18.4841 19.4333 16.8667Z" fill="white" />
                                            </g>
                                            <defs>
                                                <clipPath id="fi_6948561_clip0">
                                                <rect width="22" height="22" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-item__title">{{ __('text.common_moneyback') }}</div>
                            <div class="promos-item__text">{{ __('text.common_refund') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="promos">
            <div class="container">
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
                <div class="promos-cards">
                    <a class="promo-card" href="{{ route('home.bonus_referral_program') }}">
                        <div class="promo-card__content">
                            <div class="promo-card__title">{{ __('text.bonus_card_ref_programm') }}</div>
                            <span class="promo-card__text">{{ __('text.save_earn') }}</span>
                        </div>
                    </a>
                    <div class="promo-card promo-card--hour" href="/">
                        <div class="promo-card__content">
                            <div class="promo-card__title">
                                {{ Str::ucfirst(__('text.common_banner1_text1')) }} <br> {{ Str::ucfirst(__('text.common_banner1_text2')) }}
                            </div>
                            <span class="promo-card__text">{{ __('text.common_banner1_text3') }} {{ __('text.common_banner1_text4') }}</span>
                        </div>
                    </div>
                    <div class="promo-card promo-card--sale" href="/">
                        <div class="promo-card__content">
                            <div class="promo-card__title">
                                {{ __('text.common_banner2_text1') }} <br> {!! __('text.common_banner2_text2') !!}
                            </div>
                            <span class="promo-card__text">{{ __('text.common_banner2_text3') }} {{ __('text.common_banner2_text4') }}</span>
                        </div>
                    </div>
                </div>
                {{-- <div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
                    <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
                </div> --}}
            </div>
        </div>

        @yield('content')

        <div class="sup-footer">
            <div class="container">
                <div class="shop-info-wrapper">
                    <div class="shop-verification">
                        <div class="promos-caption">
                            <div class="promos-caption__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?rv1wchvg#fi_17047229') }}"></use>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                            <path d="M22.5 14C17.81 14 14 17.81 14 22.5C14 27.19 17.81 31 22.5 31C27.19 31 31 27.19 31 22.5C31 17.81 27.19 14 22.5 14ZM26.53 21.13L22.42 25.28C22.23 25.47 21.98 25.58 21.71 25.58C21.44 25.58 21.19 25.48 21 25.29L18.47 22.76C18.08 22.37 18.08 21.74 18.47 21.35C18.86 20.96 19.49 20.96 19.88 21.35L21.7 23.17L25.1 19.74C25.49 19.35 26.12 19.34 26.51 19.74C26.9 20.13 26.91 20.76 26.51 21.15L26.53 21.13Z" fill="white" />
                                            <path d="M12.3 25.18C7.52999 22.01 4.99999 17.49 4.99999 12.12V9.73001C4.99999 9.23001 5.36999 8.81001 5.85999 8.74001C8.41999 8.39001 10.61 7.64001 12.38 6.52001C12.7 6.32001 13.11 6.32001 13.44 6.52001C15.26 7.64001 17.51 8.39001 20.14 8.75001C20.63 8.82001 21 9.24001 21 9.74001V12.12C21.49 12.05 21.99 12 22.5 12C23.36 12 24.19 12.12 24.99 12.31C24.99 12.25 24.99 12.19 24.99 12.12V6.00001C24.99 5.45001 24.54 5.00001 23.99 5.00001C15.99 5.00001 13.74 1.57001 13.7 1.49001C13.52 1.18001 13.16 1.02001 12.82 1.01001C12.46 1.01001 12.12 1.22001 11.95 1.54001C11.93 1.57001 9.98999 5.00001 1.98999 5.00001C1.43999 5.00001 0.98999 5.45001 0.98999 6.00001V12.12C0.98999 19.69 5.02999 26 12.37 29.88C12.52 29.96 12.68 30 12.84 30C13 30 13.16 29.96 13.31 29.88C13.7 29.67 14.08 29.45 14.45 29.23C13.48 28.07 12.76 26.71 12.36 25.21C12.34 25.2 12.31 25.19 12.29 25.18H12.3Z" fill="#BCBCBC" />
                                            <path d="M7 10.58V12.12C7 16.3 8.7 19.76 12 22.46C12.02 17.93 14.92 14.07 18.97 12.62C18.97 12.45 19 12.29 19 12.12V10.6C16.7 10.2 14.66 9.51004 12.93 8.54004C11.23 9.51004 9.24 10.2 7 10.59V10.58Z" fill="#BCBCBC" />
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-caption__title">{{ __('text.common_verified') }}</div>
                            <div class="promos-caption__text">{{ __('text.common_approved_d4') }}</div>
                        </div>

                        <div class="promos-brands">
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-1-48w.webp 1x, ' . $design . '/img/brands/brand-1-97w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-1-48w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-1-48w.png 1x, ' . $design . '/img/brands/brand-1-97w.png 2x') }}" width="49" height="32"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-2-46w.webp 1x, ' . $design . '/img/brands/brand-2-93w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-2-46w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-2-46w.png 1x, ' . $design . '/img/brands/brand-2-93w.png 2x') }}" width="47" height="36"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-3-69w.webp 1x, ' . $design . '/img/brands/brand-3-138w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-3-69w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-3-69w.png 1x, ' . $design . '/img/brands/brand-3-138w.png 2x') }}" width="69" height="36"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-4-45w.webp 1x, ' . $design . '/img/brands/brand-4-90w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-4-45w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-4-45w.png 1x, ' . $design . '/img/brands/brand-4-90w.png 2x') }}" width="45" height="27"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-5-56w.webp 1x, ' . $design . '/img/brands/brand-5-113w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-5-56w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-5-56w.png 1x, ' . $design . '/img/brands/brand-5-113w.png 2x" width="57') }}" height="32"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-6-72w.webp 1x, ' . $design . '/img/brands/brand-6-145w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-6-72w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-6-72w.png 1x, ' . $design . '/img/brands/brand-6-145w.png 2x') }}" width="73" height="30"
                                    alt="Brand">
                                </picture>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="subscribe">
                    <div class="subscribe__caption">
                        <div class="subscribe__title">{{ __('text.common_subscribe') }}</div>
                        <div class="subscribe__text">{{ __('text.common_spec_offer') }}</div>
                    </div>
                    <form class="subscribe-form">
                        <label class="subscribe-form__label">
                            <input class="subscribe-form__input" type="email" name="subscribe-email" placeholder="{{ __('text.affiliate_email') }}" required>
                        </label>
                        <button class="subscribe-form__button button button_sub" type="button">{{ __('text.common_subscribe') }}</button>
                    </form>
                </div>

                @yield('rewies')
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <div class="footer__wrapper">
                    <div class="footer-nav-wrapper">
                        <a class="footer-logo" href="{{ route('home.index') }}">
                            <img src="{{ asset($design . '/svg/logo-dim.svg') }}" width="171" height="33" alt="Site logo">
                        </a>
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
                                    </ul>
                                @endif
                            </div>
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                <div class="sitemap_menu">
                                    <a class="nav__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                                </div>
                            @endif
                        </nav>
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <a class="footer-nav-button button" href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        @else
                            <a class="footer-nav-button button" href="{{ route('home.affiliate', '') }}">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        @endif
                    </div>
                </div>
                <div class="footer__copyrights">
                    {{ __('text.license_text_license1_1') }}
                    {{ $domain }}
                    {{ __('text.license_text_license1_2') }}
                    {{ __('text.license_text_license2_d13') }}
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
                    </div>
                    <div class="form__field submit-field">
                        <input class="button form__submit button--secondary button--dialog button_request_call" type="button" value="{{ __('text.common_callback') }}">
                    </div>
                </form>
                <button class="dialog__close-button" data-dialog-close="call" aria-label="Close dialog">
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#close") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
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
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?rv1wchvg#close") }}"></use>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        @endif
                    </span>
                </button>
            </div>
        </dialog> --}}

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

        <script defer src="{{ asset_ver("$design/js/main.9507b401.js") }}"></script>
        <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
        <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>