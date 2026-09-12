{{--
    Превью "Referral program" в лейауте design_3. Структура — cabinet-parts.referral-body.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page__body cabp-wrap">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{ __('text.cabinet_nav_referral') }}</h1>
    </div>
    <div class="cabp">
        @include($design . '.cabinet._nav')

        @include('cabinet-parts.referral-body')
    </div>
</div>
@endsection
