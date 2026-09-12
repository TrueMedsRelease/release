{{--
    Превью "Chat" в лейауте design_10. Структура — cabinet-parts.chat-body
    (1:1 с референсом customerportal Chat.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="container page-wrapper">
    <main class="main cabp">
        <h1 class="default__title">{{ __('text.cabinet_nav_chat') }}</h1>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.chat-body')
    </main>
</div>
@endsection
