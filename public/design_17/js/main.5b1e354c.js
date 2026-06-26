/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./js/components/Accordion.js":
/*!****************************************!*\
  !*** ./js/components/Accordion.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initAccordion)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");


const itemSelector = '[data-accordion-item]';

class Accordion extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  selectors = {
    parent: '[data-accordion]',
    button: '[data-accordion-button]',
    panel: '[data-accordion-panel]',
  };

  stateClasses = {
    isOpen: 'is-open',
    isAnimating: 'is-animating',
  };

  stateAttributes = {
    dataCollapsible: 'data-collapsible',
  };

  cssVariables = {
    height: '--accordion-height',
  };

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.parent = this.rootElement.closest(this.selectors.parent);
    this.button = this.rootElement.querySelector(this.selectors.button);
    this.panel = this.rootElement.querySelector(this.selectors.panel);
    this.isCollapsible = this.parent?.hasAttribute(this.stateAttributes.dataCollapsible);
    this.isAnimating = false;
    this.state = this.getProxyState({
      isOpen: this.rootElement.classList.contains(this.stateClasses.isOpen),
    });
    rootElement._instance = this;

    this.bindEvents();
  }

  updateUI() {
    const { isOpen } = this.state;

    this.rootElement.classList.toggle(this.stateClasses.isOpen, isOpen);

    if (this.isCollapsible && isOpen) {
      this.closeOtherInstances();
    }
  }

  toggle() {
    this.state.isOpen = !this.state.isOpen;
  }

  close() {
    this.state.isOpen = false;
  }

  closeOtherInstances() {
    const siblings = this.parent.querySelectorAll(`:scope > ${itemSelector}.${this.stateClasses.isOpen}`);
    siblings.forEach((sibling) => sibling !== this.rootElement && sibling._instance.handleAnimation());
  }

  onButtonClick = (e) => {
    this.state.isOpen && e.preventDefault();

    const isSafari = document.documentElement.classList.contains('ua-safari');
    isSafari ? this.setSafariHeight() : this.setHeight();

    if (!this.isAnimating) {
      this.isAnimating = true;
    } else {
      e.preventDefault();
      this.rootElement.classList.remove(this.stateClasses.isAnimating);
      this.rootElement.open = false;
      this.isAnimating = false;
      this.close();
      return;
    }

    this.handleAnimation();
  };

  handleAnimation() {
    this.rootElement.classList.add(this.stateClasses.isAnimating);
    this.toggle();
    this.rootElement.addEventListener('animationend', this.animationEndHandler, { once: true });
  }

  animationEndHandler = () => {
    this.rootElement.classList.remove(this.stateClasses.isAnimating);
    this.isAnimating = false;
    this.rootElement.open = this.state.isOpen;
  };

  setHeight() {
    this.panel.style.setProperty(this.cssVariables.height, `${this.panel.scrollHeight}px`);
  }

  setSafariHeight() {
    if (!this.rootElement.open) {
      this.rootElement.open = true;
      this.setHeight();
      this.rootElement.open = false;
    } else {
      this.setHeight();
    }
  }

  bindEvents() {
    this.button.addEventListener('click', this.onButtonClick);
  }
}

class initAccordion {
  constructor() {
    this.init();
  }

  init() {
    const items = document.querySelectorAll(itemSelector);
    items.forEach((item) => new Accordion(item));
  }
}


/***/ }),

/***/ "./js/components/BaseComponent.js":
/*!********************************************!*\
  !*** ./src/js/components/BaseComponent.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BaseComponent)
/* harmony export */ });
class BaseComponent {
  constructor() {
    if (this.constructor === BaseComponent) {
      throw new Error('Cannot create an instance of an abstract class.');
    }
  }

  getProxyState(initialState) {
    return new Proxy(initialState, {
      get: (target, prop) => {
        return target[prop];
      },
      set: (target, prop, newValue) => {
        const oldValue = target[prop];

        target[prop] = newValue;

        if (newValue !== oldValue) {
          this.updateUI();
        }

        return true;
      },
    });
  }

  updateUI() {
    throw new Error('updateUI() method must be implemented in the child class.');
  }
}


/***/ }),

/***/ "./js/components/Dialog.js":
/*!*************************************!*\
  !*** ./src/js/components/Dialog.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initDialog)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");
/* harmony import */ var _utils_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/utils */ "./js/utils/utils.js");



const dialogSelector = '[data-dialog]';

