
@extends($design . '.layouts.main')

@section('title', $page_properties->title)
@section('keywords', $page_properties->keyword)
@section('description', $page_properties->description)

@section('content')

<section class="page__form-block form-block">
    <div class="message_sended hidden">
        <h2>{{__('text.affiliate_thanks')}}</h2>
        <br>
        <p>{{__('text.affiliate_sended')}}</p>
    </div>
    <div class="form-block__container">
        <h2 class="form-block__title title" id = "scroll">{{__('text.affiliate_title')}}</h2>
        <div class="form-block__body">
            <div class="form-block__descr">
                <p>{{__('text.affiliate_contact_message')}}</p>
            </div>
        <form id = "message_send_form" class="form-block__form form" method="post">
            <div class="form__default-rows">
                <div class="form__row">
                    <label for="name" class="form__label">{{__('text.affiliate_name')}}</label>
                    <input data-required autocomplete="off" type="text" id = "name" name="form[name]" data-error="" placeholder="{{__('text.affiliate_name')}}" class="form__input input">
                </div>
                <div class="form__row">
                    <label for="email" class="form__label">{{__('text.affiliate_email')}}</label>
                    <input data-required autocomplete="off" type="text" id = "email" name="form[email]" data-error="" placeholder="{{__('text.affiliate_email')}}" class="form__input input">
                </div>
                <div class="form__row">
                    <label for="telegram" class="form__label">{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}</label>
                    <input data-required autocomplete="off" type="text" id = "jabber" name="form[jabber]" data-error="" placeholder="{{__('text.affiliate_jabber')}}{{__('text.affiliate_telegram')}}" class="form__input input">
                </div>
                <div class="form__row form__row--top-alignment">
                    <label for="message" class="form__label">{{__('text.affiliate_message')}}</label>
                    <textarea autocomplete="off" type="text" id = "message" name="form[message]" data-error="" placeholder="{{__('text.affiliate_message')}}" class="form__input input"></textarea>
                </div>
            </div>
            <div class="form__row form__row--captcha">
                <label for="captcha" class="form__label">{{__('text.affiliate_code')}}</label>
                <div class="form__input">
                    <picture>
                        <source srcset="{{ captcha_src() }}" type="image/webp">
                        <img src="{{ captcha_src() }}">
                    </picture>
                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" class="form__input input">
                </div>
            </div>
            <input onclick="sendAjaxAffiliate()" type="button" value="{{__('text.affiliate_send')}}" id = "message_send_button" class="form__button button button--primary button--with-arrow button--send--aff"></button>
        </form>
    </div>
</section>

@endsection