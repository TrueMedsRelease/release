<header class="header">
    {{-- <div class="christmas">
        <img src="../style_checkout/images/pay_big.png">
    </div> --}}
    <div class="header__phones-top top-phones-header">
        <div class="top-phones-header__container header__container">
            <div class="top-phones-header__items">
                <a class="top-phones-header__item" href="tel:{#title_phone_1#}">{#title_phone_1_code#}
                    {#title_phone_1#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_2#}">{#title_phone_2_code#}
                    {#title_phone_2#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_3#}">{#title_phone_3_code#}
                    {#title_phone_3#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_4#}">{#title_phone_4_code#}
                    {#title_phone_4#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_5#}">{#title_phone_5_code#}
                    {#title_phone_5#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_6#}">{#title_phone_6_code#}
                    {#title_phone_6#}</a>
                <a class="top-phones-header__item" href="tel:{#title_phone_7#}">{#title_phone_7_code#}
                    {#title_phone_7#}</a>
            </div>
        </div>
    </div>
    <div class="header__content">
        <div class="header__container">
            <div class="header__top">
                <a class="header__logo"><img src="{{ asset('style_checkout/images/logo.svg') }}" alt=""></a>
                <div class="header__selects">
                    <div class="header__select currency">
                        <h2 class="header__caption">Currency</h2>
                        <select name="form[]" id="currency_select" class="form" onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                            @foreach ($Currency::GetAllCurrency() as $item)
                                <option value="/curr={{ $item['code'] }}" @if (session('currency') == $item['code']) selected @endif> {{ Str::upper($item['code']) }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="header__select header__select--language language">
                        <h2 class="header__caption">Language</h2>
                        <select name="form[]" id="language_select" class="form"
                            onchange="location.href=this.options[this.selectedIndex].value" data-scroll>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <option value="/lang={{ $item['code'] }}" @if (App::currentLocale() == $item['code']) selected @endif> {{ $item['name'] }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="header__bottom">
                <div class="header__inner">
                    <a href="{$path.page}/cart" class="header__link-back" id="back_to_cart">
                        <svg width="18" height="18">
                            <use xlink:href="../style_checkout/images/icons/icons.svg#svg-arr-right"></use>
                        </svg>
                        <span>{#back#}</span>
                    </a>
                    <div class="header__partners">
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/geotrust.svg') }}" width="90" height="30"
                                alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/norton.svg') }}" width="70" height="40"
                                alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/comodo.svg') }}" width="90" height="30"
                                alt="Awesome image">
                        </div>
                        <div class="header__partner">
                            <img src="{{ asset('style_checkout/images/partners/mcafee.svg') }}" width="80" height="25"
                                alt="Awesome image">
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
            <aside class="enter-info__sidebar">
                <p class="timer1" style="font-weight: 800; font-size: 1.2rem; margin-bottom:10px;">
                    <span style="display: inline-block; width: 63px;" id="t1">
                    </span>
                    {#timer#}
                </p>
                <div class="your-order">
                    <h2 class="your-order__title title">{#order#}</h2>
                    <div class="your-order__table">
                        <div class="your-order__table-row your-order__table-row--top">
                            <div class="your-order__package">{#package#}</div>
                            <div class="your-order__qty">{#qty#}</div>
                            <div class="your-order__per-pack">{#per_pack#}</div>
                            <div class="your-order__price">{#price#}</div>
                        </div>
                        {foreach item=products from=$data.info.products}
                        <div class="your-order__table-row">
                            {if $products.name == 'Gift Card'}
                            <div class="your-order__package">{$products.name}</div>
                            <div class="your-order__qty">{$products.num}</div>
                            <div class="your-order__per-pack">{$products.fm_single_price}</div>
                            <div class="your-order__price" style="font-weight: 500;">{$products.fm_price}</div>
                            {else}
                            <div class="your-order__package">{$products.name} {$products.dosage} x
                                {$products.num_in_packaging} {$products.type_name}</div>
                            <div class="your-order__qty">{$products.num}</div>
                            <div class="your-order__per-pack">{$products.fm_single_price}</div>
                            <div class="your-order__price" style="font-weight: 500;">
                                {if $products.fm_old_total_price ne $products.fm_price}<span
                                    style="color: var(--red);text-decoration: line-through;font-weight: 500;">{$products.fm_old_total_price}</span></br>{/if}
                                {$products.fm_price}
                            </div>
                            {/if}
                        </div>
                        {/foreach}
                        {if $data.info.bonus}
                        {if $data.info.bonus.name !== "No Bonus"}
                        <div class="your-order__table-row">
                            <div class="your-order__package">{#bonus#}: {$data.info.bonus.name}</div>
                            <div class="your-order__qty"></div>
                            <div class="your-order__per-pack"></div>
                            <div class="your-order__price" style="font-weight: 500;">{$data.info.bonus.price}</div>
                        </div>
                        {/if}
                        {/if}
                    </div>
                    <input id="sum_reg" value="{$data.info.sum_reg}" style="display: none;">
                    <input id="sum_ems" value="{$data.info.sum_ems}" style="display: none;">
                    <input id="sum_ins_reg" value="{$data.info.sum_ins_reg}" style="display: none;">
                    <input id="sum_ins_ems" value="{$data.info.sum_ins_ems}" style="display: none;">
                    <input id="sum_secret_reg" value="{$data.info.sum_secret_reg}" style="display: none;">
                    <input id="sum_secret_ems" value="{$data.info.sum_secret_ems}" style="display: none;">
                    <input id="sum_ins_secret_reg" value="{$data.info.sum_ins_secret_reg}" style="display: none;">
                    <input id="sum_ins_secret_ems" value="{$data.info.sum_ins_secret_ems}" style="display: none;">
                    <input id="sum_reg_old" value="{$data.info.sum_reg_old}" style="display: none;">
                    <input id="sum_ems_old" value="{$data.info.sum_ems_old}" style="display: none;">
                    <input id="sum_ins_reg_old" value="{$data.info.sum_ins_reg_old}" style="display: none;">
                    <input id="sum_ins_ems_old" value="{$data.info.sum_ins_ems_old}" style="display: none;">
                    <input id="sum_secret_reg_old" value="{$data.info.sum_secret_reg_old}" style="display: none;">
                    <input id="sum_secret_ems_old" value="{$data.info.sum_secret_ems_old}" style="display: none;">
                    <input id="sum_ins_secret_reg_old" value="{$data.info.sum_ins_secret_reg_old}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_old" value="{$data.info.sum_ins_secret_ems_old}"
                        style="display: none;">
                    <input id="sum_reg_save" value="{$data.info.sum_reg_save}" style="display: none;">
                    <input id="sum_ems_save" value="{$data.info.sum_ems_save}" style="display: none;">
                    <input id="sum_ins_reg_save" value="{$data.info.sum_ins_reg_save}" style="display: none;">
                    <input id="sum_ins_ems_save" value="{$data.info.sum_ins_ems_save}" style="display: none;">
                    <input id="sum_secret_reg_save" value="{$data.info.sum_secret_reg_save}" style="display: none;">
                    <input id="sum_secret_ems_save" value="{$data.info.sum_secret_ems_save}" style="display: none;">
                    <input id="sum_ins_secret_reg_save" value="{$data.info.sum_ins_secret_reg_save}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_save" value="{$data.info.sum_ins_secret_ems_save}"
                        style="display: none;">
                    <input id="sum_reg_paypal" value="{$data.info.sum_reg_paypal}" style="display: none;">
                    <input id="sum_ems_paypal" value="{$data.info.sum_ems_paypal}" style="display: none;">
                    <input id="sum_ins_reg_paypal" value="{$data.info.sum_ins_reg_paypal}" style="display: none;">
                    <input id="sum_ins_ems_paypal" value="{$data.info.sum_ins_ems_paypal}" style="display: none;">
                    <input id="sum_secret_reg_paypal" value="{$data.info.sum_secret_reg_paypal}"
                        style="display: none;">
                    <input id="sum_secret_ems_paypal" value="{$data.info.sum_secret_ems_paypal}"
                        style="display: none;">
                    <input id="sum_ins_secret_reg_paypal" value="{$data.info.sum_ins_secret_reg_paypal}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_paypal" value="{$data.info.sum_ins_secret_ems_paypal}"
                        style="display: none;">
                    <input id="sum_reg_save_paypal" value="{$data.info.sum_reg_save_paypal}" style="display: none;">
                    <input id="sum_ems_save_paypal" value="{$data.info.sum_ems_save_paypal}" style="display: none;">
                    <input id="sum_ins_reg_save_paypal" value="{$data.info.sum_ins_reg_save_paypal}"
                        style="display: none;">
                    <input id="sum_ins_ems_save_paypal" value="{$data.info.sum_ins_ems_save_paypal}"
                        style="display: none;">
                    <input id="sum_secret_reg_save_paypal" value="{$data.info.sum_secret_reg_save_paypal}"
                        style="display: none;">
                    <input id="sum_secret_ems_save_paypal" value="{$data.info.sum_secret_ems_save_paypal}"
                        style="display: none;">
                    <input id="sum_ins_secret_reg_save_paypal" value="{$data.info.sum_ins_secret_reg_save_paypal}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_save_paypal" value="{$data.info.sum_ins_secret_ems_save_paypal}"
                        style="display: none;">
                    <input id="sum_reg_crypto" value="{$data.info.sum_reg_crypto}" style="display: none;">
                    <input id="sum_ems_crypto" value="{$data.info.sum_ems_crypto}" style="display: none;">
                    <input id="sum_ins_reg_crypto" value="{$data.info.sum_ins_reg_crypto}" style="display: none;">
                    <input id="sum_ins_ems_crypto" value="{$data.info.sum_ins_ems_crypto}" style="display: none;">
                    <input id="sum_secret_reg_crypto" value="{$data.info.sum_secret_reg_crypto}"
                        style="display: none;">
                    <input id="sum_secret_ems_crypto" value="{$data.info.sum_secret_ems_crypto}"
                        style="display: none;">
                    <input id="sum_ins_secret_reg_crypto" value="{$data.info.sum_ins_secret_reg_crypto}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_crypto" value="{$data.info.sum_ins_secret_ems_crypto}"
                        style="display: none;">
                    <input id="sum_reg_save_crypto" value="{$data.info.sum_reg_save_crypto}" style="display: none;">
                    <input id="sum_ems_save_crypto" value="{$data.info.sum_ems_save_crypto}" style="display: none;">
                    <input id="sum_ins_reg_save_crypto" value="{$data.info.sum_ins_reg_save_crypto}"
                        style="display: none;">
                    <input id="sum_ins_ems_save_crypto" value="{$data.info.sum_ins_ems_save_crypto}"
                        style="display: none;">
                    <input id="sum_secret_reg_save_crypto" value="{$data.info.sum_secret_reg_save_crypto}"
                        style="display: none;">
                    <input id="sum_secret_ems_save_crypto" value="{$data.info.sum_secret_ems_save_crypto}"
                        style="display: none;">
                    <input id="sum_ins_secret_reg_save_crypto" value="{$data.info.sum_ins_secret_reg_save_crypto}"
                        style="display: none;">
                    <input id="sum_ins_secret_ems_save_crypto" value="{$data.info.sum_ins_secret_ems_save_crypto}"
                        style="display: none;">
                    <input id="nf_sum_reg_crypto" value="{$data.info.nf_sum_reg_crypto}" style="display: none;">
                    <input id="nf_sum_ems_crypto" value="{$data.info.nf_sum_ems_crypto}" style="display: none;">
                    <input id="nf_sum_ins_reg_crypto" value="{$data.info.nf_sum_ins_reg_crypto}"
                        style="display: none;">
                    <input id="nf_sum_ins_ems_crypto" value="{$data.info.nf_sum_ins_ems_crypto}"
                        style="display: none;">
                    <input id="nf_sum_secret_reg_crypto" value="{$data.info.nf_sum_secret_reg_crypto}"
                        style="display: none;">
                    <input id="nf_sum_secret_ems_crypto" value="{$data.info.nf_sum_secret_ems_crypto}"
                        style="display: none;">
                    <input id="nf_sum_ins_secret_reg_crypto" value="{$data.info.nf_sum_ins_secret_reg_crypto}"
                        style="display: none;">
                    <input id="nf_sum_ins_secret_ems_crypto" value="{$data.info.nf_sum_ins_secret_ems_crypto}"
                        style="display: none;">
                    {*<!-- <input id="sum_reg_master" value="{$data.info.sum_reg_master}" style="display: none;">
     <input id="sum_ems_master" value="{$data.info.sum_ems_master}" style="display: none;">
     <input id="sum_ins_reg_master" value="{$data.info.sum_ins_reg_master}" style="display: none;">
     <input id="sum_ins_ems_master" value="{$data.info.sum_ins_ems_master}" style="display: none;">
     <input id="sum_secret_reg_master" value="{$data.info.sum_secret_reg_master}" style="display: none;">
     <input id="sum_secret_ems_master" value="{$data.info.sum_secret_ems_master}" style="display: none;">
     <input id="sum_ins_secret_reg_master" value="{$data.info.sum_ins_secret_reg_master}" style="display: none;">
     <input id="sum_ins_secret_ems_master" value="{$data.info.sum_ins_secret_ems_master}" style="display: none;">
     <input id="sum_reg_master_discount" value="{$data.info.sum_reg_master_discount}" style="display: none;">
     <input id="sum_ems_master_discount" value="{$data.info.sum_ems_master_discount}" style="display: none;">
     <input id="sum_ins_reg_master_discount" value="{$data.info.sum_ins_reg_master_discount}" style="display: none;">
     <input id="sum_ins_ems_master_discount" value="{$data.info.sum_ins_ems_master_discount}" style="display: none;">
     <input id="sum_secret_reg_master_discount" value="{$data.info.sum_secret_reg_master_discount}" style="display: none;">
     <input id="sum_secret_ems_master_discount" value="{$data.info.sum_secret_ems_master_discount}" style="display: none;">
     <input id="sum_ins_secret_reg_master_discount" value="{$data.info.sum_ins_secret_reg_master_discount}" style="display: none;">
     <input id="sum_ins_secret_ems_master_discount" value="{$data.info.sum_ins_secret_ems_master_discount}" style="display: none;">-->
                    *}

                    <input id="gift_card_discount_text_sum_reg" value="{$data.info.gift_card_discount_text_sum_reg}"
                        style="display: none;">
                    <input id="gift_card_discount_text_sum_ems" value="{$data.info.gift_card_discount_text_sum_ems}"
                        style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_reg"
                        value="{$data.info.gift_card_discount_text_sum_ins_reg}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_ems"
                        value="{$data.info.gift_card_discount_text_sum_ins_ems}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_reg"
                        value="{$data.info.gift_card_discount_text_sum_secret_reg}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_ems"
                        value="{$data.info.gift_card_discount_text_sum_secret_ems}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_reg"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_reg}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_ems"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_ems}" style="display: none;">

                    <input id="gift_card_discount_text_sum_reg_paypal"
                        value="{$data.info.gift_card_discount_text_sum_reg_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ems_paypal"
                        value="{$data.info.gift_card_discount_text_sum_ems_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_reg_paypal"
                        value="{$data.info.gift_card_discount_text_sum_ins_reg_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_ems_paypal"
                        value="{$data.info.gift_card_discount_text_sum_ins_ems_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_reg_paypal"
                        value="{$data.info.gift_card_discount_text_sum_secret_reg_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_ems_paypal"
                        value="{$data.info.gift_card_discount_text_sum_secret_ems_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_reg_paypal"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_reg_paypal}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_ems_paypal"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_ems_paypal}" style="display: none;">

                    <input id="gift_card_discount_text_sum_reg_crypto"
                        value="{$data.info.gift_card_discount_text_sum_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ems_crypto"
                        value="{$data.info.gift_card_discount_text_sum_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_reg_crypto"
                        value="{$data.info.gift_card_discount_text_sum_ins_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_ems_crypto"
                        value="{$data.info.gift_card_discount_text_sum_ins_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_reg_crypto"
                        value="{$data.info.gift_card_discount_text_sum_secret_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_secret_ems_crypto"
                        value="{$data.info.gift_card_discount_text_sum_secret_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_reg_crypto"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_sum_ins_secret_ems_crypto"
                        value="{$data.info.gift_card_discount_text_sum_ins_secret_ems_crypto}" style="display: none;">

                    <input id="gift_card_discount_text_nf_sum_reg_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_ems_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_ins_reg_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_ins_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_ins_ems_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_ins_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_secret_reg_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_secret_reg_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_secret_ems_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_secret_ems_crypto}" style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_ins_secret_reg_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_ins_secret_reg_crypto}"
                        style="display: none;">
                    <input id="gift_card_discount_text_nf_sum_ins_secret_ems_crypto"
                        value="{$data.info.gift_card_discount_text_nf_sum_ins_secret_ems_crypto}"
                        style="display: none;">


                    <input id="rest_total_sum_reg" value="{$data.info.rest_total_sum_reg}" style="display: none;">
                    <input id="rest_total_sum_ems" value="{$data.info.rest_total_sum_ems}" style="display: none;">
                    <input id="rest_total_sum_ins_reg" value="{$data.info.rest_total_sum_ins_reg}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_ems" value="{$data.info.rest_total_sum_ins_ems}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_reg" value="{$data.info.rest_total_sum_secret_reg}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_ems" value="{$data.info.rest_total_sum_secret_ems}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_secret_reg" value="{$data.info.rest_total_sum_ins_secret_reg}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_secret_ems" value="{$data.info.rest_total_sum_ins_secret_ems}"
                        style="display: none;">

                    <input id="rest_total_sum_reg_paypal" value="{$data.info.rest_total_sum_reg_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_ems_paypal" value="{$data.info.rest_total_sum_ems_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_reg_paypal" value="{$data.info.rest_total_sum_ins_reg_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_ems_paypal" value="{$data.info.rest_total_sum_ins_ems_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_reg_paypal" value="{$data.info.rest_total_sum_secret_reg_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_ems_paypal" value="{$data.info.rest_total_sum_secret_ems_paypal}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_secret_reg_paypal"
                        value="{$data.info.rest_total_sum_ins_secret_reg_paypal}" style="display: none;">
                    <input id="rest_total_sum_ins_secret_ems_paypal"
                        value="{$data.info.rest_total_sum_ins_secret_ems_paypal}" style="display: none;">

                    <input id="rest_total_sum_reg_crypto" value="{$data.info.rest_total_sum_reg_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_ems_crypto" value="{$data.info.rest_total_sum_ems_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_reg_crypto" value="{$data.info.rest_total_sum_ins_reg_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_ems_crypto" value="{$data.info.rest_total_sum_ins_ems_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_reg_crypto" value="{$data.info.rest_total_sum_secret_reg_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_secret_ems_crypto" value="{$data.info.rest_total_sum_secret_ems_crypto}"
                        style="display: none;">
                    <input id="rest_total_sum_ins_secret_reg_crypto"
                        value="{$data.info.rest_total_sum_ins_secret_reg_crypto}" style="display: none;">
                    <input id="rest_total_sum_ins_secret_ems_crypto"
                        value="{$data.info.rest_total_sum_ins_secret_ems_crypto}" style="display: none;">

                    <input id="rest_total_nf_sum_reg_crypto" value="{$data.info.rest_total_nf_sum_reg_crypto}"
                        style="display: none;">
                    <input id="rest_total_nf_sum_ems_crypto" value="{$data.info.rest_total_nf_sum_ems_crypto}"
                        style="display: none;">
                    <input id="rest_total_nf_sum_ins_reg_crypto" value="{$data.info.rest_total_nf_sum_ins_reg_crypto}"
                        style="display: none;">
                    <input id="rest_total_nf_sum_ins_ems_crypto" value="{$data.info.rest_total_nf_sum_ins_ems_crypto}"
                        style="display: none;">
                    <input id="rest_total_nf_sum_secret_reg_crypto"
                        value="{$data.info.rest_total_nf_sum_secret_reg_crypto}" style="display: none;">
                    <input id="rest_total_nf_sum_secret_ems_crypto"
                        value="{$data.info.rest_total_nf_sum_secret_ems_crypto}" style="display: none;">
                    <input id="rest_total_nf_sum_ins_secret_reg_crypto"
                        value="{$data.info.rest_total_nf_sum_ins_secret_reg_crypto}" style="display: none;">
                    <input id="rest_total_nf_sum_ins_secret_ems_crypto"
                        value="{$data.info.rest_total_nf_sum_ins_secret_ems_crypto}" style="display: none;">

                    <div class="your-order__rows">
                        {if !$is_only_card}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <input checked="checked" id="c_82" class="checkbox__input" type="checkbox"
                                    value="1" name="insurance" pop_show="true">
                                <label for="c_82" class="checkbox__label"><span class="checkbox__text"><b
                                            style="font-weight: 500;">{#insurance#}</b></span></label>
                            </div>
                            <div class="your-order__price" style="font-weight: 500;">{$data.info.fm_insurance}</div>
                        </div>
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <input {if $data.info.secret_on}checked{/if} id="c_83" class="checkbox__input"
                                    type="checkbox" value="1" name="secret" onclick="secretPackage()">
                                <label for="c_83" class="checkbox__label"><span class="checkbox__text"><b
                                            style="font-weight: 500;">{#secret#}</b></span></label>
                            </div>
                            <div class="your-order__price" style="font-weight: 500;">{$data.info.secret_package}</div>
                        </div>
                        {if $data.info.ems ne 'none'}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <input {if $data.info.shipping_on eq 'ems' }checked{/if} id="c_86"
                                    class="checkbox__input" type="radio" value="ems" name="delivery"
                                    onclick="delivery_shipping()">
                                <label for="c_86" class="checkbox__label"><span class="checkbox__text">
                                        <b style="font-weight: 500;">{#express#}</b>
                                        {if $data.info.nf_ems ne 0}<span class="checkbox__add-text">{#over#}
                                            {$data.info.total_ems_discount}</span>{/if}
                                        <span class="checkbox__add-text">{#express_text#}</span>
                                    </span></label>
                            </div>
                            <div style="font-size: 14px;font-weight: 500;"
                                class="your-order__price {if $data.info.nf_ems eq 0}totals-order__old-price{/if}">
                                <span>{$data.info.ems}</span>{if $data.info.nf_ems eq 0}<p
                                    style="color: var(--green);">{#free#}</p>{/if}</div>
                        </div>
                        {/if}
                        {if $data.info.regular ne 'none'}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <input {if $data.info.shipping_on eq 'regular' }checked{/if} id="c_85"
                                    class="checkbox__input" type="radio" value="regular" name="delivery"
                                    onclick="delivery_shipping()">
                                <label for="c_85" class="checkbox__label"><span class="checkbox__text">
                                        <b style="font-weight: 500;">{#regular#}</b>
                                        {if $data.info.nf_regular ne 0}<span class="checkbox__add-text">{#over#}
                                            {$data.info.total_reg_discount}</span>{/if}
                                        <span class="checkbox__add-text">{#regular_text#}</span>
                                    </span></label>
                            </div>
                            {* <div class="your-order__price" style="font-weight: 500;">{if $data.info.nf_regular eq
                                0}{#free#}{else}{$data.info.regular}{/if}</div> *}
                            <div style="font-size: 14px;font-weight: 500;"
                                class="your-order__price {if $data.info.nf_regular eq 0}totals-order__old-price{/if}">
                                <span>{$data.info.regular}</span>{if $data.info.nf_regular eq 0}<p
                                    style="color: var(--green);">{#free#}</p>{/if}</div>
                        </div>
                        {/if}
                        {/if}
                        {* <!--{if $data.info.discount}
       <div class="your-order__row">
       <div class="your-order__checkbox checkbox">
       <label style="color: var(--green);"><span class="checkbox__text"><b>{#discount#}(-{$data.info.discount}%)</b></span></label>
       </div>
       <div style="color: var(--green); font-weight: 500;">-{$data.info.special_discount}</div>
       </div>
      {/if}--> *}
                        {if $data.info.coupon_discount_sum_nf_usd ne 0}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <label style="color: var(--red);"><span
                                        class="checkbox__text">{#discount2#}(-{$data.info.coupon_percent*100}%)</span></label>
                            </div>
                            <div style="color: var(--red); font-weight: 500;">-{$data.info.coupon_discount_sum}</div>
                        </div>
                        {/if}
                        {if $data.info.reorder_discount_sum_nf_usd ne 0}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <label style="color: var(--red);"><span
                                        class="checkbox__text"><b>{#discount2#}(-10%)</b></span></label>
                            </div>
                            <div style="color: var(--red); font-weight: 500;">-{$data.info.reorder_discount_sum}</div>
                        </div>
                        {/if}
                        {*<!--{if $data.info.master_discount ne 'none'}
       <div class="your-order__row">
        <div class="your-order__checkbox checkbox">
         <label style="color: var(--red); font-weight: 500;" ><span class="checkbox__text">{#master_discount#}(-5%)</span></label>
        </div>
        <div id="master_discount_sum" style="color: var(--red); font-weight: 500;"></div>
       </div>
      {/if}-->*}
                        {if $data.info.gift_card}
                        <div class="your-order__row">
                            <div class="your-order__checkbox checkbox">
                                <label style="color: var(--red); font-weight: 500;"><span
                                        class="checkbox__text">{#gift_card#} {$data.info.gift_card}</span></label>
                            </div>
                            <div id="gift_card_minus" style="color: var(--red); font-weight: 500;">-
                                {$data.info.gift_card_discount_text}</div>
                        </div>
                        {/if}
                        {if !$is_only_card}
                        <div class="your-order__row">
                            <div class="your-order__input enter-info__input"
                                style="margin-bottom: 0; margin-right:0;">
                                <label for="coupon" class="enter-info__label">{#coupon_card#}</label>
                                <input id="coupon" autocomplete="off" type="text" name="coupon"
                                    placeholder="" class="input"
                                    value="{if $data.info.coupon ne 'none'}{$data.info.coupon}{/if}{if $data.info.gift_card ne 'none'}{$data.info.gift_card}{/if}">
                            </div>
                            <button type="submit" id="coupon_submit" class="your-order__coupon-button"
                                style="right: 7px;">
                                <svg width="24" height="24">
                                    <use xlink:href="../style_checkout/images/icons/icons.svg#svg-arr-left"></use>
                                </svg>
                            </button>
                        </div>
                        {/if}
                        <div class="your-order__bottom-row">
                            {* <a href="{$path.page}/cart" class="your-order__edit-button">
                                <svg width="18" height="18">
                                    <use xlink:href="../style_checkout/images/icons/icons.svg#svg-arr-right"></use>
                                </svg>
                                <span>{#edit#}</span>
                            </a> *}
                            <div class="your-order__total totals-order" style="width: 100%;">
                                <h3 class="totals-order__title" style="width: 100%;">{#total#}</h3>
                                <div
                                    style="display:flex; align-items: center; width: 100%; justify-content:space-between;">
                                    {if $data.info.gift_card_balance > $data.info.total_check && $data.info.gift_card}
                                    <div class="totals-order__total" style="color: var(--green); font-size:18px;">
                                    </div>
                                    {else}
                                    {if $data.info.saving ne 0}
                                    <p class="totals-order__old-price"><span id="total_old"></span> <span
                                            id="discount_text"
                                            style="text-decoration: none;">-{$data.info.discount}%</span></p>
                                    {/if}
                                    {if $data.info.saving ne 0}
                                    <div class="totals-order__savings"
                                        style="color: rgb(148, 148, 148);font-size: 13px;">{#savings#} <span
                                            id="saving"></span></div>
                                    {/if}
                                    <div class="totals-order__total" style="color: var(--green); font-size:18px;">
                                    </div>
                                    {/if}
                                </div>
                                <input style="display: none;" hidden id="total" value="{$data.info.total_check}">
                                <input hidden id="total_crypto" value="">
                                <input style="display: none;" hidden id="total_clear"
                                    value="{$data.info.total_clear}">
                                <input style="display: none;" hidden id="fn_total_clear"
                                    value="{$data.info.fn_total_clear}">
                                <input style="display: none;" hidden id="discount" value="-{$data.info.discount}%">
                                <input style="display: none;" hidden id="discount_crypto"
                                    value="-{$data.info.discount+15}%">
                                <input style="display: none;" hidden id="discount_paypal"
                                    value="-{$data.info.discount-15}%">
                                <input type="hidden" id="gift_card_code" value="{$data.info.gift_card_code}">
                                <input type="hidden" id="gift_card_discount"
                                    value="{$data.info.gift_card_discount}">
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            <div class="enter-info__sections">
                <p class="timer2" style="font-weight: 800; font-size: 1.2rem;">
                    <span id="t2" style="display: inline-block; width: 63px; margin-bottom: 20px;">
                    </span>
                    {#timer#}
                </p>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{#info#}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__line poopup">
                                <span class="poopuptext" id="myPopup6">{#wrong_phone#}</span>
                                <div class="enter-info__country phone_code">
                                    <select name="phone_code" class="form" id="phone_code_select"
                                        data-pseudo-label="{#phone#}" data-scroll>
                                        {section name=i loop=$data.info.phone_codes}
                                        <option id="{$data.info.phone_codes[i].nicename}"
                                            data-asset="/style_checkout/images/countrys/{$data.info.phone_codes[i].nicename}.svg"
                                            value="{$data.info.phone_codes[i].phonecode}" {if
                                            $data.info.selected_phone_code eq
                                            $data.info.phone_codes[i].nicename}selected{/if}>
                                            +{$data.info.phone_codes[i].phonecode}</option>
                                        {/section}
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input required autocomplete="off" type="number" id="phone" name="phone"
                                        value="{$data.info.phone}" placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                                <input type="hidden" value="1" id="check_phone_number">
                            </div>
                            <div class="enter-info__line">
                                <div class="enter-info__country alt_phone_code">
                                    <select name="alt_phone_code" class="form" id="alt_phone_code_select"
                                        data-pseudo-label="{#alt_phone#}" data-scroll>
                                        {section name=i loop=$data.info.phone_codes}
                                        <option id="{$data.info.phone_codes[i].nicename}"
                                            data-asset="/style_checkout/images/countrys/{$data.info.phone_codes[i].nicename}.svg"
                                            value="{$data.info.phone_codes[i].phonecode}" {if
                                            $data.info.alt_selected_phone_code eq
                                            $data.info.phone_codes[i].nicename}selected{/if}>
                                            +{$data.info.phone_codes[i].phonecode}</option>
                                        {/section}
                                    </select>
                                </div>
                                <div class="enter-info__input enter-info__input--country" style="margin-bottom: 0;">
                                    <input autocomplete="off" type="number" id="alt_phone" name="alt_phone"
                                        value="{$data.info.alt_phone}" placeholder="000 000 00 00" class="input"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        type = "number" maxlength = "14">
                                </div>
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__input poopup">
                                <label for="email" class="enter-info__label">{#email#}</label>
                                <span class="poopuptext" id="myPopup5">{#wrong_email#}</span>
                                <input id="email" required autocomplete="off" type="email" name="email"
                                    class="input" value="{$data.info.email}">
                                <input type="hidden" value="1" id="check_email">
                            </div>
                            <div class="enter-info__input">
                                <label for="alt-email" class="enter-info__label">{#email2#} </label>
                                <input id="alt-email" autocomplete="off" type="email" name="alt-email"
                                    class="input" value="{$data.info.email2}">
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{#billing_address#}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__input">
                                <label for="firstname" class="enter-info__label">{#name#}</label>
                                <input id="firstname" autocomplete="off" type="text" name="firstname"
                                    class="input" value="{$data.info.firstname}">
                            </div>
                            <div class="enter-info__input">
                                <label for="lastname" class="enter-info__label">{#surname#}</label>
                                <input id="lastname" autocomplete="off" type="text" name="lastname"
                                    class="input" value="{$data.info.lastname}">
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__select select_billing_country">
                                <select required id="billing_country" name="billing_country" class="form"
                                    data-pseudo-label="{#country#}" data-scroll>
                                    {section name=i loop=$data.info.country_list}
                                    <option value="{$data.info.country_list[i].country_iso2}" {if
                                        $data.info.country_list[i].country_iso2 eq
                                        $data.info.billing_country}selected{/if}>
                                        {$data.info.country_list[i].country_name}</option>
                                    {/section}
                                </select>
                            </div>
                            {if $data.info.billing_country eq 'AU' or $data.info.billing_country eq 'US' or
                            $data.info.billing_country eq 'CA'}
                            <div class="enter-info__select select_billing_state">
                                <select required id="billing_state" name="billing_state" class="form"
                                    data-pseudo-label="{#state#}" data-scroll>
                                    {section name=i loop=$data.info.billing_states_list}
                                    <option value="{$data.info.billing_states_list[i].code}" {if
                                        $data.info.billing_states_list[i].code eq
                                        $data.info.billing_state}selected{/if}>{$data.info.billing_states_list[i].name}
                                    </option>
                                    {/section}
                                </select>
                            </div>
                            {/if}
                            <div class="enter-info__input">
                                <label for="billing_city" class="enter-info__label">{#city#}</label>
                                <input required id="billing_city" autocomplete="off" type="text" name="form[]"
                                    class="input" value="{$data.info.billing_city}">
                            </div>
                        </div>
                        <div class="enter-info__row">
                            <div class="enter-info__input">
                                <label for="billing_address" class="enter-info__label">{#address#}</label>
                                <input required id="billing_address" autocomplete="off" type="text"
                                    name="form[]" class="input" value="{$data.info.billing_address}">
                            </div>
                            <div class="enter-info__input">
                                <label for="billing_zip" class="enter-info__label">{#zip#}</label>
                                <input required id="billing_zip" autocomplete="off" type="text" name="form[]"
                                    class="input" value="{$data.info.billing_zip}">
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <div class="enter-info__shiping">
                        <h2 class="enter-info__title title">{#shipping_address#}</h2>
                        <div class="enter-info__checkbox checkbox">
                            <input id="c_1" class="checkbox__input" {if $data.info.address_match eq
                                false}checked{/if} type="checkbox" value="1" name="different">
                            <label for="c_1" class="checkbox__label">
                                <span class="checkbox__text">
                                    {#shipping_info#}
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="enter-info__rows">
                        <div class="enter-info__add-inputs add-info info-form__add-inputs" {if $data.info.address_match
                            eq true}hidden{/if}>
                            <div class="enter-info__row">
                                <div class="enter-info__select select_shipping_country">
                                    <select required id="shipping_country" name="shipping_country" class="form"
                                        data-pseudo-label="{#country#}" data-scroll>
                                        {section name=i loop=$data.info.country_list}
                                        <option value="{$data.info.country_list[i].country_iso2}" {if
                                            $data.info.country_list[i].country_iso2 eq
                                            $data.info.shipping_country}selected{/if}>
                                            {$data.info.country_list[i].country_name}</option>
                                        {/section}
                                    </select>
                                </div>
                                {if $data.info.shipping_country eq 'AU' or $data.info.shipping_country eq 'US' or
                                $data.info.shipping_country eq 'CA'}
                                <div class="enter-info__select select_shipping_state">
                                    <select required id="shipping_state" name="shipping_state" class="form"
                                        data-pseudo-label="{#state#}" data-scroll>
                                        {section name=i loop=$data.info.shipping_states_list}
                                        <option value="{$data.info.shipping_states_list[i].code}" {if
                                            $data.info.shipping_states_list[i].code eq
                                            $data.info.shipping_state}selected{/if}>
                                            {$data.info.shipping_states_list[i].name}</option>
                                        {/section}
                                    </select>
                                </div>
                                {/if}
                                <div class="enter-info__input poopup">
                                    <label for="shipping_city" class="enter-info__label">{#city#}</label>
                                    <span class="poopuptext" id="myPopup2">{#required#}</span>
                                    <input id="shipping_city" autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.shipping_city}">
                                    <input required type="hidden" value="0" id="check_city">
                                </div>
                            </div>
                            <div class="enter-info__row">
                                <div class="enter-info__input poopup">
                                    <label for="shipping_address" class="enter-info__label">{#address#}</label>
                                    <span class="poopuptext" id="myPopup3">{#required#}</span>
                                    <input id="shipping_address" autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.shipping_address}">
                                    <input required type="hidden" value="0" id="check_address">
                                </div>
                                <div class="enter-info__input poopup">
                                    <label for="shipping_zip" class="enter-info__label">{#zip#}</label>
                                    <span class="poopuptext" id="myPopup4">{#required#}</span>
                                    <input id="shipping_zip" autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.shipping_zip}">
                                    <input required type="hidden" value="0" id="check_zip">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="enter-info__block">
                    <h2 class="enter-info__title title">{#payment#}</h2>
                    <div class="enter-info__rows">
                        <div class="enter-info__row">
                            <div class="enter-info__line">
                                <div class="enter-info__select card_type poopup">
                                    <input required type="hidden"
                                        value="{if $data.info.success_trans eq '1'}1{else}0{/if}" id="success_trans">
                                    <select name="form[]" class="form" id="payment_type_select"
                                        data-pseudo-label="{#type#}" {if $rest_total==0}disabled{/if}>
                                        <!--{* <option value="temp" selected>{#pls_select#}</option> *}-->

                                        <option value="card" {if $data.info.payment_type
                                            !== 'gift_card'}selected{/if}>{#bank_card#}</option>
                                        {* {if $data.info.paypal_limit ne 'none' and $data.info.billing_country ne 'US'}
                                        <option value="paypal">Paypal +15% fee</option>
                                        {/if} *}
                                        <option value="crypto">{#crypto#} -15% extra off</option>
                                        {if $data.info.gift_card}
                                        <option value="gift_card" {if $data.info.payment_type=='gift_card'
                                            }selected{/if}>{#gift_card#}</option>
                                        {/if}

                                        <!--{* {if $data.info.billing_country eq 'US'}
            <option value="card" {if $data.info.payment_type !== 'gift_card'}selected{/if}>Visa/Mastercard/Amex/Discover</option>
           {else}
            {if $data.info.billing_country eq 'CA' or $data.info.success_trans eq '1'}
             <option value="card" {if $data.info.payment_type !== 'gift_card'}selected{/if}>Visa/Mastercard/Amex/Discover</option>
             {if $data.info.paypal_limit ne 'none'}
              <option value="paypal">Paypal +15% fee</option>
             {/if}
            {else}
             <option value="master" {if $data.info.payment_type !== 'gift_card'}selected{/if}>MasterCard</option>
             {if $data.info.paypal_limit ne 'none'}
              <option value="paypal">Paypal - Visa/Amex/Discover +15% fee</option>
             {/if}
            {/if}
           {/if}
           <option value="crypto">{#crypto#} -15% extra off</option>
           {if $data.info.gift_card}
            <option value="gift_card" {if $data.info.payment_type == 'gift_card'}selected{/if}>{#gift_card#}</option>
           {/if} *}-->


                                        <!--{* <option value="other">Visa/Amex/Discover</option> *}
           {* {if $data.info.billing_country eq 'AT' or $data.info.billing_country eq 'BE' or $data.info.billing_country eq 'BG' or $data.info.billing_country eq 'CY' or $data.info.billing_country eq 'CZ' or $data.info.billing_country eq 'DE' or $data.info.billing_country eq 'DK' or $data.info.billing_country eq 'EE' or $data.info.billing_country eq 'EL' or $data.info.billing_country eq 'ES' or $data.info.billing_country eq 'FI' or $data.info.billing_country eq 'FR' or $data.info.billing_country eq 'GR' or $data.info.billing_country eq 'HR' or $data.info.billing_country eq 'HU' or $data.info.billing_country eq 'IE' or $data.info.billing_country eq 'IT' or $data.info.billing_country eq 'LT' or $data.info.billing_country eq 'LU' or $data.info.billing_country eq 'LV' or $data.info.billing_country eq 'MT' or $data.info.billing_country eq 'NL' or $data.info.billing_country eq 'PL' or $data.info.billing_country eq 'PT' or $data.info.billing_country eq 'RO' or $data.info.billing_country eq 'SE' or $data.info.billing_country eq 'SI' or $data.info.billing_country eq 'SK'}
            <option value="sepa">SEPA -10%</option>
           {/if} *}-->
                                    </select>
                                    {if $data.info.billing_country ne 'US'}
                                    <span class="poopuptext" id="myPopup9">{#not_selected#}</span>
                                    <input type="hidden" value="1" id="check_payment_selected">
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="enter-info__card-content" {*{if $data.info.billing_country ne 'US' and
                            $data.info.billing_country ne 'CA' and $data.info.billing_country ne 'PR' }hidden{/if}*} {if
                            $rest_total==0}hidden{/if}>
                            <div class="enter-info__row">
                                {* <div class="enter-info__input">*}
                                    {* <label for="card_user_name" class="enter-info__label">{#cardholder#}</label>*}
                                    {* <input id="card_holder" autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.card_holder}">*}
                                    {* </div>*}
                                <div class="enter-info__input poopup">
                                    <label for="card_numb" class="enter-info__label">{#card_number#}</label>
                                    <span class="poopuptext" id="myPopup">{#wrong_card#}</span>
                                    <input id="card_numb" data-card autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.card_number}">
                                    <img class="enter-info__pay-systems hide"
                                        src="{{ asset('style_checkout/images/pay-systems/amex.svg') }}" width="33"
                                        height="20" alt="Awesome image">
                                    <input type="hidden" value="1" id="check_number">
                                </div>
                                <div class="enter-info__input poopup">
                                    <span class="poopuptext" id="myPopup10">{#required#}</span>
                                    <label for="bank_name" class="enter-info__label">{#bank_name#}</label>
                                    <input id="bank_name" autocomplete="off" type="text" name="form[]"
                                        class="input" value="{$data.info.bank_name}">
                                    <input type="hidden" value="1" id="check_bank_name">
                                </div>
                            </div>
                            <div class="enter-info__row enter-info__row--no-wrap">
                                <div class="enter-info__card-date poopup">
                                    <span class="poopuptext" id="myPopup1">{#wrong_exp_date#}</span>
                                    <div class="enter-info__select card_month">
                                        <select name="form[]" id = "card_month" name="card_month" class="form"
                                            data-pseudo-label="{#exp_date#}" data-scroll>
                                            <option value="" {if $data.info.card_month eq 'MM' }selected{/if}>MM
                                            </option>
                                            <option value="01" {if $data.info.card_month eq '01' }selected{/if}>01
                                            </option>
                                            <option value="02" {if $data.info.card_month eq '02' }selected{/if}>02
                                            </option>
                                            <option value="03" {if $data.info.card_month eq '03' }selected{/if}>03
                                            </option>
                                            <option value="04" {if $data.info.card_month eq '04' }selected{/if}>04
                                            </option>
                                            <option value="05" {if $data.info.card_month eq '05' }selected{/if}>05
                                            </option>
                                            <option value="06" {if $data.info.card_month eq '06' }selected{/if}>06
                                            </option>
                                            <option value="07" {if $data.info.card_month eq '07' }selected{/if}>07
                                            </option>
                                            <option value="08" {if $data.info.card_month eq '08' }selected{/if}>08
                                            </option>
                                            <option value="09" {if $data.info.card_month eq '09' }selected{/if}>09
                                            </option>
                                            <option value="10" {if $data.info.card_month eq '10' }selected{/if}>10
                                            </option>
                                            <option value="11" {if $data.info.card_month eq '11' }selected{/if}>11
                                            </option>
                                            <option value="12" {if $data.info.card_month eq '12' }selected{/if}>12
                                            </option>
                                        </select>
                                    </div>
                                    <div class="enter-info__select card_year">
                                        <select autocomplete="off" id = "card_year" name="card_year"
                                            class="form" data-scroll>
                                            <option value="" selected>YY</option>
                                            {section name=foo start=$data.info.year loop=$data.info.year+15 step=1}
                                            <option value="{$smarty.section.foo.index}" {if $data.info.card_year eq
                                                $smarty.section.foo.index}selected{/if}>{$smarty.section.foo.index}
                                            </option>
                                            {/section}
                                        </select>
                                    </div>
                                    <input type="hidden" value="1" id="check_expire">
                                </div>
                                <div class="enter-info__input enter-info__input--has-icon poopup">
                                    <span class="poopuptext" id="myPopup8">{#wrong_cvc#}</span>
                                    <label for="cvc_2" class="enter-info__label">{#cvv#}</label>
                                    <input id="cvc_2" autocomplete="off" type="number" name="form[]"
                                        class="input" data-card-cvc value="{$data.info.card_cvv}">
                                    <img class="enter-info__icon-input"
                                        src="{{ asset('style_checkout/images/icons/cvc-other.svg') }}" width="60"
                                        height="28" alt="Awesome image">
                                </div>
                            </div>
                            <button id="proccess" name="proccess" class="enter-info__button button">
                                <span>{#place#}</span>
                                <svg width="18" height="18">
                                    <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left"></use>
                                </svg>
                            </button>
                        </div>
                        <div class="enter-info__crypto-content" hidden>
                            <input type="hidden" id="pay_yes" value="0">
                            <input type="hidden" id="invoiceId" value="">
                            <div class="info_text_crypto" style="line-height: 24px">
                                <div>{#crypto_text_1#}</div>
                                <div>{#crypto_text_2#}</div>
                                <ul style="padding-left: 40px; line-height: 24px">
                                    <li style="list-style: disc">{#crypto_li_0#}</li>
                                    <li style="list-style: disc">{#crypto_li_1#}</li>
                                    <li style="list-style: disc">{#crypto_li_2#}</li>
                                    <li style="list-style: disc">{#crypto_li_3#}</li>
                                    <li style="list-style: disc">{#crypto_li_4#}</li>
                                    <li style="list-style: disc">{#crypto_li_5#}</li>
                                    <li style="list-style: disc">{#crypto_li_6#}</li>
                                </ul>
                            </div>
                            <div class="content-crypto">
                                <div class="content-crypto__items">
                                    <div class="content-crypto__item">
                                        <input id="cr_01" value="ETH_ETHEREUM" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_01">
                                            <svg width="40" height="40">
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-eth">
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
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-btc">
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
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-trc20">
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
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-erc20">
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
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-ltc">
                                                </use>
                                            </svg>
                                            <span>Litecoin</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_06" value="TRX_TRON" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_06">
                                            <svg width="40" height="40">
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-trx">
                                                </use>
                                            </svg>
                                            <span>Tron</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_07" value="BNB_BSC" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_07">
                                            <svg width="40" height="40">
                                                <use xlink:href="../style_checkout/images/icons/icons.svg#svg-bnb">
                                                </use>
                                            </svg>
                                            <span>Binance Coin</span>
                                        </label>
                                    </div>
                                    <div class="content-crypto__item">
                                        <input id="cr_08" value="TON_TON" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_08">
                                            <picture style="margin-bottom: 3px;">
                                                <source srcset="{{ asset('style_checkout/images/icons/ton.png') }}"
                                                    type="image/png">
                                                <img class="product-about__img"
                                                    src="{{ asset('style_checkout/images/icons/ton.png') }}" alt="TON"
                                                    width="40" height="40" loading="lazy">
                                            </picture>
                                            <span>TON</span>
                                        </label>
                                    </div>
                                    {* <div class="content-crypto__item">
                                        <input id="cr_09" value="BUSD_BSC" type="radio"
                                            name="crypt_currency">
                                        <label class="content-crypto__label" for="cr_08">
                                            <svg width="40" height="40">
                                                <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-bnbu">
                                                </use>
                                            </svg>
                                            <span>Binance USD</span>
                                        </label>
                                    </div> *}
                                </div>
                                <div style="text-align: center;" id="requisites_load" hidden>
                                    <img src="{{ asset('style_checkout/images/loading.gif') }}">
                                </div>
                                <div id="requisites" hidden>
                                    <div class="enter-info__note">
                                        <svg width="18" height="18">
                                            <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-time"></use>
                                        </svg>
                                        <p>{#invoice#} <b id="timer">30:00</b></p>
                                    </div>
                                    <div class="content-crypto__details details-payment">
                                        <div class="details-payment__qr-code">
                                            <picture><img id="qr_code" src="" width="140"
                                                    height="140" alt="Awesome image"></picture>
                                        </div>
                                        <div class="details-payment__rows">
                                            <div class="details-payment__row">
                                                <div class="details-payment__data">
                                                    <h3 class="details-payment__title">{#amount#}</h3>
                                                    <div class="details-payment__cells">
                                                        <span class="details-payment__amount"
                                                            id="crypto_total"></span>
                                                        {* <span class="details-payment__price"
                                                            id="crypto_price"></span> *}
                                                        <span class="details-payment__old-price"
                                                            id="crypto_price"></span>
                                                        <span class="details-payment__price"
                                                            id="crypto_discount_price"></span>
                                                    </div>
                                                </div>
                                                <button type="button" class="details-payment__copy-button">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="../style_checkout/images/icons/icons.svg#svg-copy">
                                                        </use>
                                                    </svg>
                                                </button>
                                                <div class="details-payment__tip">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="../style_checkout/images/icons/icons.svg#svg-checkmark">
                                                        </use>
                                                    </svg>
                                                    <span>{#copy#}</span>
                                                </div>
                                            </div>
                                            <div class="details-payment__row">
                                                <div class="details-payment__data">
                                                    <h3 class="details-payment__title">{#funds#}</h3>
                                                    <div class="details-payment__cells">
                                                        <span id="purse"
                                                            class="details-payment__amount">0xbcec7dc127978e0733ef40cd3255ba54a450b87c</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="details-payment__copy-button">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="../style_checkout/images/icons/icons.svg#svg-copy">
                                                        </use>
                                                    </svg>
                                                </button>
                                                <div class="details-payment__tip">
                                                    <svg width="18" height="18">
                                                        <use
                                                            xlink:href="../style_checkout/images/icons/icons.svg#svg-checkmark">
                                                        </use>
                                                    </svg>
                                                    <span>{#copy#}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="enter-info__note enter-info__note--has-offset">
                                        <p>{#payment_id#} <span id="invoce_p"></span></p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="enter-info__button button" id="paid">
                                <span>{#paid#}</span>
                            </button>
                            <button style="display: none;" type="submit" class="enter-info__button button"
                                id="waiting">
                                Approving transaction <img src="{{ asset('style_checkout/images/131.gif') }}" width="30px"
                                    height="30px">
                            </button>
                        </div>
                        <div class="enter-info__paypal-content" {*{if $data.info.billing_country eq 'US' or
                            $data.info.billing_country eq 'CA' or $data.info.billing_country eq 'PR' } hidden {/if}*}
                            hidden>
                            <div class="details-payment__row">
                                <div class="details-payment__data" style="text-align: center;">
                                    {#sepa_text#}
                                </div>
                            </div>
                            <button id="proccess_paypal" name="proccess" class="enter-info__button button">
                                <span>{#sepa_button#}</span>
                            </button>
                        </div>

                        <div class="enter-info__sepa-content" hidden>
                            <div class="details-payment__row">
                                <div class="details-payment__data" style="text-align: center;">
                                    {#sepa_sum_text#} €<span id="sepa_sum">{$data.info.sepasum}</span>
                                </div>
                                <div class="details-payment__data" style="text-align: center;">
                                    {#sepa_text#}
                                </div>
                            </div>
                            <button id="proccess_sepa" name="proccess" class="enter-info__button button">
                                <span>{#sepa_button#}</span>
                            </button>
                        </div>

                        <div class="enter-info__gift-card-content" {if $rest_total !==0}hidden{/if}>
                            <button id="proccess_gift_card" name="proccess" class="enter-info__button button">
                                <span>{#place#}</span>
                                <svg width="18" height="18">
                                    <use xlink:href="{{ asset('style_checkout/images/icons/icons.svg') }}#svg-arr-left"></use>
                                </svg>
                            </button>
                        </div>
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
                        {#card_warning#}
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script src="{{ asset('style_checkout/js/app.js') }}?v=11062021"></script>
</main>
<footer class="footer">
    <div class="footer__container">
        <p class="footer__text">{#copyright#} <br> {#ltd#}</p>
    </div>
</footer>

{literal}
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
