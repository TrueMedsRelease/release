<main class="main main--grid main--aside-xl main--aside-124">
    <h1>{{__('text.common_cart_text')}}</h1>
    <div class="main__content">
        <form class="form cart-form form--flex">
            <fieldset class="form__fieldset form-panel">
                <legend>
                    {{__('text.cart_order_title_1')}}
                    <p style="font-size: 1.6rem">{{__('text.cart_order_title_text')}}</p>
                </legend>

                <div class="form__field">
                    <table class="table cart-table">
                        <thead>
                            <tr>
                                <th width="45.2">{{__('text.cart_package')}}</th>
                                <th width="15.2%">{{__('text.cart_qty')}}</th>
                                <th width="18.5%">{{__('text.cart_per_pack')}}</th>
                                <th width="18.5%">{{__('text.cart_price')}}</th>
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
                                                        <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
                                                        @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                                            {{$product['dosage_name']}}
                                                        @endif
                                                    </span>
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
                                                    @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                                        <span
                                                            class="discount-price"><s>{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</s>
                                                            -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                                        </span>
                                                    @endif
                                                    <span class="price">@if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0) {!!__('text.product_only')!!} @endif {{ $Currency::convert($product['price'],true) }} </span>
                                                </td>
                                                <td class="cart-item__total-price" width="18.5%" data-caption="Price:">
                                                    @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                                        <span
                                                            class="discount-price"><s>{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</s>
                                                            -{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%
                                                        </span>
                                                    @endif
                                                    <span class="price">{!!__('text.product_only')!!}
                                                        {{ $Currency::convert($product['price'] * $product['q'], true) }}</span>
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
                                                    <td class="cart-item__caption" colspan="5"
                                                        onclick="upgrade({{ $product['pack_id'] }})">{{__('text.cart_upgrade')}}
                                                        <b>{{ $product['upgrade_pack']['num'] }} {{$product['type_name']}} {{__('text.cart_for_only')}}
                                                            {{ $Currency::convert($product['upgrade_pack']['price'] - $product['price'], true) }}</b>
                                                        {{__('text.cart_savei')}}
                                                        {{ $Currency::convert($product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'], true) }}.
                                                        @if (
                                                            $product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 &&
                                                                $product_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                                            <b>{{__('text.cart_get_regular')}}</b>
                                                        @elseif ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                                            <b>{{__('text.cart_get_ems')}}</b>
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
            @if (!$is_only_card)
                <fieldset class="form__fieldset form-panel discount-panel">
                    <div class="form__field">
                        <div class="delivery-radios">
                            @if ($shipping['ems'] != 0)
                                <div class="form-radio-wrapper"><input class="form-radio-input" id="delivery-2" type="radio"
                                        name="delivery" value="ems" @if (session('cart_option')['shipping'] == 'ems') checked @endif
                                        onchange="change_shipping('ems', {{ $product_total_check >= 300 ? 0 : $shipping['ems'] }})"><label
                                        class="form-radio" for="delivery-2">
                                        <div class="form-radio__title" style="display: flex; align-items: center;">
                                            {{__('text.shipping_ems')}}
                                            <img loading="lazy" style="margin-left: 0.5rem; width: auto;" src="/style_checkout/images/countrys/{{session('location')['country_name']}}.svg" alt="{{session('location')['country_name']}}}">
                                        </div>
                                        <div class="form-radio__text">
                                            @if ($product_total_check >= 300)
                                            @else
                                                {{__('text.shipping_ems_discount')}}<br>
                                            @endif
                                            {{__('text.shipping_ems_text')}}
                                        </div>
                                        <div class="form-radio__price">
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
                                        </div>
                                    </label>
                                </div>
                            @endif
                            @if ($shipping['regular'] != 0)
                                <div class="form-radio-wrapper"><input class="form-radio-input" id="delivery-1" type="radio"
                                        name="delivery" value="regular" @if (session('cart_option')['shipping'] == 'regular') checked @endif
                                        onchange="change_shipping('regular', {{ $product_total_check >= 200 ? 0 : $shipping['regular'] }})">
                                    <label class="form-radio" for="delivery-1">
                                        <div class="form-radio__title" style="display: flex; align-items: center;">
                                            {{__('text.shipping_regular')}}
                                            <img loading="lazy" style="margin-left: 0.5rem; width: auto;" src="/style_checkout/images/countrys/{{session('location')['country_name']}}.svg" alt="{{session('location')['country_name']}}}">
                                        </div>
                                        <div class="form-radio__text">
                                            @if ($product_total_check >= 200)
                                            @else
                                                {{__('text.shipping_regular_discount')}}<br>
                                            @endif
                                            {{__('text.shipping_regular_text')}}
                                        </div>
                                        <div class="form-radio__price">
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
                                        </div>
                                    </label>
                                </div>
                            @endif
                        </div>
                    </div>
                </fieldset>
            @endif
            <!-- Bonus -->
            <fieldset class="form__fieldset form-panel">
                <div class="form__field">
                    <div class="pack-radios">
                        <div class="form-radio-wrapper"><input class="form-radio-input"
                                id="pack-0" type="radio" name="pack"
                                @checked(session('cart_option')['bonus_id'] == 0)
                                onchange="change_bonus(0, 0)"
                                value="0"><label class="form-radio"
                                for="pack-0">
                                <div class="form-radio__title">No Bonus</div>
                                <div class="form-radio__text"></div>
                                <div class="form-radio__price">

                                </div>
                            </label>
                        </div>
                        @foreach ($bonus as $product)
                            <div class="form-radio-wrapper"><input class="form-radio-input"
                                    id="pack-{{ $loop->iteration + 1 }}" type="radio" name="pack"
                                    @checked(session('cart_option')['bonus_id'] == $product->pack_id)
                                    onchange="change_bonus({{ $product->pack_id }}, {{ $product->price }})"
                                    value="{{ $product->pack_id }}"><label class="form-radio"
                                    for="pack-{{ $loop->iteration + 1 }}">
                                    <div class="form-radio__title">{{ $product->name }}</div>
                                    <div class="form-radio__text">{{ $product->desc }}</div>
                                    <div class="form-radio__price">
                                        @if ($product->pack_id > 0)
                                            {{ $product->price == 0 ? 'Free' : $Currency::convert($product->price) }}
                                        @endif
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </fieldset>
            <!-- Gift Card -->
            @if (env('APP_GIFT_CARD') == 1)
                @if (!$has_card)
                    <fieldset class="form__fieldset form-panel discount-panel gift_field">
                        <div class="form__field">
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
                                                    <span class="select_current_gift"
                                                        curr_packaging_id = "{{ $cards[0]->pack_id }}">{{ $Currency::convert($cards[0]->price) }}</span>
                                                    <div class="select_icon">
                                                        <img loading="lazy" src="{{ asset("$design/images/icons/arrow_down_black.svg") }}">
                                                    </div>
                                                </div>
                                                <div class="select_body_gifts">
                                                    @foreach ($cards as $card)
                                                        <div class="select_item_gift" packaging_id = "{{ $card->pack_id }}">
                                                            {{ $Currency::convert($card->price) }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="button_add_gift" onclick="addCard()">{{__('text.common_add_to_cart_text_d2')}}</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </fieldset>
                @endif
            @endif
            <fieldset class="form__fieldset form-panel">
                <div class="form__field">
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
                    <div class="cart-total">
                        @if (!$is_only_card && $total_discount_product != (session('total.product_total') - session('total.bonus_total')))
                            <div class="cart-total__title h3">{{__('text.cart_total_price_text')}}</div>
                            <div class="cart-total__discount">
                                <s> {{ $Currency::convert($total_discount) }} </s>
                                -{{ $discount }}%
                            </div>
                            <div class="cart-total__savings">
                                {{__('text.cart_saving')}}{{ $Currency::convert($saving) }}
                            </div>
                            <div class="cart-total__price h3">{{__('text.cart_only')}} {{ session('total')['all_in_currency'] }} </div>
                        @endif
                        @if ($total_discount_product == (session('total.product_total') - session('total.bonus_total')) || $is_only_card)
                            <div class="cart-total__price h3">{{ session('total')['all_in_currency'] }}</div>
                        @endif
                    </div>
                    <div class="cart-form__controls">
                        <button class="button button--outline" type="button" onclick="document.location.href='{{ route('home.index') }}'">{{__('text.cart_back_to_shop')}}</button>
                        <button class="button cart-form__checkout" type="button" onclick="document.location.href='{{ route('checkout.index') }}'">{{__('text.cart_pay_button')}}
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg") }}#arrow"></use>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>

        <h2 style="margin-top: 20px; margin-bottom: 20px;">{{__('text.recc_text')}}</h2>
        <div class="product-cards product_rec">
            @foreach ($recommendation as $product_data)
                @if ($loop->iteration == 7)
                    @break
                @endif
                <article class="card product-card">
                    @if ($product_data['discount'] != 0)
                        <span class="card__label">-{{ $product_data['discount'] }}%</span>
                    @endif
                    <a class="product-card__img" href="{{ route('home.product', $product_data['url']) }}">
                        <picture>
                            <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}">
                        </picture>
                    </a>
                    <h2 class="product-card__heading">
                        <a class="product-card__brand link-primary" href="{{ route('home.product', $product_data['url']) }}">{{ $product_data['name'] }}</a>
                    </h2>
                    <div class="product-card__active">
                        @foreach ($product_data['aktiv'] as $aktiv)
                            <a class="product-card__ingredient" href="{{ route('home.active', $aktiv['url']) }}">{{ $aktiv['name'] }}</a>
                        @endforeach
                    </div>
                    <a class="product-card__text link-primary" href="{{ route('home.product', $product_data['url']) }}">
                        {{ str()->limit($product_data['desc'], 120, $end='...') }}
                    </a>
                    <div class="product-card__controls">
                        <button class="button product-card__button" aria-label="{{__('text.common_buy_button')}}" onclick="location.href='{{ route('home.product', $product_data['url']) }}'">
                            <span class="icon">
                                <svg width="1em" height="1em" fill="currentColor">
                                    <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                </svg>

                            </span> <span class="button__text">{{__('text.common_buy_button')}}</span>
                        </button>
                    <div class="product-card__price">{{ $Currency::convert($product_data['price'], false, true) }}</div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    <aside class="main__aside main__aside--narrow">
        <div class="panel cart-panel">
            {{-- <div class="cart-panel-item">
                <div class="cart-panel-item__title">{{__('text.cart_discount1')}}</div>
                <div class="cart-panel-item__text">{{__('text.cart_discount2')}}</div>
            </div> --}}
            <div class="cart-panel-item cart-panel-item--2">
                <div class="cart-panel-item__title">{{__('text.cart_free_regular')}}</div>
                <div class="cart-panel-item__text">{{__('text.cart_sum_regular')}}</div>
            </div>
            <div class="cart-panel-item cart-panel-item--3">
                <div class="cart-panel-item__title">{{__('text.cart_free_express')}}</div>
                <div class="cart-panel-item__text">{{__('text.cart_sum_express')}}</div>
            </div>
            <div class="cart-panel-item cart-panel-item--4">
                <div class="cart-panel-item__title">{{__('text.cart_secret1')}} {{__('text.cart_secret2')}}</div>
                <div class="cart-panel-item__text">{{__('text.cart_description_secret')}}</div>
            </div>
            <div class="cart-panel-item cart-panel-item--5">
                <div class="cart-panel-item__title">{{__('text.cart_moneyback1')}} {{__('text.cart_moneyback2')}}</div>
                <div class="cart-panel-item__text">{{__('text.cart_description_moneyback')}}</div>
            </div>
        </div>
    </aside>
</main>