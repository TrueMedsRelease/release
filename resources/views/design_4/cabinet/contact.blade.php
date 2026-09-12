{{--
    Превью "Contact us" в лейауте design_4. Поля — компонентами магазина,
    инфо + диалог — cabinet-parts.contact-side (референс Contact.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<h1 class="content__title title">{{ __('text.cabinet_nav_contact') }}</h1>
<div class="cabp">
    @include($design . '.cabinet._nav')

    <div class="cabp-layout">
        <form class="contact-form__body form cabp-form" onsubmit="return false;">
            <div class="contact-form__line">
                <label for="cabp-name" class="contact-form__label">{{ __('text.cabinet_contact_name') }}</label>
                <div class="contact-form__input">
                    <input id="cabp-name" type="text" class="input" placeholder="{{ __('text.cabinet_contact_name') }}" value="John Doe" autocomplete="name">
                </div>
            </div>
            <div class="contact-form__line">
                <label for="cabp-email" class="contact-form__label">Email</label>
                <div class="contact-form__input">
                    <input id="cabp-email" type="text" class="input" placeholder="Email" value="{{ $cabp_user['email'] }}" autocomplete="email">
                </div>
            </div>
            <div class="contact-form__line">
                <label for="cabp-subject" class="contact-form__label">{{ __('text.cabinet_contact_subject') }}</label>
                <div class="contact-form__input">
                    <input id="cabp-subject" type="text" class="input" placeholder="{{ __('text.cabinet_contact_subject') }}" value="Order question">
                </div>
            </div>
            <div class="contact-form__line">
                <label for="cabp-message" class="contact-form__label">{{ __('text.cabinet_contact_message') }}</label>
                <div class="contact-form__input">
                    <textarea id="cabp-message" class="input" rows="5" placeholder="{{ __('text.cabinet_contact_placeholder') }}">Hello! Where is my order #104281?</textarea>
                </div>
            </div>
            <div class="contact-form__line">
                <label class="contact-form__button button button--filled cabp-send" for="cabp-contact-confirm">{{ __('text.cabinet_contact_send') }}</label>
            </div>
        </form>
        @include('cabinet-parts.contact-side')
    </div>
</div>
@endsection
