
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')


<div class="text-page mb50" id="scroll">
	<h2 class="title-page">{{__('text.affiliate_title')}}</h2>

	<div class="message_sended hidden">
		<h2>{{__('text.affiliate_thanks')}}</h2>
		<br>
		<p>{{__('text.affiliate_sended')}}</p>
	</div>
	<div class="contact-form">
		<form id="message_send_form" method="post">
			<div class="line">
				<div class="input-row" data-col="3">
					<div class="input-box">
						<label for="name">{{__('text.affiliate_name')}}</label>
						<input type="text" autocomplete="off" placeholder="{{__('text.affiliate_name')}}" name="name" id="name" onkeyup="undisabled('affiliate')">
					</div>
					<div class="input-box">
						<label for="email">{{__('text.affiliate_email')}}</label>
						<input type="email" autocomplete="off" placeholder="{{__('text.affiliate_email')}}" name="email" id="email" onkeyup="undisabled('affiliate')">
					</div>
					<div class="input-box">
						<label for="jabber">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
						<input type="text" autocomplete="off" placeholder="{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}" name="jabber" id="jabber">
					</div>
				</div>
			</div>
			<div class="line">
				<div class="input-row" data-col="1">
					<div class="input-box">
						<label for="message">{{__('text.affiliate_message')}}</label>
						<div class="textarea-control">
							<textarea name="message" id="message" autocomplete="off" cols="30" rows="10"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="line">
				<div class="input-row" data-col="1">
					<div class="input-box">
						<label for="captcha">{{__('text.affiliate_code')}}</label>
						<div class="code-holder">
							<input type="text" autocomplete="off" placeholder="{{__('text.affiliate_code')}}" name="captcha" id="captcha" onkeyup="undisabled('affiliate')">
							<div class="img">
								<picture>
									<img loading="lazy" id="captcha_image" src="{{ captcha_src() }}" style="border-radius: 10px;">
								</picture>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="btn btn-primary" id="affiliate_send_button" disabled="disabled">{{__('text.affiliate_send')}}</div>
		</form>
	</div>
	<div class="text-bottom-desc">
		<p>{{__('text.affiliate_contact_message_2')}}</p>
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