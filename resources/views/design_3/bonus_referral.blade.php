
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<div class="page__body" style="margin-top: 1.25rem">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.bonus_ref_menu')}}</h1>
    </div>
    <div class="page__default default-template">
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.bonus_page_bonus')}}</h2>
            <p class="default-template__text">{{__('text.bonus_page_text1')}}</p>
            <p class="default-template__text">{{ __('text.bonus_page_text2') }}</p>
            <ul class="default-template__list">
                {{ __('text.bonus_page_text3') }}
                <li>{{ __('text.bonus_page_text4') }}</li>
                <li>{{ __('text.bonus_page_text5') }}</li>
                <li>{{ __('text.bonus_page_text6') }}</li>
            </ul>
            <p class="default-template__text">{!! __('text.bonus_page_text7') !!}</p>
            <p class="default-template__text">{!! __('text.bonus_page_text8') !!}</p>
            <p class="default-template__text"><b>{{ __('text.bonus_page_text9') }}</b></p>
        </div>
        <div class="default-template__block default-template__block--with-border">
            <h2 class="default-template__caption">{{__('text.bonus_page_referral')}}</h2>
            <p class="default-template__text">{!! __('text.bonus_page_text10') !!}</p>
            <p class="default-template__text">{{ __('text.bonus_page_text11') }}</p>
        </div>
    </div>
</div>
</div>
</div>

@endsection