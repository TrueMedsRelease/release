/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./js/components/drag-nav.js":
/*!***************************************!*\
  !*** ./src/js/components/drag-nav.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ DragNav)
/* harmony export */ });
/* Draggable navigation component */
class DragNav {
  constructor(selector, options) {
    if (typeof selector === 'string') {
      const elements = document.querySelectorAll(selector);
      if (elements.length > 1) {
        elements.forEach((element) => new DragNav(element, options));
        return;
      }
      selector = elements[0];
    }

    if (!selector) {
      return console.error('Drag Nav: Selector not found.');
    }

    this.container = selector.querySelector('.drag-nav-container');
    this.items = selector.querySelectorAll('a, div');

    this.startX = null;
    this.scrollLeft = null;

    this.mouseMoveHandler = this.mouseMoveHandler.bind(this);
    this.mouseUpHandler = this.mouseUpHandler.bind(this);

    this.events();
  }

  events() {
    this.container.addEventListener('mousedown', (e) => {
      this.startX = e.pageX - this.container.offsetLeft;
      this.scrollLeft = this.container.scrollLeft;

      document.addEventListener('mousemove', this.mouseMoveHandler);
      document.addEventListener('mouseup', this.mouseUpHandler);
    });
  }

  mouseMoveHandler(e) {
    // Disable default browser drag & drop behavior
    this.items.forEach((link) => {
      link.addEventListener('dragstart', (e) => {
        e.preventDefault();
      });
    });
    const x = e.pageX - this.container.offsetLeft;
    const distance = (x - this.startX) * 1;
    // Disable pointer events on links while dragging
    if (Math.abs(distance) > 10) {
      this.container.classList.add('is-dragging');
    }
    this.container.scrollLeft = this.scrollLeft - distance;
  }

  mouseUpHandler() {
    this.container.classList.remove('is-dragging');
    document.removeEventListener('mousemove', this.mouseMoveHandler);
    document.removeEventListener('mouseup', this.mouseUpHandler);
  }
}


/***/ }),

/***/ "./js/components/dropdown.js":
/*!***************************************!*\
  !*** ./src/js/components/dropdown.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Dropdown)
/* harmony export */ });
/* Dropdown component */
class Dropdown {
  constructor(selector, options) {
    if (typeof selector === 'string') {
      const elements = document.querySelectorAll(selector);
      if (elements.length > 1) {
        elements.forEach((element) => new Dropdown(element, options));
        return;
      }
      selector = elements[0];
    }

    if (!selector) {
      return console.error('Dropdown: Selector not found.');
    }

    const defaultOptions = {
      button: '.dropdown-toggler',
      container: '.dropdown-container',
    };

    this.options = Object.assign(defaultOptions, options);

    this.dropdown = selector;
    this.button = selector.querySelector(this.options.button);
    this.container = selector.querySelector(this.options.container);
    this.containerClone = null;

    this.events();
  }

  events() {
    this.button.addEventListener('click', () => {
      this.handleDropdown();
    });

    window.addEventListener('click', (e) => {
      const container = this.containerClone ? this.containerClone : this.container;
      if (!this.button.contains(e.target) && !container.contains(e.target)) {
        this.closeDropdown();
      }
    });

    window.addEventListener('resize', () => this.closeDropdown());
  }

  handleDropdown() {
    // Create a clone of the dropdown container on the body level if needed
    if (this.dropdown.hasAttribute('data-fixed-dropdown')) {
      if (this.containerClone) {
        return this.closeDropdown();
      }
      this.cloneDropdownContainer();
      this.button.classList.add('is-active');
      return;
    }
    // Toggle visibility of the dropdown container directly
    this.container.classList.toggle('is-visible');
    this.button.classList.toggle('is-active');
  }

  cloneDropdownContainer() {
    const { top, left } = this.calculatePosition();
    this.containerClone = this.container.cloneNode(true);
    this.containerClone.style.position = 'fixed';
    document.body.appendChild(this.containerClone);

    const adjustedPosition = this.adjustPositionForEdges({ top, left }, this.containerClone);
    this.containerClone.style.top = `${adjustedPosition.top}px`;
    this.containerClone.style.left = `${adjustedPosition.left}px`;
    this.containerClone.classList.add('is-visible');
  }

