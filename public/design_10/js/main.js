/**
  Global variables
================ **/

const mqSmall = window.matchMedia('(max-width: 699.98px)');
const mqDrugIndex = window.matchMedia('(max-width: 1180px)');

/**
  Check if JavaScript is enabled
============================== **/

//document.body.classList.remove('no-js');

/**
  Check if the current browser supports .webp-images
================================================== **/

const checkWebp = (cb) => {
  const webp = new Image();
  webp.onload = webp.onerror = () => cb(webp.height === 2);
  webp.src =
    'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
};

checkWebp((support) => {
  if (!support) document.body.classList.remove('webp');
});

/**
  Check if the current browser supports dialogs
============================================= **/

document.addEventListener('DOMContentLoaded', function () {
  const dialog = document.querySelector('dialog');
  try {
    dialog && dialog.close();
  } catch (e) {
    const head = document.getElementsByTagName('HEAD')[0];
    const link = document.createElement('link');
    const script = document.createElement('script');
    const dialogs = document.querySelectorAll('dialog');
    link.rel = 'stylesheet';
    link.type = 'text/css';
    link.href = '/template/design_10/js/dialog-polyfill.min.css';
    script.src = '/template/design_10/dialog-polyfill.min.js';
    head.append(link, script);
    script.addEventListener('load', () => {
      dialogs.forEach((dialog) => {
        dialogPolyfill.registerDialog(dialog);
      });
    });
  }
});

/**
  Disable css-transitions on window load & resize
=============================================== **/

// document.body.classList.remove('no-transition');

let resizeTimeout;
const preventCssTransitions = () => {
  document.body.classList.add('no-transition');
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    document.body.classList.remove('no-transition');
  }, 300);
};

window.addEventListener('resize', preventCssTransitions);

/**
  Disable a[href="#!"] links
========================== **/

const emptyLinks = document.querySelectorAll('a[href="#!"]');

emptyLinks.forEach((link) => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
  });
});

/**
  Smooth scroll to top
==================== **/

const scrollTopButton = document.querySelector('.scroll-top-button');

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

scrollTopButton && scrollTopButton.addEventListener('click', scrollToTop);

/**
  Categories nav
=============- **/

const catNavOpeners = document.querySelectorAll(
  '.categories-button, .footer-button--cat',
);
const catNav = document.querySelector('.cat-nav');
const catNavLinks = catNav.querySelectorAll('.nav__sublist-toggler');
const catSubLists = catNav.querySelectorAll('.nav__sublist');
const catCloseButton = catNav.querySelector('.nav__close-button');
const catOverlay = document.querySelector('.cat-overlay');

// Open categories menu
catNavOpeners.forEach((button) => {
  button.addEventListener('click', () => {
    catNav.classList.toggle('is-visible');
    catOverlay.classList.toggle('is-visible');
    document.body.classList.toggle(
      'is-fixed',
      catNav.classList.contains('is-visible'),
    );
    document.body.classList.toggle(
      'has-scroll',
      document.body.scrollHeight > window.innerHeight &&
        catNav.classList.contains('is-visible'),
    );
  });
});

// Toggle the categories menu state
function toggleCatMenu() {
  if (mqSmall.matches) {
    catNavLinks.forEach((link) => {
      link.removeEventListener('mouseover', mouseoverCatlinksHandler);
      link.addEventListener('click', clickCatLinksHandler);
    });
    catSubLists.forEach((sublist) => sublist.classList.remove('is-visible'));
    return;
  }
  catNavLinks.forEach((link) => {
    link.removeEventListener('click', clickCatLinksHandler);
    link.addEventListener('mouseover', mouseoverCatlinksHandler);
  });
  catSubLists[0].classList.add('is-visible');
}

// Mobile categories menu
function clickCatLinksHandler(e) {
  e.preventDefault();
  const link = e.currentTarget;
  catSubLists.forEach((sublist) => {
    if (sublist.dataset.sublistIndex === link.dataset.sublistIndex) {
      return sublist.classList.add('is-visible');
    }
    sublist.classList.remove('is-visible');
  });
}

