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