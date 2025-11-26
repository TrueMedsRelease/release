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
        <div class="parked-domains__col">
            <form name="default_error_page_block" id="default_error_page_block" method="POST">
                <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                    <tbody style="display: flex; gap: 15px; flex-direction: column;">
                        <tr style="display:flex; justify-content: space-between; padding-top: 15px; gap: 15px;">
                            <td style="width: 30%;">
                                <div>
                                    Default Error Page
                                </div>
                            </td>
                            <td style="width: 35%;">
                                <div>
                                    <input type="radio" name="default_error_page" id="404_page" value="1" @if (env('APP_ERROR_PAGE', '1') == 1) checked @endif />
                                    <label for="404_page">
                                        404 Page
                                    </label>
                                </div>
                            </td>
                            <td style="width: 35%;">
                                <div>
                                    <input type="radio" name="default_error_page" id="redirect_page" value="0" @if (env('APP_ERROR_PAGE', '1') == 0) checked @endif />
                                    <label for="redirect_page">
                                        Redirect to Main Page
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="saveDefaultErrorPage();">
                                    <span>
                                        <span>
                                            {{__('text.admin_currencies_currencies_form_submit')}}
                                        </span>
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                                        </svg>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<input type="hidden" id="invalid_password_repeat" value="{{__('text.admin_common_form_invalid_password_repeat')}}">

@endsection