{{--
    Превью "My orders" в лейауте design_4. Структура — cabinet-parts.orders-body
    (1:1 с референсом customerportal), стили — design_4/css/cabinet-preview.css.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<h1 class="content__title title">{{ __('text.cabinet_nav_orders') }}</h1>
<div class="cabp">
    @include($design . '.cabinet._nav')

    @include('cabinet-parts.orders-body')
</div>
@endsection
