{{--
    Shared: шаг 1 логина — email (референс customerportal Login.vue).
    Живая страница: проверка почты и переход на шаг капчи (shop-login.js).
    Стили — в per-design cabinet-login.css.
--}}
<div class="cablogin-step">
    <p class="cablogin-step__caption">1 · Email</p>
    @if (!empty(session('cabinet_embedded_error')))
        <div class="cablogin-field__error" role="alert">{{ session('cabinet_embedded_error') }}</div>
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
