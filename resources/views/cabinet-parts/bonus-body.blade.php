{{--
    Shared: тело страницы "Bonus". Данные и порядок 1:1 с референсом
    (customerportal Bonus.vue). Стили — в per-design CSS.
    Переменные: $cabp_bonus_card, $cabp_bonus_history.
--}}
<div class="cabp-bank">
    <div class="cabp-bank__top">
        <span class="cabp-bank__label">{{ __('text.cabinet_nav_bonus') }}</span>
        <span class="cabp-bank__status">{{ $cabp_bonus_card['status'] }}</span>
    </div>
    <div class="cabp-bank__chip" aria-hidden="true"><span></span><span></span></div>
    <div class="cabp-bank__number-row">
        <div>
            <p class="cabp-bank__caption">{{ __('text.cabinet_card_number') }}</p>
            <p class="cabp-bank__number">{{ $cabp_bonus_card['number'] }}</p>
        </div>
        <span class="cabp-bank__copy" title="{{ __('text.cabinet_bonus_copy_card') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="8" y="8" width="12" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8" />
                <path d="M16 8V5.5A2.5 2.5 0 0 0 13.5 3h-8A2.5 2.5 0 0 0 3 5.5v8A2.5 2.5 0 0 0 5.5 16H8" stroke="currentColor" stroke-width="1.8" />
            </svg>
        </span>
    </div>
    <div class="cabp-bank__bottom">
        <div>
            <p class="cabp-bank__caption">{{ __('text.cabinet_card_status') }}</p>
            <p class="cabp-bank__value">{{ $cabp_bonus_card['status'] }}</p>
        </div>
        <div>
            <p class="cabp-bank__caption">{{ __('text.cabinet_balance') }}</p>
            <p class="cabp-bank__value">{{ $cabp_bonus_card['balance'] }}</p>
        </div>
    </div>
    <details class="cabp-bank__expire">
        <summary class="cabp-bank__expire-toggle">
            <span>{{ __('text.cabinet_bonus_expire') }} <b>{{ $cabp_bonus_card['expiring'][0]['amount'] }}</b></span>
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </summary>
        <div class="cabp-bank__expire-list">
            @foreach ($cabp_bonus_card['expiring'] as $exp)
                <div class="cabp-bank__expire-row"><span><b>{{ $exp['date'] }}</b></span><span>{{ $exp['amount'] }}</span></div>
            @endforeach
        </div>
    </details>
</div>

<div class="cabp-about">
    <p>{{ __('text.cabinet_bonus_about_1') }}</p>
    <p>{{ __('text.cabinet_bonus_about_2') }}</p>
    <ul>
        {{ __('text.cabinet_bonus_levels') }}
        <li>{{ __('text.cabinet_bonus_level_silver') }}</li>
        <li>{{ __('text.cabinet_bonus_level_gold') }}</li>
        <li>{{ __('text.cabinet_bonus_level_vip') }}</li>
    </ul>
    <p>{!! __('text.cabinet_bonus_rate_silver') !!}</p>
    <p>{!! __('text.cabinet_bonus_rate_gold') !!}</p>
    <p>{!! __('text.cabinet_bonus_rate_vip') !!}</p>
    <p>
        {{ __('text.cabinet_bonus_upgrade_1') }}<br>
        <span>{!! __('text.cabinet_bonus_upgrade_2') !!}</span><br>
        <span>{!! __('text.cabinet_bonus_upgrade_3') !!}</span>
    </p>
    <p><b>{{ __('text.cabinet_bonus_no_coupons') }}</b></p>
</div>

<div class="cabp-history">
    <table class="cabp-table">
        <thead>
            <tr>
                <th>{{ __('text.cabinet_track_date') }}</th>
                <th>{{ __('text.cabinet_bonus_type') }}</th>
                <th>{{ __('text.cabinet_bonus') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cabp_bonus_history as $h)
                <tr>
                    <td>{{ $h['date'] }}</td>
                    <td>{{ $h['type'] }}</td>
                    <td class="@if ($h['positive']) cabp-pos @else cabp-neg @endif">{{ $h['delta'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="cabp-pages" aria-hidden="true">
        <span class="cabp-page is-active">1</span>
        <span class="cabp-page">2</span>
        <span class="cabp-page">3</span>
    </div>
</div>
