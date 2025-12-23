@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')
<div id="properties_content">
    <div class="parked-domains">
        <div class="parked-domains__col" style="">
            <div class="payment-details__bottom">
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('new_password_messages', '{{__('text.admin_common_saving_message')}}'); renewalShop();">
                    <span>
                        <span>
                            {{ __('text.admin_renewal_shop') }}
                        </span>
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </button>
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('new_password_messages', '{{__('text.admin_common_saving_message')}}'); renewalDatabase();">
                    <span>
                        <span>
                            {{ __('text.admin_renewal_database') }}
                        </span>
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </button>
                <div id="new_password_messages"></div>
            </div>
            <div style="line-height: 2.5; color: red; font-size: 18px;">
                <p>{{ __('text.admin_renewal_text1') }}</p>
                <p>{{ __('text.admin_renewal_text2') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection