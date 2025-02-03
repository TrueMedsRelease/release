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
                    @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                        {{$product['dosage_name']}}
                    @endif
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
                    @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) > 0)
                        <div class="order__old-price">
                            <span>{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</span>
                            -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                        </div>
                        <div class="order__new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'], true) }}</div>
                    @else
                        <div class="order__new-price">{{ $Currency::convert($product['price'], true) }}</div>
                    @endif
				</div>
				<div class="order__price">
                    @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) > 0)
                        <div class="order__old-price">
                            <span>{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</span>
                            -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                        </div>
                        <div class="order__new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'] * $product['q'], true) }}</div>
                    @else
                        <div class="order__new-price">{{ $Currency::convert($product['price'] * $product['q'], true) }}</div>
                    @endif
				</div>
				<button type="button" class="order__remove" onclick="remove({{ $product['pack_id'] }})">
				    <svg width="18" height="18">
						<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash") }}"></use>
					</svg>
				</button>
			</div>
            @if (!empty($product['upgrade_pack']))
                <p style="cursor: pointer;" onclick="upgrade({{ $product['pack_id'] }})" class="order__upgrade" data-upgrade>
                    {{__('text.cart_upgrade')}}<b>{{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} {{ $Currency::convert($product['upgrade_pack']['price'] - $product['price']) }}</b>
                    {{__('text.cart_savei')}} {{ $Currency::convert($product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price']) }}.
                    @if ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $product_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                        <b>{{__('text.cart_get_regular')}}</b>
                    @elseif ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                        <b>{{__('text.cart_get_ems')}}</b>
                    @endif
                </p>
            @endif
		</div>
	@endforeach

	<div class="order__line delivery-line">
        <div class="delivery-line__item">
            <div class="checkbox">
                <input class="checkbox__input" id="delivery-1" data-delivery type="radio" name="delivery" value="ems" @if (session('cart_option')['shipping'] == 'ems') checked @endif
                onchange="change_shipping('ems', {{ $product_total_check >= 300 ? 0 : $shipping['ems'] }})">
                <label for="delivery-1" class="checkbox__label">
                    <span class="checkbox__delivery delivery-item">
                        <span class="delivery-item__top">
                            <span class="delivery-item__label">
                                {{__('text.checkout_express')}}
                                <img loading="lazy" style="margin-left: 0.5rem;" src="/style_checkout/images/countrys/{{session('location')['country_name']}}.svg" alt="{{session('location')['country_name']}}}">
                            </span>
                            <span class="delivery-item__price">
                                @if ($product_total_check >= 300)
                                    {{__('text.cart_free')}} <span style="text-decoration: line-through; margin-left: 5px;"> {{ $Currency::convert($shipping['ems']) }}</span>
                                @else
                                    {{ $Currency::convert($shipping['ems']) }}
                                @endif
                            </span>
                        </span>
                        <span class="delivery-item__descr">
                            @if ($product_total_check >= 300)
                            @else
                                <p>{{__('text.shipping_ems_discount')}}</p>
                            @endif
                            {{__('text.checkout_express_text')}}
                        </span>
                    </span>
                </label>
            </div>
        </div>
        <div class="delivery-line__item">
            <div class="checkbox">
                <input class="checkbox__input" id="delivery-2" data-delivery type="radio" name="delivery" value="regular" @if (session('cart_option')['shipping'] == 'regular') checked @endif
                onchange="change_shipping('regular', {{ $product_total_check >= 200 ? 0 : $shipping['regular'] }})">
                <label for="delivery-2" class="checkbox__label">
                    <span class="checkbox__delivery delivery-item">
                        <span class="delivery-item__top">
                            <span class="delivery-item__label">
                                {{__('text.checkout_regular')}}
                                <img loading="lazy" style="margin-left: 0.5rem;" src="/style_checkout/images/countrys/{{session('location')['country_name']}}.svg" alt="{{session('location')['country_name']}}}">
                            </span>
                            <span class="delivery-item__price">
                                @if ($product_total_check >= 200)
                                    {{__('text.cart_free')}} <span style="text-decoration: line-through; margin-left: 5px;"> {{ $Currency::convert($shipping['regular']) }}</span>
                                @else
                                    {{ $Currency::convert($shipping['regular']) }}
                                @endif
                            </span>
                        </span>
                        <span class="delivery-item__descr">
                            @if ($product_total_check >= 200)
                            @else
                                <p>{{__('text.shipping_regular_discount')}}</p>
                            @endif
                            {{__('text.checkout_regular_text')}}
                        </span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    @php
        $total_discount = 0;

        foreach ($products as $product) {
            if($product['dosage'] != '1card')
            {
                $total_discount += $product['max_pill_price'] * $product['num'] * $product['q'];
            }
            else {
                $total_discount += $product['price'] * $product['q'];
            }
        }

        $total_discount_product = ceil($total_discount);

        $total_discount += session('cart_option')['bonus_price'];
        if (!$is_only_card) {
            $total_discount += $shipping[session('cart_option')['shipping']];
        }

        $discount = ceil(100 - (session('total')['all'] / $total_discount) * 100);

        if ($is_only_card_with_bonus) {
            $saving = 0;
            $discount = 0;
        } else {
            $saving = $total_discount - session('total')['all'];
        }

    @endphp

	<div class="order__total total-line">
		<div class="total-line__info">
			<h3 class="total-line__label">{{__('text.cart_total_price_text')}}</h3>
            <div class="total_price">
                @if ($total_discount_product != (session('total.product_total') - session('total.bonus_total')))
                    <span style="color: var(--red)"><span style="text-decoration: line-through">{{ $Currency::convert($total_discount) }}</span> -{{ $discount }}%</span>
                    <div class="price_with_save">
                        <span style="color: var(--green); text-align: center">{{__('text.cart_saving')}} {{ $Currency::convert($saving) }}</span>
                        <span>{{__('text.cart_only')}} {{ session('total')['all_in_currency'] }}</span>
                    </div>
                @else
                    <span>{{ session('total')['all_in_currency'] }}</span>
                @endif
            </div>
		</div>
	</div>

	<div class="total-line__buttons actions-line">
		<a href="{{ route('home.index') }}" class="actions-line__continue button button--outline">{{__('text.cart_back_to_shop')}}</a>
        <a href="{{ route('checkout.index') }}" class="actions-line__pay button button--outline">
            <span>{{__('text.cart_pay_button')}}</span>
            <svg width="16" height="16">
                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-right") }}"></use>
            </svg>
        </a>
	</div>
</div>