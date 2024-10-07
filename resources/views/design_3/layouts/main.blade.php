<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#454d58"/>
	<meta name="format-detection" content="telephone=no">

    <link rel="alternate" href="{{ config('app.url') }}/lang=arb" hreflang="ar" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=cs" hreflang="cs" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=da" hreflang="da" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=de" hreflang="de" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=en" hreflang="en" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=es" hreflang="es" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fi" hreflang="fi" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fr" hreflang="fr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=gr" hreflang="gr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hans" hreflang="zh-Hans" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hant" hreflang="zh-Hant" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=hu" hreflang="hu" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=it" hreflang="it" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ja" hreflang="ja" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ms" hreflang="ms" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=nl" hreflang="nl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=no" hreflang="no" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pl" hreflang="pl" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=pt" hreflang="pt" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=ro" hreflang="ro" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sk" hreflang="sk" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=sv" hreflang="sv" />

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
</head>
<body>
    <script>
        const design = 3;
    </script>

<div class="wrapper">
	<header class="header">
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
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a>
                    <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a>
                </div>
            </div>
		</div>
		<div class="header__top top-header">
            <div class="top-header__container">
                <div class="top-header__menu menu">
                    <button type="button" class="menu__icon icon-menu"><span></span></button>
                    <nav class="menu__body">
                        <ul class="menu__list">
                            <li class="menu__item best"><a class="menu__link" data-bestsellers>{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.about') }}" class="menu__link">{{__('text.common_about_us_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.help') }}" class="menu__link">{{__('text.common_help_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.testimonials') }}" class="menu__link">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.delivery') }}" class="menu__link">{{__('text.common_shipping_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.moneyback') }}" class="menu__link">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="menu__item"><a href="{{ route('home.contact_us') }}" class="menu__link">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                    </nav>
                </div>
                <div data-tabs="1150" class="top-header__bestsellers bestsellers">
                    <nav data-tabs-titles class="bestsellers__navigation">
                        <button type="button" class="bestsellers__title">{{__('text.common_best_selling_title')}}</button>
                        @foreach ($menu as $category)
                            <button type="button" class="bestsellers__title">{{ $category['name'] }}</button>
                        @endforeach
                    </nav>
                    <div data-tabs-body class="bestsellers__content">
                        <div class="bestsellers__body">
                            @foreach ($bestsellers as $bestseller)
                                <a href="{{ route('home.product', $bestseller['url']) }}" class="bestsellers__item">
                                    {{ $bestseller['name'] }}
                                </a>
                            @endforeach
                        </div>
                            @foreach ($menu as $category)
                                <div class="bestsellers__body">
                                    @foreach ($category['products'] as $item)
                                            <a href="{{ route('home.product', $item['url']) }}" class="bestsellers__item">
                                                {{ $item['name'] }}
                                            </a>
                                    @endforeach
                                </div>
                            @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="header__main">
            <div class="container header__container">
                <div class="header__inner">
                    <a href="{{ route('home.index') }}" class="header__logo"><img src="{{ asset("$design/images/logo.svg") }}" alt=""></a>

                    <div class="header__actions" data-one-select data-da=".top-header__container, 700, last">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="header__select">
                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $language)
                                        <option value="/lang={{$language['code']}}" @if (App::currentLocale() == $language['code']) selected @endif>{{$language['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (count($Currency::GetAllCurrency()) > 1)
                            <div class="header__select">
                                <select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
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
                        <img src="/images/pay_icons/visa.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/mastercard.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/maestro.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/discover.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/amex.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/jsb.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/unionpay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/dinners-club.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/apple-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/google-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/amazon-pay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/stripe.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/paypal.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/sepa.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/cashapp.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/adyen.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/skrill.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/worldpay.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/payline.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/bitcoin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/binance-coin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/ethereum.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/litecoin.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/tron.svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/usdt(erc20).svg" alt="">
                    </li>
                    <li class="pay-index__item">
                        <img src="/images/pay_icons/usdt(trc20).svg" alt="">
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
                            @foreach (range('A', 'Z') as $l)
                                <a href="{{ route('home.first_letter', $l) }}" type="button" class="search__button">{{ $l }}</a>
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
                                    <img class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/doctor.png") }}" alt="">
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
                                        <img src="{{ asset("$design/images/partners/fda.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <picture><source class="lazy" data-srcset="{{ asset("$design/images/partners/pgeu.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/partners/pgeu.png") }}" width="60" height="auto" alt=""></picture>
                                    </div>
                                    <div class="verified__logo">
                                        <picture><source class="lazy" data-srcset="{{ asset("$design/images/partners/cipa.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/partners/cipa.png") }}" width="67" height="auto" alt=""></picture>
                                    </div>
                                    <div class="verified__logo">
                                        <img src="{{ asset("$design/images/partners/mastercard.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <img src="{{ asset("$design/images/partners/visa.svg") }}" width="60" height="auto" alt="">
                                    </div>
                                    <div class="verified__logo">
                                        <img src="{{ asset("$design/images/partners/mcafee.svg") }}" width="84" height="auto" alt="">
                                    </div>
                                </div>
                            </div>

                        <div class="sidebar__offers offers">
                            <div class="offers__item">
                                <picture><source class="lazy" data-srcset="{{ asset("$design/images/promo/01.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/promo/01.png") }}" alt=""></picture>
                            </div>
                            <div class="offers__item">
                                <picture><source class="lazy" data-srcset="{{ asset("$design/images/promo/02.webp") }}" srcset="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" type="image/webp"><img class="lazy" decoding="async" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=" data-src="{{ asset("$design/images/promo/02.png") }}" alt=""></picture>
                            </div>
                        </div>
                    </div>
                </aside>
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
                                        data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
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
				<img src="{{ asset("$design/images/icons/subscribe.svg") }}">
			</div>
			<div class="text_subscribe">
				<span class="top_text">{{__('text.common_subscribe')}}</span>
				<span class="bottom_text">{{__('text.common_spec_offer')}}</span>
			</div>
		</div>
		<div class="right_block">
			<input type="text" placeholder="Email" class="form__input input" id="email_sub">
			<div class="button_sub">
				<img src="{{ asset("$design/images/icons/subscribe_mini.svg") }}" class="sub_mini">
				<span class="button_text">{{__('text.common_subscribe')}}</span>
			</div>
		</div>
	</div>

    <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <img src="/images/shipping/usps.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/ems.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/dhl.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/ups.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/fedex.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/tnt.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/postnl.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/deutsche_post.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/dpd.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/gls.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/australia_post.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/colissimo.svg" alt="">
                </li>
                <li class="ship-index__item">
                    <img src="/images/shipping/correos.svg" alt="">
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
                    <ul class="top-footer__menu menu-top-footer">
                        <li class="menu-top-footer__item"><a href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                        <li class="menu-top-footer__item"><a href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                    </ul>
                    <a href="{{ route('home.affiliate') }}" class="top-footer__affiliate">
                        <div class="top-footer__icon">
                            <svg width="22" height="15">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-affiliate") }}"></use>
                            </svg>
                        </div>
                        <span>{{__('text.common_affiliate_main_menu_button')}}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer__bottom">
            <div class="footer__container">
                <div class="footer__copyright">
                    <p>
                        {{__('text.license_text_license1_1')}} {{str_replace(['http://', 'https://'], '', env('APP_URL'))}} {{__('text.license_text_license1_2')}}
                        {{__('text.license_text_license2_d3')}}
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>
<script src="{{ asset("$design/js/app.js") }}"></script>
<script src="{{ asset("/js/all_js.js") }}"></script>
<input hidden id="stattemp" value="{$data.web_statistic.params_string}">
{{-- <input hidden id="stattemp" style="display: none;" value="{$path.global_image}/elements/pixel?{$data.web_statistic.params_string}">
<img id="stat" style="display: none;" alt="" src=""> --}}

</body>

</html>