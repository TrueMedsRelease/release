{{--
    Превью "Chat" в лейауте design_5. Структура — cabinet-parts.chat-body
    (1:1 с референсом customerportal Chat.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="text-page cabp-wrap">
    <h2 class="title-page">{{ __('text.cabinet_nav_chat') }}</h2>
    <div class="cabp">
        @include($design . '.cabinet._nav')

        @include('cabinet-parts.chat-body')
    </div>
</div>
@endsection
