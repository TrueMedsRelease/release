@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<div class="page-container">
    <article class="content content--page content--about">
        <h1>{{__('text.bonus_ref_menu')}}</h1>
        <h2>{{ __('text.bonus_page_bonus') }}</h2>
        <p>{{ __('text.bonus_page_text1') }}</p>
        <p style="line-height: 1.5">
            {{ __('text.bonus_page_text2') }}
        </p>
        <p class="mb-0">{{ __('text.bonus_page_text3') }}</p>
        <ul style="line-height: 1.5">
            <li>{{ __('text.bonus_page_text4') }}</li>
            <li>{{ __('text.bonus_page_text5') }}</li>
            <li>{{ __('text.bonus_page_text6') }}</li>
        </ul>
        <p style="line-height: 1.8">
            {!! __('text.bonus_page_text7') !!}
        </p>
        <p style="line-height: 1.8">
            {!! __('text.bonus_page_text8') !!}
        </p>
        <p><b>{{ __('text.bonus_page_text9') }}</b></p>

        <h2>{{ __('text.bonus_page_referral') }}</h2>
        <p style="line-height: 1.5">
            {!! __('text.bonus_page_text10') !!}
        </p>
        <p style="line-height: 1.5">
            {{ __('text.bonus_page_text11') }}
        </p>
    </article>
</div>
@endsection