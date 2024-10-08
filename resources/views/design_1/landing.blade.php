var land_prod = '{literal}<style type="text/css">
    * {
        padding: 0px;
        margin: 0px;
        border: 0px;
    }

    html,
    body {
        height: 100%;
        min-width: 320px;
    }

    body {
        color: var(--main-color);
        line-height: 1;
        font-family: "Noto Sans Display", sans-serif;
        font-size: var(--main-offsize);
        -ms-text-size-adjust: 100%;
        -moz-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    input,
    button,
    textarea {
        font-size: inherit;
    }

    button {
        cursor: pointer;
        color: inherit;
        background-color: inherit;
    }

    a:hover {
        text-decoration: none;
    }

    a:link,
    a:visited {
        text-decoration: none;
    }

    a {
        color: inherit;
    }

    ul,
    li {
        list-style: none;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: 1px;
        padding: 0;
        overflow: hidden;
        white-space: nowrap;
        border: 0;
        clip: rect(0 0 0 0);
        -webkit-clip-path: inset(100%);
        clip-path: inset(100%);
    }

    .product__wrapper {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
    }

    .product__info {
        width: 100%;
        max-width: 525px;
        padding-top: 20px;
        border: 1px solid rgb(221, 232, 205);
        border-radius: 16px;
    }

    .product__info-items {
        margin-bottom: 25px;
    }

    .product__info-item:not(:last-child) {
        margin-bottom: 20px;
    }

    .product__info-title {
        margin-bottom: 15px;
        font-weight: 700;
        font-size: 18px;
        text-align: center;
    }

    .product__info-content {
        padding: 0 12px;
        margin-bottom: 25px;
    }

    .product__info-text {
        margin-bottom: 25px;
    }

    .product__info-headding {
        margin-bottom: 20px;
        font-weight: 700;
    }

    .product-about {
        width: 100%;
        max-width: 255px;
        margin-right: 17px;
    }

    .product-about__title {
        margin-bottom: 17px;
        font-size: 36px;
    }

    .product-about__accent {
        display: block;
        color: #5b7f2c;
        font-size: 24px;
    }

    .product_center {
        display: flex;
        justify-content: center;
    }

    .product-about__img {
        width: auto;
        max-width: 250px;
        height: auto;
        max-height: 250px;
        -o-object-fit: cover;
        object-fit: cover;
        margin-bottom: 45px;
    }

    .product-about__text {
        margin-bottom: 20px;
    }

    .product-about__text p:not(:last-child) {
        margin-bottom: 15px;
    }

    .product-about__characteristic {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .product-about__characteristic_ {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        flex-direction: row;
        margin-bottom: 20px;
    }

    .product-about__characteristic:not(:last-child) {
        margin-bottom: 15px;
    }

    .product-about__characteristic-meaning {
        font-weight: 700;
    }

    .product-about__characteristic-meaning--sin {
        color: #5b7f2c;
        font-weight: 400;
    }

    .product-about__characteristic-meaning--link {
        color: #5b7f2c;
        font-weight: 400;
        -webkit-transition: color 0.3s ease 0s;
        -o-transition: color 0.3s ease 0s;
        transition: color 0.3s ease 0s;
    }

    .product-about__info:not(:last-child) {
        margin-bottom: 15px;
    }

    .product-about__info-headding {
        display: block;
    }

    .product-about__info-items {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -5px;
    }

    .product-about__info-item {
        margin-right: 5px;
        color: #5b7f2c;
        -webkit-transition: color 0.3s ease 0s;
        -o-transition: color 0.3s ease 0s;
        transition: color 0.3s ease 0s;
    }

    .product-table {
        width: 100%;
        font-size: 16px;
        border-collapse: collapse;
    }

    .product-table__list {
        background-color: rgb(243, 246, 239);
    }

    .product-table__list:not(:last-child) {
        margin-bottom: 1px;
    }

    .product-table__list--top {
        font-weight: 700;
        background-color: rgb(221, 232, 205);
    }

    .product-table__list--top .product-table__per {
        font-weight: 700;
    }

    .product-table__list--top .product-table__price {
        text-align: center;
    }

    .product-table__list {
        position: relative;
    }

    .product-table__list:not(:last-child) {
        border-bottom: 1px solid #ffffff;
    }

    .no-webp .product-table__list--badge .product-table__package::before {
        background-image: url({/literal}"{$path.page}/../templates/design_1/images/icons/discount.png"{literal});
        display: block;
    }

    .product-table__list--badge .product-table__package::before {
        position: absolute;
        top: 50%;
        left: -15px;
        width: 26px;
        height: 26px;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        content: "";
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }

    .product-table th {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    .product-table__package {
        padding-left: 20px;
        font-weight: 700;
    }

    .product-table__per,
    .product-table__price,
    .product-table__add {
        padding-right: 15px;
    }

    .product-table__per {
        font-weight: 400;
    }

    .product-table__old,
    .product-table__discount {
        color: #e71a1a;
    }

    .product-table__old {
        text-decoration: line-through;
    }

    .product-table__prompt {
        display: block;
        color: #5b7f2c;
        font-size: 12px;
    }

    .product-table__cart {
        position: absolute;
        top: 12px;
        left: 20px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        width: 92%;
        padding: 12px;
        color: #ffffff;
        font-weight: 400;
        text-align: center;
        background-color: #507920;
        border-radius: 12px;
        visibility: hidden;
        opacity: 0;
        -webkit-transition: opacity 0.5s;
        -o-transition: opacity 0.5s;
        transition: opacity 0.5s;
    }

    .no-webp .product-table__cart::before {
        background-image: url({/literal}"{$path.page}/../templates/design_1/images/icons/cart-white.png"{literal});
    }

    .product-table__cart::before {
        background-image: url({/literal}"{$path.page}/../templates/design_1/images/icons/cart-white.png"{literal});
        position: absolute;
        top: 50%;
        left: 35%;
        width: 20px;
        height: 20px;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        content: "";
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }

    .product-table__cart--active {
        z-index: 10;
        visibility: visible;
        opacity: 0.9;
    }

    .product-table__btn {
        border: 0;
        position: relative;
        width: 38px;
        height: 39px;
        background-color: rgb(226, 236, 209);
        border-radius: 10px;
        -webkit-transition: background-color 0.3s ease 0s;
        -o-transition: background-color 0.3s ease 0s;
        transition: background-color 0.3s ease 0s;
    }

    .no-webp .product-table__btn::before {
        background-image: url({/literal}"{$path.page}/../templates/design_1/images/icons/cart.png"{literal}) !important;
    }

    .product-table__btn::before {
        background-image: url({/literal}"{$path.page}/../templates/design_1/images/icons/cart.png"{literal}) !important;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 23px;
        height: 23px;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        content: "";
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
    }

    .product-table__price {
        text-align: center;
    }

    .product-table__current {
        white-space: nowrap;
    }

    .product-table__current--discount {
        white-space: nowrap;
        text-align: center;
    }

    .product-about__characteristic-meaning--link:hover {
        color: var(--hover-green);
    }

    .product-about__info-item:hover {
        color: var(--hover-green);
    }

    .product-table__list:hover .product-table__cart {
        z-index: 10;
        visibility: visible;
        opacity: 0.9;
    }

    .product-table__btn:hover {
        background-color: var(--hover-green);
    }

    .webp .product-table__list--badge .product-table__package::before {
        background-image: url({/literal}{$path.page}/../templates/design_1/images/icons/discount.webp{literal});
    }

    .webp .product-table__cart::before {
        background-image: url({/literal}{$path.page}/../templates/design_1/images/icons/cart-white.webp{literal});
    }

    .webp .product-table__btn::before {
        background-image: url({/literal}{$path.page}/../templates/design_1/images/icons/cart.webp{literal});
    }
</style>
@if ($design != 'design_7' || $design != 'design_8')
<section  class="product">
    <div class="product__wrapper" style="justify-content: center;">
        <div class="product-about">
            <h2 class="product-about__title" id="scroll"> {$data.product_info.name} </h2>
            <picture class="product_center">
                <source srcset="{$path.page}/../app/set_images.php?pill={$data.product_info.name_for_image}"
                    type="image/webp"><img class="product-about__img"
                    src="{$path.page}/../app/set_images.php?pill={$data.product_info.name_for_image}"
                    alt="{$data.product__info.name}" width="150" height="150" loading="lazy">
            </picture>
            <ul class="product-about__characteristics"> {if $data.product_info.aktiv !== null}<li
                    class="product-about__characteristic_"><span class="product-about__characteristic-name"> {#active#}
                    </span>
                    <ul>
                        <li> {foreach item=cur_aktiv from=$data.product_info.aktiv}<a
                                href="{$path.page}/active/{$cur_aktiv.name}"
                                class="product-about__characteristic-meaning product-about__characteristic-meaning--link">
                                {$cur_aktiv.name} </a>{/foreach} </li>
                    </ul>
                </li>{/if}<li class="product-about__characteristic_"><span class="product-about__characteristic-name">
                        {#pack1#} </span> <span class="product-about__characteristic-meaning"> {#pack2#} </span></li>
                <div class="product-about__text">
                    <p> {$data.product_info.desc} </p>
                </div> {if $data.product_info.disease !== null}<li class="product-about__characteristic"><span
                        class="product-about__characteristic-name"> {#diseases#} </span>
                    <ul>
                        <li> {foreach item=cur_disease from=$data.product_info.disease}<a
                                href="{$path.page}/disease/{$cur_disease.url}"
                                class="product-about__characteristic-meaning product-about__characteristic-meaning--link">
                                {$cur_disease.name} </a>{/foreach} </li>
                    </ul>
                </li>{/if} {if $data.product_info.analog !== null}<li class="product-about__characteristic"><span
                        class="product-about__characteristic-name"> {$data.product_info.name} {#analogs#} </span>
                    <ul>
                        <li> {foreach item=cur_analog from=$data.product_info.analog}<a
                                class="product-about__characteristic-meaning--link"
                                href="{$path.page}/{$cur_analog.url}"> {$cur_analog.name} </a>{/foreach} </li>
                    </ul>
                </li>{/if} {if $data.product_info.sinonim !== null}<li class="product-about__characteristic"><span
                        class="product-about__characteristic-name"> {$data.product_info.name} {#others#} </span>
                    <ul>
                        <li> {foreach item=cur_other from=$data.product_info.sinonim}<a
                                href="{$path.page}/{$cur_other.url}"
                                class="product-about__characteristic-meaning--link"> {$cur_other.name} </a>{/foreach}
                        </li>
                    </ul>
                </li>{/if}
            </ul>
        </div>
        <div class="product__info">
            <ul class="product__info-items"> {assign var="prev_dosage" value="0"} {foreach
                item=cur_product_packaging from=$data.product_info.packagings name="product_dosages"} {if
                $smarty.foreach.product_dosages.iteration != 1 && $cur_product_packaging.dosage != $prev_dosage}
                </tbody>
                </table>
                </li> {/if} {if $cur_product_packaging.dosage != $prev_dosage}<li class="product__info-item">
                    <h3 class="product__info-title"> {$data.product_info.name} {$cur_product_packaging.dosage} </h3>
                    <table class="product-table">
                        <tbody>
                            <tr class="product-table__list product-table__list--top">
                                <th class="product-table__package">{#quantity_title#}</th>
                                <th class="product-table__per">{#price_per_pill_title#}</th>
                                <th class="product-table__price">{#price_title#}</th>
                                <th></th>
                            </tr> {assign var="prev_dosage" value=$cur_product_packaging.dosage} {/if}<tr
                                class="product-table__list{if $cur_product_packaging.price_per_pill == $data.best_price_by_pack[$cur_product_packaging.dosage]} product-table__list--badge{/if}">
                                <th class="product-table__package"> {$cur_product_packaging.num}
                                    {$cur_product_packaging.type_name} {if $cur_product_packaging.bonus} {if
                                    $cur_product_packaging.bonus.shipping}<span
                                        class="product-table__prompt">{$cur_product_packaging.bonus.shipping}</span>{/if}
                                    {/if} </th>
                                <th class="product-table__per">{$cur_product_packaging.price_per_pill}</th>
                                <th class="product-table__price"> {if $cur_product_packaging.discount != 0}<span
                                        class="product-table__old">{$cur_product_packaging.max_price_by_pack}</span><span
                                        class="product-table__discount">-{$cur_product_packaging.discount}%</span><span
                                        class="product-table__current">{#only#}{$cur_product_packaging.price}</span>{/if}
                                    {if $cur_product_packaging.discount == 0}<span
                                        class="product-table__current--discount">{$cur_product_packaging.price}</span>{/if}
                                </th>
                                <th class="product-table__add">
                                    <form action="{$path.page}/add_to_cart/{$cur_product_packaging.id}"><button
                                            class="product-table__btn" type="submit"><span
                                                class="sr-only">{#add_to_cart_text#}</span></button><button
                                            class="product-table__cart" type="submit"> {#add_to_cart_text#} </button>
                                </th>
                            </tr> {/foreach}
                        </tbody>
                    </table>
                </li>
            </ul>
        </div>
    </div>
</section> @endif'; document.write(land_prod);