class Dialog extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  stateClasses = {
    isVisible: 'is-visible',
    isClosing: 'close-dialog-animation',
    isLocked: 'is-locked',
  };

  stateAttributes = {
    dataModal: 'data-modal',
    dataDialogStack: 'data-dialog-stack',
  };

  selectors = {
    nativeSubmitForm: 'form.form--native-submit[method="dialog"]',
  };

  events = {
    open: 'dialog:open',
    close: 'dialog:close',
  };

  static openModals = [];
  static stackable = false;
  static overlay = null;
  static immediateClose = false;

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.dialogName = this.rootElement.dataset.dialog;
    this.isModal = this.rootElement.hasAttribute(this.stateAttributes.dataModal);
    this.openButtons = document.querySelectorAll(`[data-dialog-open="${this.dialogName}"]`);
    this.closeButtons = document.querySelectorAll(`[data-dialog-close="${this.dialogName}"]`);
    this.nativeSubmitForms = this.rootElement.querySelectorAll(this.selectors.nativeSubmitForm);
    this.state = this.getProxyState({ isOpen: false });
    rootElement._instance = this;

    this.isModal && !Dialog.stackable && this.createOverlay();
    this.bindEvents();
  }

  updateUI() {
    const { isOpen } = this.state;
    const { isVisible } = this.stateClasses;
    const bodyIsLocked = document.body.classList.contains(this.stateClasses.isLocked);

    if (isOpen) {
      !bodyIsLocked && (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.lockScrolling)(isOpen, 'dialog');

      if (!this.isModal) return this.rootElement.show();

      this.rootElement.showModal();
      !Dialog.openModals.includes(this) && Dialog.openModals.push(this);

      if (Dialog.stackable) {
        this.rootElement.setAttribute(this.stateAttributes.dataDialogStack, Dialog.openModals.length);
      } else {
        Dialog.overlay.classList.add(isVisible);
        Dialog.openModals.length === 2 && Dialog.openModals[0].close();
      }

      // Remove initial focus from the first interactive element inside the dialog
      this.rootElement.focus();
    } else {
      if (!this.isModal) return this.rootElement.close();

      !Dialog.immediateClose && this.rootElement.close();
      Dialog.stackable && this.rootElement.removeAttribute(this.stateAttributes.dataDialogStack);
      Dialog.openModals.splice(Dialog.openModals.indexOf(this), 1);
      Dialog.openModals.length === 0 && (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.lockScrolling)(isOpen, 'dialog');
    }
  }

  open() {
    this.state.isOpen = true;
    this.dispatchOpenEvent();
  }

  close() {
    this.startClosingAnimation();
    this.dispatchCloseEvent();
  }

  startClosingAnimation() {
    this.rootElement.classList.add(this.stateClasses.isClosing);
    Dialog.hideOverlay();
    this.rootElement.addEventListener('animationend', this.animationEndHandler, { once: true });
  }

  animationEndHandler = () => {
    this.completeClosingAnimation();
  };

  completeClosingAnimation() {
    this.state.isOpen = false;
    this.rootElement.classList.remove(this.stateClasses.isClosing);
  }

  handleImmediateClose() {
    if (!this.isModal) return;
    Dialog.immediateClose = true;
    if (Dialog.stackable) {
      Dialog.openModals[Dialog.openModals.length - 1].close();
    } else {
      this.rootElement.close();
      Dialog.hideOverlay();
    }
    this.state.isOpen = false;
    this.rootElement.classList.remove(this.stateClasses.isClosing);
    this.rootElement.removeEventListener('animationend', this.animationEndHandler);
    this.dispatchCloseEvent();
    Dialog.immediateClose = false;
  }

  createOverlay() {
    if (!Dialog.overlay) {
      Dialog.overlay = (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.createOverlay)('dialog-overlay');
    }
  }

  onOpenButtonClick = () => {
    this.open();
  };

  onCloseButtonClick = () => {
    this.close();
  };

  static closeAll() {
    Dialog.openModals.forEach((dialog) => dialog.close());
  }

  static hideOverlay() {
    if (!Dialog.stackable && Dialog.openModals.length === 1) {
      Dialog.overlay.classList.remove('is-visible');
    }
  }

  static handleEscapeKey(e) {
    if (e.key === 'Escape' && Dialog.openModals.length > 0) {
      if (Dialog.stackable) {
        return Dialog.openModals[Dialog.openModals.length - 1].handleImmediateClose();
      }
      // Force close all dialogs despite current animation state
      while (Dialog.openModals.length > 0) {
        Dialog.openModals[Dialog.openModals.length - 1].handleImmediateClose();
      }
    }
  }

  dispatchOpenEvent = () => {
    this.rootElement.dispatchEvent(new CustomEvent(this.events.open, { detail: { dialog: this } }));
  };

  dispatchCloseEvent = () => {
    this.rootElement.dispatchEvent(new CustomEvent(this.events.close, { detail: { dialog: this } }));
  };

  bindEvents() {
    this.openButtons.forEach((openButton) => {
      openButton.addEventListener('click', this.onOpenButtonClick);
    });
    this.closeButtons.forEach((closeButton) => {
      closeButton.addEventListener('click', this.onCloseButtonClick);
    });
    this.nativeSubmitForms[0] &&
      this.nativeSubmitForms.forEach((form) => {
        form.addEventListener('submit', (e) => {
          e.preventDefault();
          this.close();
        });
      });

    // Overlay lays beneath transparent .dialog-container::backdrop
    this.rootElement.addEventListener('click', (e) => {
      e.target === this.rootElement && this.close();
    });
  }
}

class initDialog {
  constructor() {
    this.init();
  }

  init() {
    const dialogs = document.querySelectorAll(dialogSelector);
    dialogs.forEach((dialog) => new Dialog(dialog));

    document.addEventListener('keydown', Dialog.handleEscapeKey);
  }
}


/***/ }),

/***/ "./js/components/Drawer.js":
/*!*************************************!*\
  !*** ./src/js/components/Drawer.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initDrawer)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");
/* harmony import */ var _utils_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/utils */ "./js/utils/utils.js");



const drawerSelector = '[data-drawer]';

