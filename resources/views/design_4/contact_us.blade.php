
@extends($design . '.layouts.main')

@section('title', __('text.contact_us_title'))

@section('content')

<h1 class="content__title title" id="scroll">{{__('text.contact_us_title')}}</h1>
<div class="message_sended hidden">
	<h2>{{__('text.contact_us_thanks')}}</h2>
	<br>
	<p>{{__('text.contact_us_sended')}}</p>
</div>
<div class="contact-form">
	<form class="contact-form__body form" id = "message_send_form" method="post">
		<div class="contact-form__lines">
			<div class="contact-form__line">
				<label for="name" class="contact-form__label">{{__('text.contact_us_name')}}</label>
				<div class="contact-form__input">
					<!-- Для показа ошибки и изенения цвета текст для input на красный, необходимо добавить класс error -->
					<input id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="email" class="contact-form__label">{{__('text.contact_us_email')}}</label>
				<div class="contact-form__input">
					<input id="email" autocomplete="off" type="text" name="form[email]" data-error="" placeholder="{{__('text.contact_us_email')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="subject" class="contact-form__label">{{__('text.contact_us_subject')}}</label>
				<div class="contact-form__input">
					<input id="subject" autocomplete="off" type="text" name="form[subject]" data-error="" placeholder="{{__('text.contact_us_subject')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="message" class="contact-form__label">{{__('text.contact_us_message')}}</label>
				<div class="contact-form__input">
					<textarea id="message" name="form[message]" type="text" placeholder="{{__('text.contact_us_message')}}" class="input"></textarea>
				</div>
			</div>
			<div class="contact-form__line">
				<label for="captcha" class="contact-form__label">{{__('text.contact_us_code')}}</label>
				<div class="contact-form__captcha">
					<picture>
						<source srcset="/captcha" type="image/webp">
						<img src="/captcha">
					</picture>
				</div>
				<div class="contact-form__input">
					<input id="captcha" autocomplete="off" type="text" name="form[captcha]" data-error="" placeholder="{{__('text.contact_us_code')}}" class="input">
				</div>
			</div>
		</div>
		<button type="button" onclick="sendAjaxContact()" name="form[submit]" class="contact-form__button button button--filled" id = "message_send_button">{{__('text.contact_us_send')}}</button>
	</form>
	<div class="contact-form__descr">
		<p>{{__('text.contact_us_describe1')}}</p>
        <p>{{__('text.contact_us_describe2')}}</p>
        <p>{{__('text.contact_us_describe3')}}</p>
	</div>
</div>

@endsection