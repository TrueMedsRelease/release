{{--
    Превью "Contact us" в лейауте design_3. Поля — компонентами магазина
    (плейсхолдеры без лейблов, как в формах дизайна), инфо + диалог —
    cabinet-parts.contact-side (референс Contact.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page__body cabp-wrap">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{ __('text.cabinet_nav_contact') }}</h1>
    </div>
    <div class="cabp">
        @include($design . '.cabinet._nav')

        <div class="cabp-layout">
            <form class="form cabp-form" onsubmit="return false;">
                <div class="form__row">
                    <div class="form__input">
                        <input id="cabp-name" type="text" class="input" placeholder="{{ __('text.cabinet_contact_name') }}" aria-label="{{ __('text.cabinet_contact_name') }}" value="John Doe" autocomplete="name">
                    </div>
                </div>
                <div class="form__row">
                    <div class="form__input">
                        <input id="cabp-email" type="text" class="input" placeholder="Email" aria-label="Email" value="{{ $cabp_user['email'] }}" autocomplete="email">
                    </div>
                </div>
                <div class="form__row">
                    <div class="form__input">
                        <input id="cabp-subject" type="text" class="input" placeholder="{{ __('text.cabinet_contact_subject') }}" aria-label="{{ __('text.cabinet_contact_subject') }}" value="Order question">
                    </div>
                </div>
                <div class="form__row">
                    <textarea id="cabp-message" class="input" rows="5" placeholder="{{ __('text.cabinet_contact_placeholder') }}" aria-label="{{ __('text.cabinet_contact_message') }}">Hello! Where is my order #104281?</textarea>
                </div>
                <div class="form__row">
                    <label class="form__button cabp-send" for="cabp-contact-confirm">{{ __('text.cabinet_contact_send') }}</label>
                </div>
            </form>
            @include('cabinet-parts.contact-side')
        </div>
    </div>
</div>
@endsection