class Drawer extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  stateClasses = {
    isActive: 'is-active',
    isVisible: 'is-visible',
  };

  stateAttributes = {
    ariaExpanded: 'aria-expanded',
  };

  static openDrawers = [];
  static resizeListener = false;
  static overlay = null;
  static isAnimating = false;
  static animationDelay = 300;

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.drawerName = this.rootElement.dataset.drawer;
    this.toggleButtons = document.querySelectorAll(`[data-drawer-toggle="${this.drawerName}"]`);
    this.closeButtons = document.querySelectorAll(`[data-drawer-close="${this.drawerName}"]`);
    this.state = this.getProxyState({ isOpen: false });

    this.createOverlay();
    this.bindEvents();
  }

  updateUI() {
    const { isOpen } = this.state;
    const { isActive, isVisible } = this.stateClasses;
    const { ariaExpanded } = this.stateAttributes;

    this.toggleButtons.forEach((toggleButton) => {
      toggleButton.setAttribute(ariaExpanded, isOpen);
    });

    if (isOpen) {
      this.rootElement.classList.add(isActive);
      !Drawer.openDrawers.includes(this) && Drawer.openDrawers.push(this);
    } else {
      this.rootElement.classList.remove(isActive);
      Drawer.openDrawers = Drawer.openDrawers.filter((drawer) => drawer !== this);
    }

    (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.lockScrolling)(Drawer.openDrawers.length === 1, 'drawer', Drawer.animationDelay);

    if (Drawer.openDrawers.length === 0) {
      Drawer.overlay.classList.remove(isVisible);
      this.removeResizeListener();
    } else {
      Drawer.overlay.classList.add(isVisible);
      this.addResizeListener();
    }
  }

  toggle() {
    this.state.isOpen = !this.state.isOpen;
  }

  close() {
    this.state.isOpen = false;
  }

  createOverlay() {
    if (!Drawer.overlay) {
      Drawer.overlay = (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.createOverlay)('drawer-overlay');
    }
  }

  addResizeListener() {
    if (!Drawer.resizeListener) {
      window.addEventListener('resize', Drawer.closeAll);
      Drawer.resizeListener = true;
    }
  }

  removeResizeListener() {
    if (Drawer.resizeListener) {
      window.removeEventListener('resize', Drawer.closeAll);
      Drawer.resizeListener = false;
    }
  }

  onToggleButtonClick = () => {
    if (Drawer.isAnimating) return;

    if (!this.state.isOpen) {
      Drawer.openDrawers.forEach((drawer) => {
        if (drawer !== this) {
          drawer.close();
        }
      });
      this.toggle();
    } else {
      Drawer.isAnimating = true;
      this.toggle();
      setTimeout(() => {
        Drawer.isAnimating = false;
      }, Drawer.animationDelay);
    }
  };

  onCloseButtonClick = () => {
    this.close();
  };

  static closeAll() {
    Drawer.openDrawers.forEach((drawer) => drawer.close());
  }

  bindEvents() {
    this.toggleButtons.forEach((toggleButton) => {
      toggleButton.addEventListener('click', this.onToggleButtonClick);
    });
    this.closeButtons.forEach((closeButton) => {
      closeButton.addEventListener('click', this.onCloseButtonClick);
    });
    Drawer.overlay.addEventListener('click', Drawer.closeAll);
  }
}

class initDrawer {
  constructor() {
    this.init();
  }

  init() {
    const drawers = document.querySelectorAll(drawerSelector);
    drawers.forEach((drawer) => new Drawer(drawer));

    // Close all drawers when clicking on an anchor link
    document.addEventListener('click', (e) => {
      if (e.target.matches(`${drawerSelector} a[href*="#"]:not([href="#!"]`)) {
        Drawer.closeAll();
      }
    });
  }
}


/***/ }),

/***/ "./js/components/Dropdown.js":
/*!***************************************!*\
  !*** ./src/js/components/Dropdown.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initDropdown)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");
/* harmony import */ var _utils_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/utils */ "./js/utils/utils.js");


const { computePosition, offset, flip, shift } = window.FloatingUIDOM;

const dropdownSelector = '[data-dropdown]';

