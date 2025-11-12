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
                                <li class="menu__item"><a href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}" class="menu__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                            @else
                                <li class="menu__item best"><a class="menu__link" data-bestsellers>{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.about', '') }}" class="menu__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.help', '') }}" class="menu__link">{{__('text.common_help_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.testimonials', '') }}" class="menu__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                                <li class="menu__item"><a href="{{ route('home.delivery', '') }}" class="menu__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
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
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                    </svg>
                                </div>
                                <span>{{__('text.common_cart_text_d2')}}</span>
                            </div>
                            <div class="cart-header__info">
                                <div class="cart-header__quantity">{{$cart_count}} {{__('text.common_items_d4')}}</div>
                                <div class="cart-header__total">{{ $Currency::convert($cart_total) }}</div>
                            </div>
                        @else
                            <span class="cart__icon">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                                </svg>
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

        <div class="header__search">
            <div class="container header__container">
                <form class="search" action="{{ route('search.search_product') }}" method = "POST">
                    @csrf
                    <div class="search__input  search-bar">
                        <div class="search__icon">
                            <svg width="16" height="16">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-search") }}"></use>
                            </svg>
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
                            <svg width="16" height="16">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-down") }}"></use>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>
    <div class="popup_white hide">
        <div class="popup_push">
            <div class="button_close">
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
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
                                            <svg width="78" height="14">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                            </svg>
                                        </div>
                                        <p class="preference__text">{{__('text.common_customers')}}</p>
                                    </div>
                                </div>
                                <div class="preference__items">
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            <svg width="24" height="24">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-01") }}"></use>
                                            </svg>
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_save')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_discount')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            <svg width="24" height="24">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-03") }}"></use>
                                            </svg>
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_prescription')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_restrictions')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            <svg width="24" height="22">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-02") }}"></use>
                                            </svg>
                                        </div>
                                        <div class="item-preference__info">
                                            <h4 class="item-preference__label">{{__('text.common_delivery')}}</h4>
                                            <p class="item-preference__descr">{{__('text.common_receive')}}</p>
                                        </div>
                                    </div>
                                    <div class="preference__item item-preference">
                                        <div class="item-preference__icon">
                                            <svg width="24" height="21">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-pref-01") }}"></use>
                                            </svg>
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
                                        <svg width="24" height="24">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-verified") }}"></use>
                                        </svg>
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
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                    </svg>
                </div>
                <div class="popup_bottom">
                    <div class="popup_text">{{__('text.common_callback')}}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                    @if (empty(session('form')))
                                            @selected($item['iso'] == session('location.country', ''))
                                    @else
                                        @selected($item['iso'] == session('form.phone_code', ''))
                                    @endif
                                        data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
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

    <section class="reviews">
        <div class="reviews__container">
            <div class="reviews__body">
                <div class="reviews__slider">
                    <div class="reviews__swiper">
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_1')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_1')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_2')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_2')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_3')!!}</div>
                                <div class="reviews__stars">

                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_3')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_4')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_4')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_5')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_5')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_6')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_6')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_7')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_7')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_8')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_8')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_9')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_9')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_10')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_10')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_11')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_11')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_12')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_12')}}</div>
                        </div>
                        <div class="reviews__slide">
                            <div class="reviews__top">
                                <div class="reviews__name">{!!__('text.testimonials_author_t_13')!!}</div>
                                <div class="reviews__stars">
                                    <svg width="98" height="18">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-stars") }}"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="reviews__text">{{__('text.testimonials_t_13')}}</div>
                        </div>
                    </div>
                </div>
                <div class="reviews__controls">
                    <button type="button" class="reviews__arrow reviews__arrow--prev">
                        <svg width="8.5" height="15">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-prev") }}"></use>
                        </svg>
                    </button>
                    <button type="button" class="reviews__arrow reviews__arrow--next">
                        <svg width="8.5" height="15">
                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                        </svg>
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
                            <li class="menu-top-footer__item"><a href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                        <a href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}" class="top-footer__affiliate">
                            <div class="top-footer__icon">
                                <svg width="22" height="15">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-affiliate") }}"></use>
                                </svg>
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
                            <li class="menu-top-footer__item"><a href="{{ route('home.moneyback', '') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu-top-footer__item"><a href="{{ route('home.contact_us', '') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                        <a href="{{ route('home.affiliate', '') }}" class="top-footer__affiliate">
                            <div class="top-footer__icon">
                                <svg width="22" height="15">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-affiliate") }}"></use>
                                </svg>
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