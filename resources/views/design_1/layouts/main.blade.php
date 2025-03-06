<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#84a657"/>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("/js/sw-setup.js") }}"></script>
    @endif

    <script type="text/javascript" src="{{ asset("/js/delete_cache.js") }}"></script>

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script defer src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script defer type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>
<body>
    <script>
        let flagc = false;
        let flagp = false;
        const design = 1;
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

<div class="wrapper">
	<header class="header">

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("/pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("/pub_images/christmas_big.png") }}">
        </div> --}}

        <div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none">
                        <a class="request_call">{{ __('text.common_callback') }}</a>
                        <div class="request_text">{{__('text.common_call_us_top')}}</div>
                    </div>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_1')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_1')}}</a>
                </div>
            </div>
		</div>

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
                            <input required autocomplete="off" type="number" id="phone" name="phone" value="" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
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

        <div class="container header__container">
            <div class="header__wrapper">
                <a class="header__logo logo" href="{{ route('home.index') }}">
                    <picture>
                        <source srcset="{{ asset("$design/images/logo.webp") }}" type="image/webp">
                        <img loading="lazy" class="logo__img" src="{{ asset("$design/images/logo.png") }}" alt="Logo" width="216" height="53">
                    </picture>
                </a>
                @if (count($Language::GetAllLanuages()) > 1)
                    <div class="header__currency header__control" data-da=".controls, 768, first">
                        <span class="header__label">{{__('text.common_language_text')}}</span>
                        <select name="select__value" class="form" id="lang_select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Language::GetAllLanuages() as $item)
                                <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if (count($Currency::GetAllCurrency()) > 1)
                    <div class="header__currency header__control" data-da=".controls, 768, first">
                        <span class="header__label">{{__('text.common_currency_text')}}</span>
                        <select name="select__options" class="form" id="curr_select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="{{ route('home.currency', $item['code']) }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="header__currency header__control profile" data-da=".controls, 768, last">
                    <a href='{{ route('home.login') }}' target="_blank">
                        <picture>
                            <source srcset="{{ asset("$design/images/user.png") }}" type="image/png">
                            <img loading="lazy" src="{{ asset("$design/images/user.png") }}" alt="profile" width="25" height="25" loading="lazy">
                        </picture>
                        <span class="header__label">{{__('text.common_profile')}}</span>

                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="page">
        <section class="advantages" style="background-image: url('{{ asset("$design/images/advantages/bg.webp") }}');">
            <div class="advantages__container">
                <div class="advantages__wrapper">
                    <div class="advantages__content">
                        <h2 class="advantages__title">
                            <span class="advantages__accent">1 000 000</span>
                            {{__('text.common_customers')}}
                        </h2>
                        <ul class="advantages__items">
                            <li class="advantages__item advantages__item--save">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_save')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_discount')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--fast">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_delivery')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_receive')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--prescription">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_prescription')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_restrictions')}}
                                </p>
                            </li>
                            <li class="advantages__item advantages__item--money">
                                <h3 class="advantages__subtitle">
                                    {{__('text.common_moneyback')}}
                                </h3>
                                <p class="advantages__text">
                                    {{__('text.common_refund')}}
                                </p>
                            </li>
                        </ul>
                    </div>

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

                    <div class="advantages__image">
                        <picture><source srcset="{{ asset("$design/images/advantages/doctor3.webp") }}" type="image/webp"><img loading="lazy" class="advantages__img" src="{{ asset("$design/images/advantages/doctor3.png") }}" alt="Doctor" width="400" height="320" loading="lazy"></picture>
                        <div class="cart">
                            @if ($cart_count != 0)
                                <span class="cart__price">{{ $Currency::Convert($cart_total, true) }} </span>
                                <span class="cart__text"> {{ $cart_count }} {{__('text.common_num_items_text')}}</span>
                                <a class="cart__link" href="{{ route('cart.index') }}">{{__('text.common_cart_text')}}</a>
                            @else
                                {{__('text.common_empty_cart')}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="logos">
            <div class="logos__container container">
                <div class="logos__wrapper">
                    <h2 class="logos__title">
                        <p class="logos__accent">
                            {{__('text.common_verified')}}
                        </p>
                        <span>{{__('text.common_approved')}}</span>
                    </h2>
                    <ul class="logos__items">
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/1.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/1.png") }}" alt="fda" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/2.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/2.png") }}" alt="fda" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/3.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/3.png") }}" alt="pgeu gpue" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/4.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/4.png") }}" alt="mipa" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/5.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/5.png") }}" alt="cipar" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/6.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/6.png") }}" alt="mastercard" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/7.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/7.png") }}" alt="visa" width="75" height="45" loading="lazy"></picture>
                        </li>
                        <li class="logos__item">
                            <picture><source srcset="{{ asset("$design/images/logos/8.webp") }}" type="image/webp"><img loading="lazy" class="logos__img" src="{{ asset("$design/images/logos/8.png") }}" alt="mcafee" width="75" height="45" loading="lazy"></picture>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="search">
            <h2 class="sr-only">Search</h2>
            <div class="container search__container search-bar">
                <form class="search__form search-form" action="{{ route('search.search_product') }}" method = "POST">
                    @csrf
                    <label class="sr-only" for="search-form">{{__('text.common_search')}}</label>
                    <input class="search-form__field" id="autocomplete" type="text" name="search_text" placeholder="{{__('text.common_search')}}" required autocomplete="off">
                    <button class="search-form__button" type="submit">
                        <span class="sr-only">search</span>
                    </button>
                    <ul class="search_result" style="display: none;"></ul>
                </form>
                <ul class="search__items">
                    @foreach ($first_letters as $key => $active_letter)
                        <li class="search__item">
                            @if ($active_letter)
                                <a class="search__link" href="{{ route('home.first_letter', $key) }}">{{ $key }}</a>
                            @else
                                {{ $key }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="pay-index">
            <div class="pay-index__container">
                <ul class="pay-index__list">
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#visa">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#mastercard">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#maestro">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#discover">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amex">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#jsb">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#unionpay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#dinners-club">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#apple-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#google-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amazon-pay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#stripe">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#paypal">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#sepa">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#cashapp">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#adyen">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#skrill">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#worldpay">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#payline">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#bitcoin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#binance-coin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#ethereum">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#litecoin">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#tron">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(erc20)">
                        </svg>
                    </li>
                    <li class="pay-index__item">
                        <svg>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(trc20)">
                        </svg>
                    </li>
                </ul>
            </div>
        </section>

        <div class="content__container">
            <div class="box">
                <div class="menu" data-da=".header__wrapper, 991.98, last">
                    <button type="button" class="menu__icon icon-menu"><span></span></button>
                    <div class="menu__body">
                        <div class="controls">

                        </div>
                        <div class="menu__lists">
                            <ul>
                                <li>
                                    <a href="{{ route('home.index') }}" class = "menu__label">{{__('text.common_best_selling_title')}}</a>
                                </li>
                                <ul class="menu__list">
                                    @foreach ($bestsellers as $bestseller)
                                        <li>
                                            <a href="{{ route('home.product', $bestseller['url']) }}" style="display: flex; justify-content:space-between; align-items:baseline;">{{ $bestseller['name'] }} <span style="font-size: 13px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </ul>
                            <ul>
                                @foreach ($menu as $category)
                                    <li>
                                        <a href="{{ route('home.category', $category['url']) }}" class = "menu__label">{{ $category['name'] }}</a>
                                    </li>
                                    <ul class="menu__list" @if ($cur_category != $category['name']) style="display: none"  @endif>
                                        @foreach ($category['products'] as $item)
                                            <li>
                                                <a href="{{ route('home.product', $item['url']) }}" style="display: flex; justify-content:space-between; align-items:baseline;">{{ $item['name'] }} <span style="font-size: 13px;">{{ $Currency::convert($item['price'], false, true) }}</span></a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <section class="banners" id = "scroll">
                        <h2 class="sr-only">Banners</h2>
                        <ul class="banners__items">
                            <li class="banners__item">
                                <a class="banners__link">
                                    <picture><source srcset="{{ asset("$design/images/banners/3.webp") }}" type="image/webp"><img loading="lazy" class="banners__img" src="{{ asset("$design/images/banners/3.png") }}" alt="Big Discounts Only Today" width="390" height="135" loading="lazy"></picture>
                                </a>
                            </li>
                            <li class="banners__item">
                                <a class="banners__link">
                                    <picture><source srcset="{{ asset("$design/images/banners/4.webp") }}" type="image/webp"><img loading="lazy" class="banners__img" src="{{ asset("$design/images/banners/4.png") }}" alt="Special offer" width="390" height="135" loading="lazy"></picture>
                                </a>
                            </li>
                        </ul>
                    </section>

                    <div class="btn-up btn-up_hide"></div>

                    @yield('content')

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
            <input type="email" placeholder="Email" class="form__input input" id="email_sub">
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
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#usps" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ems" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dhl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ups" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#fedex" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#tnt" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#postnl" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#deutsche_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dpd" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#gls" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" width="100%" href="/pub_images/shipping/sprite.svg#australia_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#colissimo" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#correos" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
            </ul>
        </div>
    </section>

    <section class="testimonials">
        <h2 class="sr-only">testimonials</h2>
        <div class="container testimonials__container">
            <div class="testimonials__wrapper">
                <ul class="testimonials__rating">
                    <span class="sr-only">5 stars</span>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                    <li class="testimonials__star" style="background-image: url('{{ asset("$design/images/icons/star.png") }}')"></li>
                </ul>
                <div class="testimonials__text">
                    <p>
                        <span class="testimonials__accent">
                            {!! __('text.testimonials_author_t_1') !!}
                        </span>
                            {!! __('text.testimonials_t_1') !!}
                      </p>
                </div>

                <a class="testimonials__link" href="{{ route('home.testimonials') }}">
                    {{__('text.common_next')}}
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer__container">
            <div class="footer__top">
                <p class="footer__text">
                    {{__('text.license_text_license1_1')}} {{ $domain }} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d1')}}
                </p>
                <a class="footer__link c-button" href="{{ route('home.affiliate') }}">{{__('text.common_affiliate_main_menu_button')}}</a>
            </div>
            <nav class="footer__navigation">
                <ul class="navigation">
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a>
                    </li>
                    <li class="navigation__item">
                        <a class="navigation__link" href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a>
                    </li>
                </ul>
            </nav>
        </div>
    </footer>
    <div id="thanks" aria-hidden="true" class="popup thanks">
		<div class="popup__wrapper">
			<div class="popup__content">
				<button data-close type="button" class="popup__close">
					<svg width="20" height="20">
						<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
					</svg>
				</button>
				<div class="popup__text">
					<h2>{{__('text.contact_us_thanks')}}</h2> <br>
					<p>{{__('text.contact_us_sended')}}</p>
				</div>
			</div>
		</div>
	</div>

<script defer src="{{ asset("$design/js/app.js") }}"></script>
<script defer src="{{ asset("/js/all_js.js") }}"></script>
@if ($web_statistic)
    <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
@endif

</body>
</html>