  calculatePosition() {
    const buttonRect = this.button.getBoundingClientRect();
    const containerWidth = this.container.offsetWidth;
    const left = buttonRect.left + buttonRect.width / 2 - containerWidth / 2;
    const top = buttonRect.bottom;

    return { top, left };
  }

  adjustPositionForEdges(position, element) {
    const { top, left } = position;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    const elementWidth = element.offsetWidth;
    const elementHeight = element.offsetHeight;

    let adjustedLeft = left;
    let adjustedTop = top;

    // Adjust horizontally if out of bounds
    if (left < 0) {
      adjustedLeft = 10;
    } else if (left + elementWidth > viewportWidth) {
      adjustedLeft = viewportWidth - elementWidth - 10;
    }

    // Adjust vertically if out of bounds
    if (top + elementHeight > viewportHeight) {
      adjustedTop = top - elementHeight;
      if (adjustedTop < 0) adjustedTop = 10;
    }

    return { top: adjustedTop, left: adjustedLeft };
  }

  positionDropdown(element) {
    const { top, left } = this.calculatePosition();
    const adjustedPosition = this.adjustPositionForEdges({ top, left }, element);
    element.style.top = `${adjustedPosition.top}px`;
    element.style.left = `${adjustedPosition.left}px`;
  }

  closeDropdown() {
    if (this.containerClone) {
      document.body.removeChild(this.containerClone);
      this.containerClone = null;
    }
    this.button.classList.remove('is-active');
    this.container.classList.remove('is-visible');
  }
}


/***/ }),

/***/ "./js/components/greedy-nav.js":
/*!*****************************************!*\
  !*** ./src/js/components/greedy-nav.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ GreedyNav)
/* harmony export */ });
/* Greedy navigation component */
/* Dinamically calculates total width of items and shows/hides them when needed */
class GreedyNav {
  constructor(selector, options) {
    if (typeof selector === 'string') {
      const elements = document.querySelectorAll(selector);
      if (elements.length > 2) {
        elements.forEach((element) => new GreedyNav(element, options));
        return;
      }
      selector = elements[0];
    }

    if (!selector) {
      return console.error('Greedy Nav: Selector not found.');
    }

    const defaultOptions = {
      visibleItemsContainer: '.greedy-items',
      item: '.greedy-item',
      button: '.greedy-button',
      buttonPlaceholder: 4,
      hiddenItemsContainer: '.greedy-hidden-items',
    };

    this.options = Object.assign(defaultOptions, options);
    this.container = selector;

    this.visibleItemsContainer = this.container.querySelector(this.options.visibleItemsContainer);
    this.items = this.container.querySelectorAll(this.options.item);
    this.button = this.container.querySelector(this.options.button);
    this.hiddenItemsContainer = this.container.querySelector(this.options.hiddenItemsContainer);

    this.availableSpace = 0;
    this.totalItems = 0;
    this.totalOccupiedSpace = 0;
    this.itemBreakpoints = [];
    this.placeholderNeeded = false;

    this.init();
    this.events();
  }

  init() {
    this.items.forEach((item) => {
      this.totalItems += 1;
      this.totalOccupiedSpace += item.offsetWidth;
      this.itemBreakpoints.push(this.totalOccupiedSpace);
    });

    this.checkAvailableSpace();
  }

