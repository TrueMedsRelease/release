
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<input type="hidden" id="error_subject" value="{{ $error_subject }}">
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
                <label for="subject" class="contact-form__label">{{__('text.contact_us_subject')}}</label>
                <div class="form__field custom-field" id="contact-subject" name="contact-subject">
                    <div id="subject_block">
                        <div class="contact_subject">
                            <div id="new_subject_block">
                                <div class="select_subject">
                                    <div class="select_header_subject">
                                        <span class="select_current_subject" curr_subject_id = "{{ $default_subject }}">{{ $subjects[$default_subject] }}</span>
                                        <div class="select_icon">
                                            <img src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
                                        </div>
                                    </div>
                                    <div class="select_body_subjects">
                                        @foreach ($subjects as $id => $subject)
                                            <div class="select_item_subject" subject_id = "{{ $id }}">{{ $subject }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="contact-form__line">
				<label for="name" class="contact-form__label">{{__('text.contact_us_name')}}</label>
				<div class="contact-form__input">
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
				<label for="message" class="contact-form__label">{{__('text.contact_us_message')}}</label>
				<div class="contact-form__input">
					<textarea id="message" name="form[message]" type="text" placeholder="{{__('text.contact_us_message')}}" class="input"></textarea>
				</div>
			</div>
			<div class="contact-form__line">
				<label for="captcha" class="contact-form__label">{{__('text.contact_us_code')}}</label>
				<div class="contact-form__captcha">
					<picture>
						<img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 15px;">
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