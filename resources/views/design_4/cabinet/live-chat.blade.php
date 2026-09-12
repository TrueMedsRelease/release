{{--
    Живой кабинет design_4: "Chat" (данные через /account/api/chat/*).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<h1 class="content__title title">{{ __('text.cabinet_nav_chat') }}</h1>
<div class="cabp">
    @include('cabinet-live.nav-live')

    @include('cabinet-live.chat-live')
</div>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/nacl.min.js') }}"></script>
<script src="{{ asset('js/cabinet/e2ee.js') }}"></script>
<script src="{{ asset('js/cabinet/account.js') }}"></script>
<script src="{{ asset('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset('js/cabinet/device-lib.js') }}" defer></script>
@endsection
