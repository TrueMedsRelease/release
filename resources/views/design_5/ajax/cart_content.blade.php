<div class="column-box">
	<div class="basket" >
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
                                        <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
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
                                    <span>{{ $Currency::convert($product['price'], false, true) }}</span>
                                </div>
                                <div class="item" style="display: flex; flex-direction: column">
                                    <strong>{{ $Currency::convert($product['price']) }}</strong>
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
                                        <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
                                        @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                            {{$product['dosage_name']}}
                                        @endif
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
                                    @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) == 0)
                                        <span class="new-price">{{ $Currency::convert($product['price'], true) }}</span>
                                    @else
                                        <span class="old-price" style="color: var(--red); font-size: 12px"><span style="text-decoration: line-through">{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</span> -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                        <span class="new-price">{{__('text.cart_only')}} {{ $Currency::convert($product['price'], true) }}</span>
                                    @endif
                                </div>
                                <div class="item" style="display: flex; flex-direction: column">
                                    @if (ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) == 0)
                                        <span class="new-price"><strong>{{__('text.cart_only')}} {{ $Currency::convert($product['price'] * $product['q'], true) }}</strong></span>
                                    @else
                                        <span class="old-price" style="color: var(--red); font-size: 12px"><span style="text-decoration: line-through">{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</span> -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                        <span class="new-price"><strong>{{__('text.cart_only')}} {{ $Currency::convert($product['price'] * $product['q'], true) }}</strong></span>
                                    @endif
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
                                            {{__('text.cart_upgrade')}}<b>{{ $product['upgrade_pack']['num'] }} {{ $product['type_name'] }} {{__('text.cart_for_only')}} {{ $Currency::convert($product['upgrade_pack']['price'] - $product['price']) }}</b>
                                            {{__('text.cart_savei')}} {{ $Currency::convert($product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price']) }}.
                                            @if ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $product_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                                <b>{{__('text.cart_get_regular')}}</b>
                                            @elseif ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                                <b>{{__('text.cart_get_ems')}}</b>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach

                    @if (!$is_only_card)
                        <div class="line full-line delivery-line">
                            <div class="delivery-box">
                                <div class="checkbox">
                                    <input class="checkbox__input" id="delivery-1" data-delivery type="radio" name="delivery" value="ems" @if (session('cart_option')['shipping'] == 'ems') checked @endif
                                    onchange="change_shipping('ems', {{ $product_total_check >= 300 ? 0 : $shipping['ems'] }})">
                                    <label for="delivery-1">
                                        <span class="checkbox__delivery delivery-item">
                                            <strong class="name-delivery">
                                                <span>
                                                    {{__('text.checkout_express')}}
                                                    <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
                                                </span>
                                                <span class="price">
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
                                            </strong>
                                            <span>
                                                @if ($product_total_check >= 300)
                                                @else
                                                    <p style="margin: 0;">{{__('text.shipping_ems_discount')}}</p>
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
                                            <strong class="name-delivery">
                                                <span>
                                                    {{__('text.checkout_regular')}}
                                                    <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
                                                </span>
                                                <span class="price">
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
                                            </strong>
                                            <span>
                                                @if ($product_total_check >= 200)
                                                @else
                                                    <p style="margin: 0;">{{__('text.shipping_regular_discount')}}</p>
                                                @endif
                                                {{__('text.checkout_regular_text')}}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="line full-line">
                        <div class="bonus-box">
                            <div class="checkbox bonus">
                                <input data-bonus  id="pack-0" class="checkbox__input" type="radio" value="0" name="bonus" @checked(session('cart_option')['bonus_id'] == 0)
                                onchange="change_bonus(0, 0)">
                                <label for="pack-0">
                                    <span class="order-bonus__content">
                                        <span class="order-bonus__name">
                                            <span class="bonus_name">No Bonus</span>
                                            <span class="bonus_price">

                                            </span>
                                        </span>
                                        <span class="order-bonus__package"></span>
                                    </span>
                                </label>
                            </div>
                            @foreach ($bonus as $product)
                                <div class="checkbox bonus">
                                    <input data-bonus  id="pack-{{ $loop->iteration + 1 }}" class="checkbox__input" type="radio" value="{{ $product->pack_id }}" name="bonus" @checked(session('cart_option')['bonus_id'] == $product->pack_id)
                                    onchange="change_bonus({{ $product->pack_id }}, {{ $product->price }})">
                                    <label for="pack-{{ $loop->iteration + 1 }}">
                                        <span class="order-bonus__content">
                                            <span class="order-bonus__name">
                                                <span class="bonus_name">{{ $product->name }}</span>
                                                <span class="bonus_price">
                                                    @if ($product->pack_id > 0)
                                                        {{ $product->price == 0 ? 'Free' : $Currency::convert($product->price) }}
                                                    @endif
                                                </span>
                                            </span>
                                            <span class="order-bonus__package">{{ $product->desc }}</span>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
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
                                                        <img loading="lazy" src="{{ asset("$design/images/icon/arrow_down_black.svg") }}">
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
                    <div class="line full-line total-line">
                        <div class="total-box">
                            <div class="column price">
                                <strong class="title">{{__('text.cart_total_price_text')}}</strong>
                                <span class="sum">
                                    @if (!$is_only_card && $total_discount_product != (session('total.product_total') - session('total.bonus_total')))
                                        <div class="total-line__old-price" style="color: var(--red); font-size: 13px">
                                            <span style="text-decoration: line-through">{{ $Currency::convert($total_discount) }}</span> -{{ $discount }}%
                                            <span class="total-line__savings" style="font-weight: 400; color: #262D38">{{__('text.cart_saving')}} {{ $Currency::convert($saving) }}</span>
                                        </div>
                                            <div class="total-line__new-price">
                                            <div class="total-line__digits">{{__('text.cart_only')}} {{ session('total')['all_in_currency'] }}</div>
                                        </div>
                                    @endif
                                    @if ($total_discount_product == (session('total.product_total') - session('total.bonus_total')) || $is_only_card)
                                        <div class="total-line__digits">{{ session('total')['all_in_currency'] }}</div>
                                    @endif
                                </span>
                            </div>
                            <div class="column">
                                <button class="btn btn-default blue back-to-main" onclick="location.href='{{ route('home.index') }}'">{{__('text.cart_back_to_shop')}}</button>
                                <button class="btn btn-primary button-checkout" onclick="location.href='{{ route('checkout.index') }}'">{{__('text.cart_pay_button')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <h2 class="title-page">{{__('text.recc_text')}}</h2>
        <div class="product-list product_rec">
            @foreach ($recommendation as $product_data)
                @if ($loop->iteration == 7)
                    @break
                @endif
                <div class="item">
                    @if ($product_data['discount'] != 0)
                        <span class="card__label">-{{ $product_data['discount'] }}%</span>
                    @endif
                    <a href="{{ route('home.product', $product_data['url']) }}" class="img">
                        <picture>
                            <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                        </picture>
                    </a>
                    <div class="info">
                        <div class="box">
                            <a href="{{ route('home.product', $product_data['url']) }}" class="name">{{ $product_data['name'] }}</a>
                            <a href="{{ route('home.product', $product_data['url']) }}" class="cat">
                                @foreach ($product_data['aktiv'] as $aktiv)
                                    {{ $aktiv['name'] }}
                                @endforeach
                            </a>
                        </div>
                        <div class="box">
                            <span class="price">{{ $Currency::convert($product_data['price'], false, true) }}</span>
                            <a href="{{ route('home.product', $product_data['url']) }}" class="btn btn-primary main">
                                <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                                <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div> --}}
	</div>
	<div class="sidebar">
		<div class="information-banner">
			<div class="information">
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/2.png") }}" alt="">
						<span>{{__('text.cart_free_regular')}}</span>
					</strong>
					<p>{{__('text.cart_sum_regular')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/3.png") }}" alt="">
						<span>{{__('text.cart_free_express')}}</span>
					</strong>
					<p>{{__('text.cart_sum_express')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/4.png") }}" alt="">
						<span>{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</span>
					</strong>
					<p>{{__('text.cart_description_secret')}}</p>
				</div>
				<div class="item">
					<strong class="name">
						<img loading="lazy" src="{{ asset("$design/images/icon/5.png") }}" alt="">
						<span>{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</span>
					</strong>
					<p>{{__('text.cart_description_moneyback')}}</p>
				</div>
			</div>
		</div>
	</div>
</div>

<h2 class="title-page">{{__('text.recc_text')}}</h2>
<div class="product-list product_rec">
    @foreach ($recommendation as $product_data)
        @if ($loop->iteration == 7)
            @break
        @endif
        <div class="item">
            @if ($product_data['discount'] != 0)
                <span class="card__label">-{{ $product_data['discount'] }}%</span>
            @endif
            <a href="{{ route('home.product', $product_data['url']) }}" class="img">
                <picture>
                    <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                    <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                </picture>
            </a>
            <div class="info">
                <div class="box">
                    <a href="{{ route('home.product', $product_data['url']) }}" class="name">{{ $product_data['name'] }}</a>
                    <a href="{{ route('home.product', $product_data['url']) }}" class="cat">
                        @foreach ($product_data['aktiv'] as $aktiv)
                            {{ $aktiv['name'] }}
                        @endforeach
                    </a>
                </div>
                <div class="box">
                    <span class="price">{{ $Currency::convert($product_data['price'], false, true) }}</span>
                    <a href="{{ route('home.product', $product_data['url']) }}" class="btn btn-primary main">
                        <img loading="lazy" src="{{ asset("$design/images/icon/ico-basket.svg") }}" alt="">
                        <span>{{__('text.common_add_to_cart_text_d2')}}</span>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>