  checkAvailableSpace() {
    while (true) {
      this.availableSpace = this.container.offsetWidth - (this.placeholderNeeded ? this.options.buttonPlaceholder : 0);
      this.sumOfVisibleItems = this.visibleItemsContainer.children.length;
      this.requiredSpace = this.itemBreakpoints[this.sumOfVisibleItems - 1];

      // Not enough space for visible items - hide one and add button placeholder
      if (this.requiredSpace > this.availableSpace && this.sumOfVisibleItems > 0) {
        this.hiddenItemsContainer.prepend(this.visibleItemsContainer.lastElementChild);
        this.sumOfVisibleItems -= 1;
        this.placeholderNeeded = true;
      }
      // All hidden items can fit without the placeholder
      else if (
        this.itemBreakpoints[this.totalItems - 1] <= this.container.offsetWidth &&
        this.hiddenItemsContainer.children.length > 0
      ) {
        this.visibleItemsContainer.appendChild(this.hiddenItemsContainer.firstElementChild);
        this.sumOfVisibleItems += 1;
        this.placeholderNeeded = false;
      }
      // Enough space for the next hidden item
      else if (
        this.availableSpace >= this.itemBreakpoints[this.sumOfVisibleItems] &&
        this.hiddenItemsContainer.children.length > 0
      ) {
        this.visibleItemsContainer.appendChild(this.hiddenItemsContainer.firstElementChild);
        this.sumOfVisibleItems += 1;
      }
      // No more adjustments needed, break the loop
      else {
        break;
      }
    }
    // Show or hide button when needed
    this.updateButtonState();
  }

  updateButtonState() {
    this.button.setAttribute('data-counter', this.totalItems - this.sumOfVisibleItems);
    if (this.sumOfVisibleItems === this.totalItems) {
      this.button.classList.remove('is-visible');
    } else {
      this.button.classList.add('is-visible');
    }
  }

  events() {
    window.addEventListener('resize', this.debounce(this.checkAvailableSpace.bind(this), 500));
  }

  debounce(fn, time) {
    let timeout;
    return function () {
      clearTimeout(timeout);
      const context = this;
      const args = arguments;
      timeout = setTimeout(() => fn.apply(context, args), time);
    };
  }
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************!*\
  !*** ./src/js/main.js ***!
  \************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _components_dropdown__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/dropdown */ "./js/components/dropdown.js");
/* harmony import */ var _components_greedy_nav__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/greedy-nav */ "./js/components/greedy-nav.js");
/* harmony import */ var _components_drag_nav__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/drag-nav */ "./js/components/drag-nav.js");
/**
  Global variables
================ **/
const mqSmall = window.matchMedia('(max-width: 829.98px)');

/**
  Check if JavaScript is enabled
============================== **/

document.body.classList.remove('no-js');

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
  Disable css-transitions on window load & resize
=============================================== **/

document.body.classList.remove('no-transition');

let resizeTimeout;
window.addEventListener('resize', () => {
  if (!document.body.classList.contains('no-transition')) {
    document.body.classList.add('no-transition');
  }

  // Enable transitions after resizing is finished
  clearTimeout(resizeTimeout);
  resizeTimeout = setTimeout(() => {
    document.body.classList.remove('no-transition');
  }, 300);
});

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
  Form elements UX
================ **/

/* Autoresizible textarea */

const textareaFields = document.querySelectorAll('.textarea-field');

textareaFields.forEach((field) => {
  const textarea = field.querySelector('.input-textarea');
  const initialHeight = textarea.offsetHeight;
  const initialOffset = initialHeight - textarea.clientHeight;

  textarea.addEventListener('input', () => {
    textarea.style.height = initialHeight + 'px';
    textarea.style.height = textarea.closest('.dialog')
      ? textarea.scrollHeight + initialOffset + 2 + 'px'
      : textarea.scrollHeight + initialOffset + 'px';
    // Fix clumsy textarea scroll
    textarea.scrollTo(0, textarea.scrollHeight);
  });
});

/* Quantity inputs */

