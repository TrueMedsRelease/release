{{--
    Shared: тело страницы "My orders". Структура 1:1 с референсом
    (customerportal Orders.vue + OrdersTable + OrderCard + OrderCardItems +
    OrderShippingPopover + OrderTrackingDrawer + OrderTrackBlock + OrdersSidebar
    + ConfirmDialog). Трекинг — только в дровере, по умолчанию скрыт.
    Без JS: details для позиций, checkbox-тоглы для поповера/дровера/диалога.
    Стили — в per-design CSS. Переменные: $design, $cabp_orders, $cabp_filters, $cabp_ship.
--}}
<div class="cabp-layout cabp-layout--orders">
    <div class="cabp-main">
        @foreach ($cabp_orders as $order)
            @php
                $pill = match ($order['status_key']) {
                    'delivered' => 'oc-pill--delivered',
                    'approved' => 'oc-pill--processing',
                    'declined', 'refunded' => 'oc-pill--cancelled',
                    default => 'oc-pill--pending',
                };
            @endphp
            <article class="oc-card">
                <div class="oc-card__head">
                    <div class="oc-card__id">
                        <p class="oc-card__date">{{ $order['date'] }}</p>
                        <span class="oc-card__number oc-copy" title="{{ __('text.cabinet_copy') }}">
                            <span>{{ __('text.cabinet_order_number', ['id' => $order['id']]) }}</span>
                            <svg width="13" height="13" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3" />
                                <path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3" />
                            </svg>
                        </span>
                    </div>
                    @if ($order['status_key'] !== 'unknown')
                        <span class="oc-pill {{ $pill }}"><span class="oc-pill__dot" aria-hidden="true"></span>{{ $order['status'] }}</span>
                    @endif
                </div>

                @if (count($order['items']))
                    <details class="oc-items" open>
                        <summary class="oc-items__toggle">
                            <span class="oc-items__label">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <path d="M2.2 4h7.6l-.65 6.2a.5.5 0 0 1-.5.45H3.35a.5.5 0 0 1-.5-.45L2.2 4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
                                    <path d="M4.2 4V3a1.8 1.8 0 0 1 3.6 0v1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                </svg>
                                <span class="oc-items__label-text">{{ __('text.cabinet_items', ['count' => count($order['items'])]) }}</span>
                            </span>
                            <span class="oc-items__meta">
                                <span class="oc-mono oc-items__total">{{ $order['total'] }}</span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true" class="oc-items__chevron">
                                    <path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </summary>
                        <div class="oc-items__rows">
                            @foreach ($order['items'] as $item)
                                <div class="oc-items__row">
                                    <p class="oc-items__name">{{ $item['name'] }}</p>
                                    <span class="oc-mono oc-items__qty">×{{ $item['qty'] }}</span>
                                    <span class="oc-mono oc-items__sum">{{ $item['sum'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </details>
                @else
                    <p class="oc-card__nodetail">{{ __('text.cabinet_no_details') }}</p>
                @endif

                @if (!empty($order['next_date']))
                    <p class="oc-card__status">{{ $order['next_date'] }}</p>
                @endif

                <div class="oc-card__foot">
                    <div class="oc-card__links">
                        @if (!empty($order['bonus']))
                            <span class="oc-bonus">+${{ number_format($order['bonus'], 2) }} {{ __('text.cabinet_bonus') }}</span>
                        @endif
                    </div>
                    <div class="oc-card__total">
                        <span class="oc-card__total-label">{{ __('text.cabinet_total') }}</span>
                        <span class="oc-mono oc-card__total-value">{{ $order['total'] }}</span>
                    </div>
                </div>

                <div class="oc-card__actions">
                    <div class="oc-card__secondary">
                        <span class="oc-ship">
                            <input class="cabp-check" type="checkbox" id="ship-{{ $order['id'] }}">
                            <label class="oc-footlink" for="ship-{{ $order['id'] }}">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                    <path d="M7 1C4.79 1 3 2.79 3 5c0 3 4 8 4 8s4-5 4-8c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" />
                                    <circle cx="7" cy="5" r="1.4" stroke="currentColor" stroke-width="1.2" />
                                </svg>
                                <span>{{ __('text.cabinet_shipping') }}</span>
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true" class="oc-ship__caret">
                                    <path d="M2 3.5l3 3 3-3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </label>
                            <span class="oc-ship__popover" role="dialog" aria-label="{{ __('text.cabinet_shipping') }}">
                                <span class="oc-ship__handle" aria-hidden="true"></span>
                                <p class="oc-ship__caption">{{ __('text.cabinet_shipping_to') }}</p>
                                <p class="oc-ship__name">{{ $cabp_ship['name'] }}</p>
                                <p class="oc-ship__addr">{{ $cabp_ship['address'] }}<br>{{ $cabp_ship['city'] }}<br>{{ $cabp_ship['country'] }}</p>
                                <span class="oc-ship__contact">
                                    <p class="oc-ship__note">{{ __('text.cabinet_shipping_note') }}</p>
                                    <a class="oc-ship__btn" href="{{ route('preview.cabinet.page', ['design' => $design, 'page' => 'contact']) }}">
                                        <span>{{ __('text.cabinet_nav_contact') }}</span>
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                            <path d="M2.5 6h7M6.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </span>
                                <label class="oc-ship__close" for="ship-{{ $order['id'] }}" aria-label="{{ __('text.cabinet_cancel') }}">×</label>
                            </span>
                            <label class="oc-ship__backdrop" for="ship-{{ $order['id'] }}" aria-hidden="true"></label>
                        </span>
                        @if (!empty($order['tracking_number']))
                            <span class="oc-track">
                                <input class="cabp-check" type="checkbox" id="track-{{ $order['id'] }}">
                                <label class="oc-footlink" for="track-{{ $order['id'] }}">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                        <path d="M1.5 3.5h7v6h-7z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round" />
                                        <path d="M8.5 6H11l1.5 1.8v1.7H8.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        <circle cx="4.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2" />
                                        <circle cx="10.5" cy="10.7" r="1.2" stroke="currentColor" stroke-width="1.2" />
                                    </svg>
                                    <span>{{ __('text.cabinet_tracking') }}</span>
                                </label>
                                <label class="oc-track__backdrop" for="track-{{ $order['id'] }}" aria-hidden="true"></label>
                                <span class="oc-track__panel" role="dialog" aria-modal="true" aria-label="{{ __('text.cabinet_tracking') }}">
                                    <span class="oc-track__head">
                                        <span>
                                            <p class="oc-track__eyebrow">{{ __('text.cabinet_tracking_title') }}</p>
                                            <span class="oc-mono oc-track__order">{{ __('text.cabinet_order_number', ['id' => $order['id']]) }}</span>
                                        </span>
                                        <label class="oc-track__close" for="track-{{ $order['id'] }}" aria-label="{{ __('text.cabinet_cancel') }}">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                        </label>
                                    </span>
                                    <span class="oc-track__body">
                                        <dl class="oc-track__summary">
                                            <span><dt>{{ __('text.cabinet_track_date') }}</dt><dd class="oc-mono">{{ $order['date'] }}</dd></span>
                                            <span><dt>{{ __('text.cabinet_track_sum') }}</dt><dd class="oc-mono oc-track__sum">{{ $order['total'] }}</dd></span>
                                            <span><dt>{{ __('text.cabinet_track_status') }}</dt><dd class="oc-track__status">{{ $order['status'] }}</dd></span>
                                            <span><dt>{{ __('text.cabinet_track_destination') }}</dt><dd>{{ $order['tracking']['destination'] }}</dd></span>
                                            <span><dt>{{ __('text.cabinet_track_delivery_time') }}</dt><dd>{{ __('text.cabinet_track_days', ['n' => $order['tracking']['days']]) }}</dd></span>
                                            <span><dt>{{ __('text.cabinet_track_latest') }}</dt><dd class="oc-mono">{{ $order['tracking']['latest'] }}</dd></span>
                                        </dl>
                                        @foreach ($order['tracking']['tracks'] as $track)
                                            <span class="oc-track__block">
                                                <p class="oc-track__caption">Track</p>
                                                <span class="oc-track__code-row">
                                                    <code class="oc-mono oc-track__code">{{ $track['number'] }}</code>
                                                    <span class="oc-track__copy" title="{{ __('text.cabinet_copy') }}">
                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                            <rect x="4" y="4" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.3" />
                                                            <path d="M8 4V2.5A1.5 1.5 0 0 0 6.5 1H2.5A1.5 1.5 0 0 0 1 2.5v4A1.5 1.5 0 0 0 2.5 8H4" stroke="currentColor" stroke-width="1.3" />
                                                        </svg>
                                                        <span>{{ __('text.cabinet_copy') }}</span>
                                                    </span>
                                                </span>
                                                <p class="oc-track__route">
                                                    <span>{{ $track['from'] }}</span>
                                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                        <path d="M1.5 6h9M7.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span>{{ $track['to'] }}</span>
                                                </p>
                                                <span class="oc-track__latest">
                                                    <p class="oc-track__latest-caption">{{ __('text.cabinet_track_latest') }}</p>
                                                    <p class="oc-track__latest-text">{{ $track['latest'] }}</p>
                                                    <p class="oc-mono oc-track__latest-time">{{ $track['latest_time'] }}</p>
                                                </span>
                                                <span class="oc-track__providers">
                                                    @foreach ($track['providers'] as $pi => $provider)
                                                        <span class="oc-timeline">
                                                            <span class="oc-timeline__head">
                                                                <p class="oc-timeline__title">{{ $provider['name'] }} <span class="oc-timeline__country">· {{ $provider['country'] }}</span></p>
                                                                <span class="oc-mono oc-timeline__count">{{ count($provider['events']) }} {{ __('text.cabinet_events') }}</span>
                                                            </span>
                                                            <span class="oc-timeline__body">
                                                                <p class="oc-mono oc-timeline__sync">Sync Time: {{ $provider['sync'] }}</p>
                                                                @if (count($provider['events']) > 5)
                                                                    <input class="cabp-check" type="checkbox" id="track-{{ $order['id'] }}-prov-{{ $pi }}">
                                                                @endif
                                                                <ol class="oc-timeline__list">
                                                                    @foreach ($provider['events'] as $ei => $event)
                                                                        <li class="oc-timeline__event @if ($ei > 4) oc-timeline__event--extra @endif">
                                                                            <span class="oc-timeline__dot @if ($ei === 0) oc-timeline__dot--first @endif"></span>
                                                                            <p class="oc-mono oc-timeline__time">{{ $event['time'] }}</p>
                                                                            <p class="oc-timeline__text">{{ $event['text'] }}</p>
                                                                        </li>
                                                                    @endforeach
                                                                </ol>
                                                                @if (count($provider['events']) > 5)
                                                                    <label class="oc-timeline__more oc-timeline__more--show" for="track-{{ $order['id'] }}-prov-{{ $pi }}">
                                                                        <span>{{ __('text.cabinet_show_all_events', ['count' => count($provider['events'])]) }}</span>
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                            <path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                                        </svg>
                                                                    </label>
                                                                    <label class="oc-timeline__more oc-timeline__more--muted oc-timeline__more--hide" for="track-{{ $order['id'] }}-prov-{{ $pi }}">
                                                                        <span>{{ __('text.cabinet_hide_events') }}</span>
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                                                            <path d="M2.5 7.5L6 4l3.5 3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                                                                        </svg>
                                                                    </label>
                                                                @endif
                                                            </span>
                                                        </span>
                                                    @endforeach
                                                </span>
                                            </span>
                                        @endforeach
                                    </span>
                                </span>
                            </span>
                        @endif
                    </div>
                    <div class="oc-actions">
                        @if (!empty($order['billing_url']))
                            <a class="button cabp-action cabp-action--dark" href="{{ $order['billing_url'] }}" target="_blank" rel="noopener noreferrer">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <rect x="1.5" y="2.5" width="9" height="7" rx="1.2" stroke="currentColor" stroke-width="1.3" />
                                    <path d="M1.5 4.8h9" stroke="currentColor" stroke-width="1.3" />
                                    <path d="M3 7.2h2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                </svg>
                                <span>{{ __('text.cabinet_billing') }}</span>
                            </a>
                        @endif
                        <span class="button cabp-action cabp-action--dark">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M10.5 6A4.5 4.5 0 1 1 6 1.5c1.6 0 3 .8 3.9 2M9.9 1v2.5H7.4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>{{ __('text.cabinet_repeat_order') }}</span>
                        </span>
                        @if (!empty($order['recurring']))
                            <span class="cabp-confirm">
                                <input class="cabp-check" type="checkbox" id="stop-{{ $order['id'] }}">
                                <label class="button cabp-action cabp-action--light" for="stop-{{ $order['id'] }}">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                        <circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3" />
                                        <path d="M4 4l4 4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                    </svg>
                                    <span>{{ __('text.cabinet_stop_auto') }}</span>
                                </label>
                                <label class="cabp-confirm__overlay" for="stop-{{ $order['id'] }}" aria-hidden="true"></label>
                                <span class="cabp-confirm__dialog" role="alertdialog" aria-modal="true">
                                    <span class="cabp-confirm__head">
                                        <span class="cabp-confirm__title">{{ __('text.cabinet_stop_auto') }}</span>
                                        <p class="cabp-confirm__description">{{ __('text.cabinet_stop_confirm') }}</p>
                                    </span>
                                    <span class="cabp-confirm__footer">
                                        <label class="button cabp-action cabp-action--light" for="stop-{{ $order['id'] }}"><span>{{ __('text.cabinet_cancel') }}</span></label>
                                        <span class="button cabp-action cabp-action--danger"><span>{{ __('text.cabinet_stop') }}</span></span>
                                    </span>
                                </span>
                            </span>
                        @endif
                        @if (!empty($order['shop']))
                            <a class="button cabp-action cabp-action--light" href="{{ $order['shop'] }}" target="_blank" rel="noopener noreferrer">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                    <path d="M2.2 4h7.6l-.65 6.2a.5.5 0 0 1-.5.45H3.35a.5.5 0 0 1-.5-.45L2.2 4z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round" />
                                    <path d="M4.2 4V3a1.8 1.8 0 0 1 3.6 0v1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" />
                                </svg>
                                <span>{{ __('text.cabinet_shop') }}</span>
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <aside class="oc-side">
        <div class="oc-side__card oc-side__card--filters">
            <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
            <div class="oc-side__filters" role="group" aria-label="{{ __('text.cabinet_filter_by_status') }}">
                @foreach ($cabp_filters as $f)
                    <span class="oc-side__filter @if ($f['key'] === 'all') oc-side__filter--active @endif">{{ $f['label'] }}</span>
                @endforeach
            </div>
        </div>
        <div class="oc-side__card">
            <p class="oc-side__help-title">{{ __('text.cabinet_help_title') }}</p>
            <p class="oc-side__help-text">{{ __('text.cabinet_help_text') }}</p>
            <a class="button cabp-action cabp-action--dark cabp-wide" href="{{ route('preview.cabinet.page', ['design' => $design, 'page' => 'contact']) }}">
                <span>{{ __('text.cabinet_nav_contact') }}</span>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                    <path d="M2.5 6h7M6.5 3l3 3-3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </aside>

    <input class="cabp-check" type="checkbox" id="cabp-filters">
    <label class="oc-filters-fab" for="cabp-filters">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v5.5l-4 2.5v-8L3 5z"/></svg>
        <span>{{ __('text.cabinet_filter_all') }}</span>
    </label>
    <div class="oc-filters-pop" role="dialog" aria-label="{{ __('text.cabinet_filter_by_status') }}">
        <p class="oc-side__caption">{{ __('text.cabinet_filter_by_status') }}</p>
        <div class="oc-side__filters" role="group" aria-label="{{ __('text.cabinet_filter_by_status') }}">
            @foreach ($cabp_filters as $f)
                <span class="oc-side__filter @if ($f['key'] === 'all') oc-side__filter--active @endif">{{ $f['label'] }}</span>
            @endforeach
        </div>
    </div>
</div>
