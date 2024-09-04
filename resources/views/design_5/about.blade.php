
@extends($design . '.layouts.main')

@section('title', __('text.about_us_title'))

@section('content')

<div class="text-page">
	<h2 class="title-page">{{__('text.about_us_title')}}</h2>
	{!!__('text.about_us_text')!!}
	<h3>{{__('text.about_us_title1')}}</h3>
	<p>{{__('text.about_us_text1')}}</p>
	<h3>{{__('text.about_us_title2')}}</h3>
	<p class="m0">{{__('text.about_us_text2_1')}}</p>
	<ul class="mb20">
		<li>{{__('text.about_us_text2_2')}}</li>
		<li>{{__('text.about_us_text2_3')}}</li>
	</ul>
	<p class="m0">{{__('text.about_us_text2_4')}}</p>
	<ul class="mb20">
		<li>{{__('text.about_us_text2_5')}}</li>
		<li>{{__('text.about_us_text2_6')}}</li>
	</ul>
	<h3>{{__('text.about_us_title3')}}</h3>
	<p>{{__('text.about_us_text3_1')}}</p>
	<p>{{__('text.about_us_text3_2')}}</p>
	<p class="m0">{{__('text.about_us_text3_3')}}</p>
	<ul class="mb20">
		<li>{{__('text.about_us_text3_4')}}</li>
		<li>{{__('text.about_us_text3_5')}}</li>
	</ul>
	<p>{{__('text.about_us_text3_6')}}</p>
	<h3>{{__('text.about_us_title4')}}</h3>
	<p>{{__('text.about_us_text4')}}</p>
</div>

<div class="sale-banners">
    <div class="happy-sale item">
        <span class="img">
            <img src="{{ asset("$design/images/icon/ico-banner-01.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner1_text1')}} <br>{{__('text.common_banner1_text2')}}</span>
            <span class="text">{{__('text.common_banner1_text3')}} <br> {{__('text.common_banner1_text4')}}</span>
        </span>
    </div>
    <div class="wow-sale item">
        <span class="img">
            <img src="{{ asset("$design/images/icon/ico-banner-02.svg") }}" alt="">
        </span>
        <span class="info">
            <span class="title">{{__('text.common_banner2_text1')}} <br> {!!__('text.common_banner2_text2')!!}</span>
            <span class="text">{{__('text.common_banner2_text3')}} <br> {{__('text.common_banner2_text4')}}</span>
        </span>
    </div>
</div>

@endsection