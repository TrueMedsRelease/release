<div class="page__top-line top-line">
	<h1 class="top-line__title">{{__('text.cart_cart_title')}}</h1>
</div>
<div class="page__cart cart-page">
	<div class="cart-page__content">
		<div class="cart-page__order order">
			<div class="order__top-row">
				<h2 class="order__label">{{__('text.cart_order_title_1')}}</h2>
				<p style="text-align: center; margin-top:1%;">{{__('text.cart_order_title_text')}}</p>
			</div>
			<div class="order__line order__line--filled">
				<div class="order__row order__row--headline">
					<div class="order__package">{{__('text.cart_package')}}</div>
					<div class="order__quantity">{{__('text.cart_qty')}}</div>
					<div class="order__per-pack">{{__('text.cart_per_pack')}}</div>
					<div class="order__price">{{__('text.cart_price')}}</div>
					<div class="order__remove"></div>
				</div>
			</div>
            @foreach ($products as $product)
                @if ($product['product_id'] == 616)
                    <div class="order__line">
                        <div class="order__row">
                            <div class="order__package" id = "{{ $product['product_id'] }}">
                                <span class="details-product__links">
                                    {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}">{{ $product['name'] }}</a> --}}
                                    {{ $product['name'] }}
                                </span>
                            </div>
                            <div class="order__quantity">
                                <div data-quantity class="quantity">
                                    <button data-quantity-minus type="button" class="quantity__button quantity__button_minus" onclick="down({{ $product['pack_id'] }})">
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-minus") }}"></use>
                                        </svg>
                                    </button>
                                    <div class="quantity__input">
                                        <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                    </div>
                                    <button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})">
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-plus") }}"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="order__per-pack">
                                <div class="order__new-price">${{ $product['price'] }}</div>
                            </div>
                            <div class="order__price">
                                <div class="order__new-price">${{ $product['price'] }}</div>
                            </div>
                            <button type="button" data-remove class="order__remove" onclick="remove({{ $product['pack_id'] }})">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash-bin") }}"></use>
                                </svg>
                            </button>
                        </div>
                    </div>
				@else
                    <div class="order__line">
                        <div class="order__row">
                            <div class="order__package" id = "{{ $product['product_id'] }}">
                                <span class="details-product__links">
                                    {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}">{{ $product['pack_name'] }}</a> --}}
                                    {{ $product['pack_name'] }}
                                </span>
                            </div>
                            <div class="order__quantity">
                                <div data-quantity class="quantity">
                                    <button data-quantity-minus type="button" class="quantity__button quantity__button_minus" onclick="down({{ $product['pack_id'] }})">
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-minus") }}"></use>
                                        </svg>
                                    </button>
                                    <div class="quantity__input">
                                        <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                    </div>
                                    <button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})">
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-plus") }}"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="order__per-pack">
                                <div class="order__old-price">
                                    <span>${{ $product['max_pill_price'] * $product['num'] }}</span>
                                    -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                </div>
                                <div class="order__new-price">{{__('text.cart_only')}} ${{ $product['price'] }}</div>
                            </div>
                            <div class="order__price">
                                <div class="order__old-price">
                                    <span>${{ $product['max_pill_price'] * $product['num'] * $product['q'] }}</span>
                                    -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                </div>
                                <div class="order__new-price">{{__('text.cart_only')}} ${{ $product['price'] * $product['q'] }}</div>
                            </div>
                            <button type="button" data-remove class="order__remove" onclick="remove({{ $product['pack_id'] }})">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash-bin") }}"></use>
                                </svg>
                            </button>
                        </div>
						@if (!empty($product['upgrade_pack']))
                            <p onclick="upgrade({{ $product['pack_id'] }})" class="order__upgrade" data-upgrade>
                                {{__('text.cart_upgrade')}}<b>{{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} ${{ $product['upgrade_pack']['price'] - $product['price'] }}</b>
                                {{__('text.cart_savei')}} ${{ $product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'] }}
                                @if ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $cart_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                    <b>{{__('text.cart_get_regular')}}</b>
                                @elseif ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                    <b>{{__('text.cart_get_ems')}}</b>
                                @endif
                            </p>
                        @endif
					</div>
				@endif
			@endforeach

            <div class="order__line delivery-line">
                <div class="delivery-line__item">
                    <div class="checkbox">
                        <input {{--onclick = "Ship()"--}} class="checkbox__input" id="delivery-1" data-delivery type="radio" name="delivery" value="delivery-1">
                        <label for="delivery-1" class="checkbox__label">
                            <span class="checkbox__delivery delivery-item">
                                <span class="delivery-item__top">
                                    <span class="delivery-item__label">
                                        {{__('text.checkout_express')}}
                                    </span>
                                    <span class="delivery-item__price">
                                        {{-- {{__('text.checkout_free')}}
                                        <span style="text-decoration: line-through;">$29.99</span> --}}
                                        $29.99
                                    </span>
                                </span>
                                <span class="delivery-item__descr">
                                    {{__('text.checkout_express_text')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
                <div class="delivery-line__item">
                    <div class="checkbox">
                        <input {{--onclick = "Ship()"--}} class="checkbox__input" id="delivery-2" data-delivery type="radio" name="delivery" value="delivery-2">
                        <label for="delivery-2" class="checkbox__label">
                            <span class="checkbox__delivery delivery-item">
                                <span class="delivery-item__top">
                                    <span class="delivery-item__label">
                                        {{__('text.checkout_regular')}}
                                    </span>
                                    <span class="delivery-item__price">
                                        {{__('text.checkout_free')}}
                                        <span style="text-decoration: line-through;">$14.99</span>
                                    </span>
                                </span>
                                <span class="delivery-item__descr">
                                    {{__('text.checkout_regular_text')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="order__line bonus-line">
                <div class="bonus-line__inner">
                    <div class="bonus-line__items"  style="justify-content: left;">
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="0" class="checkbox__input" type="radio" value="0" name="bonus" checked>
                                <label for="0" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">No Bonus</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="11630" class="checkbox__input" type="radio" value="11630" name="bonus">
                                <label for="11630" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">Free ED Pack</span>
                                            <span class="bonus_price">Free</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 1 tablett, Cialis 20 mg x 1 tablett, Levitra 20 mg x 1 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="4576" class="checkbox__input" type="radio" value="4576" name="bonus">
                                <label for="4576" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">Trial ED Pack</span>
                                            <span class="bonus_price">$60</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 5 tablett, Cialis 20 mg x 5 tablett, Levitra 20 mg x 5 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="4577" class="checkbox__input" type="radio" value="4577" name="bonus">
                                <label for="4577" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">Super ED Pack</span>
                                            <span class="bonus_price">$90</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 10 tablett, Cialis 20 mg x 10 tablett, Levitra 20 mg x 10 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                            <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="4919" class="checkbox__input" type="radio" value="4919" name="bonus">
                                <label for="4919" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">Extra ED Pack</span>
                                            <span class="bonus_price">$120</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 20 tablett, Cialis 20 mg x 20 tablett, Levitra 20 mg x 20 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="11656" class="checkbox__input" type="radio" value="11656" name="bonus">
                                <label for="11656" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">Mega ED Pack</span>
                                            <span class="bonus_price">$150</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 30 tablett, Cialis 20 mg x 30 tablett, Levitra 20 mg x 30 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input {{--onclick = "Bonus()"--}} data-bonus  id="11655" class="checkbox__input" type="radio" value="11655" name="bonus">
                                <label for="11655" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">He&She ED Pack</span>
                                            <span class="bonus_price">$100</span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc">Viagra 100 mg x 30 tablett, Female Viagra 100 mg x 30 tablett</span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                        <div class="button_add_gift" onclick="addCard()">{{__('text.common_add_to_cart_text_d2')}}</div>
                    </div>
                </div>
            </form>

            <div class="order__total total-line">
                <h3 class="total-line__label">{{__('text.cart_total_price_text')}}</h3>
                <div class="total-line__data">
                    <div class="total-line__old-price"><span>$649.80</span> -70%</div>
                    <div class="total-line__new-price">
                        <div class="total-line__savings">{{__('text.cart_saving')}} $540</div>
                        <div class="total-line__digits">{{__('text.cart_only')}} $195.31</div>
                    </div>
                </div>
            </div>
            <div class="order__bottom actions-line">
                <a href="{{ route('home.index') }}" class="actions-line__continue">{{__('text.cart_back_to_shop')}}</a>
                <a href="chekout.html" class="actions-line__pay">
                    <span>{{__('text.cart_pay_button')}}</span>
                    {{-- <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                    </svg> --}}
                </a>
            </div>
        </div>
	</div>
    <aside class="cart-page__sidebar">
		<div class="cart-page__preference preference-page-cart">
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
						<svg width="24" height="24">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-01") }}"></use>
						</svg>
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
						<svg width="24" height="19">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-02") }}"></use>
						</svg>
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
						<svg width="24" height="22">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-03") }}"></use>
						</svg>
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_description_secret')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
						<svg width="24" height="21">
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-05") }}"></use>
						</svg>
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
			</div>
		</div>
	</aside>
</div>