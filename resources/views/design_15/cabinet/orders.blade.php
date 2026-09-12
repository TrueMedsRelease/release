{{--
    Превью "My orders" в лейауте design_15. Структура — cabinet-parts.orders-body
    (1:1 с референсом customerportal), стили — design_15/css/cabinet-preview.css.
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page-wrapper container">
    <main class="main cabp">
        <h1 class="h1">{{ __('text.cabinet_nav_orders') }}</h1>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.orders-body')
    </main>
</div>
@endsection
