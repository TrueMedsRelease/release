@extends('admin.layouts.main')

@section('title', $title)
{{-- @section('page_name', '') --}}

@section('style_title', 'text-align: center;')
@section('page_name')
    <img loading="lazy" src="{{ asset("admin_style/images/logo_truemeds.png") }}">
@endsection

@section('content')
<div class="popup login popup_show">
	<div class="popup__wrapper">
		<div class="popup__content">
            <h2 class="popup__title">Login</h2>
			<form name='login_form' id="login_form" class="popup__form" method="POST">
                <div class="popup__row">
                    <div class="popup__input" style="width: 100%">
                        <input autocomplete="off" type="password" name="password_field" id="password_field" data-error="Invalid password" placeholder="Password" class="input">
                    </div>
                    <div id="password_error">
                        {{__('text.admin_common_form_empty_field')}}
                    </div>
                </div>
                <div class="popup__row">
					<picture>
					    <img loading="lazy" id="captcha_image" name="captcha" src="{{ captcha_src() }}" alt="">
					</picture>
                    <div class="popup__input" style="width: 100%">
                        <input type="text" id="captcha_field" name="captcha_field" placeholder="Captcha" size="6" maxlength="20" class="input" autocomplete="off"/>
                    </div>
                    <div id="captcha_error">
                        {{__('text.admin_common_form_empty_field')}}
                    </div>
                </div>
                <div id="login_messages"></div>
                <button name="enter" type="button" onclick="show_loading_message('login_messages', '{{__('text.admin_common_loading_message')}}'); logIn();" class=" jqTransformButton jqTransformButton_hover popup__button popup__button--login button button--filled">
                    <span>{{__('text.admin_login_form_button')}}</span>
                    <svg width="15" height="15">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-arr-top-right") }}"></use>
                    </svg>
                </button>
			</form>
		</div>
	</div>
</div>

<input type="hidden" id='captcha_invalid' value="{{__('text.admin_common_form_wrong_captcha_value')}}"

@endsection