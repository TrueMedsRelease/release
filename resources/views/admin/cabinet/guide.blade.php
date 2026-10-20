<div class="statistic adm-card">
    <div class="adm-card__head">
        <h3 class="payment-details__caption">How to connect (instruction)</h3>
    </div>
    <ol class="cab-admin__steps">
        <li>
            <div>
                Send the manager the <b>shop domain</b> so the shop is registered in the cabinet allowlist.
                The shop domain is always the current host — nothing to configure:
                <div class="adm-input-group cab-admin__copy">
                    <input class="input" type="text" readonly onclick="this.select();"
                           value="{{ $cabinet_domain }}" id="cab_copy_domain" aria-label="Shop domain" />
                    <button type="button" class="adm-mini-btn cab-admin__mini-btn" data-copy-target="cab_copy_domain">Copy</button>
                </div>
            </div>
        </li>
        <li>
            <div>The manager registers the domain and returns a <b>secret key</b>.</div>
        </li>
        <li>
            <div>
                Paste the secret key above, press <b>Test connection</b> (it must report
                that the secret is valid), then switch the cabinet to <b>Enabled</b> and press <b>Save</b>.
            </div>
        </li>
    </ol>
</div>
