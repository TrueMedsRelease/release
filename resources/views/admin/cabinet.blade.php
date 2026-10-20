@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

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

@section('content')

<link rel="stylesheet" href="{{ asset_ver('/admin_style/css/cabinet.css') }}">

<div class="cab-admin">
    @include('admin.cabinet.form', [
        'cabinet_enabled' => $cabinet_enabled,
        'cabinet_url' => $cabinet_url,
        'cabinet_secret' => $cabinet_secret,
    ])

    @include('admin.cabinet.guide', [
        'cabinet_domain' => $cabinet_domain,
    ])
</div>

<script src="{{ asset_ver('admin_style/js/cabinet.js') }}"></script>

@endsection
