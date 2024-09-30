<form id="main_page_form" name="main_page_form" method="POST">
    <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%;">
        <tbody align="center">
        <tr>
            <td width="10%">&nbsp;</td>
            <td valign="top" width="35%">
                <h3 class="discounts__label">{{__('text.admin_main_page_products_title')}}</h3>
                <select class="input" name="not_showed_on_main_page_products_field" id="not_showed_on_main_page_products_field" size="30" multiple style="width: 100%;" >
                    @foreach ($not_showed_product as $product)
                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                    @endforeach
                </select>
            </td>
            <td valign="middle" width="10%" align="center">
                <span class="right_icon" id="add_button" title="{{__('text.admin_main_page_add_to_main_page_text')}}" style="cursor: pointer"
                onclick="addToMain()">&nbsp;</span><br />
                <span class="left_icon" id="delete_button" title="{{__('text.admin_main_page_delete_from_main_page_text')}}" style="cursor: pointer"
                onclick="deleteFromMain()">&nbsp;</span><br />
            </td>
            <td valign="top" width="35%">
                <h3 class="discounts__label">{{__('text.admin_main_page_showed_products_title')}}</h3>
                <select class="input" name="products_on_main_field" id="products_on_main_field" size="30" multiple style="width: 100%;">
                    @foreach ($showed_product as $product)
                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                    @endforeach
                </select>
            </td>
            <td valign="middle" width="10%">
                <span class="up_icon" id="up_button" title='{{__('text.admin_main_page_up_pos_text')}}' style="cursor: pointer"
                onclick="productUpInSort()">&nbsp;</span><br />
                <span class="down_icon" id="down_button" title='{{__('text.admin_main_page_down_pos_text')}}' style="cursor: pointer"
                onclick="productDownInSort()">&nbsp;</span><br />
            </td>
        </tr>
        </tbody>
    </table>
</form>