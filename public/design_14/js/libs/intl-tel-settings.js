/* International Telephone Input settings */
export function initIntlTel() {
  if (typeof intlTelInput === 'undefined') return;

  const callbackBtn = document.querySelector('.link[data-dialog-open="call"]');
  const callbackDialog = document.querySelector('.dialog-container[data-dialog="call"]');

  callbackBtn &&
    callbackBtn.addEventListener('click', initIntlTelInput, {
      once: true,
    });

  function initIntlTelInput() {
    const callbackIntlTelInput = callbackDialog.querySelector('.intl-phone');
    intlTelInput(callbackIntlTelInput, {
      utilsScript: './vendor/intl-tel/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: 'ru',
      onlyCountries: ['us', 'au', 'de', 'fr', 'es', 'ru'],
    });
  }

  const intlInputs = document.querySelectorAll('form:not(.callback-form) .intl-phone');

  intlInputs.forEach((input) => {
    intlTelInput(input, {
      utilsScript: './vendor/intl-tel/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: 'ru',
      onlyCountries: ['us', 'au', 'de', 'fr', 'es', 'ru'],
    });
  });
}