document.querySelectorAll('.qty-input__plus').forEach((button) => {
  button.addEventListener('click', () => {
    const input = button.parentNode.querySelector('.qty-input__qty-field');
    let val = parseInt(input.value);
    isNaN(val) && (val = 0);
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

/* Show uploaded file name */

const fileUploadFields = document.querySelectorAll('.file-field');

fileUploadFields.forEach((field) => {
  const input = field.querySelector('.input-file');
  const label = field.querySelector('.label-file');
  const labelText = label.textContent;

  input.addEventListener('change', (e) => {
    label.textContent = e.target.files.length > 0 ? e.target.files[0].name : labelText;
  });
});

/* Password visibility toggler */

const passwordTogglers = document.querySelectorAll('.password-toggler');

passwordTogglers.forEach((toggler) => {
  toggler.addEventListener('click', () => {
    const input = toggler.nextSibling;
    toggler.classList.toggle('is-active');
    input.type = input.type === 'password' ? 'text' : 'password';
  });
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
    link.href = './vendor/dialog-polyfill/dialog-polyfill.min.css';
    script.src = './vendor/dialog-polyfill/dialog-polyfill.min.js';
    head.append(link, script);
    script.addEventListener('load', () => {
      dialogs.forEach((dialog) => {
        dialogPolyfill.registerDialog(dialog);
      });
    });
  }
});

/**
  Pop-ups & modal dialogs
======================= **/

const dialogs = document.querySelectorAll('.dialog-container');

dialogs.forEach((dialog) => {
  const openButtons = document.querySelectorAll(`[data-dialog=${dialog.dataset.name}]`);
  const closeButton = dialog.querySelector('.dialog__close-button');

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
    console.log('close');
  });

  if (dialog.dataset.clickableBackdrop === 'true') {
    dialog.addEventListener('click', (e) => {
      e.target === dialog && closeDialog();
    });
  }
});

/**
  Dropdown
======== **/

const dropdown = new _components_dropdown__WEBPACK_IMPORTED_MODULE_0__["default"]('.dropdown');

/**
  Greedy nav
========== **/

const greedyNav = new _components_greedy_nav__WEBPACK_IMPORTED_MODULE_1__["default"]('.greedy-nav');

/**
  Draggable nav
============= **/

const dragNav = new _components_drag_nav__WEBPACK_IMPORTED_MODULE_2__["default"]('.drag-nav');

/**
  Categories nav
=============- **/
const catNavOpeners = document.querySelectorAll('.categories-button, .footer-button--cat');
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
    catNavOpeners.forEach((button) => {
      button.classList.toggle('is-active');
    });
    document.body.classList.toggle('is-fixed', catNav.classList.contains('is-visible'));
    document.body.classList.toggle(
      'has-scroll',
      document.body.scrollHeight > window.innerHeight && catNav.classList.contains('is-visible'),
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
  if (!catNav.contains(e.target) && !catNavOpeners[0].contains(e.target) && !catNavOpeners[1].contains(e.target)) {
    closeCatNav();
  }
});

function closeCatNav() {
  catNav.classList.remove('is-visible');
  catOverlay.classList.remove('is-visible');
  catNavOpeners.forEach((button) => {
    button.classList.remove('is-active');
  });
  document.body.classList.remove('is-fixed');
  document.body.classList.remove('has-scroll');
}

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
    //   accordionButtons[i].setAttribute('aria-expanded', 'false');
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

const headerSelects = typeof customSelect !== 'undefined' && customSelect('.header-select');

const selects = typeof customSelect !== 'undefined' && customSelect('.select');

// Improve custom select aria
const selectOpeners = document.querySelectorAll('.custom-select-opener');

selectOpeners.forEach((opener) => {
  opener.setAttribute('aria-label', 'Select category');
});

/**
  Payment method's switcher
======================= **/

const paymentMethod = selects.find((obj) => obj.select.classList.contains('payment-select'));

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
  swithPaymentMethod('.payment-information__card-field', '.payment-information__crypto-field', 'I have paid');

function swithPaymentMethod(hiddenClasses, shownClasses, submitText) {
  const parent = paymentMethod.select.closest('.payment-information');
  parent.querySelectorAll(hiddenClasses).forEach((field) => {
    field.classList.add('hidden-field');
    field.querySelectorAll('input').forEach((input) => input.setAttribute('disabled', ''));
  });
  parent.querySelectorAll(shownClasses).forEach((field) => {
    field.classList.remove('hidden-field');
    field.querySelectorAll('input').forEach((input) => input.removeAttribute('disabled'));
  });
  parent.closest('.order-form').querySelector('.submit-button').textContent = submitText;
}

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
      }, 1500);
      return navigator.clipboard.writeText(text);
    }
  });
});

/**
  International Telephone Input
============================= **/

const callbackBtn = document.querySelector('.link[data-dialog="call"]');
const callbackDialog = document.querySelector('.dialog-container[data-name="call"]');

