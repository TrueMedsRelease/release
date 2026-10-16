{{--
    Живой кабинет design_3: "Chat" (данные через /account/api/chat/*).
--}}
@extends($design . '.layouts.main')

@php
if (!function_exists('asset_ver')) {
    function asset_ver(string $path): string {
        static $mtimes = [];
        $full = public_path($path);
        if (!isset($mtimes[$path])) {
            $mtimes[$path] = is_file($full) ? filemtime($full) : null;
        }
        $url = asset($path);
        $v = $mtimes[$path] ?? time();
        return $url . '?v=' . $v;
    }
}
@endphp

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset_ver($design . '/css/cabinet-preview.css') }}">
<div class="page__body cabp-wrap">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{ __('text.cabinet_nav_chat') }}</h1>
    </div>
    <div class="cabp">
        @include('cabinet-live.nav-live')

        @include('cabinet-live.chat-live')
    </div>
</div>
<script src="{{ asset_ver('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset_ver('js/cabinet/account.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/device-lib.js') }}" defer></script>
@endsection
