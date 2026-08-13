@php
    if (!function_exists('asset_ver')) {
        function asset_ver(string $path): string {
            static $mtimes = [];
            $full = public_path($path);
            if (!isset($mtimes[$path])) {
                $mtimes[$path] = is_file($full) ? filemtime($full) : null;
            }
            $url = asset($path);
            $v = $mtimes[$path] ?? time();
            return $url . '?v=' . $v;
        }
    }

    $payment_type_current = session('form.payment_type', 'mastercard');
    $billing_country_current = session('form.billing_country', session('location.country', 'US'));
    $shipping_country_current = session('form.shipping_country', session('location.country', 'US'));
@endphp
<form id="order_form" class="form order-form cart-form" data-checkout-form method="post" action="{{ route('checkout.order') }}">
    @csrf
    <input type="hidden" id="app_insur_on" value="{{ env('APP_INSUR_ON', 1) }}">
    <input type="hidden" id="app_google_on" @if (env('APP_GOOGLE_ON', 0) && session('location.country') != 'US' && $service_enable) value="1" @else value="0" @endif>
    <input type="hidden" id="app_sepa_on" @if (env('APP_SEPA_ON', 0) && in_array(session('location.country'), ["AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "IS", "LI", "CH", "GB", "MC", "SM", "AD", "VA"])) value="1" @else value="0" @endif>
    <input type="hidden" id="app_zelle_on" @if (env('APP_ZELLE_ON', 0) && (session('location.country') == "US" || session('form.billing_country') == "US")) value="1" @else value="0" @endif>
    <input type="hidden" id="success_trans" value="0">
    <fieldset class="form__fieldset">
        <div class="form__field custom-field">
            <div class="panel">
                <div class="panel__header">
                    <h2 class="panel__title">{{ __('text.cart_order_title_1') }}</h2>
                </div>
                <table class="table cart-table">
                    <thead>
                    <tr>
                        <th>{{__('text.cart_package')}}</th>
                        <th>{{__('text.cart_qty')}}</th>
                        <th>{{__('text.cart_per_pack')}}</th>
                        <th>{{__('text.cart_price')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($products as $product)
                        <tr class="cart-item-content">
                            <td class="cart-item__brand" data-caption="Package">
                                <span class="cart-item__brand-name">
                                    <span>{{ $product['name'] }}</span>
                                    @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                        {{ $product['dosage_name'] }}
                                    @endif
                                </span>
                            </td>
                            <td class="cart-item__qty" data-caption="QTY">
                                <div class="qty-input">
                                    <label class="qty-input__label">
                                        <span class="qty-input__qty-field">{{ $product['q'] }}</span>
                                    </label>
                                </div>
                            </td>
                            <td class="cart-item__pack-price" data-caption="Per Pack">
                                <span class="cart-item__price-wrapper">
                                    @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                        <span class="discount-price">
                                            <s>{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</s>
                                            <span class="discount-label">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                        </span>
                                    @endif
                                    <span class="price">@if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0) {!!__('text.product_only')!!} @endif {{ $Currency::convert($product['price'],true) }} </span>
                                </span>
                            </td>
                            <td class="cart-item__total-price" data-caption="Price">
                                <span class="cart-item__price-wrapper">
                                    @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                        <span class="discount-price">
                                            <s>{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</s>
                                            <span class="discount-label">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                        </span>
                                    @endif
                                    <span class="price">{{ $Currency::convert($product['price'] * $product['q'], true) }}</span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    @if (!$card_only)
                        <tr>
                            <td colspan="4">
                                <div class="order-option">
                                    <div class="order-option__checkbox">
                                        <input class="form__checkbox" id="shipping-insurance" type="checkbox" name="insurance" value="1" data-action="toggle-insurance" @if (session('cart_option.insurance', env('APP_INSUR_ON'))) checked="checked" @endif>
                                        <label class="form__label form__label--checkbox" for="shipping-insurance">
                                            <div class="form__label-title">{{ __('text.checkout_insurance') }}</div>
                                        </label>
                                    </div>
                                    <div class="order-option__price">
                                        {{ $Currency::convert(session('cart_option.insurance_price'), false, true) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="order-option">
                                    <div class="order-option__checkbox">
                                        <input class="form__checkbox" id="secret-packaging" type="checkbox" name="secret" value="1" data-action="toggle-secret" @if (session('cart_option.secret_package', env('APP_SECRET_ON'))) checked @endif>
                                        <label class="form__label form__label--checkbox" for="secret-packaging">
                                            <div class="form__label-title">{{ __('text.checkout_secret') }}</div>
                                        </label>
                                    </div>
                                    <div class="order-option__price">
                                        {{ $Currency::convert(session('cart_option.secret_price'), false, true) }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="order-options">
                                    @if ($shipping['ems'] != 0)
                                        <div class="order-option">
                                            <div class="order-option__checkbox">
                                                <input class="form__checkbox" id="regular-delivery" type="radio" name="delivery" value="ems" data-action="change-shipping" data-shipping-name="ems" data-shipping-price="{{ $product_total_check >= 300 ? 0 : $shipping['ems'] }}" @if (session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'ems') checked @endif>
                                                <label class="form__label form__label--checkbox" for="regular-delivery">
                                                    <div class="form__label-title">{{ __('text.checkout_express') }}</div>
                                                    <div class="form__label-text">{{ __('text.checkout_express_text') }}</div>
                                                </label>
                                            </div>
                                            <div class="order-option__price">
                                                @if ($product_total_check >= 300)
                                                    <span class="red_price">{{ $Currency::convert($shipping['ems']) }}</span>
                                                    <p style="color: var(--green); font-size: 1.4rem;">{{ __('text.checkout_free') }}</p>
                                                @else
                                                    <span>{{ $Currency::convert($shipping['ems']) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if ($shipping['regular'] != 0)
                                        <div class="order-option">
                                            <div class="order-option__checkbox">
                                                <input class="form__checkbox" id="express-delivery" type="radio" name="delivery" value="regular" data-action="change-shipping" data-shipping-name="regular" data-shipping-price="{{ $product_total_check >= 200 ? 0 : $shipping['regular'] }}" @if (session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'regular') checked @endif>
                                                <label class="form__label form__label--checkbox" for="express-delivery">
                                                    <div class="form__label-title">{{ __('text.checkout_regular') }}</div>
                                                    <div class="form__label-text">{{ __('text.checkout_regular_text') }}</div>
                                                </label>
                                            </div>
                                            <div class="order-option__price">
                                                @if ($product_total_check >= 200)
                                                    <span class="red_price">{{ $Currency::convert($shipping['regular']) }}</span>
                                                    <p style="color: var(--green); font-size: 1.4rem;">{{ __('text.checkout_free') }}</p>
                                                @else
                                                    <span>{{ $Currency::convert($shipping['regular']) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="order-total-wrapper">
                    @if (session('total.is_only_card', 0) != 1)
                        <div class="order-tabs" data-tabs>
                            <div class="tabs-buttons">
                                <button class="tabs-button @if (session('checked_bonus', 'discount') == 'discount' || session('total.is_only_card')) is-active @endif" data-action="switch-bonus" data-bonus="discount" type="button">{{ __('text.checkout_discount_code') }}</button>
                                <button class="tabs-button @if (session('checked_bonus', 'discount') == 'bonus_card') is-active @endif" data-action="switch-bonus" data-bonus="bonus_card" type="button">
                                    {{ __('text.checkout_bonus_card') }}
                                    <span class="icon" data-tooltip="{{ __('text.bonus_card_info') }}">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#question-circle") }}"></use>
                                        </svg>
                                    </span>
                                </button>
                                <button class="tabs-button @if (session('checked_bonus', 'discount') == 'gift_card') is-active @endif" data-action="switch-bonus" data-bonus="gift_card" type="button">{{ __('text.common_gift_card') }}</button>
                            </div>
                            <div class="tabs-items">
                                <div class="tabs-item @if (session('checked_bonus', 'discount') == 'discount' || session('total.is_only_card')) is-active @endif" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.checkout_coupon') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" name="coupon" placeholder="{{ __('text.checkout_coupon') }}" value="{{ session('coupon.coupon', '') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button" data-action="apply-coupon" aria-label="{{ __('text.checkout_coupon') }}">
                                                <svg width="24" height="24"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left"></use></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-item @if (session('checked_bonus', 'discount') == 'bonus_card') is-active @endif" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.checkout_bonus_card') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" name="bonus_card" placeholder="{{ __('text.checkout_bonus_card') }}" value="{{ session('bonus_card.card_number', '') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button" data-action="apply-bonus-card" aria-label="{{ __('text.checkout_bonus_card') }}">
                                                <svg width="24" height="24"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left"></use></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-item @if (session('checked_bonus', 'discount') == 'gift_card') is-active @endif" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.common_gift_card') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" name="gift_card" placeholder="{{ __('text.common_gift_card') }}" value="{{ session('gift_card.gift_card_code', '') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button" data-action="apply-gift-card" aria-label="{{ __('text.common_gift_card') }}">
                                                <svg width="24" height="24"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left"></use></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bonuses-block">
                                @if (session('checked_bonus', 'discount') == 'bonus_card' && session('bonus_card'))
                                    <div class="bonus-info">
                                        <div class="bottom_bonuses_block_head">
                                            <div>{{ __('text.checkout_your_bonus_card') }}</div>
                                            <button class="link" type="button" data-action="forget-bonus" data-forget="bonus_card" aria-label="remove">
                                                <svg width="14" height="14"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-close"></use></svg>
                                            </button>
                                        </div>
                                        <div class="bottom_bonuses_block_text"><div>{{ __('text.checkout_available_amount') }} - <b>{{ $Currency::convert(session('bonus_card.balance'), true) }}</b></div></div>
                                        <div class="bottom_bonuses_block_text">{{ __('text.checkout_bonus_text1') }} <span style="color:#ED4C54">-{{ $Currency::convert(session('total.bonus_card_discount'), true) }}</span></div>
                                    </div>
                                @endif
                                @if (session('checked_bonus', 'discount') == 'discount' && session('coupon'))
                                    <div class="bonus-info">
                                        <div class="bottom_bonuses_block_head">
                                            <div>{{ __('text.checkout_your_discount_code') }}</div>
                                            <button class="link" type="button" data-action="forget-bonus" data-forget="discount" aria-label="remove">
                                                <svg width="14" height="14"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-close"></use></svg>
                                            </button>
                                        </div>
                                        <div class="bottom_bonuses_block_text"><div>{{ __('text.checkout_discount2') }} - <b>{{ round(session('coupon.percent')) }}%</b></div></div>
                                    </div>
                                @endif
                                @if (session('checked_bonus', 'discount') == 'gift_card' && session('gift_card'))
                                    <div class="bonus-info">
                                        <div class="bottom_bonuses_block_head">
                                            <div>Your Gift Card</div>
                                            <button class="link" type="button" data-action="forget-bonus" data-forget="gift_card" aria-label="remove">
                                                <svg width="14" height="14"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-close"></use></svg>
                                            </button>
                                        </div>
                                        <div class="bottom_bonuses_block_text"><div>{{ __('text.checkout_available_amount') }} - <b>{{ $Currency::convert(session('gift_card.gift_card_balance'), true) }}</b></div></div>
                                        <div class="bottom_bonuses_block_text">{{ __('text.checkout_bonus_text3') }} <span style="color:#ED4C54">-{{ $Currency::convert(session('total.gift_card_discount'), true) }}</span></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="order-total">
                        <a class="button button--gray" href="{{ route('cart.index') }}">{{ __('text.checkout_edit') }}</a>
                        @php
                            $total_discount = 0;
                            foreach ($products as $product) {
                                if ($product['dosage'] != '1card') {
                                    $total_discount += $product['max_pill_price'] * $product['num'] * $product['q'];
                                } else {
                                    $total_discount += $product['price'];
                                }
                            }

                            $total_discount_product = ceil($total_discount);

                            $total_discount += session('cart_option.bonus_price');
                            // $total_discount += $shipping[session('cart_option.shipping')];
                            $total_discount += session('cart_option.shipping_price');
                            $total_discount += session('total.coupon_discount');
                            $total_discount += session('total.bonus_card_discount');
                            $total_discount += session('cart_option.insurance_price');
                            $total_discount += session('cart_option.secret_price');

                            $saving = $total_discount - session('total.checkout_total') + session('total.gift_card_discount');
                        @endphp
                        <div class="order-total__title">{{ __('text.checkout_total') }}:</div>
                        <div class="order-total__price-wrapper">
                            @if (session('checked_bonus', 'discount') == 'gift_card' && session()->has('gift_card') && session('total.gift_card_discount', 0) > 0 && session('total.gift_card_discount', 0) >= session('total.checkout_total'))
                                <div class="order-total__price" style="color: var(--green);">
                                    <span class="price">{{ $Currency::convert(0, true) }}</span>
                                </div>
                            @else
                                @if ((int)$total_discount_product == ((int)session('total.product_total') - (int)session('total.bonus_total')))
                                    <div class="order-total__price">
                                        <span class="price">{{ session('total.checkout_total_in_currency') }}</span>
                                    </div>
                                @else
                                    <div class="discount-price__wrapper">
                                        <div class="discount-price">
                                            <s id="total_old">{{ $Currency::convert($total_discount) }}</s>
                                            <span class="discount-label" id="discount_text">
                                                @if (ceil(100 - (session('total.checkout_total') / $total_discount) * 100) < 0)
                                                    {{ ceil(100 - (session('total.checkout_total') / $total_discount) * 100) }}%
                                                @else
                                                    -{{ ceil(100 - (session('total.checkout_total') / $total_discount) * 100) }}%
                                                @endif
                                            </span>
                                        </div>
                                        @if ($saving > 0)
                                        <div class="order-total__savings">{{ __('text.checkout_savings') }}: <span id="saving">{{ $Currency::convert($saving) }}</span></div>
                                        @endif
                                    </div>
                                    <div class="order-total__price">
                                        <span class="price">{{ session('total.checkout_total_in_currency') }}</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel fieldset-panel--contact">
        @php
            $phoneCountry = strtolower(
                session('form.phone_code', session('location.country', 'US'))
            );

            $altPhoneCountry = strtolower(
                session('form.alt_phone_code', $phoneCountry)
            );
        @endphp
        <legend class="form-legend">{{ __('text.checkout_info') }}</legend>
        <fieldset class="form__fieldset fieldset--4-col">
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="phone">{{ __('text.checkout_wrong_phone') }}</span>
                <input class="form__text-input input-tel intl-phone" type="tel" id="phone" name="phone" placeholder="000 000 00 00" data-component="intl-tel" value="{{ session('form.phone', '') }}" required>
                <label class="form__label label-tel" for="phone">{{ __('text.checkout_phone') }}</label>
                <input type="hidden" id="phone_code" name="phone_code" value="{{ $phoneCountry }}">
            </div>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="alt_phone">{{ __('text.checkout_wrong_phone') }}</span>
                <input class="form__text-input input-tel intl-phone" type="tel" id="alt_phone" name="alt_phone" placeholder="000 000 00 00" data-component="intl-tel" value="{{ session('form.alt_phone', '') }}">
                <label class="form__label label-tel" for="alt_phone">{{ __('text.checkout_alt_phone') }}</label>
                <input type="hidden" id="alt_phone_code" name="alt_phone_code" value="{{ $altPhoneCountry }}">
            </div>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="email">{{ __('text.checkout_wrong_email') }}</span>
                <input class="form__text-input input-email" type="email" id="email" name="email" data-action="auth-email" value="{{ session('form.email', '') }}" required>
                <label class="form__label label-email" for="email">{{ __('text.checkout_email') }}</label>
            </div>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="alt_email">{{ __('text.checkout_wrong_email') }}</span>
                <input class="form__text-input input-email" type="email" id="alt_email" name="alt_email" value="{{ session('form.alt_email', '') }}">
                <label class="form__label label-email" for="alt_email">{{ __('text.checkout_email2') }}</label>
            </div>
        </fieldset>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel">
        <legend class="form-legend">{{ __('text.checkout_billing_address') }}</legend>
        <fieldset class="form__fieldset fieldset--3-col">
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="firstname">{{ __('text.checkout_required') }}</span>
                <input class="form__text-input input-text" type="text" id="firstname" name="firstname" value="{{ session('form.firstname') }}" required>
                <label class="form__label label-text" for="firstname">{{ __('text.checkout_name') }}</label>
            </div>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="lastname">{{ __('text.checkout_required') }}</span>
                <input class="form__text-input input-text" type="text" id="lastname" name="lastname" value="{{ session('form.lastname', '') }}" required>
                <label class="form__label label-text" for="lastname">{{ __('text.checkout_surname') }}</label>
            </div>
            <div class="form__field custom-field">
                <span class="poopuptext" data-error-for="billing_country">{{ __('text.checkout_required') }}</span>
                <div class="form__label">{{ __('text.checkout_country') }}</div>
                <div class="select-wrapper" data-placeholder="{{ __('text.checkout_select_country') }}">
                    <select class="select" id="billing_country" name="billing_country" data-component="custom-selector" data-action="change-country">
                        @foreach ($countries as $country)
                            <option value="{{ $country['country_iso2'] }}" @selected($country['country_iso2'] == $billing_country_current)>{{ $country['country_name'] }}</option>
                        @endforeach
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                    </span>
                </div>
            </div>
        </fieldset>
        <fieldset class="form__fieldset fieldset--3-col">
            @if (in_array($billing_country_current, array_keys($states)))
            <div class="form__field custom-field">
                <span class="poopuptext" data-error-for="billing_state">{{ __('text.checkout_required') }}</span>
                <div class="form__label">{{ __('text.checkout_state') }}</div>
                <div class="select-wrapper" data-placeholder="{{ __('text.checkout_select_state') }}">
                    <select class="select" id="billing_state" name="billing_state" data-component="custom-selector" data-search>
                        @foreach ($states[$billing_country_current] as $key => $state)
                            <option value="{{ $key }}" @selected($key == session('form.billing_state', session('location.state', '')))>{{ $state }}</option>
                        @endforeach
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                    </span>
                </div>
            </div>
            @endif
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="billing_city">{{ __('text.checkout_required') }}</span>
                <input class="form__text-input input-text" type="text" id="billing_city" name="billing_city" value="{{ session('form.billing_city', session('location.city', '')) }}" required>
                <label class="form__label label-text" for="billing_city">{{ __('text.checkout_city') }}</label>
            </div>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="billing_zip">{{ __('text.checkout_required') }}</span>
                <input class="form__text-input input-text" type="text" id="billing_zip" name="billing_zip" value="{{ session('form.billing_zip', session('location.postal', '')) }}" required>
                <label class="form__label label-text" for="billing_zip">{{ __('text.checkout_zip') }}</label>
            </div>
        </fieldset>
        <div class="form__field text-field">
            <span class="poopuptext" data-error-for="billing_address">{{ __('text.checkout_required') }}</span>
            <input class="form__text-input input-text" type="text" id="billing_address" name="billing_address" value="{{ session('form.billing_address', '') }}" required>
            <label class="form__label label-text" for="billing_address">{{ __('text.checkout_address') }}</label>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel fieldset-panel--shipping">
        <legend class="form-legend">{{ __('text.checkout_shipping_address') }}</legend>
        <div class="form__field custom-field">
            <div class="order-option__checkbox shipping-address-checkbox">
                <input class="form__checkbox" id="shipping-address" type="checkbox" name="address_match" value="false" data-action="toggle-shipping-address" @if (!filter_var(session('form.address_match', true), FILTER_VALIDATE_BOOLEAN)) checked @endif>
                <label class="form__label form__label--checkbox" for="shipping-address">
                    <div class="form__label-title">{{ __('text.checkout_shipping_info') }}</div>
                </label>
            </div>
        </div>
        <div class="shipping-address-fields" @if (filter_var(session('form.address_match', true), FILTER_VALIDATE_BOOLEAN)) hidden @endif>
            <fieldset class="form__fieldset fieldset--2-col">
                <div class="form__field custom-field">
                    <span class="poopuptext" data-error-for="shipping_country">{{ __('text.checkout_required') }}</span>
                    <div class="form__label">{{ __('text.checkout_country') }}</div>
                    <div class="select-wrapper" data-placeholder="{{ __('text.checkout_select_country') }}">
                        <select class="select" id="shipping_country" name="shipping_country" data-component="custom-selector">
                            @foreach ($countries as $country)
                                <option value="{{ $country['country_iso2'] }}" @selected($country['country_iso2'] == $shipping_country_current)>{{ $country['country_name'] }}</option>
                            @endforeach
                        </select>
                        <span class="icon select-wrapper__chevron">
                            <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                        </span>
                    </div>
                </div>
                @if (in_array($shipping_country_current, array_keys($states)))
                <div class="form__field custom-field">
                    <span class="poopuptext" data-error-for="shipping_state">{{ __('text.checkout_required') }}</span>
                    <div class="form__label">{{ __('text.checkout_state') }}</div>
                    <div class="select-wrapper" data-placeholder="{{ __('text.checkout_select_state') }}">
                        <select class="select" id="shipping_state" name="shipping_state" data-component="custom-selector" data-search>
                            @foreach ($states[$shipping_country_current] as $key => $state)
                                <option value="{{ $key }}" @selected($key == session('form.shipping_state', session('location.state', '')))>{{ $state }}</option>
                            @endforeach
                        </select>
                        <span class="icon select-wrapper__chevron">
                            <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                        </span>
                    </div>
                </div>
                @endif
            </fieldset>
            <fieldset class="form__fieldset fieldset--2-col">
                <div class="form__field text-field">
                    <span class="poopuptext" data-error-for="shipping_city">{{ __('text.checkout_required') }}</span>
                    <input class="form__text-input input-text" type="text" id="shipping_city" name="shipping_city" value="{{ session('form.shipping_city', session('location.city', '')) }}">
                    <label class="form__label label-text" for="shipping_city">{{ __('text.checkout_city') }}</label>
                </div>
                <div class="form__field text-field">
                    <span class="poopuptext" data-error-for="shipping_zip">{{ __('text.checkout_required') }}</span>
                    <input class="form__text-input input-text" type="text" id="shipping_zip" name="shipping_zip" value="{{ session('form.shipping_zip', session('location.postal', '')) }}">
                    <label class="form__label label-text" for="shipping_zip">{{ __('text.checkout_zip') }}</label>
                </div>
            </fieldset>
            <div class="form__field text-field">
                <span class="poopuptext" data-error-for="shipping_address">{{ __('text.checkout_required') }}</span>
                <input class="form__text-input input-text" type="text" id="shipping_address" name="shipping_address" value="{{ session('form.shipping_address', '') }}">
                <label class="form__label label-text" for="shipping_address">{{ __('text.checkout_address') }}</label>
            </div>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel payment-information">
        <legend class="form-legend">{{ __('text.checkout_payment') }}</legend>
        <div class="form__field custom-field">
            <div class="form__label">{{ __('text.checkout_type') }}</div>
            <div class="select-wrapper" data-placeholder="{{ __('text.checkout_select_payment') }}" data-no-clear>

                @php
                    $paymentDisabled =
                        (
                            session('checked_bonus', 'discount') === 'gift_card'
                            && (float) session('total.gift_card_discount', 0) > 0
                            && (float) session('total.gift_card_discount', 0)
                                >= (float) session('total.checkout_total', 0)
                        )
                        ||
                        (
                            session('checked_bonus', 'discount') === 'bonus_card'
                            && (int) session('total.can_bonus_card', 0) === 1
                        );
                @endphp

                <select class="select payment-select" id="payment_type" name="payment_type" data-component="custom-selector" data-action="change-payment" @disabled($paymentDisabled)>
                    <option value="mastercard" data-asset="{{ asset('style_checkout/images/pay-systems/mastercard.svg') }}" @selected($payment_type_current == 'mastercard')>MasterCard</option>
                    @if (session('visa_error', false) == false)
                        <option value="visa" data-asset="{{ asset('style_checkout/images/pay-systems/visa.svg') }}" @selected($payment_type_current == 'visa')>Visa</option>
                    @endif
                    @if (in_array($billing_country_current, ['US', 'CA']) && session('total.checkout_total') < 350)
                        <option value="amex" data-asset="{{ asset('style_checkout/images/pay-systems/amex.svg') }}" @selected($payment_type_current == 'amex')>Amex</option>
                        <option value="discover" data-asset="{{ asset('style_checkout/images/pay-systems/discover.svg') }}" @selected($payment_type_current == 'discover')>Discover</option>
                    @endif
                    @if (env('APP_APPLE_PAY_ON', 0) && session('device') == 'apple' && session('wallet_available', true))
                        <option value="apple_pay" data-asset="{{ asset('style_checkout/images/icons/payment_type/apple_pay.svg') }}" @selected($payment_type_current == 'apple_pay')>Apple Pay</option>
                    @endif
                    @if (env('APP_GOOGLE_PAY_ON', 0) && session('device') == 'android' && session('wallet_available', true))
                        <option value="google_pay" data-asset="{{ asset('style_checkout/images/icons/payment_type/google_pay.svg') }}" @selected($payment_type_current == 'google_pay')>Google Pay</option>
                    @endif
                    @if (env('APP_OPEN_BANKING_ON', 0) && session('open_banking_available', true) && in_array($billing_country_current, ["AT", "BE", "BG", "CZ", "DK", "EE", "FI", "FR", "DE", "HU", "IE", "IT", "LV", "LT", "LU", "NL", "PL", "PT", "RO", "SK", "ES", "SE", "NO", "CH", "GB"]))
                        <option value="revolut" data-asset="{{ asset('style_checkout/images/icons/revolut.svg') }}" @selected($payment_type_current == 'revolut')>Revolut -5% extra off</option>
                    @endif
                    @if (env('APP_OPEN_BANKING_ON', 0) && session('open_banking_available', true) && in_array($billing_country_current, ["AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "IS", "LI", "CH", "GB", "MC", "SM", "AD", "VA"]))
                        <option value="open_banking" data-asset="{{ asset('style_checkout/images/icons/de_rotating_40x40.gif') }}" @selected($payment_type_current == 'open_banking')>Instant Bank Transfer -5% extra off</option>
                    @endif
                    @if (env('APP_ZELLE_ON', 0) && (session('location.country') == "US" || $billing_country_current == "US"))
                        <option value="zelle" data-asset="{{ asset('style_checkout/images/icons/payment_type/zelle.svg') }}" @selected($payment_type_current == 'zelle')>ZELLE</option>
                    @endif
                    @if (env('APP_PAYPAL_ON', 0) && $service_enable && session('paypal_limit', 'none') != 'none')
                        <option value="paypal" data-asset="{{ asset('style_checkout/images/icons/payment_type/paypal.svg') }}" @selected($payment_type_current == 'paypal')>Paypal</option>
                    @endif
                    @if (env('APP_SEPA_LOCAL_ON', 0) && in_array($billing_country_current, ["AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "IS", "LI", "CH", "GB", "MC", "SM", "AD", "VA"]))
                        <option value="sepa_local" data-asset="{{ asset('style_checkout/images/icons/payment_type/sepa.svg') }}" @selected($payment_type_current == 'sepa_local')>SEPA</option>
                    @endif
                    @if (env('APP_SEPA_ON', 0) == 1 && in_array($billing_country_current, ["AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "IS", "LI", "CH", "GB", "MC", "SM", "AD", "VA"]))
                        <option value="sepa" data-asset="{{ asset('style_checkout/images/icons/payment_type/sepa.svg') }}" @selected($payment_type_current == 'sepa')>SEPA</option>
                    @endif
                    @if (env('APP_USD_SWIFT_ON', 0))
                        <option value="usd_swift" data-asset="{{ asset('style_checkout/images/icons/payment_type/swift.svg') }}" @selected($payment_type_current == 'usd_swift')>SWIFT USD</option>
                    @endif
                    @if (env('APP_GBP_SWIFT_ON', 0) && $billing_country_current == "GB")
                        <option value="gbp_swift" data-asset="{{ asset('style_checkout/images/icons/payment_type/swift.svg') }}" @selected($payment_type_current == 'gbp_swift')>SWIFT GBP</option>
                    @endif
                    @if (env('APP_FPS_ON', 0) && $billing_country_current == "GB")
                        <option value="fps" data-asset="{{ asset('style_checkout/images/icons/payment_type/fps.svg') }}" @selected($payment_type_current == 'fps')>FPS</option>
                    @endif
                    @if (env('APP_DOMESTIC_ON', 0) && $billing_country_current == "AU")
                        <option value="domestic" data-asset="{{ asset('style_checkout/images/icons/payment_type/domestic.svg') }}" @selected($payment_type_current == 'domestic')>Domestic</option>
                    @endif
                    @if (env('APP_ACH_ON', 0) && $billing_country_current == "US")
                        <option value="ach" data-asset="{{ asset('style_checkout/images/icons/payment_type/ach_wire.svg') }}" @selected($payment_type_current == 'ach')>ACH / Wire</option>
                    @endif
                    @if (env('APP_INTERAC_ON', 0) && $billing_country_current == "CA")
                        <option value="interac" data-asset="{{ asset('style_checkout/images/icons/payment_type/interac.svg') }}" @selected($payment_type_current == 'interac')>Interac / EFT</option>
                    @endif
                    @if ($service_enable)
                        <option value="crypto" data-asset="{{ asset('style_checkout/images/icons/payment_type/crypto.svg') }}" @selected($payment_type_current == 'crypto')>{{ __('text.checkout_crypto') }} -15% extra off</option>
                    @endif
                    @if (session('checked_bonus', 'discount') == 'gift_card' && session('total.gift_card_discount', 0) > 0 && session('total.gift_card_discount', 0) >= session('total.checkout_total'))
                        <option value="gift_card" data-asset="{{ asset('style_checkout/images/icons/payment_type/gift.svg') }}" @selected($payment_type_current == 'gift_card')>{{ __('text.common_gift_card') }}</option>
                    @endif
                    @if (session('checked_bonus', 'discount') == 'bonus_card' && session('total.can_bonus_card', 0) == 1)
                        <option value="bonus_card" data-asset="{{ asset('style_checkout/images/icons/payment_type/bonus.svg') }}" @selected($payment_type_current == 'bonus_card')>{{ __('text.checkout_bonus_card') }}</option>
                    @endif
                </select>
                <span class="icon select-wrapper__chevron">
                    <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                </span>
            </div>
        </div>

        <div class="payment-information__card-content" @if (!in_array($payment_type_current, ['visa', 'mastercard', 'amex', 'discover'])) hidden @endif>
            <fieldset class="form__fieldset fieldset--2-col">
                <div class="form__field text-field">
                    <span class="poopuptext" data-error-for="card_numb">{{ __('text.checkout_wrong_card') }}</span>
                    <input class="form__text-input input-text" type="text" id="card_numb" name="card_numb" data-card value="{{ session('form.card_numb', '') }}">
                    <label class="form__label label-text" for="card_numb">{{ __('text.checkout_card_number') }}</label>
                </div>
                <div class="form__field text-field">
                    <span class="poopuptext" data-error-for="bank_name">{{ __('text.checkout_required') }}</span>
                    <input class="form__text-input input-text" type="text" id="bank_name" name="bank_name" value="{{ session('form.bank_name', '') }}">
                    <label class="form__label label-text" for="bank_name">{{ __('text.checkout_bank_name') }}</label>
                </div>
            </fieldset>
            <fieldset class="form__fieldset fieldset--2-col fieldset--mobile-row">
                <div class="form__field custom-field">
                    <div class="form__label">{{ __('text.checkout_exp_date') }}</div>
                    <div class="expiration-date-wrapper">
                        <div class="select-wrapper" data-placeholder="{{ __('text.checkout_month') }}">
                            <select class="select" id="card_month" name="card_month" data-component="custom-selector">
                                @foreach (range(1, 12) as $l)
                                    <option value="{{ $l < 10 ? '0' . $l : $l }}" @selected($l == session('form.card_month', ''))>{{ $l < 10 ? '0' . $l : $l }}</option>
                                @endforeach
                            </select>
                            <span class="icon select-wrapper__chevron">
                                <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                            </span>
                        </div>
                        <div class="select-wrapper" data-placeholder="{{ __('text.checkout_year') }}">
                            <select class="select" id="card_year" name="card_year" data-component="custom-selector">
                                @foreach (range(now()->year, now()->year + 15) as $l)
                                    <option value="{{ $l }}" @selected($l == session('form.card_year', ''))>{{ $l }}</option>
                                @endforeach
                            </select>
                            <span class="icon select-wrapper__chevron">
                                <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#chevron-down") }}"></use></svg>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form__field text-field">
                    <span class="poopuptext" data-error-for="cvc_2">{{ __('text.checkout_wrong_cvc') }}</span>
                    <input class="form__text-input input-text" type="text" id="cvc_2" name="cvc_2" data-card-cvc value="{{ session('form.cvc_2', '') }}">
                    <label class="form__label label-text" for="cvc_2">{{ __('text.checkout_cvv') }}</label>
                </div>
            </fieldset>
            <div class="form__field submit-field">
                <button class="button form__submit submit-button" type="submit" data-action="submit-order">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>

        <div class="payment-information__crypto-content" @if ($payment_type_current != 'crypto') hidden @endif>
            <div class="crypto-list" id="crypto-methods">
                <div class="crypto-item" data-value="BTC_BITCOIN"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/btc.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">BTC</div><div class="crypto-item__sub">BITCOIN</div></div></div>
                <div class="crypto-item" data-value="ETH_ETHEREUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/eth.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">ETH</div><div class="crypto-item__sub">ETHEREUM</div></div></div>
                <div class="crypto-item" data-value="USDT_TRON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_trx.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">TRON</div></div></div>
                <div class="crypto-item" data-value="ETH_ARBITRUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/eth.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">ETH</div><div class="crypto-item__sub">ARBITRUM</div></div></div>
                <div class="crypto-item" data-value="USDT_ETHEREUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_eth.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">ETHEREUM</div></div></div>
                <div class="crypto-item" data-value="BNB_BSC"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/bnb.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">BNB</div><div class="crypto-item__sub">BSC</div></div></div>
                <div class="crypto-item" data-value="TON_TON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/ton.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">TON</div><div class="crypto-item__sub">TON</div></div></div>
                <div class="crypto-item" data-value="LTC_LITECOIN"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/ltc.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">LTC</div><div class="crypto-item__sub">LITECOIN</div></div></div>
                <div class="crypto-item" data-value="ARB_ARBITRUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/arb.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">ARB</div><div class="crypto-item__sub">ARBITRUM</div></div></div>
                <div class="crypto-item" data-value="USDC_ARBITRUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc_arb.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">ARBITRUM</div></div></div>
                <div class="crypto-item" data-value="USDT_ARBITRUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_arb.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">ARBITRUM</div></div></div>
                <div class="crypto-item" data-value="TRX_TRON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/trx.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">TRX</div><div class="crypto-item__sub">TRON</div></div></div>
                <div class="crypto-item" data-value="SOL_SOLANA"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/sol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">SOL</div><div class="crypto-item__sub">SOLANA</div></div></div>
                <div class="crypto-item" data-value="USDT_BSC"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_bsc.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">BSC</div></div></div>
                <div class="crypto-item" data-value="USDC_ETHEREUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc_eth.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">ETHEREUM</div></div></div>
                <div class="crypto-item" data-value="USDC_BSC"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc_bsc.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">BSC</div></div></div>
                <div class="crypto-item" data-value="XRP_RIPPLE"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/xrp.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">XRP</div><div class="crypto-item__sub">RIPPLE</div></div></div>
                <div class="crypto-item" data-value="ETH_BASE"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/eth.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">ETH</div><div class="crypto-item__sub">BASE</div></div></div>
                <div class="crypto-item" data-value="USDC_SOLANA"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc_sol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">SOLANA</div></div></div>
                <div class="crypto-item" data-value="USDT_TON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_ton.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">TON</div></div></div>
                <div class="crypto-item" data-value="USDT_SOLANA"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_sol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">SOLANA</div></div></div>
                <div class="crypto-item" data-value="USDC_BASE"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">BASE</div></div></div>
                <div class="crypto-item" data-value="USDC_POLYGON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdc_pol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDC</div><div class="crypto-item__sub">POLYGON</div></div></div>
                <div class="crypto-item" data-value="DAI_ETHEREUM"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/dai.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">DAI</div><div class="crypto-item__sub">ETHEREUM</div></div></div>
                <div class="crypto-item" data-value="DOGE_DOGECOIN"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/doge.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">DOGE</div><div class="crypto-item__sub">DOGECOIN</div></div></div>
                <div class="crypto-item" data-value="POL_POLYGON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/pol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">POL</div><div class="crypto-item__sub">POLYGON</div></div></div>
                <div class="crypto-item" data-value="USDT_POLYGON"><img width="26" height="26" alt="" src="{{ asset('style_checkout/images/icons/usdt_pol.svg') }}"><div class="crypto-item__label"><div class="crypto-item__main">USDT</div><div class="crypto-item__sub">POLYGON</div></div></div>
            </div>
            <input type="hidden" id="crypto_selected" value="{{ session('crypto.currency', '') }}">
            <input type="hidden" id="pay_yes" value="0">
            <input type="hidden" id="invoiceId" value="{{ session('crypto.invoiceId', '') }}">
            <div id="requisites" @if (empty(session('crypto'))) hidden @endif>
                <div class="info_text_crypto" id="info_text_crypto" style="line-height: 24px; margin-bottom: 1.6rem;">
                    <div>{{__('text.checkout_crypto_text_1')}}</div>
                    <div>{{__('text.checkout_crypto_text_2')}}</div>
                    <ul style="padding-left: 40px; line-height: 24px">
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_0')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_1')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_2')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_3')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_4')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_5')}}</li>
                        <li style="list-style: disc">{{__('text.checkout_crypto_li_6')}}</li>
                    </ul>
                </div>
                <div class="crypto-loading" id="crypto-loading" hidden><div class="crypto-loading__spinner"></div></div>
                <div class="warning-field">
                    <div class="warning-field__icon"><span class="icon"><svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#timer") }}"></use></svg></span></div>
                    <div class="warning-field__text">{{ __('text.checkout_invoice') }}</div>
                    <div class="warning-field__timer" id="timer">30:00</div>
                </div>
                <div class="crypto-transaction-info">
                    <div class="crypto-transaction-info__qr">
                        <picture><img id="qr_code" src="{{ session('crypto.qr', '') }}" width="90" height="90" alt="QR"></picture>
                    </div>
                    <div class="crypto-transaction-info__row">
                        <div class="crypto-transaction-info__label">{{ __('text.checkout_amount') }}</div>
                        <div class="crypto-transaction-info__field copy-field">
                            <span class="crypto-amount copy-text" id="crypto_total">{{ session('crypto.amount', '') }}</span>
                            <s class="fiat-discount" id="crypto_price">{{ $Currency::convert(session('total.checkout_total', 0)) }}</s>
                            <span class="fiat-amount" id="crypto_discount_price">{{ session('crypto.crypto_total') ? $Currency::convert(session('crypto.crypto_total')) : '' }}</span>
                            <button class="copy-button" type="button" data-action="copy-text" aria-label="Copy">
                                <span class="icon"><svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#fi-sr-copy") }}"></use></svg></span>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="crypto-transaction-info__row">
                        <div class="crypto-transaction-info__label">{{ __('text.checkout_funds') }}</div>
                        <div class="crypto-transaction-info__field copy-field">
                            <span class="crypto-address copy-text" id="purse">{{ session('crypto.purse', '') }}</span>
                            <button class="copy-button" type="button" data-action="copy-text" aria-label="Copy">
                                <span class="icon"><svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#fi-sr-copy") }}"></use></svg></span>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="crypto-transaction-info__row crypto-transaction-info__row--payment-id">
                        <div class="crypto-transaction-info__field">{{ __('text.checkout_payment_id') }} <span id="invoce_p">{{ session('crypto.invoiceId', '') }}</span></div>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit" type="button" data-action="check-payment" id="paid" @if (empty(session('crypto'))) disabled @endif>
                        <span class="button-text">{{ __('text.checkout_paid') }}</span>
                    </button>
                </div>
            </div>
        </div>

         <div class="payment-information__local-content" @if (!in_array($payment_type_current, ['sepa_local', 'fps', 'domestic', 'ach', 'interac', 'usd_swift', 'gbp_swift'])) hidden @endif>
            <div class="content-local-payment" @if (!session()->has('local_payment')) hidden @endif>
                <div class="details-payment__rows">
                    <div class="details-payment__row details-payment__row--amount copy-field">
                        <div class="details-payment__data">
                            <h3 class="details-payment__title">Amount to pay</h3>
                            <div class="details-payment__cells">
                                <span style="font-weight:600">{{ session('local_payment.currency', '') }}</span>
                                <span class="copy-text" id="amount" style="font-weight:600">{{ session('local_payment.amount', '') }}</span>
                            </div>
                            <span class="details-payment__note">{{ __('text.local_payment_amount') }}</span>
                        </div>
                        <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                            {{-- <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg> --}}
                            <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#fi-sr-copy") }}"></use></svg>
                            <span class="button-text">{{ __('text.checkout_copy') }}</span>
                        </button>
                    </div>
                    <div class="details-payment__row details-payment__row--reference copy-field">
                        <div class="details-payment__data">
                            <h3 class="details-payment__title">Reference (Invoice number) - Enter this only</h3>
                            <div class="details-payment__cells">
                                <span class="copy-text" id="ref_id" style="font-weight:600">{{ session('local_payment.referer_id', '') }}</span>
                            </div>
                            <div class="details-payment__chips">
                                <span class="details-payment__chip details-payment__chip--do">
                                    <span class="details-payment__chip-icon">✓</span>
                                    <span class="details-payment__chip-label">DO:</span>
                                    <span class="details-payment__chip-value">{{ session('local_payment.referer_id') }}</span>
                                </span>
                                <span class="details-payment__chip details-payment__chip--dont">
                                    <span class="details-payment__chip-icon">×</span>
                                    <span class="details-payment__chip-label">DON'T:</span>
                                    <span class="details-payment__chip-value">INV-123456</span>
                                </span>
                                <span class="details-payment__chip details-payment__chip--dont">
                                    <span class="details-payment__chip-icon">×</span>
                                    <span class="details-payment__chip-label">DON'T:</span>
                                    <span class="details-payment__chip-value">123456 PRODUCT NAME</span>
                                </span>
                            </div>
                            <span class="details-payment__note">{{ __('text.local_payment_reference') }}</span>
                        </div>
                        <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                            {{-- <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg> --}}
                            <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#fi-sr-copy") }}"></use></svg>
                            <span class="button-text">{{ __('text.checkout_copy') }}</span>
                        </button>
                    </div>
                    @foreach (session('local_payment.instructions', []) as $title => $data)
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">{{ $title }}</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text" id="{{ strtolower($title) }}">{{ $data }}</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                {{-- <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg> --}}
                                <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#fi-sr-copy") }}"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit" type="button" data-action="process-local-payment">
                        <span class="button-text">{{ __('text.checkout_paid') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="payment-information__paypal-content" @if ($payment_type_current != 'paypal') hidden @endif>
            <div class="details-payment__row">
                <div class="details-payment__data" style="text-align: center;">{{ __('text.checkout_sepa_text') }}</div>
            </div>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-paypal">
                    <span class="button-text">{{ __('text.checkout_sepa_button') }}</span>
                </button>
            </div>
        </div>

        @if (env('APP_SEPA_ON', 0) == 1 && in_array(session('location.country'), ["AT", "BE", "BG", "HR", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "GR", "HU", "IE", "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "NO", "IS", "LI", "CH", "GB", "MC", "SM", "AD", "VA"]))
            <div class="payment-information__sepa-content" @if ($payment_type_current != 'sepa') hidden @endif>
                <div class="content-sepa" id="sepa_requisites">
                    <div class="details-payment__rows" style="margin-bottom: 30px;">
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">{{ __('text.checkout_amount') }}:</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text" id="sepa_total">{{ $Currency::ConvertInEur(session('total.checkout_total', 0)) }}</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">Bank:</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text">ING Romania</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">{{ __('text.checkout_sepa_account_number') }}</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text">RO30INGB0000999915318999</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">{{ __('text.checkout_sepa_company') }}</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text">REXPRESS S.R.L.</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                        <div class="details-payment__row copy-field">
                            <div class="details-payment__data">
                                <h3 class="details-payment__title">{{ __('text.checkout_address') }}</h3>
                                <div class="details-payment__cells">
                                    <span class="copy-text">Intrare GHEORGHE SIMIONESCU, Nr 19, Apt B 26, 014155 Bucuresti Sectorul 1, Bucharest, Romania</span>
                                </div>
                            </div>
                            <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                                <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                                <span class="button-text">{{ __('text.checkout_copy') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit" type="button" data-action="process-sepa">
                        <span class="button-text">{{ __('text.checkout_sepa_button') }}</span>
                    </button>
                </div>
            </div>
        @endif

        @if (env('APP_GOOGLE_ON', 0) == 1 && session('location.country') != 'US' && $service_enable)
            <div class="payment-information__google-content" @if ($payment_type_current != 'google') hidden @endif>
                <div class="details-payment__row">
                    <div class="details-payment__data" style="text-align: center;">{{ __('text.checkout_sepa_text') }}</div>
                </div>
                <div style="display: flex; justify-content: center;">
                    <iframe src="https://r.express/m-pay/l2Bm75tKjX?amount={{ session('total.checkout_total_eur') }}&currency=EUR&country={{ session('location.country', 'US') }}&width=200&height=50&buttonColor=white&buttonRadius=20px&buttonLocale=en" style="border: 0" width="255" height="67"></iframe>
                </div>
            </div>
        @endif

        <div class="payment-information__zelle-content" @if ($payment_type_current != 'zelle') hidden @endif>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="get-zelle-data">
                    <span class="button-text">{{ __('text.checkout_sepa_button') }}</span>
                </button>
            </div>
            <div id="zelle_requisites" hidden>
                <div class="details-payment__rows">
                    <div class="details-payment__row copy-field">
                        <div class="details-payment__data">
                            <h3 class="details-payment__title">Order ID</h3>
                            <div class="details-payment__cells"><span class="copy-text" id="zelle_orderId"></span></div>
                        </div>
                        <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                            <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                            <span class="button-text">{{ __('text.checkout_copy') }}</span>
                        </button>
                    </div>
                    <div class="details-payment__row copy-field">
                        <div class="details-payment__data">
                            <h3 class="details-payment__title">Email</h3>
                            <div class="details-payment__cells"><span class="copy-text" id="zelle_email"></span></div>
                        </div>
                        <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                            <svg width="18" height="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                            <span class="button-text">{{ __('text.checkout_copy') }}</span>
                        </button>
                    </div>
                    <div class="details-payment__row copy-field">
                        <div class="details-payment__data">
                            <h3 class="details-payment__title">Recipient</h3>
                            <div class="details-payment__cells"><span class="copy-text" id="zelle_recipient"></span></div>
                        </div>
                        <button type="button" class="copy-button" data-action="copy-text" aria-label="Copy">
                            <svg width="18" depth="18"><use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy"></use></svg>
                            <span class="button-text">{{ __('text.checkout_copy') }}</span>
                        </button>
                    </div>
                </div>
                <div class="form__field submit-field">
                    <button class="button form__submit" type="button" data-action="process-zelle">
                        <span class="button-text">{{ __('text.checkout_paid') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="payment-information__bonus-card-content" @if ($payment_type_current != 'bonus_card') hidden @endif>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-bonus-card">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>

        <div class="payment-information__gift-card-content" @if ($payment_type_current != 'gift_card') hidden @endif>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-gift-card">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>

        <div class="payment-information__open-banking-content" @if (!in_array($payment_type_current, ['revolut', 'open_banking'])) hidden @endif>
            <div class="details-payment__row">
                <div class="details-payment__data" style="text-align: center;">{{ __('text.checkout_sepa_text') }}</div>
            </div>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-open-banking" data-payment-type="{{ $payment_type_current }}">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>

        <div class="payment-information__google-pay-content" @if (!in_array($payment_type_current, ['google_pay'])) hidden @endif>
            <div class="details-payment__row">
                <div class="details-payment__data" style="text-align: center;">{{ __('text.checkout_sepa_text') }}</div>
            </div>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-wallet" data-payment-type="{{ $payment_type_current }}">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>

        <div class="payment-information__apple-pay-content" @if (!in_array($payment_type_current, ['apple_pay'])) hidden @endif>
            <div class="details-payment__row">
                <div class="details-payment__data" style="text-align: center;">{{ __('text.checkout_sepa_text') }}</div>
            </div>
            <div class="form__field submit-field">
                <button class="button form__submit" type="button" data-action="process-wallet" data-payment-type="{{ $payment_type_current }}">
                    <span class="button-text">{{ __('text.checkout_place') }}</span>
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor"><use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego2#arrow-right") }}"></use></svg>
                    </span>
                </button>
            </div>
        </div>
    </fieldset>
</form>