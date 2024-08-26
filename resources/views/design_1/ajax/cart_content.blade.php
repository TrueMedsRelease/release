<div class="basket__wrapper">
<div class="basket__content">
    <h2 class="basket__title">
        {{__('text.cart_cart_title')}}
    </h2>
    <ul class="basket-benefits">
        <li class="basket-benefits__item basket-benefits__item--regular">
            <span class="basket-benefits__title">
                {{__('text.cart_free_regular')}}
            </span>
            <p class="basket-benefits__text">
                {{__('text.cart_sum_regular')}}
            </p>
        </li>
        <li class="basket-benefits__item basket-benefits__item--express">
            <span class="basket-benefits__title">
                {{__('text.cart_free_express')}}
            </span>
            <p class="basket-benefits__text">
                {{__('text.cart_sum_express')}}
            </p>
        </li>
        <li class="basket-benefits__item basket-benefits__item--secret">
            <span class="basket-benefits__title">
                <span class="basket-benefits__subtitle">
                    {{__('text.cart_secret1')}}
                </span>
                {{__('text.cart_secret2')}}
            </span>
            <p class="basket-benefits__text">
                {{__('text.cart_description_secret')}}
            </p>
        </li>
        <li class="basket-benefits__item basket-benefits__item--moneyback">
            <span class="basket-benefits__title">
                <span class="basket-benefits__subtitle">
                    {{__('text.cart_moneyback1')}}
                </span>
                {{__('text.cart_moneyback2')}}
            </span>
            <p class="basket-benefits__text">
                {{__('text.cart_description_moneyback')}}
            </p>
        </li>
    </ul>