class Dropdown extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  selectors = {
    button: '[data-dropdown-button]',
    container: '[data-dropdown-container]',
    selectItem: '[data-dropdown-select-item]',
  };

  stateClasses = {
    isVisible: 'is-visible',
  };

  stateAttributes = {
    ariaExpanded: 'aria-expanded',
    dropdownHover: 'data-dropdown-hover',
    dropdownPlacement: 'data-dropdown-placement',
    dropdownBoundary: 'data-dropdown-boundary',
  };

  static openDropdowns = [];
  static globalClickListener = false;
  static hoverableMQ = window.matchMedia('(min-width: 960px)');
  static hoverSupport = Dropdown.evaluateHoverSupport();

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.button = this.rootElement.querySelector(this.selectors.button);
    this.container = this.rootElement.querySelector(this.selectors.container);
    this.offset = parseInt((0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.getCssValue)(this.rootElement, '--dropdown-offset'), 10) || 0;
    this.crossOffset = parseInt((0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.getCssValue)(this.rootElement, '--dropdown-cross-offset'), 10) || 0;
    this.alignmentOffset = parseInt((0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.getCssValue)(this.rootElement, '--dropdown-alignment-offset'), 10) || 0;
    this.boundaryElement = this.rootElement.closest(this.rootElement.dataset.dropdownBoundary) || undefined;
    this.isHoverable = Dropdown.hoverSupport && this.rootElement.hasAttribute(this.stateAttributes.dropdownHover);
    this.isToggler = this.rootElement.dataset.dropdownHover === 'toggler';
    this.isSelect = this.rootElement.hasAttribute('data-dropdown-select');
    this.hoverListeners = null;
    this.state = this.getProxyState({ isOpen: false });
    rootElement._instance = this;

    if (this.isSelect) {
      this.initSelected();
    }

    this.bindEvents();
  }

  updateUI() {
    const { isOpen } = this.state;
    const { isVisible } = this.stateClasses;
    const { ariaExpanded } = this.stateAttributes;
    const isSubDropdown = this.rootElement.parentElement.closest(dropdownSelector);

    if (Dropdown.openDropdowns.length && !isSubDropdown) {
      Dropdown.closeAll();
    }

    this.button.setAttribute(ariaExpanded, isOpen);

    if (isOpen) {
      // Floating UI
      computePosition(this.boundaryElement || this.button, this.container, {
        middleware: [
          flip(),
          offset(({ placement }) => {
            // Flip main-axis offset for sub-dropdowns on 'top' or 'left' placements
            const [side] = placement.split('-');
            let mainAxis = this.offset;

            if (isSubDropdown && (side === 'top' || side === 'left')) {
              mainAxis = -this.offset;
            }

            return {
              mainAxis,
              crossAxis: this.crossOffset,
              alignmentAxis: this.alignmentOffset,
            };
          }),
          shift({ padding: 0 }),
        ],
        placement: isSubDropdown
          ? this.rootElement.dataset.dropdownPlacement || 'right'
          : this.rootElement.dataset.dropdownPlacement || 'bottom',
      }).then(({ x, y }) => {
        Object.assign(this.container.style, {
          left: `${x}px`,
          top: `${y}px`,
        });
      });
      this.container.classList.add(isVisible);
      Dropdown.openDropdowns.push(this);
    } else {
      this.container.classList.remove(isVisible);
      Dropdown.openDropdowns.splice(Dropdown.openDropdowns.indexOf(this), 1);
    }
  }

  toggle() {
    this.state.isOpen = !this.state.isOpen;
  }

  open() {
    this.state.isOpen = true;
  }

  close() {
    this.state.isOpen = false;
  }

  setValue(item) {
    const label = item.textContent.trim();
    const textEl = this.button.querySelector('.button-text');
    textEl && (textEl.textContent = label);
    this.value = item.dataset.value || label;

    this.container.querySelectorAll(this.selectors.selectItem).forEach((el) => {
      el.classList.remove('is-active');
      el.setAttribute('data-dropdown-select-item', '');
    });

    item.classList.add('is-active');
    item.setAttribute('data-dropdown-select-item', 'selected');
  }

  static closeAll() {
    Dropdown.openDropdowns.forEach((dropdown) => {
      dropdown.close();
    });
  }

  addHoverListeners() {
    this.rootElement.addEventListener('mouseenter', () => {
      Dropdown.hoverSupport && this.open();
    });
    this.rootElement.addEventListener('mouseleave', () => {
      Dropdown.hoverSupport && this.close();
    });
    this.hoverListeners = true;
  }

  static evaluateHoverSupport() {
    return (
      Dropdown.hoverableMQ.matches &&
      (window.matchMedia('(any-hover: hover)').matches || !('ontouchstart' in window || navigator.maxTouchPoints > 0))
    );
  }

  bindEvents() {
    this.isHoverable && this.addHoverListeners();

    if (Dropdown.globalClickListener) return;

    document.addEventListener('click', (e) => {
      const dropdownEl = e.target.closest(dropdownSelector);
      const instance = dropdownEl?._instance;

      if (instance?.isSelect) {
        const item = e.target.closest(instance.selectors.selectItem);

        if (item) {
          instance.setValue(item);
          instance.close();
          return;
        }
      }

      const button = e.target.closest('[data-dropdown-button]');
      if (button) {
        const dropdownEl = button.closest(dropdownSelector);
        const dropdown = dropdownEl._instance;

        const isSplit = button.hasAttribute('data-dropdown-split');
        const textEl = button.querySelector('.button-text');
        const isTextClick = textEl && textEl.contains(e.target);

        if (isSplit && isTextClick) {
          return;
        }

        if (dropdown.isHoverable && !dropdown.isToggler) return;

        e.preventDefault();
        dropdown.toggle();
        return;
      }

      if (Dropdown.openDropdowns.length && !e.target.closest(this.selectors.container)) {
        Dropdown.closeAll();
      }
    });

    Dropdown.globalClickListener = true;
  }

  initSelected() {
    const selectedItem = this.container.querySelector(
      `${this.selectors.selectItem}[data-dropdown-select-item="selected"]`,
    );

    if (selectedItem) {
      this.setValue(selectedItem);
    }
  }
}

class initDropdown {
  constructor() {
    this.init();
  }

  init() {
    const dropdowns = document.querySelectorAll(dropdownSelector);
    dropdowns.forEach((dropdown) => new Dropdown(dropdown));

    const handleResize = (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.debounce)(() => {
      const currentHoverSupport = Dropdown.evaluateHoverSupport();

      if (Dropdown.hoverSupport !== currentHoverSupport) {
        Dropdown.hoverSupport = currentHoverSupport;

        dropdowns.forEach((dropdown) => {
          const instance = dropdown._instance;
          if (instance.isHoverable && !instance.hoverListeners) {
            instance.addHoverListeners();
          }
        });
      }
    }, 300);

    window.addEventListener('resize', () => {
      handleResize();
      Dropdown.openDropdowns.length && Dropdown.closeAll();
    });
  }
}


/***/ }),

/***/ "./js/components/ScrollTo.js":
/*!***************************************!*\
  !*** ./src/js/components/ScrollTo.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initScrollTo)
/* harmony export */ });
const buttonSelector = '[data-scroll-to]';

class ScrollTo {
  constructor(button) {
    this.button = button;
    this.bindEvents();
  }

  onButtonClick = () => {
    const target = this.button.dataset.scrollTo || 'top';
    const offset = parseInt(this.button.dataset.scrollOffset) || 0;

    switch (target) {
      // Scroll to the top of the page
      case 'top':
        window.scrollTo({
          top: 0,
          behavior: 'smooth',
        });
        break;
      // Scroll to the bottom of the page
      case 'bottom':
        window.scrollTo({
          top: document.body.scrollHeight,
          behavior: 'smooth',
        });
        break;
      // Scroll to the target element using the provided selector
      default:
        const targetElement = document.querySelector(target);
        if (!targetElement) {
          console.warn(`Element with selector "${target}" not found.`);
          return;
        }
        const top = targetElement.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({
          top,
          behavior: 'smooth',
        });
    }
  };

