<div class="statistic">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">How to connect (instruction)</h3>
        <ol class="cab-admin__steps">
            <li>
                Send the manager the <b>shop domain</b> so the shop is registered in the cabinet allowlist.
                The shop domain is always the current host — nothing to configure:
                <div class="cab-admin__copy">
                    <span class="cab-admin__copy-label">Shop domain:</span>
                    <input class="cab-admin__input" type="text" readonly onclick="this.select();"
                           value="{{ $cabinet_domain }}" id="cab_copy_domain" aria-label="Shop domain" />
                    <button type="button" class="cab-admin__mini-btn" data-copy-target="cab_copy_domain">Copy</button>
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
