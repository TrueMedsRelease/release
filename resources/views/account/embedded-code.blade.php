@extends('account.login')

@section('cab_title', __('text.cabinet_login_title'))

@section('cab_content')
    <h1 class="login-content__title">{{ __('text.cabinet_login_title') }}</h1>

    <form class="login-content__form form" method="post" action="{{ route('account.embedded.verify') }}" id="cab-code-form">
        @csrf
        <input type="hidden" name="fingerprint" id="cab-code-fp" value="" />
        <div class="login-content__error" role="alert" id="cab-code-error" style="display: none;"></div>
        @if (session('cabinet_embedded_error'))
            <div class="login-content__error" role="alert">{{ session('cabinet_embedded_error') }}</div>
        @endif

        <p class="login-content__hint" style="margin-bottom: 12px;">
            {{ __('text.cabinet_code_sent') }}
            <b>{{ $email }}</b>
        </p>

        <p class="login-content__hint" style="margin-bottom: 10px;">
            {{ __('text.cabinet_code_delivery_note') }}
        </p>

        <p class="login-content__hint" style="margin-bottom: 10px; font-weight: 600;">
            {{ __('text.cabinet_code_label') }}
        </p>

        <div class="otp-input" role="group" aria-label="6 digit code" id="cab-otp">
            @for ($i = 0; $i < 6; $i++)
                <input class="otp-input__cell" type="text" inputmode="numeric" autocomplete="one-time-code"
                       maxlength="1" value="" aria-label="Digit {{ $i + 1 }}" />
            @endfor
        </div>
        <input type="hidden" name="code" id="cab-code-hidden" value="" />

        <button class="form__button button button--dark confirm-btn" type="submit" id="cab-code-submit">
            <span>{{ __('text.cabinet_confirm') }}</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>

        <div class="otp-actions">
            <a class="link-btn" href="{{ route('account.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg>
                {{ __('text.cabinet_change_email') }}
            </a>
            <button type="button" class="link-btn" id="cab-resend" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 11-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
                <span id="cab-resend-label">{{ __('text.cabinet_resend') }}</span>
            </button>
        </div>
    </form>
@endsection

@push('cab_login_scripts')
@php
// Отдельной переменной: @json([...]) с вложенными route() внутри
// ломает парсер Blade-директив (несбалансированные скобки в PHP).
$cabCodeConfig = [
    'resendIn' => __('text.cabinet_resend_in'),
    'resend' => __('text.cabinet_resend'),
    'emailUrl' => route('account.index'),
];
@endphp
<script>
window.CabCodeConfig = @json($cabCodeConfig);
</script>
<script src="{{ asset('js/cabinet/embedded.js') }}" defer></script>
@endpush
