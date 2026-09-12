{{--
    Превью "Referral program" в лейауте design_4. Структура — cabinet-parts.referral-body.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<h1 class="content__title title">{{ __('text.cabinet_nav_referral') }}</h1>
<div class="cabp">
    @include($design . '.cabinet._nav')

    @include('cabinet-parts.referral-body')
</div>
@endsection