  bindEvents() {
    this.button.addEventListener('click', this.onButtonClick);
  }
}

class initScrollTo {
  constructor() {
    this.init();
  }

  init() {
    const buttons = document.querySelectorAll(buttonSelector);
    buttons.forEach((button) => new ScrollTo(button));
  }
}


/***/ }),

/***/ "./js/components/Sticky.js":
/*!*************************************!*\
  !*** ./src/js/components/Sticky.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initSticky)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");
/* harmony import */ var _utils_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/utils */ "./js/utils/utils.js");



const stickySelector = '[data-sticky]';

class Sticky extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  selectors = {
    containerElement: 'data-sticky-container',
    offsetElement: '[data-sticky-offset]',
    stickyHeaderElement: 'header',
  };

  stateClasses = {
    isSticky: 'is-sticky',
    isLocked: 'is-locked',
  };

  stateAttributes = {
    isOnTop: 'data-sticky-on-top',
  };

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.rootElement.sticky = this;
    this.isHeader = this.rootElement.classList.contains(this.selectors.stickyHeaderElement);
    this.isOnTop = this.rootElement.hasAttribute(this.stateAttributes.isOnTop);
    this.stickyElementName = this.rootElement.dataset.sticky;
    this.containerElement = this.getContainerElement();
    this.rootElementOffset = this.rootElement.offsetTop;
    this.offsetElements = this.rootElement.querySelectorAll(this.selectors.offsetElement);
    this.innerOffset = null;
    this.state = this.getProxyState({ isSticky: false });

    this.applyInitialSettings();
    this.bindEvents();
  }

  updateUI() {
    const { isSticky } = this.state;
    const container = this.containerElement;
    const stickyHeight = `${this.rootElement.offsetHeight - this.innerOffset}px`;
    const stickyHeightVar = `--sticky-${this.stickyElementName}-height`;
    const stickyOffsetVar = `--sticky-${this.stickyElementName}-offset`;

    if (isSticky) {
      this.rootElement.classList.add(this.stateClasses.isSticky);
      if (!this.innerOffset) return;
      container.style.setProperty(stickyHeightVar, stickyHeight);
      container.style.setProperty(stickyOffsetVar, `${this.innerOffset}px`);
    } else {
      if (this.isHeader && document.body.classList.contains(this.stateClasses.isLocked)) return;

      this.rootElement.classList.remove(this.stateClasses.isSticky);
      if (!this.innerOffset) return;
      container.style.removeProperty(stickyHeightVar);
      container.style.removeProperty(stickyOffsetVar);
    }
  }

  setInnerOffset() {
    this.innerOffset = 0;
    this.offsetElements.forEach((offsetElement) => {
      this.innerOffset += offsetElement.offsetHeight;
    });
  }

  setOuterOffset(boolean) {
    if (!boolean) return;
    this.rootElementOffset > 0 &&
      this.rootElement.style.setProperty(`--${this.stickyElementName}-offset`, `${this.rootElementOffset}px`);
  }

  setHeightVariable(boolean) {
    boolean &&
      this.containerElement.style.setProperty(
        `--${this.stickyElementName}-height`,
        `${this.rootElement.offsetHeight}px`,
      );
  }

  applyOuterOffset() {
    this.setOuterOffset(this.isOnTop);
    this.setHeightVariable(this.isOnTop);
  }

  recalculateOffset() {
    this.rootElementOffset = this.rootElement.offsetTop;
    this.applyOuterOffset();
    this.setInnerOffset();
    this.updateUI();
  }

  getContainerElement() {
    return this.isHeader
      ? document.body
      : this.rootElement.closest(`[${this.selectors.containerElement}=${this.stickyElementName}]`);
  }

  toggleStickState() {
    window.scrollY > this.rootElementOffset + this.innerOffset
      ? (this.state.isSticky = true)
      : (this.state.isSticky = false);
  }

  applyInitialSettings() {
    this.applyOuterOffset();
    this.setInnerOffset();
    this.toggleStickState();
  }

  handleScroll() {
    requestAnimationFrame(() => {
      this.toggleStickState();
    });
  }

  bindEvents() {
    window.addEventListener('scroll', this.handleScroll.bind(this));
    window.addEventListener(
      'resize',
      (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.debounce)(() => {
        this.recalculateOffset();
      }, 300),
    );
  }
}

class initSticky {
  constructor() {
    this.init();
  }

  init() {
    const stickyElements = document.querySelectorAll(stickySelector);
    stickyElements.forEach((element) => new Sticky(element));
  }
}


/***/ }),

/***/ "./js/components/Tabs.js":
/*!***********************************!*\
  !*** ./src/js/components/Tabs.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ initTabs)
/* harmony export */ });
/* harmony import */ var _BaseComponent__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./BaseComponent */ "./js/components/BaseComponent.js");


const tabsSelector = '[data-tabs]';
const buttonSelector = '[data-tabs-button]';
const itemSelector = '[data-tabs-item]';
const panelSelector = '[data-tabs-panel]';

