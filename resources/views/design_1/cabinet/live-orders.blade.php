{{--
    Живой кабинет design_1: "My orders" (данные через /account/api/*).
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
<section class="page__form-block form-block cabp-wrap">
    <div class="form-block__container cabp">
        <h2 class="form-block__title title">{{ __('text.cabinet_nav_orders') }}</h2>

        @include('cabinet-live.nav-live')

        @include('cabinet-live.orders-live')
    </div>
</section>
<script src="{{ asset_ver('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset_ver('js/cabinet/account.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/shop-cabinet.js') }}"></script>
<script src="{{ asset_ver('js/cabinet/device-lib.js') }}" defer></script>
@endsection
