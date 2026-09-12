{{--
    Превью "Contact us" в лейауте design_5. Поля — компонентами магазина
    (.input-box), инфо + диалог — cabinet-parts.contact-side (референс Contact.vue).
--}}
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')
<link rel="stylesheet" href="{{ asset($design . '/css/cabinet-preview.css') }}">
<div class="text-page cabp-wrap">
    <h2 class="title-page">{{ __('text.cabinet_nav_contact') }}</h2>
    <div class="cabp">
        @include($design . '.cabinet._nav')

        <div class="cabp-layout">
            <form class="cabp-form" onsubmit="return false;">
                <div class="input-box">
                    <label for="cabp-name">{{ __('text.cabinet_contact_name') }}</label>
                    <input id="cabp-name" type="text" placeholder="{{ __('text.cabinet_contact_name') }}" value="John Doe" autocomplete="name">
                </div>
                <div class="input-box">
                    <label for="cabp-email">Email</label>
                    <input id="cabp-email" type="email" placeholder="Email" value="{{ $cabp_user['email'] }}" autocomplete="email">
                </div>
                <div class="input-box">
                    <label for="cabp-subject">{{ __('text.cabinet_contact_subject') }}</label>
                    <input id="cabp-subject" type="text" placeholder="{{ __('text.cabinet_contact_subject') }}" value="Order question">
                </div>
                <div class="input-box">
                    <label for="cabp-message">{{ __('text.cabinet_contact_message') }}</label>
                    <textarea id="cabp-message" rows="5" placeholder="{{ __('text.cabinet_contact_placeholder') }}">Hello! Where is my order #104281?</textarea>
                </div>
                <label class="btn btn-primary cabp-send" for="cabp-contact-confirm">{{ __('text.cabinet_contact_send') }}</label>
            </form>
            @include('cabinet-parts.contact-side')
        </div>
    </div>
</div>
@endsection
