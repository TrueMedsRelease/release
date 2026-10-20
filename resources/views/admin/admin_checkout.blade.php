@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__rows">
        <h3 class="payment-details__caption adm-section-title">Checkout Properties</h3>
        <form name="checkout_properties" id="checkout_properties" method="POST">
            <div class="adm-settings">
                <div class="adm-setting">
                    <div class="adm-setting__label">Default Shipping</div>
                    <div class="adm-choices">
                        <label class="adm-choice" for="ship_ems">
                            <input type="radio" name="default_shipping" id="ship_ems" value="ems" @if (env('APP_DEFAULT_SHIPPING', 'ems') == 'ems') checked @endif />
                            <span class="adm-choice__mark"></span>
                            <span class="adm-choice__name">Express Delivery</span>
                        </label>
                        <label class="adm-choice" for="ship_regular">
                            <input type="radio" name="default_shipping" id="ship_regular" value="regular" @if (env('APP_DEFAULT_SHIPPING', 'ems') == 'regular') checked @endif />
                            <span class="adm-choice__mark"></span>
                            <span class="adm-choice__name">Regular Delivery</span>
                        </label>
                    </div>
                </div>
                <div class="adm-setting">
                    <div class="adm-setting__label">Default Bonus</div>
                    <div class="adm-choices">
                        <label class="adm-choice adm-choice--muted" for="bonus_0">
                            <input type="radio" name="default_bonus" id="bonus_0" value="0" @if (env('APP_DEFAULT_BONUS', 0) == 0) checked @endif />
                            <span class="adm-choice__mark"></span>
                            <span class="adm-choice__name">No Bonus</span>
                        </label>
                        @foreach ($bonus as $product)
                            <label class="adm-choice" for="bonus_{{ $product->pack_id }}">
                                <input type="radio" name="default_bonus" id="bonus_{{ $product->pack_id }}" value="{{ $product->pack_id }}" @if (env('APP_DEFAULT_BONUS', 0) == $product->pack_id) checked @endif />
                                <span class="adm-choice__mark"></span>
                                <span class="adm-choice__name">{{ $product->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="adm-actions">
                <button type="button" class="payment-details__button button button--filled" onclick="saveCheckoutInfo();">
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

@endsection