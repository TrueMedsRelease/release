@extends('account.layout')

@section('cab_title', __('text.cabinet_sessions_title'))

@section('cab_content')
<div x-data="cabinetSessions()">
    <h1 class="cab-account__title">{{ __('text.cabinet_sessions_title') }}</h1>
    <p class="cab-account__sub">{{ __('text.cabinet_sessions_sub') }}</p>

    <div x-show="loading" class="cab-account__loading">{{ __('text.cabinet_loading') }}</div>
    <div x-show="!loading && error" class="cab-account__error" role="alert" x-text="error"></div>

    <div class="cab-account__list" x-show="!loading && !error">
        <template x-for="s in sessions" :key="s.token_hash">
            <div class="oc-card" style="padding: 14px 20px;">
                <p class="oc-card__number" style="font-size: 14px;" x-text="s.shop_name || s.shop_domain || s.shop_id"></p>
                <p class="oc-card__status" style="margin: 4px 0 0;" x-text="(s.shop_domain || '') + ' В· ' + formatDate(s.issued_at)"></p>
            </div>
        </template>
        <div class="oc-card" style="padding: 16px 20px;" x-show="!sessions.length">
            <p class="cab-account__sub" style="margin: 0;">{{ __('text.cabinet_sessions_empty') }}</p>
        </div>
        <div x-show="sessions.length">
            <button type="button" class="oc-action oc-action--light" style="width: 100%;" @click="askRevoke()">
                {{ __('text.cabinet_revoke_all') }}
            </button>
        </div>
    </div>

    <div class="oc-confirm-backdrop" x-show="confirmOpen" x-cloak>
        <div class="oc-confirm" role="dialog" aria-modal="true">
            <h3>{{ __('text.cabinet_revoke_all') }}</h3>
            <p>{{ __('text.cabinet_revoke_confirm') }}</p>
            <div class="oc-confirm__actions">
                <button type="button" class="oc-confirm__btn oc-confirm__btn--ghost" @click="confirmOpen = false">{{ __('text.cabinet_cancel') }}</button>
                <button type="button" class="oc-confirm__btn oc-confirm__btn--danger" :disabled="revoking" @click="confirmRevoke()">{{ __('text.cabinet_revoke') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('cab_scripts')
@php
// РћС‚РґРµР»СЊРЅРѕР№ РїРµСЂРµРјРµРЅРЅРѕР№: @json([...]) СЃ РІР»РѕР¶РµРЅРЅС‹РјРё route() РІРЅСѓС‚СЂРё
// Р»РѕРјР°РµС‚ РїР°СЂСЃРµСЂ Blade-РґРёСЂРµРєС‚РёРІ (РЅРµСЃР±Р°Р»Р°РЅСЃРёСЂРѕРІР°РЅРЅС‹Рµ СЃРєРѕР±РєРё РІ PHP).
$cabinetConfig = [
    'cabinetUrl' => $cabinet_url,
    'email' => $email,
    'startUrl' => route('account.index'),
    'tScopeForbidden' => __('text.cabinet_scope_forbidden'),
];
@endphp
<script>
window.CabinetCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
window.CabinetConfig = @json($cabinetConfig);
</script>
<script src="{{ asset('js/cabinet-alpine.min.js') }}" defer></script>
<script src="{{ asset('js/cabinet/account.js') }}?v={{ filemtime(public_path('js/cabinet/account.js')) }}"></script>
@endpush

