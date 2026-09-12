{{--
    Live: "Contact us" — Alpine (account.js cabinetContact, авторизованная ветка).
    Переменные: $design.
--}}
@php
$cabLiveContactConfig = [
    'contactUrl' => route('account.api.contact'),
    'meUrl' => route('account.api.me'),
    'loginUrl' => route('preview.cabinet.login', ['design' => $design, 'step' => 'email']),
    'email' => (string) session('cabinet.email', ''),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabLiveContactConfig);
window.CabinetMeUrl = @json(route('account.api.me'));
</script>

<div x-data="cabinetContact()" x-cloak>
    <div class="cabp-live-success" x-show="success" style="text-align:center">
        <h2 style="font-weight:bold">{{ __('text.cabinet_contact_thanks') }}</h2>
        <p>{{ __('text.cabinet_contact_sent') }}</p>
    </div>

    <div class="cabp-layout" x-show="!success">
        <form class="cabp-live-form" @submit.prevent="submit()">
            <div class="contact-alert" role="alert" x-show="submitError" x-text="submitError"></div>
            <div class="cabp-live-field">
                <label for="cab-live-name">{{ __('text.cabinet_contact_name') }}</label>
                <input id="cab-live-name" type="text" autocomplete="name" x-model="name">
            </div>
            <div class="cabp-live-field">
                <label for="cab-live-email">Email</label>
                <input id="cab-live-email" type="email" autocomplete="email" x-model="email">
            </div>
            <div class="cabp-live-field">
                <label for="cab-live-subject">{{ __('text.cabinet_contact_subject') }}</label>
                <input id="cab-live-subject" type="text" x-model="subject">
            </div>
            <div class="cabp-live-field">
                <label for="cab-live-message">{{ __('text.cabinet_contact_message') }}</label>
                <textarea id="cab-live-message" rows="5" x-model="message" placeholder="{{ __('text.cabinet_contact_placeholder') }}"></textarea>
            </div>
            <button type="submit" class="button cabp-action cabp-action--dark" :disabled="sending">
                <span x-show="!sending">{{ __('text.cabinet_contact_send') }}</span>
                <span x-show="sending">…</span>
            </button>
        </form>
        <aside class="cabp-side">
            <div class="cabp-help">
                <p>{{ __('text.cabinet_contact_info_1') }}</p>
                <p>{{ __('text.cabinet_contact_info_2') }}</p>
                <p>{{ __('text.cabinet_contact_info_3') }}</p>
            </div>
        </aside>
    </div>

    <div class="cabp-confirm" x-cloak :class="{ '_open': confirmOpen }">
        <div class="cabp-confirm__overlay" @click="onCancel()"></div>
        <div class="cabp-confirm__dialog" role="alertdialog" aria-modal="true">
            <div class="cabp-confirm__head">
                <p class="cabp-confirm__title">{{ __('text.cabinet_contact_confirm_title') }}</p>
                <p class="cabp-confirm__description">{{ __('text.cabinet_contact_confirm_text') }}</p>
            </div>
            <div class="cabp-confirm__footer">
                <button type="button" class="button cabp-action cabp-action--light" @click="onCancel()">{{ __('text.cabinet_cancel') }}</button>
                <button type="button" class="button cabp-action cabp-action--dark" @click="onConfirm()">{{ __('text.cabinet_continue') }}</button>
            </div>
        </div>
    </div>
</div>
