
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="checkup" onclick="location.href='{{ route('home.checkup') }}'">
    <img loading="lazy" src="{{ asset("pub_images/checkup_img/white/checkup_big.png") }}">
</div>

<div class="text-page">
	<h2 class="title-page">{{__('text.bonus_ref_menu')}}</h2>
    <h3>{{__('text.bonus_page_bonus')}}</h3>
    <br>
    <p>{{ __('text.bonus_page_text1') }}</p>
    <p>{{ __('text.bonus_page_text2') }}</p>
    <ul class="mb20">
        {{ __('text.bonus_page_text3') }}
        <li>{{ __('text.bonus_page_text4') }}</li>
        <li>{{ __('text.bonus_page_text5') }}</li>
        <li>{{ __('text.bonus_page_text6') }}</li>
    </ul>
    <p>{!! __('text.bonus_page_text7') !!}</p>
    <p>{!! __('text.bonus_page_text8') !!}</p>
    <p><b>{{ __('text.bonus_page_text9') }}</b></p>

    <h3>{{__('text.bonus_page_referral')}}</h3>
    <br>
    <p>{!! __('text.bonus_page_text10') !!}</p>
    <p>{{ __('text.bonus_page_text11') }}</p>
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