</div>
<div class="order">
    <div class="order__content">
        <span class="order__title"  id = "scroll">
            {{__('text.cart_order_title_1')}}
        </span>
        <p style="text-align: center; padding-bottom:2%; margin-top:-1%;">{{__('text.cart_order_title_text')}}</p>
        <div class="order__top">
            <span class="order__top-name">
                {{__('text.cart_package')}}
            </span>
            <span class="order__top-name">
                {{__('text.cart_qty')}}
            </span>
            <span class="order__top-name">
                {{__('text.cart_per_pack')}}
            </span>
            <span class="order__top-name">
                {{__('text.cart_price')}}
            </span>
        </div>
        <form>
        <ul class="order__items">
            @foreach ($products as $product)
                @if ($product['product_id'] == 616)
                    <li class="order__item">
                        <div class="order__product" id = "{{ $product['product_id'] }}">
                        <span class="order__name">
                            {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}" class="product-about__characteristic-meaning--link">{{ $product['pack_name'] }}</a> --}}
                            {{ $product['name'] }}
                        </span>
                            <div class="quantity" data-quantity>
                                <button class="quantity__button quantity__button_plus" data-quantity-plus type="button" onclick="up({{ $product['pack_id'] }})">
                                    <span class="sr-only">Plus</span>
                                </button>
                                <div class="quantity__input">
                                    <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                </div>
                                <button class="quantity__button quantity__button_minus" data-quantity-minus type="button" onclick="down({{ $product['pack_id'] }})">
                                    <span class="sr-only">Minus</span>
                                </button>
                            </div>
                            <div class="order__pack">
                                <span class="order__only">${{ $product['price'] }}</span>
                            </div>
                            <div class="order__price">
                                <span class="order__only">${{ $product['price'] }}</span>
                            </div>
                            <button type="button" data-remove class="remove-button" onclick="remove({{ $product['pack_id'] }})">
                                x
                            </button>
                        </div>
                    </li>
                @else
                    <li class="order__item">
                        <div class="order__product" id = "{{ $product['product_id'] }}">
                        <span class="order__name">
                            {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}" class="product-about__characteristic-meaning--link">{{ $product['pack_name'] }}</a> --}}
                            {{ $product['pack_name'] }}
                        </span>
                            <div class="quantity" data-quantity>
                                <button class="quantity__button quantity__button_plus" data-quantity-plus type="button">
                                    <span class="sr-only">Plus</span>
                                </button>
                                <div class="quantity__input">
                                    <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                </div>
                                <button class="quantity__button quantity__button_minus" data-quantity-minus type="button">
                                    <span class="sr-only">Minus</span>
                                </button>
                            </div>
                            <div class="order__pack">
                                <div class="order__pack-top">
                                    <span class="order__price line-through">${{ $product['max_pill_price'] * $product['num'] }}</span>
                                    <span class="order__discount">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                </div>
                                <span class="order_only">{{__('text.cart_only')}} ${{ $product['price'] }}</span>
                            </div>
                            <div class="order__price">
                                <div class="order__price-top">
                                    <span class="order__price line-through">${{ $product['max_pill_price'] * $product['num'] * $product['q'] }}</span>
                                    <span class="order__discount">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                </div>
                                <span class="order__only">{{__('text.cart_only')}} ${{ $product['price'] * $product['q'] }}</span>
                            </div>
                            <button type="button" data-remove class="remove-button" onclick="remove({{ $product['pack_id'] }})">
                                x
                            </button>
                        </div>
                        @if (!empty($product['upgrade_pack']))
                            <p onclick="upgrade({{ $product['pack_id'] }})" class="order__text" data-upgrade>
                                {{__('text.cart_upgrade')}}<span class="order__text-accent"> {{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} ${{ $product['upgrade_pack']['price'] - $product['price'] }} </span>
                                {{__('text.cart_savei')}} ${{ $product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'] }}
                                @if ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $cart_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                    {{__('text.cart_get_regular')}}
                                @elseif ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                    {{__('text.cart_get_ems')}}
                                @endif
                            </p>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
        </form>
        {{-- {if $data.available_shippings}
            {if !$is_only_card}
                {include file="includes/shipping_selector.tpl"}
            {/if}
        {/if} --}}

        <form class="order-delivery" action="#">
            <div class="order-delivery__item">
                <input class="hidden" id="delivery-1" data-delivery type="radio" name="delivery" value="delivery-1" checked>
                <label class="visible" for="delivery-1">
                    <span class="sr-only">delivery-1</span>
                </label>
                <div class="order-delivery__content">
                    <div class="order-delivery__top">
                        <span class="order-delivery__name">
                            {{__('text.checkout_express')}}
                        </span>
                        <span>
                            {{-- {{__('text.checkout_free')}}
                            <span style="text-decoration: line-through;">$29.99</span> --}}
                            $29.99
                        </span>
                    </div>
                    <p class="order-delivery__text">
                        {{__('text.checkout_express_text')}}
                    </p>
                </div>
            </div>
            <div class="order-delivery__item">
                <input class="hidden" id="delivery-2" data-delivery type="radio" name="delivery" value="delivery-2">
                <label class="visible" for="delivery-2">
                    <span class="sr-only">delivery-2</span>
                </label>
                <div class="order-delivery__content">
                    <div class="order-delivery__top">
                        <span class="order-delivery__name">
                            {{__('text.checkout_regular')}}
                        </span>
                        <span>
                            {{__('text.checkout_free')}}
                            <span style="text-decoration: line-through;">$14.99</span>
                        </span>
                    </div>
                    <p class="order-delivery__text">
                        {{__('text.checkout_regular_text')}}
                    </p>
                </div>
            </div>
        </form>

        <form class="order-bonus" action="#">
            <div class="order-bonus__items">
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="0" type="radio" name="bonus" value="0" checked>
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">No Bonus</span>
                        </span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="11630" type="radio" name="bonus" value="11630">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">Free ED Pack</span>
                            <span class="bonus_price">Free</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 1 pills, Cialis 20 mg x 1 pills, Levitra 20 mg x 1 pills</span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="4576" type="radio" name="bonus" value="4576">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">Trial ED Pack</span>
                            <span class="bonus_price">$60</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 5 pills, Cialis 20 mg x 5 pills, Levitra 20 mg x 5 pills</span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="4577" type="radio" name="bonus" value="4577">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">Super ED Pack</span>
                            <span class="bonus_price">$90</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 10 pills, Cialis 20 mg x 10 pills, Levitra 20 mg x 10 pills</span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="4919" type="radio" name="bonus" value="4919">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">Extra ED Pack</span>
                            <span class="bonus_price">$120</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 20 pills, Cialis 20 mg x 20 pills, Levitra 20 mg x 20 pills</span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="11656" type="radio" name="bonus" value="11656">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">Mega ED Pack</span>
                            <span class="bonus_price">$150</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 30 pills, Cialis 20 mg x 30 pills, Levitra 20 mg x 30 pills</span>
                    </div>
                </label>
                <label class="order-bonus__item">
                    <input data-bonus class="hidden" id="11655" type="radio" name="bonus" value="11655">
                    <span class="visible"></span>
                    <div class="order-bonus__content">
                        <span class="order-bonus__name">
                            <span class="bonus_name">He&She ED Pack</span>
                            <span class="bonus_price">$100</span>
                        </span>
                        <span class="order-bonus__package">Viagra 100 mg x 30 pills, Female Viagra 100 mg x 30 pills</span>
                    </div>
                </label>
            </div>
        </form>

        <form class="gift_card" action="#">
            <div class="gift_block">
                <div class="gift_top_block__item">
                    <span class="visible gift"></span>
                    <div class="top_left_text">{{__('text.cart_add_gift')}}</div>
                </div>
                <div class="gift_bottom_block">
                    <div class="bottom_left_text">{{__('text.common_gift_card')}}</div>
                    <div>
                        <div class="select_gift">
                            <div class="select_header_gift">
                                <span class="select_current_gift" curr_packaging_id = "11631">$50</span>
                                <div class="select_icon">
                                    <img src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
                                </div>
                            </div>
                            <div class="select_body_gifts">
                                <div class="select_item_gift" packaging_id = "11631">$50</div>
                                <div class="select_item_gift" packaging_id = "11632">$100</div>
                                <div class="select_item_gift" packaging_id = "11633">$200</div>
                                <div class="select_item_gift" packaging_id = "11634">$300</div>
                                <div class="select_item_gift" packaging_id = "11635">$400</div>
                                <div class="select_item_gift" packaging_id = "11636">$500</div>
                            </div>
                        </div>
                    </div>
                    <div class="button_add_gift">{{__('text.common_add_to_cart_text_d2')}}</div>
                </div>
            </div>
        </form>

        <div class="order-total">
            <span class="order-total__title">{{__('text.cart_total_price_text')}}</span>
            <div class="order-total__content">
                <div class="order-total__price">
                    <span class="order-total__old line-through">$649.80</span>
                    <span class="order-total__discount">-70%</span>
                </div>
                <span class="order-total__saving">{{__('text.cart_saving')}} $540</span>
                <span class="order-total__only">{{__('text.cart_only')}} $195.31</span>
            </div>
        </div>

        <a href="CHECKOUT" class="order__pay">{{__('text.cart_pay_button')}}<span>→</span></a>
    </div>
    <a class="order__continue" href="{{ route('home.index') }}">{{__('text.cart_back_to_shop')}}
    </a>

</div>
</div>