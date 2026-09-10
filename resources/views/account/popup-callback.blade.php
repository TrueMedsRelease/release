@extends('account.layout')

@section('cab_title', __('text.cabinet_login_title'))

@section('cab_content')
    @if ($success)
        <h1 class="cab-account__title">{{ __('text.cabinet_login_done') }}</h1>
        <p class="cab-account__sub">{{ __('text.cabinet_login_done_text') }}</p>
        <div class="cab-account__list">
            <div class="oc-card" style="padding: 20px;">
                <a class="oc-action oc-action--dark" href="{{ route('account.orders') }}">{{ __('text.cabinet_my_orders') }}</a>
            </div>
        </div>
    @else
        <h1 class="cab-account__title">{{ __('text.cabinet_login_title') }}</h1>
        <div class="cab-account__error" role="alert">{{ __('text.cabinet_login_failed') }}</div>
        <div style="height: 16px;"></div>
        <div class="cab-account__list">
            <div class="oc-card" style="padding: 20px;">
                <a class="oc-action oc-action--light" href="{{ route('account.index') }}">{{ __('text.cabinet_try_again') }}</a>
            </div>
        </div>
    @endif
@endsection

@push('cab_scripts')
<script>
(function () {
    // В попапе пробуем закрыться — opener сам обновится (поллит popup.closed).
    try {
        if (window.opener && !window.opener.closed) window.close();
    } catch (e) {}
})();
</script>
@endpush
