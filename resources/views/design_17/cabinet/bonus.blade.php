{{--
    Превью "Bonus" в лейауте design_17. Структура — cabinet-parts.bonus-body
    (1:1 с референсом customerportal, карта — банковской картой).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="main__content cabp">
    <div class="page-heading">
        <h1 class="h1">{{ __('text.cabinet_nav_bonus') }}</h1>
    </div>

    @include($design . '.cabinet._nav')

    @include('cabinet-parts.bonus-body')
</div>
@endsection
