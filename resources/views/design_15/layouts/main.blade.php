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
        <meta name="theme-color" content="#474ce6" />
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

        <link href="{{ asset($design . '/fonts/poppins-extralight.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-light.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-medium.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
        <link href="{{ asset($design . '/fonts/poppins-semibold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
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
            const design = 15;
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
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-down") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path
                                        d="M12.6172 5.66666L7.97507 10.3088L3.33296 5.66666"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
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
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-left") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path
                                                    d="M15.5 5.07367L8.53683 12.0368L15.5 19"
                                                    stroke="currentColor"
                                                    stroke-width="1.5"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        @endif
                                    </span>
                                    {{ __('text.common_categories_menu') }}
                                </button>
                            </div>
                            <button class="nav__close-button" aria-label="Close categories" data-cat-nav-close></button>
                            <ul class="nav__list">
                                {{-- <li class="nav__item">
                                    <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.index') }}" data-sublist-index="0">{{ __('text.common_best_selling_title') }}
                                        <span class="icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="1em" height="1em" fill="currentColor">
                                                    <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 13" fill="none" width="1em" height="1em" fill="currentColor">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.36676 1.73958C6.0663 0.567387 5.14229 -0.193129 4.30228 0.0430425C3.46335 0.278131 3.02396 1.42107 3.32443 2.59327C3.62489 3.76546 4.5489 4.52489 5.38891 4.2898C6.22891 4.05472 6.66723 2.91286 6.36676 1.73958Z" fill="currentColor"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.69772 0.0430425C8.85771 -0.193129 7.9337 0.566304 7.63323 1.73958C7.33277 2.91286 7.77108 4.05363 8.61109 4.2898C9.4511 4.52598 10.3762 3.76546 10.6756 2.59327C10.975 1.42107 10.5377 0.279214 9.69772 0.0430425Z" fill="currentColor"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8408 7.53988C12.6808 7.77605 13.6048 7.01661 13.9053 5.84334C14.2058 4.67006 13.7674 3.52929 12.9274 3.29311C12.0874 3.05694 11.1634 3.81638 10.863 4.98965C10.5636 6.16185 11.0008 7.3037 11.8408 7.53988Z" fill="currentColor"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M7.82278 12.1864C8.3677 12.584 9.12048 13 9.87218 13C10.696 13 11.3077 12.3749 11.3077 11.4833C11.3077 8.4499 9.04725 5.4165 7 5.4165C4.95275 5.4165 2.69227 8.4499 2.69227 11.4833C2.69227 12.3749 3.30396 13 4.12782 13C4.88059 13 5.63229 12.584 6.1783 12.1864C6.67692 11.8246 7.32416 11.8246 7.82278 12.1864Z" fill="currentColor"/>
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07256 3.29311C0.23255 3.5282 -0.205762 4.67006 0.0947027 5.84334C0.39409 7.01661 1.31918 7.77496 2.15918 7.53988C2.99919 7.30479 3.4375 6.16293 3.13704 4.98965C2.83657 3.81746 1.91257 3.05803 1.07256 3.29311Z" fill="currentColor"/>
                                                </svg>
                                            @endif
                                        </span>
                                    </a>
                                </li> --}}
                                @foreach ($menu as $category)
                                    <li class="nav__item">
                                        <a class="nav__link sublist-toggler sublist-toggler--level-1" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#paw") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 13" fill="none" width="1em" height="1em" fill="currentColor">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M6.36676 1.73958C6.0663 0.567387 5.14229 -0.193129 4.30228 0.0430425C3.46335 0.278131 3.02396 1.42107 3.32443 2.59327C3.62489 3.76546 4.5489 4.52489 5.38891 4.2898C6.22891 4.05472 6.66723 2.91286 6.36676 1.73958Z" fill="currentColor"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.69772 0.0430425C8.85771 -0.193129 7.9337 0.566304 7.63323 1.73958C7.33277 2.91286 7.77108 4.05363 8.61109 4.2898C9.4511 4.52598 10.3762 3.76546 10.6756 2.59327C10.975 1.42107 10.5377 0.279214 9.69772 0.0430425Z" fill="currentColor"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8408 7.53988C12.6808 7.77605 13.6048 7.01661 13.9053 5.84334C14.2058 4.67006 13.7674 3.52929 12.9274 3.29311C12.0874 3.05694 11.1634 3.81638 10.863 4.98965C10.5636 6.16185 11.0008 7.3037 11.8408 7.53988Z" fill="currentColor"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.82278 12.1864C8.3677 12.584 9.12048 13 9.87218 13C10.696 13 11.3077 12.3749 11.3077 11.4833C11.3077 8.4499 9.04725 5.4165 7 5.4165C4.95275 5.4165 2.69227 8.4499 2.69227 11.4833C2.69227 12.3749 3.30396 13 4.12782 13C4.88059 13 5.63229 12.584 6.1783 12.1864C6.67692 11.8246 7.32416 11.8246 7.82278 12.1864Z" fill="currentColor"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.07256 3.29311C0.23255 3.5282 -0.205762 4.67006 0.0947027 5.84334C0.39409 7.01661 1.31918 7.77496 2.15918 7.53988C2.99919 7.30479 3.4375 6.16293 3.13704 4.98965C2.83657 3.81746 1.91257 3.05803 1.07256 3.29311Z" fill="currentColor"/>
                                                    </svg>
                                                @endif
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="categories-sublists">
                                {{-- <ul class="nav__sublist categories-sublist" data-sublist-index="0">
                                    <li class="nav__item nav__item--return">
                                        <button class="nav__mobile-return">
                                            <span class="icon">
                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                    <svg width="1em" height="1em" fill="currentColor">
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-left") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                                        <path
                                                            d="M15.5 5.07367L8.53683 12.0368L15.5 19"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />
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
                                </ul> --}}
                                @foreach ($menu as $category)
                                    <ul class="nav__sublist categories-sublist" data-sublist-index="{{ $loop->iteration }}">
                                        <li class="nav__item nav__item--return">
                                            <button class="nav__mobile-return">
                                                <span class="icon">
                                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-left") }}"></use>
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                                            <path
                                                                d="M15.5 5.07367L8.53683 12.0368L15.5 19"
                                                                stroke="currentColor"
                                                                stroke-width="1.5"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            />
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
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-right") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                        <path
                                                            d="M5.66699 3.38245L10.3091 8.02456L5.66699 12.6667"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />
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
                                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-right") }}"></use>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                        <path
                                                            d="M5.66699 3.38245L10.3091 8.02456L5.66699 12.6667"
                                                            stroke="currentColor"
                                                            stroke-width="1.5"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#close") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @endif
                                </span>
                            </button>
                            <div class="dropdown" data-dropdown data-fixed-dropdown>
                                <button class="link greedy-button" data-dropdown-button aria-label="Show dropdown">
                                    <span class="icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#fi-sr-menu-dots-vertical") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#fi-sr-menu-dots-vertical_clip0)">
                                                    <path d="M9.00034 2.66669C9.73673 2.66669 10.3337 2.06973 10.3337 1.33334C10.3337 0.596958 9.73673 0 9.00034 0C8.26395 0 7.66699 0.596958 7.66699 1.33334C7.66699 2.06973 8.26395 2.66669 9.00034 2.66669Z" fill="#B4B5CE"/>
                                                    <path d="M9.00034 9.33335C9.73673 9.33335 10.3337 8.73639 10.3337 8C10.3337 7.26361 9.73673 6.66665 9.00034 6.66665C8.26395 6.66665 7.66699 7.26361 7.66699 8C7.66699 8.73639 8.26395 9.33335 9.00034 9.33335Z" fill="#B4B5CE"/>
                                                    <path d="M9.00034 16C9.73673 16 10.3337 15.403 10.3337 14.6666C10.3337 13.9303 9.73673 13.3333 9.00034 13.3333C8.26395 13.3333 7.66699 13.9303 7.66699 14.6666C7.66699 15.403 8.26395 16 9.00034 16Z" fill="#B4B5CE"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="fi-sr-menu-dots-vertical_clip0">
                                                    <rect width="3.55556" height="16" fill="white" transform="translate(7.22266)"/>
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="icon is-hidden">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#close") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path
                                                d="M12.6172 5.66666L7.97507 10.3088L3.33296 5.66666"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path
                                                d="M12.6172 5.66666L7.97507 10.3088L3.33296 5.66666"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-down") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path
                                                d="M12.6172 5.66666L7.97507 10.3088L3.33296 5.66666"
                                                stroke="currentColor"
                                                stroke-width="1.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
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
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.875 17.8926C8.27344 17.8926 7.75 18.4002 7.75 19.0711C7.75 19.742 8.27344 20.2496 8.875 20.2496C9.47656 20.2496 10 19.742 10 19.0711C10 18.4002 9.47656 17.8926 8.875 17.8926ZM6.25 19.0711C6.25 17.6118 7.40549 16.3926 8.875 16.3926C10.3445 16.3926 11.5 17.6118 11.5 19.0711C11.5 20.5304 10.3445 21.7496 8.875 21.7496C7.40549 21.7496 6.25 20.5304 6.25 19.0711Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M16.375 17.8926C15.7734 17.8926 15.25 18.4002 15.25 19.0711C15.25 19.742 15.7734 20.2496 16.375 20.2496C16.9766 20.2496 17.5 19.742 17.5 19.0711C17.5 18.4002 16.9766 17.8926 16.375 17.8926ZM13.75 19.0711C13.75 17.6118 14.9055 16.3926 16.375 16.3926C17.8445 16.3926 19 17.6118 19 19.0711C19 20.5304 17.8445 21.7496 16.375 21.7496C14.9055 21.7496 13.75 20.5304 13.75 19.0711Z" fill="currentColor"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 3C1.875 2.58579 2.21079 2.25 2.625 2.25H4.77856C5.41521 2.25 5.95014 2.72849 6.02083 3.3612L7.04587 12.5355H18.7729C18.8813 12.5355 18.9773 12.4656 19.0107 12.3625L20.8625 6.64844C20.9149 6.48695 20.7945 6.32137 20.6247 6.32137H8.875C8.46079 6.32137 8.125 5.98558 8.125 5.57137C8.125 5.15715 8.46079 4.82137 8.875 4.82137H20.6247C21.813 4.82137 22.6558 5.98043 22.2895 7.11088L20.4376 12.825C20.2038 13.5467 19.5315 14.0355 18.7729 14.0355H6.57908L5.5483 14.6413C5.47808 14.6826 5.45059 14.7267 5.43722 14.7629C5.42143 14.8058 5.41737 14.8623 5.43374 14.9225C5.45012 14.9826 5.48226 15.0293 5.51758 15.0582C5.54748 15.0827 5.59352 15.1068 5.67497 15.1068H18.25C18.6642 15.1068 19 15.4426 19 15.8568C19 16.271 18.6642 16.6068 18.25 16.6068H5.67497C3.89172 16.6068 3.25086 14.2517 4.78824 13.3481L5.57563 12.8853L4.55494 3.75H2.625C2.21079 3.75 1.875 3.41421 1.875 3Z" fill="currentColor"/>
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
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#burger") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                    <path d="M3 5H21.07" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 12H21.07" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 19H21.07" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#candy-box") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M5 7C4.60444 7 4.21776 6.8827 3.88886 6.66294C3.55996 6.44318 3.30362 6.13082 3.15224 5.76537C3.00087 5.39992 2.96126 4.99778 3.03843 4.60982C3.1156 4.22186 3.30608 3.86549 3.58579 3.58579C3.86549 3.30608 4.22186 3.1156 4.60982 3.03843C4.99778 2.96126 5.39992 3.00087 5.76537 3.15224C6.13082 3.30362 6.44318 3.55996 6.66294 3.88886C6.8827 4.21776 7 4.60444 7 5C7 5.53043 6.78929 6.03914 6.41421 6.41421C6.03914 6.78929 5.53043 7 5 7ZM14 5C14 4.60444 13.8827 4.21776 13.6629 3.88886C13.4432 3.55996 13.1308 3.30362 12.7654 3.15224C12.3999 3.00087 11.9978 2.96126 11.6098 3.03843C11.2219 3.1156 10.8655 3.30608 10.5858 3.58579C10.3061 3.86549 10.1156 4.22186 10.0384 4.60982C9.96126 4.99778 10.0009 5.39992 10.1522 5.76537C10.3036 6.13082 10.56 6.44318 10.8889 6.66294C11.2178 6.8827 11.6044 7 12 7C12.5304 7 13.0391 6.78929 13.4142 6.41421C13.7893 6.03914 14 5.53043 14 5ZM21 5C21 4.60444 20.8827 4.21776 20.6629 3.88886C20.4432 3.55996 20.1308 3.30362 19.7654 3.15224C19.3999 3.00087 18.9978 2.96126 18.6098 3.03843C18.2219 3.1156 17.8655 3.30608 17.5858 3.58579C17.3061 3.86549 17.1156 4.22186 17.0384 4.60982C16.9613 4.99778 17.0009 5.39992 17.1522 5.76537C17.3036 6.13082 17.56 6.44318 17.8889 6.66294C18.2178 6.8827 18.6044 7 19 7C19.5304 7 20.0391 6.78929 20.4142 6.41421C20.7893 6.03914 21 5.53043 21 5ZM7 12C7 11.6044 6.8827 11.2178 6.66294 10.8889C6.44318 10.56 6.13082 10.3036 5.76537 10.1522C5.39992 10.0009 4.99778 9.96126 4.60982 10.0384C4.22186 10.1156 3.86549 10.3061 3.58579 10.5858C3.30608 10.8655 3.1156 11.2219 3.03843 11.6098C2.96126 11.9978 3.00087 12.3999 3.15224 12.7654C3.30362 13.1308 3.55996 13.4432 3.88886 13.6629C4.21776 13.8827 4.60444 14 5 14C5.53043 14 6.03914 13.7893 6.41421 13.4142C6.78929 13.0391 7 12.5304 7 12ZM14 12C14 11.6044 13.8827 11.2178 13.6629 10.8889C13.4432 10.56 13.1308 10.3036 12.7654 10.1522C12.3999 10.0009 11.9978 9.96126 11.6098 10.0384C11.2219 10.1156 10.8655 10.3061 10.5858 10.5858C10.3061 10.8655 10.1156 11.2219 10.0384 11.6098C9.96126 11.9978 10.0009 12.3999 10.1522 12.7654C10.3036 13.1308 10.56 13.4432 10.8889 13.6629C11.2178 13.8827 11.6044 14 12 14C12.5304 14 13.0391 13.7893 13.4142 13.4142C13.7893 13.0391 14 12.5304 14 12ZM21 12C21 11.6044 20.8827 11.2178 20.6629 10.8889C20.4432 10.56 20.1308 10.3036 19.7654 10.1522C19.3999 10.0009 18.9978 9.96126 18.6098 10.0384C18.2219 10.1156 17.8655 10.3061 17.5858 10.5858C17.3061 10.8655 17.1156 11.2219 17.0384 11.6098C16.9613 11.9978 17.0009 12.3999 17.1522 12.7654C17.3036 13.1308 17.56 13.4432 17.8889 13.6629C18.2178 13.8827 18.6044 14 19 14C19.5304 14 20.0391 13.7893 20.4142 13.4142C20.7893 13.0391 21 12.5304 21 12ZM7 19C7 18.6044 6.8827 18.2178 6.66294 17.8889C6.44318 17.56 6.13082 17.3036 5.76537 17.1522C5.39992 17.0009 4.99778 16.9613 4.60982 17.0384C4.22186 17.1156 3.86549 17.3061 3.58579 17.5858C3.30608 17.8655 3.1156 18.2219 3.03843 18.6098C2.96126 18.9978 3.00087 19.3999 3.15224 19.7654C3.30362 20.1308 3.55996 20.4432 3.88886 20.6629C4.21776 20.8827 4.60444 21 5 21C5.53043 21 6.03914 20.7893 6.41421 20.4142C6.78929 20.0391 7 19.5304 7 19ZM14 19C14 18.6044 13.8827 18.2178 13.6629 17.8889C13.4432 17.56 13.13082 17.3036 12.7654 17.1522C12.3999 17.0009 11.9978 16.9613 11.6098 17.0384C11.2219 17.1156 10.8655 17.3061 10.5858 17.5858C10.3061 17.8655 10.1156 18.2219 10.0384 18.6098C9.96126 18.9978 10.0009 19.3999 10.1522 19.7654C10.3036 20.1308 10.56 20.4432 10.8889 20.6629C11.2178 20.8827 11.6044 21 12 21C12.5304 21 13.0391 20.7893 13.4142 20.4142C13.7893 20.0391 14 19.5304 14 19ZM21 19C21 18.6044 20.8827 18.2178 20.6629 17.8889C20.4432 17.56 20.1308 17.3036 19.7654 17.1522C19.3999 17.0009 18.9978 16.9613 18.6098 17.0384C18.2219 17.1156 17.8655 17.3061 17.5858 17.5858C17.3061 17.8655 17.1156 18.2219 17.0384 18.6098C16.9613 18.9978 17.0009 19.3999 17.1522 19.7654C17.3036 20.1308 17.56 20.4432 17.8889 20.6629C18.2178 20.8827 18.6044 21 19 21C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="icon is-hidden">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#close-thin") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path
                                            d="M6 6L18.7742 18.7742"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                        <path
                                            d="M6 18.7742L18.7742 5.99998"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
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
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#search") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" fill="none" width="1em" height="1em" fill="currentColor">
                                            <path
                                                d="M8.25 15C11.9779 15 15 11.9779 15 8.25C15 4.52208 11.9779 1.5 8.25 1.5C4.52208 1.5 1.5 4.52208 1.5 8.25C1.5 11.9779 4.52208 15 8.25 15Z"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M16.5 16.5L13.5 13.5"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    @endif
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <a class="cart-button button" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                    <span class="icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.875 17.8926C8.27344 17.8926 7.75 18.4002 7.75 19.0711C7.75 19.742 8.27344 20.2496 8.875 20.2496C9.47656 20.2496 10 19.742 10 19.0711C10 18.4002 9.47656 17.8926 8.875 17.8926ZM6.25 19.0711C6.25 17.6118 7.40549 16.3926 8.875 16.3926C10.3445 16.3926 11.5 17.6118 11.5 19.0711C11.5 20.5304 10.3445 21.7496 8.875 21.7496C7.40549 21.7496 6.25 20.5304 6.25 19.0711Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M16.375 17.8926C15.7734 17.8926 15.25 18.4002 15.25 19.0711C15.25 19.742 15.7734 20.2496 16.375 20.2496C16.9766 20.2496 17.5 19.742 17.5 19.0711C17.5 18.4002 16.9766 17.8926 16.375 17.8926ZM13.75 19.0711C13.75 17.6118 14.9055 16.3926 16.375 16.3926C17.8445 16.3926 19 17.6118 19 19.0711C19 20.5304 17.8445 21.7496 16.375 21.7496C14.9055 21.7496 13.75 20.5304 13.75 19.0711Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 3C1.875 2.58579 2.21079 2.25 2.625 2.25H4.77856C5.41521 2.25 5.95014 2.72849 6.02083 3.3612L7.04587 12.5355H18.7729C18.8813 12.5355 18.9773 12.4656 19.0107 12.3625L20.8625 6.64844C20.9149 6.48695 20.7945 6.32137 20.6247 6.32137H8.875C8.46079 6.32137 8.125 5.98558 8.125 5.57137C8.125 5.15715 8.46079 4.82137 8.875 4.82137H20.6247C21.813 4.82137 22.6558 5.98043 22.2895 7.11088L20.4376 12.825C20.2038 13.5467 19.5315 14.0355 18.7729 14.0355H6.57908L5.5483 14.6413C5.47808 14.6826 5.45059 14.7267 5.43722 14.7629C5.42143 14.8058 5.41737 14.8623 5.43374 14.9225C5.45012 14.9826 5.48226 15.0293 5.51758 15.0582C5.54748 15.0827 5.59352 15.1068 5.67497 15.1068H18.25C18.6642 15.1068 19 15.4426 19 15.8568C19 16.271 18.6642 16.6068 18.25 16.6068H5.67497C3.89172 16.6068 3.25086 14.2517 4.78824 13.3481L5.57563 12.8853L4.55494 3.75H2.625C2.21079 3.75 1.875 3.41421 1.875 3Z" fill="currentColor"/>
                            </svg>
                        @endif
                    </span>
                    <span class="cart-button__text">{{ __('text.common_cart_text_d2') }}</span>
                    <span class="cart-button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                </a>
            </div>
        </header>

        <div class="promos">
            <div class="container">
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
                <div class="shop-info-wrapper">
                    <!-- Shop info-->
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
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#fi_879859') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="none" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#fi_879859_clip0)">
                                                    <path d="M28.0052 15.3885C27.8844 15.1416 27.8844 14.8583 28.0052 14.6114L29.1241 12.3225C29.747 11.048 29.2534 9.52897 28.0003 8.86412L25.7498 7.66998C25.507 7.54119 25.3405 7.31198 25.293 7.04133L24.8528 4.53184C24.6077 3.13462 23.3153 2.19572 21.911 2.39441L19.3883 2.75124C19.1161 2.78968 18.8468 2.70214 18.6493 2.51107L16.8182 0.739671C15.7986 -0.24669 14.2014 -0.246749 13.1818 0.739671L11.3507 2.51124C11.1531 2.70237 10.8839 2.78974 10.6116 2.75142L8.08902 2.39458C6.68424 2.19578 5.39226 3.13479 5.14716 4.53201L4.70701 7.04139C4.65949 7.31209 4.49302 7.54125 4.25027 7.6701L1.9997 8.86423C0.746621 9.52903 0.25303 11.0482 0.875995 12.3226L1.99484 14.6115C2.11554 14.8585 2.11554 15.1418 1.99484 15.3887L0.875936 17.6776C0.252971 18.952 0.746562 20.4711 1.99964 21.1359L4.25021 22.3301C4.49302 22.4588 4.65949 22.6881 4.70701 22.9587L5.14716 25.4682C5.37029 26.7401 6.46094 27.6322 7.71443 27.6321C7.83789 27.6321 7.96316 27.6234 8.08908 27.6056L10.6117 27.2487C10.8837 27.2101 11.1532 27.2978 11.3507 27.4889L13.1818 29.2603C13.6917 29.7535 14.3457 30.0001 15 30C15.6541 30 16.3085 29.7534 16.8181 29.2603L18.6493 27.4889C18.8469 27.2978 19.1162 27.2105 19.3883 27.2487L21.911 27.6056C23.3159 27.8043 24.6077 26.8654 24.8528 25.4681L25.293 22.9588C25.3406 22.6881 25.507 22.4589 25.7498 22.3301L28.0003 21.1359C29.2534 20.4711 29.747 18.952 29.1241 17.6775L28.0052 15.3885ZM11.5394 7.21377C13.2885 7.21377 14.7116 8.63683 14.7116 10.386C14.7116 12.1351 13.2885 13.5582 11.5394 13.5582C9.79028 13.5582 8.36722 12.1351 8.36722 10.386C8.36722 8.63683 9.79028 7.21377 11.5394 7.21377ZM9.90207 21.3213C9.73315 21.4902 9.51173 21.5747 9.29036 21.5747C9.06899 21.5747 8.84751 21.4903 8.67865 21.3213C8.3408 20.9835 8.3408 20.4357 8.67865 20.0978L20.0979 8.67861C20.4356 8.34076 20.9835 8.34076 21.3213 8.67861C21.6592 9.01646 21.6592 9.56425 21.3213 9.9021L9.90207 21.3213ZM18.4605 22.7863C16.7113 22.7863 15.2883 21.3632 15.2883 19.6141C15.2883 17.8649 16.7113 16.4419 18.4605 16.4419C20.2096 16.4419 21.6327 17.8649 21.6327 19.6141C21.6327 21.3632 20.2096 22.7863 18.4605 22.7863Z" fill="white"/>
                                                    <path d="M18.4605 18.1722C17.6654 18.1722 17.0186 18.819 17.0186 19.6141C17.0186 20.4091 17.6654 21.0559 18.4605 21.0559C19.2555 21.0559 19.9023 20.4091 19.9023 19.6141C19.9023 18.819 19.2555 18.1722 18.4605 18.1722Z" fill="white"/>
                                                    <path d="M11.5395 8.94406C10.7445 8.94406 10.0977 9.59087 10.0977 10.3859C10.0977 11.181 10.7445 11.8279 11.5395 11.8279C12.3346 11.8279 12.9814 11.181 12.9814 10.3859C12.9814 9.59093 12.3346 8.94406 11.5395 8.94406Z" fill="white"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="fi_879859_clip0">
                                                    <rect width="30" height="30" fill="white"/>
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
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#fi_10372960') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.25 1.25C10.5596 1.25 10 1.80965 10 2.5C10 3.19035 10.5596 3.75 11.25 3.75H18.75C19.4404 3.75 20 3.19035 20 2.5C20 1.80965 19.4404 1.25 18.75 1.25H11.25ZM7.55141 3.12239C7.49454 2.78192 7.22018 2.5 6.875 2.5H6.25C4.86929 2.5 3.75 3.61929 3.75 5V26.25C3.75 27.6308 4.86929 28.75 6.25 28.75H23.75C25.1308 28.75 26.25 27.6308 26.25 26.25V5C26.25 3.61929 25.1308 2.5 23.75 2.5H23.125C22.7799 2.5 22.5055 2.78192 22.4486 3.12239C22.1521 4.89724 20.609 6.25 18.75 6.25H11.25C9.39096 6.25 7.84788 4.89724 7.55141 3.12239ZM16.875 11.875C16.875 11.5299 16.5951 11.25 16.25 11.25L13.75 11.25C13.4047 11.25 13.125 11.5298 13.125 11.875V15.0001H10C9.65483 15.0001 9.375 15.2799 9.375 15.6251V18.1251C9.375 18.4703 9.65483 18.7501 10 18.7501H13.125V21.875C13.125 22.2201 13.4049 22.5 13.75 22.5H16.25C16.5951 22.5 16.875 22.2201 16.875 21.875V18.7501L20 18.75C20.3451 18.75 20.625 18.4702 20.625 18.125V15.6251C20.625 15.2799 20.3451 15.0001 20 15.0001H16.875V11.875Z" fill="white"/>
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
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#fi_6830961') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="none" width="1em" height="1em" fill="currentColor">
                                                <g clip-path="url(#fi_6830961_clip0)">
                                                    <path d="M15.1172 23.7264C15.1174 22.1195 15.5859 20.5476 16.4655 19.2028C17.345 17.8581 18.5974 16.7988 20.0695 16.1547C21.5416 15.5106 23.1695 15.3095 24.7541 15.576C26.3387 15.8426 27.8113 16.5652 28.9916 17.6555C29.0307 14.8309 29.0072 12.0063 28.9213 9.18165C28.8744 7.24102 27.7787 5.29278 25.943 4.33008C24.9996 3.83145 24.0223 3.35157 23.0338 2.89688C19.1279 4.16016 14.5535 5.82657 9.93984 7.88965C9.49626 8.08573 9.11625 8.40179 8.84263 8.80222C8.56902 9.20266 8.41267 9.67155 8.39121 10.1561C8.31914 12.3826 8.29688 14.6807 8.32617 16.9529C8.32853 17.0196 8.30946 17.0852 8.27176 17.1402C8.23406 17.1952 8.17972 17.2367 8.11671 17.2585C8.0537 17.2803 7.98536 17.2814 7.92171 17.2615C7.85806 17.2416 7.80246 17.2019 7.76309 17.1481C7.27481 16.5231 6.79785 15.9051 6.33223 15.2941C6.29785 15.2464 6.24677 15.2133 6.18913 15.2015C6.13149 15.1896 6.0715 15.1999 6.02109 15.2303C5.65898 15.4459 5.30332 15.6586 4.9541 15.8684C4.7584 15.9856 4.51758 15.8291 4.51641 15.5883C4.50469 13.3248 4.56094 11.0555 4.68223 8.85586C4.7332 7.92833 5.31504 7.05469 6.17578 6.6293C10.6008 4.44375 15.3217 2.6584 19.35 1.33711C18.4125 0.970317 17.4803 0.633989 16.5703 0.315239C15.3434 -0.106056 14.0109 -0.106056 12.784 0.315239C9.74766 1.3629 6.43184 2.73047 3.41074 4.33008C1.57559 5.29278 0.481055 7.24102 0.432422 9.18165C0.312891 13.0606 0.312891 16.9395 0.432422 20.8184C0.479297 22.759 1.57559 24.7072 3.41074 25.6699C6.43184 27.2695 9.74766 28.6371 12.7857 29.6842C14.0127 30.1055 15.3451 30.1055 16.5721 29.6842C16.8373 29.5928 17.1051 29.4984 17.3754 29.4012C15.9206 27.8705 15.1118 25.838 15.1172 23.7264Z" fill="white"/>
                                                    <path d="M23.3829 17.4533C22.1421 17.4534 20.9292 17.8215 19.8976 18.5109C18.8659 19.2004 18.0619 20.1803 17.5872 21.3267C17.1125 22.4731 16.9883 23.7345 17.2305 24.9514C17.4727 26.1684 18.0703 27.2862 18.9478 28.1635C19.8252 29.0408 20.9432 29.6382 22.1601 29.8801C23.3771 30.1221 24.6385 29.9977 25.7849 29.5228C26.9312 29.0479 27.9109 28.2437 28.6002 27.2119C29.2894 26.1801 29.6572 24.9672 29.6571 23.7264C29.6571 22.9025 29.4947 22.0867 29.1794 21.3256C28.864 20.5645 28.4018 19.8729 27.8192 19.2904C27.2366 18.7079 26.545 18.2458 25.7838 17.9306C25.0226 17.6154 24.2068 17.4532 23.3829 17.4533ZM26.9612 22.6881L23.1901 26.6478C23.0875 26.7558 22.9641 26.8419 22.8274 26.9009C22.6906 26.9599 22.5434 26.9906 22.3944 26.9912H22.3892C22.2411 26.9911 22.0946 26.9614 21.9582 26.9037C21.8218 26.846 21.6984 26.7616 21.5952 26.6555L19.5942 24.5941C19.3899 24.3836 19.2776 24.1004 19.282 23.807C19.2863 23.5137 19.4071 23.234 19.6177 23.0297C19.8282 22.8253 20.1114 22.713 20.4048 22.7174C20.6981 22.7218 20.9778 22.8425 21.1821 23.0531L22.3827 24.2889L25.3599 21.1623C25.5646 20.9617 25.8391 20.8484 26.1257 20.8462C26.4123 20.8441 26.6886 20.9532 26.8963 21.1507C27.104 21.3482 27.227 21.6185 27.2393 21.9049C27.2516 22.1912 27.1524 22.4711 26.9624 22.6857L26.9612 22.6881Z" fill="white"/>
                                                </g>
                                                <defs>
                                                    <clipPath id="fi_6830961_clip0">
                                                    <rect width="30" height="30" fill="white"/>
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
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#fi_5404297') }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" fill="none" width="1em" height="1em" fill="currentColor">
                                                <path d="M15.3255 0.941245C14.9111 0.930932 14.4967 0.941244 14.0842 0.968432C11.4086 1.14374 8.88298 2.00812 6.65735 3.68249V2.80874C6.65548 2.29406 6.23829 1.87687 5.7236 1.875C5.2061 1.87312 4.78423 2.29124 4.78235 2.80874V5.715C4.78235 6.55031 5.78923 6.9675 6.38079 6.37968C10.7111 2.05031 17.5652 1.60124 22.4224 5.32874C27.2795 9.05624 28.6183 15.7912 25.5583 21.0937C22.4964 26.3962 15.9977 28.5994 10.3408 26.2556C4.68391 23.9119 1.64923 17.7609 3.23454 11.8472C3.36954 11.3466 3.07142 10.8319 2.56985 10.6987C2.0711 10.5628 1.55642 10.8572 1.41954 11.3559C-0.404835 18.1659 3.10798 25.2937 9.62079 27.9928C16.1336 30.6909 23.6542 28.1306 27.1792 22.0275C30.7042 15.9244 29.1592 8.13093 23.5661 3.84C21.1192 1.96218 18.2242 1.00593 15.3255 0.941245ZM15.0039 5.40562C9.71548 5.40562 5.40392 9.71062 5.40392 14.9981C5.40392 20.2856 9.71642 24.5962 15.0039 24.5962C20.2914 24.5962 24.5945 20.2847 24.5945 14.9981C24.5945 9.71156 20.2914 5.40562 15.0039 5.40562ZM15.0039 9.10781C15.5214 9.10968 15.9395 9.53156 15.9377 10.0491V10.1934C16.8752 10.3209 17.6786 10.9022 18.1052 11.7112C18.3452 12.1697 18.168 12.7359 17.7086 12.9769C17.252 13.2169 16.6849 13.0406 16.443 12.5831C16.2752 12.2634 15.9508 12.0431 15.5495 12.0431H14.6777C14.102 12.0431 13.667 12.4781 13.667 13.0537C13.667 13.6294 14.102 14.0644 14.6777 14.0644H15.0039H15.0499H15.3283C16.9108 14.0644 18.2064 15.3684 18.2064 16.95C18.2064 18.3234 17.2305 19.4822 15.9377 19.7662V19.9528C15.9358 20.4675 15.5186 20.8847 15.0039 20.8866C14.4855 20.8894 14.0627 20.4712 14.0608 19.9528V19.8066C13.1252 19.6772 12.3217 19.0997 11.8961 18.2925C11.6542 17.835 11.8314 17.2659 12.2899 17.025C12.7492 16.7831 13.3164 16.9612 13.5574 17.4206C13.7252 17.7403 14.0561 17.9587 14.4564 17.9587H15.3283C15.9049 17.9587 16.3314 17.5256 16.3314 16.95C16.3314 16.3744 15.9049 15.9394 15.3283 15.9394H15.0039H14.6777C13.0952 15.9394 11.792 14.6353 11.792 13.0537C11.792 11.6841 12.7708 10.5262 14.0608 10.2394V10.0491C14.0589 9.52781 14.4817 9.10499 15.0039 9.10781Z" fill="white"/>
                                            </svg>
                                        @endif
                                    </span>
                                </div>
                                <div class="promos-item__title">{{ __('text.common_moneyback') }}</div>
                                <div class="promos-item__text">{{ __('text.common_refund') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shop verification-->
                    <div class="shop-verification">
                        <div class="promos-caption">
                            <div class="promos-caption__icon">
                                <span class="icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#fi_8861036') }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 20" fill="none" width="1em" height="1em" fill="currentColor" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' verified pharmacy' }}" @endif>
                                            <g clip-path="url(#fi_8861036_clip0)">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.57787 0.0660864L1.35564 1.61943C0.559831 1.81811 0 2.54645 0 3.38311V11.1012C0 14.1566 1.70436 16.9451 4.3921 18.2871L7.6096 19.8936C7.7328 19.955 7.8664 19.9858 8 19.9858C8.1336 19.9858 8.2672 19.955 8.3904 19.8936L11.6079 18.2871C14.2956 16.9451 16 14.1566 16 11.1012V3.38311C16 2.54645 15.4402 1.81811 14.6444 1.61943L8.42213 0.0660864C8.28347 0.0314719 8.14178 0.0141602 8 0.0141602C7.85822 0.0141602 7.71653 0.0314719 7.57787 0.0660864ZM12.1841 7.91849C12.5312 7.56397 12.5312 6.98918 12.1841 6.63467C11.837 6.28014 11.2741 6.28014 10.927 6.63466L7.11111 10.5317L5.07298 8.45027C4.72584 8.09575 4.16303 8.09575 3.8159 8.45027C3.46877 8.80477 3.46877 9.37959 3.81591 9.73408L6.48258 12.4575C6.82969 12.812 7.39253 12.812 7.73964 12.4575L12.1841 7.91849Z" fill="white"/>
                                            </g>
                                            <defs>
                                                <clipPath id="fi_8861036_clip0">
                                                <rect width="16" height="20" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    @endif
                                </span>
                            </div>
                            <div class="promos-caption__title">{{ __('text.common_verified') }}</div>
                            <div class="promos-caption__text">{{ __('text.common_approved_d4') }}</div>
                        </div>

                        <!-- Header brands-->
                        <div class="promos-brands">
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-1-48w.webp 1x, ' . $design . '/img/brands/brand-1-97w.webp 2x') }}">
                                    <img src="{{ asset($design . '/img/brands/brand-1-48w.png') }}" srcset="{{ asset($design . '/img/brands/brand-1-48w.png 1x, ' . $design . '/img/brands/brand-1-97w.png 2x') }}" width="49" height="32" alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-2-46w.webp 1x, ' . $design . '/img/brands/brand-2-93w.webp 2x') }}"><img
                                    src="{{ asset($design . '/img/brands/brand-2-46w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-2-46w.png 1x, ' . $design . '/img/brands/brand-2-93w.png 2x') }}" width="47" height="36"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-4-45w.webp 1x, ' . $design . '/img/brands/brand-4-90w.webp 2x') }}"><img
                                    src="{{ asset($design . '/img/brands/brand-4-45w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-4-45w.png 1x, ' . $design . '/img/brands/brand-4-90w.png 2x') }}" width="45" height="27"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-3-69w.webp 1x, ' . $design . '/img/brands/brand-3-138w.webp 2x') }}"><img
                                    src="{{ asset($design . '/img/brands/brand-3-69w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-3-69w.png 1x, ' . $design . '/img/brands/brand-3-138w.png 2x') }}" width="69" height="36"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-5-56w.webp 1x, ' . $design . '/img/brands/brand-5-113w.webp 2x') }}"><img
                                    src="{{ asset($design . '/img/brands/brand-5-56w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-5-56w.png 1x, ' . $design . '/img/brands/brand-5-113w.png 2x') }}" width="57" height="32"
                                    alt="Brand">
                                </picture>
                            </div>
                            <div class="promos-brand">
                                <picture>
                                <source type="image/webp" srcset="{{ asset($design . '/img/brands/brand-6-72w.webp 1x, ' . $design . '/img/brands/brand-6-145w.webp 2x') }}"><img
                                    src="{{ asset($design . '/img/brands/brand-6-72w.png') }}"
                                    srcset="{{ asset($design . '/img/brands/brand-6-72w.png 1x, ' . $design . '/img/brands/brand-6-145w.png 2x') }}" width="73" height="30"
                                    alt="Brand">
                                </picture>
                            </div>
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
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#close") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#close") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                <path d="M6 6L18.7742 18.7742" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M6 18.7742L18.7742 5.99998" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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

        <script defer src="{{ asset_ver("$design/js/main.f9d6eb0f.js") }}"></script>
        <script defer src="{{ asset_ver("$design/js/app.js") }}"></script>
        <script defer src="{{ asset_ver('js/all_js.js') }}"></script>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
    </body>
</html>