{{--
    Shared: инфо-блок + ConfirmDialog страницы "Contact us"
    (customerportal Contact.vue, авторизованная ветка). Диалог на CSS-тогле,
    открывается кнопкой отправки (id="cabp-contact-confirm").
--}}
<aside class="cabp-side">
    <div class="cabp-help">
        <p>{{ __('text.cabinet_contact_info_1') }}</p>
        <p>{{ __('text.cabinet_contact_info_2') }}</p>
        <p>{{ __('text.cabinet_contact_info_3') }}</p>
    </div>
</aside>

<span class="cabp-confirm">
    <input class="cabp-check" type="checkbox" id="cabp-contact-confirm">
    <label class="cabp-confirm__overlay" for="cabp-contact-confirm" aria-hidden="true"></label>
    <span class="cabp-confirm__dialog" role="alertdialog" aria-modal="true">
        <span class="cabp-confirm__head">
            <span class="cabp-confirm__title">{{ __('text.cabinet_contact_confirm_title') }}</span>
            <p class="cabp-confirm__description">{{ __('text.cabinet_contact_confirm_text') }}</p>
        </span>
        <span class="cabp-confirm__footer">
            <label class="button cabp-action cabp-action--light" for="cabp-contact-confirm"><span>{{ __('text.cabinet_cancel') }}</span></label>
            <span class="button cabp-action cabp-action--dark"><span>{{ __('text.cabinet_continue') }}</span></span>
        </span>
    </span>
</span>
