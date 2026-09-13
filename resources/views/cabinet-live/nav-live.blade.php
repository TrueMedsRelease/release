{{--
     Live: шапка разделов кабинета в лейауте магазина.
     Табы скрываем сразу по capabilities (кеш 60с), фолбэк — session cabinet.forbidden.*
     + me-chip через /account/api/me (403 по скоупу me — прячем целиком).
     Переменные: $design, $cabp_page, $cabCapabilities (опц.).
--}}
@php
    use App\Services\CabinetService;
    $cabpTabs = [
        ['key' => 'orders', 'label' => __('text.cabinet_nav_orders'), 'scope' => 'orders'],
        ['key' => 'bonus', 'label' => __('text.cabinet_nav_bonus'), 'scope' => 'bonus'],
        ['key' => 'referral', 'label' => __('text.cabinet_nav_referral'), 'scope' => 'referral'],
        ['key' => 'contact', 'label' => __('text.cabinet_nav_contact'), 'scope' => 'contact'],
        ['key' => 'chat', 'label' => __('text.cabinet_nav_chat'), 'scope' => 'chat'],
    ];
    $cabpCurrent = $cabp_page ?? 'orders';
    // capabilities — серверный префетч с кешем 60с (мгновенно, без 403-мигания)
    $cabCapabilities = $cabCapabilities ?? CabinetService::capabilities();
    $cabScopes = is_array($cabCapabilities) && isset($cabCapabilities['scopes']) && is_array($cabCapabilities['scopes'])
        ? $cabCapabilities['scopes'] : null;
    $cabChatEnabled = is_array($cabCapabilities) ? ($cabCapabilities['chat_enabled'] ?? true) : true;
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
            @if ($tab['key'] === 'chat' && !$cabChatEnabled) @continue @endif
            @if ($cabScopes !== null)
                @if (!empty($tab['scope']) && !in_array($tab['scope'], $cabScopes, true)) @continue @endif
            @else
                @if (!empty($tab['scope']) && session('cabinet.forbidden.' . $tab['scope'])) @continue @endif
            @endif
            <a href="{{ route('cabinet.live.page', ['page' => $tab['key']]) }}"
               class="cabp-tab @if ($cabpCurrent === $tab['key']) is-active @endif"
               @if ($cabpCurrent === $tab['key']) aria-current="page" @endif>{{ $tab['label'] }}</a>
        @endforeach
    </nav>
</div>
