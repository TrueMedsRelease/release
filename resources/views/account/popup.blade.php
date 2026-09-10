@extends('account.layout')

@section('cab_title', __('text.cabinet_login_title'))

@section('cab_content')
    <h1 class="cab-account__title">{{ __('text.cabinet_login_title') }}</h1>
    <p class="cab-account__sub" id="cab-popup-status">{{ __('text.cabinet_popup_wait') }}</p>
    <div class="cab-account__list">
        <div class="oc-card" style="padding: 20px;">
            <a id="cab-popup-manual" class="oc-action oc-action--light" href="{{ $login_url }}">{{ __('text.cabinet_popup_manual') }}</a>
        </div>
    </div>
@endsection

@push('cab_scripts')
<script>
(function () {
    var loginUrl = @json($loginUrl);
    var opened = null;
    try {
        opened = window.open(loginUrl, 'cabinet_login', 'width=520,height=660,menubar=no,toolbar=no,location=yes,status=no');
    } catch (e) { opened = null; }
    if (!opened) return; // popup blocked — manual link above
    document.getElementById('cab-popup-manual').style.display = 'none';
    var timer = setInterval(function () {
        if (opened.closed) {
            clearInterval(timer);
            window.location.href = @json(route('account.orders'));
        }
    }, 500);
})();
</script>
@endpush
