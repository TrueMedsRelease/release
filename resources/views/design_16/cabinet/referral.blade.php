{{--
    Превью "Referral program" в лейауте design_16. Структура — cabinet-parts.referral-body.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page-wrapper container">
    <main class="main cabp">
        <h1 class="h1">{{ __('text.cabinet_nav_referral') }}</h1>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.referral-body')
    </main>
</div>
@endsection
