<h1 class="title-page">{{__('text.cart_cart_title')}}</h1>
<div class="table-box cart-table">
	<h2>{{__('text.cart_order_title_1')}}
		<p class="text-head">{{__('text.cart_order_title_text')}}</p>
	</h2>
	<div class="table">
		<div class="head">
			<div class="line">
				<span class="item">{{__('text.cart_package')}}</span>
				<span class="item">{{__('text.cart_qty')}}</span>
				<span class="item">{{__('text.cart_per_pack')}}</span>
				<span class="item">{{__('text.cart_price')}}</span>
				<span class="item"></span>
			</div>
		</div>
		<div class="body">
			@foreach ($products as $product)
                @if ($product['product_id'] == 616)
					<div class="line">
						<div class="item" id="{{ $product['product_id'] }}">
                            <span>
                                {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}">{{ $product['name'] }}</a> --}}
                                {{ $product['name'] }}
							</span>
						</div>
						<div class="item">
							<div class="number-spinner">
                                <span class="ns-btn">
                                    <span data-dir="dwn" class="button-dwn" onclick="down({{ $product['pack_id'] }})"></span>
                                </span>
							    <input class="pl-ns-value" data-quantity-value id="{{ $product['pack_id'] }}" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}" autocomplete="off" type="text">
								<span class="ns-btn">
                                    <span data-dir="up" class="button-up" onclick="up({{ $product['pack_id'] }})"></span>
                                </span>
							</div>
						</div>
						<div class="item" style="display: flex; flex-direction: column">
							<span>${{ $product['price'] }}</span>
						</div>
						<div class="item" style="display: flex; flex-direction: column">
							<strong>${{ $product['price'] }}</strong>
						</div>
						<div class="item">
							<button class="btn btn-remove" onclick="remove({{ $product['pack_id'] }})">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M17.9998 6C17.8123 5.81253 17.558 5.70721 17.2928 5.70721C17.0277 5.70721 16.7733 5.81253 16.5858 6L11.9998 10.586L7.41382 6C7.22629 5.81253 6.97198 5.70721 6.70682 5.70721C6.44165 5.70721 6.18735 5.81253 5.99982 6C5.81235 6.18753 5.70703 6.44184 5.70703 6.707C5.70703 6.97217 5.81235 7.22647 5.99982 7.414L10.5858 12L5.99982 16.586C5.81235 16.7735 5.70703 17.0278 5.70703 17.293C5.70703 17.5582 5.81235 17.8125 5.99982 18C6.18735 18.1875 6.44165 18.2928 6.70682 18.2928C6.97198 18.2928 7.22629 18.1875 7.41382 18L11.9998 13.414L16.5858 18C16.7733 18.1875 17.0277 18.2928 17.2928 18.2928C17.558 18.2928 17.8123 18.1875 17.9998 18C18.1873 17.8125 18.2926 17.5582 18.2926 17.293C18.2926 17.0278 18.1873 16.7735 17.9998 16.586L13.4138 12L17.9998 7.414C18.1873 7.22647 18.2926 6.97217 18.2926 6.707C18.2926 6.44184 18.1873 6.18753 17.9998 6Z" fill="#ED4C54"/>
								</svg>
							</button>
						</div>
					</div>
				@else
					<div class="line">
						<div class="item" id="{{ $product['product_id'] }}">
                            <span>
                                {{-- <a href="{$path.page}/{$cur_product_in_cart_info.url}">{$cur_product_in_cart_info.name}</a> --}}
                                {{ $product['pack_name'] }}
                            </span>
						</div>
						<div class="item">
							<div class="number-spinner">
                                <span class="ns-btn">
                                    <span data-dir="dwn" class="button-dwn" onclick="down({{ $product['pack_id'] }})"></span>
                                </span>
								<input class="pl-ns-value" data-quantity-value id="{{ $product['pack_id'] }}" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}" autocomplete="off" type="text">
								<span class="ns-btn">
                                    <span data-dir="up" class="button-up" onclick="up({{ $product['pack_id'] }})"></span>
                                </span>
							</div>
						</div>
						<div class="item" style="display: flex; flex-direction: column">
                            <span class="old-price" style="color: var(--red); font-size: 12px"><span style="text-decoration: line-through">${{ $product['max_pill_price'] * $product['num'] }}</span> -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                            <span class="new-price">{{__('text.cart_only')}} ${{ $product['price'] }}</span>
						</div>
						<div class="item" style="display: flex; flex-direction: column">
                            <span class="old-price" style="color: var(--red); font-size: 12px"><span style="text-decoration: line-through">${{ $product['max_pill_price'] * $product['num'] * $product['q'] }}</span> -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                            <span class="new-price"><strong>{{__('text.cart_only')}} ${{ $product['price'] * $product['q'] }}</strong></span>
						</div>
						<div class="item">
							<button class="btn btn-remove" onclick="remove({{ $product['pack_id'] }})">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M17.9998 6C17.8123 5.81253 17.558 5.70721 17.2928 5.70721C17.0277 5.70721 16.7733 5.81253 16.5858 6L11.9998 10.586L7.41382 6C7.22629 5.81253 6.97198 5.70721 6.70682 5.70721C6.44165 5.70721 6.18735 5.81253 5.99982 6C5.81235 6.18753 5.70703 6.44184 5.70703 6.707C5.70703 6.97217 5.81235 7.22647 5.99982 7.414L10.5858 12L5.99982 16.586C5.81235 16.7735 5.70703 17.0278 5.70703 17.293C5.70703 17.5582 5.81235 17.8125 5.99982 18C6.18735 18.1875 6.44165 18.2928 6.70682 18.2928C6.97198 18.2928 7.22629 18.1875 7.41382 18L11.9998 13.414L16.5858 18C16.7733 18.1875 17.0277 18.2928 17.2928 18.2928C17.558 18.2928 17.8123 18.1875 17.9998 18C18.1873 17.8125 18.2926 17.5582 18.2926 17.293C18.2926 17.0278 18.1873 16.7735 17.9998 16.586L13.4138 12L17.9998 7.414C18.1873 7.22647 18.2926 6.97217 18.2926 6.707C18.2926 6.44184 18.1873 6.18753 17.9998 6Z" fill="#ED4C54"/>
								</svg>
							</button>
						</div>
					</div>
					@if (!empty($product['upgrade_pack']))
						<div class="line line-info">
							<div class="item">
                                <span onclick="upgrade({{ $product['pack_id'] }})">
                                    {{__('text.cart_upgrade')}}<b>{{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} ${{ $product['upgrade_pack']['price'] - $product['price'] }}</b>
                                    {{__('text.cart_savei')}} ${{ $product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'] }}
                                    @if ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $cart_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                        <b>{{__('text.cart_get_regular')}}</b>
                                    @elseif ($cart_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                        <b>{{__('text.cart_get_ems')}}</b>
                                    @endif
                                </span>
							</div>
						</div>
					@endif
				@endif
			@endforeach

			<div class="line full-line delivery-line">
                <div class="delivery-box">
                    <div class="checkbox">
                        <input {{--onclick = "Ship()"--}} class="checkbox__input" id="delivery-1" data-delivery type="radio" name="delivery" value="delivery-1">
                        <label for="delivery-1">
                            <span class="checkbox__delivery delivery-item">
                                <strong class="name-delivery">
                                    {{__('text.checkout_express')}}
                                    <span class="price">
                                        {{-- {{__('text.checkout_free')}}
                                        <span style="text-decoration: line-through;">$29.99</span> --}}
                                        $29.99
                                    </span>
                                </strong>
                                <span>
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
                                <strong class="name-delivery">
                                    {{__('text.checkout_regular')}}
                                    <span class="price">
                                        {{__('text.checkout_free')}}
                                        <span style="text-decoration: line-through;">$14.99</span>
                                    </span>
                                </strong>
                                <span>
                                    {{__('text.checkout_regular_text')}}
                                </span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="line full-line">
                <div class="bonus-box">
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="0" class="checkbox__input" type="radio" value="0" name="bonus" checked>
                        <label for="0">
                            <div class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">No Bonus</span>
                                </span>
                            </div>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="11630" class="checkbox__input" type="radio" value="11630" name="bonus">
                        <label for="11630">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">Free ED Pack</span>
                                    <span class="bonus_price">Free</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 1 tablett, Cialis 20 mg x 1 tablett, Levitra 20 mg x 1 tablett</span>
                            </span>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="4576" class="checkbox__input" type="radio" value="4576" name="bonus">
                        <label for="4576">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">Trial ED Pack</span>
                                    <span class="bonus_price">$60</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 5 tablett, Cialis 20 mg x 5 tablett, Levitra 20 mg x 5 tablett</span>
                            </span>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="4577" class="checkbox__input" type="radio" value="4577" name="bonus">
                        <label for="4577">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">Super ED Pack</span>
                                    <span class="bonus_price">$90</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 10 tablett, Cialis 20 mg x 10 tablett, Levitra 20 mg x 10 tablett</span>
                            </span>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="4919" class="checkbox__input" type="radio" value="4919" name="bonus">
                        <label for="4919">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">Extra ED Pack</span>
                                    <span class="bonus_price">$120</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 20 tablett, Cialis 20 mg x 20 tablett, Levitra 20 mg x 20 tablett</span>
                            </span>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="11656" class="checkbox__input" type="radio" value="11656" name="bonus">
                        <label for="11656">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">Mega ED Pack</span>
                                    <span class="bonus_price">$150</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 30 tablett, Cialis 20 mg x 30 tablett, Levitra 20 mg x 30 tablett</span>
                            </span>
                        </label>
                    </div>
                    <div class="checkbox bonus">
                        <input {{--onclick = "Bonus()"--}} data-bonus  id="11655" class="checkbox__input" type="radio" value="11655" name="bonus">
                        <label for="11655">
                            <span class="order-bonus__content">
                                <span class="order-bonus__name">
                                    <span class="bonus_name">He&She ED Pack</span>
                                    <span class="bonus_price">$100</span>
                                </span>
                                <span class="order-bonus__package">Viagra 100 mg x 30 tablett, Female Viagra 100 mg x 30 tablett</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

			{{-- {if $data.gift_card_setting.enable}
				{if !$has_card} --}}
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
				{{-- {/if}
			{/if} --}}

			<div class="line full-line total-line">
				<div class="total-box">
					<div class="column price">
						<strong class="title">{{__('text.cart_total_price_text')}}</strong>
						<span class="sum">
							{{-- {if $data.discount != 0 && !$is_only_card}
								{if $total_save > 0} --}}
									<div class="total-line__old-price" style="color: var(--red); font-size: 13px">
										<span style="text-decoration: line-through">$649.80</span> -70%
										<span class="total-line__savings" style="font-weight: 400; color: #262D38">{{__('text.cart_saving')}} $540</span>
									</div>
										<div class="total-line__new-price">
										<div class="total-line__digits">{{__('text.cart_only')}} $195.31</div>
									</div>
                    			{{-- {else}
                        			<div class="total-line__digits">{$data.total_price}</div>
								{/if}
							{/if}
							{if $data.discount == 0 || $is_only_card}
								<div class="total-line__digits">{$data.total_price}</div>
							{/if} --}}
						</span>
					</div>
					<div class="column">
						<button class="btn btn-default blue back-to-main">{{__('text.cart_back_to_shop')}}</button>
						{{-- {if $data.checkout_type === "internal"}
							<button class="btn btn-primary button-checkout">{{__('text.cart_pay_button')}}</button>
						{else} --}}
							<button class="btn btn-primary button-checkout">{{__('text.cart_pay_button')}}</button>
						{{-- {/if} --}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>