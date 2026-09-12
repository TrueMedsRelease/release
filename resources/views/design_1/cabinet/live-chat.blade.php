{{--
    Живой кабинет design_1: "Chat" (данные через /account/api/chat/*).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<section class="page__form-block form-block cabp-wrap">
    <div class="form-block__container cabp">
        <h2 class="form-block__title title">{{ __('text.cabinet_nav_chat') }}</h2>

        @include('cabinet-live.nav-live')

        @include('cabinet-live.chat-live')
    </div>
</section>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/nacl.min.js') }}"></script>
<script src="{{ asset('js/cabinet/e2ee.js') }}"></script>
<script src="{{ asset('js/cabinet/account.js') }}"></script>
<script src="{{ asset('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset('js/cabinet/device-lib.js') }}" defer></script>
@endsection
