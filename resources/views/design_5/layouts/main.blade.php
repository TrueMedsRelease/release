<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#4FAFCD" />
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

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("js/sw-setup.js") }}"></script>
    @endif

    {{-- <script type="text/javascript" src="{{ asset("js/delete_cache.js") }}"></script> --}}

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/css/style.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset($design . '/css/all.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset($design . '/css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset($design . '/css/slick.css') }}" rel="stylesheet">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
    </script>

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        let flagm = false;
        const design = 5;
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

    <header class="header">

        {{-- <div class="christmas" style="display: none">
            <img loading="lazy" src="{{ asset("pub_images/pay_big.png") }}">
            <img loading="lazy" src="{{ asset("pub_images/christmas_big.png") }}">
        </div> --}}

        <div class="phone-box">
            <div class="container">
                <div class="holder-phone">
                    <div class="phone_info"><a class="request_call">{{ __('text.common_callback') }}</a>
                        <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                    </div>
                    @foreach ($phone_arr as $id_phone => $phones)
                        <a href="tel:{{ __('text.phones_title_phone_' . $id_phone) }}">{{ __('text.phones_title_phone_' . $id_phone . '_code') }} {{ __('text.phones_title_phone_' . $id_phone) }}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="container">
            <div class="nav">
                <span class="close"></span>
                <div class="menu_top">
                    <ul>
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            @php
                                $domainWithoutZone = preg_replace('/\.[^.]+$/', '', request()->getHost());
                            @endphp
                            <li class="categories_button"><img loading="lazy" src="{{ asset("$design/images/icon/ico-menu.svg") }}"
                                alt=""><a class="categories_a">{{ __('text.common_categories_menu') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                            </li>
                            <li><a href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                            </li>
                            <li><a href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                            <li><a
                                    href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                            </li>
                        @else
                            <li class="categories_button"><img loading="lazy" src="{{ asset("$design/images/icon/ico-menu.svg") }}"
                                alt=""><a class="categories_a">{{ __('text.common_categories_menu') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                            </li>
                            <li><a href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                            </li>
                            <li><a href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a></li>
                            <li><a
                                    href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                            </li>
                            <li><a
                                    href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <aside class="categories-sidebar hide">
                    <div class="categories-sidebar__inner">
                        <div data-spollers class="categories-sidebar__spollers spollers">
                            <div class="spollers__item">
                                <button type="button" data-spoller
                                    class="spollers__title _spoller-active">{{ __('text.common_best_selling_title') }}</button>
                                <ul class="spollers__body main_bestsellers" id="main_bestsellers">
                                    @foreach ($bestsellers as $bestseller)
                                        <li class="spollers__item-list"><a
                                                href="{{ route('home.product', $bestseller['url']) }}">{{ $bestseller['name'] }}</a><span
                                                style="font-size: 12px;">{{ $Currency::convert($bestseller['price'], false, true) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @foreach ($menu as $category)
                                <div class="spollers__item">
                                    <button type="button" data-spoller
                                        class="spollers__title @if ($cur_category == $category['name']) _spoller-active @endif">{{ $category['name'] }}</button>
                                    <ul class="spollers__body">
                                        @foreach ($category['products'] as $item)
                                            <li class="spollers__item-list">
                                                <a href="{{ route('home.product', $item['url']) }}">
                                                    {{ $item['name'] }}
                                                </a>
                                                <span
                                                    style="font-size: 12px;">{{ $Currency::Convert($item['price'], false, true) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>

            <div class="header-container">
                <div class="panel-box">
                    <a href="{{ route('home.index') }}" class="logo">
                        @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                            <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" width="145" height="40" alt="{{ $domainWithoutZone }}">
                        @else
                            <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" width="145" height="40" alt="Logo">
                        @endif
                    </a>
                    <div class="drop-info">
                        @if (count($Language::GetAllLanuages()) > 1)
                            <div class="lang drop">
                                <select name="form[]" class="form" id="lang_select"
                                    onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                            @if (App::currentLocale() == $item['code']) selected @endif>{{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @if (count($Currency::GetAllCurrency()) > 1)
                            <div class="wallet drop">
                                <select name="form[]" class="form" id="curr_select"
                                    onchange="location.href=this.options[this.selectedIndex].value">
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ route('home.currency', $item['code']) }}"
                                            @if (session('currency') == $item['code']) selected @endif>
                                            {{ Str::upper($item['code']) }} </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="profile_top_block">
                            <a href="{{ route('home.login') }}" class="item profile_top" target="_blank">
                                <span class="ico">
                                    <img loading="lazy" src="{{ asset("$design/images/icon/ico-profile.svg") }}" alt=""
                                        width="20" height="20">
                                </span>
                                <span class="name">{{ __('text.common_profile') }}</span>
                            </a>
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
                    <a @if ($cart_count != 0) href="{{ route('cart.index') }}" @endif class="cart-box">
                        <span class="icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M18 19.1249H8.079C7.7247 19.125 7.3818 18.9996 7.111 18.7709C6.8403 18.5423 6.6593 18.2253 6.6 17.8759L3.963 2.37593C3.9035 2.02678 3.7224 1.70994 3.4517 1.48153C3.181 1.25311 2.8382 1.12785 2.484 1.12793H1.5"
                                    stroke="#262D38" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M16.125 22.125C16.3321 22.125 16.5 21.9571 16.5 21.75C16.5 21.5429 16.3321 21.375 16.125 21.375"
                                    stroke="#262D38" stroke-width="1.5" />
                                <path
                                    d="M16.125 22.125C15.9179 22.125 15.75 21.9571 15.75 21.75C15.75 21.5429 15.9179 21.375 16.125 21.375"
                                    stroke="#262D38" stroke-width="1.5" />
                                <path
                                    d="M8.625 22.125C8.8321 22.125 9 21.9571 9 21.75C9 21.5429 8.8321 21.375 8.625 21.375"
                                    stroke="#262D38" stroke-width="1.5" />
                                <path
                                    d="M8.625 22.125C8.4179 22.125 8.25 21.9571 8.25 21.75C8.25 21.5429 8.4179 21.375 8.625 21.375"
                                    stroke="#262D38" stroke-width="1.5" />
                                <path
                                    d="M6.04661 14.6251H18.1176C18.7865 14.625 19.4362 14.4014 19.9635 13.9897C20.4907 13.5781 20.8653 13.002 21.0276 12.3531L22.4776 6.55309C22.5053 6.44248 22.5073 6.32702 22.4837 6.21548C22.46 6.10394 22.4112 5.99927 22.3411 5.90941C22.2709 5.81955 22.1811 5.74688 22.0786 5.69692C21.9761 5.64696 21.8636 5.62103 21.7496 5.62109H4.51561"
                                    stroke="#262D38" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M18 8.625V10.875" stroke="#262D38" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M13.5 8.625V10.875" stroke="#262D38" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9 8.625V10.875" stroke="#262D38" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                            @if ($cart_count != 0)
                                <span class="status">{{ $cart_count }}</span>
                            @endif
                        </span>
                        <span class="price">{{ $Currency::convert($cart_total) }}</span>
                    </a>
                </div>
                <div class="certificates-box">
                    <div class="verified-info">
                        <strong>{{ __('text.common_verified_d4') }}</strong>
                        <span>{{ __('text.common_approved_d4') }}</span>
                    </div>
                    <img loading="lazy" src="{{ asset("$design/images/img-certificates.png") }}" @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) alt="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' partners' }}" @else alt="" @endif>
                </div>
            </div>
        </div>

        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icon/icons.svg#svg-close") }}"></use>
                    </svg>
                </div>
                <div class="popup_bottom">
                    <div class="popup_text">{{ __('text.common_callback') }}</div>
                    <div class="phone">
                        <div class="enter-info__country phone_code">
                            <select name="phone_code" class="form" data-scroll>
                                @foreach ($phone_codes as $item)
                                    <option id=""
                                        @if (empty(session('form'))) @selected($item['iso'] == session('location.country', ''))
                                @else
                                    @selected($item['iso'] == session('form.phone_code', '')) @endif
                                        data-asset="{{ asset('style_checkout/images/countrys/sprite.svg#' . $item['nicename']) }}"
                                        value="+{{ $item['phonecode'] }}">
                                        +{{ $item['phonecode'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="enter-info__input enter-info__input--country">
                            <input required autocomplete="off" type="number" id="phone" name="phone"
                                value="" placeholder="000 000 00 00" class="input"
                                maxlength = "14" oninput="maxLengthCheck(this)">
                        </div>
                    </div>
                    <div class="button_request_call">{{ __('text.common_callback') }}</div>
                </div>
                <div class="message_sended request hidden">
                    <h2>{{ __('text.contact_us_thanks') }}</h2>
                    <br>
                    <p>{{ __('text.phone_request_mes_text') }}</p>
                </div>
            </div>
        </div>

        <div class="popup_white hide">
            <div class="popup_push">
                <div class="button_close">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icon/icons.svg#svg-close") }}"></use>
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
    </header>
    <main class="content">
        <div class="container">

            @yield('content')

        </div>
    </main>

    <section class="subscribe_container">
        <div class="block_subscribe">
            <div class="left_block">
                <div class="subscribe_img">
                    <img loading="lazy" src="{{ asset("$design/images/icon/subscribe.svg") }}">
                </div>
                <div class="text_subscribe">
                    <span class="top_text">{{ __('text.common_subscribe') }}</span>
                    <span class="bottom_text">{{ __('text.common_spec_offer') }}</span>
                </div>
            </div>
            <div class="right_block">
                <input type="text" placeholder="Email" class="form__input input" id="email_sub">
                <div class="button_sub">
                    <img loading="lazy" src="{{ asset("$design/images/icon/subscribe_mini.svg") }}" class="sub_mini">
                    <span class="button_text">{{ __('text.common_subscribe') }}</span>
                </div>
            </div>
        </div>
    </section>


   <section class="ship-index">
        <div class="ship-index__container">
            <ul class="ship-index__list">
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' usps' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#usps')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ems' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ems')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dhl' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dhl')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' ups' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#ups')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' fedex' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#fedex')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' tnt' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#tnt')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' postnl' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#postnl')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' deutsche_post' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#deutsche_post')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' dpd' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#dpd')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' gls' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#gls')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' australia_post' }}" @endif>
                        <use width="100%" height="100%" width="100%" href="{{ asset('pub_images/shipping/sprite.svg#australia_post')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' colissimo' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#colissimo')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
                <li class="ship-index__item">
                    <svg @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957])) aria-label="{{ __('text.text_aff_domain_1') . ' ' . __('text.text_aff_domain_2') . ' correos' }}" @endif>
                        <use width="100%" height="100%" href="{{ asset('pub_images/shipping/sprite.svg#correos')  }}" preserveAspectRatio="xMinYMin">
                    </svg>
                </li>
            </ul>
        </div>
    </section>

    <footer class="footer">
        <div class="item-box">
            <section class="page__reviews reviews">
                <div class="feedback">
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_1') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_1') }}</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_2') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_2') }}</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_3') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_3') }}</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_4') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_4') }}</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_5') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_5') }}</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="head">
                            <span class="name">{!! __('text.testimonials_author_t_6') !!}</span>
                            <div class="stars">
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                                <span class="active"></span>
                            </div>
                        </div>
                        <div class="text">
                            <p>{{ __('text.testimonials_t_6') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="item-box">
            <div class="footer-info">
                <div class="info-column">
                    @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                        <div class="item">
                            <ul class="footer-nav">
                                <li><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.about', '_' . $domainWithoutZone) }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li><a href="{{ route('home.help', '_' . $domainWithoutZone) }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.testimonials', '_' . $domainWithoutZone) }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="item">
                            <ul class="footer-nav">
                                <li><a
                                        href="{{ route('home.delivery', '_' . $domainWithoutZone) }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.moneyback', '_' . $domainWithoutZone) }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.contact_us', '_' . $domainWithoutZone) }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="item">
                            <a href="{{ route('home.affiliate', '_' . $domainWithoutZone) }}"
                                class="btn btn-primary">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @else
                        <div class="item">
                            <ul class="footer-nav">
                                <li><a
                                        href="{{ route('home.index') }}">{{ __('text.common_best_sellers_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.about', '') }}">{{ __('text.common_about_us_main_menu_item') }}</a>
                                </li>
                                <li><a href="{{ route('home.help', '') }}">{{ __('text.common_help_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.testimonials', '') }}">{{ __('text.common_testimonials_main_menu_item') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="item">
                            <ul class="footer-nav">
                                <li><a
                                        href="{{ route('home.delivery', '') }}">{{ __('text.common_shipping_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.moneyback', '') }}">{{ __('text.common_moneyback_main_menu_item') }}</a>
                                </li>
                                <li><a
                                        href="{{ route('home.contact_us', '') }}">{{ __('text.common_contact_us_main_menu_item') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="item">
                            <a href="{{ route('home.affiliate', '') }}"
                                class="btn btn-primary">{{ __('text.common_affiliate_main_menu_button') }}</a>
                        </div>
                    @endif
                </div>
                @if (in_array(session('aff'), [1799, 1947, 1952, 1957]) || in_array(env('APP_AFF'), [1799, 1947, 1952, 1957]))
                    <div class="sitemap_menu">
                        <a class="navigation__link" href="{{ route('home.sitemap', '_' . $domainWithoutZone) }}">{{__('text.menu_title_sitemap')}}</a>
                    </div>
                @endif
                <div class="copyright">
                    <p>
                        {{ __('text.license_text_license1_1') }}
                        {{ $domain }}
                        {{ __('text.license_text_license1_2') }}
                        {{ __('text.license_text_license2_d5') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <div class="mob-nav">
        <a href="#" class="item js-menu">
            <span class="ico">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-menu.svg") }}" alt="">
            </span>
            <span class="name">{{ __('text.common_categories_menu') }}</span>
        </a>
        <a href="{{ route('home.index') }}" class="item">
            <span class="ico">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-home.svg") }}" alt="">
            </span>
            <span class="name">{{ __('text.common_home_main_menu_item') }}</span>
        </a>
        <a href="{{ route('home.login') }}" class="item" target="_blank">
            <span class="ico">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-profile.svg") }}" alt="">
            </span>
            <span class="name">{{ __('text.common_profile') }}</span>
        </a>
        <a @if ($cart_count != 0) href="{{ route('cart.index') }}" @endif class="item cart">
            <span class="ico">
                <img loading="lazy" src="{{ asset("$design/images/icon/ico-cart.svg") }}" alt="">
                <span class="number">{{ $cart_count }}</span>
            </span>
            <span class="name">{{ $Currency::convert($cart_total) }}</span>
        </a>
    </div>
    <div class="announce">
        <div class="announce__item @yield('announce_color', 'announce__item--blue')">
            <div class="announce__icon">
                <svg width="24" height="24">
                    <use xlink:href="@yield('announce_img', asset($design . '/images/icon/icons.svg#svg-checkmark'))"></use>
                </svg>
            </div>
            <div class="announce__text">
                <b>@yield('announce_text_1', random_int(2, 30) .' ' .__('text.common_product1'))</b>@yield('announce_text_2', __('text.common_product2'))
            </div>
        </div>
    </div>

    @if ($web_statistic)
        <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
    @endif

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
    </script>

    <script defer src="{{ asset("$design/js/app.js") }}"></script>
    <script defer src="{{ asset("$design/js/slick.js") }}"></script>
    <script defer src="{{ asset("$design/js/main.js") }}"></script>
    <script defer src="{{ asset('js/all_js.js') }}"></script>

</body>

</html>
