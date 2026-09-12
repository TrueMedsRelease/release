{{--
    Превью "Bonus" в лейауте design_5. Структура — cabinet-parts.bonus-body
    (1:1 с референсом customerportal, карта — банковской картой).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="text-page cabp-wrap">
    <h2 class="title-page">{{ __('text.cabinet_nav_bonus') }}</h2>
    <div class="cabp">
        @include($design . '.cabinet._nav')

        @include('cabinet-parts.bonus-body')
    </div>
</div>
@endsection
