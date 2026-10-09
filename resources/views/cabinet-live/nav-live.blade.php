{{--
     Live: cabinet section header in the shop layout.
     Tabs are hidden upfront via capabilities (60s cache), fallback is session cabinet.forbidden.*
     + me-chip via /account/api/me (403 on me scope hides it entirely).
     Variables: $design, $cabp_page, $cabCapabilities (opt.).
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
    // capabilities — server-side prefetch with 60s cache (instant, no 403 flicker)
    $cabCapabilities = $cabCapabilities ?? CabinetService::capabilities();
    $cabScopes = is_array($cabCapabilities) && isset($cabCapabilities['scopes']) && is_array($cabCapabilities['scopes'])
        ? $cabCapabilities['scopes'] : null;
    $cabChatEnabled = is_array($cabCapabilities) ? ($cabCapabilities['chat_enabled'] ?? true) : true;
    // Shop display settings (admin panel → cabinet): a disabled page hides
    // the tab immediately, ANDed with the license scopes below.
    $cabDisplay = $cabDisplay ?? CabinetService::displayConfig();
    $cabPages = is_array($cabDisplay) && isset($cabDisplay['pages']) && is_array($cabDisplay['pages'])
        ? $cabDisplay['pages'] : [];
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
            @if (array_key_exists($tab['key'], $cabPages) && empty($cabPages[$tab['key']])) @continue @endif
            @if ($cabScopes !== null)
                @if (!empty($tab['scope']) && !in_array($tab['scope'], $cabScopes, true)) @continue @endif
            @else
                @if (!empty($tab['scope']) && session('cabinet.forbidden.' . $tab['scope'])) @continue @endif
            @endif
            <a href="{{ route('cabinet.live.page', ['page' => $tab['key']]) }}"
               class="cabp-tab @if ($cabpCurrent === $tab['key']) is-active @endif"
               @if ($cabpCurrent === $tab['key']) aria-current="page" @endif>{{ $tab['label'] }}</a>
        @endforeach
        <form method="POST" action="{{ route('account.logout') }}" style="margin:0 0 0 auto;display:inline-flex">
            @csrf
            <button type="submit" class="cabp-tab" style="font:inherit;background:transparent;cursor:pointer">{{ __('text.cabinet_logout') }}</button>
        </form>
    </nav>
</div>
