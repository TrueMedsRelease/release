<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="index, follow" />
    <link rel="stylesheet" type="text/css" href="{{ asset('style_checkout/style.css') }}">
	<link rel="shortcut icon" href="{{ asset('style_checkout/favicon.ico') }}">
    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <title>{{__('text.checkout_title')}}</title>
    {!! isset($pixel) ? $pixel : '' !!}
</head>
<body>
    <div class="preloader">
		<div class="preloader__row">
			<div class="preloader__item"></div>
			<div class="preloader__item"></div>
		</div>
	</div>
	<div class="ploader">
		<div class="ploader__row">
			<div class="ploader__item"></div>
			<div class="ploader__item"></div>
			<div class="ploader__item"></div>
			<div class="ploader__item"></div>
			<div class="ploader__item"></div>
		</div>
	</div>
    <div class="wrapper">
        <header class="header">
            {{-- <div class="christmas">
                <img src="../style_checkout/images/pay_big.png">
            </div> --}}
            <div class="header__phones-top top-phones-header">
                <div class="top-phones-header__container header__container">
                    <div class="top-phones-header__items">
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}
                            {{__('text.phones_title_phone_1')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}
                            {{__('text.phones_title_phone_2')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}
                            {{__('text.phones_title_phone_3')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}
                            {{__('text.phones_title_phone_4')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}
                            {{__('text.phones_title_phone_5')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}
                            {{__('text.phones_title_phone_6')}}</a>
                        <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}
                            {{__('text.phones_title_phone_7')}}</a>
                    </div>
                </div>
            </div>
            <div class="header__content">
                <div class="header__container">
                    <div class="header__top">
                        <a class="header__logo"><img src="{{ asset('style_checkout/images/logo.svg') }}" alt=""></a>
                        <div class="header__selects">
                            <div class="header__select currency">
                                <h2 class="header__caption">{{__('text.checkout_currency')}}</h2>
                                <select name="form[]" id="currency_select" class="form"
                                    onclick="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="{{ url()->current() }}/curr={{ $item['code'] }}"
                                            @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="header__select header__select--language language">
                                <h2 class="header__caption">{{__('text.checkout_language')}}</h2>
                                <select name="form[]" id="language_select" class="form"
                                    onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="{{ url()->current() }}/lang={{ $item['code'] }}"
                                            @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="header__bottom">
                        <div class="header__inner">
                            <a href="{{ route('cart.index') }}" class="header__link-back" id="back_to_cart">
                                <svg width="18" height="18">
                                    <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-right"></use>
                                </svg>
                                <span>{{__('text.checkout_back')}}</span>
                            </a>
                            <div class="header__partners">
                                <div class="header__partner">
                                    <img src="{{ asset('style_checkout/images/partners/geotrust.svg') }}" width="90"
                                        height="30" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img src="{{ asset('style_checkout/images/partners/norton.svg') }}" width="70"
                                        height="40" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img src="{{ asset('style_checkout/images/partners/comodo.svg') }}" width="90"
                                        height="30" alt="Awesome image">
                                </div>
                                <div class="header__partner">
                                    <img src="{{ asset('style_checkout/images/partners/mcafee.svg') }}" width="80"
                                        height="25" alt="Awesome image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="wrapper_new">
        </div>
	</div>
    <script>
        $(document).ready(function() {
        $(".ploader").hide();
            $.ajax({
            method: 'GET',
            data: { },
                url: "{{ route('checkout.content') }}",
                dataType: 'html',
                success : function(data) {
                    data = JSON.parse(data);
                    $('.wrapper_new').html(data.html);
                    document.body.classList.add('loaded_hiding');
                    window.setTimeout(function () {
                        document.body.classList.add('loaded');
                        document.body.classList.remove('loaded_hiding');
                    }, 500);
                }
            });
        });
    </script>
    <div id="insur_popup">
		<div class="popup_block_insur">
			<button type="button" class="close_popup">
				<svg width="20" height="20">
					<use xlink:href="/style_checkout/images/icons/icons.svg#svg-close"></use>
				</svg>
			</button>
			<h3 class="popup_head">{{__('text.checkout_notice')}}</h3>
			<div class="popup_text">
				<p>{{__('text.checkout_insurance_popup')}}</p>
			</div>
			<button id="change_insur">
				{{__('text.checkout_ok')}}
			</button>
		</div>
	</div>
</body>
</html>