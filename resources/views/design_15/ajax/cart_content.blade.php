<main class="main main--aside">
    <div class="main__content">
        <div class="main__heading">
            <h1 class="h1">{{__('text.common_cart_text')}}</h1>
        </div>
        <form class="form cart-form">
            <fieldset class="form__fieldset">
                <div class="form__field custom-field">
                    <div class="panel">
                        <div class="panel__header">
                            <h2 class="panel__title" style="display: flex; flex-direction: column;">
                                {{__('text.cart_order_title_1')}}
                                <p style="font-size: 1.4rem;">{{__('text.cart_order_title_text')}}</p>
                            </h2>
                        </div>
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>{{__('text.cart_package')}}</th>
                                    <th>{{__('text.cart_qty')}}</th>
                                    <th>{{__('text.cart_per_pack')}}</th>
                                    <th>{{__('text.cart_price')}}</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="cart-item-wrapper" colspan="5">
                                            <table class="cart-item">
                                                <tr class="cart-item-content">
                                                    <td class="cart-item__brand" data-caption="Package">
                                                        <span class="cart-item__brand-name">
                                                            <a href="{{route('home.product', $product['url'])}}">{{ $product['name'] }}</a>
                                                            @if (!in_array($product['product_id'], [616, 619, 620, 483, 484, 501, 615]))
                                                                {{$product['dosage_name']}}
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td class="cart-item__qty" data-caption="QTY">
                                                        <div class="qty-input">
                                                            <button class="qty-input__minus" type="button" aria-label="Minus" onclick="down({{ $product['pack_id'] }})">
                                                                <span class="icon">
                                                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                        <svg width="1em" height="1em" fill="currentColor">
                                                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#minus') }}"></use>
                                                                        </svg>
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                                            <path
                                                                                d="M2 8L14 8"
                                                                                stroke="currentColor"
                                                                                stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                            />
                                                                        </svg>
                                                                    @endif
                                                                </span>
                                                            </button>
                                                            <label class="qty-input__label">
                                                                <input class="qty-input__qty-field" inputmode="numeric" type="number" name="quantity" value="{{ $product['q'] }}">
                                                            </label>
                                                            <button class="qty-input__plus" type="button" aria-label="Plus" onclick="up({{ $product['pack_id'] }})">
                                                                <span class="icon">
                                                                    @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                        <svg width="1em" height="1em" fill="currentColor">
                                                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#plus') }}"></use>
                                                                        </svg>
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                                            <path
                                                                                d="M8 2V14"
                                                                                stroke="currentColor"
                                                                                stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                            />
                                                                            <path
                                                                                d="M2 8L14 8"
                                                                                stroke="currentColor"
                                                                                stroke-width="2"
                                                                                stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                            />
                                                                        </svg>
                                                                    @endif
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="cart-item__pack-price" data-caption="Per Pack">
                                                        <span class="cart-item__price-wrapper">
                                                            @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                                                <span class="discount-price">
                                                                    <s>{{ $Currency::convert($product['max_pill_price'] * $product['num'], true) }}</s>
                                                                    <span class="discount-label">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                                                </span>
                                                            @endif
                                                            <span class="price">@if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0) {!!__('text.product_only')!!} @endif {{ $Currency::convert($product['price'],true) }} </span>
                                                        </span>
                                                    </td>
                                                    <td class="cart-item__total-price" data-caption="Price">
                                                        <span class="cart-item__price-wrapper">
                                                            @if ($product['dosage'] != '1card' && ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) != 0)
                                                                <span class="discount-price">
                                                                    <s>{{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}</s>
                                                                    <span class="discount-label">-{{ ceil(100 - ($product['price'] / ($product['max_pill_price'] * $product['num'])) * 100) }}%</span>
                                                                </span>
                                                            @endif
                                                            <span class="price">{!!__('text.product_only')!!} {{ $Currency::convert($product['price'] * $product['q'], true) }}</span>
                                                        </span>
                                                    </td>
                                                    <td class="cart-item__remove">
                                                        <button class="cart-remove" type="button" aria-label="Remove from cart" onclick="remove({{ $product['pack_id'] }})">
                                                            <span class="icon">
                                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                    <svg width="1em" height="1em" fill="currentColor">
                                                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?6a5f4frd#trash') }}"></use>
                                                                    </svg>
                                                                @else
                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                                                        <path d="M5.00033 15.8333C5.00033 16.75 5.75033 17.5 6.66699 17.5H13.3337C14.2503 17.5 15.0003 16.75 15.0003 15.8333V5.83333H5.00033V15.8333ZM15.8337 3.33333H12.917L12.0837 2.5H7.91699L7.08366 3.33333H4.16699V5H15.8337V3.33333Z" fill="#E14C4C"/>
                                                                    </svg>
                                                                @endif
                                                            </span>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    @if (!empty($product['upgrade_pack']))
                                                        <td class="cart-item__caption" colspan="5" onclick="upgrade({{ $product['pack_id'] }})">
                                                            <a style="text-decoration: underline; cursor: pointer;">
                                                                {{__('text.cart_upgrade')}}
                                                                <b>{{ $product['upgrade_pack']['num'] }} {{$product['type_name']}} {{__('text.cart_for_only')}} {{ $Currency::convert($product['upgrade_pack']['price'] - $product['price'], true) }}</b>
                                                                {{__('text.cart_savei')}}
                                                                {{ $Currency::convert($product['max_pill_price'] * $product['upgrade_pack']['num'] - $product['upgrade_pack']['price'], true) }}.
                                                                @if ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 200 && $product_total + ($product['upgrade_pack']['price'] - $product['price']) < 300)
                                                                    <b>{{__('text.cart_get_regular')}}</b>
                                                                @elseif ($product_total + ($product['upgrade_pack']['price'] - $product['price']) >= 300)
                                                                    <b>{{__('text.cart_get_ems')}}</b>
                                                                @endif
                                                            </a>
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
                </div>
            </fieldset>

            @if (!$is_only_card)
                <fieldset class="form__fieldset">
                    <div class="form__field custom-field">
                        <div class="delivery-radios alt-panel">
                            @if ($shipping['ems'] != 0)
                                <div class="form-radio-wrapper">
                                    <input class="form-radio-input" id="delivery-2" type="radio" name="delivery" value="ems" @if (session('cart_option')['shipping'] == 'ems') checked @endif onchange="change_shipping('ems', {{ $product_total_check >= 300 ? 0 : $shipping['ems'] }})">
                                    <label class="form-radio" for="delivery-2">
                                        <div class="form-radio__title">
                                            {{__('text.shipping_ems')}}
                                            <span class="icon">
                                                <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
                                            </span>
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
                                <div class="form-radio-wrapper">
                                    <input class="form-radio-input" id="delivery-1" type="radio" name="delivery" value="regular" @if (session('cart_option')['shipping'] == 'regular') checked @endif onchange="change_shipping('regular', {{ $product_total_check >= 200 ? 0 : $shipping['regular'] }})">
                                    <label class="form-radio" for="delivery-1">
                                        <div class="form-radio__title">
                                            {{__('text.shipping_regular')}}
                                            <span class="icon">
                                                <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
                                            </span>
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

            {{-- <fieldset class="form__fieldset">
                <div class="form__field custom-field">
                    <div class="pack-radios alt-panel">
                        <div class="form-radio-wrapper">
                            <input class="form-radio-input" id="pack-0" type="radio" name="pack" @checked(session('cart_option')['bonus_id'] == 0) onchange="change_bonus(0, 0)" value="0">
                            <label class="form-radio" for="pack-0">
                                <div class="form-radio__title">No Bonus</div>
                                <div class="form-radio__text"></div>
                                <div class="form-radio__price"></div>
                            </label>
                        </div>
                        @foreach ($bonus as $product)
                            <div class="form-radio-wrapper">
                                <input class="form-radio-input" id="pack-{{ $loop->iteration + 1 }}" type="radio" name="pack" @checked(session('cart_option')['bonus_id'] == $product->pack_id) onchange="change_bonus({{ $product->pack_id }}, {{ $product->price }})" value="{{ $product->pack_id }}">
                                <label class="form-radio" for="pack-{{ $loop->iteration + 1 }}">
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
            </fieldset> --}}

            @if (env('APP_GIFT_CARD') == 1)
                @if (!$has_card)
                    <fieldset class="form__fieldset gift_field">
                        <div class="form__field custom-field">
                            <form class="gift_card" action="#">
                                <div class="gift_block alt-panel cart-form-gift">
                                    <div class="add-gift-card">
                                        <div class="order-option__checkbox">
                                            <input class="form__checkbox" id="gift-card-checkbox" type="checkbox">
                                            <label class="form__label form__label--checkbox" for="gift-card-checkbox">
                                                <div class="form__label-title">{{__('text.cart_add_gift')}}</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="gift-card-balance">
                                        <div class="h3">{{__('text.common_gift_card')}}</div>
                                        <div id="new_gift_block">
                                            <div class="select_gift">
                                                <div class="select_header_gift">
                                                    <span class="select_current_gift" curr_packaging_id = "{{ $cards[0]->pack_id }}">{{ $Currency::convert($cards[0]->price) }}</span>
                                                    <span class="icon select-wrapper__chevron">
                                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                            <svg width="1em" height="1em" fill="currentColor">
                                                                <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#chevron-down") }}"></use>
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                                <path
                                                                    d="M12.6172 5.66666L7.97507 10.3088L3.33296 5.66666"
                                                                    stroke="currentColor"
                                                                    stroke-width="1.5"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                />
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="select_body_gifts">
                                                    @foreach ($cards as $card)
                                                        <div class="select_item_gift" packaging_id = "{{ $card->pack_id }}">{{ $Currency::convert($card->price) }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <button class="button" type="button" style="min-width: 10rem;" onclick="addCard()">{{__('text.common_add_to_cart_text_d2')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </fieldset>
                @endif
            @endif

            <fieldset class="form__fieldset">
                <div class="form__field custom-field">
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

                    <div class="cart-total-panel">
                        <div class="cart-total">
                            @if (!$is_only_card && $total_discount_product != (session('total.product_total') - session('total.bonus_total')))
                                <div class="cart-total__title">{{__('text.cart_total_price_text')}}</div>
                                <div class="cart-total__wrapper">
                                    <div class="discount-price__wrapper">
                                        <div class="discount-price">
                                            <s>{{ $Currency::convert($total_discount) }}</s>
                                            <span class="discount-label">-{{ $discount }}%</span>
                                        </div>
                                        <div class="cart-total__savings c-green">
                                            {{__('text.cart_saving')}}{{ $Currency::convert($saving) }}
                                        </div>
                                    </div>
                                    <div class="cart-total__price">{{__('text.cart_only')}} {{ session('total')['all_in_currency'] }} </div>
                                </div>
                            @endif
                            @if ($total_discount_product == (session('total.product_total') - session('total.bonus_total')) || $is_only_card)
                                <div class="cart-total__title">{{__('text.cart_total_price_text')}}</div>
                                <div class="cart-total__wrapper">
                                    <div class="cart-total__price">{{ session('total')['all_in_currency'] }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="cart-form__controls">
                            <a class="button button--dark-surface" href="{{ route('home.index') }}">{{__('text.cart_back_to_shop')}}</a>
                            <a class="button" href="{{ route('checkout.index') }}">
                                {{__('text.cart_pay_button')}}
                            </a>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="related-products">
        <h2 class="related-products__title">{{ __('text.recc_text') }}</h2>
        <div class="cards">
            @foreach ($recommendation as $product_data)
                @if ($loop->iteration == 7)
                    @break
                @endif
                <article class="card">
                    <div class="card__img">
                        <picture style="max-height: 200px; max-width: 200px;">
                            <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}" style="max-height: 200px; max-width: 200px;">
                        </picture>
                    </div>
                    <div class="card__header">
                        <h2 class="card__title">
                            <a href="{{ route('home.product', $product_data['url']) }}">{{ $product_data['name'] }}</a>
                        </h2>
                        @if (!in_array($product_data['id'], [616, 619, 620, 483, 484, 501, 615]))
                            <div class="card__variants">
                                @foreach ($product_data['product_dosages'] as $dosage)
                                    <span class="card__variant">{{ $dosage }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="card__footer">
                        <div class="card__price-wrapper">
                            <span class="card__price">{{ $Currency::convert($product_data['price'], false, true) }}</span>
                        </div>
                        <button class="card__button button button--secondary" onclick="location.href = '{{ route('home.product', $product_data['url']) }}'">
                            <span class="icon">
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg?6a5f4frd#cart") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.875 17.8926C8.27344 17.8926 7.75 18.4002 7.75 19.0711C7.75 19.742 8.27344 20.2496 8.875 20.2496C9.47656 20.2496 10 19.742 10 19.0711C10 18.4002 9.47656 17.8926 8.875 17.8926ZM6.25 19.0711C6.25 17.6118 7.40549 16.3926 8.875 16.3926C10.3445 16.3926 11.5 17.6118 11.5 19.0711C11.5 20.5304 10.3445 21.7496 8.875 21.7496C7.40549 21.7496 6.25 20.5304 6.25 19.0711Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M16.375 17.8926C15.7734 17.8926 15.25 18.4002 15.25 19.0711C15.25 19.742 15.7734 20.2496 16.375 20.2496C16.9766 20.2496 17.5 19.742 17.5 19.0711C17.5 18.4002 16.9766 17.8926 16.375 17.8926ZM13.75 19.0711C13.75 17.6118 14.9055 16.3926 16.375 16.3926C17.8445 16.3926 19 17.6118 19 19.0711C19 20.5304 17.8445 21.7496 16.375 21.7496C14.9055 21.7496 13.75 20.5304 13.75 19.0711Z" fill="currentColor"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 3C1.875 2.58579 2.21079 2.25 2.625 2.25H4.77856C5.41521 2.25 5.95014 2.72849 6.02083 3.3612L7.04587 12.5355H18.7729C18.8813 12.5355 18.9773 12.4656 19.0107 12.3625L20.8625 6.64844C20.9149 6.48695 20.7945 6.32137 20.6247 6.32137H8.875C8.46079 6.32137 8.125 5.98558 8.125 5.57137C8.125 5.15715 8.46079 4.82137 8.875 4.82137H20.6247C21.813 4.82137 22.6558 5.98043 22.2895 7.11088L20.4376 12.825C20.2038 13.5467 19.5315 14.0355 18.7729 14.0355H6.57908L5.5483 14.6413C5.47808 14.6826 5.45059 14.7267 5.43722 14.7629C5.42143 14.8058 5.41737 14.8623 5.43374 14.9225C5.45012 14.9826 5.48226 15.0293 5.51758 15.0582C5.54748 15.0827 5.59352 15.1068 5.67497 15.1068H18.25C18.6642 15.1068 19 15.4426 19 15.8568C19 16.271 18.6642 16.6068 18.25 16.6068H5.67497C3.89172 16.6068 3.25086 14.2517 4.78824 13.3481L5.57563 12.8853L4.55494 3.75H2.625C2.21079 3.75 1.875 3.41421 1.875 3Z" fill="currentColor"/>
                                    </svg>
                                @endif
                            </span>
                            {{ __('text.common_add_to_cart_text_d2') }}
                        </button>
                    </div>
                    <div class="card-features">
                        @if ($product_data['discount'] != 0)
                            <div class="card-feature card-feature--discount">-{{ $product_data['discount'] }}%</div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    <aside class="main__aside">
        <div class="cart-panel">
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