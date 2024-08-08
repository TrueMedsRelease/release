<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Complete</title>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no">
    <!-- <style>body{opacity: 0;}</style> -->
    <link rel="stylesheet" href="{{ asset('style_checkout/style.css') }}?v=24042024">
    <link rel="shortcut icon" href="{{ asset('style_checkout/favicon.ico') }}">
    <script src="{{ asset('vendor/jquery/jquery-3.6.3.min.js') }}"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="header__phones-top top-phones-header">
                <div class="top-phones-header__container header__container">
                    <div class="top-phones-header__items">
                        <a class="top-phones-header__item" href="tel:{#title_phone_1#}">{#title_phone_1_code#}
                            {#title_phone_1#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_2#}">{#title_phone_2_code#}
                            {#title_phone_2#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_3#}">{#title_phone_3_code#}
                            {#title_phone_3#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_4#}">{#title_phone_4_code#}
                            {#title_phone_4#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_5#}">{#title_phone_5_code#}
                            {#title_phone_5#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_6#}">{#title_phone_6_code#}
                            {#title_phone_6#}</a>
                        <a class="top-phones-header__item" href="tel:{#title_phone_7#}">{#title_phone_7_code#}
                            {#title_phone_7#}</a>
                    </div>
                </div>
            </div>
            <div class="header__content">
                <div class="header__container">
                    <div class="header__top">
                        <a class="header__logo"><img src="../style_checkout/images/logo.svg" alt=""></a>
                        <div class="header__selects">
                            <div class="currency header__select">
                                <h2 class="header__caption">Currency</h2>
                                <select name="form[]" id="currency_select" class="form"
                                    onclick="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Currency::GetAllCurrency() as $item)
                                        <option value="/curr={{ $item['code'] }}"
                                            @if (session('currency') == $item['code']) selected @endif>
                                            {{ Str::upper($item['code']) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="header__select header__select--language language">
                                <h2 class="header__caption">Language</h2>
                                <select name="form[]" id="language_select" class="form"
                                    onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                                    @foreach ($Language::GetAllLanuages() as $item)
                                        <option value="/lang={{ $item['code'] }}"
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
                                    <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-right"></use>
                                </svg>
                                <span>Back to Shopping cart</span>
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
        <main class="succes">
            <div class="succes__container">
                <h1 class="succes__title">COMPLETE!</h1>
                <h2 class="succes__subtitle">CONGRATULATION,
                    {{ session('form.firstname') . ' ' . session('form.lastname') }}! EXCELLENT CHOICE!</h2>
                <div class="succes__block">
                    <b>Your order number is:<span> {{ session('order.order_id') }}</span></b>
                </div>
                <div class="succes__block succes__block--transparent">
                    We will charge your card - {{ $Currency::convert(session('total.all')) }}, <br>
                    please check that the amount on the card is sufficient for the transaction.
                </div>
                {{-- {if $data.success_info.gift_card}
					<div class="gift_block">
						{foreach from=$data.success_info.gift_card item=card}
							<div class="gift">
								<div class="gift_text">{#gift_card#} {$card->price_text}</div>
								<img src="../style_checkout/images/gift_card_img.svg" class="gift_img">
								<div class="gift_code">
									<div class="code_text" id="code_text">{$card->code}</div>
									<img src="../style_checkout/images/icons/copy.png" id="copy_img">
								</div>
							</div>
						{/foreach}
					</div>
				{/if} --}}
                <div class="succes__block">
                    <p><b>You need confirm your order by phone:</b></p>
                    <div class="succes__row">
                        <a style="cursor: pointer;" class="succes__button button" id="succes__button">
                            <svg width="18" height="18">
                                <use xlink:class="succes__link"
                                    href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-phone"></use>
                            </svg>
                            <span>Call me now</span>
                        </a>
                        <p id = "thank" hidden>
                            Thank you. Please wait, our support will call you shortly.
                        </p>
                        <p class="succes__phones">
                            <span>or you can call to us:</span>
                            <a class="succes__link"
                                href="tel:{#title_phone_1#}">{#title_phone_1_code#}{#title_phone_1#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_2#}">{#title_phone_2_code#}{#title_phone_2#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_3#}">{#title_phone_3_code#}{#title_phone_3#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_4#}">{#title_phone_4_code#}{#title_phone_4#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_5#}">{#title_phone_5_code#}{#title_phone_5#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_6#}">{#title_phone_6_code#}{#title_phone_6#}</a> <a
                                class="succes__link"
                                href="tel:{#title_phone_7#}">{#title_phone_7_code#}{#title_phone_7#}</a>
                        </p>
                    </div>
                </div>
                <div class="succes__block">
                    <b>Support site for check order status:</b> <a class="succes__link"
                        href="https://true-client-support.com">true-client-support.com</a>
                </div>
                <div class="succes__block">
                    <b>Our email: </b> <a class="succes__link"
                        href="mailto:support@true-client-support.com">support@true-client-support.com</a>
                </div>
                <div class="succes__last-words">
                    <p>Receive orders quickly</p>
                    <p>You should also receive an SMS notification with the current status of your order.</p>
                    <p>If you need any help, please call our support team, or send us an email.</p>
                    <p>Thank you for choosing us!</p>
                </div>
            </div>
        </main>
        <footer class="footer">
            <div class="footer__container">
                <p class="footer__text">Copyright © 1999-2024 All Rights Reserved. TrueSecurePayments Ltd.</p>
            </div>
        </footer>
    </div>
    <!-- <div id="popup" aria-hidden="true" class="popup">
 <div class="popup__wrapper">
  <div class="popup__content">
   <button data-close type="button" class="popup__close">
    <svg width="20" height="20">
     <use xlink:href="../style_checkout/images/icons/icons.svg#svg-close"></use>
    </svg>
   </button>
   <div class="popup__text">
    Text
   </div>
  </div>
 </div>
</div> -->
    <script src="{{ asset('style_checkout/js/app_success.js') }}"></script>

    {literal}
    <script>
        $(".succes__button").click(function() {
            document.getElementById("succes__button").style.display = 'none';
            document.getElementById("thank").hidden = false;
        });
    </script>
    {/literal}
</body>

</html>
