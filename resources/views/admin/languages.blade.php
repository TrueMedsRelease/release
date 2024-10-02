@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')
<div class="statistic" style="margin-top: 2%;">
    {{-- <div class="statistic__top-row">
    	<div class="payment-details__top-row">
    	    <div class="payment-details__currency">
    	        <h3 class="payment-details__caption">{{__('text.admin_languages_new_language_title')}}</h3>
    	    </div>
    	</div>
        <form id="new_langage_form" name="new_langage_form" method="POST">
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <thead class="table_head">
                    <th width="10%">{{__('text.admin_languages_default_title')}}</th>
                    <th width="10%">{{__('text.admin_languages_show_title')}}</th>
                    <th width="25%">{{__('text.admin_languages_name_title')}}</th>
                    <th width="15%">{{__('text.admin_languages_code_title')}}</th>
                    <th width="25%">{{__('text.admin_languages_country_iso2_title')}}</th>
                    <th width="15%">&nbsp;</th>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding-left: 10px">
                            <div style="width: 40%; margin-left: 30%;">
                                <input type="checkbox" name="new_default_field" id="new_default_field" />
                            </div>
                        </td>
                        <td style="padding-left: 10px; text-align:center;">
                            <input type="checkbox" name="new_show_field" id="new_show_field" value="1"/>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; margin-top:5%; display:flex; justify-content:center;">
                                <input class="input" type="text" size="20" maxlength="32" name="new_name_field" id="new_name_field" value=""/>
                            </div>
                            <div id="new_name_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; margin-top:5%;display:flex; justify-content:center;">
                                <input class="input" type="text" size="8" maxlength="8" name="new_code_field" id="new_code_field" value=""/>
                            </div>
                            <div id="new_code_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; margin-top:5%;display:flex; justify-content:flex-end;">
                            <input class="input" type="text" size="20" maxlength="120" name="new_country_iso2_field" id="new_country_iso2_field" value=""/>
                            </div>
                            <div id="new_country_iso2_error">
                            </div>
                        </td>
                        <td style="height: 81.53px;display: flex;align-items: center;justify-content: center;">
                            <a href="javascript: void()" id="new_add" class="add" onclick="show_loading_message('new_add', '{{__('text.admin_common_add_message')}}');">
                                {{__('text.admin_common_add_text')}}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
		</form>
	</div> --}}
	<div class="statistic__rows">
		<form id="languages_form" name="languages_form" action="" method="POST">
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <tbody valign="middle" align="center">
                    <tr>
                        <td colspan="6">
                            <h3 class="payment-details__caption" style="text-align:left;">{{__('text.admin_languages_languages_title')}}</h3>
                        </td>
                    </tr>
                    @foreach ($languages_info as $cur_lang_info)
                        <tr>
                            <td width="10%" style="padding-left: 10px">
                                <input  type="radio" name="default_language_code_field" value="{{ $cur_lang_info['code'] }}"
                                @if ($default_language_code == $cur_lang_info['code']) checked @endif />
                            </td>
                            <td width="10%" style="padding-left: 10px">
                                <input type="checkbox" name="{{ $cur_lang_info['id'] }}_show_field" value="{{ $cur_lang_info['code'] }}"
                                @if ($cur_lang_info['show']) checked @endif />
                            </td>
                            <td width="25%">
                                <div class="input_elem" style="width: 80%; margin-top:5%;">
                                    <input class="input" type="text" size="20" maxlength="32" name="{{ $cur_lang_info['id'] }}_name_field" id="{{ $cur_lang_info['id'] }}_name_field" value="{{ $cur_lang_info['name'] }}"/>
                                </div>
                                <div id="{{ $cur_lang_info['id'] }}_name_error">
                                </div>
                            </td>
                            <td width="15%">
                                <div class="input_elem" style="width: 80%; margin-top:5%;">
                                <input  class="input" type="text" size="8" maxlength="8" name="{{ $cur_lang_info['id'] }}_code_field" id="{{ $cur_lang_info['id'] }}_code_field" value="{{ $cur_lang_info['code'] }}" />
                                </div>
                                <div id="{{ $cur_lang_info['id'] }}_code_error">
                                </div>
                            </td>
                            <td width="25%">
                                <div class="input_elem" style="width: 80%; margin-top:5%;">
                                <input class="input" type="text" size="20" maxlength="120" name="{{ $cur_lang_info['id'] }}_country_iso2_field" id="{{ $cur_lang_info['id'] }}_country_iso2_field" value="{{ $cur_lang_info['country_iso2'] }}" />
                                </div>
                                <div id="{{ $cur_lang_info['id'] }}_country_iso2_error">
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6">
                            <div class="rowElem"><div id="languages_messages">&nbsp;</div></div>
                            <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="show_loading_message('languages_messages', '{{__('text.admin_common_saving_message')}}'); saveLanguagesInfo();">
                                <span>
                                    {{__('text.admin_languages_languages_form_submit')}}
                                </span>
                                <svg width="20" height="20">
                                    <use xlink:href="/admin/images/icons/icons.svg#svg-checkmark"></use>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
    	</form>
    </div>
</div>

@endsection