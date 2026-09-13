{{--
    Живой кабинет design_16: "Bonus" (данные через /account/api/*).
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
<div class="page-wrapper container">
    <main class="main cabp">
        <h1 class="h1">{{ __('text.cabinet_nav_bonus') }}</h1>

        @include('cabinet-live.nav-live')

        @include('cabinet-live.bonus-live')
    </main>
</div>
<script src="{{ asset_ver('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset_ver('js/cabinet/nacl.min.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/e2ee.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/account.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/device-lib.js') }}" defer></script>
@endsection
