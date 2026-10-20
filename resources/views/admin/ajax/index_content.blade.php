{{-- <div class="statistic__rows">
    <h3 class="payment-details__caption">SUBSCRIBE POPUP STATUS</h3>
    <form name="new_currency_form" id="new_currency_form" method="POST">
        <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
            <tbody>
                <tr>
                    <td>
                        <div>
                            <input type="radio" name="subsc_popup_info" id="subsc_popup_on" value="1" @if (env('SUBSCRIBE_POPUP_STATUS', 1) == 1) checked @endif />
                            <label for="subsc_popup_on">
                                On
                            </label>
                        </div>
                    </td>
                    <td>
                        <div>
                            <input type="radio" name="subsc_popup_info" id="subsc_popup_off" value="0" @if (env('SUBSCRIBE_POPUP_STATUS', 1) == 0) checked @endif />
                            <label for="subsc_popup_off">
                                Off
                            </label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="saveSubscribePopupInfo();">
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
</div> --}}
<div class="statistic__top-row">
    <div class="payment-details__top-row">
        <div class="payment-details__currency">
            <h3 class="payment-details__caption">{{__('text.admin_main_page_main_page_title')}}</h3>
        </div>
    </div>
</div>
<div class="statistic__rows">
    <form id="main_page_form" name="main_page_form" method="POST">
    <div class="adm-dual">
        <div class="adm-dual__list">
            <h3 class="discounts__label">{{__('text.admin_main_page_products_title')}}</h3>
            <select class="input" name="not_showed_on_main_page_products_field" id="not_showed_on_main_page_products_field" size="30" multiple >
                @foreach ($not_showed_product as $product)
                    <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="adm-dual__controls">
            <span class="right_icon" id="add_button" title="{{__('text.admin_main_page_add_to_main_page_text')}}" onclick="addToMain()" role="button"></span>
            <span class="left_icon" id="delete_button" title="{{__('text.admin_main_page_delete_from_main_page_text')}}" onclick="deleteFromMain()" role="button"></span>
        </div>
        <div class="adm-dual__list">
            <h3 class="discounts__label">{{__('text.admin_main_page_showed_products_title')}}</h3>
            <select class="input" name="products_on_main_field" id="products_on_main_field" size="30" multiple >
                @foreach ($showed_product as $product)
                    <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="adm-dual__controls">
            <span class="up_icon" id="up_button" title='{{__('text.admin_main_page_up_pos_text')}}' onclick="productUpInSort()" role="button"></span>
            <span class="down_icon" id="down_button" title='{{__('text.admin_main_page_down_pos_text')}}' onclick="productDownInSort()" role="button"></span>
        </div>
    </div>
</form>
</div>