@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')
<div id="properties_content">
    <div class="parked-domains">
        <div class="parked-domains__col">
            <form method="post" id="user_properties_form" name="user_properties_form">
                <div class="payment-details__items">
                    <div class="payment-details__item" style="display: none">
                        <div class="payment-details__top-row">
                            <div class="payment-details__currency">
                                <h3 class="payment-details__caption">{{__('text.admin_main_properties_login_form')}}</h3>
                            </div>
                            <p class="payment-details__code"></p>
                        </div>
                        <div class="payment-details__input input_elem">
                            <input autocomplete="off" type="text" maxlength="32" name="user_login_field" id="user_login_field" value="{{ $user_login }}" class="input">
                        </div>
                    </div>
                    <div class="payment-details__item">
                        <div class="payment-details__top-row">
                            <div class="payment-details__currency">
                                <div class="payment-details__icon">
                                    <img loading="lazy" src="{{ asset("admin_style/images/icons/lock.svg") }}" width="25" height="25" alt="">
                                </div>
                                <h3 class="payment-details__caption">{{__('text.admin_main_properties_new_password_form')}}</h3>
                            </div>
                        </div>
                        <div class="payment-details__input input_elem">
                            <input autocomplete="off" type="password" maxlength="32" name="new_password_field" id="new_password_field" value="" class="input">
                        </div>
                        <div id="new_password_error">
                            {{__('text.admin_common_form_empty_field')}}
                        </div>
                    </div>
                    <div class="payment-details__item">
                        <div class="payment-details__top-row">
                            <div class="payment-details__currency">
                                <div class="payment-details__icon">
                                    <img loading="lazy" src="{{ asset("admin_style/images/icons/lock.svg") }}" width="25" height="25" alt="">
                                </div>
                                <h3 class="payment-details__caption">{{__('text.admin_main_properties_repeat_new_password_form')}}</h3>
                            </div>
                            <p class="payment-details__code"></p>
                        </div>
                        <div class="payment-details__input input_elem">
                            <input autocomplete="off" type="password" maxlength="32" name="new_password_repeat_field" id="new_password_repeat_field" value="" class="input">
                        </div>
                        <div id="new_password_repeat_error">
                            {{__('text.admin_common_form_empty_field')}}
                        </div>
                    </div>
                </div>
                <div id="new_password_messages"></div>
                <div class="payment-details__bottom">
                    <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('new_password_messages', '{{__('text.admin_common_saving_message')}}'); saveUserProperties();">
                        <span>
                            <span>
                                {{__('text.admin_main_properties_password_form_submit')}}
                            </span>
                        </span>
                        <svg width="20" height="20">
                            <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="invalid_password_repeat" value="{{__('text.admin_common_form_invalid_password_repeat')}}">

@endsection