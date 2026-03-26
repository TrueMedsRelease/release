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
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-minus") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 20" fill="currentColor" width="20" height="20">
                                                <path fill-opacity=".75" fill-rule="evenodd" d="M14.32 0c1.78.019 2.686.21 3.625.713a5.04 5.04 0 0 1 2.092 2.092c.502.94.694 1.845.713 3.626v7.138c-.019 1.78-.21 2.687-.713 3.626a5.04 5.04 0 0 1-2.092 2.092c-.94.502-1.845.694-3.626.713H7.181c-1.78-.019-2.687-.21-3.626-.713a5.04 5.04 0 0 1-2.092-2.092C.961 16.255.77 15.35.75 13.57V6.431c.019-1.78.21-2.687.713-3.626A5.04 5.04 0 0 1 3.555.713C4.495.211 5.4.02 7.18 0h7.138Zm-.306 1.817H7.486l-.507.004c-1.345.026-1.947.164-2.567.496a3.222 3.222 0 0 0-1.345 1.345c-.332.62-.47 1.222-.496 2.567l-.004.507v6.528l.004.507c.026 1.345.164 1.947.496 2.567a3.222 3.222 0 0 0 1.345 1.345c.62.332 1.222.47 2.567.496l.507.004h6.528l.507-.004c1.345-.026 1.947-.164 2.567-.496a3.221 3.221 0 0 0 1.345-1.345c.332-.62.47-1.222.496-2.567l.004-.507V6.736l-.004-.507c-.026-1.345-.164-1.947-.496-2.567a3.222 3.222 0 0 0-1.345-1.345c-.62-.332-1.222-.47-2.567-.496l-.507-.004Zm-6.9 7.275h7.275a.91.91 0 0 1 0 1.819H7.114a.91.91 0 0 1 0-1.819Z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <div class="quantity__input">
                                        <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                    </div>
                                    <button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-plus") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 20" fill="currentColor" width="20" height="20">
                                                <path fill-opacity=".75" d="M14.32 0c1.78.019 2.686.21 3.625.713a5.04 5.04 0 0 1 2.092 2.092c.502.94.694 1.845.713 3.626v7.138c-.019 1.78-.21 2.687-.713 3.626a5.04 5.04 0 0 1-2.092 2.092c-.94.502-1.845.694-3.626.713H7.181c-1.78-.019-2.687-.21-3.626-.713a5.04 5.04 0 0 1-2.092-2.092C.961 16.255.77 15.35.75 13.57V6.431c.019-1.78.21-2.687.713-3.626A5.04 5.04 0 0 1 3.555.713C4.495.211 5.4.02 7.18 0h7.138Zm-.306 1.817H7.486l-.507.004c-1.345.026-1.947.164-2.567.496a3.222 3.222 0 0 0-1.345 1.345c-.332.62-.47 1.222-.496 2.567l-.004.507v6.528l.004.507c.026 1.345.164 1.947.496 2.567a3.222 3.222 0 0 0 1.345 1.345c.62.332 1.222.47 2.567.496l.507.004h6.528l.507-.004c1.345-.026 1.947-.164 2.567-.496a3.221 3.221 0 0 0 1.345-1.345c.332-.62.47-1.222.496-2.567l.004-.507V6.736l-.004-.507c-.026-1.345-.164-1.947-.496-2.567a3.222 3.222 0 0 0-1.345-1.345c-.62-.332-1.222-.47-2.567-.496l-.507-.004Zm-3.262 3.638a.91.91 0 0 1 .909.91v2.727h2.728a.91.91 0 0 1 0 1.819H11.66v2.728a.91.91 0 0 1-1.819 0V10.91H7.115a.91.91 0 0 1 0-1.819h2.727V6.365a.91.91 0 0 1 .91-.91Z"/>
                                            </svg>
                                        @endif
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash-bin") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 20" fill="currentColor" width="20" height="20">
                                        <path d="M9 .804c1.86 0 3.072.966 3.46 2.74h4.757a.783.783 0 0 1 0 1.565h-.864L15.09 17.094a2.348 2.348 0 0 1-2.335 2.102H5.244a2.348 2.348 0 0 1-2.335-2.102L1.647 5.109H.783a.783.783 0 0 1 0-1.566H5.54C5.928 1.77 7.14.804 9 .804Zm5.778 4.305H3.221l1.245 11.82a.782.782 0 0 0 .778.701h7.512c.4 0 .736-.302.778-.7l1.244-11.821ZM9 6.674c.432 0 .783.35.783.782v7.827a.783.783 0 0 1-1.566 0V7.457c0-.433.35-.783.783-.783Zm3.17 0a.783.783 0 0 1 .742.822l-.391 7.826a.783.783 0 0 1-1.564-.079l.392-7.826a.783.783 0 0 1 .82-.742Zm-6.34 0c.432-.02.8.312.821.743l.392 7.826a.783.783 0 0 1-1.564.079l-.391-7.826a.783.783 0 0 1 .742-.821ZM9 2.37c-.991 0-1.565.355-1.84 1.173h3.68C10.566 2.725 9.992 2.37 9 2.37Z"/>
                                    </svg>
                                @endif
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
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-minus") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 20" fill="currentColor" width="20" height="20">
                                                <path fill-opacity=".75" fill-rule="evenodd" d="M14.32 0c1.78.019 2.686.21 3.625.713a5.04 5.04 0 0 1 2.092 2.092c.502.94.694 1.845.713 3.626v7.138c-.019 1.78-.21 2.687-.713 3.626a5.04 5.04 0 0 1-2.092 2.092c-.94.502-1.845.694-3.626.713H7.181c-1.78-.019-2.687-.21-3.626-.713a5.04 5.04 0 0 1-2.092-2.092C.961 16.255.77 15.35.75 13.57V6.431c.019-1.78.21-2.687.713-3.626A5.04 5.04 0 0 1 3.555.713C4.495.211 5.4.02 7.18 0h7.138Zm-.306 1.817H7.486l-.507.004c-1.345.026-1.947.164-2.567.496a3.222 3.222 0 0 0-1.345 1.345c-.332.62-.47 1.222-.496 2.567l-.004.507v6.528l.004.507c.026 1.345.164 1.947.496 2.567a3.222 3.222 0 0 0 1.345 1.345c.62.332 1.222.47 2.567.496l.507.004h6.528l.507-.004c1.345-.026 1.947-.164 2.567-.496a3.221 3.221 0 0 0 1.345-1.345c.332-.62.47-1.222.496-2.567l.004-.507V6.736l-.004-.507c-.026-1.345-.164-1.947-.496-2.567a3.222 3.222 0 0 0-1.345-1.345c-.62-.332-1.222-.47-2.567-.496l-.507-.004Zm-6.9 7.275h7.275a.91.91 0 0 1 0 1.819H7.114a.91.91 0 0 1 0-1.819Z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <div class="quantity__input">
                                        <input data-quantity-value class = "data-quantite" id = "{{ $product['pack_id'] }}" autocomplete="off" type="text" name="{{ $product['pack_id'] }}" value="{{ $product['q'] }}">
                                    </div>
                                    <button data-quantity-plus type="button" class="quantity__button quantity__button_plus" onclick="up({{ $product['pack_id'] }})">
                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                            <svg width="20" height="20">
                                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-plus") }}"></use>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 21 20" fill="currentColor" width="20" height="20">
                                                <path fill-opacity=".75" d="M14.32 0c1.78.019 2.686.21 3.625.713a5.04 5.04 0 0 1 2.092 2.092c.502.94.694 1.845.713 3.626v7.138c-.019 1.78-.21 2.687-.713 3.626a5.04 5.04 0 0 1-2.092 2.092c-.94.502-1.845.694-3.626.713H7.181c-1.78-.019-2.687-.21-3.626-.713a5.04 5.04 0 0 1-2.092-2.092C.961 16.255.77 15.35.75 13.57V6.431c.019-1.78.21-2.687.713-3.626A5.04 5.04 0 0 1 3.555.713C4.495.211 5.4.02 7.18 0h7.138Zm-.306 1.817H7.486l-.507.004c-1.345.026-1.947.164-2.567.496a3.222 3.222 0 0 0-1.345 1.345c-.332.62-.47 1.222-.496 2.567l-.004.507v6.528l.004.507c.026 1.345.164 1.947.496 2.567a3.222 3.222 0 0 0 1.345 1.345c.62.332 1.222.47 2.567.496l.507.004h6.528l.507-.004c1.345-.026 1.947-.164 2.567-.496a3.221 3.221 0 0 0 1.345-1.345c.332-.62.47-1.222.496-2.567l.004-.507V6.736l-.004-.507c-.026-1.345-.164-1.947-.496-2.567a3.222 3.222 0 0 0-1.345-1.345c-.62-.332-1.222-.47-2.567-.496l-.507-.004Zm-3.262 3.638a.91.91 0 0 1 .909.91v2.727h2.728a.91.91 0 0 1 0 1.819H11.66v2.728a.91.91 0 0 1-1.819 0V10.91H7.115a.91.91 0 0 1 0-1.819h2.727V6.365a.91.91 0 0 1 .91-.91Z"/>
                                            </svg>
                                        @endif
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-trash-bin") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 20" fill="currentColor" width="20" height="20">
                                        <path d="M9 .804c1.86 0 3.072.966 3.46 2.74h4.757a.783.783 0 0 1 0 1.565h-.864L15.09 17.094a2.348 2.348 0 0 1-2.335 2.102H5.244a2.348 2.348 0 0 1-2.335-2.102L1.647 5.109H.783a.783.783 0 0 1 0-1.566H5.54C5.928 1.77 7.14.804 9 .804Zm5.778 4.305H3.221l1.245 11.82a.782.782 0 0 0 .778.701h7.512c.4 0 .736-.302.778-.7l1.244-11.821ZM9 6.674c.432 0 .783.35.783.782v7.827a.783.783 0 0 1-1.566 0V7.457c0-.433.35-.783.783-.783Zm3.17 0a.783.783 0 0 1 .742.822l-.391 7.826a.783.783 0 0 1-1.564-.079l.392-7.826a.783.783 0 0 1 .82-.742Zm-6.34 0c.432-.02.8.312.821.743l.392 7.826a.783.783 0 0 1-1.564.079l-.391-7.826a.783.783 0 0 1 .742-.821ZM9 2.37c-.991 0-1.565.355-1.84 1.173h3.68C10.566 2.725 9.992 2.37 9 2.37Z"/>
                                    </svg>
                                @endif
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
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="24" height="24">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-01") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 25" width="24" height="24">
                                <path fill="#454D58" d="M11.507.045c.33-.06.655-.06.986 0 .33.059.598.154 1.134.397l8.58 3.9c.528.24.793.404 1.055.66.26.253.451.55.574.89.124.346.164.655.164 1.234v10.35c0 .579-.04.888-.164 1.233a2.33 2.33 0 0 1-.574.89c-.262.257-.527.421-1.054.66l-8.77 3.985c-.414.183-.656.262-.945.313-.33.06-.655.06-.986 0-.33-.059-.598-.154-1.134-.397l-8.58-3.9c-.528-.24-.793-.404-1.055-.66a2.336 2.336 0 0 1-.574-.89C.04 18.363 0 18.054 0 17.475L.002 6.921c.01-.452.053-.726.162-1.028.123-.34.315-.638.574-.89.262-.257.527-.421 1.054-.66l8.77-3.985c.414-.182.656-.261.945-.313ZM2 7.354v10.231c.002.2.01.3.024.372l.022.074a.34.34 0 0 0 .088.137c.07.069.151.119.486.27l8.38 3.81V11.432l-.042-.015-.2-.082L2 7.354Zm20-.001-8.759 3.982a3 3 0 0 1-.24.097L13 22.248l8.38-3.81c.215-.097.326-.152.396-.198l.032-.022.058-.05a.339.339 0 0 0 .088-.137c.034-.093.046-.187.046-.555V7.353ZM6.5 4.399 3.416 5.801l8.17 3.713a1 1 0 0 0 .713.044l.115-.044 2.669-1.213L6.5 4.399Zm5.64-2.386a.751.751 0 0 0-.28 0c-.136.025-.262.07-.66.25L8.917 3.3 17.5 7.202l3.083-1.401-7.784-3.538a5.187 5.187 0 0 0-.516-.214l-.05-.015-.093-.02Z"/>
                            </svg>
                        @endif
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_free_regular')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_sum_regular')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="24" height="19">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-02") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="24" height="19">
                                <path fill="#454D58" d="M12 .067c1.687 0 3.293.349 4.751.978l.328.147a12 12 0 0 1 3.406 2.39A11.965 11.965 0 0 1 24 12.067c0 .425-.022.846-.065 1.26a1 1 0 1 1-1.99-.208 9.966 9.966 0 0 0-2.874-8.123 10.048 10.048 0 0 0-3.112-2.115l-.005-.002A9.964 9.964 0 0 0 12 2.067a9.959 9.959 0 0 0-4.7 1.17l-.066.037A10.038 10.038 0 0 0 4.93 4.996 9.966 9.966 0 0 0 2 12.067c0 2.014.594 3.886 1.617 5.454a1 1 0 1 1-1.676 1.092A11.948 11.948 0 0 1 0 12.067c0-3.313 1.344-6.314 3.515-8.485a12.038 12.038 0 0 1 3.74-2.54l.006-.002A11.964 11.964 0 0 1 12 .067Z"/>
                                <path fill="#454D58" fill-rule="evenodd" d="m14.077 9.867 8.905 7.247c1 .814.084 2.4-1.12 1.94l-10.73-4.088a3.04 3.04 0 0 1-.318-.143 3 3 0 1 1 2.979-5.162c.097.062.192.13.284.206ZM12.5 11.2a1 1 0 1 0-1 1.733 1 1 0 0 0 1-1.732Z" clip-rule="evenodd"/>
                            </svg>
                        @endif
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_free_express')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_sum_express')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="24" height="22">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-03") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 23" width="24" height="22">
                                <path fill="#454D58" fill-rule="evenodd" d="M5.466.603h13.068c.67 0 1.222 0 1.675.03.47.033.903.101 1.321.274a4 4 0 0 1 2.165 2.165c.173.418.241.852.273 1.32.04.578.031 1.159.031 1.737 0 .21 0 .414-.012.589a2.03 2.03 0 0 1-.14.65 2 2 0 0 1-1.082 1.082 2.23 2.23 0 0 1-.766.146v8.247c0 .805 0 1.47-.044 2.01-.046.562-.144 1.08-.392 1.564a4 4 0 0 1-1.747 1.748c-.486.248-1.002.346-1.565.392-.54.044-1.205.044-2.01.044H7.759c-.805 0-1.47 0-2.01-.044-.563-.046-1.08-.144-1.564-.392a4 4 0 0 1-1.748-1.748c-.248-.485-.346-1.002-.392-1.564C2 18.313 2 17.648 2 16.843V8.596a2.213 2.213 0 0 1-.766-.146A2 2 0 0 1 .153 7.368a2.029 2.029 0 0 1-.14-.65A9.143 9.143 0 0 1 0 6.128c0-.576-.009-1.158.03-1.735.033-.47.101-.903.274-1.321A4 4 0 0 1 2.47.907c.418-.173.852-.241 1.32-.273C4.244.603 4.796.603 5.466.603Zm-2.982 6c-.248 0-.387-.002-.475-.009A7.659 7.659 0 0 1 2 6.102c0-.712 0-1.197.026-1.573.025-.367.07-.558.126-.692a2 2 0 0 1 1.082-1.082c.134-.055.325-.101.692-.126.377-.026.86-.026 1.573-.026h13c.712 0 1.197 0 1.573.026.367.025.558.07.692.126a2 2 0 0 1 1.082 1.082c.055.134.101.325.126.692.026.376.026.86.026 1.573 0 .258 0 .402-.008.492a7.337 7.337 0 0 1-.475.008H2.484Zm1.516 2v8.199c0 .856.001 1.439.038 1.889.036.438.1.662.18.819a2 2 0 0 0 .874.873c.157.08.381.145.82.18.45.038 1.032.038 1.888.038h8.4c.856 0 1.439 0 1.889-.037.438-.036.662-.1.819-.18a2 2 0 0 0 .873-.875c.08-.156.145-.38.18-.819.038-.45.039-1.032.039-1.888v-8.2H4Z" clip-rule="evenodd"/>
                                <path fill="#454D58" fill-rule="evenodd" d="M16.207 10.895a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 1 1 1.414-1.414l1.293 1.293 4.293-4.293a1 1 0 0 1 1.414 0Z" clip-rule="evenodd"/>
                            </svg>
                        @endif
					</div>
					<h2 class="preference-page-cart__label">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</h2>
				</div>
				<p class="preference-page-cart__descr">{{__('text.cart_description_secret')}}</p>
			</div>
			<div class="preference-page-cart__item">
				<div class="preference-page-cart__top">
					<div class="preference-page-cart__icon">
                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                            <svg width="24" height="21">
                                <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-f-04") }}"></use>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 22" width="24" height="21">
                                <path fill="#454D58" d="M15.963 14.719a.923.923 0 0 1 1.305 1.305l-1.195 1.193h7.004c.473 0 .863.357.917.816l.006.107c0 .51-.413.924-.923.924h-7.002l1.193 1.193a.923.923 0 0 1 .077 1.218l-.077.087a.923.923 0 0 1-1.305 0l-2.77-2.769a.923.923 0 0 1 0-1.305l2.77-2.77Zm4.345-5.952c0-.986-.068-1.337-.261-1.698a1.593 1.593 0 0 0-.668-.668c-.361-.193-.712-.26-1.698-.26H1.846v8.45c0 .986.068 1.337.26 1.697.156.29.379.513.67.669.36.193.711.26 1.697.26h5.68a.923.923 0 0 1 0 1.847h-5.68c-1.265 0-1.904-.124-2.569-.48A3.44 3.44 0 0 1 .48 17.16C.123 16.494 0 15.855 0 14.591V4.293h.002v-.05c.016-.924.13-1.439.415-1.974a3.02 3.02 0 0 1 1.251-1.25C2.248.71 2.804.601 3.881.601h10.7c1.076 0 1.633.107 2.212.417a3.02 3.02 0 0 1 1.251 1.251c.293.548.405 1.076.417 2.043.78.05 1.276.186 1.788.46A3.44 3.44 0 0 1 21.675 6.2c.355.665.479 1.304.479 2.568v5.681a.923.923 0 1 1-1.846 0v-5.68Zm-3.693 1.989a.923.923 0 1 1 0 1.846H14.77a.923.923 0 0 1 0-1.846h1.846ZM14.58 2.448H3.881c-.798 0-1.067.052-1.342.2a1.174 1.174 0 0 0-.494.493c-.135.252-.19.5-.198 1.153h14.767c-.008-.654-.063-.9-.198-1.153a1.174 1.174 0 0 0-.493-.494c-.276-.147-.544-.199-1.343-.199Z"/>
                            </svg>
                        @endif
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
                        <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                    </picture>
                </div>
            </div>
            <button type="button" class="item-product__button" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                @if (env('APP_PRINT_SPRITE', 1) == 1)
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("$design/images/icons/icons.svg#svg-cart") }}"></use>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 20" width="20" height="20">
                        <path fill="#000" fill-rule="evenodd" d="M2.451 2.025C2.136 2 1.728 2 1.111 2H1a1 1 0 1 1 0-2h.148c.57 0 1.054 0 1.454.03.421.032.822.101 1.213.277a3.5 3.5 0 0 1 1.482 1.255c.094.142.172.288.238.438h14.782c.51 0 .958 0 1.322.032.38.034.792.112 1.17.348a2.5 2.5 0 0 1 1.086 1.46c.118.43.074.847-.003 1.22-.073.359-.202.788-.349 1.277l-1.401 4.67-.043.143c-.201.674-.377 1.266-.745 1.725a3 3 0 0 1-1.219.907c-.546.22-1.163.22-1.866.218H9.176c-.452 0-.845 0-1.173-.025a3.032 3.032 0 0 1-1.037-.238 3 3 0 0 1-1.27-1.076 3.031 3.031 0 0 1-.405-.983c-.078-.32-.143-.708-.217-1.153L4.07 4.507c-.102-.609-.17-1.01-.245-1.318-.072-.295-.136-.431-.195-.52a1.5 1.5 0 0 0-.635-.538c-.097-.043-.242-.084-.545-.106ZM6.014 4l.024.142 1.003 6.02c.081.49.134.802.192 1.039.055.225.1.309.129.353a1 1 0 0 0 .423.358c.048.022.138.051.369.069.243.018.56.019 1.057.019h8.908c.943 0 1.131-.018 1.267-.073a1 1 0 0 0 .407-.302c.091-.115.163-.29.433-1.193l1.39-4.63c.162-.541.264-.884.317-1.143.042-.208.035-.28.033-.292a.5.5 0 0 0-.216-.29c-.01-.005-.078-.034-.29-.052C21.198 4 20.84 4 20.275 4H6.014Zm15.737.077h-.001.001Zm.215.289v.001-.001Z" clip-rule="evenodd"/>
                        <path fill="#000" d="M7 18a2 2 0 1 1 4 0 2 2 0 0 1-4 0Zm9 0a2 2 0 1 1 4 0 2 2 0 0 1-4 0Z"/>
                    </svg>
                @endif
                <span>{{__('text.product_add_to_cart_text')}}</span>
            </button>
        </a>
    @endforeach
</div>