@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__rows">
		{{-- <h3 class="payment-details__caption">{{__('text.admin_currencies_new_currency_title')}}</h3>
		<form name="new_currency_form" id="new_currency_form" method="POST">
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <thead class="table_head">
                    <tr align="center">
                        <th width="5%">{{__('text.admin_currencies_default_title')}}</th>
                        <th width="5%">{{__('text.admin_currencies_show_title')}}</th>
                        <th width="19%">{{__('text.admin_currencies_name_title')}}</th>
                        <th width="12%">{{__('text.admin_currencies_prefix_title')}}</th>
                        <th width="12%">{{__('text.admin_currencies_code_title')}}</th>
                        <th width="12%">{{__('text.admin_currencies_coeff_title')}}</th>
                        <th width="19%">{{__('text.admin_currencies_country_iso2_title')}}</th>
                        <th width="16%">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="width: 40%; margin-left: 30%;">
                                <input type="checkbox" name="new_default_field" id="new_default_field" />
                            </div>
                        </td>
                        <td style="padding-left: 10px; text-align:center;">
                            <input type="checkbox" name="new_show_field" id="new_show_field" value="1"/>
                        </td>
                        <td >
                            <div class="input_elem" style="width: 80%; display: flex; justify-content: center;">
                                <input class="input" type="text" size="12" maxlength="32" name="new_name_field" id="new_name_field" value=""/>
                            </div>
                            <div id="new_name_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; display: flex; justify-content: center;">
                                <input class="input" type="text" size="10" maxlength="10" name="new_prefix_field" id="new_prefix_field" value=""/>
                            </div>
                            <div id="new_prefix_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; display: flex; justify-content: center;">
                                <input class="input" type="text" size="10" maxlength="10" name="new_code_field" id="new_code_field" value=""/>
                            </div>
                            <div id="new_code_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; display: flex; justify-content: center;">
                                <input class="input" type="text" size="10" maxlength="10" name="new_coef_field" id="new_coef_field" value=""/>
                            </div>
                            <div id="new_coef_error">
                            </div>
                        </td>
                        <td>
                            <div class="input_elem" style="width: 80%; display: flex; justify-content: flex-end;">
                                <input class="input" type="text" size="20" maxlength="120" name="new_country_iso2_field" id="new_country_iso2_field" value=""/>
                            </div>
                            <div id="new_country_iso2_error">
                            </div>
                        </td>
                        <td>
                            <a href="javascript: void()" class="add" id="new_add" onclick="show_loading_message('new_add', '{{__('text.admin_common_add_message')}}');">
                                {{__('text.admin_common_add_text')}}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
		</form> --}}
		<h3 class="payment-details__caption">{{__('text.admin_currencies_currencies_title')}}</h3>
		<form id="currencies_form" name="currencies_form" action="" method="POST">
            @csrf
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <tbody valign="middle" align="center">
                @foreach ($currencies_info as $cur_currency_info)
                    <tr>
                        <td width="5%">
                            <div style="width: 40%; margin-left: 30%;"><input type="radio" name="default_currency_code_field" id="default_currency_code_field" value="{{ $cur_currency_info['code'] }}" @if ($cur_currency_info['code'] == $default_currency_code) checked @endif/></div>
                        </td>
                        <td width="5%" style="padding-left: 10px">
                            <input type="checkbox" name="{{ $cur_currency_info['id'] }}_show_field" value="{{ $cur_currency_info['code'] }}"
                            @if ($cur_currency_info['show_in_menu']) checked @endif />
                        </td>
                        <td  width="19%" style="padding-top:0.7%;">
                            <div class="input_elem" style="width: 80%">
                                <input class="input" type="text" size="22" maxlength="32" name="{{ $cur_currency_info['id'] }}_name_field" id="{{ $cur_currency_info['id'] }}_name_field" value="{{ $cur_currency_info['name'] }}" />
                            </div>
                            <div id="{{ $cur_currency_info['id'] }}_name_error">
                            </div>
                        </td>
                        <td width="12%" style="padding-top:0.7%;">
                            <div class="input_elem" style="width: 80%">
                                <input class="input" type="text" size="10" maxlength="10" name="{{ $cur_currency_info['id'] }}_prefix_field" id="{{ $cur_currency_info['id'] }}_prefix_field" value="{{ $cur_currency_info['prefix'] }}" />
                            </div>
                            <div id="{{ $cur_currency_info['id'] }}_prefix_error">
                            </div>
                        </td>
                        <td width="12%" style="padding-top:0.7%;">
                            <div class="input_elem" style="width: 80%">
                                <input class="input" type="text" size="10" maxlength="10" name="{{ $cur_currency_info['id'] }}_code_field" id="{{ $cur_currency_info['id'] }}_code_field" value="{{ $cur_currency_info['code'] }}" />
                            </div>
                            <div id="{{ $cur_currency_info['id'] }}_code_error">
                            </div>
                        </td>
                        <td width="12%" style="padding-top:0.7%;">
                            <div class="input_elem" style="width: 80%">
                                <input class="input" type="text" size="10" maxlength="10" name="{{ $cur_currency_info['id'] }}_coef_field" id="{{ $cur_currency_info['id'] }}_coef_field" value="{{ $cur_currency_info['coef'] }}" />
                            </div>
                            <div id="{{ $cur_currency_info['id'] }}_coef_error">
                            </div>
                        </td>
                        <td width="19%" style="padding-top:0.7%;">
                            <div class="input_elem" style="width: 80%">
                                <input class="input" type="text" size="20" maxlength="120" name="{{ $cur_currency_info['id'] }}_country_iso2_field" id="{{ $cur_currency_info['id'] }}_country_iso2_field" value="{{ $cur_currency_info['country_iso2'] }}" />
                            </div>
                            <div id="{{ $cur_currency_info['id'] }}_country_iso2_error">
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6">
                        <div class="rowElem"><div id="currencies_messages">&nbsp;</div></div>
                        <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="show_loading_message('currencies_messages', '{{__('text.admin_common_saving_message')}}'); saveCurrenciesInfo();">
                            <span>
                                <span>
                                    {{__('text.admin_currencies_currencies_form_submit')}}
                                </span>
                                <svg width="20" height="20">
                                    <use xlink:href="/admin/images/icons/icons.svg#svg-checkmark"></use>
                                </svg>
                            </span>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
		</form>
    </div>
</div>

@endsection