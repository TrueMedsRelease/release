{{--
    Превью "Bonus" в лейауте design_9. Структура — cabinet-parts.bonus-body
    (1:1 с референсом customerportal, карта — банковской картой).
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
                <h1 class="default__title title">{{ __('text.cabinet_nav_bonus') }}</h1>

                @include($design . '.cabinet._nav')

                @include('cabinet-parts.bonus-body')
            </div>
        </div>
    </div>
</main>
@endsection
