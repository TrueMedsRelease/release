<form id="show_packagings_form" name="show_packagings_form" method="POST">
    <table class="form_table" cellpadding="4" cellspacing="0" width="100%">
        <tbody  align="center">
        <tr>
            <td valign="top" width="26%">
                <h3 class="discounts__label">{{__('text.admin_packagings_show_all_products_title')}}</h3>
                <select class="input" name="all_products_field" id="all_products_field" size="30" style="width: 100%;" onchange="loadProductPackaging(this.value)">
                    @foreach ($all_products_info as $cur_product_info)
                        <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                    @endforeach
                </select>
            </td>
            <td width="3%">&nbsp;</td>
            <td valign="top" width="26%">
                <h3 class="discounts__label">{{__('text.admin_packagings_show_not_showed_packagings_title')}}</h3>
                <select class="input" name="not_showed_packagings_field" id="not_showed_packagings_field" size="30" multiple style="width: 100%;" >
                    @if(count($not_showed_packagings) > 0)
                        @foreach ($not_showed_packagings as $packaging)
                            <option value="{{ $packaging->id }}">{{ $packaging->dosage }} x {{ $packaging->num }} {{ $packaging->name }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td valign="middle" width="10%" align="center">
                <span class="right_icon" id="add_button" title="{#add_to_list_of_showed_products_text#}" style="cursor: pointer"
                onclick="addPackagngInShowed()">&nbsp;</span><br />
                <span class="left_icon" id="delete_button" title="{#delete_from_list_of_showed_products_text#}" style="cursor: pointer"
                onclick="deletePackagngFromShowed()">&nbsp;</span><br />
            </td>
            <td valign="top" width="26%">
                <h3 class="discounts__label">{{__('text.admin_packagings_show_showed_packagings_title')}}</h3>
                <select class="input" name="showed_packagings_field" id="showed_packagings_field" size="30" multiple style="width: 100%;">
                    @if(count($showed_packagings) > 0)
                        @foreach ($showed_packagings as $packaging)
                            <option value="{{ $packaging->id }}">{{ $packaging->dosage }} x {{ $packaging->num }} {{ $packaging->name }}</option>
                        @endforeach
                    @endif
                </select>
            </td>
            <td valign="middle" width="10%">
                <span class="up_icon" id="up_button" title='{#up_pos_text#}' style="cursor: pointer"
                onclick="packagingUpInSort()">&nbsp;</span><br />
                <span class="down_icon" id="down_button" title='{#down_pos_text#}' style="cursor: pointer"
                onclick="packagingDownInSort()">&nbsp;</span><br />
            </td>
        </tr>
        </tbody>
    </table>
</form>