class Tabs extends _BaseComponent__WEBPACK_IMPORTED_MODULE_0__["default"] {
  stateClasses = {
    isActive: 'is-active',
  };

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.buttons = Array.from(this.rootElement.querySelectorAll(buttonSelector));
    this.items = Array.from(this.rootElement.querySelectorAll(itemSelector));
    this.panels = this.items.map((item) => item.querySelector(panelSelector));
    this.activeIndex = this.buttons.findIndex((btn) => btn.classList.contains(this.stateClasses.isActive));
    if (this.activeIndex === -1) this.activeIndex = 0;
    this.updateUI();
    this.bindEvents();
    rootElement._instance = this;
  }

  updateUI() {
    this.buttons.forEach((btn, i) => {
      const isActive = i === this.activeIndex;
      btn.classList.toggle(this.stateClasses.isActive, isActive);
      btn.setAttribute('aria-selected', isActive);
      btn.tabIndex = isActive ? 0 : -1;
    });
    this.items.forEach((item, i) => {
      const isActive = i === this.activeIndex;
      item.classList.toggle(this.stateClasses.isActive, isActive);
      this.panels[i].ariaHidden = !isActive;
    });
  }

  activate(index) {
    if (index !== this.activeIndex) {
      this.activeIndex = index;
      this.updateUI();
    }
  }

  onButtonClick = (e) => {
    const index = this.buttons.indexOf(e.currentTarget);
    if (index !== -1) {
      this.activate(index);
    }
  };

  bindEvents() {
    this.buttons.forEach((btn) => {
      btn.addEventListener('click', this.onButtonClick);
    });
  }
}

class initTabs {
  constructor() {
    this.init();
  }

  init() {
    const tabs = document.querySelectorAll(tabsSelector);
    tabs.forEach((tab) => new Tabs(tab));
  }
}


/***/ }),

/***/ "./js/libs/custom-select-settings.js":
/*!***********************************************!*\
  !*** ./src/js/libs/custom-select-settings.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initCustomSelect: () => (/* binding */ initCustomSelect)
/* harmony export */ });
/* Custom select settings */
function initCustomSelect() {
  if (typeof customSelect === 'undefined') return;

  const selects = customSelect('.select');

  selects.forEach((instance) => {
    enhanceCustomSelect(instance);

    /* Scroll to the top of the panel when the panel is opened */
    instance.container.addEventListener('custom-select:open', () => {
      const panel = instance.panel;
      const selected = panel.querySelector('.is-selected');

      if (selected) {
        panel.scrollTop = 0;
        return;
      }
    });
  });

  // Improve custom select aria
  const selectOpeners = document.querySelectorAll('.custom-select-opener');

  selectOpeners.forEach((opener) => {
    opener.setAttribute('aria-label', 'Select category');
  });

  // Add data-attributes content to the select options
  function enhanceCustomSelect(instance) {
    const select = instance.select;
    const options = select.querySelectorAll('option');
    const items = instance.panel.querySelectorAll('.custom-select-option');

    options.forEach((option, index) => {
      const item = items[index];
      if (!item) return;

      const text = option.text;
      const { flag, label, caption } = option.dataset;

      if (flag) item.dataset.flag = flag;
      if (label) item.dataset.label = label;
      if (caption) item.dataset.caption = caption;

      item.innerHTML = `<span class="cs-option-wrapper">${renderOption({ text, flag, label, caption })}</span>`;
    });

    const update = () => {
      const selected = select.options[select.selectedIndex];
      const { flag, label, caption } = selected.dataset;
      const text = selected.text;

      if (flag) instance.opener.dataset.flag = flag;
      if (label) instance.opener.dataset.label = label;
      if (caption) instance.opener.dataset.caption = caption;

      instance.opener.innerHTML = `<span class="cs-opener-wrapper">${renderOption({ text, flag, label, caption })}</span>`;
    };

    function renderOption({ text, flag, image, label, caption }) {
        const hasMeta = image || flag || label;

        return `
            <span class="cs-text">${text}</span>
            ${
            hasMeta
                ? `<span class="cs-meta">
                    ${
                    image
                        ? `<img src="${image}" class="cs-image" alt="">`
                        : flag
                        ? `<span data-flag="${flag}" class="cs-flag"></span>`
                        : ''
                    }
                    ${label ? `<span class="cs-label">${label}</span>` : ''}
                </span>`
                : ''
            }
            ${caption ? `<span class="cs-caption">${caption}</span>` : ''}
        `;
    }

    function setOrRemoveData(element, key, value) {
        if (value) {
            element.dataset[key] = value;
        } else {
            delete element.dataset[key];
        }
    }

    update();
    select.addEventListener('change', update);
  }
}


/***/ }),

/***/ "./js/libs/intl-tel-settings.js":
/*!******************************************!*\
  !*** ./src/js/libs/intl-tel-settings.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initIntlTel: () => (/* binding */ initIntlTel)
/* harmony export */ });
/* International Telephone Input settings */
function initIntlTel() {
  if (typeof intlTelInput === 'undefined') return;

  const callbackBtn = document.querySelector('[data-dialog-open="call"]');
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
      initialCountry: $('#initial_country').val(),
      onlyCountries: JSON.parse($('#country_iso').val()),
    });
  }

  const intlInputs = document.querySelectorAll('form:not(.callback-form) .intl-phone');

  intlInputs.forEach((input) => {
    intlTelInput(input, {
      utilsScript: './vendor/intl-tel/js/utils.js',
      useFullscreenPopup: false,
      showSelectedDialCode: true,
      initialCountry: $('#initial_country').val(),
      onlyCountries: JSON.parse($('#country_iso').val()),
    });
  });
}


/***/ }),

/***/ "./js/libs/just-validate-settings.js":
/*!***********************************************!*\
  !*** ./src/js/libs/just-validate-settings.js ***!
  \***********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initJustValidate: () => (/* binding */ initJustValidate)
