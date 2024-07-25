<h1>Shopping cart</h1>
<div class="main__content">
    <form class="form cart-form form--flex">
        <fieldset class="form__fieldset form-panel">
            <legend>Only Today. Finale Sale. Big Discounts.
                <p style="font-size: 1.6rem">Low stock: Items in your cart are reserved for you for 30 minutes.</p>
            </legend>

            <div class="form__field">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th width="45.2">Package</th>
                            <th width="15.2%">QTY</th>
                            <th width="18.5%">Per Pack</th>
                            <th width="18.5%">Price</th>
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
                                                <td class="cart-item__caption" colspan="5" onclick="upgrade({{ $product['pack_id'] }})">Upgrade this pack to
                                                    <b>{{ $product['upgrade_pack']['num'] }} pills for only
                                                        ${{ $product['upgrade_pack']['price'] - $product['price'] }}</b>
                                                    and save ${{ $product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'] }}.
                                                    @if ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $cart_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                                        <b>Bonus: Free Regular Delivery!</b>
                                                    @elseif ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                                        <b>Bonus: Free Express Delivery!</b>
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
                            <div class="form-radio__title">Regular Delivery</div>
                            <div class="form-radio__text">Delivery takes а few weeks. Online tracking is not
                                available</div>
                            <div class="form-radio__price">Free</div>
                        </label></div>
                    <div class="form-radio-wrapper"><input class="form-radio-input" id="delivery-2" type="radio"
                            name="delivery"><label class="form-radio" for="delivery-2">
                            <div class="form-radio__title">Express Dellvery</div>
                            <div class="form-radio__text">Delivery takes а few days. Online tracking is available
                            </div>
                            <div class="form-radio__price">Only $29.99</div>
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
                    <div class="h3">Would you like to add a gift card to your order?</div><button class="button"
                        type="button">Add</button>
                </div>
                <div class="gift-card-balance">
                    <div class="h3">Gift Card</div>
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
                    <div class="cart-total__title h3">Total:</div>
                    <div class="cart-total__discount"><s>$649.80</s> -70%</div>
                    <div class="cart-total__savings">Savings: $540</div>
                    <div class="cart-total__price h3">Only $ 195.31</div>
                </div>
                <div class="cart-form__controls"><button class="button button--outline" type="button">Continue
                        shopping</button><button class="button cart-form__checkout" type="submit">Pay for order
                        <span class="icon"><svg width="1em" height="1em" fill="currentColor">
                                <use href="svg/icons/sprite.svg#arrow"></use>
                            </svg></span></button></div>
            </div>
        </fieldset>
    </form>
</div>
<aside class="main__aside main__aside--narrow">
    <div class="panel cart-panel">
        <div class="cart-panel-item">
            <div class="cart-panel-item__title">10% discount for next orders</div>
            <div class="cart-panel-item__text">For orders over $100</div>
        </div>
        <div class="cart-panel-item cart-panel-item--2">
            <div class="cart-panel-item__title">Free Regular Delivery</div>
            <div class="cart-panel-item__text">For orders over $200</div>
        </div>
        <div class="cart-panel-item cart-panel-item--3">
            <div class="cart-panel-item__title">Free Express Delivery</div>
            <div class="cart-panel-item__text">For orders over $300</div>
        </div>
        <div class="cart-panel-item cart-panel-item--4">
            <div class="cart-panel-item__title">Secret packaging: Available</div>
            <div class="cart-panel-item__text">There is no description on the package, and the description is left
                blank</div>
        </div>
        <div class="cart-panel-item cart-panel-item--5">
            <div class="cart-panel-item__title">Moneyback: 30 days</div>
            <div class="cart-panel-item__text">In case of any dissatisfaction or damage to the package, we will
                take
                care of it by shipping the medicines again or refunding your money</div>
        </div>
    </div>
</aside>
