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
		<h3 class="payment-details__caption adm-section-title">{{__('text.admin_currencies_currencies_title')}}</h3>
		<form id="currencies_form" name="currencies_form" action="" method="POST">
            @csrf
            <div class="adm-table-scroll">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th class="adm-table__check">{{__('text.admin_currencies_default_title')}}</th>
                            <th class="adm-table__check">{{__('text.admin_currencies_show_title')}}</th>
                            <th>{{__('text.admin_currencies_name_title')}}</th>
                            <th>{{__('text.admin_currencies_prefix_title')}}</th>
                            <th>{{__('text.admin_currencies_code_title')}}</th>
                            <th>{{__('text.admin_currencies_coeff_title')}}</th>
                            <th>{{__('text.admin_currencies_country_iso2_title')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($currencies_info as $cur_currency_info)
                        <tr>
                            <td class="adm-table__check">
                                <input type="radio" name="default_currency_code_field" value="{{ $cur_currency_info['code'] }}" @if ($cur_currency_info['code'] == $default_currency_code) checked @endif/>
                            </td>
                            <td class="adm-table__check">
                                <input type="checkbox" name="{{ $cur_currency_info['id'] }}_show_field" value="{{ $cur_currency_info['code'] }}" @if ($cur_currency_info['show_in_menu']) checked @endif />
                            </td>
                            <td>
                                <input class="input" type="text" maxlength="32" name="{{ $cur_currency_info['id'] }}_name_field" id="{{ $cur_currency_info['id'] }}_name_field" value="{{ $cur_currency_info['name'] }}" />
                                <div id="{{ $cur_currency_info['id'] }}_name_error"></div>
                            </td>
                            <td>
                                <input class="input" type="text" maxlength="10" name="{{ $cur_currency_info['id'] }}_prefix_field" id="{{ $cur_currency_info['id'] }}_prefix_field" value="{{ $cur_currency_info['prefix'] }}" />
                                <div id="{{ $cur_currency_info['id'] }}_prefix_error"></div>
                            </td>
                            <td>
                                <input class="input" type="text" maxlength="10" name="{{ $cur_currency_info['id'] }}_code_field" id="{{ $cur_currency_info['id'] }}_code_field" value="{{ $cur_currency_info['code'] }}" />
                                <div id="{{ $cur_currency_info['id'] }}_code_error"></div>
                            </td>
                            <td>
                                <input class="input" type="text" maxlength="10" name="{{ $cur_currency_info['id'] }}_coef_field" id="{{ $cur_currency_info['id'] }}_coef_field" value="{{ $cur_currency_info['coef'] }}" />
                                <div id="{{ $cur_currency_info['id'] }}_coef_error"></div>
                            </td>
                            <td>
                                <input class="input" type="text" maxlength="120" name="{{ $cur_currency_info['id'] }}_country_iso2_field" id="{{ $cur_currency_info['id'] }}_country_iso2_field" value="{{ $cur_currency_info['country_iso2'] }}" />
                                <div id="{{ $cur_currency_info['id'] }}_country_iso2_error"></div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="adm-actions">
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('currencies_messages', '{{__('text.admin_common_saving_message')}}'); saveCurrenciesInfo();">
                    <span>
                        {{__('text.admin_currencies_currencies_form_submit')}}
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </button>
                <div id="currencies_messages" class="adm-actions__message"></div>
            </div>
		</form>
    </div>
</div>

@endsection