/* harmony export */ });
/* Just validate settings */
function initJustValidate() {
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
      errorMessage: 'Некорректный формат ввода',
      // Latin & russian letters, numbers, space, hyphens
      value: /^[A-Za-z0-9А-Яа-яЁё\s\-–—]+$/gi,
    };

    textFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Это обязательноe поле',
        },
        ...(strictTextFieldValidation ? [strictTextFieldValidationRuleset] : []),
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

    /* Textarea fields */
    const textareaFields = form.querySelectorAll('.input-textarea[required]');
    const strictTextareaValidation = false;
    const strictTextareaValidationRuleset = {
      rule: 'customRegexp',
      errorMessage: 'Некорректный формат ввода',
      // Latin & russian letters, numbers, space,
      // general punctuation marks, quotes
      value: /^[A-Za-z0-9А-Яа-яЁё\s\-_.,!?;:'"«»“”‘’()]+$/gi,
    };

    textareaFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Это обязательноe поле',
        },
        ...(strictTextareaValidation ? [strictTextareaValidationRuleset] : []),
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

    /* Email fields */
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

    /* Phone number fields */
    const phoneFields = form.querySelectorAll('.input-tel[required]');
    // 11 digit + iMask symbols format
    const iMaskPhoneRegex = /^(?:\+7|(?:\D*\d){11}\D*)$/gi;
    // Russian phone number formats + simple 7 digit format
    const russianPhoneRegex =
      /^(?:\+?7|8)?[\s-]?(?:\(?(?:9\d{2}|4[6-9]\d|5\d{2}|3[0-8]\d|82\d|8[1-9][0-9])\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}|\d{3}[\s-]?\d{2}[\s-]?\d{2})$/gi;

    phoneFields.forEach((field) => {
      validator.addField(field, [
        {
          rule: 'required',
          errorMessage: 'Это обязательноe поле',
        },
        {
          rule: 'customRegexp',
          errorMessage: 'Некорректный формат номера',
          value: iMaskPhoneRegex,
        },
      ]);
    });

    /* Password fields */
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

    /* Checkboxes */
    const checkboxes = form.querySelectorAll('.input-checkbox[required]');

    checkboxes.forEach((checkbox) => {
      validator.addField(checkbox, [
        {
          rule: 'required',
          errorMessage: 'Это обязательноe поле',
        },
      ]);
    });

    /* Radio groups */
    const radioGroups = form.querySelectorAll('.radio-fieldset--required');

    radioGroups.forEach((radioGroup) => {
      validator.addRequiredGroup(radioGroup, 'Выберите одну из опций');
    });

    /* Selects */
    const selects = form.querySelectorAll('select[required]');

    selects.forEach((select) => {
      validator.addField(select, [
        {
          rule: 'required',
          errorMessage: 'Выберите одну из опций',
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


/***/ }),

/***/ "./js/libs/swiper-settings.js":
/*!****************************************!*\
  !*** ./src/js/libs/swiper-settings.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initSwiper: () => (/* binding */ initSwiper)
/* harmony export */ });
/* Swiper settings */
function initSwiper() {
  if (typeof Swiper === 'undefined') return;

  const footerTestimonialsSwiper = new Swiper('.footer-testimonials__swiper', {
    slidesPerView: 1,
    centeredSlides: true,
    allowTouchMove: true,
    grabCursor: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    speed: 800,
    loop: true,
    spaceBetween: 20,
    breakpoints: {
      1200: {
        spaceBetween: 40,
      },
    },
  });
}


/***/ }),

/***/ "./js/utils/utils.js":
/*!*******************************!*\
  !*** ./src/js/utils/utils.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   applyInitialSettings: () => (/* binding */ applyInitialSettings),
/* harmony export */   checkDialog: () => (/* binding */ checkDialog),
/* harmony export */   createOverlay: () => (/* binding */ createOverlay),
/* harmony export */   debounce: () => (/* binding */ debounce),
/* harmony export */   getCssValue: () => (/* binding */ getCssValue),
/* harmony export */   initBrowserChecks: () => (/* binding */ initBrowserChecks),
/* harmony export */   lockScrolling: () => (/* binding */ lockScrolling)
/* harmony export */ });
/**
  Browser utilities
================= **/

/* Check if JavaScript is enabled */
function checkJS() {
  document.documentElement.classList.remove('no-js');
}

/* Detect browser */
function detectBrowser() {
  const test = (regexp) => regexp.test(navigator.userAgent);
  const browser = (() => {
    switch (true) {
      case test(/edg/i):
        return 'edge';
      case test(/trident/i):
        return 'ie';
      case test(/firefox|fxios/i):
        return 'firefox';
      case test(/opr\//i):
        return 'opera';
      case test(/chrome|chromium|crios/i):
        return 'chrome';
      case test(/safari|AppleWebKit/i):
        return 'safari';
      default:
        return 'unknown';
    }
  })();

  document.documentElement.classList.add(`ua-${browser}`);
}

/* Check if browser supports .webp-images */
function checkWebp() {
  const webp = new Image();
  webp.src =
    'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';

  webp.onload = webp.onerror = () => {
    const isWebpSupported = webp.height === 2;
    if (!isWebpSupported) document.documentElement.classList.remove('webp');
  };
}

/* Check if browser supports dialog element */
function checkDialog() {
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
      link.href = 'vendor/dialog-polyfill/dialog-polyfill.min.css';
      script.src = 'vendor/dialog-polyfill/dialog-polyfill.min.js';
      head.append(link, script);
      script.addEventListener('load', () => {
        dialogs.forEach((dialog) => {
          dialogPolyfill.registerDialog(dialog);
        });
      });
    }
  });
}

function initBrowserChecks() {
  checkJS();
  detectBrowser();
  checkWebp();
  checkDialog();
}

/**
  DOM utilities
============= **/

/* Disable a[href="#!"] links */
function disableEmptyLinks() {
  document.addEventListener('click', (e) => {
    if (e.target.matches('a[href="#!"]')) {
      e.preventDefault();
    }
  });
}

/* Disable CSS transitions during window resize */
function disableTransitionsOnResize(className = 'no-transitions', delay = 300) {
  const handleResize = debounce(() => {
    document.body.classList.remove(className);
  }, delay);

  window.addEventListener('resize', () => {
    document.body.classList.add(className);
    handleResize();
  });
}

