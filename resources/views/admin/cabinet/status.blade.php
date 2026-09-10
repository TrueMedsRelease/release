<div class="statistic">
    <div class="statistic__rows">
        <h3 class="payment-details__caption">Status</h3>
        <div class="cab-admin__status">
            <div class="cab-admin__tile">
                <div class="cab-admin__tile-caption">Cabinet</div>
                @if ($cabinet_enabled)
                    <span class="cab-admin__badge cab-admin__badge--on">Enabled</span>
                @else
                    <span class="cab-admin__badge cab-admin__badge--off">Disabled</span>
                @endif
            </div>
            <div class="cab-admin__tile">
                <div class="cab-admin__tile-caption">Secret key</div>
                @if ($cabinet_secret === '')
                    <span class="cab-admin__badge cab-admin__badge--warn">Missing</span>
                    <div class="cab-admin__tile-note">The cabinet will not work until you paste the key below.</div>
                @else
                    <span class="cab-admin__badge cab-admin__badge--on">Set</span>
                @endif
            </div>
            <div class="cab-admin__tile">
                <div class="cab-admin__tile-caption">Embedded login</div>
                @if ($cabinet_embedded)
                    <span class="cab-admin__badge cab-admin__badge--on">Allowed</span>
                    <div class="cab-admin__tile-note">Customers sign in right here in the shop.</div>
                @else
                    <span class="cab-admin__badge cab-admin__badge--off">Not allowed</span>
                    <div class="cab-admin__tile-note">Customers go to the cabinet pages. Refreshed by “Test connection”.</div>
                @endif
            </div>
        </div>
    </div>
</div>
