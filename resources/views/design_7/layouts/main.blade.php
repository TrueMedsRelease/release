<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Defult')</title>
    <meta name="description" content="Verified Pharmacy Store">
    <meta name="keywords" content="key, words">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#161C8E"/>
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
    let flagc = false;
    let flagp = false;
    let flagm = false;
    const design = 7;
</script>
<div class="wrapper">
    <input type="hidden" class="design" value="{{$design}}">
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
				<div class="button_request_call">{{__('text.common_callback')}}</div>
			</div>
			<div class="message_sended request hidden">
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
	<header class="header">
		<div class="header__phones-top top-phones-header">
            <div class="header__container">
                <div class="top-phones-header__items">
                    <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a class="request_call">{{ __('text.common_callback') }}</a><div class="request_text">{{__('text.common_call_us_top')}}</div></div>
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
		<div class="header__container header__container--second">
			<div class="header__body">
				<div class="header__right">
					<div class="header__top">
						<div class="header__inner">
							<div class="header__top-row">
								<a href="{{ route('home.index') }}" class="header__logo logo">
									<img src="{{ asset("$design/images/logo.svg") }}" alt="">
								</a>
							</div>
							<div class="header__info-row">
								<div class="header__actions actions">
									<div class="actions__item">
										<div class="actions__icon">
											<img src="{{ asset("$design/images/icons/lang.svg") }}" width="24" height="20" alt="">
										</div>
										<div class="actions__select">
											<select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
												@foreach ($Language::GetAllLanuages() as $language)
                                                    <option value="/lang={{$language['code']}}" @if (App::currentLocale() == $language['code']) selected @endif>{{$language['name']}}</option>
                                                @endforeach
											</select>
										</div>
									</div>
									<div class="actions__item">
										<div class="actions__icon">
											<img src="{{ asset("$design/images/icons/wallet.svg") }}" width="24" height="20" alt="">
										</div>
										<div class="actions__select">
											<select name="form[]" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
												@foreach ($Currency::GetAllCurrency() as $item)
                                                    <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                                                @endforeach
											</select>
										</div>
									</div>
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
								<button type="button" class="header__cart cart" @if ($cart_count != 0)onclick="location.href='{{ route('cart.index') }}'"@endif>
									<span class="cart__icon">
										<img src="{{ asset("$design/images/icons/cart_blue.svg") }}" width="24" height="24">
                                        @if ($cart_count != 0)
                                            <span class="cart__quantity">{{$cart_count}}</span>
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
                    <div class="top-phones-header__item request" style="pointer-events: none; font-weight: 600"><a class="request_call">{{ __('text.common_callback') }}</a><div class="request_text">{{__('text.common_call_us_top')}}</div></div>
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

        <p class="footer_copyright">
            {{__('text.license_text_license1_1')}} {{Request::getHost()}} {{__('text.license_text_license1_2')}}
            {{__('text.license_text_license2_d7')}}
        </p>

<script src="{{ asset("$design/js/app.js") }}"></script>
<script src="{{ asset("$design/js/main.js") }}"></script>
<script src="{{ asset("/js/all_js.js") }}"></script>

</footer>

<input hidden id="stattemp" value="{$data.web_statistic.params_string}">

</body>

</html>