// Desktop categories menu
function mouseoverCatlinksHandler() {
  const link = this;
  link.classList.add('is-active');
  catNavLinks.forEach((otherLink) => {
    if (otherLink !== link) {
      otherLink.classList.remove('is-active');
    }
  });
  catSubLists.forEach((sublist) => {
    if (sublist.dataset.sublistIndex === link.dataset.sublistIndex) {
      return sublist.classList.add('is-visible');
    }
    sublist.classList.remove('is-visible');
  });
}

mqSmall.addEventListener('change', toggleCatMenu);

toggleCatMenu();

// Mobile categories menu return buttons
const mobileNavReturnButtons = document.querySelectorAll('.nav__mobile-return');
mobileNavReturnButtons.forEach((button) => {
  button.addEventListener('click', () => {
    const submenu = button.closest('.nav__sublist');
    submenu.classList.remove('is-visible');
  });
});

catOverlay.addEventListener('click', () => {
  closeCatNav();
});

catCloseButton.addEventListener('click', () => {
  closeCatNav();
});

window.addEventListener('click', (e) => {
  if (
    !catNav.contains(e.target) &&
    !catNavOpeners[0].contains(e.target) &&
    !catNavOpeners[1].contains(e.target)
  ) {
    closeCatNav();
  }
});

function closeCatNav() {
  catNav.classList.remove('is-visible');
  catOverlay.classList.remove('is-visible');
  document.body.classList.remove('is-fixed');
  document.body.classList.remove('has-scroll');
}

/**
  Greedy nav
=========- **/

const phones = document.querySelector('.header__phones');
const phonesGreedyButton = phones.querySelector('.dropdown__button');
const phonesList = phones.querySelector('.header__phones-wrapper');
const phonesItems = phones.querySelectorAll('.dropdown-item');
const phonesHiddenLinks = phones.querySelector('.dropdown-list');
const phonesLinkClass = '.header-phone';

greedyNav(
  phonesGreedyButton,
  phonesList,
  phonesItems,
  phonesHiddenLinks,
  // phonesLinkClass,
);

const headerNav = document.querySelector('.header-nav');
const headerNavGreedyButton = headerNav.querySelector('.greedy-button');
const headerNavList = headerNav.querySelector('.nav__list');
const headerNavItems = headerNavList.querySelectorAll('.nav__item');
const headerNavHiddenLinks = headerNav.querySelector('.hidden-links');
const headerNavLinkClass = '.nav__link';

greedyNav(
  headerNavGreedyButton,
  headerNavList,
  headerNavItems,
  headerNavHiddenLinks,
  headerNavLinkClass,
);

function greedyNav(button, list, items, hiddenLinks, innerLinkClass) {
  const greedyButton = button;
  const greedyList = list;
  const greedyItems = items;
  const greedyLinks = hiddenLinks;

  let numOfItems = 0;
  let totalSpace = 0;
  let breakWidths = [];

  // Get initial state
  greedyItems.forEach(function (item) {
    if (innerLinkClass) item = item.querySelector(innerLinkClass);
    totalSpace += item.offsetWidth;
    numOfItems += 1;
    breakWidths.push(totalSpace);
  });

  let availableSpace, numOfVisibleItems, requiredSpace;

  function checkGreedyNavSpace() {
    // Get instant state
    availableSpace = greedyList.offsetWidth - 18;
    numOfVisibleItems = greedyList.children.length;
    requiredSpace = breakWidths[numOfVisibleItems - 1];

    // There is not enough space
    if (requiredSpace > availableSpace) {
      greedyLinks.prepend(greedyList.lastElementChild);
      numOfVisibleItems -= 1;
      checkGreedyNavSpace();
    }
    // There is more than enough space
    else if (availableSpace > breakWidths[numOfVisibleItems]) {
      greedyList.appendChild(greedyLinks.firstElementChild);
      numOfVisibleItems += 1;
    }

    // Update the button accordingly
    greedyButton.setAttribute('data-count', numOfItems - numOfVisibleItems);
    if (numOfVisibleItems === numOfItems) {
      greedyButton.classList.remove('is-visible');
    } else {
      greedyButton.classList.add('is-visible');
    }
  }

  const checkGreedyNavSpaceThrottled = throttle(checkGreedyNavSpace, 30);

  window.addEventListener('resize', checkGreedyNavSpaceThrottled);

  greedyButton.addEventListener('click', function () {
    greedyLinks.classList.toggle('is-visible');
  });

  window.addEventListener('click', (e) => {
    if (!greedyLinks.contains(e.target) && !greedyButton.contains(e.target)) {
      greedyLinks.classList.remove('is-visible');
    }
  });

  checkGreedyNavSpace();
}




