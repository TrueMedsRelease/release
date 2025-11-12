<!DOCTYPE html>
<html
@if (session('locale', 'en') == 'arb')
    lang="ar"
@elseif (session('locale', 'en') == 'gr')
    lang="el"
@elseif (session('locale', 'en') == 'hans')
    lang="zh-Hans"
@elseif (session('locale', 'en') == 'hant')
    lang="zh-Hant"
@elseif (session('locale', 'en') == 'no')
    lang="nb"
@else
    lang="{{ session('locale', 'en') }}"
@endif
>

<head>
	<title>{{__('text.login_title')}}</title>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">

    @php
        if (!function_exists('asset_ver')) {
            function asset_ver(string $path): string {
                static $mtimes = [];
                $full = public_path($path);
                if (!isset($mtimes[$path])) {
                    $mtimes[$path] = is_file($full) ? filemtime($full) : null;
                }
                $url = asset($path);
                $v = $mtimes[$path] ?? time();
                return $url . '?v=' . $v;
            }
        }
    @endphp

	<link rel="stylesheet" href="style_login/style_.css">
	<link rel="shortcut icon" href="style_login/favicon.ico">

    <script>
        const routeSearchAutocomplete = "{{ route('search.search_autocomplete') }}";
        const routeCartContent = "{{ route('cart.content') }}";
        const routeCheckCode = "{{ route('home.check_code') }}";
        const routeRequestLogin = "{{ route('home.request_login') }}";
    </script>

    <script defer src="{{ asset("vendor/jquery/jquery-3.6.3.min.js") }}"></script>
    <script defer src="{{ asset_ver("vendor/jquery/autocomplete.js") }}"></script>
    <script defer src="{{ asset("vendor/jquery/init.js") }}"></script>
    <script defer type="text/javascript" src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div id="preloader">
        <div class="spinner"></div>
    </div>
	<div class="wrapper wrapper--login">
		<div class="login">
			<div class="login__body">
				<div class="login__inner">
					<header class="login-header">
						<a href="" class="login-header__logo"><img loading="lazy" src="style_login/icons/logo-white.svg" alt=""></a>
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
											<img loading="lazy" id="captcha_image_log" src="{{ captcha_src() }}">
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
                            @foreach ($phone_arr as $id_phone => $phones)
                                <li class="login-footer__phone"><a href="tel:{{__('text.phones_title_phone_' . $id_phone)}}">{{__('text.phones_title_phone_' . $id_phone . '_code')}}{{__('text.phones_title_phone_' . $id_phone)}}</a></li>
                            @endforeach
                        </ul>
					</footer>
				</div>
			</div>
			<div class="login__image">
				<picture>
					<source srcset="style_login/icons/hero.webp" type="image/webp">
					<img loading="lazy" src="style_login/icons/hero.png" width="1388" height="1080" alt="Awesome image">
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
<script defer>

    const email_invalid_text = "{{__('text.login_email_invalid')}}";
    const code_invalid_text = "{{__('text.login_code_invalid')}}";

    window.addEventListener('load', function () {
        var preloader = document.getElementById('preloader');
        preloader.style.display = 'none';
    });
</script>
<script defer src="{{ asset_ver("js/all_js.js") }}"></script>
</body>

</html>

