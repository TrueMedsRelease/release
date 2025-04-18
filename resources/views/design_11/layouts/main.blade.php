<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#616ede" />
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

    {{-- <link rel="icon" href="//{{ request()->headers->get('host') }}/design_11/images/favicon/favicon.ico" sizes="any"> --}}

    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset($design . '/images/favicon/favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset($design . '/images/favicon/apple-touch-icon-180x180.png') }}">

    <link href="{{ asset($design . '/fonts/neue-machina-regular.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="{{ asset($design . '/fonts/neue-machina-bold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="{{ asset($design . '/fonts/neue-machina-ultrabold.woff2') }}" rel="preload" as="font" type="font/woff2" crossorigin="anonymous">

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("/js/sw-setup.js") }}"></script>
    @endif

    <script type="text/javascript" src="{{ asset("/js/delete_cache.js") }}"></script>

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/vendor/custom-select/custom-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/vendor/intl-tel/css/intlTelInput.min.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/pages.css') }}" rel="stylesheet">

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>

    <script defer src="{{ asset($design . '/vendor/custom-select/custom-select.min.js') }}"></script>
    <script defer src="{{ asset($design . '/vendor/intl-tel/js/intlTelInput.min.js') }}"></script>
    <script defer src="{{ asset($design . '/vendor/just-validate.min.js') }}"></script>
    <script defer src="{{ asset("$design/js/app.js") }}"></script>

    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body class="webp @yield('page_name')">
    <script>
        let flagc = false;
        let flagp = false;
        const design = 11;
    </script>

    @if (session('locale'))
        <input type="hidden" id="lang_session" value="{{ $Language::$languages_name[session('locale')] }}">
    @endif

    @if (session('order'))
        <input type="hidden" id="order_info_session" value="{{ json_encode(session('order')) }}">
    @endif

    <input type="hidden" id="is_pwa_here" value="{{ env('APP_PWA', 0) }}">
    <input type="hidden" id="vapid_pub" value="{{ base64_encode(env('VAPID_PUBLIC_KEY', '')) }}">
    <input type="hidden" id="subsc_popup" value="{{ env('SUBSCRIBE_POPUP_STATUS', 1) }}">
    <input type="hidden" id="country_iso" value="{{ $codes }}">
    <input type="hidden" id="initial_country" value="{{ strtolower(session('location.country')) }}">

    {{-- <div class="christmas" style="display: none">
        <img loading="lazy" src="{{ asset("/pub_images/pay_big.png") }}">
        <img loading="lazy" src="{{ asset("/pub_images/christmas_big.png") }}">
    </div> --}}

    <div class="topbar">
        <div class="container">
            <div class="header-phones drag-nav">
                <div class="drag-nav-container">
                    <div class="request-callback">
                        <button class="link" data-dialog="call">{{ __('text.common_callback') }}</button>
                        <span>&nbsp;{{ __('text.common_call_us_top') }}</span>
                    </div>
                    <a href="tel:{{ __('text.phones_title_phone_1') }}">{{ __('text.phones_title_phone_1_code') }} {{ __('text.phones_title_phone_1') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_2') }}">{{ __('text.phones_title_phone_2_code') }} {{ __('text.phones_title_phone_2') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_3') }}">{{ __('text.phones_title_phone_3_code') }} {{ __('text.phones_title_phone_3') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_4') }}">{{ __('text.phones_title_phone_4_code') }} {{ __('text.phones_title_phone_4') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_5') }}">{{ __('text.phones_title_phone_5_code') }} {{ __('text.phones_title_phone_5') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_6') }}">{{ __('text.phones_title_phone_6_code') }} {{ __('text.phones_title_phone_6') }}</a>
                    <a href="tel:{{ __('text.phones_title_phone_7') }}">{{ __('text.phones_title_phone_7_code') }} {{ __('text.phones_title_phone_7') }}</a>
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
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#menu-fries') }}"></use>

                                </svg>
                            </span>
                            <span class="icon button__close-icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close-thin') }}"></use>

                                </svg>
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
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @else
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a> </div>
                                    <div class="greedy-item"><a class="header-nav-link" href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a></div>
                                @endif
                            </div>
                            <div class="dropdown" data-fixed-dropdown>
                                <button class="dropdown-toggler link greedy-button" aria-label="Show dropdown">
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-sr-menu-dots-vertical') }}"></use>
                                        </svg>
                                    </span>
                                    <span class="icon is-hidden">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close') }}"></use>
                                        </svg>
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
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                </svg>
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
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                </svg>
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
                    <img src="{{ asset($design . '/svg/logo.svg') }}" width="214" height="52" alt="{{ $domainWithoutZone }}">
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
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                    </svg>
                                </span>
                            </a>
                        </li>
                        @foreach ($menu as $category)
                            <li class="nav__item">
                                <a class="nav__link nav__sublist-toggler" href="{{ route('home.category', $category['url']) }}" data-sublist-index="{{ $loop->iteration }}">{{ $category['name'] }}
                                    <span class="icon">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                        </svg>
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
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                        </svg>
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
                                            <svg width="1em" height="1em" fill="currentColor">
                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                            </svg>
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
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' visa' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#visa">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' mastercard' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#mastercard">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' maestro' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#maestro">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' discover' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#discover">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amex' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amex">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' jcb' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#jsb">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' union-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#unionpay">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dinners-club' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#dinners-club">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' apple-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#apple-pay">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' google-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#google-pay">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' amazon-pay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#amazon-pay">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' stripe' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#stripe">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' paypal' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#paypal">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' sepa' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#sepa">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' cashapp' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#cashapp">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' adyen' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#adyen">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' skrill' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#skrill">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' worldpay' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#worldpay">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' payline' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#payline">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' bitcoin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#bitcoin">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' binance-coin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#binance-coin">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ethereum' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#ethereum">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' litecoin' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#litecoin">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tron' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#tron">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(erc20)' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(erc20)">
                        </svg>
                    </div>
                    <div class="promos-payment-method">
                        <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usdt(trc20)' }}" @endif>
                            <use width="100%" height="100%" href="/pub_images/pay_icons/sprite.svg#usdt(trc20)">
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
            <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
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
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#arrow-circle') }}"></use>
                        </svg>
                    </span>
                    <span class="button-text">{{ __('text.common_subscribe') }}</span>
                </button>
            </form>
        </div>

        @yield('rewies')

        <div class="footer-delivery-methods drag-nav" style="@yield('delivery_style')">
            <div class="drag-nav-container" style="@yield('delivery_style_2')">
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#usps" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ems" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dhl" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#ups" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#fedex" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#tnt" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#postnl" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#deutsche_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#dpd" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#gls" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                        <use width="100%" height="100%" width="100%" href="/pub_images/shipping/sprite.svg#australia_post" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#colissimo" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
                <div class="footer-delivery-method">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                        <use width="100%" height="100%" href="/pub_images/shipping/sprite.svg#correos" preserveAspectRatio="xMinYMin">
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                <div class="footer__wrapper">
                    <a class="footer__logo" href="{{ route('home.index') }}">
                        <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="{{ $domainWithoutZone }}">
                    </a>
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
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
                        <img src="{{ asset($design . '/svg/logo-footer.svg') }}" width="206" height="44" alt="{{ $domainWithoutZone }}">
                    </a>
                    <nav class="nav footer-nav">
                        <div class="nav-container">
                            <ul class="nav__list">
                                <li class="nav__item"><a class="nav__link is-active" href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a></li>
                                <li class="nav__item"><a class="nav__link" href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a></li>
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
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#menu-fries') }}"></use>
                            </svg>
                        </span>
                        <span class="icon button__close-icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#close-thin') }}"></use>
                            </svg>
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
                <svg class="close_popup" width="15" height="15">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
                </svg>
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

    <script defer src="{{ asset('/js/all_js.js') }}"></script>
    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif
</body>
</html>