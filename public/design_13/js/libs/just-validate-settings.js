/* Just validate settings */
export function initJustValidate() {
  if (typeof JustValidate === 'undefined') return;

  const forms = document.querySelectorAll('.form:not(.form--native-submit)');

  forms.forEach((form) => {
    const validator = new JustValidate(form, {
      errorLabelStyle: {
        // Remove default inline styling
        color: false,
      },
      errorFieldCssClass: 'error-field',
      errorLabelCssClass: 'error-label',
    });

    /* Text fields */
    const textFields = form.querySelectorAll('.input-text[required]');
    const strictTextFieldValidation = true;
    const strictTextFieldValidationRuleset = {
      rule: 'customRegexp',
      errorMessage: 'Incorrect input format',
      // Latin & russian letters, numbers, space, hyphens
      value: /^[A-Za-z0-9А-Яа-яЁё\s\-–—]+$/gi,
    };

    textFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
        ...(strictTextFieldValidation ? [strictTextFieldValidationRuleset] : []),
        {
          rule: 'minLength',
          errorMessage: 'Minimum 2 characters',
          value: 2,
        },
        {
          rule: 'maxLength',
          errorMessage: 'Maximum 30 characters',
          value: 30,
        },
      ]);
    });

    /* Textarea fields */
    const textareaFields = form.querySelectorAll('.input-textarea[required]');
    const strictTextareaValidation = false;
    const strictTextareaValidationRuleset = {
      rule: 'customRegexp',
      errorMessage: 'Incorrect input format',
      // Latin & russian letters, numbers, space,
      // general punctuation marks, quotes
      value: /^[A-Za-z0-9А-Яа-яЁё\s\-_.,!?;:'"«»“”‘’()]+$/gi,
    };

    textareaFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
        ...(strictTextareaValidation ? [strictTextareaValidationRuleset] : []),
        {
          rule: 'minLength',
          errorMessage: 'Minimum 5 characters',
          value: 5,
        },
        {
          rule: 'maxLength',
          errorMessage: 'Maximum 2000 characters',
          value: 2000,
        },
      ]);
    });

    /* Email fields */
    const emailFields = form.querySelectorAll('.input-email[required]');

    emailFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
        {
          rule: 'email',
          errorMessage: 'Invalid email format',
        },
      ]);
    });

    /* Phone number fields */
    const phoneFields = form.querySelectorAll('.input-tel[required]');
    // 11 digit + iMask symbols format
    const iMaskPhoneRegex = /^(?:\+7|(?:\D*\d){11}\D*)$/gi;
    // Russian phone number formats + simple 7 digit format
    const russianPhoneRegex =
      /^(?:\+?7|8)?[\s-]?(?:\(?(?:9\d{2}|4[6-9]\d|5\d{2}|3[0-8]\d|82\d|8[1-9][0-9])\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}|\d{3}[\s-]?\d{2}[\s-]?\d{2})$/gi;
    // Simplified phone number format
    const simplifiedPhoneRegex = /^(?:\D*\d){7,10}\D*$/gi;

    phoneFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
        {
          rule: 'customRegexp',
          errorMessage: 'Invalid phone number',
          value: simplifiedPhoneRegex,
        },
      ]);
    });

    /* Password fields */
    const passwordFields = form.querySelectorAll('.input-password[required]');

    passwordFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
        {
          rule: 'minLength',
          errorMessage: 'Minimum 8 characters',
          value: 8,
        },
      ]);
    });

    /* Checkboxes */
    const checkboxes = form.querySelectorAll('.input-checkbox[required]');

    checkboxes.forEach((checkbox) => {
      validator.addField(checkbox, [
        {
          rule: 'required',
          errorMessage: 'Required field',
        },
      ]);
    });

    /* Radio groups */
    const radioGroups = form.querySelectorAll('.radio-fieldset--required');

    radioGroups.forEach((radioGroup) => {
      validator.addRequiredGroup(radioGroup, 'Select an option');
    });

    /* Selects */
    const selects = form.querySelectorAll('select[required]');

    selects.forEach((select) => {
      validator.addField(select, [
        {
          rule: 'required',
          errorMessage: 'Select an option',
        },
      ]);
    });

    validator.onFail((e) => {
      // Error logic goes here
    });

    validator.onSuccess((e) => {
      closeDialog(form);
      // Success logic goes here

      /* 
        Example: redirect to success page upon form submission 
      */
      // const rootLocation = 'https://domen.name';
      // const successPage = 'success.html';
      // window.location.href = rootLocation + successPage;
      /* 
        Example: show status dialog upon form submission
      */
      // const xhr = new XMLHttpRequest();
      // const formData = new FormData(form);
      // xhr.open('POST', form.getAttribute('action'));
      // xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      // xhr.send(formData);
      // xhr.onreadystatechange = function () {
      //   const statusDialogSuccess = document.querySelector('dialog[data-dialog="success"]');
      //   // Form send successfully
      //   if (this.readyState === 4 && this.status === 200) {
      //     statusDialogSuccess.showModal();
      //   }
      // };
      // xhr.onerror = function () {
      //   const statusDialogError = document.querySelector('dialog[data-dialog="error"]');
      //   statusDialogError.showModal();
      // };
    });
  });
}

function closeDialog(form) {
  if (form.method !== 'dialog') return;
  const dialog = form.closest('dialog')._instance;
  dialog.close();
}
