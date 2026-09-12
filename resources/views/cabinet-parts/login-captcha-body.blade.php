{{--
    Shared: шаг 2 логина — пазл-капча (референс customerportal Login.vue + SmartCaptcha).
    Живая страница: пазл грузится из кабинета, после решения форма уходит
    на embedded.start (shop-login.js). Стили — в per-design cabinet-login.css.
--}}
<div class="cablogin-step" id="shop-captcha">
    <p class="cablogin-step__caption">2 · {{ __('text.cabinet_captcha_title') }}</p>
    <p class="cablogin-hint">{{ __('text.cabinet_captcha_subtitle') }}</p>
    <form class="cablogin-form" id="shop-captcha-form" method="post" action="{{ route('account.embedded.start') }}">
        @csrf
        <input type="hidden" name="email" id="shop-captcha-email" value="">
        <input type="hidden" name="fingerprint" id="shop-fp" value="">
        <div class="cablogin-captcha">
            <div class="cablogin-captcha__canvas" id="shop-captcha-canvas">
                <img id="shop-captcha-bg" alt="" draggable="false" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                <div id="shop-captcha-piece" style="display: none; position: absolute;"></div>
            </div>
            <div class="cablogin-captcha__controls">
                <span class="cablogin-btn cablogin-btn--ghost" id="shop-captcha-refresh"><span>↻</span></span>
                <span class="cablogin-btn cablogin-btn--primary" id="shop-captcha-verify"><span>{{ __('text.cabinet_captcha_verify') }}</span></span>
            </div>
            <div class="cablogin-status" id="shop-captcha-status"></div>
        </div>
    </form>
    <p class="cablogin-hint">{{ __('text.cabinet_captcha_hint') }}</p>
    <div class="cablogin-actions">
            <a class="cablogin-link" href="{{ route('cabinet.login', ['step' => 'email']) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 8l9 6 9-6"/></svg>
            {{ __('text.cabinet_change_email') }} (<span id="shop-captcha-email-text"></span>)
        </a>
    </div>
</div>
