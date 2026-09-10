<div class="statistic">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Settings</h3>
        <form name="cabinet_properties" id="cabinet_properties" method="POST" action="#"
              data-save-url="{{ route('admin.save_cabinet_info') }}">
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
                    <div class="cab-admin__label"><label for="cabinet_url">Cabinet URL</label></div>
                    <div class="cab-admin__hint">Address of the customer cabinet (true-help).</div>
                </div>
                <div>
                    <input class="cab-admin__input" type="text" name="cabinet_url" id="cabinet_url"
                           value="{{ $cabinet_url }}" placeholder="https://true-help.com"
                           inputmode="url" autocomplete="off" />
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
            </div>
        </form>
    </div>
</div>
