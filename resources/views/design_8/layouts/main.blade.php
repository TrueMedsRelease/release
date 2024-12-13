<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Title')</title>
    <meta name="robots" content="index, follow" />
    <meta name="Description" content="@yield('description', 'Description')">
    <meta name="Keywords" content="@yield('keywords', 'Keywords')">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#087ED8" />
    <meta name="format-detection" content="telephone=no">

    <link rel="alternate" href="{{ config('app.url') }}/lang=arb" hreflang="ar" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=cs" hreflang="cs" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=da" hreflang="da" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=de" hreflang="de" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=en" hreflang="en" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=es" hreflang="es" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fi" hreflang="fi" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=fr" hreflang="fr" />
    <link rel="alternate" href="{{ config('app.url') }}/lang=gr" hreflang="el" />
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

    @if (env('APP_PWA', 0))
        <link rel="manifest" href="{{ asset($design . '/images/favicon/manifest.webmanifest') }}">
        <script defer type="text/javascript" src="{{ asset("/js/sw-setup.js") }}"></script>
    @endif

    <script type="text/javascript" src="{{ asset("/js/delete_cache.js") }}"></script>

    {{-- <script defer type="text/javascript" src="{{ "vendor/jquery/pwa.js" }}"></script> --}}

    <link href="{{ asset($design . '/css/style.css?v=261124') }}" rel="stylesheet">

    <script defer src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/autocomplete.js') }}"></script>
    <script defer src="{{ asset('vendor/jquery/init.js') }}"></script>
    <script defer type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>
    {!! isset($pixel) ? $pixel : '' !!}
</head>

<body>
    <script>
        let flagc = false;
        let flagp = false;
        let flagm = false;
        const design = 8;
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

    <div class="wrapper">
        <input type="hidden" class="design" value="{{ $design }}">
        <div class="popup_gray" style="display: none">
            <div class="popup_call">
                <div class="button_close">
                    <svg class="close_popup" width="15" height="15">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-close") }}"></use>
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
                                placeholder="000 000 00 00" class="input" maxlength = "14"
                                oninput="maxLengthCheck(this)">
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
        <header class="header">

            <div class="christmas" style="display: none">
                {{-- <img loading="lazy" src="{{ asset("/pub_images/pay_big.png") }}"> --}}
                <img loading="lazy" src="{{ asset("/pub_images/christmas_big.png") }}">
            </div>

            <div class="header__phones-top top-phones-header">
                <div class="header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a
                                class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_1') }}">{{ __('text.phones_title_phone_1_code') }}{{ __('text.phones_title_phone_1') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_2') }}">{{ __('text.phones_title_phone_2_code') }}{{ __('text.phones_title_phone_2') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_3') }}">{{ __('text.phones_title_phone_3_code') }}{{ __('text.phones_title_phone_3') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_4') }}">{{ __('text.phones_title_phone_4_code') }}{{ __('text.phones_title_phone_4') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_5') }}">{{ __('text.phones_title_phone_5_code') }}{{ __('text.phones_title_phone_5') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_6') }}">{{ __('text.phones_title_phone_6_code') }}{{ __('text.phones_title_phone_6') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_7') }}">{{ __('text.phones_title_phone_7_code') }}{{ __('text.phones_title_phone_7') }}</a>
                    </div>
                </div>
            </div>
            <div class="header__container header__container--second">
                <div class="header__body">
                    <div class="header__right">
                        <div class="header__top">
                            <div class="header__inner">
                                <div class="header__top-row">
                                    <a href="{{ route('home.index') }}" class="header__logo logo">
                                        <img loading="lazy" src="{{ asset("$design/images/logo.svg") }}" alt="">
                                    </a>
                                </div>
                                <div class="header__info-row">
                                    <div class="header__actions actions">
                                        @if (count($Language::GetAllLanuages()) > 1)
                                            <div class="actions__item">
                                                <div class="actions__icon">
                                                    <img loading="lazy" src="{{ asset("$design/images/icons/lang.svg") }}"
                                                        width="24" height="20" alt="">
                                                </div>
                                                <div class="actions__select">
                                                    <select name="form[]" class="form" id="lang_select"
                                                        onchange="location.href=this.options[this.selectedIndex].value"
                                                        data-scroll>
                                                        @foreach ($Language::GetAllLanuages() as $item)
                                                            <option value="{{ route('home.language', $item['code']) }}" data-code="{{ $item['code'] }}"
                                                                @if (App::currentLocale() == $item['code']) selected @endif>
                                                                {{ $item['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($Currency::GetAllCurrency()) > 1)
                                            <div class="actions__item">
                                                <div class="actions__icon">
                                                    <img loading="lazy" src="{{ asset("$design/images/icons/wallet.svg") }}"
                                                        width="24" height="20" alt="">
                                                </div>
                                                <div class="actions__select">
                                                    <select name="form[]" class="form" id="curr_select"
                                                        onchange="location.href=this.options[this.selectedIndex].value"
                                                        data-scroll>
                                                        @foreach ($Currency::GetAllCurrency() as $item)
                                                            <option value="{{ route('home.currency', $item['code']) }}"
                                                                @if (session('currency') == $item['code']) selected @endif>
                                                                {{ Str::upper($item['code']) }} </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
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
                                    <button type="button" class="header__cart cart"
                                        @if ($cart_count != 0) onclick="location.href='{{ route('cart.index') }}'" @endif>
                                        <span class="cart__icon">
                                            <img loading="lazy" src="{{ asset("$design/images/icons/cart.svg") }}" width="24"
                                                height="24">
                                            @if ($cart_count != 0)
                                                <span class="cart__quantity">{{ $cart_count }}</span>
                                            @endif
                                        </span>
                                        <span class="cart__total">{{ $Currency::convert($cart_total) }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')

        <footer>
            <div class="header__phones-top top-phones-header footer">
                <div class="header__container">
                    <div class="top-phones-header__items">
                        <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a
                                class="request_call">{{ __('text.common_callback') }}</a>
                            <div class="request_text">{{ __('text.common_call_us_top') }}</div>
                        </div>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_1') }}">{{ __('text.phones_title_phone_1_code') }}{{ __('text.phones_title_phone_1') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_2') }}">{{ __('text.phones_title_phone_2_code') }}{{ __('text.phones_title_phone_2') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_3') }}">{{ __('text.phones_title_phone_3_code') }}{{ __('text.phones_title_phone_3') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_4') }}">{{ __('text.phones_title_phone_4_code') }}{{ __('text.phones_title_phone_4') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_5') }}">{{ __('text.phones_title_phone_5_code') }}{{ __('text.phones_title_phone_5') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_6') }}">{{ __('text.phones_title_phone_6_code') }}{{ __('text.phones_title_phone_6') }}</a>
                        <a class="top-phones-header__item"
                            href="tel:{{ __('text.phones_title_phone_7') }}">{{ __('text.phones_title_phone_7_code') }}{{ __('text.phones_title_phone_7') }}</a>
                    </div>
                </div>
            </div>

            <p class="footer_copyright">
                {{ __('text.license_text_license1_1') }}
                {{ $domain }}
                {{ __('text.license_text_license1_2') }}
                {{ __('text.license_text_license2_d8') }}
            </p>

            <script defer src="{{ asset("$design/js/app.js") }}"></script>
            <script defer src="{{ asset("$design/js/main.js") }}"></script>
            <script defer src="{{ asset('/js/all_js.js') }}"></script>

        </footer>

        @if ($web_statistic)
            <input hidden id="stattemp" value="{{ $web_statistic['params_string'] }}">
        @endif
</body>

</html>
