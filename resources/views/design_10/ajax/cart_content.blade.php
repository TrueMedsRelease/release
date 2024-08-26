<h1>{{__('text.common_cart_text')}}</h1>
<div class="main__content">
    <form class="form cart-form form--flex">
        <fieldset class="form__fieldset form-panel">
            <legend>{{__('text.cart_order_title_1')}}
                <p style="font-size: 1.6rem">{{__('text.cart_order_title_text')}}</p>
            </legend>

            <div class="form__field">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th width="45.2">{{__('text.cart_package')}}</th>
                            <th width="15.2%">{{__('text.cart_qty')}}</th>
                            <th width="18.5%">{{__('text.cart_per_pack')}}</th>
                            <th width="18.5%">{{__('text.cart_price')}}</th>
                            <th width="2.6%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="cart-item-wrapper" colspan="5">
                                    <table class="cart-item">
                                        <tr class="cart-item-content">
                                            <td class="cart-item__brand" width="45.2%">
                                                <span class="cart-item__brand-wrapper">
                                                    {{ $product['pack_name'] }}</span>
                                            </td>
                                            <td class="cart-item__qty" width="15.2%" data-caption="QTY:">
                                                <div class="qty-input">
                                                    <button class="qty-input__minus" type="button"
                                                        onclick="down({{ $product['pack_id'] }})">-</button>
                                                    <label class="qty-input__label">
                                                        <input class="qty-input__qty-field" inputmode="numeric"
                                                            type="text" name="quantity" size="1"
                                                            value="{{ $product['q'] }}">
                                                    </label>
                                                    <button class="qty-input__plus" type="button"
                                                        onclick="up({{ $product['pack_id'] }})">+</button>
                                                </div>
                                            </td>
                                            <td class="cart-item__pack-price" width="18.5%" data-caption="Per Pack:">
                                                <span
                                                    class="discount-price"><s>${{ $product['max_pill_price'] * $product['num'] }}</s>
                                                    -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                                <span class="price">Only ${{ $product['price'] }} </span>
                                            </td>
                                            <td class="cart-item__total-price" width="18.5%" data-caption="Price:">
                                                <span
                                                    class="discount-price"><s>${{ $product['max_pill_price'] * $product['num'] * $product['q'] }}</s>
                                                    -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                                <span class="price">Only
                                                    ${{ $product['price'] * $product['q'] }}</span>
                                            </td>
                                            <td class="cart-item__remove" width="2.6%">
                                                <button class="icon-button" type="button" aria-label="Remove from cart"
                                                    onclick="remove({{ $product['pack_id'] }})">
                                                    <span class="icon">
                                                        <svg width="1em" height="1em" fill="currentColor">
                                                            <use
                                                                href="{{ asset("$design/svg/icons/sprite.svg") }}#trash">
                                                            </use>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>

                                            @if (!empty($product['upgrade_pack']))
                                                <td class="cart-item__caption" colspan="5" onclick="upgrade({{ $product['pack_id'] }})">{{__('text.cart_upgrade')}}
                                                    <b>{{ $product['upgrade_pack']['num'] }} pills {{__('text.cart_for_only')}}
                                                        ${{ $product['upgrade_pack']['price'] - $product['price'] }}</b>
                                                        {{__('text.cart_savei')}} ${{ $product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'] }}.
                                                    @if ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $cart_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                                        <b>{{__('text.cart_get_regular')}}</b>
                                                    @elseif ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                                        <b>{{__('text.cart_get_ems')}}</b>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </fieldset>
        <!-- Shipping -->
        <fieldset class="form__fieldset form-panel discount-panel">
            <div class="form__field">
                <div class="delivery-radios">
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="delivery-1" type="radio"
                            name="delivery" checked><label class="form-radio" for="delivery-1">
                            <div class="form-radio__title">{{__('text.checkout_regular')}}</div>
                            <div class="form-radio__text">{{__('text.checkout_regular_text')}}</div>
                            <div class="form-radio__price">{{__('text.checkout_free')}}</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="delivery-2" type="radio"
                            name="delivery"><label class="form-radio" for="delivery-2">
                            <div class="form-radio__title">{{__('text.checkout_express')}}</div>
                            <div class="form-radio__text">{{__('text.checkout_express_text')}}</div>
                            <div class="form-radio__price">{{__('text.checkout_only')}} $29.99</div>
                        </label></div>
                </div>
            </div>
        </fieldset>
        <!-- Bonus -->
        <fieldset class="form__fieldset form-panel">
            <div class="form__field">
                <div class="pack-radios">
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="pack-1" type="radio"
                            name="pack"><label class="form-radio" for="pack-1">
                            <div class="form-radio__title">No Select</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="pack-2" type="radio"
                            name="pack" checked><label class="form-radio" for="pack-2">
                            <div class="form-radio__title">Free ED Pack</div>
                            <div class="form-radio__text">Viagra 100 mg x 1 pills x Cialis 20 mg x 1 pills x
                                Levitra 20 mg x 1
                                pills</div>
                            <div class="form-radio__price">Free</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="pack-3" type="radio"
                            name="pack"><label class="form-radio" for="pack-3">
                            <div class="form-radio__title">Trial ED Pack</div>
                            <div class="form-radio__text">Viagra 100 mg x 5 pills + Cialis 20 mg x 5 pills +
                                Levitra 20mg x 5
                                pills</div>
                            <div class="form-radio__price">$60</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="pack-4" type="radio"
                            name="pack"><label class="form-radio" for="pack-4">
                            <div class="form-radio__title">Super ED Pack</div>
                            <div class="form-radio__text">Viagra 100 mg x 10 pills + Cialis 20 mg x 10 pills +
                                Levitra 20 mg x
                                10 pills</div>
                            <div class="form-radio__price">$90</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="pack-5" type="radio"
                            name="pack"><label class="form-radio" for="pack-5">
                            <div class="form-radio__title">Extra ED Pack</div>
                            <div class="form-radio__text">Viagra 100 mg x 20 pills + Cialis 20 mg x 20 pills +
                                Levitra 20 mg x
                                20 pills</div>
                            <div class="form-radio__price">$120</div>
                        </label></div>
                </div>
            </div>
        </fieldset>
        <!-- Gift Card -->
        <fieldset class="form__fieldset form-panel discount-panel">
            <div class="form__field">
                <div class="add-gift-card">
                    <div class="h3">{{__('text.cart_add_gift')}}</div>
                    <button class="button" type="button">{{__('text.common_add_to_cart_text_d2')}}</button>
                </div>
                <div class="gift-card-balance">
                    <div class="h3">{{__('text.common_gift_card')}}</div>
                    <div class="select-wrapper"><select class="select">
                            <option value="1">$50</option>
                            <option value="2">$100</option>
                            <option value="3">$250</option>
                            <option value="4">$500</option>
                        </select><span class="icon select-wrapper__chevron"><svg width="1em" height="1em"
                                fill="currentColor">
                                <use href="svg/icons/sprite.svg#chevron-down"></use>
                            </svg></span></div>
                </div>
            </div>
        </fieldset>
        <fieldset class="form__fieldset form-panel">
            <div class="form__field">
                <div class="cart-total">
                    <div class="cart-total__title h3">{{__('text.cart_total_price_text')}}</div>
                    <div class="cart-total__discount"><s>$649.80</s> -70%</div>
                    <div class="cart-total__savings">{{__('text.cart_saving')}} $540</div>
                    <div class="cart-total__price h3">{{__('text.cart_only')}} $195.31</div>
                </div>
                <div class="cart-form__controls">
                    <button class="button button--outline" type="button">{{__('text.cart_back_to_shop')}}</button>
                    <button class="button cart-form__checkout" type="submit">{{__('text.cart_pay_button')}}
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="svg/icons/sprite.svg#arrow"></use>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<aside class="main__aside main__aside--narrow">
    <div class="panel cart-panel">
        <div class="cart-panel-item">
            <div class="cart-panel-item__title">{{__('text.cart_discount1')}}</div>
            <div class="cart-panel-item__text">{{__('text.cart_discount2')}}</div>
        </div>
        <div class="cart-panel-item cart-panel-item--2">
            <div class="cart-panel-item__title">{{__('text.cart_free_regular')}}</div>
            <div class="cart-panel-item__text">{{__('text.cart_sum_regular')}}</div>
        </div>
        <div class="cart-panel-item cart-panel-item--3">
            <div class="cart-panel-item__title">{{__('text.cart_free_express')}}</div>
            <div class="cart-panel-item__text">{{__('text.cart_sum_express')}}</div>
        </div>
        <div class="cart-panel-item cart-panel-item--4">
            <div class="cart-panel-item__title">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</div>
            <div class="cart-panel-item__text">{{__('text.cart_description_secret')}}</div>
        </div>
        <div class="cart-panel-item cart-panel-item--5">
            <div class="cart-panel-item__title">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</div>
            <div class="cart-panel-item__text">{{__('text.cart_description_moneyback')}}</div>
        </div>
    </div>
</aside>
