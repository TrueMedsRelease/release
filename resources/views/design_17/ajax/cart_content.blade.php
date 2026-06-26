<div class="main__heading">
    <h1 class="h1">{{ __('text.common_cart_text') }}</h1>
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
                                                            @if (env('APP_PRINT_SPRITE', 1) == 1)
                                                                <svg width="1em" height="1em" fill="currentColor">
                                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#minus') }}"></use>
                                                                </svg>
                                                            @else
                                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                                    <path d="M2 8L14 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                                                    <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#plus') }}"></use>
                                                                </svg>
                                                            @else
                                                                <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                                    <path d="M8 2V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M2 8L14 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#trash-round') }}"></use>
                                                            </svg>
                                                        @else
                                                            <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor">
                                                                <path d="M5.00033 15.8333C5.00033 16.75 5.75033 17.5 6.66699 17.5H13.3337C14.2503 17.5 15.0003 16.75 15.0003 15.8333V5.83333H5.00033V15.8333ZM15.8337 3.33333H12.917L12.0837 2.5H7.91699L7.08366 3.33333H4.16699V5H15.8337V3.33333Z" fill="currentColor"/>
                                                            </svg>
                                                        @endif
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            @if (!empty($product['upgrade_pack']))
                                                <td class="cart-item__caption" colspan="5" onclick="upgrade({{ $product['pack_id'] }})">
                                                    <a>
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

    <fieldset class="form__fieldset cart-form__delivery-fieldset">
        <div class="form__field custom-field">
            @if (!$is_only_card)
                <div class="select-wrapper radio-select">
                    <select class="select" name="delivery" onchange="changeShippingFromSelect(this)">
                        <option value="regular" data-image="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}"
                            data-price="{{ $product_total_check >= 200 ? 0 : $shipping['regular'] }}"

                            @if ($is_only_card)
                                data-label="{{ $Currency::convert($shipping['regular']) }}"
                            @elseif ($is_only_card_with_bonus)
                                data-label="{{ $Currency::convert($shipping['regular']) }}"
                            @else
                                @if ($product_total_check >= 200)
                                    data-label="{{ __('text.cart_free') }} <span style='text-decoration: line-through;''>{{ $Currency::convert($shipping['regular']) }}</span>"
                                @else
                                    data-label="{{ $Currency::convert($shipping['regular']) }}"
                                @endif
                            @endif

                            @if ($product_total_check >= 200)
                                data-caption="{{ __('text.shipping_regular_text') }}"
                            @else
                                data-caption="{{ __('text.shipping_regular_text') }}. {{ __('text.shipping_regular_discount') }}"
                            @endif

                            @selected(session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'regular')
                            >
                            {{ __('text.shipping_regular') }}
                        </option>
                        <option value="ems" data-image="{{ asset("style_checkout/images/countrys/" . session('location.country_name') . ".svg") }}"
                            data-price="{{ $product_total_check >= 300 ? 0 : $shipping['ems'] }}"

                            @if ($is_only_card)
                                data-label="{{ $Currency::convert($shipping['ems']) }}"
                            @elseif ($is_only_card_with_bonus)
                                data-label="{{ $Currency::convert($shipping['ems']) }}"
                            @else
                                @if ($product_total_check >= 300)
                                    data-label="{{ __('text.cart_free') }} <span style='text-decoration: line-through;''>{{ $Currency::convert($shipping['ems']) }}</span>"
                                @else
                                    data-label="{{ $Currency::convert($shipping['ems']) }}"
                                @endif
                            @endif

                            @if ($product_total_check >= 300)
                                data-caption="{{ __('text.shipping_ems_text') }}"
                            @else
                                data-caption="{{ __('text.shipping_ems_text') }}. {{ __('text.shipping_ems_discount') }}"
                            @endif

                            @selected(session('cart_option.shipping', env('APP_DEFAULT_SHIPPING')) == 'ems')
                            >
                            {{ __('text.shipping_ems') }}
                        </option>
                    </select>
                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                        </svg>
                    </span>
                </div>
            @endif
            <div class="select-wrapper radio-select">
                <div class="select-wrapper radio-select">
                    <select class="select" name="delivery-addons" onchange="changeBonusFromSelect(this)">
                        <option value="0" data-price="0" @selected(session('cart_option.bonus_id', 0) == 0)>
                            No Bonus
                        </option>

                        @foreach ($bonus as $product)
                            <option value="{{ $product->pack_id }}"
                                @if ($product->pack_id > 0)
                                    data-label="{{ $product->price == 0 ? 'Free' : $Currency::convert($product->price) }}"
                                @endif
                                data-caption="{{ $product->desc }}"
                                data-price="{{ $product->price }}"
                                @selected(session('cart_option.bonus_id', 0) == $product->pack_id)
                            >
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>

                    <span class="icon select-wrapper__chevron">
                        <svg width="1em" height="1em" fill="currentColor">
                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </fieldset>

    @if (env('APP_GIFT_CARD') == 1)
        @if (!$has_card)
            <fieldset class="form__fieldset">
                <div class="form__field custom-field">
                    <div class="alt-panel cart-form-gift">
                        <div class="add-gift-card">
                            <div class="order-option__checkbox">
                                <input class="form__checkbox" id="gift-card-checkbox" type="checkbox">
                                <label class="form__label form__label--checkbox" for="gift-card-checkbox">
                                    <div class="form__label-title">{{ __('text.cart_add_gift') }}</div>
                                </label>
                            </div>
                        </div>
                        <div class="gift-card-balance">
                            <div class="h3">{{ __('text.common_gift_card') }}</div>
                            <div class="gift-card-wrapper">
                                <div class="select-wrapper">
                                    <select class="select" name="gift_card_select">
                                        @foreach ($cards as $card)
                                            <option value="{{ $card->pack_id }}">{{ $Currency::convert($card->price) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="icon select-wrapper__chevron">
                                        <svg width="1em" height="1em" fill="currentColor">
                                            <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#chevron-down') }}"></use>
                                        </svg>
                                    </span>
                                </div>
                                <button class="button" type="button" onclick="addCardNew()">{{ __('text.common_add_to_cart_text_d2') }}</button>
                            </div>
                        </div>
                    </div>
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

                $total_discount += session('cart_option.bonus_price', 0);
                if (!$is_only_card) {
                    $total_discount += $shipping[session('cart_option.shipping', env('APP_DEFAULT_SHIPPING'))];
                }

                $discount = ceil(100 - (session('total.all') / $total_discount) * 100);

                if ($is_only_card_with_bonus) {
                    $saving = 0;
                    $discount = 0;
                } else {
                    $saving = $total_discount - session('total.all');
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
                                <div class="cart-total__savings c-primary">
                                    {{__('text.cart_saving')}}{{ $Currency::convert($saving) }}
                                </div>
                            </div>
                            <div class="cart-total__price"><span>{{__('text.cart_only')}}</span> {{ session('total.all_in_currency') }} </div>
                        </div>
                    @endif
                    @if ($total_discount_product == (session('total.product_total') - session('total.bonus_total')) || $is_only_card)
                        <div class="cart-total__title">{{__('text.cart_total_price_text')}}</div>
                        <div class="cart-total__wrapper">
                            <div class="cart-total__price">{{ session('total.all_in_currency') }}</div>
                        </div>
                    @endif
                </div>
                <div class="cart-form__controls">
                    {{-- <a class="button button--white--border" href="{{ route('home.index') }}">{{__('text.cart_back_to_shop')}}</a> --}}
                    <a class="button button--white" href="{{ route('checkout.index') }}">
                        {{__('text.cart_pay_button')}}
                        <span class="icon">
                            <svg width="1em" height="1em" fill="currentColor">
                                <use href="{{ asset($design . '/svg/icons/sprite.svg?vmxkaego#arrow-right') }}"></use>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </fieldset>
</form>

{{-- <div class="related-products">
    <h2 class="related-products__title">{{ __('text.recc_text') }}</h2>
    <div class="cards">
        @foreach ($recommendation as $product_data)
            @if ($loop->iteration == 7)
                @break
            @endif
            <article class="card">
                <div class="card__header">
                    <h2 class="card__title"><a href="{{ route('home.product', $product_data['url']) }}">{{ $product_data['name'] }}</a></h2>
                    <div class="card__ingredients">
                        <span class="card__ingredient">
                            @foreach ($product_data['aktiv'] as $aktiv)
                                {{ $aktiv['name'] }}
                            @endforeach
                        </span>
                    </div>
                </div>
                <div class="card__img">
                    <picture style="max-height: 126px; max-width: 126px;">
                        <source srcset="{{ route('home.set_images', $product_data['image']) }}" type="image/webp">
                        <img loading="lazy" src="{{ route('home.set_images', $product_data['image']) }}" alt="{{ $product_data['alt'] }}" style="max-height: 126px; max-width: 126px;">
                    </picture>
                </div>
                <div class="card__variants">
                    @foreach ($product_data['product_dosages'] as $dosage)
                        <span class="card__variant">{{ $dosage }}</span>
                    @endforeach
                </div>
                <div class="card__footer">
                    <div class="card__price-wrapper">
                        <span class="card__price">{{ $Currency::convert($product_data['price'], false, true) }} {{ strtolower(__("text.common_per_pill")) }}</span>
                    </div>
                    <button class="card__button button button--outlined" onclick="location.href = '{{ route('home.product', $product_data['url']) }}'">
                        {{ __('text.product_add_to_cart_text') }}
                    </button>
                </div>
            </article>
        @endforeach
    </div>
</div> --}}