@extends('account.layout')

{{--
    Bonus 1:1 СЃ customerportal assets/Pages/Bonus.vue.
    РљР»Р°СЃСЃС‹ Рё СЃС‚СЂСѓРєС‚СѓСЂР° вЂ” С‚Рµ Р¶Рµ (.page__bonus.referrer, .referrer__container,
    .bonus_card__body, .table-referrer, .pagination, tippy copy).
--}}

@section('cab_title', __('text.cabinet_nav_bonus'))

@section('cab_content')
<section class="page__bonus referrer" x-data="cabinetBonus()" x-cloak>
    <div class="referrer__container">
        <div class="referrer__top-row top-row">
            <h2 class="top-row__title title">{{ __('text.cabinet_nav_bonus') }}</h2>
        </div>

        <div class="load-error" role="alert" x-show="failed">
            <svg class="load-error__icon" width="36" height="36" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-attention" /></svg>
            <p class="load-error__title">{{ __('text.cabinet_nav_bonus') }}</p>
            <p class="load-error__text" x-text="failed"></p>
            <button type="button" class="load-error__button" @click="reload()">{{ __('text.cabinet_reload') }}</button>
        </div>

        <template x-if="!failed">
            <div>
                {{-- РЎРєРµР»РµС‚РѕРЅ РєР°СЂС‚С‹ (BonusSkeleton 1:1) --}}
                <div class="bonus_card__body" style="margin-bottom: 20px;" aria-hidden="true" x-show="loading">
                    <div class="bonus_card__line top">
                        <div class="referrer__share-link bonus-card-row__main">
                            <p class="referrer__text-share">{{ __('text.cabinet_card_number') }}</p>
                            <div class="referrer__action action-referrer">
                                <span class="skeleton skeleton-bonus__value" style="width: 160px; height: 16px; border-radius: 6px;"></span>
                                <span class="skeleton" style="width: 24px; height: 24px; border-radius: 6px;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="bonus_card__line">
                        <div class="bonus_card__share-link">
                            <p class="referrer__text-share">{{ __('text.cabinet_card_status') }}</p>
                            <div class="referrer__action action-referrer">
                                <span class="skeleton" style="width: 85px; height: 16px; border-radius: 6px;"></span>
                            </div>
                        </div>
                        <div class="bonus_card__share-link">
                            <p class="referrer__text-share">{{ __('text.cabinet_balance') }}</p>
                            <div class="referrer__action action-referrer">
                                <span class="skeleton" style="width: 65px; height: 16px; border-radius: 6px;"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <template x-if="!loading">
                    <div>
                        <div class="bonus_card__body" style="margin-bottom: 20px;">
                            <div class="bonus_card__line top">
                                <div class="referrer__share-link bonus-card-row__main">
                                    <p class="referrer__text-share">{{ __('text.cabinet_card_number') }}</p>
                                    <div class="referrer__action action-referrer">
                                        <div class="action-referrer__data" data-tippy-data x-text="cardNumber || 'вЂ”'"></div>
                                        <div class="action-referrer__icon tippy" data-tippy>
                                            <button type="button" class="action-referrer__button" x-show="cardNumber" aria-label="{{ __('text.cabinet_bonus_copy_card') }}" @click="copyCard()">
                                                <svg width="24" height="24" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-copy" /></svg>
                                            </button>
                                            <div class="tippy__content" x-bind:class="{ active: copied === 'card' }" data-tippy-content>{{ __('text.cabinet_copied') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <details class="bonus-expire" x-show="(expiring || []).length">
                                    <summary class="bonus-expire__toggle">
                                        <span class="bonus-expire__toggle-left">
                                            <span class="bonus-expire__icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                                                    <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd" d="M13.44 1.5c.16 2.11 1.03 3.41 2.33 5.18 1.83 2.5 4.03 5.48 4.03 9.34 0 4.45-3.42 7.98-7.86 7.98-4.78 0-8.06-3.47-8.06-7.98 0-3.37 1.48-5.77 2.95-7.77 1.16-1.57 2.28-3.08 2.28-4.79 0-.49.37-.9.85-.94.63-.05.98.37 1.07.92.33 2.16 1.06 3.54 2.33 4.82-.05-2.14.48-4.33 1.78-6.47.26-.43.72-.62 1.18-.5.46.13.77.53.8 1.01Zm-1.4 19.56c-1.57 0-2.73-1.42-2.73-3.32 0-1.18.68-2.12 1.44-3.03.64-.77 1.33-1.58 1.78-2.58.31.55.73 1.02 1.13 1.47 1.01 1.12 1.98 2.18 1.98 3.8 0 2.11-1.59 3.66-3.6 3.66Z" />
                                                </svg>
                                            </span>
                                            <span class="bonus-expire__text">
                                                {{ __('text.cabinet_bonus_expire') }} <b x-text="'$' + (expiring[0] ? expiring[0].remainingAmount : 0)"></b>
                                            </span>
                                        </span>
                                        <span class="bonus-expire__arrow">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    </summary>
                                    <div class="bonus-expire__dropdown">
                                        <template x-for="(item, index) in (expiring || [])" :key="index">
                                            <div class="bonus-expire__item">
                                                <span class="bonus-expire__date"><b x-text="item.expiresAt"></b></span>
                                                <span class="bonus-expire__amount" x-text="expireAmount(item.remainingAmount)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </details>
                            </div>

                            <div class="bonus_card__line">
                                <div class="bonus_card__share-link">
                                    <p class="referrer__text-share">{{ __('text.cabinet_card_status') }}</p>
                                    <div class="referrer__action action-referrer">
                                        <div class="action-referrer__data" data-tippy-data x-text="cardStatusLabel"></div>
                                    </div>
                                </div>
                                <div class="bonus_card__share-link">
                                    <p class="referrer__text-share">{{ __('text.cabinet_balance') }}</p>
                                    <div class="referrer__action action-referrer">
                                        <div class="action-referrer__data" data-tippy-data x-text="'$' + balance"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="bonus_card__body" style="margin-bottom: 20px; line-height: 1.5;">
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
                    <div class="bonus_card__body">
                        <table class="referrer__table table-referrer" x-show="(history || []).length">
                            <thead>
                                <tr class="table-referrer__row table-referrer__row--top">
                                    <th class="table-referrer__date" scope="col">{{ __('text.cabinet_track_date') }}</th>
                                    <th class="table-referrer__type" scope="col">{{ __('text.cabinet_bonus_type') }}</th>
                                    <th class="table-referrer__bonus" scope="col">{{ __('text.cabinet_bonus') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in (history || [])" :key="index">
                                    <tr class="table-referrer__row">
                                        <td class="table-referrer__date" x-text="item.date"></td>
                                        <td class="table-referrer__type" x-text="actionLabel(item)"></td>
                                        <td class="table-referrer__bonus" x-bind:style="{ color: deltaColor(item.delta) }" x-text="deltaText(item.delta)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="pagination" x-show="totalPages > 1">
                            <button type="button" class="pagination__item" x-bind:disabled="page <= 1" @click="goPage(page - 1)">
                                <svg width="18" height="20" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-arr-left" /></svg>
                            </button>
                            <template x-for="p in pages" :key="p">
                                <button type="button" class="pagination__item" x-bind:class="{ 'is-active': p === page }" @click="goPage(p)" x-text="p"></button>
                            </template>
                            <button type="button" class="pagination__item" x-bind:disabled="page >= totalPages" @click="goPage(page + 1)">
                                <svg width="18" height="18" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-arr-right" /></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</section>
@endsection

@push('cab_scripts')
@php
$cabinetConfig = [
    'bonusUrl' => route('account.api.bonus'),
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
window.CabinetConfig = @json($cabinetConfig);
</script>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/account.js') }}?v={{ filemtime(public_path('js/cabinet/account.js')) }}"></script>
@endpush