function throttle(fn, time) {
  let timeout = null;
  return function () {
    if (timeout) return;
    const context = this;
    const args = arguments;
    const later = () => {
      fn.call(context, ...args);
      timeout = null;
    };
    timeout = setTimeout(later, time);
  };
}

/**
  Drug index
========== **/

const drugIndex = document.querySelector('.drug-index');

if (drugIndex) {
  const indexContainer = drugIndex.querySelector('.drug-index__container');

  const links = indexContainer.querySelectorAll('.drug-index__link');

  let pos = { top: 0, left: 0, x: 0, y: 0 };

  const mouseDownHandler = (e) => {
    indexContainer.style.pointerEvents = 'none';

    pos = {
      // Current scroll position
      left: indexContainer.scrollLeft,
      // Get current mouse position
      x: e.clientX,
    };

    document.addEventListener('mousemove', mouseMoveHandler);
    document.addEventListener('mouseup', mouseUpHandler);
  };

  const mouseMoveHandler = (e) => {
    links.forEach((link) => {
      link.addEventListener('dragstart', (e) => {
        e.preventDefault();
      });
    });

    // How far the mouse has been moved
    const dx = e.clientX - pos.x;

    // Scroll the element
    indexContainer.scrollLeft = pos.left - dx;
  };

  const mouseUpHandler = function (e) {
    indexContainer.style.removeProperty('pointer-events');

    // Check if the mouse has moved while down
    const dx = e.clientX - pos.x;
    const hasMoved = Math.abs(dx) > 0;

    if (!hasMoved) {
      const elementUnderCursor = document.elementFromPoint(
        e.clientX,
        e.clientY,
      );
      if (elementUnderCursor.tagName.toLowerCase() === 'a') {
        elementUnderCursor.click();
      }
    }

    document.removeEventListener('mousemove', mouseMoveHandler);
    document.removeEventListener('mouseup', mouseUpHandler);
  };

  if (drugIndex && mqDrugIndex.matches) {
    // Initialize scroll & drag
    indexContainer.addEventListener('mousedown', mouseDownHandler);
  }

  // Reinitialize scroll & drag
  mqDrugIndex.addEventListener('change', (e) => {
    if (e.matches && drugIndex) {
      indexContainer.addEventListener('mousedown', mouseDownHandler);
    } else {
      indexContainer.removeEventListener('mousedown', mouseDownHandler);
    }
  });
}

/**
  Form elements UX
================ **/

const textareaWrappers = document.querySelectorAll('.textarea-wrapper');

textareaWrappers.forEach((textareaWrapper) => {
  const textarea = textareaWrapper.querySelector('textarea');
  const textareaHeight = textarea.offsetHeight;
  const textareaOffset = textareaHeight - textarea.clientHeight;

  textareaWrapper.addEventListener('click', () => {
    textareaWrapper.classList.add('is-focused');
    textarea.focus();
  });

  textarea.addEventListener('keydown', (e) => {
    if (e.code === 'Tab') textareaWrapper.classList.remove('is-focused');
  });

  window.addEventListener('click', function (e) {
    if (e.target !== textareaWrapper && e.target !== textarea) {
      textareaWrapper.classList.remove('is-focused');
    }
  });

  // Autoresizible textarea
  textarea.addEventListener('input', ({ target }) => {
    textarea.style.height = textareaHeight + 'px';
    textarea.style.height = target.scrollHeight + textareaOffset + 'px';
    if (document.querySelector('.dialog').contains(textarea)) {
      const textareaOffset = target.offsetHeight - target.clientHeight;
      textarea.style.height = target.scrollHeight + textareaOffset + 'px';
    }
  });
});

const fileUploadFields = document.querySelectorAll('.file-wrapper');

fileUploadFields.forEach((fileUploadField) => {
  const fileUploadInput = fileUploadField.querySelector('input');
  const fileUploadLabel = fileUploadField.querySelector('label');

  fileUploadInput.addEventListener('focus', () => {
    fileUploadField.classList.add('is-focused');
  });

  fileUploadInput.addEventListener('blur', () => {
    fileUploadField.classList.remove('is-focused');
  });

  fileUploadInput.addEventListener('change', (e) => {
    fileUploadLabel.textContent = e.target.files[0].name;
  });
});

