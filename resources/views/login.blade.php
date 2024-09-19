<!DOCTYPE html>
<html lang="en">

<head>
	<title>{{__('text.login_title')}}</title>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="style_login/style_.css">
	<link rel="shortcut icon" href="style_login/favicon.ico">

    <script src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script src="{{ asset("vendor/jquery/autocomplete.js") }}"></script>
    <script src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.js"></script>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>


<body>
    <div id="preloader">
        <div class="spinner"></div>
    </div>
	<div class="wrapper wrapper--login">
		<div class="login">
			<div class="login__body">
				<div class="login__inner">
					<header class="login-header">
						<a href="" class="login-header__logo"><img src="style_login/icons/logo-white.svg" alt=""></a>
					</header>
					<main class="login-content">
						<h1 class="login-content__title">{{__('text.login_login')}}</h1>
						<div name='loginform' id="frm_login" class="login-content__form form" method="POST" action="">
							<div class="form__rows">
								<div class="form__row">
									<label for="email" class="form__label">{{__('text.login_email')}}</label>
									<div class="form__input">
										<input type="text" name="form[email]" id="email" data-error="Not correct" class="input">
									</div>
								</div>
								<div class="form__row" id="email_error">
									<p style="color: #ff6666; font-weight: bold; width: max-content; padding: 5px;" class="input">{{__('text.login_email_empty')}}</p>
								</div>
								<div class="form__row">
									<label for="code" class="form__label">{{__('text.login_code')}}</label>
									<div class="form__input form__input--has-image">
										<input id="code" autocomplete="off" type="text" name="form[code]" id="code" data-error="Not correct" class="input">
										<picture>
											<source srcset="{{ captcha_src() }}" type="image/webp">
											<img src="{{ captcha_src() }}">
										</picture>
									</div>
								</div>
								<div class="form__row" id="captcha_error">
									<p style="color: #ff6666; font-weight: bold; width: max-content; padding: 5px;" class="input">{{__('text.login_code_empty')}}</p>
								</div>
							</div>
							<button name="enter" id="enter" type="submit" onclick="enterProfile()" class="form__button button button--dark">
								<span>{{__('text.login_enter')}}</span>
								<svg width="18" height="18">
									<use xlink:href="style_login/icons/icons.svg#svg-arr-right"></use>
								</svg>
							</button>
						</div>
					</main>
					<footer class="login-footer">
                        <ul class="login-footer__phones">
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}{{__('text.phones_title_phone_1')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}{{__('text.phones_title_phone_2')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}{{__('text.phones_title_phone_3')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}{{__('text.phones_title_phone_4')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}{{__('text.phones_title_phone_5')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}{{__('text.phones_title_phone_6')}}</a></li>
                            <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}{{__('text.phones_title_phone_7')}}</a></li>
                        </ul>
					</footer>
				</div>
			</div>
			<div class="login__image">
				<picture>
					<source srcset="style_login/icons/hero.webp" type="image/webp">
					<img src="style_login/icons/hero.png" width="1388" height="1080" alt="Awesome image">
				</picture>
			</div>
		</div>
	</div>
	{{-- <div id="popup" aria-hidden="true" class="popup">
	    <div class="popup__wrapper">
		    <div class="popup__content">
                <button data-close type="button" class="popup__close">
                    <svg width="20" height="20">
                        <use xlink:href="{$path.image}/icons/icons.svg#svg-close"></use>
                    </svg>
                </button>
                <div class="popup__text">
                    Text
                </div>
            </div>
		</div>
	</div> --}}
</div>
<script>

    const email_invalid_text = "{{__('text.login_email_invalid')}}";
    const code_invalid_text = "{{__('text.login_code_invalid')}}";

    window.addEventListener('load', function () {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>
<script src="js/all_js.js"></script>
</body>

</html>

