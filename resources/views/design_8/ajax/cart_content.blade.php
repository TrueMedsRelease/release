<div class="page-cart__top-row">
	<h1 class="page-cart__title title">{{__('text.cart_cart_title')}}</h1>
</div>
<div class="page-cart__order order">
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
		<div class="order__line">
			<div class="order__row">
				<div class="order__package" id="{{ $product['product_id'] }}">
					{{ $product['pack_name'] }}
				</div>
				<div class="order__quantity">
					<div data-quantity class="quantity">
						<button data-quantity-minus type="button" class="quantity__button quantity__button_minus" onclick="down({{ $product['pack_id'] }})"></button>
						<div class="quantity__input">
							<input data-quantity-value id="{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
						</div>
						<button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})"></button>
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
				<button type="button" class="order__remove" onclick="remove({{ $product['pack_id'] }})">
				    <svg width="18" height="18">
						<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash") }}"></use>
					</svg>
				</button>
			</div>
            @if (!empty($product['upgrade_pack']))
                <p style="cursor: pointer;" onclick="upgrade({{ $product['pack_id'] }})" class="order__upgrade" data-upgrade>
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

	<div class="order__total total-line">
		<div class="total-line__info">
			<h3 class="total-line__label">{{__('text.cart_total_price_text')}}</h3>
            <div class="total_price">
                <span style="color: var(--red)"><span style="text-decoration: line-through">$649.80</span> -70%</span>
                <div class="price_with_save">
                    <span style="color: var(--green); text-align: center">{{__('text.cart_saving')}} $540</span>
                    <span>{{__('text.cart_only')}} $195.31</span>
                </div>
            </div>
		</div>
	</div>

	<div class="total-line__buttons actions-line">
		<a href="{{ route('home.index') }}" class="actions-line__continue button button--outline">{{__('text.cart_back_to_shop')}}</a>
        <a href="chekout.html" class="actions-line__pay button button--outline">
            <span>{{__('text.cart_pay_button')}}</span>
            <svg width="16" height="16">
                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-right") }}"></use>
            </svg>
        </a>
	</div>
</div>