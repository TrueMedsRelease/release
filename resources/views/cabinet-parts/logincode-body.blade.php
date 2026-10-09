{{--
    Shared: шаг 3 логина — код из почты (референс customerportal Login.vue, OtpInput).
    Живая страница: OTP уходит на embedded.verify (автосабмит + кнопка, shop-login.js).
    Стили — в per-design cabinet-login.css. Переменные: $design, $email.
--}}
<div class="cablogin-step">
    <p class="cablogin-step__caption">3 · {{ __('text.cabinet_code_label') }}</p>
    <form class="cablogin-form" id="shop-code-form" method="post" action="{{ route('account.embedded.verify') }}">
        @csrf
        <input type="hidden" name="fingerprint" id="shop-code-fp" value="">
        @if (!empty(session('cabinet_embedded_error')))
            <div class="cablogin-field__error" role="alert">{{ session('cabinet_embedded_error') }}</div>
        @endif
        <p class="cablogin-hint">{{ __('text.cabinet_code_sent') }} <b>{{ $email ?? '' }}</b></p>
        <p class="cablogin-hint">{{ __('text.cabinet_code_delivery_note') }}</p>

        <div class="cablogin-otp" role="group" aria-label="6 digit code" id="shop-otp">
            @for ($i = 0; $i < 6; $i++)
                <input class="cablogin-otp__cell" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="1" value="" aria-label="Digit {{ $i + 1 }}">
            @endfor
        </div>
        <input type="hidden" name="code" id="shop-code-hidden" value="">

        <button class="cablogin-btn cablogin-btn--primary cablogin-btn--wide" type="submit"><span>{{ __('text.cabinet_confirm') }}</span></button>

        <div class="cablogin-actions">
            <a class="cablogin-link" href="{{ route('account.login', ['step' => 'email']) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg>
                {{ __('text.cabinet_change_email') }}
            </a>
            <button type="button" class="cablogin-link" id="shop-resend" disabled>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 11-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
                <span id="shop-resend-label">{{ __('text.cabinet_resend') }}</span>
            </button>
        </div>
    </form>
</div>
