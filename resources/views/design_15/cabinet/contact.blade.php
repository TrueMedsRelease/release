{{--
    Превью "Contact us" в лейауте design_15. Поля — компонентами магазина,
    инфо + диалог — cabinet-parts.contact-side (референс Contact.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="page-wrapper container">
    <main class="main cabp">
        <h1 class="h1">{{ __('text.cabinet_nav_contact') }}</h1>

        @include($design . '.cabinet._nav')

        <div class="cabp-layout">
            <form class="form form-panel cabp-form" onsubmit="return false;">
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="cabp-name" placeholder=" " value="John Doe">
                    <label class="form__label form__label--text" for="cabp-name">{{ __('text.cabinet_contact_name') }}</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-email" type="email" id="cabp-email" placeholder=" " value="{{ $cabp_user['email'] }}">
                    <label class="form__label form__label--email" for="cabp-email">Email</label>
                </div>
                <div class="form__field">
                    <input class="form__text-input input-text" type="text" id="cabp-subject" placeholder=" " value="Order question">
                    <label class="form__label form__label--text" for="cabp-subject">{{ __('text.cabinet_contact_subject') }}</label>
                </div>
                <div class="form__field">
                    <textarea class="form__text-input input-textarea" id="cabp-message" rows="5" placeholder="{{ __('text.cabinet_contact_placeholder') }}">Hello! Where is my order #104281?</textarea>
                    <label class="form__label form__label--textarea" for="cabp-message">{{ __('text.cabinet_contact_message') }}</label>
                </div>
                <div class="form__field">
                    <label class="button form__submit cabp-send" for="cabp-contact-confirm"><span>{{ __('text.cabinet_contact_send') }}</span></label>
                </div>
            </form>
            @include('cabinet-parts.contact-side')
        </div>
    </main>
</div>
@endsection
