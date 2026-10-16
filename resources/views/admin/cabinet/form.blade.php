<div class="statistic">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Settings</h3>
        <form name="cabinet_properties" id="cabinet_properties" method="POST" action="#"
              data-save-url="{{ route('admin.save_customer_personal_account_info') }}"
              data-test-url="{{ route('admin.test_customer_personal_account_connection') }}">
            <div class="cab-admin__field">
                <div>
                    <div class="cab-admin__label">Status</div>
                </div>
                <div class="cab-admin__radio-group" role="radiogroup" aria-label="Cabinet status">
                    <label class="cab-admin__radio" for="cabinet_on">
                        <input type="radio" name="cabinet_enabled" id="cabinet_on" value="1" @if ($cabinet_enabled) checked @endif />
                        Enabled
                    </label>
                    <label class="cab-admin__radio" for="cabinet_off">
                        <input type="radio" name="cabinet_enabled" id="cabinet_off" value="0" @if (!$cabinet_enabled) checked @endif />
                        Disabled
                    </label>
                </div>
            </div>

            <div class="cab-admin__field">
                <div>
                    <div class="cab-admin__label">Cabinet URL</div>
                </div>
                <div>
                    <input class="cab-admin__input" type="text" id="cabinet_url" readonly onclick="this.select();"
                           value="{{ $cabinet_url }}" aria-label="Cabinet URL" />
                </div>
            </div>

            <div class="cab-admin__field">
                <div>
                    <div class="cab-admin__label"><label for="cabinet_secret">Secret key</label></div>
                    <div class="cab-admin__hint">Issued by the manager. Never share it publicly.</div>
                </div>
                <div class="cab-admin__secret-wrap">
                    <input class="cab-admin__input" type="password" name="cabinet_secret" id="cabinet_secret"
                           value="{{ $cabinet_secret }}" placeholder="paste the secret key here" autocomplete="off" />
                    <button type="button" class="cab-admin__mini-btn" id="cab_secret_toggle" aria-label="Show secret key">Show</button>
                </div>
            </div>

            <div class="cab-admin__actions">
                <button type="submit" id="cab_save_btn" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled">
                    <span>
                        <span>Save</span>
                        <svg width="20" height="20">
                            <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                        </svg>
                    </span>
                </button>
                <button type="button" id="cab_test_btn" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled">
                    <span>
                        <span>Test connection</span>
                    </span>
                </button>
                <span class="cab-admin__result" id="cabinet_test_result" role="status" aria-live="polite"></span>
            </div>
        </form>
    </div>
</div>

@php
$cbPages = [
    'orders' => 'My orders',
    'bonus' => 'Bonus',
    'referral' => 'Referral program',
    'contact' => 'Contact Us',
    'chat' => 'Chat',
];
$cbShopButtons = ['all' => 'Show always', 'foreign' => 'Foreign shops only', 'hide' => 'Hide'];
@endphp
<div class="statistic">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Display settings</h3>
        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label">Cabinet pages</div>
            </div>
            <div>
                @foreach ($cbPages as $cbKey => $cbLabel)
                    <label class="cab-admin__radio" for="cab_page_{{ $cbKey }}">
                        <input type="checkbox" name="display_pages_{{ $cbKey }}" id="cab_page_{{ $cbKey }}" value="1"
                               @if (!empty($cabinet_display['pages'][$cbKey])) checked @endif />
                        {{ $cbLabel }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label">My orders blocks</div>
            </div>
            <div>
                <label class="cab-admin__radio" for="cab_orders_filters">
                    <input type="checkbox" name="display_orders_filters" id="cab_orders_filters" value="1"
                           @if (!empty($cabinet_display['orders_filters'])) checked @endif />
                    Filter by status
                </label>
                <label class="cab-admin__radio" for="cab_orders_help">
                    <input type="checkbox" name="display_orders_help" id="cab_orders_help" value="1"
                           @if (!empty($cabinet_display['orders_help'])) checked @endif />
                    Need help with an order?
                </label>
            </div>
        </div>

        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label"><label for="cab_shop_button">Shop button (My orders)</label></div>
            </div>
            <div>
                <select class="cab-admin__input" name="display_orders_shop_button" id="cab_shop_button">
                    @foreach ($cbShopButtons as $cbValue => $cbLabel)
                        <option value="{{ $cbValue }}" @if (($cabinet_display['orders_shop_button'] ?? 'all') === $cbValue) selected @endif>{{ $cbLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label"><label for="cab_shop_override">Shop domain override</label></div>
            </div>
            <div>
                <input class="cab-admin__input" type="text" name="display_shop_domain_override" id="cab_shop_override"
                       value="{{ $cabinet_display['shop_domain_override'] ?? '' }}" placeholder="https://example.com" autocomplete="off" />
            </div>
        </div>

        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label"><label for="cab_billing_override">Billing URL override</label></div>
            </div>
            <div>
                <input class="cab-admin__input" type="text" name="display_billing_url_override" id="cab_billing_override"
                       value="{{ $cabinet_display['billing_url_override'] ?? '' }}" placeholder="https://billing.example.com" autocomplete="off" />
            </div>
        </div>

        <div class="cab-admin__field">
            <div>
                <div class="cab-admin__label"><label for="cab_checkout_override">Repeat order link override</label></div>
            </div>
            <div>
                <input class="cab-admin__input" type="text" name="display_checkout_base_override" id="cab_checkout_override"
                       value="{{ $cabinet_display['checkout_base_override'] ?? '' }}" placeholder="https://example.com?aff=1842" autocomplete="off" />
            </div>
        </div>

        <div class="cab-admin__actions">
            <button type="button" id="cab_display_save_btn" class="jqTransformButton jqTransformButton_hover payment-details__button button button--filled">
                <span>
                    <span>Save display settings</span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</div>
