<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Defult')</title>
    <meta name="description" content="Verified Pharmacy Store">
    <meta name="keywords" content="key, words">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#ff746c"/>
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
    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">
    <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">

    <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">

    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
</head>
<body class="no-js webp">
    <script>
        const design = 10;
    </script>

    <header class="header">
        <div class="container">
            <div class="header__grid-wrapper"><!-- Phones-->
                <address class="header__phones">
                    <div class="header__phones-caption"><button class="link-button" data-dialog="call">{{ __('text.common_callback') }}</button> <span class="caption-text">{{__('text.common_call_us_top')}}</span></div>
                    <ul class="header__phones-wrapper">
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}
                            {{__('text.phones_title_phone_1')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}
                            {{__('text.phones_title_phone_2')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}
                            {{__('text.phones_title_phone_3')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}
                            {{__('text.phones_title_phone_4')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}
                            {{__('text.phones_title_phone_5')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}
                            {{__('text.phones_title_phone_6')}}</a></li>
                        <li class="dropdown-item"><a class="header-phone" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}
                            {{__('text.phones_title_phone_7')}}</a></li>
                    </ul>
                    <div class="dropdown"><button class="dropdown__button" aria-label="Show dropdown"><span
                                class="icon"><svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#dots-vertical") }}"></use>
                                </svg></span></button>
                        <ul class="dropdown-list"></ul>
                    </div>
                </address><!-- Settings & auth-->
                <div class="header__controls">
                    <div class="header__lang header-select-wrapper">
                        <select class="header-select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Language::GetAllLanuages() as $item)
                                <option value="/lang={{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                            @endforeach
                        </select><span class="icon header-select-wrapper__icon"><svg width="1em" height="1em"
                                fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#world") }}"></use>
                            </svg></span><span class="icon header-select-wrapper__chevron"><svg width="1em"
                                height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#chevron-down") }}"></use>
                            </svg></span>
                    </div>
                    <div class="header__currency header-select-wrapper">
                        <select class="header-select" onchange="location.href=this.options[this.selectedIndex].value">
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                            @endforeach
                        </select>
                        <span class="icon header-select-wrapper__icon"><svg width="1em" height="1em"
                                fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#money") }}"></use>
                            </svg></span><span class="icon header-select-wrapper__chevron"><svg width="1em"
                                height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#chevron-down") }}"></use>
                            </svg></span></div><a class="header__auth" href="#!"><span class="icon"><svg
                                width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#profile") }}"></use>
                            </svg></span>{{__('text.common_profile')}}</a>
                </div>
                <!-- Logo-->
                <a class="logo" href="{{ route('home.index') }}">
                    <div class="logo__image"><img src="{{ asset("$design/svg/logo.svg") }}" width="40"
                            height="40" alt="Site logo"></div>
                    <div class="logo__title">TrueMeds</div>
                    <div class="logo__text">Discount Store. Since 1998</div>
                </a>
                <div class="header__search-wrapper">
                    <!-- Categories button-->
                    <button class="button categories-button">
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg") }}#hotdog"></use>
                            </svg>
                        </span>
                        <span class="button__text">{{__('text.common_categories_menu')}}</span>
                    </button>
                    <!-- Search-->
                    <form class="search-form" action="{{ route('search.search_product') }}" method="post">
                        @csrf
                        <div class="search search-bar" style="width: 100%">
                            <label class="form__label form__label--text">
                                <input class="form__text-input input-text" id="autocomplete" type="text" placeholder="{{__('text.common_search')}}" name="search_text">
                            </label>
                            <button class="search-button" aria-label="Search" type="submit">
                                <span class="icon">
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg") }}#search"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                    <!-- Cart-->
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
                    <a class="cart-button button button--secondary" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}">
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg") }}#cart"></use>
                            </svg>
                        </span>
                        <span class="button__text">{{__('text.common_cart_text_d2')}}</span>
                        <span class="button__total">{{ $Currency::Convert($cart_total, true) }}</span>
                    </a>
                </div>
            </div>
            <!-- Header info-->
            <div class="header__info">
                <div class="customer-choise">
                    <div class="customer-choise__logo"><img src="{{ asset("$design/svg/ui/choise.svg") }}"
                            width="42" height="42" alt="Site logo">
                    </div>
                    <div class="customer-choise__counter">1 000 000</div>
                    <div class="customer-choise__text">{{__('text.common_customers')}}</div>
                </div>
                <div class="header-brands">
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-1-md-45w.webp") }} 1x,{{ asset("$design/images/brands/brand-1-md-91w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-1-md-45w.jpg") }} 1x,{{ asset("$design/images/brands/brand-1-md-91w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-1-31w.webp") }} 1x,{{ asset("$design/images/brands/brand-1-63w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-1-31w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-1-31w.jpg") }} 1x,{{ asset("$design/images/brands/brand-1-63w.jpg") }} 2x"
                                width="32" height="21" alt="Brand">
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-2-md-40w.webp") }} 1x,{{ asset("$design/images/brands/brand-2-md-80w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-2-md-40w.jpg") }} 1x,{{ asset("$design/images/brands/brand-2-md-80w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-2-32w.webp") }} 1x,{{ asset("$design/images/brands/brand-2-65w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-2-32w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-2-32w.jpg") }} 1x,{{ asset("$design/images/brands/brand-2-65w.jpg") }} 2x"
                                width="33" height="24" alt="Brand">
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-3-md-45w.webp") }} 1x,{{ asset("$design/images/brands/brand-3-md-91w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-3-md-45w.jpg") }} 1x,{{ asset("$design/images/brands/brand-3-md-91w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-3-37w.webp") }} 1x,{{ asset("$design/images/brands/brand-3-75w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-3-37w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-3-37w.jpg") }} 1x,{{ asset("$design/images/brands/brand-3-75w.jpg") }} 2x"
                                width="38" height="24" alt="Brand">
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-4-md-29w.webp") }} 1x,{{ asset("$design/images/brands/brand-4-md-59w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-4-md-29w.jpg") }} 1x,{{ asset("$design/images/brands/brand-4-md-59w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-4-24w.webp") }} 1x,{{ asset("$design/images/brands/brand-4-49w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-4-24w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-4-24w.jpg") }} 1x,{{ asset("$design/images/brands/brand-4-49w.jpg") }} 2x"
                                width="25" height="24" alt="Brand">
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-5-md-53w.webp") }} 1x,{{ asset("$design/images/brands/brand-5-md-106w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)"
                                srcset="{{ asset("$design/images/brands/brand-5-md-53w.jpg") }} 1x,{{ asset("$design/images/brands/brand-5-md-106w.jpg") }} 2x">
                            <source type="image/webp"
                                srcset="{{ asset("$design/images/brands/brand-5-42w.webp") }} 1x,{{ asset("$design/images/brands/brand-5-85w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-5-42w.jpg") }}"
                                srcset="{{ asset("$design/images/brands/brand-5-42w.jpg") }} 1x,{{ asset("$design/images/brands/brand-5-85w.jpg") }} 2x"
                                width="43" height="24" alt="Brand">
                        </picture>
                    </div>
                    <div class="header-brand">
                        <picture>
                            <source type="image/webp" media="(min-width: 43.75em) and (min-width: 700px)" srcset="{{ asset("$design/images/brands/brand-6-md-72w.webp") }} 1x,{{ asset("$design/images/brands/brand-6-md-144w.webp") }} 2x">
                            <source type="image/jpeg" media="(min-width: 43.75em) and (min-width: 700px)" srcset="{{ asset("$design/images/brands/brand-6-md-72w.jpg") }} 1x,{{ asset("$design/images/brands/brand-6-md-144w.jpg") }} 2x">
                            <source type="image/webp" srcset="{{ asset("$design/images/brands/brand-6-55w.webp") }} 1x,{{ asset("$design/images/brands/brand-6-111w.webp") }} 2x">
                            <img src="{{ asset("$design/images/brands/brand-6-55w.jpg") }}" srcset="{{ asset("$design/images/brands/brand-6-55w.jpg") }} 1x,{{ asset("$design/images/brands/brand-6-111w.jpg") }} 2x" width="56" height="22" alt="Brand">
                        </picture>
                    </div>
                </div>
            </div>
            <nav class="nav header-nav">
                <div class="nav-container">
                    <ul class="nav__list">
                        <li class="nav__item"><a class="nav__link is-active"
                                href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                        <li class="nav__item"><a class="nav__link" href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a>
                        </li>
                        <li class="nav__item"><a class="nav__link" href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                        <li class="nav__item"><a class="nav__link"
                                href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                        <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a>
                        </li>
                        <li class="nav__item"><a class="nav__link"
                                href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                        <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                    </ul>
                </div><button class="greedy-button" aria-label="Show dropdown"><span class="icon"><svg
                            width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg#dots-vertical") }}"></use>
                        </svg></span></button>
                <ul class="hidden-links dropdown-list"></ul>
            </nav>
            <nav class="nav cat-nav">
                <div class="nav-container">
                    <div class="nav__heading">{{__('text.common_categories_menu')}}</div><button class="nav__close-button"
                        aria-label="Close categories"></button>
                    <ul class="nav__list">
                        <li class="nav__item">
                            <a class="nav__link is-active nav__sublist-toggler" href="{{ route('home.index') }}"
                                data-sublist-index="0">{{__('text.common_best_selling_title')}}</a>
                        </li>
                        @foreach ($menu as $category)
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler"
                                    href="{{ route('home.category', $category['url']) }}"
                                    data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="categories-sublists">
                        <ul class="nav__sublist sublist-4-col" data-sublist-index="0">
                            <li class="nav__item nav__item--return">
                                <button class="nav__mobile-return">{{__('text.common_best_selling_title')}}</button>
                            </li>
                            @foreach ($bestsellers as $bestseller)
                                <li class="nav__item"><a class="nav__link"
                                        href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                        @foreach ($menu as $category)
                            <ul class="nav__sublist sublist-4-col" data-sublist-index="{{ $loop->iteration }}">
                                <li class="nav__item nav__item--return">
                                    <button class="nav__mobile-return">{{ $category['name'] }}</button>
                                </li>
                                @foreach ($category['products'] as $item)
                                    <li class="nav__item"><a class="nav__link"
                                            href="{{ route('home.product', $item['url']) }}">{{ $item['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <div class="container sub-header"><!-- Drug index-->
        <div class="drug-index">
            <div class="drug-index__container">
                <ul class="drug-index__list">
                    @foreach (range('A', 'Z') as $l)
                        <li class="drug-index__item"><a class="drug-index__link"
                                href="{{ route('home.first_letter', $l) }}">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div><!-- Store info-->
        <div class="store-info">
            <div class="store-info-caption">
                <div class="store-info-caption__title">{{__('text.common_verified')}}</div>
                <div class="store-info-caption__text">{{__('text.common_approved_d4')}}</div>
            </div>
            <div class="store-info-blocks">
                <div class="store-info-block store-info-block--1">
                    <div class="store-info-block__title">{{__('text.common_save')}}</div>
                    <div class="store-info-block__text">{{__('text.common_discount')}}</div>
                </div>
                <div class="store-info-block store-info-block--2">
                    <div class="store-info-block__title">{{__('text.common_prescription')}}</div>
                    <div class="store-info-block__text">{{__('text.common_restrictions')}}</div>
                </div>
                <div class="store-info-block store-info-block--3">
                    <div class="store-info-block__title">{{__('text.common_delivery')}}</div>
                    <div class="store-info-block__text">{{__('text.common_receive')}}</div>
                </div>
                <div class="store-info-block store-info-block--4">
                    <div class="store-info-block__title">{{__('text.common_moneyback')}}</div>
                    <div class="store-info-block__text">{{__('text.common_refund')}}</div>
                </div>
            </div>
        </div>

        <section class="pay-index">
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
    </div>

    @yield('content')

    <div class="container sup-footer">
        <div class="subscribe">
            <div class="subscribe__caption">
                <div class="subscribe__title">{{__('text.common_subscribe')}}</div>
                <div class="subscribe__text">{{__('text.common_spec_offer')}}</div>
            </div>
            <form class="form form form--secondary subscribe-form">
                <div class="form__field">
                    <label class="form__label form__label--email">
                        <input class="form__text-input input-email" type="email" placeholder="{{__('text.affiliate_email')}}" id="email_sub">
                    </label>
                </div>
                <div class="form__field">
                    <div class="button form__submit button_sub">{{__('text.common_subscribe')}}</div>
                </div>
            </form>
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

        @yield('rewies')
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer__wrapper"><!-- Footer logo--><a class="logo logo--footer" href="index.html">
                    <div class="logo__image"><img src="{{ asset("$design/svg/logo-footer.svg") }}" width="40"
                            height="40" alt="Site logo"></div>
                    <div class="logo__title">TrueMeds</div>
                    <div class="logo__text">Discount Store. Since 1998</div>
                </a>
                <nav class="nav footer-nav">
                    <div class="nav-container">
                        <ul class="nav__list">
                            <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{__('text.common_best_sellers_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.about') }}">{{__('text.common_about_us_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.help') }}">{{__('text.common_help_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials') }}">{{__('text.common_testimonials_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery') }}">{{__('text.common_shipping_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.moneyback') }}">{{__('text.common_moneyback_main_menu_item')}}</a></li>
                            <li class="nav__item"><a class="nav__link" href="{{ route('home.contact_us') }}">{{__('text.common_contact_us_main_menu_item')}}</a></li>
                        </ul>
                    </div>
                </nav><a class="button" href="{{ route('home.affiliate') }}">{{__('text.common_affiliate_main_menu_button')}}</a>
            </div>
            <!-- Copyrights-->
            <div class="footer__copyrights">
                <p>
                    {{__('text.license_text_license1_1')}} {{Request::getHost()}} {{__('text.license_text_license1_2')}}
                    {{__('text.license_text_license2_d10')}}
                </p>
            </div>
            <!-- Footer controls-->
            <div class="footer-buttons">
                <div class="footer-buttons__container"><a class="footer-button" href="{{ route('home.index') }}"><span
                            class="icon"><svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#home") }}"></use>
                            </svg></span> <span class="button__text">{{__('text.common_home_main_menu_item')}}</span></a><button
                        class="footer-button footer-button--cat"><span class="icon"><svg width="1em"
                                height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#hotdog") }}"></use>
                            </svg></span> <span class="button__text">{{__('text.common_categories_menu')}}</span></button><a
                        class="footer-button" href="profile.html"><span class="icon"><svg width="1em"
                                height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#profile") }}"></use>
                            </svg></span> <span class="button__text">{{__('text.common_profile')}}</span></a><a
                        class="footer-button footer-button--cart" href="{{ route('cart.index') }}" data-counter="{{ $cart_count }}"><span
                            class="icon"><svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                            </svg></span> <span class="button__text">{{ $Currency::Convert($cart_total, true) }}</span></a></div>
            </div>
        </div>
    </footer>
    <div class="cat-overlay overlay"></div>

    <dialog class="dialog" data-name="call" data-modal="true" data-clickable-backdrop="true">
        <div class="dialog__inner-wrapper">
            <header class="dialog__header">
                <div class="dialog__title">{{__('text.common_callback')}}</div>
                <button class="dialog__close-button close-button">Close</button>
            </header>
            <form class="form callback-form" method="dialog">
                <div class="form__field phone_block">
                    <div class="enter-info__country phone_code">
                        <select name="phone_code" class="form" id="phone_code_select" data-scroll>
                            @foreach ($phone_codes as $item)
                                <option id=""
                                @if (empty(session('form')))
                                        @selected($item['iso'] == session('location.country', ''))
                                @else
                                    @selected($item['iso'] == session('form.phone_code', ''))
                                @endif
                                    data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                    value="{{ $item['iso'] }}">
                                    +{{ $item['phonecode'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="enter-info__input enter-info__input--country">
                        <input required autocomplete="off" type="number" id="phone" name="phone" placeholder="000 000 00 00" class="input" maxlength = "14" oninput="maxLengthCheck(this)">
                    </div>
                </div>
                <div class="form__field">
                    <input class="button form__submit button_request_call" type="submit" value="{{__('text.common_callback')}}">
                </div>
            </form>
            <div class="message_sended hidden">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.phone_request_mes_text')}}</p>
            </div>
        </div>
    </dialog>

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

    <div class="popup_gray" style="display: none">
        <div class="popup_call">
            <button class="dialog__close-button close-button-message">Close</button>
            <div class="message_sended">
                <h2>{{__('text.contact_us_thanks')}}</h2>
                <br>
                <p>{{__('text.phone_request_mes_text')}}</p>
            </div>
        </div>
    </div>

    <script src="{{ asset("$design/js/app.js") }}"></script>
    <script defer src="{{ asset($design . '/js/custom-select.min.js') }}"></script>
    <script defer src="{{ asset($design . '/js/intlTelInput.min.js') }}"></script>
    <script defer src="{{ asset($design . '/js/main.js') }}"></script>
    <script src="{{ asset("/js/all_js.js") }}"></script>
    {{-- {if $data.page_name ne "checkout"}
        {if $data.web_statistic}
            <input hidden id="stattemp" value="{$data.web_statistic.params_string}">
        {/if}
    {/if} --}}
</body>

</html>
