{{--
    Превью "Contact us" в лейауте design_1. Поля — компонентами магазина,
    инфо + диалог — cabinet-parts.contact-side (референс Contact.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<section class="page__form-block form-block cabp-wrap">
    <div class="form-block__container cabp">
        <h2 class="form-block__title title">{{ __('text.cabinet_nav_contact') }}</h2>

        @include($design . '.cabinet._nav')

        <div class="cabp-layout">
            <form class="form cabp-form" onsubmit="return false;">
                <div class="form__row">
                    <label for="cabp-name" class="form__label">{{ __('text.cabinet_contact_name') }}</label>
                    <input id="cabp-name" type="text" class="form__input input" value="John Doe" autocomplete="name">
                </div>
                <div class="form__row">
                    <label for="cabp-email" class="form__label">Email</label>
                    <input id="cabp-email" type="text" class="form__input input" value="{{ $cabp_user['email'] }}" autocomplete="email">
                </div>
                <div class="form__row">
                    <label for="cabp-subject" class="form__label">{{ __('text.cabinet_contact_subject') }}</label>
                    <input id="cabp-subject" type="text" class="form__input input" value="Order question">
                </div>
                <div class="form__row">
                    <label for="cabp-message" class="form__label">{{ __('text.cabinet_contact_message') }}</label>
                    <textarea id="cabp-message" class="form__input input" rows="5" placeholder="{{ __('text.cabinet_contact_placeholder') }}">Hello! Where is my order #104281?</textarea>
                </div>
                <div class="form__row">
                    <label class="form__button button button--primary cabp-send" for="cabp-contact-confirm"><span>{{ __('text.cabinet_contact_send') }}</span></label>
                </div>
            </form>
            @include('cabinet-parts.contact-side')
        </div>
    </div>
</section>
@endsection
