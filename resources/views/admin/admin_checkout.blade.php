@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Checkout Properties</h3>
        <form name="checkout_properties" id="checkout_properties" method="POST">
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <tbody style="display: flex; gap: 15px; flex-direction: column;">
                    <tr style="display:flex; justify-content: space-between; padding-top: 15px; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Default Shipping
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_shipping" id="ship_ems" value="ems" @if (env('APP_DEFAULT_SHIPPING', 'ems') == 'ems') checked @endif />
                                <label for="ship_ems">
                                    Express Delivery
                                </label>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_shipping" id="ship_regular" value="regular" @if (env('APP_DEFAULT_SHIPPING', 'ems') == 'regular') checked @endif />
                                <label for="ship_regular">
                                    Regular Delivery
                                </label>
                            </div>
                        </td>
                    </tr>
                    <!-- <tr style="display:flex; justify-content: space-between; padding: 15px 0; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Secret Packaging
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_secret" id="secret_on" value="1" @if (env('APP_SECRET_ON', 1) == 1) checked @endif />
                                <label for="secret_on">
                                    Checked
                                </label>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_secret" id="secret_off" value="0" @if (env('APP_SECRET_ON', 1) == 0) checked @endif />
                                <label for="secret_off">
                                    Unchecked
                                </label>
                            </div>
                        </td>
                    </tr> -->
                    <!-- <tr style="display:flex; justify-content: space-between; padding-bottom: 15px; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Shipping Insurance
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_insur" id="insur_on" value="1" @if (env('APP_INSUR_ON', 1) == 1) checked @endif />
                                <label for="insur_on">
                                    Checked
                                </label>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="default_insur" id="insur_off" value="0" @if (env('APP_INSUR_ON', 1) == 0) checked @endif />
                                <label for="insur_off">
                                    Unchecked
                                </label>
                            </div>
                        </td>
                    </tr> -->
                    <tr style="display:flex; justify-content: space-between; padding-bottom: 15px; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Paypal
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="paypal_setting" id="paypal_on" value="1" @if (env('APP_PAYPAL_ON', 0) == 1) checked @endif />
                                <label for="paypal_on">
                                    Available
                                </label>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="paypal_setting" id="paypal_off" value="0" @if (env('APP_PAYPAL_ON', 0) == 0) checked @endif />
                                <label for="paypal_off">
                                    Unavailable
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="saveCheckoutInfo();">
                                <span>
                                    <span>
                                        {{__('text.admin_currencies_currencies_form_submit')}}
                                    </span>
                                    <svg width="20" height="20">
                                        <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
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

@endsection