function applyInitialSettings() {
  disableEmptyLinks();
  disableTransitionsOnResize();
}

/**
  General utilities
================= **/

/* Debounce function to limit its execution rate */
function debounce(func, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

/* Get CSS property value */
function getCssValue(element, property) {
  return window.getComputedStyle(element).getPropertyValue(property);
}

/**
  UI utilities
============ **/

/* Create overlay element */
function createOverlay(className, parent = document.body) {
  const overlay = document.createElement('div');
  overlay.classList.add(className, 'overlay');
  parent.appendChild(overlay);
  return overlay;
}

/* Lock scrolling */
function lockScrolling(enableLock, source, delay = 0) {
  const { documentElement: html, body } = document;
  const currentSource = body.dataset.lockSource;
  const hasSameSource = currentSource === source;
  const scrollY = window.scrollY;

  // Toggle lock state and document scrollbar
  if (!currentSource || hasSameSource) {
    body.classList.toggle('is-locked', enableLock);
    html.classList.toggle('has-scrollbar', body.scrollHeight > window.innerHeight && enableLock);
  }

  // Toggle lock source
  if (enableLock && !currentSource) {
    body.dataset.lockSource = source;
  } else if (!enableLock && hasSameSource) {
    body.removeAttribute('data-lock-source');
  }

  // Manage scroll offset
  if (enableLock && scrollY > 0) {
    body.style.setProperty('--top-body-offset', `-${scrollY}px`);
  } else if (hasSameSource) {
    const offset = Math.abs(parseInt(getCssValue(body, '--top-body-offset') || '0', 10));
    setTimeout(() => {
      body.style.removeProperty('--top-body-offset');
    }, delay);
    requestAnimationFrame(() => {
      window.scrollTo({ top: offset, left: 0, behavior: 'instant' });
    });
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
/* harmony import */ var _utils_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/utils */ "./js/utils/utils.js");
/* harmony import */ var _components_Accordion__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/Accordion */ "./js/components/Accordion.js");
/* harmony import */ var _components_Dialog__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/Dialog */ "./js/components/Dialog.js");
/* harmony import */ var _components_Drawer__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/Drawer */ "./js/components/Drawer.js");
/* harmony import */ var _components_Dropdown__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/Dropdown */ "./js/components/Dropdown.js");
/* harmony import */ var _components_Sticky__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/Sticky */ "./js/components/Sticky.js");
/* harmony import */ var _components_ScrollTo__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/ScrollTo */ "./js/components/ScrollTo.js");
/* harmony import */ var _components_Tabs__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/Tabs */ "./js/components/Tabs.js");
/* harmony import */ var _libs_just_validate_settings__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./libs/just-validate-settings */ "./js/libs/just-validate-settings.js");
/* harmony import */ var _libs_custom_select_settings__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./libs/custom-select-settings */ "./js/libs/custom-select-settings.js");
/* harmony import */ var _libs_intl_tel_settings__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./libs/intl-tel-settings */ "./js/libs/intl-tel-settings.js");
/* harmony import */ var _libs_swiper_settings__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./libs/swiper-settings */ "./js/libs/swiper-settings.js");
/**
  Inintial checks & settings
========================== **/


(0,_utils_utils__WEBPACK_IMPORTED_MODULE_0__.initBrowserChecks)();
(0,_utils_utils__WEBPACK_IMPORTED_MODULE_0__.applyInitialSettings)();

/**
  Components
========== **/








new _components_Accordion__WEBPACK_IMPORTED_MODULE_1__["default"]();
new _components_Dialog__WEBPACK_IMPORTED_MODULE_2__["default"]();
new _components_Drawer__WEBPACK_IMPORTED_MODULE_3__["default"]();
new _components_Dropdown__WEBPACK_IMPORTED_MODULE_4__["default"]();
new _components_Sticky__WEBPACK_IMPORTED_MODULE_5__["default"]();
new _components_ScrollTo__WEBPACK_IMPORTED_MODULE_6__["default"]();
new _components_Tabs__WEBPACK_IMPORTED_MODULE_7__["default"]();

/**
  Form elements UX
================ **/

/* Autoresizible textarea */

// const textareaFields = document.querySelectorAll('.textarea-field');

// textareaFields.forEach((field) => {
//   const textarea = field.querySelector('.input-textarea');
//   const initialHeight = textarea.offsetHeight;
//   const initialOffset = initialHeight - textarea.clientHeight;

//   textarea.addEventListener('input', () => {
//     textarea.style.height = initialHeight + 'px';
//     textarea.style.height = textarea.closest('.dialog')
//       ? textarea.scrollHeight + initialOffset + 2 + 'px'
//       : textarea.scrollHeight + initialOffset + 'px';
//     // Fix clumsy textarea scroll
//     textarea.scrollTo(0, textarea.scrollHeight);
//   });
// });

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
  Vendor libs settings
==================== **/





(0,_libs_just_validate_settings__WEBPACK_IMPORTED_MODULE_8__.initJustValidate)();
(0,_libs_custom_select_settings__WEBPACK_IMPORTED_MODULE_9__.initCustomSelect)();
(0,_libs_intl_tel_settings__WEBPACK_IMPORTED_MODULE_10__.initIntlTel)();
(0,_libs_swiper_settings__WEBPACK_IMPORTED_MODULE_11__.initSwiper)();

/**
  Payment method's switcher
======================= **/

const paymentMethod = document.querySelector('.payment-select')?.customSelect;

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
  parent.closest('.order-form').querySelector('.submit-button .button-text').textContent = submitText;
  parent.closest('.order-form').querySelector('.submit-button .button-text').value = submitText;
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

})();

/******/ })()
;