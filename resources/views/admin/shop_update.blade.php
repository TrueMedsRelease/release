@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')
<div id="properties_content">
    <div class="parked-domains">
        <div class="parked-domains__col">
            <div class="adm-alert">
                {{-- <p>{{ __('text.admin_renewal_text1') }}</p> --}}
                {{-- <p>{{ __('text.admin_renewal_text2') }}</p> --}}
                <div>
                    <p>Warning: If you update the store, your changes will be lost. If you need to update, please contact support.</p>
                    <p>If you want to update manually, please create a dump of your database and store.</p>
                </div>
            </div>
            <div class="adm-actions">
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('new_password_messages', '{{__('text.admin_common_saving_message')}}'); renewalShop();">
                    <span>
                        <span>
                            {{-- {{ __('text.admin_renewal_shop') }} --}}
                            Update Shop
                        </span>
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </button>
                <button type="button" class="payment-details__button button button--filled" onclick="show_loading_message('new_password_messages', '{{__('text.admin_common_saving_message')}}'); renewalDatabase();">
                    <span>
                        <span>
                            {{-- {{ __('text.admin_renewal_database') }} --}}
                            Update Database
                        </span>
                    </span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </button>
                <div id="new_password_messages" class="adm-actions__message"></div>
            </div>
        </div>
    </div>
</div>
@endsection