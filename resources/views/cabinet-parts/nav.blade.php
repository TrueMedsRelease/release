{{--
    Shared: шапка кабинета (табы + юзер). Структура 1:1 с референсом.
    Используется превью всех дизайнов; стили — в per-design CSS.
--}}
@php
    $cabpTabs = [
        ['key' => 'orders', 'label' => __('text.cabinet_nav_orders')],
        ['key' => 'bonus', 'label' => __('text.cabinet_nav_bonus')],
        ['key' => 'referral', 'label' => __('text.cabinet_nav_referral')],
        ['key' => 'contact', 'label' => __('text.cabinet_nav_contact')],
        ['key' => 'chat', 'label' => __('text.cabinet_nav_chat')],
    ];
    $cabpCurrent = $cabp_page ?? 'orders';
@endphp
<div class="cabp-head">
    <div class="cabp-user">
        <span class="cabp-avatar">{{ $cabp_user['initials'] }}</span>
        <div class="cabp-user__meta">
            <b>{{ $cabp_user['name'] }}</b>
            <span>{{ $cabp_user['id_label'] }} · {{ $cabp_user['email'] }}</span>
        </div>
        <span class="cabp-bonus">{{ $cabp_user['bonus'] }} {{ __('text.cabinet_bonus') }}</span>
    </div>
    <nav class="cabp-tabs" aria-label="Cabinet">
        @foreach ($cabpTabs as $tab)
            <a href="{{ route('preview.cabinet.page', ['design' => $design, 'page' => $tab['key']]) }}"
               class="cabp-tab @if ($cabpCurrent === $tab['key']) is-active @endif"
               @if ($cabpCurrent === $tab['key']) aria-current="page" @endif>{{ $tab['label'] }}</a>
        @endforeach
    </nav>
</div>
