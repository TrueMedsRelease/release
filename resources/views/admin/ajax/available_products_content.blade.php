<!-- <div class="statistic__rows">
    <h3 class="payment-details__caption">Gift Card</h3>
    <form name="new_currency_form" id="new_currency_form" method="POST">
        <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
            <tbody>
                <tr>
                    <td>
                        <div>
                            <input type="radio" name="gift_card_info" id="card_available" value="available" @if (env('APP_GIFT_CARD') == 1) checked @endif />
                            <label for="card_available">
                                Available
                            </label>
                        </div>
                    </td>
                    <td>
                        <div>
                            <input type="radio" name="gift_card_info" id="card_unavailable" value="unavailable" @if (env('APP_GIFT_CARD') == 0) checked @endif />
                            <label for="card_unavailable">
                                Unavailable
                            </label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="saveGiftCardInfo();">
                            <span>
                                <span>
                                    {{__('text.admin_currencies_currencies_form_submit')}}
                                </span>
                                <svg width="20" height="20">
                                    <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                                </svg>
                            </span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div> -->
<div class="statistic__top-row">
    <div class="payment-details__top-row">
        <div class="payment-details__currency">
            <h3 class="payment-details__caption">{{__('text.admin_products_show_title')}}</h3>
        </div>
    </div>
</div>
<div class="statistic__rows">
    <form id="show_products_form" name="show_products_form" method="POST">
        <table class="form_table" cellpadding="4" cellspacing="0" width="90%">
            <tbody  align="center">
            <tr>
                <td width="10%">&nbsp;</td>
                <td valign="top" width="35%">
                    <h3 class="discounts__label">{{__('text.admin_products_show_not_showed_products_title')}}</h3>
                    <select class="input" name="not_showed_products_field" id="not_showed_products_field" size="30" multiple style="width: 100%;" >
                        @foreach ($not_showed_products_info as $cur_product_info)
                            <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td valign="middle" width="10%" align="center">
                    <span class="right_icon" id="add_button" title="{#add_to_list_of_showed_products_text#}" style="cursor: pointer"
                    onclick="addProductToShowed()">&nbsp;</span><br />
                    <span class="left_icon" id="delete_button" title="{#delete_from_list_of_showed_products_text#}" style="cursor: pointer"
                    onclick="deleteProductFromShowed()">&nbsp;</span><br />
                </td>
                <td valign="top" width="35%">
                    <h3 class="discounts__label">{{__('text.admin_products_show_showed_products_title')}}</h3>
                    <select class="input" name="showed_products_field" id="showed_products_field" size="30" multiple style="width: 100%;">
                        @foreach ($showed_products_info as $cur_product_info)
                            <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>