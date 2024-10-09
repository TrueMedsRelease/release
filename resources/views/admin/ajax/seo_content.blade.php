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
                                <textarea id="description_field" name="description_field" class="input">
                                    @if ($page_properties)
                                        {{ $page_properties->description }}
                                    @endif
                                </textarea>
                            </div>
                            </div>
                        </div>
                        <div class="payment-details__item" style="flex-direction: row;padding-top: 1.2%; padding-bottom: 7.6%; gap:25px;">
                            <div class="label">{{__('text.admin_main_properties_keywords_title')}}</div>
                            <div class="input_elem" style="width: 100%">
                                <textarea id="keywords_field" name="keywords_field" class="input">
                                    @if ($page_properties)
                                        {{ $page_properties->keyword }}
                                    @endif
                                </textarea>
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