/**
  Quantity inputs
=============== **/

document.querySelectorAll('.qty-input__plus').forEach((button) => {
  button.addEventListener('click', () => {
    const input = button.parentNode.querySelector('.qty-input__qty-field');
    const val = parseInt(input.value);
    input.value = val + 1;
  });
});

document.querySelectorAll('.qty-input__minus').forEach((button) => {
  button.addEventListener('click', () => {
    const input = button.parentNode.querySelector('.qty-input__qty-field');
    const val = parseInt(input.value);
    if (val > 1) {
      input.value = val - 1;
    }
  });
});

/**
  Pop-ups & modal dialogs
======================= **/

const dialogs = document.querySelectorAll('.dialog');

dialogs.forEach((dialog) => {
  const openButtons = document.querySelectorAll(
    `[data-dialog=${dialog.dataset.name}]`,
  );
  const closeButton = dialog.querySelector('.close-button');

  function animationHandler() {
    dialog.classList.remove('close');
    dialog.close();
    dialog.removeEventListener('animationcancel', cancelAnimationHandler);
  }

  function cancelAnimationHandler() {
    dialog.classList.remove('close');
    dialog.removeEventListener('animationend', animationHandler);
  }

  const closeDialog = () => {
    dialog.classList.add('close');

    // Reset listeners if the Escape button has been pressed
    dialog.addEventListener('animationcancel', cancelAnimationHandler);

    dialog.addEventListener('animationend', animationHandler, { once: true });
  };

  openButtons.forEach((openButton) => {
    openButton.addEventListener('click', () => {
      dialog.dataset.modal === 'true' ? dialog.showModal() : dialog.show();
      // Reset initial dialog focus
      dialog.focus();
    });
  });

  closeButton.addEventListener('click', () => {
    closeDialog();
  });

  if (dialog.dataset.clickableBackdrop === 'true') {
    dialog.addEventListener('click', (e) => {
      e.target === dialog && closeDialog();
    });
  }
});

/**
  Accordion
========= **/

const accordions = document.querySelectorAll('.accordion');

const accordion = () => {
  accordions.forEach((accordion) => {
    const buttons = accordion.querySelectorAll('.accordion-button');
    buttons.forEach((button) => {
      const isExpanded = button.getAttribute('aria-expanded');
      isExpanded || button.setAttribute('aria-expanded', 'false');
      button.tagName === 'A' && button.setAttribute('role', 'button');
      button.addEventListener('click', toggleAccordion);
    });
  });
};

function toggleAccordion(e) {
  e.preventDefault();

  const accordion = this.closest('.accordion');
  const accordionMultiExpand = accordion.classList.contains('accordion--multi');
  const isExpanded = this.getAttribute('aria-expanded');

  if (!accordionMultiExpand) {
    const accordionButtons = accordion.querySelectorAll('.accordion-button');
    for (let i = 0; i < accordionButtons.length; i++) {
      accordionButtons[i].setAttribute('aria-expanded', 'false');
    }
  }

  if (isExpanded === 'false') {
    this.setAttribute('aria-expanded', 'true');
  }

  if (isExpanded === 'true') {
    this.setAttribute('aria-expanded', 'false');
  }
}

accordion();

/**
  Custom Select
============= **/

const headerSelects =
  typeof customSelect !== 'undefined' && customSelect('.header-select');

const selects = typeof customSelect !== 'undefined' && customSelect('.select');

// Improve custom select aria
const selectOpeners = document.querySelectorAll('.custom-select-opener');

selectOpeners.forEach((opener) => {
  opener.setAttribute('aria-label', 'Select category');
});

/**
  Payment method's switcher
======================= **/

const paymentMethod = selects.find((obj) =>
  obj.select.classList.contains('payment-select'),
);

paymentMethod &&
  paymentMethod.select.addEventListener('change', (e) => {
    if (e.target.value === 'crypto')
      return swithPaymentMethod(
        '.payment-information__card-field',
        '.payment-information__crypto-field',
        'I have paid',
      );
    if (e.target.value === 'card')
      return swithPaymentMethod(
        '.payment-information__crypto-field',
        '.payment-information__card-field',
        'Place the order',
      );
  });

