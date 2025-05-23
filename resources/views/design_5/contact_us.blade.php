
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<input type="hidden" id="error_subject" value="{{ $error_subject }}">
<div class="text-page mb50" id="scroll">
	<h2 class="title-page">{{__('text.contact_us_title')}}</h2>

	<div class="message_sended hidden">
		<h2>{{__('text.contact_us_thanks')}}</h2>
		<br>
		<p>{{__('text.contact_us_sended')}}</p>
	</div>
	<div class="contact-form">
		<form id="message_send_form" method="post">
			<div class="line">
				<div class="input-row" data-col="3">
                    <div class="input-box">
                        <label for="subject">{{__('text.contact_us_subject')}}</label>
                        <div class="form__field custom-field" id="subject" name="subject">
                            <div id="subject_block">
                                <div class="contact_subject">
                                    <div id="new_subject_block">
                                        <div class="select_subject">
                                            <div class="select_header_subject">
                                                <span class="select_current_subject" curr_subject_id = "{{ $default_subject }}">{{ $subjects[$default_subject] }}</span>
                                                <div class="select_icon">
                                                    <img src="{{ asset("$design/images/icon/arrow_down_black.svg") }}">
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
					<div class="input-box">
						<label for="name">{{__('text.contact_us_name')}}</label>
						<input type="text" autocomplete="off" placeholder="{{__('text.contact_us_name')}}" name="name" id="name" onkeyup="undisabled('contact_us')">
					</div>
					<div class="input-box">
						<label for="email">{{__('text.contact_us_email')}}</label>
						<input type="email" autocomplete="off" placeholder="{{__('text.contact_us_email')}}" name="email" id="email" onkeyup="undisabled('contact_us')">
					</div>
				</div>
			</div>
			<div class="line">
				<div class="input-row" data-col="1">
					<div class="input-box">
						<label for="message">{{__('text.contact_us_message')}}</label>
						<div class="textarea-control">
							<textarea name="message" id="message" autocomplete="off" cols="30" rows="10"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="line">
				<div class="input-row" data-col="1">
					<div class="input-box">
						<label for="captcha">{{__('text.contact_us_code')}}</label>
						<div class="code-holder">
							<input type="text" autocomplete="off" placeholder="{{__('text.contact_us_code')}}" name="captcha" id="captcha" onkeyup="undisabled('contact_us')">
							<div class="img">
								<picture>
									<img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 10px;">
								</picture>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="btn btn-primary" id="message_send_button" disabled="disabled">{{__('text.contact_us_send')}}</div>
		</form>
	</div>
	<div class="text-bottom-desc">
		<p>{{__('text.contact_us_describe1')}}</p>
        <p>{{__('text.contact_us_describe2')}}</p>
        <p>{{__('text.contact_us_describe3')}}</p>
	</div>
</div>

<div class="sale-banners">
    <div class="happy-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
            <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
        </span>
    </div>
    <div class="wow-sale item">
        <span class="img">
            <img loading="lazy" src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner2_text1')}} <br> {!!__('text.common_banner2_text2')!!}</span>
            <span class="text">{{__('text.common_banner2_text3')}} <br> {{__('text.common_banner2_text4')}}</span>
        </span>
    </div>
</div>

@endsection