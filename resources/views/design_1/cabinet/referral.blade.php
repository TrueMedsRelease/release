{{--
    Превью "Referral program" в лейауте design_1. Структура — cabinet-parts.referral-body.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<section class="page__form-block form-block cabp-wrap">
    <div class="form-block__container cabp">
        <h2 class="form-block__title title">{{ __('text.cabinet_nav_referral') }}</h2>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.referral-body')
    </div>
</section>
@endsection