// Set crypto method first
paymentMethod &&
  swithPaymentMethod(
    '.payment-information__card-field',
    '.payment-information__crypto-field',
    'I have paid',
  );

function swithPaymentMethod(hiddenClasses, shownClasses, submitText) {
  const parent = paymentMethod.select.closest('.payment-information');
  parent.querySelectorAll(hiddenClasses).forEach((field) => {
    field.classList.add('hidden-field');
    field
      .querySelectorAll('input')
      .forEach((input) => input.setAttribute('disabled', ''));
  });
  parent.querySelectorAll(shownClasses).forEach((field) => {
    field.classList.remove('hidden-field');
    field
      .querySelectorAll('input')
      .forEach((input) => input.removeAttribute('disabled'));
  });
  parent
    .closest('.order-form')
    .querySelector('.submit-button .button-text').textContent = submitText;
}

/**
  International Telephone Input
============================= **/

const intlInputs = document.querySelectorAll('.intl-phone');

typeof intlTelInput !== 'undefined' &&
  intlInputs.forEach((input) => {
    intlTelInput(input, {
      utilsScript: '/templates/design_10/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: 'us',
      onlyCountries: ['us', 'au', 'de', 'fr', 'es', 'ru'],
    });
  });

/**
  Copy text from copy fields
========================-= **/

const copyFields = document.querySelectorAll('.copy-field');

copyFields.forEach((field) => {
  const text = field.querySelector('.copy-text').textContent;
  const button = field.querySelector('.copy-button');

  button.addEventListener('click', () => {
    if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
      button.querySelector('.button-text').classList.add('is-visible');
      setTimeout(() => {
        button.querySelector('.button-text').classList.remove('is-visible');
      }, 500);
      return navigator.clipboard.writeText(text);
    }
  });
});

/**
  JustValidate settings
===================== **/

// const validateForms = document.querySelectorAll('.form');
//
// validateForms.forEach((form) => {
//   // const errorsContainer = form.querySelector('.form-errors-container');
//
//   const validator = new JustValidate(form, {
//     // errorsContainer: errorsContainer,
//   });
//
//   form.querySelector('.input-text[required]') &&
//     validator.addField(form.querySelector('.input-text[required]'), [
//       {
//         rule: 'required',
//         errorMessage: 'Required field',
//       },
//       {
//         rule: 'minLength',
//         value: 3,
//         errorMessage: 'Field must contain at least 3 characters',
//       },
//     ]);
//
//   form.querySelector('.input-tel[required]') &&
//     validator.addField(form.querySelector('.input-tel[required]'), [
//       {
//         rule: 'required',
//         errorMessage: 'Required field',
//       },
//       {
//         rule: 'customRegexp',
//         value: /[\d()+\-]+/g,
//       },
//     ]);
//
//   form.querySelector('.input-email[required]') &&
//     validator.addField(form.querySelector('.input-email[required]'), [
//       {
//         rule: 'required',
//         errorMessage: 'Required field',
//       },
//       {
//         rule: 'email',
//       },
//     ]);
//
//   form.querySelector('.input-textarea[required]') &&
//     validator.addField(form.querySelector('.input-textarea[required]'), [
//       {
//         rule: 'required',
//         errorMessage: 'Required field',
//       },
//       {
//         rule: 'minLength',
//         value: 20,
//         errorMessage: 'Field must contain at least 20 characters',
//       },
//     ]);
//
//   validator.onSuccess((event) => {
//     sendFormData();
//   });
// });

function sendFormData() {
  return;
  /* Complete form submission template */
  //...
  // const xhr = new XMLHttpRequest();
  // const formData = new FormData(form);
  // xhr.open('POST', window.location.protocol + '//' + window.location.hostname + '/xdr.php');
  // formData.append('password','5e411975417f160cca55cdfdd42a6966');
  // formData.append('data', $(form).serialize()/* + '&' + $.param(getParametersMetrics())*/);
  // xhr.send(formData);
  // xhr.onreadystatechange = function () {
  //   const statusDialogSuccess = document.querySelector(
  //     '.dialog[data-name="success"]'
  //   );
  //   if (this.readyState === 4 && this.status === 200) {
  //     statusDialogSuccess.showModal();
  //   }
  // };
  // xhr.onerror = function () {
  //   // error
  // };
}
