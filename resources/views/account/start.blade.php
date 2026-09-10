@extends('account.layout')

@section('cab_title', __('text.cabinet_login_title'))

@section('cab_content')
    <h1 class="cab-account__title">{{ __('text.cabinet_login_title') }}</h1>
    <p class="cab-account__sub">{{ __('text.cabinet_login_text') }}</p>

    @if ($cabinet_error !== '')
        <div class="cab-account__error" role="alert">{{ $cabinet_error }}</div>
        <div style="height: 16px;"></div>
    @endif

    <div class="cab-account__list">
        <div class="oc-card" style="padding: 20px; display: flex; flex-direction: column; gap: 10px;">
            <a class="oc-action oc-action--dark" href="{{ route('account.start') }}">
                {{ __('text.cabinet_continue') }}
            </a>
            <p class="cab-account__sub" style="margin: 0;">{{ __('text.cabinet_login_redirect_note') }}</p>
        </div>
    </div>
@endsection
