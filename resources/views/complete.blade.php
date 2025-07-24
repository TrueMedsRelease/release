<!DOCTYPE html>
<html lang="ru">

<head>
    <title>{{ __('text.success_title') }}</title>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="index, follow" />
    <link rel="stylesheet" href="{{ asset('style_checkout/style.css') }}?v=24042024">
    <link rel="shortcut icon" href="{{ asset('style_checkout/favicon.ico') }}">
    <script src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {!! isset($pixel) ? $pixel : '' !!}
</head>

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

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__phones-top top-phones-header">
                <div class="top-phones-header__container header__container">
                    <div class="top-phones-header__items">
                        @foreach ($phone_arr as $id_phone => $phones)
                            <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="header__content">
                <div class="header__container">
                    <div class="header__top">
                        <a class="header__logo"><img loading="lazy" src="{{ asset('style_checkout/images/logo.svg') }}"
                                alt=""></a>
                        <div class="header__selects">
                            <div class="currency header__select">
                                <h2 class="header__caption">{{ __('text.checkout_currency') }}</h2>
                                <select name="form[]" id="currency_select" class="form"
                                    onclick="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ url()->current() }}/curr={{ $item['code'] }}"
                                            @if (session('currency') == $item['code']) selected @endif>
                                            {{ Str::upper($item['code']) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="header__select header__select--language language">
                                <h2 class="header__caption">{{ __('text.checkout_language') }}</h2>
                                <select name="form[]" id="language_select" class="form"
                                    onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ url()->current() }}/lang={{ $item['code'] }}"
                                            @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="header__bottom">
                        <div class="header__inner">
                            <a href="{{ route('cart.index') }}" class="header__link-back">
                                <svg width="18" height="18">
                                    <use
                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-right">
                                    </use>
                                </svg>
                                <span>{{ __('text.checkout_back') }}</span>
                            </a>
                            <div class="header__partners">
                                <div class="header__partner">
                                    <img loading="lazy" src="{{ asset('style_checkout/images/partners/geotrust.svg') }}"
                                        width="90" height="30" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img loading="lazy" src="{{ asset('style_checkout/images/partners/norton.svg') }}" width="70"
                                        height="40" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img loading="lazy" src="{{ asset('style_checkout/images/partners/comodo.svg') }}" width="90"
                                        height="30" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img loading="lazy" src="{{ asset('style_checkout/images/partners/mcafee.svg') }}" width="80"
                                        height="25" alt="Awesome image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="succes">
            <div class="succes__container">
                {{-- <h1 class="succes__title"></h1> --}}
                <h2 class="succes__subtitle">{{ __('text.success_complete') }} {{ __('text.success_congrat') }} {{ session('form.firstname') . ' ' . session('form.lastname') }}{{ __('text.success_choice') }}
                </h2>
                @if(session('order') != 'error')
                <div class="succes__block">
                    <b>{{ __('text.success_order_number') }}<span> {{ session('order.order_id') }}</span></b>
                </div>
                @endif
                <div class="succes__block succes__block--transparent">
                    {{ __('text.success_charge') }}<b>{{ $Currency::convert(session('total.checkout_total')) }}</b>, {{ __('text.success_amount') }}
                </div>
                @if (session('order.gift_card'))
					<div class="gift_block">
						@foreach (session('order.gift_card') as $card)
							<div class="gift">
								<div class="gift_text">{{ __('text.common_gift_card') }} {{ $Currency::convert($card['price']) }}</div>
								<img loading="lazy" src="{{ asset("style_checkout/images/gift_card_img.svg") }}" class="gift_img">
								<div class="gift_code">
									<div class="code_text" id="code_text">{{ $card['code'] }}</div>
									<img loading="lazy" src="{{ asset("style_checkout/images/icons/copy.png") }}" id="copy_img">
								</div>
							</div>
						@endforeach
					</div>
				@endif
                <div class="succes__block">
                    <p><b>{{ __('text.success_confirm') }}</b></p>
                    <div class="succes__row">
                        <a style="cursor: pointer;" class="succes__button button" id="succes__button">
                            <svg width="18" height="18">
                                <use xlink:class="succes__link"
                                    href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-phone"></use>
                            </svg>
                            <span>{{ __('text.success_call_me') }}</span>
                        </a>
                        <p id = "thank" hidden>
                            {{ __('text.success_shortly') }}
                        </p>
                        <p class="succes__phones">
                            <span>{{ __('text.success_call_us') }}</span>
                            @foreach ($phone_arr as $id_phone => $phones)
                                <a class="succes__link" href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a>
                            @endforeach
                        </p>
                    </div>
                </div>
                <div class="succes__block">
                    <div style="margin-bottom: 15px">
                        <b>{{ __('text.success_support') }}</b>
                        <a class="succes__link" href="https://true-client-support.com">true-client-support.com</a>
                    </div>
                    <div>
                        <b>{{ __('text.success_email') }}</b>
                        <a class="succes__link" href="mailto:support@true-client-support.com">support@true-client-support.com</a>
                    </div>
                </div>
                <div class="succes__last-words">
                    {{-- <p>{{ __('text.common_receive') }}</p>
                    <p>{{ __('text.success_sms') }}</p> --}}
                    <p style="color: #e14c5c;">{{ __('text.complete_bottom_text_1') }}</p>
                    <p style="color: #e14c5c;">{{ __('text.complete_bottom_text_2') }}</p>
                    <p>{{ __('text.success_help') }}</p>
                    <p>{{ __('text.success_thank') }}</p>
                </div>
            </div>
        </main>
        <footer class="footer">
            <div class="footer__container">
                <p class="footer__text">{{ __('text.success_copyright') }} {{ __('text.success_ltd') }}</p>
            </div>
        </footer>
    </div>

    <script src="{{ asset('style_checkout/js/app_success.js') }}"></script>

    <script>
        $(".succes__button").click(function() {
            document.getElementById("succes__button").style.display = 'none';
            document.getElementById("thank").hidden = false;
        });
    </script>
</body>

</html>
