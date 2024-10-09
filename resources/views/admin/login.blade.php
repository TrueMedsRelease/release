@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', __('text.admin_login_title'))

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
						<div class="popup__input">
							<input type="text" id="captcha_field" name="captcha_field" placeholder="code" size="6" maxlength="20" class="input" autocomplete="off"/>
						</div>
						<div id="captcha_error">
                            {{__('text.admin_common_form_empty_field')}}
                        </div>
					</div>
					<div class="popup__row">
						<div>{{__('text.admin_login_form_password')}}</div>
						<div class="popup__input log_pass">
							<input autocomplete="off" type="password" name="password_field" id="password_field" data-error="Invalid password" placeholder="password" class="input">
						</div>
						<div id="password_error">
                            {{__('text.admin_common_form_empty_field')}}
						</div>
					</div>
					<div id="login_messages"></div>
					<button name="enter" type="button" onclick="show_loading_message('login_messages', '{{__('text.admin_common_loading_message')}}'); logIn();" class=" jqTransformButton jqTransformButton_hover popup__button popup__button--login button button--filled">
						<span>{{__('text.admin_login_form_submit')}}</span>
						<svg width="15" height="15">
							<use xlink:href="/admin_style/images/icons/icons.svg#svg-arr-top-right"></use>
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

<input type="hidden" id='captcha_invalid' value="{{__('text.admin_common_form_wrong_captcha_value')}}"

@endsection