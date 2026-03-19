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
                            <h2 class="h2">
                                {{__('text.cart_order_title_1')}}
                                <p style="font-size: 1.4rem; margin-top: 10px">{{__('text.cart_order_title_text')}}</p>
                            </h2>
                        </div>
                        <table class="table cart-table">
                            <thead>
                                <tr>
                                    <th>{{__('text.cart_package')}}</th>
                                    <th>{{__('text.cart_qty')}}</th>
                                    <th>{{__('text.cart_per_pack')}}</th>
                                    <th>{{__('text.cart_price')}}</th>
                                    <th></th>
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
                                                                    <img src="{{ asset($design . '/images/icons/minus.svg') }}" class="inline-svg">
                                                                </span>
                                                            </button>
                                                            <label class="qty-input__label">
                                                                <input class="qty-input__qty-field" inputmode="numeric" type="number" name="quantity" value="{{ $product['q'] }}">
                                                            </label>
                                                            <button class="qty-input__plus" type="button" aria-label="Plus" onclick="up({{ $product['pack_id'] }})">
                                                                <span class="icon">
                                                                    <img src="{{ asset($design . '/images/icons/plus.svg') }}" class="inline-svg">
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
                                                    <td class="cart-item__remove" width="2.6%">
                                                        <button class="cart-remove" type="button" aria-label="Remove from cart" onclick="remove({{ $product['pack_id'] }})">
                                                            <span class="icon">
                                                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                    <svg width="1em" height="1em" fill="currentColor">
                                                                        <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#trash') }}"></use>
                                                                    </svg>
                                                                @else
                                                                    <svg width="15" height="21" viewBox="0 0 15 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <g filter="url(#trash_filter0_d_1_7410)">
                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.5041 11.6788L12.7227 10.1079C12.7899 9.62459 12.8514 9.18275 12.9061 8.77745C13.0184 7.94452 13.0746 7.52806 12.8255 7.2432C12.5765 6.95833 12.1485 6.95833 11.2925 6.95833H3.29087C2.43485 6.95833 2.00684 6.95833 1.75781 7.2432C1.50878 7.52806 1.56495 7.94452 1.67727 8.77744C1.73194 9.18287 1.79339 9.62442 1.86068 10.1079L2.07929 11.6788C2.31689 13.386 2.43569 14.2397 2.68758 14.9253C3.15862 16.2073 3.97286 17.1623 4.94471 17.5724C5.46442 17.7917 6.07351 17.7917 7.29168 17.7917C8.50984 17.7917 9.11893 17.7917 9.63864 17.5724C10.6105 17.1623 11.4247 16.2073 11.8958 14.9253C12.1477 14.2397 12.2665 13.386 12.5041 11.6788ZM6.25 8.625C6.25 8.27982 5.97018 8 5.625 8C5.27982 8 5 8.27982 5 8.625V15.2917C5 15.6368 5.27982 15.9167 5.625 15.9167C5.97018 15.9167 6.25 15.6368 6.25 15.2917V8.625ZM9.58333 8.625C9.58333 8.27982 9.30351 8 8.95833 8C8.61316 8 8.33333 8.27982 8.33333 8.625V15.2917C8.33333 15.6368 8.61316 15.9167 8.95833 15.9167C9.30351 15.9167 9.58333 15.6368 9.58333 15.2917V8.625Z" fill="url(#trash_paint0_linear_1_7410)" />
                                                                            <path d="M7.29167 0.5C5.10554 0.5 3.33333 2.27221 3.33333 4.45833V4.66667H0.625C0.279822 4.66667 0 4.94649 0 5.29167C0 5.63684 0.279822 5.91667 0.625 5.91667H13.9583C14.3035 5.91667 14.5833 5.63684 14.5833 5.29167C14.5833 4.94649 14.3035 4.66667 13.9583 4.66667H11.25V4.45833C11.25 2.27221 9.47779 0.5 7.29167 0.5Z" fill="url(#trash_paint1_linear_1_7410)" />
                                                                        </g>

                                                                        <defs>
                                                                            <filter id="trash_filter0_d_1_7410" x="0" y="0.5" width="14.5833" height="20.2916" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                                                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                                                            <feOffset dy="3"/>
                                                                            <feComposite in2="hardAlpha" operator="out"/>
                                                                            <feColorMatrix type="matrix" values="0 0 0 0 0.178072 0 0 0 0 0.205659 0 0 0 0 0.443333 0 0 0 0.5 0"/>
                                                                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1_7410"/>
                                                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1_7410" result="shape"/>
                                                                            </filter>

                                                                            <linearGradient id="trash_paint0_linear_1_7410" x1="8.04637" y1="0.499999" x2="8.04637" y2="17.7917" gradientUnits="userSpaceOnUse">
                                                                            <stop stop-color="white"/>
                                                                            <stop offset="0.3" stop-color="#FECCD1"/>
                                                                            <stop offset="0.6" stop-color="#FFECEE"/>
                                                                            <stop offset="1" stop-color="white"/>
                                                                            <stop offset="1" stop-color="white"/>
                                                                            </linearGradient>

                                                                            <linearGradient id="trash_paint1_linear_1_7410" x1="8.04637" y1="0.499999" x2="8.04637" y2="17.7917" gradientUnits="userSpaceOnUse">
                                                                            <stop stop-color="white"/>
                                                                            <stop offset="0.3" stop-color="#FECCD1"/>
                                                                            <stop offset="0.6" stop-color="#FFECEE"/>
                                                                            <stop offset="1" stop-color="white"/>
                                                                            <stop offset="1" stop-color="white"/>
                                                                            </linearGradient>
                                                                        </defs>
                                                                    </svg>
                                                                @endif
                                                            </span>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    @if (!empty($product['upgrade_pack']))
                                                        <td class="cart-item__caption" colspan="5" onclick="upgrade({{ $product['pack_id'] }})">
                                                            <a class="a_update_cart" style="cursor: pointer">
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
                <fieldset class="form__fieldset ">
                    <div class="form__field custom-field">
                        <div class="delivery-radios grey-panel">
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

            <fieldset class="form__fieldset">
                <div class="form__field custom-field">
                    <div class="pack-radios grey-panel">
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
            </fieldset>
            @if (env('APP_GIFT_CARD') == 1)
                @if (!$has_card)
                    <fieldset class="form__fieldset gift_field">
                        <div class="form__field custom-field">
                            <form class="gift_card" action="#">
                                <div class="gift_block grey-panel cart-form-gift">
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
                                                    <div class="select_icon">
                                                        @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                            <svg width="1em" height="1em" fill="currentColor">
                                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?1utcbwkl#fi-rr-angle-small-down') }}"></use>
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="none" width="1em" height="1em" fill="currentColor">
                                                                <path d="M9.47256 3.1972C9.41058 3.13471 9.33685 3.08512 9.25561 3.05127C9.17437 3.01743 9.08723 3 8.99923 3C8.91122 3 8.82408 3.01743 8.74284 3.05127C8.6616 3.08512 8.58787 3.13471 8.52589 3.1972L5.47256 6.25053C5.41058 6.31301 5.33685 6.36261 5.25561 6.39645C5.17437 6.4303 5.08723 6.44772 4.99923 6.44772C4.91122 6.44772 4.82408 6.4303 4.74284 6.39645C4.6616 6.36261 4.58787 6.31301 4.52589 6.25053L1.47256 3.1972C1.41058 3.13471 1.33685 3.08512 1.25561 3.05127C1.17437 3.01743 1.08723 3 0.999226 3C0.911218 3 0.824081 3.01743 0.742842 3.05127C0.661602 3.08512 0.587868 3.13471 0.525893 3.1972C0.401726 3.32211 0.332031 3.49107 0.332031 3.6672C0.332031 3.84332 0.401726 4.01229 0.525893 4.1372L3.58589 7.19719C3.96089 7.57172 4.46922 7.7821 4.99923 7.7821C5.52923 7.7821 6.03756 7.57172 6.41256 7.19719L9.47256 4.1372C9.59673 4.01229 9.66642 3.84332 9.66642 3.6672C9.66642 3.49107 9.59673 3.32211 9.47256 3.1972Z" fill="currentColor"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="select_body_gifts">
                                                    @foreach ($cards as $card)
                                                        <div class="select_item_gift" packaging_id = "{{ $card->pack_id }}">{{ $Currency::convert($card->price) }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <button class="button button--uppercase" type="button" onclick="addCard()">{{__('text.common_add_to_cart_text_d2')}}</button>
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
                                    <div class="discount-price">
                                        <s>{{ $Currency::convert($total_discount) }}</s>
                                        <span class="discount-label">-{{ $discount }}%</span>
                                    </div>
                                    <div class="cart-total__savings c-green">
                                        {{__('text.cart_saving')}}{{ $Currency::convert($saving) }}
                                    </div>
                                    <div class="cart-total__price"><span>{{__('text.cart_only')}}</span> {{ session('total')['all_in_currency'] }} </div>
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
                            <a class="button button--uppercase" href="{{ route('home.index') }}">{{__('text.cart_back_to_shop')}}</a>
                            <a class="button button--uppercase" href="{{ route('checkout.index') }}">
                                {{__('text.cart_pay_button')}}
                                <span class="icon">
                                    <img src="{{ asset($design . '/images/icons/arr-right.svg') }}" class="inline-svg">
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>

        <h2 style="margin-bottom: 10px; margin-top: 10px">{{ __('text.recc_text') }}</h2>
        <div class="product-cards cart">
            @foreach ($recommendation as $product_data)
                @if ($loop->iteration == 7)
                    @break
                @endif
                <article class="card">
                    @if ($product_data['discount'] != 0)
                        <span class="card__label">-{{ $product_data['discount'] }}%</span>
                    @endif
                    <div class="card__img">
                        <picture style="max-height: 175px; max-width: 175px;">
                            <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                            <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}" style="max-height: 175px; max-width: 175px; width: auto; height: auto;">
                        </picture>
                    </div>
                    <div class="card__content">
                        <h2 class="card__title">
                            <a class="card__link" href="{{ route('home.product', $product_data['url']) }}">
                                {{ $product_data['name'] }}
                            </a>
                        </h2>
                        <span class="card__ingredient">
                            @foreach ($product_data['aktiv'] as $aktiv)
                                {{ $aktiv['name'] }}
                            @endforeach
                        </span>
                        <span class="card__price">{{ $Currency::convert($product_data['price'], false, true) }}</span>
                        <button class="card__button button" onclick="location.href = '{{ route('home.product', $product_data['url']) }}'">
                            <span class="icon">
                                <img src="{{ asset($design . '/images/icons/cart.svg') }}" class="inline-svg">
                            </span>
                            @if (!in_array(App::currentLocale(), ['de', 'it', 'gr', 'nl', 'hu', 'pt']))
                                <span class="button__text">{{__('text.product_add_to_cart_text')}}</span>
                            @endif
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    <aside class="main__aside">
        <div class="cart-panel">
            {{-- <div class="cart-panel-item">
                <div class="cart-panel-item__title">{{ __('text.cart_discount1') }}</div>
                <div class="cart-panel-item__text">{{ __('text.cart_discount2') }}</div>
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