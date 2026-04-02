
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<h1 class="content__title title" id="scroll">{{__('text.bonus_ref_menu')}}</h1>
<div class="text-page">
    <div class="text-page__block">
    	<h2 class="text-page__label">{{__('text.bonus_page_bonus')}}</h2>
    	<p>{{__('text.about_us_text1')}}</p>
        <p>{{ __('text.bonus_page_text2') }}</p>
        <ul class="text-page__list">
            {{ __('text.bonus_page_text3') }}
            <li>{{ __('text.bonus_page_text4') }}</li>
            <li>{{ __('text.bonus_page_text5') }}</li>
            <li>{{ __('text.bonus_page_text6') }}</li>
        </ul>
        <p>{!! __('text.bonus_page_text7') !!}</p>
        <p>{!! __('text.bonus_page_text8') !!}</p>
        <p><b>{{ __('text.bonus_page_text9') }}</b></p>

        <h2 class="text-page__label">{{__('text.bonus_page_referral')}}</h2>
        <p>{!! __('text.bonus_page_text10') !!}</p>
        <p>{{ __('text.bonus_page_text11') }}</p>
    </div>
</div>

@endsection