{{--
    Live: "My orders" — Alpine (account.js cabinetOrders) на классах превью.
    Состояния: загрузка/ошибка/пусто/403-скоуп/тосты. Конфиг — ниже.
    V1 soft-кнопки действий: public/css/cabinet-v1.css (общий для всех дизайнов).
    Переменные: $design.
--}}
<link rel="stylesheet" href="{{ asset_ver('css/cabinet-v1.css') }}">
@php
$cabLiveOrdersConfig = [
    'email' => (string) session('cabinet.email', ''),
    'startUrl' => route('cabinet.login', ['step' => 'email']),
    'loginUrl' => route('cabinet.login', ['step' => 'email']),
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
window.CabinetConfig = @json($cabLiveOrdersConfig);
window.CabinetMeUrl = @json(route('account.api.me'));
</script>

<div class="cabp-layout cabp-layout--orders" x-data="cabinetOrders()" x-cloak>
    <div class="cabp-main">
        <div class="cabp-loaderror" role="alert" x-show="failed">
            <p class="cabp-loaderror__title">{{ __('text.cabinet_nav_orders') }}</p>
            <p class="cabp-loaderror__text" x-text="failed"></p>
            <button type="button" class="button cabp-action cabp-action--dark" @click="reload()">{{ __('text.cabinet_reload') }}</button>
        </div>

        <template x-if="!failed">
            <div class="cabp-live-list">
                <div class="cabp-skel-list" aria-hidden="true" x-show="loading">
                    <template x-for="i in 3" :key="i">
                        <div class="oc-card cabp-skel-card">
                            <div class="oc-card__head">
                                <div><span class="cabp-skel" style="width:84px"></span> <span class="cabp-skel" style="width:132px"></span></div>
                                <span class="cabp-skel" style="width:72px"></span>
                            </div>
                            <div style="padding:0 20px 16px"><span class="cabp-skel" style="width:60%"></span></div>
                        </div>
                    </template>
                </div>

                <div class="cabp-empty" x-show="!loading && !items.length">
                    <p>{{ __('text.cabinet_empty') }}</p>
                    <button type="button" class="button cabp-action cabp-action--light" x-show="filtered" @click="selectFilter('all')">{{ __('text.cabinet_reset_filter') }}</button>
                </div>

                <template x-for="order in items" :key="order.id">
                    <article class="oc-card">
                        <div class="oc-card__head">
                            <div class="oc-card__id">
                                <p class="oc-card__date" x-text="order.date"></p>
                                <button type="button" class="oc-card__number oc-copy" :class="{ 'is-copied': orderCopiedId === order.id }" title="{{ __('text.cabinet_copy') }}" @click="copyOrderNumber(order.id)">
                                    <span x-text="orderNumber(order.id)"></span>
                                    <svg x-show="orderCopiedId === order.id" width="13" height="13" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.5 6.5L5 9l4.5-5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <svg x-show="orderCopiedId !== order.id" width="13" height="13" viewBox="0 0 12 12" fill="none" aria-hidden="true"><rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3"/></svg>
                                </button>
                            </div>
                            <span class="oc-pill" x-show="order.statusKey && order.statusKey !== 'unknown'" :class="pillClass(order.statusKey)">
                                <span class="oc-pill__dot" aria-hidden="true"></span>
                                <span x-text="statusLabel(order)"></span>
                            </span>
                        </div>

                        <div class="oc-items" x-show="(order.items || []).length">
                            <button type="button" class="oc-items__toggle" @click="toggleItems(order.id)" :aria-expanded="String(isItemsOpen(order.id))">
                                <span class="oc-items__label">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2.2 4h7.6l-.65 6.2a.5.5 0 0 1-.5.45H3.35a.5.5 0 0 1-.5-.45L2.2 4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M4.2 4V3a1.8 1.8 0 0 1 3.6 0v1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    <span class="oc-items__label-text" x-text="itemsLabel(order)"></span>
                                </span>
                                <span class="oc-items__meta">
                                    <span class="oc-mono oc-items__total" x-text="formatMoney(itemsTotal(order.items))"></span>
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" class="oc-items__chevron" :class="{ 'oc-items__chevron--open': isItemsOpen(order.id) }">
                                        <path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>
                            <div class="oc-items__rows" x-show="isItemsOpen(order.id)">
                                <template x-for="(item, idx) in (order.items || [])" :key="idx">
                                    <div class="oc-items__row">
                                        <p class="oc-items__name" x-text="itemName(item)"></p>
                                        <span class="oc-mono oc-items__qty" x-text="'×' + (item.quantity || 0)"></span>
                                        <span class="oc-mono oc-items__sum" x-text="formatMoney(Number(item.price || 0) * Number(item.quantity || 0))"></span>
                                    </div>
                                </template>
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
                                <span class="oc-ship">
                                    <button type="button" class="oc-footlink" :aria-expanded="String(isShipOpen(order.id))" @click.stop="toggleShip(order.id)">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M7 1C4.79 1 3 2.79 3 5c0 3 4 8 4 8s4-5 4-8c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><circle cx="7" cy="5" r="1.4" stroke="currentColor" stroke-width="1.2"/></svg>
                                        <span>{{ __('text.cabinet_shipping') }}</span>
                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true" class="oc-ship__caret" :class="{ 'oc-ship__caret--open': isShipOpen(order.id) }">
                                            <path d="M2 3.5l3 3 3-3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                    <span class="oc-ship__popover" role="dialog" x-cloak :class="{ '_open': isShipOpen(order.id) }" @click.outside="closeShip(order.id)">
                                        <span class="oc-ship__handle" aria-hidden="true"></span>
                                        <span class="oc-ship__close" @click="closeShip(order.id)" style="cursor:pointer">×</span>
                                        <template x-if="shipLoading[order.id]">
                                            <div aria-hidden="true"><span class="cabp-skel" style="width:40%"></span><br><span class="cabp-skel" style="width:90%"></span></div>
                                        </template>
                                        <template x-if="!shipLoading[order.id] && shipFailed[order.id]">
                                            <span class="oc-ship__contact">
                                                <p class="oc-ship__note">{{ __('text.cabinet_shipping_failed') }}</p>
                                                <button type="button" class="oc-ship__btn" @click="loadShip(order.id)">{{ __('text.cabinet_retry') }}</button>
                                            </span>
                                        </template>
                                        <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipLoaded[order.id] && !shipData[order.id]">
                                            <p class="oc-ship__note">{{ __('text.cabinet_shipping_not_found') }}</p>
                                        </template>
                                        <template x-if="!shipLoading[order.id] && !shipFailed[order.id] && shipData[order.id]">
                                            <span>
                                                <p class="oc-ship__caption">{{ __('text.cabinet_shipping_to') }}</p>
                                                <p class="oc-ship__name" x-text="shipFullName(order.id)"></p>
                                                <p class="oc-ship__addr"><span x-text="shipAddress(order.id)"></span><br><span x-text="shipCityLine(order.id)"></span><br><span x-text="shipCountryLine(order.id)"></span></p>
                                                <span class="oc-ship__contact">
                                                    <p class="oc-ship__note">{{ __('text.cabinet_shipping_note') }}</p>
                                                    <a class="oc-ship__btn" href="{{ route('cabinet.live.page', ['page' => 'contact']) }}"><span>{{ __('text.cabinet_nav_contact') }}</span></a>
                                                </span>
                                            </span>
                                        </template>
                                    </span>
                                </span>
                                <button type="button" class="oc-footlink" x-show="order.trackingNumber === undefined || order.trackingNumber" @click="openTrack(order.id)">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M1.5 3.5h7v6h-7z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/><path d="M8.5 6H11l1.5 1.8v1.7H8.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="4.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2"/><circle cx="10.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2"/></svg>
                                    <span>{{ __('text.cabinet_tracking') }}</span>
                                </button>
                            </div>
                            <div class="oc-actions">
                                <span class="button cabp-action cabp-action--dark" :disabled="repeatingId === order.id" @click="repeatVirtual(order)" style="cursor:pointer">
                                    <span x-show="repeatingId !== order.id">{{ __('text.cabinet_repeat_order') }}</span>
                                    <span x-show="repeatingId === order.id">…</span>
                                </span>
                                <span class="button cabp-action cabp-action--danger-soft" x-show="showStop(order)" :disabled="stoppingId === order.id" @click="askStop(order.id)" style="cursor:pointer">
                                    <span>{{ __('text.cabinet_stop_auto') }}</span>
                                </span>
                            </div>
                        </div>

                        <template x-if="trackRendered[order.id]">
                            <span>
                                <span class="oc-track__backdrop _open" @click="closeTrack(order.id)"></span>
                                <span class="oc-track__panel _open" role="dialog" aria-modal="true" @keydown.escape.window="closeTrack(order.id)">
                                    <span class="oc-track__head">
                                        <span>
                                            <p class="oc-track__eyebrow">{{ __('text.cabinet_tracking_title') }}</p>
                                            <span class="oc-mono oc-track__order" x-text="orderNumber(order.id)"></span>
                                        </span>
                                        <span class="oc-track__close" @click="closeTrack(order.id)" style="cursor:pointer">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        </span>
                                    </span>
                                    <span class="oc-track__body">
                                        <template x-if="trackLoading[order.id]">
                                            <span aria-hidden="true"><span class="cabp-skel" style="width:40%"></span><br><span class="cabp-skel" style="width:90%"></span></span>
                                        </template>
                                        <template x-if="!trackLoading[order.id] && trackFailed[order.id]">
                                            <span class="cabp-loaderror"><p class="cabp-loaderror__text">{{ __('text.cabinet_tracking_failed') }}</p>
                                            <button type="button" class="button cabp-action cabp-action--dark" @click="loadTrack(order.id)">{{ __('text.cabinet_retry') }}</button></span>
                                        </template>
                                        <template x-if="!trackLoading[order.id] && !trackFailed[order.id] && !(trackList(order.id) || []).length">
                                            <p class="cabp-empty">{{ __('text.cabinet_track_none') }}</p>
                                        </template>
                                        <template x-if="!trackLoading[order.id] && !trackFailed[order.id] && (trackList(order.id) || []).length">
                                            <span>
                                                <dl class="oc-track__summary">
                                                    <span><dt>{{ __('text.cabinet_track_date') }}</dt><dd class="oc-mono" x-text="order.date"></dd></span>
                                                    <span><dt>{{ __('text.cabinet_track_sum') }}</dt><dd class="oc-mono oc-track__sum" x-text="formatMoney(order.total)"></dd></span>
                                                    <span><dt>{{ __('text.cabinet_track_status') }}</dt><dd class="oc-track__status" x-text="statusLabel(order)"></dd></span>
                                                    <span><dt>{{ __('text.cabinet_track_destination') }}</dt><dd x-text="trackDestination(order.id)"></dd></span>
                                                    <span><dt>{{ __('text.cabinet_track_delivery_time') }}</dt><dd x-text="trackDays(order.id)"></dd></span>
                                                    <span><dt>{{ __('text.cabinet_track_latest') }}</dt><dd class="oc-mono" x-text="trackLatest(order.id)"></dd></span>
                                                </dl>
                                                <template x-for="track in (trackList(order.id) || [])" :key="track.number">
                                                    <span class="oc-track__block">
                                                        <p class="oc-track__caption">Track</p>
                                                        <span class="oc-track__code-row">
                                                            <code class="oc-mono oc-track__code" x-text="track.number"></code>
                                                            <span class="oc-track__copy" @click="copyTrackNumber(track.number)" style="cursor:pointer">
                                                                <span x-text="trackCopied === track.number ? '{{ __('text.cabinet_copied') }}' : '{{ __('text.cabinet_copy') }}'"></span>
                                                            </span>
                                                        </span>
                                                        <p class="oc-track__route" x-show="track.fromName || track.toName || track.fromCountry || track.toCountry">
                                                            <span x-text="(track.fromName || track.fromCountry || '—')"></span>
                                                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M1.5 6h9M7.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                            <span x-text="(track.toName || track.toCountry || '—')"></span>
                                                        </p>
                                                        <span class="oc-track__latest" x-show="track.latestStatus">
                                                            <p class="oc-track__latest-caption">Latest update</p>
                                                            <p class="oc-track__latest-text" x-text="((track.latestEventLocation ? track.latestEventLocation + ', ' : '') + (track.latestEventDescription || ''))"></p>
                                                            <p class="oc-mono oc-track__latest-time" x-text="track.latestTime"></p>
                                                        </span>
                                                        <span class="oc-track__providers">
                                                            <template x-for="(provider, pIdx) in (track.providers || [])" :key="pIdx">
                                                                <span class="oc-timeline">
                                                                    <span class="oc-timeline__head">
                                                                        <p class="oc-timeline__title"><span x-text="provider.name"></span> <span class="oc-timeline__country">· <span x-text="provider.country"></span></span></p>
                                                                        <span class="oc-mono oc-timeline__count" x-text="((provider.events || []).length + ' events')"></span>
                                                                    </span>
                                                                    <span class="oc-timeline__body">
                                                                        <p class="oc-mono oc-timeline__sync" x-text="'Sync Time: ' + (provider.latestSyncTime || '')"></p>
                                                                        <ol class="oc-timeline__list">
                                                                            <template x-for="(ev, eIdx) in visibleEvents(order.id, track, pIdx)" :key="eIdx">
                                                                                <li class="oc-timeline__event">
                                                                                    <span class="oc-timeline__dot" :class="{ 'oc-timeline__dot--first': eIdx === 0 }"></span>
                                                                                    <p class="oc-mono oc-timeline__time" x-text="ev.time"></p>
                                                                                    <p class="oc-timeline__text" x-text="((ev.location ? ev.location + ', ' : '') + (ev.description || ''))"></p>
                                                                                </li>
                                                                            </template>
                                                                        </ol>
                                                                        <span class="oc-timeline__more" x-show="(provider.events || []).length > 5 && !isProviderExpanded(order.id, track, pIdx)" @click="toggleProvider(order.id, track, pIdx)" style="cursor:pointer">
                                                                            <span x-text="showAllLabel(provider)"></span>
                                                                        </span>
                                                                        <span class="oc-timeline__more oc-timeline__more--muted" x-show="isProviderExpanded(order.id, track, pIdx)" @click="toggleProvider(order.id, track, pIdx)" style="cursor:pointer">
                                                                            <span>{{ __('text.cabinet_hide_events') }}</span>
                                                                        </span>
                                                                    </span>
                                                                </span>
                                                            </template>
                                                        </span>
                                                    </span>
                                                </template>
                                            </span>
                                        </template>
                                    </span>
                                </span>
                            </span>
                        </template>
                    </article>
                </template>

                <div x-show="loadingMore" style="text-align:center;padding:8px"><span class="cabp-spinner" aria-hidden="true"></span></div>
            </div>
        </template>

        <div x-ref="sentinel" aria-hidden="true"></div>
    </div>

    <aside class="oc-side">
        <div class="oc-side__card oc-side__card--filters">
            <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
            <div class="oc-side__filters" role="group" aria-label="{{ __('text.cabinet_filter_by_status') }}">
                <template x-for="f in filters" :key="f.key">
                    <button type="button" class="oc-side__filter" :class="{ 'oc-side__filter--active': activeFilter === f.key }" :disabled="listBusy" :aria-pressed="String(activeFilter === f.key)" @click="selectFilter(f.key)"><span x-text="f.label"></span></button>
                </template>
            </div>
        </div>
        <div class="oc-side__card">
            <p class="oc-side__help-title">{{ __('text.cabinet_help_title') }}</p>
            <p class="oc-side__help-text">{{ __('text.cabinet_help_text') }}</p>
            <a class="button cabp-action cabp-action--dark cabp-wide" href="{{ route('cabinet.live.page', ['page' => 'contact']) }}">
                <span>{{ __('text.cabinet_nav_contact') }}</span>
            </a>
        </div>
    </aside>

    <button type="button" class="oc-filters-fab" :aria-expanded="String(filtersOpen)" @click.stop="filtersOpen = !filtersOpen">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v5.5l-4 2.5v-8L3 5z"/></svg>
        <span x-text="activeFilterLabel"></span>
    </button>
    <div class="oc-filters-pop" role="dialog" x-cloak :class="{ '_open': filtersOpen }" @click.outside="filtersOpen = false" @keydown.escape.window="filtersOpen = false">
        <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
        <div class="oc-side__filters" role="group">
            <template x-for="f in filters" :key="f.key">
                <button type="button" class="oc-side__filter" :class="{ 'oc-side__filter--active': activeFilter === f.key }" :disabled="listBusy" @click="selectFilter(f.key)"><span x-text="f.label"></span></button>
            </template>
        </div>
    </div>

    <div class="cabp-confirm" x-cloak :class="{ '_open': confirmStopId !== null }">
        <div class="cabp-confirm__overlay" @click="cancelStop()"></div>
        <div class="cabp-confirm__dialog" role="alertdialog" aria-modal="true">
            <div class="cabp-confirm__head">
                <p class="cabp-confirm__title">{{ __('text.cabinet_stop_auto') }}</p>
                <p class="cabp-confirm__description">{{ __('text.cabinet_stop_confirm') }}</p>
            </div>
            <div class="cabp-confirm__footer">
                <button type="button" class="button cabp-action cabp-action--light" @click="cancelStop()">{{ __('text.cabinet_cancel') }}</button>
                <button type="button" class="button cabp-action cabp-action--danger" @click="confirmStop()">{{ __('text.cabinet_stop') }}</button>
            </div>
        </div>
    </div>

    <div class="cabp-toast" role="alert" x-show="toast" x-text="toast"></div>
</div>
