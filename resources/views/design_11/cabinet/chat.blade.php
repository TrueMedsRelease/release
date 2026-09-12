{{--
    Превью "Chat" в лейауте design_11. Структура — cabinet-parts.chat-body
    (1:1 с референсом customerportal Chat.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page-wrapper container">
    <main class="main cabp">
        <div class="main__heading">
            <h1 class="h1">{{ __('text.cabinet_nav_chat') }}</h1>
        </div>

        @include($design . '.cabinet._nav')

        @include('cabinet-parts.chat-body')
    </main>
</div>
@endsection
