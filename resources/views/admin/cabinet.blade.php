@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<link rel="stylesheet" href="{{ asset('admin_style/css/cabinet.css') }}">

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

<script src="{{ asset('admin_style/js/cabinet.js') }}"></script>

@endsection