callbackBtn &&
  callbackBtn.addEventListener('click', initIntlTelInput, {
    once: true,
  });

function initIntlTelInput() {
  const callbackIntlTelInput = callbackDialog.querySelector('.intl-phone');
  typeof intlTelInput !== 'undefined' &&
    intlTelInput(callbackIntlTelInput, {
      utilsScript: './vendor/intl-tel/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: $('#initial_country').val(),
      onlyCountries: JSON.parse($('#country_iso').val()),
    });
}

const intlInputs = document.querySelectorAll('form:not(.callback-form) .intl-phone');

typeof intlTelInput !== 'undefined' &&
  intlInputs.forEach((input) => {
    intlTelInput(input, {
      utilsScript: './vendor/intl-tel/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: $('#initial_country').val(),
      onlyCountries: JSON.parse($('#country_iso').val()),
    });
  });

/**
  JustValidate settings
===================== **/

const forms = document.querySelectorAll('.form');

forms.forEach((form) => {
  const validator = new JustValidate(form, {
    errorLabelStyle: {
      // Remove default styling
      color: false,
    },
  });

  const textFields = form.querySelectorAll('.input-text[required]');

  textFields.forEach((field) => {
    validator.addField(field, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
      {
        rule: 'customRegexp',
        errorMessage: 'Некорректный формат ввода',
        // Latin & russian letters, numbers, spaces, hyphens
        value: /^[A-Za-z0-9А-Яа-я\s\-]+$/gi,
      },
      {
        rule: 'minLength',
        errorMessage: 'Минимум 2 символа',
        value: 2,
      },
      {
        rule: 'maxLength',
        errorMessage: 'Максимум 30 символов',
        value: 30,
      },
    ]);
  });

  const textareaFields = form.querySelectorAll('.input-textarea[required]');

  textareaFields.forEach((field) => {
    validator.addField(field, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
      {
        rule: 'customRegexp',
        errorMessage: 'Некорректный формат ввода',
        // Latin & russian letters, numbers, spaces, hyphens
        value: /^[A-Za-z0-9А-Яа-я\s\-]+$/gi,
      },
      {
        rule: 'minLength',
        errorMessage: 'Минимум 5 символов',
        value: 5,
      },
      {
        rule: 'maxLength',
        errorMessage: 'Максимум 2000 символов',
        value: 2000,
      },
    ]);
  });

  const emailFields = form.querySelectorAll('.input-email[required]');

  emailFields.forEach((field) => {
    validator.addField(field, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
      {
        rule: 'email',
        errorMessage: 'Некорректный формат e-mail',
      },
    ]);
  });

  const phoneFields = form.querySelectorAll('.input-tel[required]');

  phoneFields.forEach((field) => {
    validator.addField(field, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
      {
        rule: 'customRegexp',
        errorMessage: 'Некорректный формат номера',
        // Russian phone number formats + simple 7 digit format
        value:
          /^(?:\+?7|8)?[\s-]?(?:\(?(?:9\d{2}|4[6-9]\d|5\d{2}|3[0-8]\d|82\d|8[1-9][0-9])\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}|\d{3}[\s-]?\d{2}[\s-]?\d{2})$/gi,
      },
    ]);
  });

  const passwordFields = form.querySelectorAll('.input-password[required]');

  passwordFields.forEach((field) => {
    validator.addField(field, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
      {
        rule: 'minLength',
        errorMessage: 'Минимум 8 символов',
        value: 8,
      },
    ]);
  });

  const checkboxes = form.querySelectorAll('.input-checkbox[required]');

  checkboxes.forEach((checkbox) => {
    validator.addField(checkbox, [
      {
        rule: 'required',
        errorMessage: 'Это обязательноe поле',
      },
    ]);
  });

  const radioGroups = form.querySelectorAll('.radio-fieldset--required');

  radioGroups.forEach((radioGroup) => {
    validator.addRequiredGroup(radioGroup, 'Выберите одну из опций');
  });

  validator.onSuccess((event) => {
    /* Redirect to the success page upon successful form submission */
    // const rootLocation = 'https://domen.name';
    // const successPage = 'success.html';
    // window.location.href = rootLocation + successPage;
    /* Complete form submission template */
    //   const xhr = new XMLHttpRequest();
    //   const formData = new FormData(form);
    //   xhr.open('POST', form.getAttribute('action'));
    //   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    //   xhr.send(formData);
    //   xhr.onreadystatechange = function () {
    //     const statusDialogSuccess = document.querySelector(
    //       '.dialog[data-name="success"]'
    //     );
    //     // Form send successfully
    //     if (this.readyState === 4 && this.status === 200) {
    //       statusDialogSuccess.showModal();
    //     }
    //   };
    //   xhr.onerror = function () {
    //     // error
    //   };
  });
});

})();

