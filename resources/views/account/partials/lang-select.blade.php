{{--
    Селект языка 1:1 с customerportal:
    assets/Components/LanguageSelect.vue + AppSelect.vue (header/login вариант).
    Без floating label (портал его не передаёт), подпись — нативное имя языка,
    иконка arr-down из спрайта, _drop-up + CustomScrollbar как в AppSelect.
    Требует: $cabLocales (code => native name), $cabCurrentLocale.
--}}
<div class="select" x-data="cabinetLangSelect()" x-bind:class="{ '_select-open': langOpen, '_drop-up': langOpen && dropUp }" @keydown.escape.window="close()">
    <div class="select__body" x-ref="body">
        <button
            type="button"
            class="select__title"
            aria-haspopup="listbox"
            aria-label="Language"
            x-bind:aria-expanded="String(langOpen)"
            @click="toggle()"
        >
            <span class="select__value">
                <span class="select__text">{{ $cabLocales[$cabCurrentLocale] ?? $cabCurrentLocale }}</span>
                <span class="select__icon" aria-hidden="true">
                    <svg width="16" height="16" aria-hidden="true"><use href="{{ asset('cabinet/icons.svg') }}#svg-arr-down" /></svg>
                </span>
            </span>
        </button>
        <div class="select__options" x-ref="panel" x-show="langOpen" x-cloak @click.outside="close()">
            <div class="select__scroll" role="listbox" aria-label="Language" x-ref="scroll">
                @foreach ($cabLocales as $cabCode => $cabLabel)
                    <a
                        href="{{ route('home.language', ['locale' => $cabCode]) }}"
                        role="option"
                        aria-selected="{{ $cabCode === $cabCurrentLocale ? 'true' : 'false' }}"
                        class="select__option @if ($cabCode === $cabCurrentLocale) _select-selected @endif"
                    ><span class="select__option-label">{{ $cabLabel }}</span></a>
                @endforeach
            </div>
        </div>
    </div>
</div>
