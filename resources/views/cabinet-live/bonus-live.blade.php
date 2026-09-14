{{--
    Live: "Bonus" — Alpine (account.js cabinetBonus).
    Переменные: $design.
--}}
<link rel="stylesheet" href="{{ asset_ver('css/cabinet-v1.css') }}">
@php
$cabLiveBonusConfig = [
    'bonusUrl' => route('account.api.bonus'),
    'loginUrl' => route('cabinet.login', ['step' => 'email']),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
    'tExpireAmount' => __('text.cabinet_bonus_expire_amount'),
    'tActions' => [
        'accrue_order' => 'Accrue (Order: :id)',
        'expire' => 'Expire',
        'debit_order' => 'Debit (Order: :id)',
        'refund' => 'Refund',
        'admin_adjust' => 'Admin Adjust',
        'accrue_referral' => 'Accrue Referral',
        'admin_debit' => 'Admin Debit',
        'admin_credit' => 'Admin Credit',
    ],
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabLiveBonusConfig);
window.CabinetMeUrl = @json(route('account.api.me'));
</script>

<div x-data="cabinetBonus()" x-cloak>
    <div class="cabp-loaderror" role="alert" x-show="failed">
        <p class="cabp-loaderror__title">{{ __('text.cabinet_nav_bonus') }}</p>
        <p class="cabp-loaderror__text" x-text="failed"></p>
        <button type="button" class="button cabp-action cabp-action--dark" @click="reload()">{{ __('text.cabinet_reload') }}</button>
    </div>

    <template x-if="!failed">
        <div>
            <div class="cabp-skel-list" aria-hidden="true" x-show="loading">
                <div class="cabp-bank"><span class="cabp-skel" style="width:50%"></span><br><br><span class="cabp-skel" style="width:70%"></span></div>
            </div>

            <template x-if="!loading">
                <div class="cabp-bank">
                    <div class="cabp-bank__top">
                        <span class="cabp-bank__label">{{ __('text.cabinet_nav_bonus') }}</span>
                        <span class="cabp-bank__status" x-text="cardStatusLabel"></span>
                    </div>
                    <div class="cabp-bank__chip" aria-hidden="true"><span></span><span></span></div>
                    <div class="cabp-bank__number-row">
                        <div>
                            <p class="cabp-bank__caption">{{ __('text.cabinet_card_number') }}</p>
                            <p class="cabp-bank__number" x-text="cardNumber || '—'"></p>
                        </div>
                        <span class="cabp-bank__copy" role="button" tabindex="0" title="{{ __('text.cabinet_bonus_copy_card') }}" aria-label="{{ __('text.cabinet_bonus_copy_card') }}" x-show="cardNumber" @click="copyCard()" @keydown.enter.prevent="copyCard()" @keydown.space.prevent="copyCard()" style="cursor:pointer">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M16 8V5.5A2.5 2.5 0 0 0 13.5 3h-8A2.5 2.5 0 0 0 3 5.5v8A2.5 2.5 0 0 0 5.5 16H8" stroke="currentColor" stroke-width="1.8"/></svg>
                            <span class="cabp-bank__copied" x-show="copied === 'card'" x-cloak>{{ __('text.cabinet_copied') }}</span>
                        </span>
                    </div>
                    <div class="cabp-bank__bottom">
                        <div>
                            <p class="cabp-bank__caption">{{ __('text.cabinet_card_status') }}</p>
                            <p class="cabp-bank__value" x-text="cardStatusLabel"></p>
                        </div>
                        <div>
                            <p class="cabp-bank__caption">{{ __('text.cabinet_balance') }}</p>
                            <p class="cabp-bank__value" x-text="'$' + balance"></p>
                        </div>
                    </div>
                    <details class="cabp-bank__expire" x-show="(expiring || []).length">
                        <summary class="cabp-bank__expire-toggle">
                            <span>{{ __('text.cabinet_bonus_expire') }} <b x-text="'$' + (expiring[0] ? expiring[0].remainingAmount : 0)"></b></span>
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </summary>
                        <div class="cabp-bank__expire-list">
                            <template x-for="(item, index) in (expiring || [])" :key="index">
                                <div class="cabp-bank__expire-row">
                                    <span><b x-text="item.expiresAt"></b></span>
                                    <span x-text="expireAmount(item.remainingAmount)"></span>
                                </div>
                            </template>
                        </div>
                    </details>
                </div>
            </template>

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

            <template x-if="!loading && !failed">
                <div class="cabp-history" x-show="(history || []).length">
                    <table class="cabp-table" x-show="(history || []).length">
                        <thead>
                            <tr>
                                <th>{{ __('text.cabinet_track_date') }}</th>
                                <th>{{ __('text.cabinet_bonus_type') }}</th>
                                <th>{{ __('text.cabinet_bonus') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in (history || [])" :key="index">
                                <tr>
                                    <td x-text="item.date"></td>
                                    <td x-text="actionLabel(item)"></td>
                                    <td :style="{ color: deltaColor(item.delta) }" x-text="deltaText(item.delta)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="cabp-pages" x-show="totalPages > 1">
                        <button type="button" class="cabp-page" :disabled="page <= 1" @click="goPage(page - 1)">‹</button>
                        <template x-for="p in pages" :key="p">
                            <button type="button" class="cabp-page" :class="{ 'is-active': p === page }" @click="goPage(p)" x-text="p"></button>
                        </template>
                        <button type="button" class="cabp-page" :disabled="page >= totalPages" @click="goPage(page + 1)">›</button>
                    </div>
                </div>
            </template>
        </div>
    </template>
</div>
