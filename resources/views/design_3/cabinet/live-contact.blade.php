{{--
    Живой кабинет design_3: "Contact us" (отправка через /account/api/*).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page__body cabp-wrap">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{ __('text.cabinet_nav_contact') }}</h1>
    </div>
    <div class="cabp">
        @include('cabinet-live.nav-live')

        @include('cabinet-live.contact-live')
    </div>
</div>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/nacl.min.js') }}"></script>
<script src="{{ asset('js/cabinet/e2ee.js') }}"></script>
<script src="{{ asset('js/cabinet/account.js') }}"></script>
<script src="{{ asset('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset('js/cabinet/device-lib.js') }}" defer></script>
@endsection
