@extends('admin.layouts.main')

@section('content')
<div class="popup login popup_show">
	<div class="popup__wrapper">
		<div class="popup__content">
			<form name='login_form' id="login_form" class="popup__form" method="POST">
				<div class="popup__row">
					<picture>
					<img id="captcha_image" name="captcha" src="{{ captcha_src() }}" alt="">
					</picture>
						<div></div>
						<div class="popup__input log_login">
							<input type="text" id="captcha_field" name="captcha_field" placeholder="code" size="6" maxlength="20" class="input"/>
						</div>
						<div id="captcha_error"></div>
					</div>
					<div class="popup__row">
						<div>{{__('text.admin_login_form_password')}}</div>
						<div class="popup__input log_pass">
							<input autocomplete="off" type="password" name="password_field" id="password_field" data-error="Invalid password" placeholder="password" class="input">
						</div>
						<div id="password_error">
						</div>
					</div>
					<div id="login_messages"></div>
					<button name="enter" type="button" onclick="show_loading_message('login_messages', '{#loading_message#}'); xajax_login(xajax.getFormValues('login_form'));" class=" jqTransformButton jqTransformButton_hover popup__button popup__button--login button button--filled">
						<span>{{__('text.admin_login_form_submit')}}</span>
						<svg width="15" height="15">
							<use xlink:href="/admin/images/icons/icons.svg#svg-arr-top-right"></use>
						</svg>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
    $(document).ready( function() {
        document.getElementById("captcha_field").style.width = "226px";
    });
</script>

@endsection