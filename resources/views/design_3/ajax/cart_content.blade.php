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
                                    <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
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
                                <div class="order__new-price">{{ $Currency::convert($product['price'], false, true) }}</div>
                            </div>
                            <div class="order__price">
                                <div class="order__new-price">{{ $Currency::convert($product['price']) }}</div>
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
                                    <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
                                    @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                        {{$product['dosage_name']}}
                                    @endif
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
                                @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) == 0)
                                    <div class="order__new-price">{{ $Currency::convert($product['price'], true) }}</div>
                                @else
                                    <div class="order__old-price">
                                        <span>{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</span>
                                        -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                    </div>
                                    <div class="order__new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'], true) }}</div>
                                @endif
                            </div>
                            <div class="order__price">
                                @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) == 0)
                                    <div class="order__new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'] * $product['q'], true) }}</div>
                                @else
                                    <div class="order__old-price">
                                        <span>{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</span>
                                        -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                    </div>
                                    <div class="order__new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'] * $product['q'], true) }}</div>
                                @endif
                            </div>
                            <button type="button" data-remove class="order__remove" onclick="remove({{ $product['pack_id'] }})">
                                <svg width="20" height="20">
                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash-bin") }}"></use>
                                </svg>
                            </button>
                        </div>
						@if (!empty($product['upgrade_pack']))
                            <p onclick="upgrade({{ $product['pack_id'] }})" class="order__upgrade" data-upgrade>
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
				@endif
			@endforeach

            @if (!$is_only_card)
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
                                            @if ($is_only_card)
                                                {{ $Currency::convert($shipping['ems']) }}
                                            @elseif ($is_only_card_with_bonus)
                                                {{ $Currency::convert($shipping['ems']) }}
                                            @else
                                                @if ($product_total_check >= 300)
                                                    {{__('text.cart_free')}} <span style="text-decoration: line-through;">{{ $Currency::convert($shipping['ems']) }}</span>
                                                @else
                                                    {{ $Currency::convert($shipping['ems']) }}
                                                @endif
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
                                            @if ($is_only_card)
                                                {{ $Currency::convert($shipping['regular']) }}
                                            @elseif ($is_only_card_with_bonus)
                                                {{ $Currency::convert($shipping['regular']) }}
                                            @else
                                                @if ($product_total_check >= 200)
                                                    {{__('text.cart_free')}} <span style="text-decoration: line-through;">{{ $Currency::convert($shipping['regular']) }}</span>
                                                @else
                                                    {{ $Currency::convert($shipping['regular']) }}
                                                @endif
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
            @endif

            <div class="order__line bonus-line">
                <div class="bonus-line__inner">
                    <div class="bonus-line__items"  style="justify-content: left;">
                        <div class="bonus-line__item">
                            <div class="checkbox">
                                <input data-bonus  id="pack-0" class="checkbox__input" type="radio" value="0" name="bonus" @checked(session('cart_option')['bonus_id'] == 0)
                                onchange="change_bonus(0, 0)">
                                <label for="pack-0" class="checkbox__label bonus">
                                    <span class="checkbox__text">
                                        <span class="bonus_top">
                                            <span class="bonus_name">No Bonus</span>
                                            <span class="bonus_price">

                                            </span>
                                        </span>
                                        <span class="bonus_bottom">
                                            <span class="bonus_desc"></span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        @foreach ($bonus as $product)
                            <div class="bonus-line__item">
                                <div class="checkbox">
                                    <input data-bonus  id="pack-{{ $loop->iteration + 1 }}" class="checkbox__input" type="radio" value="{{ $product->pack_id }}" name="bonus" @checked(session('cart_option')['bonus_id'] == $product->pack_id)
                                    onchange="change_bonus({{ $product->pack_id }}, {{ $product->price }})">
                                    <label for="pack-{{ $loop->iteration + 1 }}" class="checkbox__label bonus">
                                        <span class="checkbox__text">
                                            <span class="bonus_top">
                                                <span class="bonus_name">{{ $product->name }}</span>
                                                <span class="bonus_price">
                                                    @if ($product->pack_id > 0)
                                                        {{ $product->price == 0 ? 'Free' : $Currency::convert($product->price) }}
                                                    @endif
                                                </span>
                                            </span>
                                            <span class="bonus_bottom">
                                                <span class="bonus_desc">{{ $product->desc }}</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if (env('APP_GIFT_CARD') == 1)
                @if (!$has_card)
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
                                            <span class="select_current_gift" curr_packaging_id = "{{ $cards[0]->pack_id }}">{{ $Currency::convert($cards[0]->price) }}</span>
                                            <div class="select_icon">
                                                <img loading="lazy" src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
                                            </div>
                                        </div>
                                        <div class="select_body_gifts">
                                            @foreach ($cards as $card)
                                                <div class="select_item_gift" packaging_id = "{{ $card->pack_id }}">
                                                    {{ $Currency::convert($card->price) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="button_add_gift" onclick="addCard()">{{__('text.common_add_to_cart_text_d2')}}</div>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
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

                $total_discount_product = $total_discount;

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
                <h3 class="total-line__label">{{__('text.cart_total_price_text')}}</h3>
                <div class="total-line__data">
                    @if (!$is_only_card && $total_discount_product != (session('total.product_total') - session('total.bonus_total')))
                        <div class="total-line__old-price"><span>{{ $Currency::convert($total_discount) }}</span> -{{ $discount }}%</div>
                        <div class="total-line__new-price">
                            <div class="total-line__savings">{{__('text.cart_saving')}} {{ $Currency::convert($saving) }}</div>
                            <div class="total-line__digits">{{__('text.cart_only')}} {{ session('total')['all_in_currency'] }}</div>
                        </div>
                    @endif
                    @if ($total_discount_product == (session('total.product_total') - session('total.bonus_total')) || $is_only_card)
                        <div class="total-line__digits">{{ session('total')['all_in_currency'] }}</div>
                    @endif
                </div>
            </div>
            <div class="order__bottom actions-line">
                <a href="{{ route('home.index') }}" class="actions-line__continue">{{__('text.cart_back_to_shop')}}</a>
                <a href="{{ route('checkout.index') }}" class="actions-line__pay">
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
							<use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-04") }}"></use>
						</svg>
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
			</div>
		</div>
	</aside>
</div>

<h2 class="page__title title" style="margin-top: 20px;">{{__('text.recc_text')}}</h2>
<div class="products__items product_rec">
    @foreach ($recommendation as $product_data)
        @if ($loop->iteration == 7)
            @break
        @endif
        <a href="{{ route('home.product', $product_data['url']) }}" class="item-product">
            <div class="item-product__content">
                <div class="item-product__top">
                    <div class="item-product__left">
                        <div class="item-product__name">{{ $product_data['name'] }}</div>
                        <p class="item-product__company">
                            @foreach ($product_data['aktiv'] as $aktiv)
                                {{ $aktiv['name'] }}
                            @endforeach
                        </p>
                    </div>
                    <div class="item-product__price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                </div>
                @if ($product_data['discount'] != 0)
                    <span class="card__label">-{{ $product_data['discount'] }}%</span>
                @endif
                <div class="item-product__image-ibg">
                    <picture>
                        <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                        <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['image'] }}">
                    </picture>
                </div>
            </div>
            <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                <svg width="24" height="20">
                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                </svg>
                <span>{{__('text.product_add_to_cart_text')}}</span>
            </button>
        </a>
    @endforeach
</div>