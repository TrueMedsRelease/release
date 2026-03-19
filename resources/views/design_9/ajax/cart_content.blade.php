<div class="page-cart__container">
	<div class="page-cart__body">
		<div class="page-cart__content">
			<h1 class="page-cart__title title">{{__('text.cart_cart_title')}}</h1>
			<div class="page-cart__order order">
				<div class="order__top-row">
					<h2 class="order__label">{{__('text.cart_order_title_1')}}</h2>
					<p style="text-align: center; padding-bottom:1%;margin-top:1%;">{{__('text.cart_order_title_text')}}</p>
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
                                    <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
								</div>
								<div class="order__quantity">
									<div data-quantity class="quantity">
										<button data-quantity-minus type="button" class="quantity__button quantity__button_minus" onclick="down({{ $product['pack_id'] }})"></button>
										<div class="quantity__input">
											<input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
										</div>
										<button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})"></button>
									</div>
								</div>
								<div class="order__per-pack">
									<div class="order__new-price">{{ $Currency::convert($product['price'], false, true) }}</div>
								</div>
								<div class="order__price">
									<div class="order__new-price">{{ $Currency::convert($product['price'] * $product['q']) }}</div>
								</div>
                                <button type="button" data-remove class="order__remove" onclick="remove({{ $product['pack_id'] }})">
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2123 12.2208L15.4309 10.6499C15.4982 10.1666 15.5597 9.72475 15.6143 9.31944C15.7267 8.48652 15.7828 8.07005 15.5338 7.78519C15.2848 7.50033 14.8568 7.50033 14.0007 7.50033H5.99912C5.1431 7.50033 4.71509 7.50033 4.46606 7.78519C4.21704 8.07005 4.2732 8.48651 4.38552 9.31944C4.44019 9.72487 4.50164 10.1664 4.56893 10.6499L4.78754 12.2208C5.02514 13.928 5.14394 14.7817 5.39583 15.4673C5.86687 16.7493 6.68111 17.7042 7.65297 18.1144C8.17267 18.3337 8.78177 18.3337 9.99993 18.3337C11.2181 18.3337 11.8272 18.3337 12.3469 18.1144C13.3187 17.7042 14.133 16.7493 14.604 15.4673C14.8559 14.7817 14.9747 13.928 15.2123 12.2208ZM8.95825 9.16699C8.95825 8.82181 8.67843 8.54199 8.33325 8.54199C7.98807 8.54199 7.70825 8.82181 7.70825 9.16699V15.8337C7.70825 16.1788 7.98807 16.4587 8.33325 16.4587C8.67843 16.4587 8.95825 16.1788 8.95825 15.8337V9.16699ZM12.2916 9.16699C12.2916 8.82181 12.0118 8.54199 11.6666 8.54199C11.3214 8.54199 11.0416 8.82181 11.0416 9.16699V15.8337C11.0416 16.1788 11.3214 16.4587 11.6666 16.4587C12.0118 16.4587 12.2916 16.1788 12.2916 15.8337V9.16699Z"/>
                                            <path d="M9.99992 1.04199C7.81379 1.04199 6.04159 2.8142 6.04159 5.00033V5.20866H3.33325C2.98807 5.20866 2.70825 5.48848 2.70825 5.83366C2.70825 6.17884 2.98807 6.45866 3.33325 6.45866H16.6666C17.0118 6.45866 17.2916 6.17884 17.2916 5.83366C17.2916 5.48848 17.0118 5.20866 16.6666 5.20866H13.9583V5.00033C13.9583 2.8142 12.186 1.04199 9.99992 1.04199Z"/>
                                        </svg>
                                    @endif
                                </button>
							</div>
						</div>
					@else
						<div class="order__line">
							<div class="order__row">
								<div class="order__package" id = "{{ $product['product_id'] }}">
									<a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
                                    @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                        {{$product['dosage_name']}}
                                    @endif
								</div>
								<div class="order__quantity">
									<div data-quantity class="quantity">
										<button data-quantity-minus type="button" class="quantity__button quantity__button_minus" onclick="down({{ $product['pack_id'] }})"></button>
										<div class="quantity__input">
											<input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
										</div>
										<button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})"></button>
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
                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                        <svg width="20" height="20">
                                            <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash") }}"></use>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2123 12.2208L15.4309 10.6499C15.4982 10.1666 15.5597 9.72475 15.6143 9.31944C15.7267 8.48652 15.7828 8.07005 15.5338 7.78519C15.2848 7.50033 14.8568 7.50033 14.0007 7.50033H5.99912C5.1431 7.50033 4.71509 7.50033 4.46606 7.78519C4.21704 8.07005 4.2732 8.48651 4.38552 9.31944C4.44019 9.72487 4.50164 10.1664 4.56893 10.6499L4.78754 12.2208C5.02514 13.928 5.14394 14.7817 5.39583 15.4673C5.86687 16.7493 6.68111 17.7042 7.65297 18.1144C8.17267 18.3337 8.78177 18.3337 9.99993 18.3337C11.2181 18.3337 11.8272 18.3337 12.3469 18.1144C13.3187 17.7042 14.133 16.7493 14.604 15.4673C14.8559 14.7817 14.9747 13.928 15.2123 12.2208ZM8.95825 9.16699C8.95825 8.82181 8.67843 8.54199 8.33325 8.54199C7.98807 8.54199 7.70825 8.82181 7.70825 9.16699V15.8337C7.70825 16.1788 7.98807 16.4587 8.33325 16.4587C8.67843 16.4587 8.95825 16.1788 8.95825 15.8337V9.16699ZM12.2916 9.16699C12.2916 8.82181 12.0118 8.54199 11.6666 8.54199C11.3214 8.54199 11.0416 8.82181 11.0416 9.16699V15.8337C11.0416 16.1788 11.3214 16.4587 11.6666 16.4587C12.0118 16.4587 12.2916 16.1788 12.2916 15.8337V9.16699Z"/>
                                            <path d="M9.99992 1.04199C7.81379 1.04199 6.04159 2.8142 6.04159 5.00033V5.20866H3.33325C2.98807 5.20866 2.70825 5.48848 2.70825 5.83366C2.70825 6.17884 2.98807 6.45866 3.33325 6.45866H16.6666C17.0118 6.45866 17.2916 6.17884 17.2916 5.83366C17.2916 5.48848 17.0118 5.20866 16.6666 5.20866H13.9583V5.00033C13.9583 2.8142 12.186 1.04199 9.99992 1.04199Z"/>
                                        </svg>
                                    @endif
                                </button>
							</div>
                            @if (!empty($product['upgrade_pack']))
                            <p onclick="upgrade({{ $product['pack_id'] }})" class="order__upgrade" data-upgrade>
                                {{__('text.cart_upgrade')}}<span style="font-weight: 600">{{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} {{ $Currency::convert($product['upgrade_pack']['price'] - $product['price']) }}</span>
                                {{__('text.cart_savei')}} {{ $Currency::convert($product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price']) }}.
                                @if ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $product_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                    <span style="font-weight: 600">{{__('text.cart_get_regular')}}</span>
                                @elseif ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                    <span style="font-weight: 600">{{__('text.cart_get_ems')}}</span>
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
                                                <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
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
                                                <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
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
						<div class="bonus-line__items">
                            <div class="bonus-line__item">
                                <div class="checkbox">
                                    <input data-bonus  id="pack-0" class="checkbox__input" type="radio" name="bonus" value="0" @checked(session('cart_option')['bonus_id'] == 0)
                                    onchange="change_bonus(0, 0)">
                                    <label for="pack-0" class="checkbox__label">
                                        <div class="order-bonus__content">
                                            <span class="order-bonus__name">
                                                <span class="bonus_name">No Bonus</span>
                                                <span class="bonus_price">

                                                </span>
                                            </span>
                                            <span class="order-bonus__package"></span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @foreach ($bonus as $product)
                                <div class="bonus-line__item">
                                    <div class="checkbox">
                                        <input data-bonus  id="pack-{{ $loop->iteration + 1 }}" class="checkbox__input" type="radio" name="bonus" value="{{ $product->pack_id }}" @checked(session('cart_option')['bonus_id'] == $product->pack_id)
                                        onchange="change_bonus({{ $product->pack_id }}, {{ $product->price }})">
                                        <label for="pack-{{ $loop->iteration + 1 }}" class="checkbox__label">
                                            <div class="order-bonus__content">
                                                <span class="order-bonus__name">
                                                    <span class="bonus_name">{{ $product->name }}</span>
                                                    <span class="bonus_price">
                                                        @if ($product->pack_id > 0)
                                                            {{ $product->price == 0 ? 'Free' : $Currency::convert($product->price) }}
                                                        @endif
                                                    </span>
                                                </span>
                                                <span class="order-bonus__package">{{ $product->desc }}</span>
                                            </div>
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
                                                    <img src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
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
                        if($product['product_id'] != 616)
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
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="20" height="20">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-arr-next") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20">
                                <g clip-path="url(#ba)">
                                    <path d="M19.267 8.258 16.04 5a.833.833 0 0 0-1.183 0 .833.833 0 0 0 0 1.183l2.967 2.984H.833a.833.833 0 0 0 0 1.666h17.042l-3.017 3.017a.834.834 0 0 0 0 1.15.834.834 0 0 0 1.183 0l3.226-3.208a2.5 2.5 0 0 0 0-3.534Z"/>
                                </g>
                                <defs>
                                    <clipPath id="ba">
                                    <path d="M0 0h20v20H0z" transform="rotate(-180 10 10)"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        @endif
					</a>
				</div>
			</div>

            <div style="margin-bottom: 20px;">
                <h2 class="bestsellers__title title" style="font-size: 32px;">{{__('text.recc_text')}}</h2>
                <div class="bestsellers__body">
                    <div class="product_list">
                        @foreach ($recommendation as $product_data)
                            @if ($loop->iteration == 7)
                                @break
                            @endif
                            <div class="product_info">
                                @if ($product_data['discount'] != 0)
                                    <span class="card__label">-{{ $product_data['discount'] }}%</span>
                                @endif
                                <div class="product_info_top">
                                    <a href="{{ route('home.product', $product_data['url']) }}">
                                        <div class="product_img">
                                            <picture>
                                                <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                                                <img src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                                            </picture>
                                        </div>
                                    </a>
                                    <a href="{{ route('home.product', $product_data['url']) }}" class="product_center">
                                        <div class="product_main">
                                            <div class="product_text">
                                                <span class="product_name">{{ $product_data['name'] }}</span>
                                                <span class="product_active">
                                                    @foreach ($product_data['aktiv'] as $aktiv)
                                                        {{ $aktiv['name'] }}
                                                    @endforeach
                                                </span>
                                            </div>
                                            <div class="product_desc top">{{ $product_data['desc'] }}</div>
                                        </div>
                                    </a>
                                    <div class="product_right_block">
                                        <div class="product_price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                                        <button type="button" class="product-card__button button button--accent" title="{{__('text.product_add_to_cart_text')}}" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                <svg width="24" height="24">
                                                    <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart-white") }}"></use>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="24" height="24">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M14.4697 3.46967C14.7626 3.17678 15.2375 3.17678 15.5304 3.46967L19.3107 7.25001H22C22.4142 7.25001 22.75 7.5858 22.75 8.00001C22.75 8.41422 22.4142 8.75001 22 8.75001H21.4557C21.2293 9.60606 20.9365 10.593 20.5959 11.7414L19.5708 15.1969C19.1636 16.5702 18.914 17.412 18.4765 18.0966C17.7233 19.2751 16.5663 20.1386 15.2223 20.5256C14.4416 20.7503 13.5635 20.7502 12.1312 20.75H11.8688C10.4365 20.7502 9.55843 20.7503 8.77772 20.5256C7.43365 20.1386 6.2767 19.2751 5.52349 18.0966C5.08598 17.412 4.83637 16.5702 4.42921 15.1969L3.40431 11.742C3.06357 10.5934 2.77073 9.60621 2.54431 8.75001H2C1.58579 8.75001 1.25 8.41422 1.25 8.00001C1.25 7.5858 1.58579 7.25001 2 7.25001H4.93198L8.71231 3.46969C9.0052 3.17679 9.48008 3.17679 9.77297 3.46969C10.0659 3.76258 10.0659 4.23745 9.77297 4.53035L7.05331 7.25001H17.1894L14.4697 4.53033C14.1768 4.23744 14.1768 3.76256 14.4697 3.46967ZM10.75 12C10.75 11.5858 10.4142 11.25 10 11.25C9.58579 11.25 9.25 11.5858 9.25 12V16C9.25 16.4142 9.58579 16.75 10 16.75C10.4142 16.75 10.75 16.4142 10.75 16V12ZM14.75 12C14.75 11.5858 14.4142 11.25 14 11.25C13.5858 11.25 13.25 11.5858 13.25 12V16C13.25 16.4142 13.5858 16.75 14 16.75C14.4142 16.75 14.75 16.4142 14.75 16V12Z" fill="white"/>
                                                </svg>
                                            @endif
                                            <span>{{__('text.common_buy_button')}}</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="product_info_bottom">
                                    <div class="product_desc bottom">{{ $product_data['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
		</div>
		<aside class="page-cart__sidebar">
			<div class="page-cart__preference preference-page-cart">
				<div class="preference-page-cart__item">
					<div class="preference-page-cart__top">
						<div class="preference-page-cart__icon">
							<img src="{{ asset("$design/images/icons/pref-03.svg") }}" alt="">
						</div>
						<h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
					</div>
					<p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
				</div>
				<div class="preference-page-cart__item">
					<div class="preference-page-cart__top">
						<div class="preference-page-cart__icon">
							<img src="{{ asset("$design/images/icons/delivery.svg") }}" alt="">
						</div>
						<h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
					</div>
					<p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
				</div>
				<div class="preference-page-cart__item">
					<div class="preference-page-cart__top">
						<div class="preference-page-cart__icon">
							<img src="{{ asset("$design/images/icons/secret.svg") }}" alt="">
						</div>
						<h2 class="preference-page-cart__label">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</h2>
					</div>
					<p class="preference-page-cart__descr">{{__('text.cart_description_secret')}}</p>
				</div>
				<div class="preference-page-cart__item">
					<div class="preference-page-cart__top">
						<div class="preference-page-cart__icon">
							<img src="{{ asset("$design/images/icons/pref-04.svg") }}" alt="">
						</div>
						<h2 class="preference-page-cart__label">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</h2>
					</div>
					<p class="preference-page-cart__descr">{{__('text.cart_description_moneyback')}}</p>
				</div>
			</div>
		</aside>
	</div>
</div>