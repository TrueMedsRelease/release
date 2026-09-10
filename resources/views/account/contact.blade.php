@extends('account.layout')

{{--
    Contact us 1:1 СЃ customerportal assets/Pages/Contact.vue
    (Р°РІС‚РѕСЂРёР·РѕРІР°РЅРЅР°СЏ РІРµС‚РєР°: Р±РµР· РєР°РїС‡Рё-РјРѕРґР°Р»РєРё вЂ” РѕРЅР° С‚РѕР»СЊРєРѕ РґР»СЏ РіРѕСЃС‚РµР№,
    СЌС‚Р° СЃС‚СЂР°РЅРёС†Р° С‚СЂРµР±СѓРµС‚ РІС…РѕРґР°; ConfirmDialog РїСЂРё СЃРјРµРЅРµ name/email,
    success #ok, contact-alert, info_1/2/3 вЂ” РІСЃС‘ РєР°Рє РІ РїРѕСЂС‚Р°Р»Рµ).
--}}

@section('cab_title', __('text.cabinet_nav_contact'))

@section('cab_content')
<section class="page__contact-us contact-us" x-data="cabinetContact()" x-cloak>
    <div class="contact-us__wrapper">
        <div class="message_sended" style="text-align: center;" x-show="success">
            <div id="ok">
                <h2 style="font-weight: bold;">{{ __('text.cabinet_contact_thanks') }}</h2>
                <br>
                <p>{{ __('text.cabinet_contact_sent') }}</p>
            </div>
        </div>

        <div class="contact-us__container" x-show="!success">
            <div class="contact-us__top-row top-row">
                <h2 class="top-row__title title">{{ __('text.cabinet_nav_contact') }}</h2>
            </div>

            <div class="contact-us__body">
                <form class="contact-us__form" method="post" action="{{ route('account.api.contact') }}" @submit.prevent="submit()">
                    <div class="contact-alert" role="alert" x-show="submitError" x-text="submitError"></div>

                    <div class="form__rows">
                        <div class="form__line">
                            <div class="form__row" x-bind:class="{ active: name }">
                                <label class="form__label" for="cab-contact-name">{{ __('text.cabinet_contact_name') }}</label>
                                <div class="form__input">
                                    <input id="cab-contact-name" type="text" class="input" x-bind:class="{ '_form-error': fieldError('name') }" x-model="name" autocomplete="name">
                                </div>
                            </div>
                        </div>
                        <div class="form__line">
                            <div class="form__row" x-bind:class="{ active: email }">
                                <label class="form__label" for="cab-contact-email">Email</label>
                                <div class="form__input">
                                    <input id="cab-contact-email" type="email" class="input" x-bind:class="{ '_form-error': fieldError('email') }" x-model="email" autocomplete="email">
                                </div>
                            </div>
                        </div>
                        <div class="form__line">
                            <div class="form__row" x-bind:class="{ active: subject }">
                                <label class="form__label" for="cab-contact-subject">{{ __('text.cabinet_contact_subject') }}</label>
                                <div class="form__input">
                                    <input id="cab-contact-subject" type="text" class="input" x-bind:class="{ '_form-error': fieldError('subject') }" x-model="subject">
                                </div>
                            </div>
                        </div>
                        <div class="form__line">
                            <div class="form__row" x-bind:class="{ active: message }">
                                <label class="form__label" for="cab-contact-message">{{ __('text.cabinet_contact_message') }}</label>
                                <div class="form__input">
                                    <textarea id="cab-contact-message" class="input" x-bind:class="{ '_form-error': fieldError('message') }" rows="6" x-model="message" placeholder="{{ __('text.cabinet_contact_placeholder') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="form__button button button--green" x-bind:disabled="sending">
                        <span x-show="!sending">{{ __('text.cabinet_contact_send') }}</span>
                        <span class="button__spinner" aria-hidden="true" x-show="sending"></span>
                    </button>
                </form>
                <div class="contact-us__info">
                    <p>{{ __('text.cabinet_contact_info_1') }}</p>
                    <p>{{ __('text.cabinet_contact_info_2') }}</p>
                    <p>{{ __('text.cabinet_contact_info_3') }}</p>
                </div>
            </div>
        </div>

        {{-- ConfirmDialog 1:1 (СЃРјРµРЅР° РєРѕРЅС‚Р°РєС‚РЅС‹С… РґР°РЅРЅС‹С…) --}}
        <div class="confirm-overlay" x-show="confirmOpen" @click.self="onCancel()" @keydown.escape.window="onCancel()">
            <div class="confirm-dialog" role="alertdialog" aria-modal="true">
                <div class="confirm-header">
                    <h2 class="confirm-title">{{ __('text.cabinet_contact_confirm_title') }}</h2>
                    <p class="confirm-description">{{ __('text.cabinet_contact_confirm_text') }}</p>
                </div>
                <div class="confirm-footer">
                    <button type="button" class="confirm-btn confirm-btn--cancel" @click="onCancel()">{{ __('text.cabinet_cancel') }}</button>
                    <button type="button" class="confirm-btn confirm-btn--confirm" @click="onConfirm()">{{ __('text.cabinet_continue') }}</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('cab_scripts')
@php
$cabinetConfig = [
    'contactUrl' => route('account.api.contact'),
    'meUrl' => route('account.api.me'),
    'email' => $email,
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabinetConfig);
</script>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/account.js') }}?v={{ filemtime(public_path('js/cabinet/account.js')) }}"></script>
@endpush

