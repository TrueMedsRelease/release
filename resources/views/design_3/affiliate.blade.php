
@extends($design . '.layouts.main')

@section('title', $title)

@section('content')

<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.affiliate_title')}}</h1>
    </div>
    <div class="message_sended hidden">
        <h2>{{__('text.affiliate_thanks')}}</h2>
        <br>
        <p>{{__('text.affiliate_sended')}}</p>
    </div>
    <div class="page__contact contact-us">
        <form class="contact-us__form form" method="POST">
            <div class="form__row">
                <div class="form__input">
                    <input data-required id = "name" autocomplete="off" type="text" name="form[]" data-error="Error" placeholder="{{__('text.affiliate_name')}}" class="input">
                </div>
                <div class="form__input">
                    <input autocomplete="off" id = "email" type="text" name="form[]" data-error="Error" placeholder="{{__('text.affiliate_email')}}" class="input">
                </div>
                <div class="form__input">
                    <input autocomplete="off" id = "jabber" type="text" name="form[]" data-error="Error" placeholder="{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}" class="input">
                </div>
            </div>
            <div class="form__row">
                <textarea autocomplete="off" id = "message" type="text" name="form[]" data-error="Error" placeholder="{{__('text.affiliate_message')}}" class="input"></textarea>
            </div>
            <div class="form__row captcha">
                <picture>
                    <source srcset="{{ captcha_src() }}" type="image/webp">
                    <img src="{{ captcha_src() }}">
                </picture>
                <div class="form__input">
                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="Error" placeholder="{{__('text.affiliate_code')}}" class="input">
                </div>
            </div>
            <button onclick="sendAjaxAffiliate()" type="button" class="form__button" id = "message_send_button">{{__('text.affiliate_send')}}</button>
        </form>
        <div class="contact-us__descr">
            <p>{{__('text.affiliate_contact_message')}}</p>
        </div>
    </div>
</div>

@endsection