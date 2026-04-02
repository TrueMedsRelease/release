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
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#454d58"/>
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

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset_ver("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset_ver($design . '/css/style.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script defer src="{{ asset_ver("vendor/jquery/autocomplete.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script async src="https://true-serv.net/static/statistics/assets/js/v1/main.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>
<body>
    <script>
        let flagc = false;
        let flagp = false;
        const design = 3;
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

<div class="wrapper">
	<header class="header">

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/black_friday_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/new_year_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/valentine_day_big.png") }}">
        </div> --}}

		<div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none">
                        <a class="request_call">{{ __('text.common_callback') }}</a>
                        <div class="request_text">{{__('text.common_call_us_top')}}</div>
                    </div>
                    @foreach ($phone_arr as $id_phone => $phones)
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                    @endforeach
                </div>
            </div>
		</div>
		<div class="header__top top-header">
            <div class="top-header__container">
                <div class="top-header__menu menu">
                    <button type="button" class="menu__icon icon-menu"><span></span></button>
                    <nav class="menu__body">
                        <ul class="menu__list">
                            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                                @php
                                    $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                                @endphp
                                <li class="menu__item best"><a class="menu__link" data-bestsellers>{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.about', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.help', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_help_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.delivery', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.bonus_ref_menu')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                            @else
                                <li class="menu__item best"><a class="menu__link" data-bestsellers>{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.about', '') }}" class="menu__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.help', '') }}" class="menu__link">{{__('text.common_help_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.testimonials', '') }}" class="menu__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.delivery', '') }}" class="menu__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.bonus_referral_program', '') }}" class="menu__link">{{__('text.bonus_ref_menu')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.moneyback', '') }}" class="menu__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.contact_us', '') }}" class="menu__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
                <div data-tabs class="top-header__bestsellers bestsellers">
                    <nav data-tabs-titles class="bestsellers__navigation">
                        <button type="button" class="bestsellers__title _tab-active">{{__('text.common_best_selling_title')}}</button>
                        @foreach ($menu as $category)
                            <button type="button" class="bestsellers__title">{{ $category['name'] }}</button>
                        @endforeach
                    </nav>
                    <div data-tabs-body class="bestsellers__content">
                        <ul class="bestsellers__body">
                            @foreach ($bestsellers as $bestseller)
                                <li>
                                    <a href="{{ route('home.product', $bestseller['url']) }}" class="bestsellers__item">
                                        {{ $bestseller['name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                            @foreach ($menu as $category)
                                <ul class="bestsellers__body">
                                    @foreach ($category['products'] as $item)
                                        <li>
                                            <a href="{{ route('home.product', $item['url']) }}" class="bestsellers__item">
                                                {{ $item['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="header__main">
            <div class="container header__container">
                <div class="header__inner">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <a href="{{ route('home.index') }}" class="header__logo"><img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="{{ $domainWithoutZone }}"></a>
                    @else
                        <a href="{{ route('home.index') }}" class="header__logo"><img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="Logo"></a>
                    @endif
                    <div class="header__actions" data-one-select data-da=".top-header__container, 700, last">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="header__select">
                                <select name="form[]" class="form" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif>{{$item['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (count($Currency::GetAllCurrency()) > 1)
                            <div class="header__select">
                                <select name="form[]" class="form" id="curr_select" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <a href='{{ route('home.login') }}' target="_blank" class="header__status">{{__('text.common_profile')}}</a>
                    </div>
                    <a href="{{ route('cart.index') }}" class="header__cart cart-header">
                        @php
                            $cart_count = 0;
                            $cart_total = 0;
                            if(!empty(session('cart')))
                            {
                                foreach (session('cart') as $value) {
                                    $cart_count += $value['q'];
                                    $cart_total += $value['price'] * $value['q'];
                                }
                            }
                        @endphp
                        @if ($cart_count != 0)
                            <div class="cart-header__left">
                                <div class="cart-header__icon">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="20" height="20">
                                            <path fill="#000" fill-rule="evenodd" d="M2.451 2.025C2.136 2 1.728 2 1.111 2H1a1 1 0 1 1 0-2h.148c.57 0 1.054 0 1.454.03.421.032.822.101 1.213.277a3.5 3.5 0 0 1 1.482 1.255c.094.142.172.288.238.438h14.782c.51 0 .958 0 1.322.032.38.034.792.112 1.17.348a2.5 2.5 0 0 1 1.086 1.46c.118.43.074.847-.003 1.22-.073.359-.202.788-.349 1.277l-1.401 4.67-.043.143c-.201.674-.377 1.266-.745 1.725a3 3 0 0 1-1.219.907c-.546.22-1.163.22-1.866.218H9.176c-.452 0-.845 0-1.173-.025a3.032 3.032 0 0 1-1.037-.238 3 3 0 0 1-1.27-1.076 3.031 3.031 0 0 1-.405-.983c-.078-.32-.143-.708-.217-1.153L4.07 4.507c-.102-.609-.17-1.01-.245-1.318-.072-.295-.136-.431-.195-.52a1.5 1.5 0 0 0-.635-.538c-.097-.043-.242-.084-.545-.106ZM6.014 4l.024.142 1.003 6.02c.081.49.134.802.192 1.039.055.225.1.309.129.353a1 1 0 0 0 .423.358c.048.022.138.051.369.069.243.018.56.019 1.057.019h8.908c.943 0 1.131-.018 1.267-.073a1 1 0 0 0 .407-.302c.091-.115.163-.29.433-1.193l1.39-4.63c.162-.541.264-.884.317-1.143.042-.208.035-.28.033-.292a.5.5 0 0 0-.216-.29c-.01-.005-.078-.034-.29-.052C21.198 4 20.84 4 20.275 4H6.014Zm15.737.077h-.001.001Zm.215.289v.001-.001Z" clip-rule="evenodd"/>
                                            <path fill="#000" d="M7 18a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm9 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z"/>
                                        </svg>
                                    @endif
                                </div>
                                <span>{{__('text.common_cart_text_d2')}}</span>
                            </div>
                            <div class="cart-header__info">
                                <div class="cart-header__quantity">{{$cart_count}} {{__('text.common_items_d4')}}</div>
                                <div class="cart-header__total">{{ $Currency::convert($cart_total) }}</div>
                            </div>
                        @else
                            <span class="cart__icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="20" height="20">
                                        <path fill="#000" fill-rule="evenodd" d="M2.451 2.025C2.136 2 1.728 2 1.111 2H1a1 1 0 1 1 0-2h.148c.57 0 1.054 0 1.454.03.421.032.822.101 1.213.277a3.5 3.5 0 0 1 1.482 1.255c.094.142.172.288.238.438h14.782c.51 0 .958 0 1.322.032.38.034.792.112 1.17.348a2.5 2.5 0 0 1 1.086 1.46c.118.43.074.847-.003 1.22-.073.359-.202.788-.349 1.277l-1.401 4.67-.043.143c-.201.674-.377 1.266-.745 1.725a3 3 0 0 1-1.219.907c-.546.22-1.163.22-1.866.218H9.176c-.452 0-.845 0-1.173-.025a3.032 3.032 0 0 1-1.037-.238 3 3 0 0 1-1.27-1.076 3.031 3.031 0 0 1-.405-.983c-.078-.32-.143-.708-.217-1.153L4.07 4.507c-.102-.609-.17-1.01-.245-1.318-.072-.295-.136-.431-.195-.52a1.5 1.5 0 0 0-.635-.538c-.097-.043-.242-.084-.545-.106ZM6.014 4l.024.142 1.003 6.02c.081.49.134.802.192 1.039.055.225.1.309.129.353a1 1 0 0 0 .423.358c.048.022.138.051.369.069.243.018.56.019 1.057.019h8.908c.943 0 1.131-.018 1.267-.073a1 1 0 0 0 .407-.302c.091-.115.163-.29.433-1.193l1.39-4.63c.162-.541.264-.884.317-1.143.042-.208.035-.28.033-.292a.5.5 0 0 0-.216-.29c-.01-.005-.078-.034-.29-.052C21.198 4 20.84 4 20.275 4H6.014Zm15.737.077h-.001.001Zm.215.289v.001-.001Z" clip-rule="evenodd"/>
                                        <path fill="#000" d="M7 18a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm9 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="cart__text">{{__('text.common_cart_text_d2')}}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <section class="pay-index" data-da=".header, 1150, last">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#visa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/visa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#mastercard') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/mastercard.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#maestro') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/maestro.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#discover') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/discover.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amex') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#jsb') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/jsb.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#unionpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/unionpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' unionpay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#dinners-club') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/dinners-club.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#apple-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/apple-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#google-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/google-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#amazon-pay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/amazon-pay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#stripe') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/stripe.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#paypal') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/paypal.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#sepa') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/sepa.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#cashapp') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/cashapp.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#adyen') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/adyen.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#skrill') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/skrill.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#worldpay') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/worldpay.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#payline') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/payline.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#bitcoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/bitcoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#binance-coin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/binance-coin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#ethereum') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/ethereum.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#litecoin') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/litecoin.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#tron') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/tron.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(erc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(erc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                        @endif
                    </li>
                    <li class="pay-index__item">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                                <use width="100%" height="100%" href="{{ asset('pub_images/pay_icons/sprite.svg#usdt(trc20)') }}">
                            </svg>
                        @else
                            <img width="100%" height="100%" src="{{ asset('pub_images/pay_icons/usdt(trc20).svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                        @endif
                    </li>
                </ul>
            </div>
        </section>

        <div class="header__search">
            <div class="container header__container">
                <form class="search" action="{{ route('search.search_product') }}" method = "POST">
                    @csrf
                    <div class="search__input  search-bar">
                        <div class="search__icon">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="16" height="16">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" width="16" height="16">
                                    <path fill-rule="evenodd" d="M7.333 2.083a5.25 5.25 0 1 0 0 10.5 5.25 5.25 0 0 0 0-10.5Zm-6.75 5.25a6.75 6.75 0 1 1 13.5 0 6.75 6.75 0 0 1-13.5 0Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M11.47 11.47a.75.75 0 0 1 1.06 0l2.667 2.666a.75.75 0 0 1-1.06 1.061L11.47 12.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                        <input id="autocomplete" autocomplete="off" type="text" name="search_text" placeholder="{{__('text.common_search')}}" class="input input--search">
                    </div>
                    <div class="search__select">
                        <div class="search__caption">{{__('text.common_first_letter')}}</div>
                        {{-- <div class="search__selected-letter">{$data.first_letter}</div> --}}
                        <div class="search__buttons">
                            @foreach ($first_letters as $key => $active_letter)
                                @if ($active_letter)
                                    <div class="search__button">
                                        <a type="button" href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                                    </div>
                                @else
                                    <div class="search__button">
                                        {{ $key }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="search__icon-down">
                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                <svg width="16" height="16">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-down") }}"></use>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 16" fill="currentColor" width="16" height="16">
                                    <path d="M13.14 6.273a.667.667 0 0 0-.947 0L9.14 9.327a.667.667 0 0 1-.946 0L5.14 6.273a.667.667 0 1 0-.946.94l3.06 3.06a2 2 0 0 0 2.826 0l3.06-3.06a.667.667 0 0 0 0-.94Z"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>
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
                <div class="popup_head">{{__('text.common_push_head')}}</div>
                <div class="popup_push_text">{{__('text.common_push_text')}}</div>
                <div class="push_buttons">
                    <div class="push_decline">{{__('text.common_decline')}}</div>
                    <div class="push_allow">{{__('text.common_allow')}}</div>
                </div>
            </div>
        </div>
    </div>
    <main class="page">
        <div class="page__container page__container--home">
            <div class="checkup top" onclick="location.href='{{ route('home.checkup') }}'">
                <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_middle.png") }}">
                <div></div>
            </div>
            @yield('title_3', '')
            <h2 class="page__title title" data-da=".page__products, 1150, first">@yield('title_2', '')</h2>
            <div class="page__inner">
                <div class="page__sidebar">
                    <aside class="sidebar">
                        <div class="sidebar__preference preference">
                            <div class="preference__image">
                                <picture>
                                    <source class="lazy" data-srcset="{{ asset("$design/images/doctor-s.webp") }}" media="(max-width:479px)" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp">
                                    <source class="lazy" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-srcset="{{ asset("$design/images/doctor.webp") }}" type="image/webp">
                                    <img loading="lazy" class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/doctor.png") }}" alt="">
                                </picture>
                            </div>

                            <aside class="categories-sidebar">
                                <div class="categories-sidebar__inner">
                                    <div class="categories-sidebar__top-row">
                                        <div class="categories-sidebar__icon"><button type="button" class="icon-menu icon-menu--first"><span></span></button></div>
                                        <h3 class="categories-sidebar__title">{{__('text.common_categories_menu')}}</h3>
                                    </div>
                                    <div data-spollers class="categories-sidebar__spollers spollers">
                                        <div class="spollers__item">
                                            <button type="button" data-spoller class="spollers__title _spoller-active">{{__('text.common_best_selling_title')}}</button>
                                            <ul class="spollers__body main_bestsellers" id="main_bestsellers">
                                                @foreach ($bestsellers as $bestseller)
                                                    <li class="spollers__item-list"><a href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @foreach ($menu as $category)
                                            <div class="spollers__item">
                                                <button type="button" data-spoller class="spollers__title @if($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                                                <ul class="spollers__body">
                                                    @foreach ($category['products'] as $item)
                                                        <li class="spollers__item-list">
                                                            <a href="{{ route('home.product', $item['url']) }}">
                                                                {{ $item['name'] }}
                                                            </a>
                                                            <span style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </aside>

                            <div class="preference__inner">
                                <div class="preference__top">
                                    <h2 class="preference__label">10 000 000</h2>
                                    <div class="preference__left">
                                        <div class="preference__stars">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="78" height="14">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="78" height="14">
                                                    <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <p class="preference__text">{{__('text.common_customers')}}</p>
                                    </div>
                                </div>
                                <div class="preference__items">
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="24" height="24">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-01") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                    <path fill="#454D58" d="M15.167 8.317a.772.772 0 0 0-.26-1.052.754.754 0 0 0-1.04.263l-5.175 8.72a.772.772 0 0 0 .26 1.052c.36.218.825.1 1.04-.263l5.175-8.72Z"/>
                                                    <path fill="#454D58" fill-rule="evenodd" d="M6.926 9.201c0-1.13.905-2.045 2.022-2.045 1.117 0 2.023.916 2.023 2.045v.511c0 1.13-.906 2.045-2.023 2.045a2.034 2.034 0 0 1-2.022-2.045v-.511Zm2.7 0v.511a.681.681 0 0 1-.678.685.681.681 0 0 1-.677-.685v-.511c0-.378.303-.685.677-.685.375 0 .678.307.678.685Zm5.39 3.579a2.034 2.034 0 0 0-2.023 2.045v.51c0 1.13.906 2.046 2.023 2.046a2.034 2.034 0 0 0 2.022-2.045v-.511c0-1.13-.905-2.045-2.022-2.045Zm.677 2.556v-.511a.681.681 0 0 0-.677-.685.681.681 0 0 0-.678.684v.512c0 .378.304.685.678.685a.681.681 0 0 0 .677-.685Z" clip-rule="evenodd"/>
                                                    <path fill="#454D58" fill-rule="evenodd" d="M14.385 1.023a3.292 3.292 0 0 0-4.77 0l-.77.805c-.276.29-.67.435-1.065.393l-1.102-.117c-1.866-.198-3.523 1.208-3.654 3.1l-.078 1.117a1.312 1.312 0 0 1-.567.993l-.917.627C-.094 9.002-.47 11.156.633 12.69l.65.906c.235.325.307.742.198 1.129l-.305 1.076c-.517 1.824.564 3.718 2.385 4.178l1.074.27c.387.098.707.37.87.738l.45 1.022a3.302 3.302 0 0 0 4.483 1.65l.995-.49a1.282 1.282 0 0 1 1.134 0l.995.49a3.302 3.302 0 0 0 4.483-1.65l.45-1.022c.163-.368.483-.64.87-.737l1.074-.271c1.82-.46 2.902-2.354 2.385-4.178l-.305-1.076a1.32 1.32 0 0 1 .197-1.13l.65-.905c1.103-1.536.728-3.689-.828-4.75l-.917-.627a1.312 1.312 0 0 1-.567-.993l-.078-1.117c-.131-1.892-1.788-3.298-3.654-3.1l-1.102.117a1.286 1.286 0 0 1-1.065-.393l-.77-.805ZM11.07 2.444a1.284 1.284 0 0 1 1.862 0l.77.805a3.296 3.296 0 0 0 2.73 1.005l1.101-.116a1.299 1.299 0 0 1 1.427 1.21l.077 1.116A3.361 3.361 0 0 0 20.49 9.01l.918.627c.607.414.753 1.254.323 1.853l-.65.906a3.384 3.384 0 0 0-.505 2.894l.305 1.076a1.31 1.31 0 0 1-.931 1.63l-1.074.272c-.99.25-1.811.946-2.227 1.889l-.45 1.022a1.288 1.288 0 0 1-1.75.644l-.995-.49a3.284 3.284 0 0 0-2.906 0l-.995.49a1.289 1.289 0 0 1-1.75-.644l-.45-1.023a3.327 3.327 0 0 0-2.227-1.888l-1.074-.271a1.31 1.31 0 0 1-.93-1.63l.304-1.077a3.384 3.384 0 0 0-.505-2.894l-.65-.906a1.317 1.317 0 0 1 .323-1.853l.918-.627a3.361 3.361 0 0 0 1.453-2.545l.077-1.116a1.299 1.299 0 0 1 1.427-1.21l1.1.116A3.296 3.296 0 0 0 10.3 3.25l.77-.805Z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_save')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_discount')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="24" height="24">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-03") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 22" width="24" height="24">
                                                    <path fill="#454D58" fill-rule="evenodd" d="M5.466 0h13.068c.67 0 1.222 0 1.675.032.47.032.903.1 1.321.273a4 4 0 0 1 2.165 2.165c.173.418.241.852.273 1.32.04.578.031 1.159.031 1.737 0 .21 0 .414-.012.589a2.03 2.03 0 0 1-.14.65 2 2 0 0 1-1.082 1.082 2.23 2.23 0 0 1-.766.146v8.247c0 .805 0 1.47-.044 2.01-.046.563-.144 1.08-.392 1.564a4 4 0 0 1-1.747 1.748c-.486.248-1.002.346-1.565.392-.54.044-1.205.044-2.01.044H7.759c-.805 0-1.47 0-2.01-.044-.563-.046-1.08-.144-1.564-.392a4 4 0 0 1-1.748-1.748c-.248-.485-.346-1.002-.392-1.564C2 17.711 2 17.046 2 16.241V7.994a2.213 2.213 0 0 1-.766-.146A2 2 0 0 1 .153 6.766a2.029 2.029 0 0 1-.14-.65A9.143 9.143 0 0 1 0 5.526c0-.576-.009-1.158.03-1.735.033-.47.101-.903.274-1.321A4 4 0 0 1 2.47.305c.418-.173.852-.241 1.32-.273C4.244 0 4.796 0 5.466 0ZM2.484 6c-.248 0-.387 0-.475-.008A7.659 7.659 0 0 1 2 5.5c0-.712 0-1.197.026-1.573.025-.367.07-.558.126-.692a2 2 0 0 1 1.082-1.082c.134-.055.325-.101.692-.126C4.304 2 4.787 2 5.5 2h13c.712 0 1.197 0 1.573.026.367.025.558.07.692.126a2 2 0 0 1 1.082 1.082c.055.134.101.325.126.692.026.376.026.86.026 1.573 0 .258 0 .402-.008.492a7.337 7.337 0 0 1-.475.008H2.484ZM4 8v8.2c0 .856.001 1.439.038 1.889.036.438.1.662.18.819a2 2 0 0 0 .874.873c.157.08.381.145.82.18C6.361 20 6.943 20 7.8 20h8.4c.856 0 1.439-.001 1.889-.038.438-.036.662-.1.819-.18a2 2 0 0 0 .873-.875c.08-.156.145-.38.18-.819.038-.45.039-1.032.039-1.888V8H4Z" clip-rule="evenodd"/>
                                                    <path fill="#454D58" fill-rule="evenodd" d="M16.207 10.293a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 1 1 1.414-1.414l1.293 1.293 4.293-4.293a1 1 0 0 1 1.414 0Z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_prescription')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_restrictions')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="24" height="22">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-02") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="22">
                                                    <path fill="#454D58" d="M12 0c6.627 0 12 5.373 12 12s-5.373 12-12 12S0 18.627 0 12 5.373 0 12 0Zm0 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2Zm4.29 6.29c.39-.39 1.03-.39 1.42 0 .39.39.39 1.03 0 1.42l-7.003 6.997a1 1 0 0 1-1.414 0l-3.5-3.5a1 1 0 1 1 1.414-1.414L10 14.586l6.29-6.296Z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_delivery')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_receive')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="24" height="24">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-01") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                                    <path fill="#454D58" d="M15.167 8.317a.772.772 0 0 0-.26-1.052.754.754 0 0 0-1.04.263l-5.175 8.72a.772.772 0 0 0 .26 1.052c.36.218.825.1 1.04-.263l5.175-8.72Z"/>
                                                    <path fill="#454D58" fill-rule="evenodd" d="M6.926 9.201c0-1.13.905-2.045 2.022-2.045 1.117 0 2.023.916 2.023 2.045v.511c0 1.13-.906 2.045-2.023 2.045a2.034 2.034 0 0 1-2.022-2.045v-.511Zm2.7 0v.511a.681.681 0 0 1-.678.685.681.681 0 0 1-.677-.685v-.511c0-.378.303-.685.677-.685.375 0 .678.307.678.685Zm5.39 3.579a2.034 2.034 0 0 0-2.023 2.045v.51c0 1.13.906 2.046 2.023 2.046a2.034 2.034 0 0 0 2.022-2.045v-.511c0-1.13-.905-2.045-2.022-2.045Zm.677 2.556v-.511a.681.681 0 0 0-.677-.685.681.681 0 0 0-.678.684v.512c0 .378.304.685.678.685a.681.681 0 0 0 .677-.685Z" clip-rule="evenodd"/>
                                                    <path fill="#454D58" fill-rule="evenodd" d="M14.385 1.023a3.292 3.292 0 0 0-4.77 0l-.77.805c-.276.29-.67.435-1.065.393l-1.102-.117c-1.866-.198-3.523 1.208-3.654 3.1l-.078 1.117a1.312 1.312 0 0 1-.567.993l-.917.627C-.094 9.002-.47 11.156.633 12.69l.65.906c.235.325.307.742.198 1.129l-.305 1.076c-.517 1.824.564 3.718 2.385 4.178l1.074.27c.387.098.707.37.87.738l.45 1.022a3.302 3.302 0 0 0 4.483 1.65l.995-.49a1.282 1.282 0 0 1 1.134 0l.995.49a3.302 3.302 0 0 0 4.483-1.65l.45-1.022c.163-.368.483-.64.87-.737l1.074-.271c1.82-.46 2.902-2.354 2.385-4.178l-.305-1.076a1.32 1.32 0 0 1 .197-1.13l.65-.905c1.103-1.536.728-3.689-.828-4.75l-.917-.627a1.312 1.312 0 0 1-.567-.993l-.078-1.117c-.131-1.892-1.788-3.298-3.654-3.1l-1.102.117a1.286 1.286 0 0 1-1.065-.393l-.77-.805ZM11.07 2.444a1.284 1.284 0 0 1 1.862 0l.77.805a3.296 3.296 0 0 0 2.73 1.005l1.101-.116a1.299 1.299 0 0 1 1.427 1.21l.077 1.116A3.361 3.361 0 0 0 20.49 9.01l.918.627c.607.414.753 1.254.323 1.853l-.65.906a3.384 3.384 0 0 0-.505 2.894l.305 1.076a1.31 1.31 0 0 1-.931 1.63l-1.074.272c-.99.25-1.811.946-2.227 1.889l-.45 1.022a1.288 1.288 0 0 1-1.75.644l-.995-.49a3.284 3.284 0 0 0-2.906 0l-.995.49a1.289 1.289 0 0 1-1.75-.644l-.45-1.023a3.327 3.327 0 0 0-2.227-1.888l-1.074-.271a1.31 1.31 0 0 1-.93-1.63l.304-1.077a3.384 3.384 0 0 0-.505-2.894l-.65-.906a1.317 1.317 0 0 1 .323-1.853l.918-.627a3.361 3.361 0 0 0 1.453-2.545l.077-1.116a1.299 1.299 0 0 1 1.427-1.21l1.1.116A3.296 3.296 0 0 0 10.3 3.25l.77-.805Z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_moneyback')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_refund')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar__info" data-da=".page__container, 1150, last">
                            <div class="sidebar__verified verified">
                                <div class="verified__top">
                                    <div class="verified__icon">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="24" height="24">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-verified") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 25" width="24" height="24">
                                                <path fill="#fff" d="M16.283.5c2.137.023 3.224.253 4.351.856a6.048 6.048 0 0 1 2.51 2.51c.603 1.127.833 2.214.856 4.35v8.567c-.023 2.137-.253 3.224-.856 4.351a6.048 6.048 0 0 1-2.51 2.51c-1.127.603-2.214.833-4.35.856H7.716c-2.137-.023-3.224-.253-4.351-.856a6.048 6.048 0 0 1-2.51-2.51C.253 20.008.023 18.92 0 16.785V8.216c.023-2.136.253-3.223.856-4.35a6.048 6.048 0 0 1 2.51-2.51C4.493.753 5.58.523 7.716.5h8.567Zm-.366 2.18H8.083l-.608.006c-1.614.03-2.337.197-3.08.594A3.866 3.866 0 0 0 2.78 4.895c-.397.743-.564 1.466-.594 3.08l-.006.608v7.834l.006.608c.03 1.615.197 2.337.594 3.08.374.7.916 1.241 1.615 1.615.743.398 1.466.564 3.08.594l.608.006h7.834l.608-.006c1.615-.03 2.337-.197 3.08-.594a3.866 3.866 0 0 0 1.615-1.615c.398-.743.564-1.465.594-3.08l.006-.608V8.583l-.006-.608c-.03-1.614-.197-2.337-.594-3.08a3.866 3.866 0 0 0-1.615-1.615c-.743-.397-1.465-.564-3.08-.594l-.608-.006Zm.06 6.214a1.091 1.091 0 0 1 1.543 1.543l-6.11 6.11a1.091 1.091 0 0 1-1.543 0L7.03 13.71a1.091 1.091 0 1 1 1.543-1.543l2.065 2.065 5.338-5.338Z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="verified__descr">
                                        <h3 class="verified__label">{{__('text.common_verified')}}</h3>
                                        <p class="verified__text">{{__('text.common_approved')}}</p>
                                    </div>
                                </div>
                                <div class="verified__logos">
                                    <div class="verified__logo">
                                        <img loading="lazy" src="{{ asset("$design/images/partners/fda.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <picture><source class="lazy" data-srcset="{{ asset("$design/images/partners/pgeu.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img loading="lazy" class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/partners/pgeu.png") }}" width="60" height="auto" alt=""></picture>
                                    </div>
                                    <div class="verified__logo">
                                        <picture><source class="lazy" data-srcset="{{ asset("$design/images/partners/cipa.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img loading="lazy" class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/partners/cipa.png") }}" width="67" height="auto" alt=""></picture>
                                    </div>
                                    <div class="verified__logo">
                                        <img loading="lazy" src="{{ asset("$design/images/partners/mastercard.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <img loading="lazy" src="{{ asset("$design/images/partners/visa.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <img loading="lazy" src="{{ asset("$design/images/partners/mcafee.svg") }}" width="84" height="auto" alt="">
                                    </div>
                                </div>
                            </div>

                        <div class="sidebar__offers offers">
                            <div class="offers__item">
                                <picture><source class="lazy" data-srcset="{{ asset("$design/images/promo/01.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img loading="lazy" class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/promo/01.png") }}" alt=""></picture>
                            </div>
                            <div class="offers__item">
                                <picture><source class="lazy" data-srcset="{{ asset("$design/images/promo/02.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img loading="lazy" class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/promo/02.png") }}" alt=""></picture>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="checkup bottom" onclick="location.href='{{ route('home.checkup') }}'">
                <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
                <div></div>
            </div>
        @yield('content')

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
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
                <div class="popup_bottom">
                    <div class="popup_text">{{__('text.common_callback')}}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                        @if (empty(session('form'))) @selected($item['iso'] == session('location.country', ''))

                                        @else
                                            @selected($item['iso'] == session('form.phone_code', ''))
                                        @endif
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
                                        @else
                                            @php
                                                $file = 'style_checkout/images/countrys/' . $item['nicename'] . '.svg';
                                            @endphp

                                            @if (file_exists(public_path($file)))
                                                data-asset="{{ asset($file) }}"
                                            @else
                                                data-asset=""
                                            @endif
                                        @endif
                                            value="+{{ $item['phonecode'] }}">
                                            +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="enter-info__input enter-info__input--country">
                            <input required autocomplete="off" type="number" id="phone" name="phone" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{__('text.common_callback')}}</div>
                </div>
                <div class="message_sended hidden">
					<h2>{{__('text.contact_us_thanks')}}</h2>
					<br>
					<p>{{__('text.phone_request_mes_text')}}</p>
				</div>
            </div>
        </div>
    </main>

    <div class="block_subscribe bottom">
		<div class="left_block">
			<div class="subscribe_img">
				<img loading="lazy" src="{{ asset("$design/images/icons/subscribe.svg") }}">
			</div>
			<div class="text_subscribe">
				<span class="top_text">{{__('text.common_subscribe')}}</span>
				<span class="bottom_text">{{__('text.common_spec_offer')}}</span>
			</div>
		</div>
		<div class="right_block">
			<input type="text" placeholder="Email" class="form__input input" id="email_sub">
			<div class="button_sub">
				<img loading="lazy" src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
				<span class="button_text">{{__('text.common_subscribe')}}</span>
			</div>
		</div>
	</div>

    <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#usps')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/usps.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ems')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ems.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dhl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dhl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ups')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/ups.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#fedex')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/fedex.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#tnt')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/tnt.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#postnl')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/postnl.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#deutsche_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/deutsche_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dpd')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/dpd.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#gls')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/gls.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                            <use width="100%" height="100%" width="100%" href="{{ asset('pub_images/shipping/sprite.svg#australia_post')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/australia_post.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#colissimo')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/colissimo.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                    @endif
                </li>
                <li class="ship-index__item">
                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                            <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#correos')  }}" preserveAspectRatio="xMinYMin">
                        </svg>
                    @else
                        <img width="100%" height="100%" src="{{ asset('pub_images/shipping/correos.svg') }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                    @endif
                </li>
            </ul>
        </div>
    </section>

    <section class="reviews">
        <div class="reviews__container">
            <div class="reviews__body">
                <div class="reviews__slider">
                    <div class="reviews__swiper">
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_1')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_1')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_2')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_2')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_3')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_3')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_4')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_4')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_5')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_5')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_6')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_6')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_7')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_7')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_8')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_8')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_9')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_9')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_10')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_10')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_11')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_11')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_12')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_12')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_13')!!}</div>
                                <div class="reviews__stars">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="98" height="18">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 78 14" width="98" height="18">
                                            <path fill="#EED54F" d="M6.308 1.658a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324L10.43 8.262a.75.75 0 0 0-.257.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L1.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L17.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L33.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L49.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Zm16 0a.75.75 0 0 1 1.384 0l1.09 2.609a.75.75 0 0 0 .59.454l2.916.395a.75.75 0 0 1 .373 1.324l-2.233 1.822a.75.75 0 0 0-.256.75l.713 3.08c.155.67-.599 1.178-1.162.783l-2.292-1.609a.75.75 0 0 0-.862 0l-2.292 1.609c-.563.395-1.317-.113-1.162-.783l.713-3.08a.75.75 0 0 0-.257-.75L65.34 6.44a.75.75 0 0 1 .373-1.324l2.915-.395a.75.75 0 0 0 .592-.454l1.089-2.609Z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_13')}}</div>
                        </div>
                    </div>
                </div>
                <div class="reviews__controls">
                    <button type="button" class="reviews__arrow reviews__arrow--prev">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="8.5" height="15">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 15" fill="currentColor" width="8.5" height="15">
                                <path d="m3.164 7.5 5.793-5.793A1 1 0 0 0 7.543.293l-6.5 6.5a1 1 0 0 0 0 1.414l6.5 6.5a1 1 0 0 0 1.414-1.414L3.164 7.5Z"/>
                            </svg>
                        @endif
                    </button>
                    <button type="button" class="reviews__arrow reviews__arrow--next">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="8.5" height="15">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 15" fill="currentColor" width="8.5" height="15">
                                <path d="m6.836 7.5-5.793 5.793a1 1 0 1 0 1.414 1.414l6.5-6.5a1 1 0 0 0 0-1.414l-6.5-6.5a1 1 0 0 0-1.414 1.414L6.836 7.5Z"/>
                            </svg>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer__top">
            <div class="footer__container">
                <div class="top-footer">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <ul class="top-footer__menu menu-top-footer">
                            <li class="menu-top-footer__item"><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{__('text.common_help_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.bonus_referral_program', '_' . $domainWithoutZone) }}">{{__('text.bonus_ref_menu')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                        <a href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}" class="top-footer__affiliate">
                            <div class="top-footer__icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="22" height="15">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-affiliate") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 16" width="22" height="15">
                                        <path fill="#454D58" d="M11 8.882c3.399 0 5.72 1.552 5.72 3.97 0 1.704-1.13 2.648-2.64 2.648H7.92c-1.51 0-2.64-.944-2.64-2.647 0-2.419 2.321-3.97 5.72-3.97Zm7.04 0c2.416 0 3.96 1.462 3.96 3.31 0 1.492-1.137 2.426-2.42 2.426h-.66a.881.881 0 0 1-.88-.883c0-.487.394-.882.88-.882h.66c.38 0 .66-.23.66-.662 0-.849-.734-1.544-2.2-1.544a.881.881 0 0 1-.88-.882c0-.488.394-.883.88-.883Zm-14.08 0c.486 0 .88.395.88.883a.881.881 0 0 1-.88.882c-1.466 0-2.2.695-2.2 1.544 0 .432.28.662.66.662h.66a.881.881 0 0 1 0 1.765h-.66c-1.283 0-2.42-.934-2.42-2.427 0-1.847 1.544-3.309 3.96-3.309ZM11 10.647c-2.541 0-3.96.948-3.96 2.206 0 .65.278.882.88.882h6.16c.602 0 .88-.232.88-.882 0-1.258-1.419-2.206-3.96-2.206Zm5.28-9.265a3.084 3.084 0 0 1 3.08 3.089 3.084 3.084 0 0 1-3.08 3.088.881.881 0 0 1-.88-.883c0-.487.394-.882.88-.882l.127-.006A1.322 1.322 0 0 0 17.6 4.471c0-.731-.591-1.324-1.32-1.324l-.103-.006a.882.882 0 0 1 .103-1.759Zm-10.56 0a.882.882 0 0 1 .103 1.759l-.103.006c-.729 0-1.32.593-1.32 1.324 0 .688.523 1.253 1.193 1.317l.127.006a.881.881 0 0 1 0 1.765A3.084 3.084 0 0 1 2.64 4.47a3.084 3.084 0 0 1 3.08-3.089ZM11 .5c1.944 0 3.52 1.58 3.52 3.53A3.525 3.525 0 0 1 11 7.559a3.525 3.525 0 0 1-3.52-3.53A3.525 3.525 0 0 1 11 .5Zm0 1.765c-.972 0-1.76.79-1.76 1.764 0 .975.788 1.765 1.76 1.765s1.76-.79 1.76-1.765c0-.974-.788-1.764-1.76-1.764Z"/>
                                    </svg>
                                @endif
                            </div>
                            <span>{{__('text.common_affiliate_main_menu_button')}}</span>
                        </a>
                    @else
                        <ul class="top-footer__menu menu-top-footer">
                            <li class="menu-top-footer__item"><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.about', '') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.help', '') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.testimonials', '') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.delivery', '') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.bonus_referral_program', '') }}">{{__('text.bonus_ref_menu')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.moneyback', '') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.contact_us', '') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                        <a href="{{ route('home.affiliate', '') }}" class="top-footer__affiliate">
                            <div class="top-footer__icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="22" height="15">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-affiliate") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 16" width="22" height="15">
                                        <path fill="#454D58" d="M11 8.882c3.399 0 5.72 1.552 5.72 3.97 0 1.704-1.13 2.648-2.64 2.648H7.92c-1.51 0-2.64-.944-2.64-2.647 0-2.419 2.321-3.97 5.72-3.97Zm7.04 0c2.416 0 3.96 1.462 3.96 3.31 0 1.492-1.137 2.426-2.42 2.426h-.66a.881.881 0 0 1-.88-.883c0-.487.394-.882.88-.882h.66c.38 0 .66-.23.66-.662 0-.849-.734-1.544-2.2-1.544a.881.881 0 0 1-.88-.882c0-.488.394-.883.88-.883Zm-14.08 0c.486 0 .88.395.88.883a.881.881 0 0 1-.88.882c-1.466 0-2.2.695-2.2 1.544 0 .432.28.662.66.662h.66a.881.881 0 0 1 0 1.765h-.66c-1.283 0-2.42-.934-2.42-2.427 0-1.847 1.544-3.309 3.96-3.309ZM11 10.647c-2.541 0-3.96.948-3.96 2.206 0 .65.278.882.88.882h6.16c.602 0 .88-.232.88-.882 0-1.258-1.419-2.206-3.96-2.206Zm5.28-9.265a3.084 3.084 0 0 1 3.08 3.089 3.084 3.084 0 0 1-3.08 3.088.881.881 0 0 1-.88-.883c0-.487.394-.882.88-.882l.127-.006A1.322 1.322 0 0 0 17.6 4.471c0-.731-.591-1.324-1.32-1.324l-.103-.006a.882.882 0 0 1 .103-1.759Zm-10.56 0a.882.882 0 0 1 .103 1.759l-.103.006c-.729 0-1.32.593-1.32 1.324 0 .688.523 1.253 1.193 1.317l.127.006a.881.881 0 0 1 0 1.765A3.084 3.084 0 0 1 2.64 4.47a3.084 3.084 0 0 1 3.08-3.089ZM11 .5c1.944 0 3.52 1.58 3.52 3.53A3.525 3.525 0 0 1 11 7.559a3.525 3.525 0 0 1-3.52-3.53A3.525 3.525 0 0 1 11 .5Zm0 1.765c-.972 0-1.76.79-1.76 1.764 0 .975.788 1.765 1.76 1.765s1.76-.79 1.76-1.765c0-.974-.788-1.764-1.76-1.764Z"/>
                                    </svg>
                                @endif
                            </div>
                            <span>{{__('text.common_affiliate_main_menu_button')}}</span>
                        </a>
                    @endif
                </div>
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="sitemap_menu">
                        <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                    </div>
                @endif
            </div>
        </div>
        <div class="footer__bottom">
            <div class="footer__container">
                <div class="footer__copyright">
                    <p>
                        {{__('text.license_text_license1_1')}} {{ $domain }} {{__('text.license_text_license1_2')}}
                        {{__('text.license_text_license2_d3')}}
                    </p>
                </div>
            </div>
        </div>
    </footer>
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
<script defer src="{{ asset_ver("js/all_js.js") }}"></script>

@if ($web_statistic)
    <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
@endif
{{-- <input hidden id="stattemp" style="display: none;" value="{$path.global_image}/elements/pixel?{$data.web_statistic.params_string}">
<img loading="lazy" id="stat" style="display: none;" alt="" src=""> --}}

</body>

</html>