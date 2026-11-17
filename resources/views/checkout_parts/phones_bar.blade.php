@php
    $ckPhoneFlags = [
        'US' => 'united states', 'CA' => 'canada', 'AU' => 'australia', 'UK' => 'united kingdom',
        'FR' => 'france', 'ES' => 'spain', 'NZ' => 'new zealand', 'DK' => 'denmark', 'SE' => 'sweden',
        'CH' => 'switzerland', 'CZ' => 'czechia', 'FI' => 'finland', 'GR' => 'greece', 'PT' => 'portugal',
        'DE' => 'germany', 'IT' => 'italy', 'NL' => 'netherlands',
    ];
    $ckFlagUrl = fn ($iso) => isset($ckPhoneFlags[$iso])
        ? asset('style_checkout/images/countrys/' . rawurlencode($ckPhoneFlags[$iso]) . '.svg')
        : null;
    $ckMainPhoneId = array_key_first($phone_arr);
    $ckOtherPhones = array_slice($phone_arr, 1, null, true);
@endphp
<div class="header__phones-top top-phones-header ck-phones">
    <div class="ck-phones__container header__container">
        <span class="ck-phones__label">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1L6.6 10.8Z" fill="currentColor"/>
            </svg>
            <span>{{ __('text.phones_title_support') }}</span>
        </span>

        @if ($ckMainPhoneId)
            <a class="ck-phones__main" href="tel:{{ __('text.phones_title_phone_' . $ckMainPhoneId) }}">
                @if ($ckFlagUrl($phone_arr[$ckMainPhoneId]))
                    <img class="ck-phones__flag" src="{{ $ckFlagUrl($phone_arr[$ckMainPhoneId]) }}" width="20" height="14" alt="{{ $phone_arr[$ckMainPhoneId] }}">
                @endif
                <span>{{ __('text.phones_title_phone_' . $ckMainPhoneId) }}</span>
            </a>
        @endif

        @if (count($ckOtherPhones))
            <details class="ck-phones__more" data-ck-phones-more>
                <summary class="ck-phones__more-btn">
                    <span>{{ __('text.checkout_phones_other') }}</span>
                    <svg width="12" height="8" viewBox="0 0 12 8" aria-hidden="true"><path d="M1 1.5l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </summary>
                <div class="ck-phones__list">
                    @foreach ($ckOtherPhones as $idPhone => $iso)
                        <a class="ck-phones__item" href="tel:{{ __('text.phones_title_phone_' . $idPhone) }}">
                            @if ($ckFlagUrl($iso))
                                <img class="ck-phones__flag" src="{{ $ckFlagUrl($iso) }}" width="20" height="14" alt="" loading="lazy">
                            @endif
                            <span class="ck-phones__iso">{{ $iso }}</span>
                            <span class="ck-phones__num">{{ __('text.phones_title_phone_' . $idPhone) }}</span>
                        </a>
                    @endforeach
                </div>
            </details>
        @endif
    </div>
</div>
