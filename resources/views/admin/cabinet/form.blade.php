<div class="statistic adm-card">
    <div class="adm-card__head">
        <h3 class="payment-details__caption">Settings</h3>
    </div>
    <form name="cabinet_properties" id="cabinet_properties" method="POST" action="#"
          data-save-url="{{ route('admin.save_customer_personal_account_info') }}"
          data-test-url="{{ route('admin.test_customer_personal_account_connection') }}">
        <div class="adm-settings">
            <div class="adm-setting">
                <div class="adm-setting__label">Status</div>
                <div class="adm-choices cab-admin__status" role="radiogroup" aria-label="Cabinet status">
                    <label class="adm-choice cab-admin__status-on" for="cabinet_on">
                        <input type="radio" name="cabinet_enabled" id="cabinet_on" value="1" @if ($cabinet_enabled) checked @endif />
                        <span class="adm-choice__mark"></span>
                        <span class="adm-choice__name">Enabled</span>
                    </label>
                    <label class="adm-choice adm-choice--muted" for="cabinet_off">
                        <input type="radio" name="cabinet_enabled" id="cabinet_off" value="0" @if (!$cabinet_enabled) checked @endif />
                        <span class="adm-choice__mark"></span>
                        <span class="adm-choice__name">Disabled</span>
                    </label>
                </div>
            </div>

            <div class="adm-setting">
                <div class="adm-setting__label">Cabinet URL</div>
                <div class="adm-input-group">
                    <input class="input" type="text" id="cabinet_url" readonly onclick="this.select();"
                           value="{{ $cabinet_url }}" aria-label="Cabinet URL" />
                    <button type="button" class="adm-mini-btn cab-admin__mini-btn" data-copy-target="cabinet_url">Copy</button>
                </div>
            </div>

            <div class="adm-setting">
                <div class="adm-setting__label">
                    <label for="cabinet_secret">Secret key</label>
                    <span class="adm-setting__hint">Issued by the manager. Never share it publicly.</span>
                </div>
                <div class="adm-input-group">
                    <input class="input" type="password" name="cabinet_secret" id="cabinet_secret"
                           value="{{ $cabinet_secret }}" placeholder="Paste the secret key here" autocomplete="off" />
                    <button type="button" class="adm-mini-btn cab-admin__mini-btn" id="cab_secret_toggle" aria-label="Show secret key">Show</button>
                </div>
            </div>
        </div>

        <div class="adm-actions">
            <button type="submit" id="cab_save_btn" class="payment-details__button button button--filled">
                <span>
                    <span>Save</span>
                    <svg width="20" height="20">
                        <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                    </svg>
                </span>
            </button>
            <button type="button" id="cab_test_btn" class="payment-details__button button button--outline">
                <span>
                    <span>Test connection</span>
                </span>
            </button>
            <span class="cab-admin__result" id="cabinet_test_result" role="status" aria-live="polite"></span>
        </div>
    </form>
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
<div class="statistic adm-card">
    <div class="adm-card__head">
        <h3 class="payment-details__caption">Display settings</h3>
    </div>
    <div class="adm-settings">
        <div class="adm-setting">
            <div class="adm-setting__label">Cabinet pages</div>
            <div class="adm-choices">
                @foreach ($cbPages as $cbKey => $cbLabel)
                    <label class="adm-choice adm-choice--check" for="cab_page_{{ $cbKey }}">
                        <input type="checkbox" name="display_pages_{{ $cbKey }}" id="cab_page_{{ $cbKey }}" value="1"
                               @if (!empty($cabinet_display['pages'][$cbKey])) checked @endif />
                        <span class="adm-choice__mark"></span>
                        <span class="adm-choice__name">{{ $cbLabel }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="adm-setting">
            <div class="adm-setting__label">My orders blocks</div>
            <div class="adm-choices">
                <label class="adm-choice adm-choice--check" for="cab_orders_filters">
                    <input type="checkbox" name="display_orders_filters" id="cab_orders_filters" value="1"
                           @if (!empty($cabinet_display['orders_filters'])) checked @endif />
                    <span class="adm-choice__mark"></span>
                    <span class="adm-choice__name">Filter by status</span>
                </label>
                <label class="adm-choice adm-choice--check" for="cab_orders_help">
                    <input type="checkbox" name="display_orders_help" id="cab_orders_help" value="1"
                           @if (!empty($cabinet_display['orders_help'])) checked @endif />
                    <span class="adm-choice__mark"></span>
                    <span class="adm-choice__name">Need help with an order?</span>
                </label>
            </div>
        </div>

        <div class="adm-setting">
            <div class="adm-setting__label"><label for="cab_shop_button">Shop button (My orders)</label></div>
            <div class="adm-field-narrow">
                <select class="input" name="display_orders_shop_button" id="cab_shop_button">
                    @foreach ($cbShopButtons as $cbValue => $cbLabel)
                        <option value="{{ $cbValue }}" @if (($cabinet_display['orders_shop_button'] ?? 'all') === $cbValue) selected @endif>{{ $cbLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="adm-setting">
            <div class="adm-setting__label"><label for="cab_shop_override">Shop domain override</label></div>
            <div>
                <input class="input" type="text" name="display_shop_domain_override" id="cab_shop_override"
                       value="{{ $cabinet_display['shop_domain_override'] ?? '' }}" placeholder="https://example.com" autocomplete="off" />
            </div>
        </div>

        <div class="adm-setting">
            <div class="adm-setting__label"><label for="cab_billing_override">Billing URL override</label></div>
            <div>
                <input class="input" type="text" name="display_billing_url_override" id="cab_billing_override"
                       value="{{ $cabinet_display['billing_url_override'] ?? '' }}" placeholder="https://billing.example.com" autocomplete="off" />
            </div>
        </div>

        <div class="adm-setting">
            <div class="adm-setting__label"><label for="cab_checkout_override">Repeat order link override</label></div>
            <div>
                <input class="input" type="text" name="display_checkout_base_override" id="cab_checkout_override"
                       value="{{ $cabinet_display['checkout_base_override'] ?? '' }}" placeholder="https://example.com?aff=1842" autocomplete="off" />
            </div>
        </div>
    </div>

    <div class="adm-actions">
        <button type="button" id="cab_display_save_btn" class="payment-details__button button button--filled">
            <span>
                <span>Save display settings</span>
                <svg width="20" height="20">
                    <use xlink:href="{{ asset("admin_style/images/icons/icons.svg#svg-checkmark") }}"></use>
                </svg>
            </span>
        </button>
    </div>
</div>