/******/ })()
;

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) {
        return parts.pop().split(";").shift();
    } else {
        return '';
    }
}

function containsOnlyDigits(str) {
    const pattern = /^\d+$/; // Регулярное выражение для проверки только цифр
    return pattern.test(str);
}

if (location.pathname != '/'){
    $('.main_bestsellers').attr('aria-expanded', 'false');
}

$(document).on('click', '.button_close_message', function () {
    $('.popup_gray').hide();
    $('body').css({'overflow':'auto'});
});

$(document).on('click', '.popup_white .close_popup', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

if (getCookie('hide_push') != '' || Notification.permission === 'denied' || getCookie('user_push') != '' || $('#is_pwa_here').val() == 0 || $('#subsc_popup').val() == 0) {
    $('.popup_white').hide();
} else {
    setTimeout(function(){
        $('.popup_white').show();
    }, 5000);
}

$(document).on('click', '.push_decline', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

$(document).on('click', '.push_allow', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 900);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
    enableNotif();
});

$(document).on('click', '.button_request_call', function () {
    let phone_code = $('.iti__selected-dial-code').text();
    let number = $('#callback-phone').val().replace(/[\s()\-]/g, '');

    if (number && containsOnlyDigits(number)) {
        $.ajax({
            url: '/request_call',
            type: "POST",
            cache: false,
            data: {phone: phone_code+number},
            dataType: "json",
            success: function (res) {
                if (res['status'] == 'success') {
                    $('.popup_bottom').hide();

                    const mesa = document.querySelector('.message_sended');
                    mesa.classList.remove('hidden');
                    mesa.classList.add('active');
                } else {
                    alert(res['text']);
                }
            }
        });
    }
});

$(document).on('click', '.button_sub', function () {
    let email = $('[name="subscribe-email"]').val();

    if (email) {
        $.ajax({
            url: '/request_subscribe',
            type: "POST",
            cache: false,
            data: {email: email},
            dataType: "json",
            success: function (res) {
                if (res['status'] == 'error') {
                    alert(res['text']);
                } else {
                    $('.popup_gray').show();
                    $('.popup_bottom').hide();

                    const mesa = document.querySelector('.message_sended');
                    mesa.classList.remove('hidden');
                    mesa.classList.add('active');

                    setTimeout(function(){
                        $('.popup_gray').hide();
                        $('body').css({'overflow':'auto'});
                    }, 5000);
                }
            }
        });
    }
});

