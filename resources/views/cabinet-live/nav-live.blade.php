{{--
    Live: шапка разделов кабинета в лейауте магазина.
    Табы (скрытие по session cabinet.forbidden.*, как боевой layout) + me-chip
    через /account/api/me (403 по скоупу me — прячем целиком).
    Переменные: $design, $cabp_page.
--}}
@php
    $cabpTabs = [
        ['key' => 'orders', 'label' => __('text.cabinet_nav_orders'), 'scope' => 'orders'],
        ['key' => 'bonus', 'label' => __('text.cabinet_nav_bonus'), 'scope' => 'bonus'],
        ['key' => 'referral', 'label' => __('text.cabinet_nav_referral'), 'scope' => 'referral'],
        ['key' => 'contact', 'label' => __('text.cabinet_nav_contact'), 'scope' => 'contact'],
        ['key' => 'chat', 'label' => __('text.cabinet_nav_chat'), 'scope' => 'chat'],
    ];
    $cabpCurrent = $cabp_page ?? 'orders';
@endphp
<div class="cabp-head" x-data="shopCabinetNav()" x-cloak>
    <div class="cabp-user" x-show="!meForbidden">
        <span class="cabp-avatar" x-text="meLoading ? '…' : avatarInitials"></span>
        <div class="cabp-user__meta">
            <b x-text="meLoading ? '' : userName"></b>
            <span x-text="meLoading ? '' : userIdLabel"></span>
        </div>
        <span class="cabp-bonus" x-show="bonusText" x-text="bonusText + ' {{ __('text.cabinet_bonus') }}'"></span>
    </div>
    <nav class="cabp-tabs" aria-label="Cabinet">
        @foreach ($cabpTabs as $tab)
            @if (!empty($tab['scope']) && session('cabinet.forbidden.' . $tab['scope'])) @continue @endif
            <a href="{{ route('cabinet.live.page', ['page' => $tab['key']]) }}"
               class="cabp-tab @if ($cabpCurrent === $tab['key']) is-active @endif"
               @if ($cabpCurrent === $tab['key']) aria-current="page" @endif>{{ $tab['label'] }}</a>
        @endforeach
    </nav>
</div>
