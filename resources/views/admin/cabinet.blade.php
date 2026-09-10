@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<link rel="stylesheet" href="{{ asset('admin_style/css/cabinet.css') }}">

<div class="cab-admin">
    <p class="cab-admin__lead">
        When enabled, the “My Account” button opens the customer cabinet
        <b>inside this shop</b> (orders are loaded from the cabinet via API).
        When disabled, customers are redirected to true-help as before.
    </p>

    @include('admin.cabinet.status', [
        'cabinet_enabled' => $cabinet_enabled,
        'cabinet_secret' => $cabinet_secret,
        'cabinet_embedded' => $cabinet_embedded,
    ])

    @include('admin.cabinet.form', [
        'cabinet_enabled' => $cabinet_enabled,
        'cabinet_url' => $cabinet_url,
        'cabinet_secret' => $cabinet_secret,
        'cabinet_domain' => $cabinet_domain,
    ])

    @include('admin.cabinet.guide', [
        'cabinet_domain' => $cabinet_domain,
        'cabinet_callback' => $cabinet_callback,
    ])
</div>

<script src="{{ asset('admin_style/js/cabinet.js') }}"></script>

@endsection
