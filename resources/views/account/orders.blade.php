@extends('account.layout')

{{--
    My orders 1:1 СЃ customerportal assets/Pages/Orders.vue
    + Components/OrdersTable.vue + orders/OrderCard.vue
    + orders/OrderCardItems.vue + orders/OrderShippingPopover.vue
    + orders/OrderTrackingDrawer.vue + orders/OrderTrackBlock.vue
    + orders/OrdersSidebar.vue + ConfirmDialog.vue + LoadError.vue.
    РљР»Р°СЃСЃС‹ Рё СЃС‚СЂСѓРєС‚СѓСЂР° вЂ” С‚Рµ Р¶Рµ. Р”Р°РЅРЅС‹Рµ вЂ” С‡РµСЂРµР· /account/api/* (Alpine cabinetOrders).
--}}

@section('cab_title', __('text.cabinet_nav_orders'))

@section('cab_content')
<section class="page__orders orders" x-data="cabinetOrders()" x-cloak>
    <div class="orders__container">
        {{-- LoadError 1:1 (РІРѕ РІСЃСЋ СЃС‚СЂР°РЅРёС†Сѓ, РІРјРµСЃС‚Рѕ РєРѕРЅС‚РµРЅС‚Р°) --}}
        <div class="load-error" role="alert" x-show="failed">
            <svg class="load-error__icon" width="36" height="36" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-attention" /></svg>
            <p class="load-error__title">{{ __('text.cabinet_my_orders') }}</p>
            <p class="load-error__text" x-text="failed"></p>
            <button type="button" class="load-error__button" @click="reload()">{{ __('text.cabinet_reload') }}</button>
        </div>

        <template x-if="!failed">
            <div>
                <h1 class="orders__title">{{ __('text.cabinet_nav_orders') }}</h1>
                <div class="orders__layout">
                    <div class="orders__list">
                        <div class="oc-list" aria-label="{{ __('text.cabinet_nav_orders') }}">
                            {{-- РЎРєРµР»РµС‚РѕРЅС‹ РїСЂРё РїРµСЂРІРѕР№ Р·Р°РіСЂСѓР·РєРµ (OrdersTable 1:1) --}}
                            <div class="oc-skel-list" aria-hidden="true" x-show="loading">
                                @for ($i = 0; $i < 3; $i++)
                                    <div class="oc-skel-card">
                                        <div class="oc-skel-card__head">
                                            <div class="oc-skel-card__id">
                                                <span class="oc-skel oc-skel--date" style="width: 84px"></span>
                                                <span class="oc-skel oc-skel--number" style="width: 132px"></span>
                                            </div>
                                            <span class="oc-skel oc-skel--pill" style="width: 72px"></span>
                                        </div>
                                        <div class="oc-skel-card__items">
                                            <div class="oc-skel-row"><span class="oc-skel" style="width: 55%"></span><span class="oc-skel" style="width: 64px"></span></div>
                                            <div class="oc-skel-row"><span class="oc-skel" style="width: 55%"></span><span class="oc-skel" style="width: 64px"></span></div>
                                        </div>
                                        <div class="oc-skel-card__foot">
                                            <span class="oc-skel" style="width: 128px"></span>
                                            <span class="oc-skel oc-skel--total" style="width: 76px"></span>
                                        </div>
                                        <div class="oc-skel-card__actions">
                                            <span class="oc-skel oc-skel--btn" style="width: 128px"></span>
                                            <span class="oc-skel oc-skel--btn" style="width: 104px"></span>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            {{-- РџСѓСЃС‚Рѕ + СЃР±СЂРѕСЃ С„РёР»СЊС‚СЂР° (OrdersTable 1:1) --}}
                            <div class="oc-list__empty" x-show="!loading && !items.length">
                                <p class="oc-list__empty-text">{{ __('text.cabinet_empty') }}</p>
                                <button type="button" class="oc-list__reset" x-show="filtered" @click="selectFilter('all')">{{ __('text.cabinet_reset_filter') }}</button>
                            </div>

                            {{-- РљР°СЂС‚РѕС‡РєРё (OrderCard 1:1) --}}
                            <template x-for="order in items" :key="order.id">
                                <article class="oc-card">
                                    <div class="oc-card__head">
                                        <div class="oc-card__id">
                                            <p class="oc-card__date" x-text="order.date"></p>
                                            <button type="button" class="oc-card__number oc-copy" x-bind:class="{ 'is-copied': orderCopiedId === order.id }" aria-label="{{ __('text.cabinet_copy') }}" title="{{ __('text.cabinet_copy') }}" @click="copyOrderNumber(order.id)">
                                                <span x-text="orderNumber(order.id)"></span>
                                                <svg x-show="orderCopiedId === order.id" width="13" height="13" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <path d="M2.5 6.5L5 9l4.5-5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <svg x-show="orderCopiedId !== order.id" width="13" height="13" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3" />
                                                    <path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3" />
                                                </svg>
                                            </button>
                                        </div>
                                        <span class="oc-pill" x-show="order.statusKey && order.statusKey !== 'unknown'" x-bind:class="pillClass(order.statusKey)">
                                            <span class="oc-pill__dot" aria-hidden="true"></span>
                                            <span x-text="statusLabel(order)"></span>
                                        </span>
                                    </div>

                                    {{-- РџРѕР·РёС†РёРё (OrderCardItems 1:1, РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ СЂР°СЃРєСЂС‹С‚С‹) --}}
                                    <div class="oc-items" x-show="(order.items || []).length">
                                        <button type="button" class="oc-items__toggle" @click="toggleItems(order.id)" x-bind:aria-expanded="String(isItemsOpen(order.id))">
                                            <span class="oc-items__label">
                                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <path d="M2.2 4h7.6l-.65 6.2a.5.5 0 0 1-.5.45H3.35a.5.5 0 0 1-.5-.45L2.2 4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
                                                    <path d="M4.2 4V3a1.8 1.8 0 0 1 3.6 0v1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                                </svg>
                                                <span class="oc-items__label-text" x-text="itemsLabel(order)"></span>
                                            </span>
                                            <span class="oc-items__meta">
                                                <span class="oc-mono oc-items__total" x-text="formatMoney(itemsTotal(order.items))"></span>
                                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" class="oc-items__chevron" x-bind:class="{ 'oc-items__chevron--open': isItemsOpen(order.id) }">
                                                    <path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div class="oc-items__collapse" x-bind:class="{ 'oc-items__collapse--open': isItemsOpen(order.id) }">
                                            <div class="oc-items__overflow">
                                                <div class="oc-items__rows">
                                                    <template x-for="(item, idx) in (order.items || [])" :key="idx">
                                                        <div class="oc-items__row">
                                                            <p class="oc-items__name" x-text="itemName(item)"></p>
                                                            <span class="oc-mono oc-items__qty" x-text="'×' + (item.quantity || 0)"></span>
                                                            <span class="oc-mono oc-items__sum" x-text="formatMoney(Number(item.price || 0) * Number(item.quantity || 0))"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="oc-card__nodetail" x-show="!(order.items || []).length">{{ __('text.cabinet_no_details') }}</p>

                                    <p class="oc-card__status" x-show="nextDateLine(order)" x-text="nextDateLine(order)"></p>

                                    <div class="oc-card__foot">
                                        <div class="oc-card__links" x-show="Number(order.bonus || 0) > 0">
                                            <span class="oc-bonus" x-text="'+' + formatMoney(order.bonus) + ' {{ __('text.cabinet_bonus') }}'"></span>
                                        </div>
                                        <div class="oc-card__total">
                                            <span class="oc-card__total-label">{{ __('text.cabinet_total') }}</span>
                                            <span class="oc-mono oc-card__total-value" x-text="formatMoney(order.total)"></span>
                                        </div>
                                    </div>

                                    <div class="oc-card__actions">
                                        <div class="oc-card__secondary">
                                            {{-- Shipping (OrderShippingPopover 1:1: footlink + popover + bottom-sheet) --}}
                                            <div class="oc-ship">
                                                <button type="button" class="oc-footlink" x-bind:aria-expanded="String(isShipOpen(order.id))" @click="toggleShip(order.id)">
                                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                        <path d="M7 1C4.79 1 3 2.79 3 5c0 3 4 8 4 8s4-5 4-8c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" />
                                                        <circle cx="7" cy="5" r="1.4" stroke="currentColor" stroke-width="1.2" />
                                                    </svg>
                                                    <span>{{ __('text.cabinet_shipping') }}</span>
                                                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true" class="oc-ship__caret" x-bind:class="{ 'oc-ship__caret--open': isShipOpen(order.id) }">
                                                        <path d="M2 3.5l3 3 3-3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                                <div class="oc-ship__popover" x-show="isShipOpen(order.id)" @click.outside="closeShip(order.id)">
                                                    <template x-if="shipLoading[order.id]">
                                                        <div class="oc-ship__skel" aria-hidden="true">
                                                            <span class="oc-skel oc-skel--caption"></span>
                                                            <span class="oc-skel oc-skel--name"></span>
                                                            <span class="oc-skel oc-skel--addr"></span>
                                                            <span class="oc-skel oc-skel--addr-short"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && shipFailed[order.id]">
                                                        <div class="oc-ship__state">
                                                            <p class="oc-ship__note">{{ __('text.cabinet_shipping_failed') }}</p>
                                                            <button type="button" class="oc-ship__btn" @click="loadShip(order.id)">{{ __('text.cabinet_retry') }}</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipLoaded[order.id] && !shipData[order.id]">
                                                        <p class="oc-ship__note">{{ __('text.cabinet_shipping_not_found') }}</p>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipData[order.id]">
                                                        <div>
                                                            <p class="oc-ship__caption">{{ __('text.cabinet_shipping_to') }}</p>
                                                            <p class="oc-ship__name" x-text="shipFullName(order.id)"></p>
                                                            <p class="oc-ship__addr">
                                                                <span x-text="shipAddress(order.id)"></span><br>
                                                                <span x-text="shipCityLine(order.id)"></span><br>
                                                                <span x-text="shipCountryLine(order.id)"></span>
                                                            </p>
                                                            <div class="oc-ship__contact">
                                                                <p class="oc-ship__note">{{ __('text.cabinet_shipping_note') }}</p>
                                                                <a href="{{ route('account.contact') }}" class="oc-ship__btn">
                                                                    <span>{{ __('text.cabinet_nav_contact') }}</span>
                                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                        <path d="M2.5 6h7M6.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="oc-ship__backdrop" x-show="isShipOpen(order.id)" @click="closeShip(order.id)"></div>
                                                <div class="oc-ship__sheet" role="dialog" aria-label="{{ __('text.cabinet_shipping') }}" x-show="isShipOpen(order.id)">
                                                    <div class="oc-ship__handle" aria-hidden="true"></div>
                                                    <template x-if="shipLoading[order.id]">
                                                        <div class="oc-ship__skel" aria-hidden="true">
                                                            <span class="oc-skel oc-skel--caption"></span>
                                                            <span class="oc-skel oc-skel--name"></span>
                                                            <span class="oc-skel oc-skel--addr"></span>
                                                            <span class="oc-skel oc-skel--addr-short"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && shipFailed[order.id]">
                                                        <div class="oc-ship__state">
                                                            <p class="oc-ship__note">{{ __('text.cabinet_shipping_failed') }}</p>
                                                            <button type="button" class="oc-ship__btn" @click="loadShip(order.id)">{{ __('text.cabinet_retry') }}</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipLoaded[order.id] && !shipData[order.id]">
                                                        <p class="oc-ship__note">{{ __('text.cabinet_shipping_not_found') }}</p>
                                                    </template>
                                                    <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipData[order.id]">
                                                        <div>
                                                            <p class="oc-ship__caption">{{ __('text.cabinet_shipping_to') }}</p>
                                                            <p class="oc-ship__name" x-text="shipFullName(order.id)"></p>
                                                            <p class="oc-ship__addr">
                                                                <span x-text="shipAddress(order.id)"></span><br>
                                                                <span x-text="shipCityLine(order.id)"></span><br>
                                                                <span x-text="shipCountryLine(order.id)"></span>
                                                            </p>
                                                            <div class="oc-ship__contact">
                                                                <p class="oc-ship__note">{{ __('text.cabinet_shipping_note') }}</p>
                                                                <a href="{{ route('account.contact') }}" class="oc-ship__btn">
                                                                    <span>{{ __('text.cabinet_nav_contact') }}</span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Tracking (OrderTrackingDrawer 1:1: footlink + drawer) --}}
                                            <button type="button" class="oc-footlink" x-show="order.trackingNumber === undefined || order.trackingNumber" @click="openTrack(order.id)">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                    <path d="M1.5 3.5h7v6h-7z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" />
                                                    <path d="M8.5 6H11l1.5 1.8v1.7H8.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                                    <circle cx="4.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2" />
                                                    <circle cx="10.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2" />
                                                </svg>
                                                <span>{{ __('text.cabinet_tracking') }}</span>
                                            </button>
                                        </div>
                                        <div class="oc-actions">
                                            <button type="button" class="oc-action oc-action--dark" x-bind:disabled="repeatingId === order.id" @click="repeatVirtual(order)">
                                                <span class="oc-spinner" aria-hidden="true" x-show="repeatingId === order.id"></span>
                                                <svg x-show="repeatingId !== order.id" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <path d="M10.5 6A4.5 4.5 0 1 1 6 1.5c1.6 0 3 .8 3.9 2M9.9 1v2.5H7.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span>{{ __('text.cabinet_repeat_order') }}</span>
                                            </button>
                                            <button type="button" class="oc-action oc-action--light" x-show="showStop(order)" x-bind:disabled="stoppingId === order.id" @click="askStop(order.id)">
                                                <span class="oc-spinner" aria-hidden="true" x-show="stoppingId === order.id"></span>
                                                <svg x-show="stoppingId !== order.id" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                    <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3" />
                                                    <path d="M4 4l4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                                </svg>
                                                <span>{{ __('text.cabinet_stop_auto') }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Tracking drawer (РїРѕСЂС‚Р°Р»СЊРЅС‹Р№ OrderTrackingDrawer 1:1) --}}
                                    <template x-if="trackRendered[order.id]">
                                        <div>
                                            <div class="oc-track__backdrop" @click="closeTrack(order.id)"></div>
                                            <aside class="oc-track__panel" role="dialog" aria-modal="true" aria-label="{{ __('text.cabinet_tracking') }}" @keydown.escape.window="closeTrack(order.id)">
                                                <div class="oc-track__head">
                                                    <div>
                                                        <p class="oc-track__eyebrow">{{ __('text.cabinet_tracking_title') }}</p>
                                                        <button type="button" class="oc-mono oc-track__order oc-track__order--copy" x-bind:class="{ 'is-copied': trackOrderCopied === order.id }" aria-label="{{ __('text.cabinet_copy') }}" title="{{ __('text.cabinet_copy') }}" @click="copyOrderNumber(order.id, true)">
                                                            <span x-text="orderNumber(order.id)"></span>
                                                            <svg x-show="trackOrderCopied === order.id" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                <path d="M2.5 6.5L5 9l4.5-5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <svg x-show="trackOrderCopied !== order.id" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3" />
                                                                <path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <button type="button" class="oc-track__close" aria-label="Close" @click="closeTrack(order.id)">
                                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                            <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="oc-track__body" aria-live="polite">
                                                    <template x-if="trackLoading[order.id]">
                                                        <div>
                                                            <div class="oc-skel oc-skel__line oc-skel__w40"></div>
                                                            <div class="oc-skel oc-skel__box" aria-hidden="true"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!trackLoading[order.id] && trackFailed[order.id]">
                                                        <div class="oc-track__state">
                                                            <p class="oc-track__state-text">{{ __('text.cabinet_tracking_failed') }}</p>
                                                            <button type="button" class="oc-btn oc-btn--dark" @click="loadTrack(order.id)">{{ __('text.cabinet_retry') }}</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!trackLoading[order.id] && !trackFailed[order.id] && !(trackList(order.id) || []).length">
                                                        <div class="oc-track__state">
                                                            <p class="oc-track__state-text">{{ __('text.cabinet_track_none') }}</p>
                                                        </div>
                                                    </template>
                                                    <template x-if="!trackLoading[order.id] && !trackFailed[order.id] && (trackList(order.id) || []).length">
                                                        <div>
                                                            <dl class="oc-track__summary">
                                                                <div><dt>{{ __('text.cabinet_track_date') }}</dt><dd class="oc-mono" x-text="order.date"></dd></div>
                                                                <div><dt>{{ __('text.cabinet_track_sum') }}</dt><dd class="oc-mono oc-track__sum" x-text="formatMoney(order.total)"></dd></div>
                                                                <div><dt>{{ __('text.cabinet_track_status') }}</dt><dd class="oc-track__status" x-text="statusLabel(order)"></dd></div>
                                                                <div><dt>{{ __('text.cabinet_track_destination') }}</dt><dd x-text="trackDestination(order.id)"></dd></div>
                                                                <div><dt>{{ __('text.cabinet_track_delivery_time') }}</dt><dd x-text="trackDays(order.id)"></dd></div>
                                                                <div><dt>{{ __('text.cabinet_track_latest') }}</dt><dd class="oc-mono" x-text="trackLatest(order.id)"></dd></div>
                                                            </dl>
                                                            <template x-for="track in (trackList(order.id) || [])" :key="track.number">
                                                                <div class="oc-track__block">
                                                                    <p class="oc-track__caption">Track</p>
                                                                    <div class="oc-track__code-row">
                                                                        <code class="oc-mono oc-track__code" x-text="track.number"></code>
                                                                        <button type="button" class="oc-track__copy" aria-label="{{ __('text.cabinet_copy') }}" title="{{ __('text.cabinet_copy') }}" @click="copyTrackNumber(track.number)">
                                                                            <svg x-show="trackCopied !== track.number" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                                <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3" />
                                                                                <path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3" />
                                                                            </svg>
                                                                            <svg x-show="trackCopied === track.number" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                                <path d="M2.5 6.5L5 9l4.5-5.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                                            </svg>
                                                                            <span x-text="trackCopied === track.number ? '{{ __('text.cabinet_copied') }}' : '{{ __('text.cabinet_copy') }}'"></span>
                                                                        </button>
                                                                    </div>
                                                                    <p class="oc-track__route" x-show="track.fromName || track.toName || track.fromCountry || track.toCountry">
                                                                        <span x-text="(track.fromName || track.fromCountry || 'вЂ”')"></span>
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                            <path d="M1.5 6h9M7.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                                        </svg>
                                                                        <span x-text="(track.toName || track.toCountry || 'вЂ”')"></span>
                                                                    </p>
                                                                    <div class="oc-track__latest" x-show="track.latestStatus">
                                                                        <p class="oc-track__latest-caption">Latest update</p>
                                                                        <p class="oc-track__latest-text" x-text="((track.latestEventLocation ? track.latestEventLocation + ', ' : '') + (track.latestEventDescription || ''))"></p>
                                                                        <p class="oc-mono oc-track__latest-time" x-text="track.latestTime"></p>
                                                                    </div>
                                                                    <div class="oc-track__providers">
                                                                        <template x-for="(provider, pIdx) in (track.providers || [])" :key="pIdx">
                                                                            <div class="oc-timeline">
                                                                                <div class="oc-timeline__head">
                                                                                    <p class="oc-timeline__title"><span x-text="provider.name"></span> <span class="oc-timeline__country">В· <span x-text="provider.country"></span></span></p>
                                                                                    <span class="oc-mono oc-timeline__count" x-text="((provider.events || []).length + ' events')"></span>
                                                                                </div>
                                                                                <div class="oc-timeline__body">
                                                                                    <p class="oc-mono oc-timeline__sync" x-text="'Sync Time: ' + (provider.latestSyncTime || '')"></p>
                                                                                    <ol class="oc-timeline__list">
                                                                                        <template x-for="(ev, eIdx) in visibleEvents(order.id, track, pIdx)" :key="eIdx">
                                                                                            <li class="oc-timeline__event">
                                                                                                <span class="oc-timeline__dot" x-bind:class="{ 'oc-timeline__dot--first': eIdx === 0 }"></span>
                                                                                                <p class="oc-mono oc-timeline__time" x-text="ev.time"></p>
                                                                                                <p class="oc-timeline__text" x-text="((ev.location ? ev.location + ', ' : '') + (ev.description || ''))"></p>
                                                                                            </li>
                                                                                        </template>
                                                                                    </ol>
                                                                                    <button type="button" class="oc-timeline__more" x-show="(provider.events || []).length > 5 && !isProviderExpanded(order.id, track, pIdx)" @click="toggleProvider(order.id, track, pIdx)">
                                                                                        <span x-text="showAllLabel(provider)"></span>
                                                                                    </button>
                                                                                    <button type="button" class="oc-timeline__more" x-show="isProviderExpanded(order.id, track, pIdx)" @click="toggleProvider(order.id, track, pIdx)">
                                                                                        <span>{{ __('text.cabinet_hide_events') }}</span>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </aside>
                                        </div>
                                    </template>
                                </article>
                            </template>

                            <div class="oc-list__state oc-list__state--more" x-show="loadingMore">
                                <span class="oc-list__spinner" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>

                    {{-- РЎР°Р№РґР±Р°СЂ (OrdersSidebar 1:1) --}}
                    <aside class="oc-side">
                        <div class="oc-side__card oc-side__card--filters">
                            <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
                            <div class="oc-side__filters" role="group" aria-label="{{ __('text.cabinet_filter_by_status') }}">
                                <template x-for="f in filters" :key="f.key">
                                    <button type="button" class="oc-side__filter" x-bind:class="{ 'oc-side__filter--active': activeFilter === f.key }" x-bind:disabled="listBusy" x-bind:aria-pressed="String(activeFilter === f.key)" @click="selectFilter(f.key)" x-text="f.label"></button>
                                </template>
                            </div>
                        </div>
                        <div class="oc-side__card">
                            <p class="oc-side__help-title">{{ __('text.cabinet_help_title') }}</p>
                            <p class="oc-side__help-text">{{ __('text.cabinet_help_text') }}</p>
                            <a href="{{ route('account.contact') }}" class="oc-side__help-btn">
                                <span>{{ __('text.cabinet_nav_contact') }}</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <path d="M2.5 6h7M6.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </aside>
                </div>

                {{-- РњРѕР±РёР»СЊРЅС‹Рµ С„РёР»СЊС‚СЂС‹: FAB + popover (OrdersSidebar 1:1) --}}
                <button type="button" class="oc-filters-fab" x-bind:class="{ '_open': filtersOpen }" x-bind:aria-expanded="String(filtersOpen)" aria-label="{{ __('text.cabinet_filter_by_status') }}" @click="filtersOpen = !filtersOpen">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v5.5l-4 2.5v-8L3 5z"/></svg>
                    <span x-text="activeFilterLabel"></span>
                </button>
                <div class="oc-filters-pop _open" role="dialog" aria-label="{{ __('text.cabinet_filter_by_status') }}" x-show="filtersOpen" @click.outside="filtersOpen = false" @keydown.escape.window="filtersOpen = false">
                    <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
                    <div class="oc-side__filters" role="group" aria-label="{{ __('text.cabinet_filter_by_status') }}">
                        <template x-for="f in filters" :key="f.key">
                            <button type="button" class="oc-side__filter" x-bind:class="{ 'oc-side__filter--active': activeFilter === f.key }" x-bind:disabled="listBusy" x-bind:aria-pressed="String(activeFilter === f.key)" @click="selectFilter(f.key)" x-text="f.label"></button>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <div class="orders__sentinel" aria-hidden="true" x-ref="sentinel"></div>
    </div>

    {{-- ConfirmDialog 1:1 (destructive, РѕСЃС‚Р°РЅРѕРІРєР° Р°РІС‚РѕРїРѕРІС‚РѕСЂР°) --}}
    <div class="confirm-overlay" x-show="confirmStopId !== null" @click.self="cancelStop()" @keydown.escape.window="cancelStop()">
        <div class="confirm-dialog" role="alertdialog" aria-modal="true">
            <div class="confirm-header">
                <h2 class="confirm-title">{{ __('text.cabinet_stop_auto') }}</h2>
                <p class="confirm-description">{{ __('text.cabinet_stop_confirm') }}</p>
            </div>
            <div class="confirm-footer">
                <button type="button" class="confirm-btn confirm-btn--cancel" @click="cancelStop()">{{ __('text.cabinet_cancel') }}</button>
                <button type="button" class="confirm-btn confirm-btn--confirm confirm-btn--destructive" @click="confirmStop()">{{ __('text.cabinet_stop') }}</button>
            </div>
        </div>
    </div>

    {{-- РўРѕСЃС‚ РѕС€РёР±РѕРє (repeat Рё РґСЂ.) --}}
    <div class="oc-toast" role="alert" x-show="toast" x-text="toast"></div>
</section>
@endsection

@push('cab_scripts')
@php
$cabinetConfig = [
    'email' => $email,
    'startUrl' => route('account.index'),
    'ordersUrl' => route('account.api.orders'),
    'meUrl' => route('account.api.me'),
    'shipUrl' => url('/account/api/orders'),
    'stopUrl' => url('/account/api/orders'),
    'repeatUrl' => url('/account/api/orders'),
    'tOrderNumber' => __('text.cabinet_order_number'),
    'tItems' => __('text.cabinet_items'),
    'tNextDate' => __('text.cabinet_next_date'),
    'tCopied' => __('text.cabinet_copied'),
    'tFilters' => [
        'all' => __('text.cabinet_filter_all'),
        'checking' => __('text.cabinet_filter_checking'),
        'approved' => __('text.cabinet_filter_approved'),
        'delivered' => __('text.cabinet_filter_delivered'),
        'declined' => __('text.cabinet_filter_declined'),
        'refunded' => __('text.cabinet_filter_refunded'),
    ],
    'tStatus' => [
        'checking' => __('text.cabinet_status_checking'),
        'approved' => __('text.cabinet_status_approved'),
        'delivered' => __('text.cabinet_status_delivered'),
        'declined' => __('text.cabinet_status_declined'),
        'refunded' => __('text.cabinet_status_refunded'),
    ],
    'tRepeatFailed' => __('text.cabinet_repeat_failed'),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
    'tShowAll' => __('text.cabinet_show_all_events'),
    'tDays' => __('text.cabinet_track_days'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabinetConfig);
</script>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/account.js') }}?v={{ filemtime(public_path('js/cabinet/account.js')) }}"></script>
@endpush

