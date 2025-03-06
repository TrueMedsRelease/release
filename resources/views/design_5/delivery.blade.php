
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="christmas" style="display: none" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("/pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div class="text-page mb50">
	<h2 class="title-page">{{__('text.shipping_title')}}</h2>
	<h3>{{__('text.shipping_title1')}}</h3>
	<div class="shipping-info">
		<div class="item">
			<img loading="lazy" src="{{ asset("$design/images/icon/ico-info-05.svg") }}" alt="">
			<div class="text">
				<p>{!!__('text.shipping_text_2')!!}</p>
			</div>
		</div>
		<div class="item">
			<img loading="lazy" src="{{ asset("$design/images/icon/ico-info-02.svg") }}" alt="">
			<div class="text">
				<p>{!!__('text.shipping_text_1')!!}</p>
			</div>
		</div>
	</div>
	<p>{{__('text.shipping_text_3')}}</p>
	<p>{{__('text.shipping_title2')}}</p>
	<p>{{__('text.shipping_text_4')}}</p>
	<p>{{__('text.shipping_text_5')}}</p>

	<ul class="mb20">
		<li>{{__('text.shipping_text_6')}}</li>
		<li>{{__('text.shipping_text_7')}}</li>
		<li>{{__('text.shipping_text_8')}}</li>
		<li>{{__('text.shipping_text_9')}}</li>
	</ul>
	<p>{{__('text.shipping_text_10')}}<a href="{{ route('home.contact_us') }}">{{__('text.shipping_contact_us_shipping')}}</a></p>
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