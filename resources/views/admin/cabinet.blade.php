@extends('admin.layouts.main')

@section('title', $title)
@section('page_name', $title)

@section('content')

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Embedded Customer Cabinet</h3>
        <p style="margin: 0 0 15px; max-width: 760px;">
            When enabled, the "My Account" button opens the customer cabinet
            <b>inside this shop</b> (orders are loaded from the cabinet via API).
            When disabled, customers are redirected to true-help as before.
        </p>
        <form name="cabinet_properties" id="cabinet_properties" method="POST">
            <table class="form_table" cellpadding="4" cellspacing="0" style="width:100%">
                <tbody style="display: flex; gap: 15px; flex-direction: column;">
                    <tr style="display:flex; justify-content: space-between; padding-top: 15px; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Status: <b>{{ $cabinet_enabled ? 'Enabled' : 'Disabled' }}</b>
                                @if ($cabinet_enabled && $cabinet_secret === '')
                                    <span style="color: #b00;">— secret key is missing, cabinet will not work</span>
                                @endif
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="cabinet_enabled" id="cabinet_on" value="1" @if ($cabinet_enabled) checked @endif />
                                <label for="cabinet_on">
                                    Enabled
                                </label>
                            </div>
                        </td>
                        <td style="width: 35%;">
                            <div>
                                <input type="radio" name="cabinet_enabled" id="cabinet_off" value="0" @if (!$cabinet_enabled) checked @endif />
                                <label for="cabinet_off">
                                    Disabled
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr style="display:flex; justify-content: space-between; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Embedded login (from cabinet)
                            </div>
                            <div style="font-size: 12px; color: #666;">
                                Refreshed by "Test connection". When allowed, customers sign in
                                right here in the shop; otherwise they go to the cabinet pages.
                            </div>
                        </td>
                        <td style="width: 70%;" colspan="2">
                            <div>
                                <b>{{ $cabinet_embedded ? 'Allowed' : 'Not allowed' }}</b>
                            </div>
                        </td>
                    </tr>
                    <tr style="display:flex; justify-content: space-between; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Cabinet URL
                            </div>
                        </td>
                        <td style="width: 70%;" colspan="2">
                            <div>
                                <input type="text" name="cabinet_url" id="cabinet_url" value="{{ $cabinet_url }}" style="width: 100%;" placeholder="https://true-help.com" />
                            </div>
                        </td>
                    </tr>
                    <tr style="display:flex; justify-content: space-between; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Shop domain
                            </div>
                            <div style="font-size: 12px; color: #666;">
                                The domain registered in the cabinet allowlist (?s=).
                                Defaults to the current host ({{ $cabinet_host }}).
                                Override it when testing from another host.
                            </div>
                        </td>
                        <td style="width: 70%;" colspan="2">
                            <div>
                                <input type="text" name="cabinet_domain" id="cabinet_domain" value="{{ $cabinet_domain }}" style="width: 100%;" placeholder="{{ $cabinet_host }}" />
                            </div>
                        </td>
                    </tr>
                    <tr style="display:flex; justify-content: space-between; gap: 15px;">
                        <td style="width: 30%;">
                            <div>
                                Secret key
                            </div>
                            <div style="font-size: 12px; color: #666;">
                                Issued by the manager. Never share it publicly.
                            </div>
                        </td>
                        <td style="width: 70%;" colspan="2">
                            <div>
                                <input type="password" name="cabinet_secret" id="cabinet_secret" value="{{ $cabinet_secret }}" style="width: 100%;" placeholder="paste the secret key here" autocomplete="off" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button type="button" class=" jqTransformButton jqTransformButton_hover payment-details__button button button--filled" onclick="saveCabinetInfo();">
                                <span>
                                    <span>
                                        Save
                                    </span>
                                    <svg width="20" height="20">
                                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                                    </svg>
                                </span>
                            </button>
                            <button type="button" class=" jqTransformButton payment-details__button button" onclick="testCabinetConnection();" style="margin-left: 10px;">
                                <span>
                                    <span>
                                        Test connection
                                    </span>
                                </span>
                            </button>
                            <span id="cabinet_test_result" style="margin-left: 12px;"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>

<div class="statistic" style="margin-top: 2%;">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">How to connect (instruction)</h3>
        <ol style="margin: 0 0 5px 20px; max-width: 760px; line-height: 1.6;">
            <li>
                Send the manager <b>two values</b> so the shop is registered in the cabinet allowlist
                (use the "Shop domain" field above if it differs from the current host):
                <div style="margin: 8px 0;">
                    <div>Shop domain: <input type="text" readonly value="{{ $cabinet_domain }}" onclick="this.select();" style="width: 280px;" /></div>
                    <div style="margin-top: 6px;">Callback URL: <input type="text" readonly value="{{ $cabinet_callback }}" onclick="this.select();" style="width: 420px; max-width: 100%;" /></div>
                </div>
            </li>
            <li>
                The manager registers the domain and returns a <b>secret key</b>.
            </li>
            <li>
                Paste the secret key above, press <b>Test connection</b> (it must report
                that the secret is valid), then switch the cabinet to <b>Enabled</b> and press <b>Save</b>.
            </li>
        </ol>
    </div>
</div>

@endsection
