{{--
    Превью "My orders" в лейауте design_10. Структура — cabinet-parts.orders-body
    (1:1 с референсом customerportal), стили — design_10/css/cabinet-preview.css.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="container page-wrapper">
    <main class="main cabp">
        <h1 class="default__title">{{ __('text.cabinet_nav_orders') }}</h1>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.orders-body')
    </main>
</div>
@endsection
