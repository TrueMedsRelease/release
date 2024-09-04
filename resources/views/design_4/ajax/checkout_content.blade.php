<header class="header">
    {{-- <div class="christmas">
        <img src="../style_checkout/images/pay_big.png">
    </div> --}}
    <div class="header__phones-top top-phones-header">
        <div class="top-phones-header__container header__container">
            <div class="top-phones-header__items">
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_1')}}">{{__('text.phones_title_phone_1_code')}}
                    {{__('text.phones_title_phone_1')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_2')}}">{{__('text.phones_title_phone_2_code')}}
                    {{__('text.phones_title_phone_2')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_3')}}">{{__('text.phones_title_phone_3_code')}}
                    {{__('text.phones_title_phone_3')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_4')}}">{{__('text.phones_title_phone_4_code')}}
                    {{__('text.phones_title_phone_4')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_5')}}">{{__('text.phones_title_phone_5_code')}}
                    {{__('text.phones_title_phone_5')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_6')}}">{{__('text.phones_title_phone_6_code')}}
                    {{__('text.phones_title_phone_6')}}</a>
                <a class="top-phones-header__item" href="tel:{{__('text.phones_title_phone_7')}}">{{__('text.phones_title_phone_7_code')}}
                    {{__('text.phones_title_phone_7')}}</a>
            </div>
        </div>
    </div>
    <div class="header__content">
        <div class="header__container">
            <div class="header__top">
                <a class="header__logo"><img src="{{ asset('style_checkout/images/logo.svg') }}" alt=""></a>
                <div class="header__selects">
                    <div class="header__select currency">
                        <h2 class="header__caption">{{__('text.checkout_currency')}}</h2>
                        <select name="form[]" id="currency_select" class="form"
                            onclick="location.href=this.options[this.selectedIndex].value" data-scroll>
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="/curr={{ $item['code'] }}"
                                    @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="header__select header__select--language language">
                        <h2 class="header__caption">{{__('text.checkout_language')}}</h2>
                        <select name="form[]" id="language_select" class="form"
                            onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <option value="/lang={{ $item['code'] }}"
                                    @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="header__bottom">
                <div class="header__inner">
                    <a href="{{ route('cart.index') }}" class="header__link-back" id="back_to_cart">
                        <svg width="18" height="18">
                            <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-right"></use>
                        </svg>
                        <span>{{__('text.checkout_back')}}</span>
                    </a>
                    <div class="header__partners">
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/geotrust.svg') }}" width="90"
                                height="30" alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/norton.svg') }}" width="70"
                                height="40" alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/comodo.svg') }}" width="90"
                                height="30" alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/mcafee.svg') }}" width="80"
                                height="25" alt="Awesome image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<main class="enter-info">
    <div class="enter-info__container">
        <form id="order_form" class="enter-info__form">
            @csrf
            <aside class="enter-info__sidebar">
                <p class="timer1" style="font-weight: 800; font-size: 1.2rem; margin-bottom:10px;">
                    <span style="display: inline-block; width: 63px;" id="t1">
                    </span>
                    {{__('text.checkout_timer')}}
                </p>
                <div class="your-order">
                    <h2 class="your-order__title title">{{__('text.checkout_order')}}</h2>
                    <div class="your-order__table">
                        <div class="your-order__table-row your-order__table-row--top">
                            <div class="your-order__package">{{__('text.checkout_package')}}</div>
                            <div class="your-order__qty">{{__('text.checkout_qty')}}</div>
                            <div class="your-order__per-pack">{{__('text.checkout_per_pack')}}</div>
                            <div class="your-order__price">{{__('text.checkout_price')}}</div>
                        </div>
                        @foreach ($products as $product)
                            <div class="your-order__table-row">
                                <div class="your-order__package">{{ $product['pack_name'] }}</div>
                                <div class="your-order__qty">{{ $product['q'] }}</div>
                                <div class="your-order__per-pack">{{ $product['price'] }}</div>
                                <div class="your-order__price" style="font-weight: 500;">
                                    @if ($product['dosage'] != '1card' && $product['price'] / $product['num'] != $product['max_pill_price'])
                                        <span style="color: var(--red);text-decoration: line-through;font-weight: 500;">
                                            {{ $Currency::convert($product['max_pill_price'] * $product['num'] * $product['q'], true) }}
                                        </span></br>
                                    @endif
                                    {{ $Currency::convert($product['price'] * $product['q'], true) }}
                                </div>
                            </div>
                        @endforeach
                        @if ($bonus != '')
                            <div class="your-order__table-row">
                                <div class="your-order__package">{{__('text.checkout_bonus')}}: {{ $bonus->name }} </div>
                                <div class="your-order__qty"></div>
                                <div class="your-order__per-pack"></div>
                                <div class="your-order__price" style="font-weight: 500;">
                                    {{ $Currency::convert($bonus->price) }} </div>
                            </div>
                        @endif
                    </div>
                    <div class="your-order__rows">
                        @if (!$card_only)
                            <div class="your-order__row">
                                <div class="your-order__checkbox checkbox">
                                    <input @if (session('cart_option.insurance')) checked="checked" @endif id="c_82"
                                        class="checkbox__input" type="checkbox" value="1" name="insurance"
                                        pop_show="true" onclick="Insurance()">
                                    <label for="c_82" class="checkbox__label"><span class="checkbox__text"><b
                                                style="font-weight: 500;">{{__('text.checkout_insurance')}}</b></span></label>
                                </div>
                                <div class="your-order__price" style="font-weight: 500;">
                                    {{ $Currency::convert(session('cart_option.insurance_price')) }}
                                </div>
                            </div>
                            <div class="your-order__row">
                                <div class="your-order__checkbox checkbox">
                                    <input @if (session('cart_option.secret_package')) checked @endif id="c_83"
                                        class="checkbox__input" type="checkbox" value="1" name="secret"
                                        onclick="secretPackage()">
                                    <label for="c_83" class="checkbox__label"><span class="checkbox__text"><b
                                                style="font-weight: 500;">{{__('text.checkout_secret')}}</b></span></label>
                                </div>
                                <div class="your-order__price" style="font-weight: 500;">
                                    {{ $Currency::convert(session('cart_option.secret_price')) }}
                                </div>
                            </div>
                            @if ($shipping['ems'] != 0)
                                <div class="your-order__row">
                                    <div class="your-order__checkbox checkbox">
                                        <input @if (session('cart_option.shipping') == 'ems') checked @endif id="c_86"
                                            class="checkbox__input" type="radio" value="ems" name="delivery"
                                            onclick="change_shipping('ems', {{ $product_total >= 300 ? 0 : $shipping['ems'] }})">
                                        <label for="c_86" class="checkbox__label"><span class="checkbox__text">
                                                <b style="font-weight: 500;">{{__('text.checkout_express')}}</b>
                                                <span class="checkbox__add-text">
                                                    {{__('text.checkout_over')}} {{ $Currency::convert(300) }}
                                                </span>
                                                <span class="checkbox__add-text">{{__('text.checkout_express_text')}}</span>
                                            </span></label>
                                    </div>
                                    <div style="font-size: 14px;font-weight: 500;"
                                        class="your-order__price @if ($product_total >= 300) totals-order__old-price @endif">
                                        <span>{{ $Currency::convert($shipping['ems']) }}</span>
                                        @if ($product_total >= 300)
                                            <p style="color: var(--green);">{{__('text.checkout_free')}}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if ($shipping['regular'] != 0)
                                <div class="your-order__row">
                                    <div class="your-order__checkbox checkbox">
                                        <input @if (session('cart_option.shipping') == 'regular') checked @endif id="c_85"
                                            class="checkbox__input" type="radio" value="regular" name="delivery"
                                            onclick="change_shipping('regular', {{ $product_total >= 200 ? 0 : $shipping['regular'] }})">
                                        <label for="c_85" class="checkbox__label"><span class="checkbox__text">
                                                <b style="font-weight: 500;">{{__('text.checkout_regular')}}</b>
                                                <span class="checkbox__add-text">
                                                    {{__('text.checkout_over')}} {{ $Currency::convert(200) }}
                                                </span>
                                                <span class="checkbox__add-text">{{__('text.checkout_regular_text')}}</span>
                                            </span></label>
                                    </div>
                                    <div style="font-size: 14px;font-weight: 500;"
                                        class="your-order__price @if ($product_total >= 200) totals-order__old-price @endif">
                                        <span>{{ $Currency::convert($shipping['regular']) }}</span>
                                        @if ($product_total >= 200)
                                            <p style="color: var(--green);">{{__('text.checkout_free')}}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endif
                        {{-- Discount coupon --}}
                        @if (!empty(session('coupon')) && session('coupon.type') == 'coupon')
                            <div class="your-order__row">
                                <div class="your-order__checkbox checkbox">
                                    <label style="color: var(--red);">
                                        <span class="checkbox__text">
                                            {{__('text.checkout_discount2')}}(-{{ round(session('coupon.percent')) }}%)
                                        </span>
                                    </label>
                                </div>
                                <div style="color: var(--red); font-weight: 500;">
                                    -{{ $Currency::convert(session('total.coupon_discount')) }}</div>
                            </div>
                        @endif
                        {{-- Reorder discount --}}
                        {{-- <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <label style="color: var(--red);"><span
                                        class="checkbox__text"><b>{#discount2#}(-10%)</b></span></label>
                            </div>
                            <div style="color: var(--red); font-weight: 500;">-{$data.info.reorder_discount_sum}</div>
                        </div> --}}
                        {{-- Master Discount --}}
                        {{-- <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                            <label style="color: var(--red); font-weight: 500;" ><span class="checkbox__text">{#master_discount#}(-5%)</span></label>
                            </div>
                            <div id="master_discount_sum" style="color: var(--red); font-weight: 500;"></div>
                        </div> --}}
                        {{-- Gift card discount --}}
                        {{-- <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <label style="color: var(--red); font-weight: 500;"><span
                                        class="checkbox__text">{#gift_card#} {$data.info.gift_card}</span></label>
                            </div>
                            <div id="gift_card_minus" style="color: var(--red); font-weight: 500;">-
                                {$data.info.gift_card_discount_text}</div>
                        </div> --}}

                        {{-- if !$is_only_card --}}
                        <div class="your-order__row">
                            <div class="your-order__input enter-info__input"
                                style="margin-bottom: 0; margin-right:0;">
                                <label for="coupon" class="enter-info__label">{{__('text.checkout_coupon_card')}}</label>
                                <input id="coupon" autocomplete="off" type="text" name="coupon"
                                    placeholder="" class="input"
                                    value="{{ session('coupon.coupon','') }}">
                            </div>
                            <button type="button" id="coupon_submit" class="your-order__coupon-button"
                                style="right: 7px;" onclick="Coupon()">
                                <svg width="24" height="24">
                                    <use
                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                    </use>
                                </svg>
                            </button>
                        </div>

                        <div class="your-order__bottom-row">
                            {{-- <a href="{$path.page}/cart" class="your-order__edit-button">
                                <svg width="18" height="18">
                                    <use
                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-right">
                                    </use>
                                </svg>
                                <span>{#edit#}</span>
                            </a> --}}
                            <div class="your-order__total totals-order" style="width: 100%;">
                                <h3 class="totals-order__title" style="width: 100%;">{{__('text.checkout_total')}}</h3>
                                <div
                                    style="display:flex; align-items: center; width: 100%; justify-content:space-between;">
                                    {{-- {if $data.info.gift_card_balance > $data.info.total_check && $data.info.gift_card}
                                    <div class="totals-order__total" style="color: var(--green); font-size:18px;">
                                    </div>
                                    {else} --}}
                                    <p class="totals-order__old-price">
                                        <span id="total_old">
                                            @php
                                                $total_discount = 0;
                                                foreach ($products as $product) {
                                                    if ($product['dosage'] != '1card') {
                                                        $total_discount += $product['max_pill_price'] * $product['num'];
                                                    } else {
                                                        $total_discount += $product['price'];
                                                    }
                                                }
                                                $total_discount += session('cart_option.bonus_price');
                                                $total_discount += $shipping[session('cart_option.shipping')];
                                                $total_discount += session('total.coupon_discount');
                                            @endphp
                                            {{ $Currency::convert($total_discount) }}
                                        </span>
                                        <span id="discount_text"
                                            style="text-decoration: none;">-{{ ceil(100 - (session('total.all') / $total_discount) * 100) }}%</span>
                                    </p>
                                    <div class="totals-order__savings"
                                        style="color: rgb(148, 148, 148);font-size: 13px;">{{__('text.checkout_savings')}} <span
                                            id="saving">{{ $Currency::convert($total_discount - session('total.all')) }}</span>
                                    </div>
                                    <div class="totals-order__total" style="color: var(--green); font-size:18px;">
                                        {{ session('total.all_in_currency') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="enter-info__sections">
                <p class="timer2" style="font-weight: 800; font-size: 1.2rem; margin-bottom: 20px;">
                    <span id="t2" style="display: inline-block; width: 63px;">
                    </span>
                    {{__('text.checkout_timer')}}
                </p>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{{__('text.checkout_info')}}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__line poopup">
                                <span class="poopuptext" id="error_phone">{{__('text.checkout_wrong_phone')}}</span>
                                <div class="enter-info__country phone_code">
                                    <select name="phone_code" class="form" id="phone_code_select"
                                        data-pseudo-label="{{__('text.checkout_phone')}}" data-scroll>
                                        @foreach ($phone_codes as $item)
                                            <option id=""
                                            @if (empty(session('form')))
                                                 @selected($item['iso'] == session('location.country', ''))
                                            @else
                                                @selected($item['iso'] == session('form.phone_code', ''))
                                            @endif
                                                data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                                value="{{ $item['iso'] }}">
                                                +{{ $item['phonecode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input required autocomplete="off" type="number" id="phone" name="phone"
                                        value="{{ session('form.phone', '') }}"
                                        placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                            </div>
                            <div class="enter-info__line poopup">
                                <span class="poopuptext" id="error_alt_phone">{{__('text.checkout_wrong_phone')}}</span>
                                <div class="enter-info__country alt_phone_code">
                                    <select name="alt_phone_code" class="form" id="phone_code_select"
                                        data-pseudo-label="{{__('text.checkout_alt_phone')}}" data-scroll>
                                        @foreach ($phone_codes as $item)
                                            <option id=""
                                            @if (empty(session('form')))
                                                @selected($item['iso'] == session('location.country', ''))
                                            @else
                                                @selected($item['iso'] == session('form.alt_phone_code', ''))
                                            @endif
                                                data-asset="{{ asset('style_checkout/images/countrys/' . $item['nicename'] . '.svg') }}"
                                                value="{{ $item['iso'] }}">
                                                +{{ $item['phonecode'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input autocomplete="off" type="number" id="alt_phone" name="alt_phone"
                                        value="{{ session('form.alt_phone', '') }}"
                                        placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__input poopup">
                                <label for="email" class="enter-info__label">{{__('text.checkout_email')}}</label>
                                <span class="poopuptext" id="error_email">{{__('text.checkout_wrong_email')}}</span>
                                <input id="email" required autocomplete="off" type="email" name="email"
                                    class="input"
                                    value="{{ session('form.email', '') }}">
                            </div>
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_alt_email">{{__('text.checkout_wrong_email')}}</span>
                                <label for="alt_email" class="enter-info__label">{{__('text.checkout_email2')}}</label>
                                <input id="alt_email" autocomplete="off" type="email" name="alt_email"
                                    class="input"
                                    value="{{ session('form.alt_email', '') }}">
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{{__('text.checkout_billing_address')}}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_firstname">{{__('text.checkout_required')}}</span>
                                <label for="firstname" class="enter-info__label">{{__('text.checkout_name')}}</label>
                                <input required id="firstname" autocomplete="off" type="text" name="firstname"
                                    class="input"
                                    value="{{ session('form.firstname') }}">
                            </div>
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_lastname">{{__('text.checkout_required')}}</span>
                                <label for="lastname" class="enter-info__label">{{__('text.checkout_surname')}}</label>
                                <input required id="lastname" autocomplete="off" type="text" name="lastname"
                                    class="input"
                                    value="{{ session('form.lastname', '') }}">
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__select select_billing_country">
                                <select required id="billing_country" name="billing_country" class="form"
                                    data-pseudo-label="Country:" data-scroll>
                                    @foreach ($countries as $country)
                                        <option @selected($country['country_iso2'] == session('form.billing_country', session('location.country', 'US'))) value="{{ $country['country_iso2'] }}">
                                            {{ $country['country_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (in_array(session('form.billing_country', session('location.country', 'US')), array_keys($states)))
                            <div class="enter-info__select select_billing_state">
                                <select required id="billing_state" name="billing_state" class="form"
                                    data-pseudo-label="State" data-scroll>
                                    @foreach ($states[session('form.billing_country', session('location.country', 'US'))] as $key => $state)
                                    <option value="{{ $key }}" @selected($key == session('form.billing_state', session('location.state', '')))>
                                        {{ $state }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_billing_city">{{__('text.checkout_required')}}</span>
                                <label for="billing_city" class="enter-info__label">{{__('text.checkout_city')}}</label>
                                <input required id="billing_city" autocomplete="off" type="text"
                                    name="billing_city" class="input"
                                    value="{{ session('form.billing_city', session('location.city', '')) }}">
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_billing_address">{{__('text.checkout_required')}}</span>
                                <label for="billing_address" class="enter-info__label">{{__('text.checkout_address')}}</label>
                                <input required id="billing_address" autocomplete="off" type="text"
                                    name="billing_address" class="input"
                                    value="{{ session('form.billing_address', '') }}">
                            </div>
                            <div class="enter-info__input poopup">
                                <span class="poopuptext" id="error_billing_zip">{{__('text.checkout_required')}}</span>
                                <label for="billing_zip" class="enter-info__label">{{__('text.checkout_zip')}}</label>
                                <input required id="billing_zip" autocomplete="off" type="text"
                                    name="billing_zip" class="input"
                                    value="{{ session('form.billing_zip', session('location.postal', '')) }}">
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <div class="enter-info__shiping">
                        <h2 class="enter-info__title title">{{__('text.checkout_shipping_address')}}</h2>
                        <div class="enter-info__checkbox checkbox">
                            <input id="c_1" class="checkbox__input"
                            @if (!filter_var(session('form.address_match', true), FILTER_VALIDATE_BOOLEAN))
                                checked
                            @endif
                            type="checkbox" value="false"
                                name="address_match">
                            <label for="c_1" class="checkbox__label">
                                <span class="checkbox__text">
                                    {{__('text.checkout_shipping_info')}}
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="enter-info__rows">
                        <div
                        @if (filter_var(session('form.address_match', true), FILTER_VALIDATE_BOOLEAN))
                            hidden
                        @endif
                        class="enter-info__add-inputs add-info info-form__add-inputs">
                            <div class="enter-info__row">
                                <div class="enter-info__select select_shipping_country">
                                    <select required id="shipping_country" name="shipping_country" class="form"
                                        data-pseudo-label="{{__('text.checkout_country')}}" data-scroll>
                                        @foreach ($countries as $country)
                                            <option @selected($country['country_iso2'] == session('form.shipping_country', session('location.country')))
                                                value="{{ $country['country_iso2'] }}">{{ $country['country_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (in_array(session('form.shipping_country', session('location.country', 'US')), array_keys($states)))
                                <div class="enter-info__select select_shipping_state">
                                    <select required id="shipping_state" name="shipping_state" class="form"
                                        data-pseudo-label="{{__('text.checkout_state')}}" data-scroll>
                                        @foreach ($states[session('form.shipping_country', session('location.country', 'US'))] as $key => $state)
                                        <option value="{{ $key }}" @selected($key == session('form.shipping_state', session('location.state', '')))>
                                            {{ $state }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="enter-info__input poopup">
                                    <label for="shipping_city" class="enter-info__label">{{__('text.checkout_city')}}</label>
                                    <span class="poopuptext" id="error_shipping_city">{{__('text.checkout_required')}}</span>
                                    <input id="shipping_city" autocomplete="off" type="text" name="shipping_city"
                                        class="input"
                                        value="{{ session('form.shipping_city', session('location.city', '')) }}">
                                </div>
                            </div>
                            <div class="enter-info__row">
                                <div class="enter-info__input poopup">
                                    <label for="shipping_address" class="enter-info__label">{{__('text.checkout_address')}}</label>
                                    <span class="poopuptext" id="error_shipping_address">{{__('text.checkout_required')}}</span>
                                    <input id="shipping_address" autocomplete="off" type="text"
                                        name="shipping_address" class="input"
                                        value="{{ session('form.shipping_address', '') }}">
                                </div>
                                <div class="enter-info__input poopup">
                                    <label for="shipping_zip" class="enter-info__label">{{__('text.checkout_zip')}}</label>
                                    <span class="poopuptext" id="error_shipping_zip">{{__('text.checkout_required')}}</span>
                                    <input id="shipping_zip" autocomplete="off" type="text" name="shipping_zip"
                                        class="input"
                                        value="{{ session('form.shipping_zip', session('location.postal', '')) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{{__('text.checkout_payment')}}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__line">
                                <div class="enter-info__select card_type poopup">
                                    <input required type="hidden"
                                        value="{if $data.info.success_trans eq '1'}1{else}0{/if}" id="success_trans">
                                    <select name="payment_type" class="form" id="payment_type_select"
                                        data-pseudo-label="Type of Payment">
                                        <option value="card" selected>{{__('text.checkout_bank_card')}}</option>
                                        <option value="crypto">{{__('text.checkout_crypto')}} -15% extra off</option>

                                        {{-- {if $data.info.gift_card} --}}
											{{-- <option value="gift_card" {if $data.info.payment_type == 'gift_card'}selected{/if}>{{__('text.common_gift_card')}}</option> --}}
										{{-- {/if} --}}
                                    </select>
                                    <span class="poopuptext" id="myPopup9">{{__('text.checkout_not_selected')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="enter-info__card-content">
                            <div class="enter-info__row">
                                <div class="enter-info__input poopup">
                                    <label for="card_numb" class="enter-info__label">{{__('text.checkout_card_number')}}</label>
                                    <span class="poopuptext" id="error_card_numb">{{__('text.checkout_wrong_card')}}</span>
                                    <input id="card_numb" data-card autocomplete="off" type="text"
                                        name="card_numb" class="input" value="{{ session('form.card_numb', '') }}">
                                    <img class="enter-info__pay-systems hide"
                                        src="{{ asset('style_checkout/images/pay-systems/amex.svg') }}"
                                        width="33" height="20" alt="Awesome image">
                                </div>
                                <div class="enter-info__input poopup">
                                    <span class="poopuptext" id="error_bank_name">{{__('text.checkout_required')}}</span>
                                    <label for="bank_name" class="enter-info__label">{{__('text.checkout_bank_name')}}</label>
                                    <input id="bank_name" autocomplete="off" type="text" name="bank_name"
                                    class="input" value="{{ session('form.bank_name', '') }}">
                                </div>
                            </div>
                            <div class="enter-info__row enter-info__row--no-wrap">
                                <div class="enter-info__card-date poopup">
                                    <span class="poopuptext" id="error_expire_date">{{__('text.checkout_wrong_exp_date')}}</span>
                                    <div class="enter-info__select card_month">
                                        <select name="card_month" id = "card_month" name="card_month" class="form"
                                            data-pseudo-label="{{__('text.checkout_exp_date')}}" data-scroll>
                                            <option value="">MM</option>
                                            @foreach (range(1, 12) as $l)
                                                <option @selected($l == session('form.card_month', '')) value="{{ $l < 10 ? '0' . $l : $l }}">
                                                    {{ $l < 10 ? '0' . $l : $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="enter-info__select card_year">
                                        <select autocomplete="off" id = "card_year" name="card_year" class="form"
                                            data-scroll>
                                            <option value="" selected>YY</option>
                                            @foreach (range(now()->year, now()->year + 15) as $l)
                                                <option @selected($l == session('form.card_year', '')) value="{{ $l }}">{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="enter-info__input enter-info__input--has-icon poopup">
                                    <span class="poopuptext" id="error_cvc_2">{{__('text.checkout_wrong_cvc')}}</span>
                                    <label for="cvc_2" class="enter-info__label">{{__('text.checkout_cvv')}}</label>
                                    <input id="cvc_2" autocomplete="off" type="number" name="cvc_2"
                                        class="input" data-card-cvc value="{{ session('form.cvc_2', '') }}">
                                    <img class="enter-info__icon-input"
                                        src="{{ asset('style_checkout/images/icons/cvc-other.svg') }}" width="60"
                                        height="28" alt="Awesome image">
                                </div>
                            </div>
                            <button id="proccess" name="proccess" class="enter-info__button button">
                                <span>{{__('text.checkout_place')}}</span>
                                <svg width="18" height="18">
                                    <use
                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                    </use>
                                </svg>
                            </button>
                        </div>
                        <div class="enter-info__crypto-content" hidden>
                            <input type="hidden" id="pay_yes" value="0">
                            <input type="hidden" id="invoiceId" value="">
                            <div class="info_text_crypto" style="line-height: 24px">
                                <div>{{__('text.checkout_crypto_text_1')}}</div>
                                <div>{{__('text.checkout_crypto_text_2')}}</div>
                                <ul style="padding-left: 40px; line-height: 24px">
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_0')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_1')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_2')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_3')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_4')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_5')}}</li>
                                    <li style="list-style: disc">{{__('text.checkout_crypto_li_6')}}</li>
                                </ul>
                            </div>
                            <div class="content-crypto">
                                <div class="content-crypto__items">
                                    <div class="content-crypto__item">
                                        <input id="cr_01" value="ETH_ETHEREUM" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_01">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-eth">
                                                </use>
                                            </svg>
                                            <span>Ethereum</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_02" value="BTC_BITCOIN" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_02">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-btc">
                                                </use>
                                            </svg>
                                            <span>Bitcoin</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_03" value="USDT_TRON" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_03">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-trc20">
                                                </use>
                                            </svg>
                                            <span>USDT(TRC20)</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_04" value="USDT_ETHEREUM" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_04">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-erc20">
                                                </use>
                                            </svg>
                                            <span>USDT(ERC20)</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_05" value="LTC_LITECOIN" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_05">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-ltc">
                                                </use>
                                            </svg>
                                            <span>Litecoin</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_06" value="TRX_TRON" type="radio" name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_06">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-trx">
                                                </use>
                                            </svg>
                                            <span>Tron</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_07" value="BNB_BSC" type="radio" name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_07">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-bnb">
                                                </use>
                                            </svg>
                                            <span>Binance Coin</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_08" value="TON_TON" type="radio" name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_08">
                                            <picture style="margin-bottom: 3px;">
                                                <source srcset="{{ asset('style_checkout/images/icons/ton.png') }}"
                                                    type="image/png">
                                                <img class="product-about__img"
                                                    src="{{ asset('style_checkout/images/icons/ton.png') }}"
                                                    alt="TON" width="40" height="40" loading="lazy">
                                            </picture>
                                            <span>TON</span>
                                        </label>
                                    </div>
                                    {{-- <div class="content-crypto__item">
                                        <input id="cr_09" value="BUSD_BSC" type="radio" name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_08">
                                            <svg width="40" height="40">
                                                <use
                                                    xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-bnbu">
                                                </use>
                                            </svg>
                                            <span>Binance USD</span>
                                        </label>
                                    </div> --}}
                                </div>
                                <div style="text-align: center;" id="requisites_load" hidden>
                                    <img src="{{ asset('style_checkout/images/loading.gif') }}">
                                </div>
                                <div id="requisites" hidden>
                                    <div class="enter-info__note">
                                        <svg width="18" height="18">
                                            <use
                                                xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-time">
                                            </use>
                                        </svg>
                                        <p>{{__('text.checkout_invoice')}} <b id="timer">30:00</b></p>
                                    </div>
                                    <div class="content-crypto__details details-payment">
                                        <div class="details-payment__qr-code">
                                            <picture><img id="qr_code" src="" width="140"
                                                    height="140" alt="Awesome image"></picture>
                                        </div>
                                        <div class="details-payment__rows">
                                            <div class="details-payment__row">
                                                <div class="details-payment__data">
                                                    <h3 class="details-payment__title">{{__('text.checkout_amount')}}</h3>
                                                    <div class="details-payment__cells">
                                                        <span class="details-payment__amount"
                                                            id="crypto_total"></span>
                                                        <span class="details-payment__old-price"
                                                            id="crypto_price"></span>
                                                        <span class="details-payment__price"
                                                            id="crypto_discount_price"></span>
                                                    </div>
                                                </div>
                                                <button type="button" class="details-payment__copy-button">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy">
                                                        </use>
                                                    </svg>
                                                </button>
                                                <div class="details-payment__tip">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-checkmark">
                                                        </use>
                                                    </svg>
                                                    <span>{{__('text.checkout_copy')}}</span>
                                                </div>
                                            </div>
                                            <div class="details-payment__row">
                                                <div class="details-payment__data">
                                                    <h3 class="details-payment__title">{{__('text.checkout_funds')}}</h3>
                                                    <div class="details-payment__cells">
                                                        <span id="purse"
                                                            class="details-payment__amount">0xbcec7dc127978e0733ef40cd3255ba54a450b87c</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="details-payment__copy-button">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-copy">
                                                        </use>
                                                    </svg>
                                                </button>
                                                <div class="details-payment__tip">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-checkmark">
                                                        </use>
                                                    </svg>
                                                    <span>{{__('text.checkout_copy')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="enter-info__note enter-info__note--has-offset">
                                        <p>{{__('text.checkout_payment_id')}}<span id="invoce_p"></span></p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="enter-info__button button" id="paid">
                                <span>{{__('text.checkout_paid')}}</span>
                            </button>
                            <button style="display: none;" type="submit" class="enter-info__button button"
                                id="waiting">
                                {{__('text.checkout_approving')}} <img src="{{ asset('style_checkout/images/131.gif') }}"
                                    width="30px" height="30px">
                            </button>
                        </div>
                        <div class="enter-info__paypal-content" hidden>
                            <div class="details-payment__row">
                                <div class="details-payment__data" style="text-align: center;">
                                    {{__('text.checkout_sepa_text')}}
                                </div>
                            </div>
                            <button id="proccess_paypal" name="proccess" class="enter-info__button button">
                                <span>{{__('text.checkout_sepa_button')}}</span>
                            </button>
                        </div>

                        <div class="enter-info__sepa-content" hidden>
                            <div class="details-payment__row">
                                <div class="details-payment__data" style="text-align: center;">
                                    {{__('text.checkout_sepa_sum_text')}} <span id="sepa_sum">{$data.info.sepasum}</span>
                                </div>
                                <div class="details-payment__data" style="text-align: center;">
                                    {{__('text.checkout_sepa_text')}}
                                </div>
                            </div>
                            <button id="proccess_sepa" name="proccess" class="enter-info__button button">
                                <span>{{__('text.checkout_sepa_button')}}</span>
                            </button>
                        </div>

                        {{-- <div class="enter-info__gift-card-content" {if $rest_total !==0}hidden{/if}>
                            <button id="proccess_gift_card" name="proccess" class="enter-info__button button">
                                <span>{#place#}</span>
                                <svg width="18" height="18">
                                    <use
                                        xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left">
                                    </use>
                                </svg>
                            </button>
                        </div> --}}
                    </div>
                </section>
            </div>
            <div class="popup_warning">
                <div class="popup_block">
                    <div class="popup_close_button">
                        <svg width="20" height="20">
                            <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-close"></use>
                        </svg>
                    </div>
                    <div class="popup_text">
                        {{__('text.checkout_card_warning')}}
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('style_checkout/js/app.js') }}?v=11062021"></script>
</main>
<footer class="footer">
    <div class="footer__container">
        <p class="footer__text">{{__('text.checkout_copyright')}} <br> {{__('text.checkout_ltd')}}</p>
    </div>
</footer>

<style>
    .timer1 {
        display: none;
    }

    .timer2 {
        display: none;
    }

    .popup_warning {
        display: none;
        position: fixed;
        z-index: 1000;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        padding: 1.875rem 0.625rem;
        -webkit-transition: visibility 0.8s ease 0s;
        transition: visibility 0.8s ease 0s;
    }

    .popup_block {
        background-color: var(--green);
        color: white;
        -webkit-box-flex: 1;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        min-height: 10%;
        width: 40%;
        position: fixed;
        top: 40%;
        left: 37%;
    }

    .popup_close_button {
        position: absolute;
        top: 10px;
        right: 15px;
        cursor: pointer;
    }


    @media (min-width: 992px) {
        .timer1 {
            display: none;
        }

        .timer2 {
            display: block;
        }
    }

    @media (max-width: 991px) {
        .timer1 {
            display: block;
        }

        .timer2 {
            display: none;
        }
    }

    .tooltip span {
        border-radius: 5px 5px 5px 5px;
        visibility: hidden;
        opacity: 0;
        position: absolute;
        border-radius: 5px;
        animation: 50s show ease;
        transition: 1s;
        bottom: 40px;
        /* top:50px; */
    }

    @media (min-width: 25.625em) {
        .tooltip span {
            left: 110%;
        }
    }

    @media (min-width: 995px) {
        .page__content {
            width: 740px;
        }
    }

    .tooltip:hover span {
        visibility: visible;
        opacity: 0.6;
    }
</style>