function sendAjaxContact() {
    var error = false;
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    const name = document.getElementById("contact-name").value;
    if (name.length === 0) {
        document.getElementById("contact-name").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-name").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const email = document.getElementById("contact-email").value;
    if (!email.match(validRegex) || email.length === 0) {
        document.getElementById("contact-email").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-email").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const subject_id = $('#subject_text').val();
    const message = document.getElementById("contact-message").value;

    const captcha = document.getElementById("contact-captcha").value;

    if (captcha.length === 0) {
        document.getElementById("contact-captcha").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-captcha").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const submit = true;

    if (subject_id == 0) {
        alert($('#error_subject').val());
        error = true;
    }

    if (!error) {
        $.ajax({
            url: '/request_contact_us',
            type: "POST",
            cache: false,
            data: {
                'name' : name,
                'email' : email,
                'subject' : subject_id,
                'message' : message,
                'captcha' : captcha,
                'submit' : submit
            },
            dataType: "json",
            success: function(data) { //Данные отправлены успешно
                if (data['status'] == 'error') {
                    alert(data['text']);
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {

                    $(".form").hide();
                    $(".main__heading").hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
        	}
 	    });
    }
}

function sendAjaxAffiliate() {
    var error = false;
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    const name = document.getElementById("affiliate-name").value;
    if (name.length === 0) {
        document.getElementById("affiliate-name").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("affiliate-name").style.borderColor = "rgba(255, 255, 255, 10%)";
    }
    const email = document.getElementById("affiliate-email").value;
    if (!email.match(validRegex) || email.length === 0) {
        document.getElementById("affiliate-email").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("affiliate-email").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const jabber = document.getElementById("affiliate-messenger").value;
    const message = document.getElementById("affiliate-message").value;
    const captcha = document.getElementById("affiliate-captcha").value;

    if (captcha.length === 0) {
        document.getElementById("contact-captcha").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-captcha").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const submit = true;

    if (!error) {
        $.ajax({
            url:     '/request_affiliate',
            type:     "POST",
            cache: false,
            data: {
                'name' : name,
                'email' : email,
                'jabber' : jabber,
                'message' : message,
                'captcha' : captcha,
                'submit' : submit
            },
            dataType: "json",
            success: function(data) { //Данные отправлены успешно
                if (data['status'] == 'error') {
                    alert(data['text']);
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {

                    $(".form").hide();
                    $(".main__heading").hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
        	}
     	});
    }
}

if (flagc) {
    setTimeout(function() {
        $('.modal_cart').removeClass('hidden').css('opacity', 1);
        $('.modal_cart').css('z-index', 10);
    }, 2000);

    setTimeout(function() {
        $('.modal_cart').css('opacity', 0);
    }, 5000);

    setTimeout(function() {
        $('.modal_cart').css('z-index', 0);
        $('.modal_cart').addClass('hidden');
    }, 10000);
}
if (flagp) {
    setTimeout(function() {
        $('.cmcmodal').removeClass('hidden').css('opacity', 1);
        $('.cmcmodal').css('z-index', 10);
    }, 2000);

    setTimeout(function() {
        $('.cmcmodal').css('opacity', 0);
    }, 5000);

    setTimeout(function() {
        $('.cmcmodal').css('z-index', 0);
        $('.cmcmodal').addClass('hidden');
    }, 10000);
}

$(document).on('click', '.select_header_gift', function () {
    $(this).parent().toggleClass('is-active');
});

$(document).on('click', '.select_item_gift', function () {
    $('.select_current_gift').text($(this).text());
    $('.select_current_gift').attr('curr_packaging_id', $(this).attr('packaging_id'));
    $(this).parent().parent().removeClass('is-active');
});

if (window.innerWidth > 1925) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_biggest.png');
}
if (window.innerWidth > 769 && window.innerWidth < 1920) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_big.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_big.png');
}
if (window.innerWidth > 391 && window.innerWidth < 769) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_middle.png');
}
if (window.innerWidth < 391) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_small.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_small.png');
}

window.addEventListener('resize', function (e) {
    if (window.innerWidth > 1925) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_biggest.png');
    }
    if (window.innerWidth > 769 && window.innerWidth < 1920) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_big.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_big.png');
    }
    if (window.innerWidth > 391 && window.innerWidth < 769) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_middle.png');
    }
    if (window.innerWidth < 391) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_small.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/black/checkup_small.png');
    }
});

$('.more').on('click', function (e){
    e.preventDefault();
    let block = $(this).parents('.text-box').find('.text');
    block.toggleClass('active');
    if($(this).text() == 'view all') {
        $(this).html('hide');
        block.css({'display': 'flex', 'flex-wrap': 'wrap', 'gap': '5px'});
    } else {
        $(this).html('view all');
        block.css({'display': '-webkit-box'});
    }
});

if (getCookie('christmas')) {
    $('.christmas').hide();
} else {
    $('.christmas').show();
}

// $(document).on('click', '.christmas', function () {
//     $(this).hide();
//     let date = new Date;
//     date.setDate(date.getDate() + 1);
//     date = date.toUTCString();
//     document.cookie = 'christmas=1; path=/; expires=' + date;
// });
