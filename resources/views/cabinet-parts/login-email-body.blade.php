{{--
    Shared: login step 1 — email (customerportal Login.vue reference).
    Live page: email check and move to the captcha step (shop-login.js).
    With ?login_key= an autologin box attempts sign-in first (same JS).
    Styles in per-design cabinet-login.css.
--}}
<div class="cablogin-step">
    <p class="cablogin-step__caption">1 · Email</p>
    @if (!empty(session('cabinet_embedded_error')))
        <div class="cablogin-field__error" role="alert">{{ session('cabinet_embedded_error') }}</div>
    @endif
    @if (!empty($loginKey ?? ''))
        <div class="cablogin-autologin" id="shop-autologin" data-login-key="{{ $loginKey }}" role="status">
            <span class="cablogin-spinner" id="shop-autologin-spinner" aria-hidden="true"></span>
            <p class="cablogin-hint" id="shop-autologin-status">{{ __('text.cabinet_autologin_working') }}</p>
        </div>
        <div class="cablogin-field__error" role="alert" id="shop-autologin-error" hidden></div>
    @endif
    <form class="cablogin-form" onsubmit="return false;" novalidate>
        <div class="cablogin-field">
            <label class="cablogin-field__label" for="shop-email">{{ __('text.cabinet_login_email') }}</label>
            <input id="shop-email" class="cablogin-field__input" type="email" autocomplete="email" value="{{ $email ?? '' }}">
        </div>
        <div class="cablogin-field__error" role="alert" id="shop-email-error" hidden></div>
        <span class="cablogin-btn cablogin-btn--primary" id="shop-continue"><span>{{ __('text.cabinet_login_submit') }}</span></span>
        <p class="cablogin-hint">{{ __('text.cabinet_login_hint') }}</p>
    </form>
</div>
