{{--
    Превью "Chat" в лейауте design_2. Структура — cabinet-parts.chat-body
    (1:1 с референсом customerportal Chat.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<main class="default cabp-wrap">
    <div class="default__container">
        <div class="default__body">
            <div class="default__content cabp">
                <h1 class="default__title title">{{ __('text.cabinet_nav_chat') }}</h1>

                @include($design . '.cabinet._nav')

                @include('cabinet-parts.chat-body')
            </div>
        </div>
    </div>
</main>
@endsection
