
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')

<h1 class="content__title title" id="scroll">{{__('text.affiliate_title')}}</h1>
<div class="message_sended hidden">
	<h2>{{__('text.affiliate_thanks')}}</h2>
	<br>
	<p>{{__('text.affiliate_sended')}}</p>
</div>
<div class="contact-form">
	<form class="contact-form__body form" method="POST">
		<div class="contact-form__lines">
			<div class="contact-form__line">
				<label for="name" class="contact-form__label">{{__('text.affiliate_name')}}</label>
				<div class="contact-form__input">
					<!-- Для показа ошибки и изенения цвета текст для input на красный, необходимо добавить класс error -->
					<input id="name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.affiliate_name')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="email" class="contact-form__label">{{__('text.affiliate_email')}}</label>
				<div class="contact-form__input">
					<!-- Для показа ошибки и изенения цвета текст для input на красный, необходимо добавить класс error -->
					<input id="email" autocomplete="off" type="text" name="form[email]" data-error="" placeholder="{{__('text.affiliate_email')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="jabber" class="contact-form__label">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
				<div class="contact-form__input">
					<input id="jabber" autocomplete="off" type="text" name="form[jabber]" data-error="" placeholder="{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}" class="input">
				</div>
			</div>
			<div class="contact-form__line">
				<label for="message" class="contact-form__label">{{__('text.affiliate_message')}}</label>
				<div class="contact-form__input">
					<textarea id="message" name="form[message]" placeholder="{{__('text.affiliate_message')}}" class="input"></textarea>
				</div>
			</div>
			<div class="contact-form__line">
				<label for="captcha" class="contact-form__label">{{__('text.affiliate_code')}}</label>
				<div class="contact-form__captcha">
					<picture>
            		    <source srcset="{{ captcha_src() }}" type="image/webp">
            		    <img src="{{ captcha_src() }}">
            		</picture>
				</div>
				<div class="contact-form__input">
					<input id="captcha" autocomplete="off" type="text" name="form[captcha]" data-error="Ошибка" placeholder="{{__('text.affiliate_code')}}" class="input">
				</div>
			</div>
		</div>
		<button onclick="sendAjaxAffiliate()" id = "message_send_button" type="button" class="contact-form__button button button--filled">{{__('text.affiliate_send')}}</button>
	</form>
	<div class="contact-form__descr">
		<p>{{__('text.affiliate_contact_message')}}</p>
	</div>
</div>

@endsection