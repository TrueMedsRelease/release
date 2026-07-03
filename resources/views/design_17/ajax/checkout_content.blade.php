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
@endphp
<form class="form order-form cart-form">
    <fieldset class="form__fieldset">
        <div class="form__field custom-field">
            <div class="panel">
                <div class="panel__header">
                    <h2 class="panel__title">{{ __('text.checkout_order') }}</h2>
                </div>
                <table class="table cart-table order-table">
                    <thead>
                        <tr>
                            <th>{{ __('text.checkout_package') }}</th>
                            <th>{{ __('text.checkout_qty') }}</th>
                            <th>{{ __('text.checkout_per_pack') }}</th>
                            <th>{{ __('text.checkout_price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4">
                                <div class="cart-item-wrapper">
                                    <table class="cart-item">
                                        @foreach ($products as $product)
                                            <tr class="cart-item-content">
                                                <td class="cart-item__brand">
                                                    <span class="cart-item__brand-name">{{ $product['pack_name'] }}</span>
                                                </td>
                                                <td class="cart-item__qty">{{ $product['q'] }}</td>
                                                <td class="cart-item__pack-price">
                                                    {{ $Currency::convert($product['price'], true) }}
                                                </td>
                                                <td class="cart-item__total-price">
                                                    @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                                        <span style="color: var(--color-red);text-decoration: line-through;font-weight: 500;">
                                                            {{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}
                                                        </span>
                                                    @endif
                                                    <span class="price">{{ $Currency::convert($product['price'] * $product['q'], true) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="order-option">
                                    <div class="order-option__checkbox">
                                        <input class="form__checkbox" id="shipping-insurance" type="checkbox" @if (session('cart_option.insurance', env('APP_INSUR_ON'))) checked="checked" @endif>
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
                                        <input class="form__checkbox" id="secret-packaging" type="checkbox" @if (session('cart_option.secret_package', env('APP_SECRET_ON'))) checked @endif>
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
                                                <input class="form__checkbox" id="regular-delivery" type="radio" name="delivery" @if (session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'ems') checked @endif
                                                onclick="change_shipping('ems', {{ $product_total_check >= 300 ? 0 : $shipping['ems'] }})">
                                                <label class="form__label form__label--checkbox" for="regular-delivery">
                                                    <div class="form__label-title">{{ __('text.checkout_express') }}</div>
                                                    <div class="form__label-text">{{ __('text.checkout_express_text') }}</div>
                                                </label>
                                            </div>
                                            <div class="order-option__price">
                                                <span>{{ $Currency::convert($shipping['ems']) }}</span>
                                                @if ($product_total_check >= 300)
                                                    <p style="color: var(--green);">{{__('text.checkout_free')}}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    @if ($shipping['regular'] != 0)
                                        <div class="order-option">
                                            <div class="order-option__checkbox">
                                                <input class="form__checkbox" id="express-dellvery" type="radio" name="delivery" @if (session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'regular') checked @endif
                                                onclick="change_shipping('regular', {{ $product_total_check >= 200 ? 0 : $shipping['regular'] }})">
                                                <label class="form__label form__label--checkbox" for="express-dellvery">
                                                    <div class="form__label-title">{{ __('text.checkout_regular') }}</div>
                                                    <div class="form__label-text">{{ __('text.checkout_regular_text') }}</div>
                                                </label>
                                            </div>
                                            <div class="order-option__price">
                                                <span>{{ $Currency::convert($shipping['regular']) }}</span>
                                                @if ($product_total_check >= 200)
                                                    <p style="color: var(--green);">{{__('text.checkout_free')}}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="order-total-wrapper">
                    @if (session('total.is_only_card', 0) != 1)
                        <div class="order-tabs" data-tabs>
                            <div class="tabs-buttons">
                                <button class="tabs-button is-active" data-tabs-button @if (session('checked_bonus', 'discount') == 'discount' || session('total.is_only_card')) aria-selected="true" @else aria-selected="false" @endif aria-selected="true" type="button">{{ __('text.checkout_discount_code') }}</button>
                                <button class="tabs-button" data-tabs-button @if (session('checked_bonus', 'discount') == 'bonus_card') aria-selected="true" @else aria-selected="false" @endif type="button">{{ __('text.checkout_bonus_card') }}
                                    <span class="icon" data-tooltip="{{ __('text.bonus_card_info') }}">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#question-circle") }}"></use>
                                        </svg>
                                    </span>
                                </button>
                                <button class="tabs-button" data-tabs-button @if (session('checked_bonus', 'discount') == 'gift_card') aria-selected="true" @else aria-selected="false" @endif type="button">{{ __('text.common_gift_card') }}</button>
                            </div>
                            <div class="tabs-items">
                                <div class="tabs-item is-active" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.checkout_coupon') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" placeholder="{{ __('text.checkout_coupon') }}" value="{{ session('coupon.coupon','') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button">
                                                <svg width="24" height="24">
                                                    <use
                                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                                    </use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-item" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.checkout_bonus_card') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" placeholder="{{ __('text.checkout_bonus_card') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button">
                                                <svg width="24" height="24">
                                                    <use
                                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                                    </use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tabs-item" data-tabs-item>
                                    <div class="tabs-panel" data-tabs-panel>
                                        <div class="discount-code">
                                            <div class="discount-code__title">{{ __('text.common_gift_card') }}:</div>
                                            <label>
                                                <input class="form__text-input" type="text" placeholder="{{ __('text.common_gift_card') }}" value="{{ session('gift_card.gift_card_code','') }}">
                                            </label>
                                            <button class="discount-code__button button" type="button">
                                                <svg width="24" height="24">
                                                    <use
                                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                                    </use>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="order-total">
                        {{-- <a class="button button--gray" href="cart.html">Edit order</a> --}}
                        <div class="order-total__title">{{ __('text.checkout_total') }}</div>

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

                        <div class="order-total__price-wrapper">
                            <div class="discount-price__wrapper">
                                <div class="discount-price">
                                    <s>{{ $Currency::convert($total_discount) }}</s>
                                    <span class="discount-label">-{{ ceil(100 - (session('total.checkout_total') / $total_discount) * 100) }}%</span>
                                </div>
                                <div class="order-total__savings">{{ __('text.checkout_savings') }}: {{ $Currency::convert($saving) }}</div>
                            </div>
                            <div class="order-total__price">
                                <span class="price">{{ session('total.checkout_total_in_currency') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel fieldset-panel--contact">
        <legend class="form-legend">Contact information</legend>
        <fieldset class="form__fieldset fieldset--4-col">
            <div class="form__field text-field">
                <input class="form__text-input input-tel intl-phone" type="tel" id="order-order-phone" name="order-order-phone" placeholder="000 000 00 00" required>
                <label class="form__label label-tel" for="order-order-phone">Mobile phone:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-tel intl-phone" type="tel" id="order-order-phone-alt" name="order-order-phone-alt" placeholder="000 000 00 00" required>
                <label class="form__label label-tel" for="order-order-phone-alt">Alternative mobile phone:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-email" type="email" id="order-order-email" name="order-order-email" required>
                <label class="form__label label-email" for="order-order-email">Email:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-email" type="email" id="order-order-alt-email" name="order-order-alt-email" required>
                <label class="form__label label-email" for="order-order-alt-email">Alternative Email:</label>
            </div>
        </fieldset>
        {{-- <div class="form__field custom-field">
            <p class="form__footnote">*Please enter the email address you've previously used in this checkout
            and login to get the 10% reorder discount</p>
        </div> --}}
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel">
        <legend class="form-legend">Billing address</legend>
        <fieldset class="form__fieldset fieldset--3-col">
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-name" name="order-order-name" required>
                <label class="form__label label-text" for="order-order-name">First name:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-last-name" name="order-order-last-name" required>
                <label class="form__label label-text" for="order-order-last-name">Last name:</label>
            </div>
            <div class="form__field custom-field">
                <div class="form__label">Country:</div>
                <div class="select-wrapper">
                    <select class="select">
                        <option value="1">United States</option>
                        <option value="2">Australia</option>
                        <option value="3">Germany</option>
                        <option value="4">Spain</option>
                        <option value="5">Russia</option>
                        <option value="6">United States</option>
                        <option value="7">Australia</option>
                        <option value="8">Germany</option>
                        <option value="9">Spain</option>
                        <option value="10">Russia</option>
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </fieldset>
        <fieldset class="form__fieldset fieldset--3-col">
            <div class="form__field custom-field">
                <div class="form__label">State/Province:</div>
                <div class="select-wrapper">
                    <select class="select">
                        <option value="">Select State/Province</option>
                        <option value="1">California</option>
                        <option value="2">Texas</option>
                        <option value="3">Florida</option>
                        <option value="4">New York</option>
                        <option value="5">Pennsylvania</option>
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-city" name="order-order-city" required>
                <label class="form__label label-text" for="order-order-city">City:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-postal" name="order-order-postal" required>
                <label class="form__label label-text" for="order-order-postal">Zip/postal code:</label>
            </div>
        </fieldset>
        <div class="form__field text-field">
            <input class="form__text-input input-text" type="text" id="order-order-address" name="order-order-address" required>
            <label class="form__label label-text" for="order-order-address">Address:</label>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel fieldset-panel--shipping">
        <legend class="form-legend">Shipping address</legend>
        <div class="form__field custom-field">
            <div class="order-option__checkbox shipping-address-checkbox">
                <input class="form__checkbox" id="shipping-address" type="checkbox" checked>
                <label class="form__label form__label--checkbox" for="shipping-address">
                    <div class="form__label-title">My shipping info is different from my billing info</div>
                </label>
            </div>
        </div>
        <fieldset class="form__fieldset fieldset--2-col">
            <div class="form__field custom-field">
                <div class="form__label">Country:</div>
                <div class="select-wrapper">
                    <select class="select">
                        <option value="1">United States</option>
                        <option value="2">Australia</option>
                        <option value="3">Germany</option>
                        <option value="4">Spain</option>
                        <option value="5">Russia</option>
                        <option value="6">United States</option>
                        <option value="7">Australia</option>
                        <option value="8">Germany</option>
                        <option value="9">Spain</option>
                        <option value="10">Russia</option>
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="form__field custom-field">
                <div class="form__label">State/Province:</div>
                <div class="select-wrapper">
                    <select class="select">
                        <option value="">Select State/Province</option>
                        <option value="1">California</option>
                        <option value="2">Texas</option>
                        <option value="3">Florida</option>
                        <option value="4">New York</option>
                        <option value="5">Pennsylvania</option>
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </fieldset>
        <fieldset class="form__fieldset fieldset--2-col">
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-shipping-city" name="order-order-shipping-city" required>
                <label class="form__label label-text" for="order-order-shipping-city">City:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-shipping-postal" name="order-order-shipping-postal" required>
                <label class="form__label label-text" for="order-order-shipping-postal">Zip/postal code:</label>
            </div>
        </fieldset>
        <div class="form__field text-field">
            <input class="form__text-input input-text" type="text" id="order-order-shipping-address" name="order-order-shipping-address" required>
            <label class="form__label label-text" for="order-order-shipping-address">Address:</label>
        </div>
    </fieldset>
    <fieldset class="form__fieldset fieldset-panel payment-information">
        <legend class="form-legend">Payment information</legend>
        <div class="form__field custom-field">
            <div class="form__label">Type of Payment:</div>
            <div class="select-wrapper">
                <select class="select payment-select">
                    <option value="card">Card</option>
                    <option value="crypto" selected>Crypto</option>
                </select>
                <span class="icon select-wrapper__chevron">
                    <svg width="1em" height="1em" fill="currentColor">
                        <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                    </svg>
                </span>
            </div>
        </div>
        <fieldset class="form__fieldset fieldset--2-col payment-information__card-field">
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-card-number" name="order-order-card-number" required>
                <label class="form__label label-text" for="order-order-card-number">Card number:</label>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-cardholder" name="order-order-cardholder" required>
                <label class="form__label label-text" for="order-order-cardholder">Cardholder name:</label>
            </div>
        </fieldset>
        <fieldset class="form__fieldset fieldset--2-col fieldset--mobile-row payment-information__card-field">
            <div class="form__field custom-field"><!-- Cart--><!-- Order-->
                <div class="form__label">Expiration date:</div>
                <div class="expiration-date-wrapper">
                    <div class="select-wrapper">
                        <select class="select">
                            <option value="01" selected>01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                        <span class="icon select-wrapper__chevron">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                            </svg>
                        </span>
                    </div>
                    <div class="select-wrapper">
                        <select class="select">
                            <option value="2026" selected>2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                            <option value="2031">2031</option>
                            <option value="2032">2032</option>
                            <option value="2033">2033</option>
                            <option value="2034">2034</option>
                        </select>
                        <span class="icon select-wrapper__chevron">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#chevron-down") }}"></use>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form__field text-field">
                <input class="form__text-input input-text" type="text" id="order-order-cvv-cvc" name="order-order-cvv-cvc" required>
                <label class="form__label label-text" for="order-order-cvv-cvc">CVV2/CVC2</label>
            </div>
        </fieldset>
        {{-- <div class="form__field custom-field payment-information__crypto-field hidden-field">
            <div class="crypto-methods">
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-1" type="radio"
                    name="crypto-method" checked><label class="crypto-radio" for="crypto-method-1">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#eth"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Ethereum</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-2" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-2">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#btc"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Bitcoin</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-3" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-3">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#usdt"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">USDT (TRC20)</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-4" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-4">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#usdt"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">USDT (ERC20)</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-5" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-5">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#ltc"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Litecoin</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-6" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-6">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#trx"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Tron</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-7" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-7">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#bnb"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Binance Coin</div>
                </label></div>
                <div class="crypto-method"><input class="crypto-radio-input" id="crypto-method-8" type="radio"
                    name="crypto-method"><label class="crypto-radio" for="crypto-method-8">
                    <div class="crypto-radio__icon"><span class="icon"><svg width="1em" height="1em"
                        fill="currentColor">
                        <use href="svg/icons/sprite.svg?vmxkaego#busd"></use>
                        </svg></span></div>
                    <div class="crypto-radio__text">Binance USD</div>
                </label></div>
            </div>
            <div class="warning-field">
                <div class="warning-field__icon">
                    <span class="icon">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#timer") }}"></use>
                        </svg>
                    </span>
                </div>
                <div class="warning-field__text">Invoice will be expired in</div>
                <div class="warning-field__timer">15:53</div>
            </div>
            <div class="crypto-transaction-info">
                <div class="crypto-transaction-info__qr">
                    <picture>
                        <source type="image/webp" srcset="img/layout/qr-90w.webp 1x,img/layout/qr-180w.webp 2x"><img
                        src="img/layout/qr-90w.png" srcset="img/layout/qr-90w.png 1x,img/layout/qr-180w.png 2x"
                        width="90" height="90" alt="QR">
                    </picture>
                </div>
                <div class="crypto-transaction-info__row">
                    <div class="crypto-transaction-info__label">Amount:</div>
                    <div class="crypto-transaction-info__field copy-field">
                        <span class="crypto-amount copy-text">0.04526841</span>
                        <s class="fiat-discount">$88.15</s>
                        <span class="fiat-amount">$74.93</span>
                        <button class="copy-button" type="button" aria-label="Copy">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#fi-sr-copy") }}"></use>
                                </svg>
                            </span>
                            <span class="button-text">Copied</span>
                        </button>
                    </div>
                </div>
                <div class="crypto-transaction-info__row">
                    <div class="crypto-transaction-info__label">Send the funds to this address:</div>
                    <div class="crypto-transaction-info__field copy-field">
                        <span class="crypto-address copy-text">0xbcec7dc127978e0733ef40cd3255ba54a450b87c</span>
                        <button class="copy-button" type="button" aria-label="Copy">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#fi-sr-copy") }}"></use>
                                </svg>
                            </span>
                            <span class="button-text">Copied</span>
                        </button>
                    </div>
                </div>
                <div class="crypto-transaction-info__row crypto-transaction-info__row--payment-id">
                    <div class="crypto-transaction-info__field">
                        Your payment ID: 0712bf01-c8ef-43bd-8343-210e37c27eb4
                    </div>
                </div>
            </div>
        </div> --}}
    </fieldset>
    <div class="form__field submit-field">
        <button class="button form__submit submit-button" type="submit">
            <span class="button-text">Place the order</span>
            <span class="icon">
                <svg width="1em" height="1em" fill="currentColor">
                    <use href="{{ asset("$design/svg/icons/sprite.svg?vmxkaego#arrow-right") }}"></use>
                </svg>
            </span>
        </button>
    </div>
</form>