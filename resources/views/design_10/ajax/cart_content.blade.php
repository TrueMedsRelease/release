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
                                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                <svg width="1em" height="1em" fill="currentColor">
                                                                    <use
                                                                        href="{{ asset("$design/svg/icons/sprite.svg") }}#trash">
                                                                    </use>
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" width="1em" height="1em" fill="currentColor">
                                                                    <path d="M13.9997 2.66666H11.933C11.614 1.11572 10.2497 0.002 8.66632 0H7.33298C5.74957 0.002 4.38526 1.11572 4.06632 2.66666H1.99966C1.63148 2.66666 1.33301 2.96513 1.33301 3.33331C1.33301 3.7015 1.63148 4 1.99966 4H2.66632V12.6667C2.66854 14.5067 4.15963 15.9978 5.99966 16H9.99966C11.8397 15.9978 13.3308 14.5067 13.333 12.6667V4H13.9997C14.3679 4 14.6663 3.70153 14.6663 3.33334C14.6663 2.96516 14.3679 2.66666 13.9997 2.66666ZM7.33301 11.3333C7.33301 11.7015 7.03454 12 6.66635 12C6.29813 12 5.99966 11.7015 5.99966 11.3333V7.33334C5.99966 6.96516 6.29813 6.66669 6.66632 6.66669C7.03451 6.66669 7.33298 6.96516 7.33298 7.33334V11.3333H7.33301ZM9.99966 11.3333C9.99966 11.7015 9.7012 12 9.33301 12C8.96482 12 8.66635 11.7015 8.66635 11.3333V7.33334C8.66635 6.96516 8.96482 6.66669 9.33301 6.66669C9.7012 6.66669 9.99966 6.96516 9.99966 7.33334V11.3333ZM5.44701 2.66666C5.73057 1.86819 6.4857 1.33434 7.33301 1.33331H8.66635C9.51366 1.33434 10.2688 1.86819 10.5524 2.66666H5.44701Z" fill="currentColor"/>
                                                                </svg>
                                                            @endif
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
                                            <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
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
                                            <img loading="lazy" style="max-width: 15px; margin-left: 0.5rem;" src="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}" alt="{{ session('location.country_name') }}">
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg") }}#arrow"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 12" fill="none" width="1em" height="1em" fill="currentColor">
                                        <path d="M15.5303 6.53033C15.8232 6.23744 15.8232 5.76256 15.5303 5.46967L10.7574 0.696699C10.4645 0.403806 9.98959 0.403806 9.6967 0.696699C9.40381 0.989593 9.40381 1.46447 9.6967 1.75736L13.9393 6L9.6967 10.2426C9.40381 10.5355 9.40381 11.0104 9.6967 11.3033C9.98959 11.5962 10.4645 11.5962 10.7574 11.3033L15.5303 6.53033ZM0 6.75H15V5.25H0V6.75Z" fill="white"/>
                                    </svg>
                                @endif
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
                                @if (env('APP_PRINT_SPRITE', 1) == 1)
                                    <svg width="1em" height="1em" fill="currentColor">
                                        <use href="{{ asset("$design/svg/icons/sprite.svg#cart") }}"></use>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" width="1em" height="1em" fill="currentColor">
                                        <defs>
                                            <clipPath id="cart_clip0">
                                            <rect width="20" height="20" fill="currentColor"/>
                                            </clipPath>
                                        </defs>

                                        <g clip-path="url(#cart_clip0)">
                                            <path d="M18.9275 3.3975C18.6931 3.1162 18.3996 2.88996 18.0679 2.73485C17.7363 2.57973 17.3745 2.49955 17.0083 2.5H3.535L3.5 2.2075C3.42837 1.59951 3.13615 1.03894 2.67874 0.632065C2.22133 0.225186 1.63052 0.000284828 1.01833 0L0.833333 0C0.61232 0 0.400358 0.0877974 0.244078 0.244078C0.0877974 0.400358 0 0.61232 0 0.833333C0 1.05435 0.0877974 1.26631 0.244078 1.42259C0.400358 1.57887 0.61232 1.66667 0.833333 1.66667H1.01833C1.22244 1.66669 1.41945 1.74163 1.57198 1.87726C1.72451 2.0129 1.82195 2.19979 1.84583 2.4025L2.9925 12.1525C3.11154 13.1665 3.59873 14.1015 4.36159 14.78C5.12445 15.4585 6.10988 15.8334 7.13083 15.8333H15.8333C16.0543 15.8333 16.2663 15.7455 16.4226 15.5893C16.5789 15.433 16.6667 15.221 16.6667 15C16.6667 14.779 16.5789 14.567 16.4226 14.4107C16.2663 14.2545 16.0543 14.1667 15.8333 14.1667H7.13083C6.61505 14.1652 6.11233 14.0043 5.69161 13.7059C5.27089 13.4075 4.95276 12.9863 4.78083 12.5H14.7142C15.6911 12.5001 16.6369 12.1569 17.3865 11.5304C18.1361 10.9039 18.6417 10.0339 18.815 9.0725L19.4692 5.44417C19.5345 5.08417 19.5198 4.71422 19.4262 4.36053C19.3326 4.00684 19.1623 3.67806 18.9275 3.3975Z" fill="currentColor"/>
                                            <path d="M5.83329 20.0006C6.75376 20.0006 7.49995 19.2544 7.49995 18.3339C7.49995 17.4134 6.75376 16.6672 5.83329 16.6672C4.91282 16.6672 4.16663 17.4134 4.16663 18.3339C4.16663 19.2544 4.91282 20.0006 5.83329 20.0006Z" fill="currentColor"/>
                                            <path d="M14.1667 20.0006C15.0871 20.0006 15.8333 19.2544 15.8333 18.3339C15.8333 17.4134 15.0871 16.6672 14.1667 16.6672C13.2462 16.6672 12.5 17.4134 12.5 18.3339C12.5 19.2544 13.2462 20.0006 14.1667 20.0006Z" fill="currentColor"/>
                                        </g>
                                    </svg>
                                @endif
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