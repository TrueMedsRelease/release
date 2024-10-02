<form id="products_form" name="products_form" method="POST">
    {{-- <input type="hidden" name="page_name" value="admin_product"> --}}
    <table style="width: 100%;">
        <tbody  align="center">
            <tr>
                <td valign="top" width="28%" class="products_on_products">
                    <select class="input" name="all_products_field" id="all_products_field" size="50" style="width: 100%;"
                            onchange="show_loading_message('products_loading_messages', '{{__('text.admin_common_loading_message')}}');
                                loadProductInfo(this.value);">
                        @foreach ($all_products_info as $cur_product_info)
                            <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                        @endforeach
                    </select>
                </td>
                <td width="12%">
                    &nbsp;
                    <div id="products_loading_messages"></div>
                </td>
                @if (count((array)$product_info) > 0)
                    <td valign="middle" width="60%" align="center">
                        <fieldset style="display: flex; justify-content:space-around;">
                            <div class="rowElem">
                                <div class="label">
                                    <label for="is_show_field" style="display: flex;justify-content:space-around;width: 80px;">
                                        <input type="checkbox" name="is_show_field" id="is_show_field"  class="no_input_style" @if ($product_info->is_showed == 1) checked @endif style="margin-right: 10%;"/>
                                        {{__('text.admin_products_is_show_title')}}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_name_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <input class="input" readonly type="text" id="{{ $item['id'] }}_name_field" name="{{ $item['id'] }}_name_field" value="{{ $product_info->names[$item['id']] }}" size="32" maxlength="64"/>
                                    </div>
                                    <div id="{{ $item['id'] }}_name_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_desc_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters" style="flex-direction: row;padding-top: 1.2%; padding-bottom: 7.6%;">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <textarea class="input" id="{{ $item['id'] }}_desc_field" name="{{ $item['id'] }}_desc_field" cols="40" rows="4">{{ $product_info->descriptions[$item['id']] }}</textarea>
                                    </div>
                                    <div id="{{ $item['id'] }}_desc_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_url_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_url_field" name="{{ $item['id'] }}_url_field" value="{{ $product_info->urls[$item['id']] }}" size="32" maxlength="64"/>
                                    </div>
                                    <div id="{{ $item['id'] }}_url_error"></div>
                                </div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_params_title')}}</legend>
                            <div class="products__enters" style="flex-direction: row;padding-top: 1.2%; padding-bottom: 7.6%;">
                                <div class="label">{{__('text.admin_products_product_manufacturer_title')}}</div>
                                <div class="input_elem">
                                    <textarea class="input" id="sinonims_field" name="sinonims_field" cols="40" rows="4">{{ implode(', ', $product_info->sinonim) }}</textarea>
                                </div>
                                <div id="sinonims_error"></div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_seo_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="label" style='text-align: left;font-weight: bold;margin-left: 5%;'>{{ $item['name'] }}</div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_title_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_title_field" name="{{ $item['id'] }}_title_field" value="{{ $product_info->seo[$item['id']]['title'] }}" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_title_error"></div>
                                </div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_keywords_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_keywords_field" name="{{ $item['id'] }}_keywords_field" value="{{ $product_info->seo[$item['id']]['keywords'] }}" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_keywords_error"></div>
                                </div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_description_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_description_field" name="{{ $item['id'] }}_description_field" value="{{ $product_info->seo[$item['id']]['description'] }}" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_description_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_price_title')}}</legend>
                            <div id="product_prices" style="display:flex; flex-direction:column;">
                                @foreach ($product_info->packaging_info as $pack_id => $packaging_info)
                                    <div class="rowElem">
                                        <div class="label" style="text-align: left;font-weight: bold;margin-left: 5%;">
                                            <h4>{{ $pack_id }}</h4>
                                        </div>
                                    </div>
                                    @foreach ($packaging_info as $pack)
                                        <div class="rowElem" style="padding-top:1%; display:flex; align-items:center;">
                                            <div class="label" style="width: 30%; float: left;">{{ $pack['num'] }} {{__('text.admin_products_form_tabs_text')}}</div>
                                            <div id="{{ $pack['pack_id'] }}_price_error" style="width: 10%; float: left;">&nbsp;</div>
                                            <div class="input_elem packaging_price_input" style="width: 30%; float: left; display:flex;">
                                                <input class="input" type="text" size="6" name="{{ $pack['pack_id'] }}_price" id="{{ $pack['pack_id'] }}_price" value="{{ $pack['price'] }}">
                                            </div>
                                            <div class="label" style="width: 30%; float: right;">
                                                {{__('text.admin_products_form_min_price_title')}} <span class="strong_text">{{ $pack['min_price'] }}$(USD)</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </fieldset>
                        <fieldset>
                            <div id="products_saving_messages"></div>
                        </fieldset>
                        <button style="margin-top:2%" type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled"
                                onclick="show_loading_message('products_saving_messages', '{{__('text.admin_common_saving_message')}}');
                                    saveProductInfo();
                                ">
                            <span>
                                {{__('text.admin_products_products_form_submit')}}
                            </span>
                            <svg width="20" height="20">
                                <use xlink:href="/admin/images/icons/icons.svg#svg-checkmark"></use>
                            </svg>
                        </button>
                    </td>
                @else
                    <td valign="middle" width="60%" align="center">
                        <fieldset style="display: flex; justify-content:space-around;">
                            <div class="rowElem">
                                <div class="label">
                                    <label for="is_show_field" style="display: flex;justify-content:space-around;width: 80px;">
                                        <input type="checkbox" name="is_show_field" id="is_show_field"  class="no_input_style" style="margin-right: 10%;"/>
                                        {{__('text.admin_products_is_show_title')}}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_name_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <input class="input" readonly type="text" id="{{ $item['id'] }}_name_field" name="{{ $item['id'] }}_name_field" size="32" maxlength="64"/>
                                    </div>
                                    <div id="{{ $item['id'] }}_name_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_desc_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters" style="flex-direction: row;padding-top: 1.2%; padding-bottom: 7.6%;">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <textarea class="input" id="{{ $item['id'] }}_desc_field" name="{{ $item['id'] }}_desc_field" cols="40" rows="4"></textarea>
                                    </div>
                                    <div id="{{ $item['id'] }}_desc_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_url_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters">
                                    <div class="label">{{ $item['name'] }}:</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_url_field" name="{{ $item['id'] }}_url_field" size="32" maxlength="64"/>
                                    </div>
                                    <div id="{{ $item['id'] }}_url_error"></div>
                                </div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_params_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="products__enters">
                                    <div class="label">{{ $item['name'] }} - {{__('text.admin_products_product_manufacturer_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_manufacturer_field" name="{{ $item['id'] }}_manufacturer_field" size="32" maxlength="64" />
                                    </div>
                                    <div id="{{ $item['id'] }}_manufacturer_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_seo_title')}}</legend>
                            @foreach ($Language::GetAllLanuages() as $item)
                                <div class="label" style='text-align: left;font-weight: bold;margin-left: 5%;'>{{ $item['name'] }}</div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_title_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_title_field" name="{{ $item['id'] }}_title_field" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_title_error"></div>
                                </div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_keywords_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_keywords_field" name="{{ $item['id'] }}_keywords_field" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_keywords_error"></div>
                                </div>
                                <div class="products__enters">
                                    <div class="label">{{__('text.admin_products_seo_description_title')}}</div>
                                    <div class="input_elem">
                                        <input class="input" type="text" id="{{ $item['id'] }}_description_field" name="{{ $item['id'] }}_description_field" size="32" maxlength="256" />
                                    </div>
                                    <div id="{{ $item['id'] }}_description_error"></div>
                                </div>
                            @endforeach
                        </fieldset>
                        <fieldset>
                            <legend class="discounts__label">{{__('text.admin_products_product_price_title')}}</legend>
                            <div id="product_prices" style="display:flex; flex-direction:column;">
                            </div>
                        </fieldset>
                        <fieldset>
                            <div id="products_saving_messages"></div>
                        </fieldset>
                        <button style="margin-top:2%" type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled"
                                onclick="show_loading_message('products_saving_messages', '{{__('text.admin_common_saving_message')}}');
                                    saveProductInfo();
                                ">
                            <span>
                                {{__('text.admin_products_products_form_submit')}}
                            </span>
                            <svg width="20" height="20">
                                <use xlink:href="/admin/images/icons/icons.svg#svg-checkmark"></use>
                            </svg>
                        </button>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>
</form>