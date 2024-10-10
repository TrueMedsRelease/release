<div class="parked-domains">
    <div class="parked-domains__col">
        <form method="post" id="templates_form" name="templates_form">
            <div class="payment-details__items">
                <div class="payment-details__item">
                    <div class="payment-details__top-row">
                        <div class="payment-details__currency">
                            <h3 class="payment-details__caption">{{__('text.admin_main_properties_templates_title')}}</h3>
                        </div>
                        <p class="payment-details__code"></p>
                    </div>

                    <table>
                        <tr>
                            <td width="40%;">
                                <script type="text/javascript">
                                    var selected_template_image_id = "";
                                    var prev_template_image_id = "";
                                    var cur_template_image_id = "";
                                    function showImage(id){
                                        prev_template_image_id = cur_template_image_id;
                                        cur_template_image_id = id;
                                        $('#' + prev_template_image_id).css("display", "none");
                                        $('#' + cur_template_image_id).css("display", "");
                                    }
                                    function selectImage(id){
                                        selected_template_image_id = id;
                                        prev_checkout_template_image_id = cur_template_image_id;
                                        cur_template_image_id = selected_template_image_id;
                                    }
                                    function resetImage(){
                                        $('#' + cur_template_image_id).css("display", "none");
                                        $('#' + selected_template_image_id).css("display", "");
                                        cur_template_image_id = selected_template_image_id;
                                        prev_template_image_id = cur_template_image_id;
                                    }
                                </script>
                                @foreach ($templates as $cur_template_ar)
                                    @if ($cur_template_ar['name'] == $cur_template)
                                        <script type="text/javascript">
                                            selectImage('{{$cur_template_ar['name']}}_image');
                                        </script>
                                    @endif
                                    <div id="div_template_{{ $cur_template_ar['name'] }}" class="rowElem" onmouseover=" showImage('{{ $cur_template_ar['name'] }}_image');" onmouseout="resetImage();">
                                        <input type="radio" name="template_name_field" id="template_{{ $cur_template_ar['name'] }}" value="{{ $cur_template_ar['name'] }}" @if ($cur_template_ar['name'] == $cur_template) checked @endif
                                            onclick="selectImage('{{ $cur_template_ar['name'] }}_image');">
                                        <label for='template_{{ $cur_template_ar['name'] }}'>
                                            {{ $cur_template_ar['name'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </td>
                            <td width="10%;">&nbsp;</td>
                            <td width="50%;" valign="center">
                                @foreach ($templates as $cur_template_ar)
                                    <img id="{{ $cur_template_ar['name'] }}_image" src="{{ $cur_template_ar['scrin'] }}" width="200" alt="" style="display: @if ($cur_template_ar['name'] == $cur_template) block @else none @endif">
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="payment-details__bottom">
            <div id="templates_messages">&nbsp;</div>
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('templates_messages', '{{__('text.admin_common_saving_message')}}'); saveTemplate();">
                    <span>
                        {{__('text.admin_main_properties_password_form_submit')}}
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                    </svg>
                </button>
            </div>
        </form>
    </div>
    <div class="parked-domains__col">
        <form method="post" id="pixel_form" name="pixel_form">
            <div class="payment-details__items">
                <div class="payment-details__item">
                    <div class="payment-details__top-row">
                        <div class="payment-details__currency">
                            <h3 class="payment-details__caption">Pixel</h3>
                        </div>
                        <p class="payment-details__code"></p>
                    </div>

                    <table>
                        <tr>
                            <td width="40%;">
                                <div id="pixel_shop_block" class="rowElem" style="padding: 20px 0;">
                                    <input type="radio" name="pixel_name_field" id="pixel_shop" value="shop" onclick="loadPixelData(this.value)">
                                    <label for='pixel_shop'>
                                        shop
                                    </label>
                                </div>
                                <div id="pixel_checkout_block" class="rowElem" style="padding: 20px 0;">
                                    <input type="radio" name="pixel_name_field" id="pixel_checkout" value="checkout" onclick="loadPixelData(this.value)">
                                    <label for='pixel_shop'>
                                        checkout
                                    </label>
                                </div>
                                <div id="pixel_complete_block" class="rowElem" style="padding: 20px 0;">
                                    <input type="radio" name="pixel_name_field" id="pixel_complete" value="complete" onclick="loadPixelData(this.value)">
                                    <label for='pixel_complete'>
                                        complete
                                    </label>
                                </div>
                            </td>
                            <td width="10%;">&nbsp;</td>
                            <td width="50%;" valign="center">
                                <div class="input_elem" style="width: 100%">
                                    <textarea id="pixel_text" name="pixel_text" class="input"></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="payment-details__bottom">
            <div id="templates_messages">&nbsp;</div>
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('templates_messages', '{{__('text.admin_common_saving_message')}}'); SavePixelData();">
                    <span>
                        {{__('text.admin_main_properties_password_form_submit')}}
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__top-row">
        <div class="payment-details__top-row">
            <div class="payment-details__currency">
                <h3 class="payment-details__caption">{{__('text.admin_main_properties_titles_adn_tags_title')}}</h3>
            </div>
            <p class="payment-details__code"></p>
        </div>
    </div>
    <div class="statistic__rows">
        <form id="page_properties_form" name="page_properties_form" method="POST">
            <table style="width: 100%">
                <thead class="table_head">
                <th width="22%">{{__('text.admin_main_properties_page_names_title')}}</th>
                <th width="5%">&nbsp;</th>
                <th width="10%">Language</th>
                <th width="5%">&nbsp;</th>
                {{-- <th width="20%" align="left">{{__('text.admin_main_properties_available_tags_title')}}</th> --}}
                <th width="53%">{{__('text.admin_main_properties_page_properties_title')}}</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="table_head" width="22%">
                            <select class="input" name="pages_field" id="pages_field" size="15" style="width: 100%; overflow: hidden;">
                                <option value="main">{{__('text.admin_main_properties_main_page_name')}}</option>
                                <option value="product">{{__('text.admin_main_properties_product_page_name')}}</option>
                                <option value="category">{{__('text.admin_main_properties_category_page_name')}}</option>
                                <option value="active">{{__('text.admin_main_properties_aktiv_page_name')}}</option>
                                <option value="first_letter">{{__('text.admin_main_properties_first_letter_page_name')}}</option>
                                <option value="search">{{__('text.admin_main_properties_search_result_page_name')}}</option>
                                <option value="cart">{{__('text.admin_main_properties_cart_page_name')}}</option>
                                <option value="shipping">{{__('text.admin_main_properties_shipping_page_name')}}</option>
                                <option value="contact_us">{{__('text.admin_main_properties_contact_us_page_name')}}</option>
                                <option value="about_us">{{__('text.admin_main_properties_about_us_page_name')}}</option>
                                <option value="faq">{{__('text.admin_main_properties_faq_page_name')}}</option>
                                <option value="moneyback">{{__('text.admin_main_properties_moneyback_page_name')}}</option>
                                <option value="testimonials">{{__('text.admin_main_properties_testimonials_page_name')}}</option>
                                <option value="affiliate">{{__('text.admin_main_properties_affiliate_page_name')}}</option>
                            </select>
                        </td>
                        <td width="5%">&nbsp;</td>
                        <td width="10%">
                            <select class="input" name="language_field" id="language_field" size="15" style="width: 100%;">
                                @foreach ($language::GetAllLanuages() as $item)
                                    <option value="{{ $item['id'] }}">{{ Str::ucfirst($item['code']) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td width="5%">&nbsp;</td>
                        <td align="left" width="53%">
                            <div class="payment-details__item" style="flex-direction: row; padding-top: 6.2%; gap:25px;">
                                <div class="label">{{__('text.admin_main_properties_title_title')}}</div>
                                <div class="input_elem" style="width: 100%">
                                    <input type="text" id="title_field" name="title_field" size="30" maxlength="256" @if ($page_properties)
                                        value="{{ $page_properties->title }}"
                                    @else
                                        value=""
                                    @endif class="input"/>
                                </div>
                                </div>
                            </div>
                            <div class="payment-details__item" style="flex-direction: row; padding-top: 1.2%; padding-bottom: 7.6%; gap:25px;">
                                <div class="label">{{__('text.admin_main_properties_description_title')}}</div>
                                <div class="input_elem" style="width: 100%">
                                    <textarea id="description_field" name="description_field" class="input">@if ($page_properties){{ $page_properties->description }}@endif</textarea>
                                </div>
                                </div>
                            </div>
                            <div class="payment-details__item" style="flex-direction: row;padding-top: 1.2%; padding-bottom: 7.6%; gap:25px;">
                                <div class="label">{{__('text.admin_main_properties_keywords_title')}}</div>
                                <div class="input_elem" style="width: 100%">
                                    <textarea id="keywords_field" name="keywords_field" class="input">@if ($page_properties){{ $page_properties->keyword }}@endif</textarea>
                                </div>
                                </div>
                            </div>
                            <div  style="text-align: center;"><div id="titles_and_tags_messages">&nbsp;</div></div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div style="padding: 10px 0; line-height: 1.5;">
                If you want to see page properties choose "Page name" and "Language" and <b>click button "Load"</b>
            </div>
            <div style="display: flex; gap:20px;">
                <button type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="loadPageProperties();">
                    <span>
                        Load
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                    </svg>
                </button>
                <button type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="show_loading_message('titles_and_tags_messages', '{{__('text.admin_common_saving_message')}}'); savePageProperties();">
                    <span>
                        {{__('text.admin_main_properties_titles_and_tags_form_submit')}}
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__top-row">
        <div class="payment-details__top-row">
            <div class="payment-details__currency">
                <h3 class="payment-details__caption">Product URL</h3>
            </div>
            <p class="payment-details__code"></p>
        </div>
    </div>

    <form id="products_form" name="products_form" method="POST">
        <table style="width: 100%;">
            <tbody  align="center">
                <tr>
                    <td valign="top" width="28%" class="products_on_products">
                        <select class="input" name="all_products_field" id="all_products_field" size="50" style="width: 100%;"
                                onchange="show_loading_message('products_loading_messages', '{{__('text.admin_common_loading_message')}}');
                                    loadProductURL(this.value);">
                            @foreach ($all_products_info as $cur_product_info)
                                <option value="{{ $cur_product_info['id'] }}">{{ $cur_product_info['name'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td width="12%">
                        <div id="products_loading_messages" style="margin-bottom: 870px"></div>
                    </td>
                    @if (count($product_url) > 0)
                        <td valign="middle" width="60%" align="center">
                            <fieldset>
                                <legend class="discounts__label">{{__('text.admin_products_product_url_title')}}</legend>
                                @foreach ($language::GetAllLanuages() as $item)
                                    <div class="products__enters">
                                        <div class="label">{{ $item['name'] }}:</div>
                                        <div class="input_elem">
                                            <input class="input" type="text" id="{{ $item['id'] }}_url_field" name="{{ $item['id'] }}_url_field" value="{{ $product_url[$item['id']] }}" size="32" maxlength="64"/>
                                        </div>
                                        <div id="{{ $item['id'] }}_url_error"></div>
                                    </div>
                                    </div>
                                @endforeach
                            </fieldset>
                            <fieldset>
                                <div id="products_saving_messages"></div>
                            </fieldset>
                            <button style="margin-top:2%" type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled"
                                    onclick="show_loading_message('products_saving_messages', '{{__('text.admin_common_saving_message')}}');
                                        saveProductURL();
                                    ">
                                <span>
                                    {{__('text.admin_products_products_form_submit')}}
                                </span>
                                <svg width="20" height="20">
                                    <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                                </svg>
                            </button>
                        </td>
                    @else
                        <td valign="middle" width="60%" align="center">
                            <fieldset>
                                <legend class="discounts__label">{{__('text.admin_products_product_url_title')}}</legend>
                                @foreach ($language::GetAllLanuages() as $item)
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
                                <div id="products_saving_messages"></div>
                            </fieldset>
                            <button style="margin-top:2%" type="button" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled"
                                    onclick="show_loading_message('products_saving_messages', '{{__('text.admin_common_saving_message')}}');
                                        saveProductURL();
                                    ">
                                <span>
                                    {{__('text.admin_products_products_form_submit')}}
                                </span>
                                <svg width="20" height="20">
                                    <use xlink:href="/admin_style/images/icons/icons.svg#svg-checkmark"></use>
                                </svg>
                            </button>
                        </td>
                    @endif
                </tr>
            </tbody>
        </table>
    </form>
</div>

