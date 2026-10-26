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
                <div class="adm-settings">
                    <div class="adm-setting">
                        <div class="adm-setting__label">Default Error Page</div>
                        <div class="adm-choices">
                            <label class="adm-choice" for="404_page">
                                <input type="radio" name="default_error_page" id="404_page" value="1" @if (env('APP_ERROR_PAGE', '1') == 1) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">404 Page</span>
                            </label>
                            <label class="adm-choice" for="redirect_page">
                                <input type="radio" name="default_error_page" id="redirect_page" value="0" @if (env('APP_ERROR_PAGE', '1') == 0) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">Redirect to Main Page</span>
                            </label>
                        </div>
                    </div>
                    <div class="adm-setting">
                        <div class="adm-setting__label">Show Keywords and Description in meta</div>
                        <div class="adm-choices">
                            <label class="adm-choice" for="our_meta">
                                <input type="radio" name="default_meta" id="our_meta" value="1" @if (env('APP_DEFAULT_META', '1') == 1) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">Pharmacy Meta</span>
                            </label>
                            <label class="adm-choice" for="default_meta">
                                <input type="radio" name="default_meta" id="default_meta" value="0" @if (env('APP_DEFAULT_META', '1') == 0) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">Default Meta</span>
                            </label>
                        </div>
                    </div>
                    <div class="adm-setting">
                        <div class="adm-setting__label">Show Promo Coupon Popup</div>
                        <div class="adm-choices">
                            <label class="adm-choice" for="promo_coupon_on">
                                <input type="radio" name="promo_coupon_popup" id="promo_coupon_on" value="1" @if (env('APP_PROMO_COUPON_POPUP', '1') == 1) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">ON</span>
                            </label>
                            <label class="adm-choice" for="promo_coupon_off">
                                <input type="radio" name="promo_coupon_popup" id="promo_coupon_off" value="0" @if (env('APP_PROMO_COUPON_POPUP', '1') == 0) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">OFF</span>
                            </label>
                        </div>
                    </div>
                    <div class="adm-setting">
                        <div class="adm-setting__label">Show Seasonal Upsell</div>
                        <div class="adm-choices">
                            <label class="adm-choice" for="seasonal_upsell_on">
                                <input type="radio" name="seasonal_upsell" id="seasonal_upsell_on" value="1" @if (env('SEASONAL_UPSELL_ENABLED', '1') == 1) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">ON</span>
                            </label>
                            <label class="adm-choice" for="seasonal_upsell_off">
                                <input type="radio" name="seasonal_upsell" id="seasonal_upsell_off" value="0" @if (env('SEASONAL_UPSELL_ENABLED', '1') == 0) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">OFF</span>
                            </label>
                        </div>
                    </div>
                    <div class="adm-setting">
                        <div class="adm-setting__label">Show Bonus Card & Referral Program</div>
                        <div class="adm-choices">
                            <label class="adm-choice" for="bonus_promo_on">
                                <input type="radio" name="bonus_promo" id="bonus_promo_on" value="1" @if (env('APP_BONUS_PROMO', '1') == 1) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">ON</span>
                            </label>
                            <label class="adm-choice" for="bonus_promo_off">
                                <input type="radio" name="bonus_promo" id="bonus_promo_off" value="0" @if (env('APP_BONUS_PROMO', '1') == 0) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">OFF</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="adm-actions">
                    <button type="button" class="payment-details__button button button--filled" onclick="saveDefaultErrorPage();">
                        <span>
                            {{__('text.admin_currencies_currencies_form_submit')}}
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