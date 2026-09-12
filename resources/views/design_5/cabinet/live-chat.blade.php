{{--
    Живой кабинет design_5: "Chat" (данные через /account/api/chat/*).
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
        @include('cabinet-live.nav-live')

        @include('cabinet-live.chat-live')
    </div>
</div>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/nacl.min.js') }}"></script>
<script src="{{ asset('js/cabinet/e2ee.js') }}"></script>
<script src="{{ asset('js/cabinet/account.js') }}"></script>
<script src="{{ asset('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset('js/cabinet/device-lib.js') }}" defer></script>
@endsection
