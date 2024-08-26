
@extends($design . '.layouts.main')

@section('title', __('text.contact_us_title'))

@section('content')

<div class="page__body">
    <div class="page__top-line top-line">
        <h1 class="top-line__title">{{__('text.contact_us_title')}}</h1>
    </div>
    <div class="message_sended hidden">
        <h2>{{__('text.contact_us_thanks')}}</h2>
        <br>
        <p>{{__('text.contact_us_sended')}}</p>
    </div>
    <div class="page__contact contact-us">
        <form class="contact-us__form form" method="POST">
            <div class="form__row">
                <div class="form__input">
                    <input data-required id = "name" autocomplete="off" type="text" name="form[name]" data-error="" placeholder="{{__('text.contact_us_name')}}" class="input">
                </div>
                <div class="form__input">
                    <input data-required="email" id = "email" autocomplete="off" type="text" name="form[email]" data-error="Error" placeholder="{{__('text.contact_us_email')}}" class="input">
                </div>
                <div class="form__input">
                    <input autocomplete="off" id = "subject" type="text" name="form[subject]" data-error="Error" placeholder="{{__('text.contact_us_subject')}}" class="input">
                </div>
            </div>
            <div class="form__row">
                <textarea autocomplete="off" id = "message" type="text" name="form[message]" data-error="Error" placeholder="{{__('text.contact_us_message')}}" class="input"></textarea>
            </div>
            <div class="form__row captcha">
            <picture>
            <source srcset="/captcha" type="image/webp">
            <img src="/captcha">
            </picture>
            <div class="form__input">
                    <input autocomplete="off" type="text" id = "captcha" name="form[captcha]" data-error="Error" placeholder="{{__('text.contact_us_code')}}" class="input">
                </div>
            </div>
            <button onclick="sendAjaxContact()" type="button" class="form__button">{{__('text.contact_us_send')}}</button>
        </form>
        <div class="contact-us__descr">
            <p>{{__('text.contact_us_describe1')}}</p>
            <p>{{__('text.contact_us_describe2')}}</p>
            <p>{{__('text.contact_us_describe3')}}</p>
        </div>
    </div>
</div>

@endsection