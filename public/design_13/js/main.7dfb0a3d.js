/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./js/components/Accordion.js":
/*!****************************************!*\
  !*** ./src/js/components/Accordion.js ***!
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
  };

  stateAttributes = {
    dataModal: 'data-modal',
    dataDialogStack: 'data-dialog-stack',
  };

  selectors = {
    nativeSubmitForm: 'form.form--native-submit[method="dialog"]',
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

    (0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.lockScrolling)(isOpen, 'dialog');

    if (isOpen) {
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
    }
  }

  open() {
    this.state.isOpen = true;
  }

  close() {
    this.startClosingAnimation();
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
  };

  stateClasses = {
    isVisible: 'is-visible',
  };

  stateAttributes = {
    ariaExpanded: 'aria-expanded',
    dropdownHover: 'data-dropdown-hover',
  };

  featureAttributes = {
    fixedDropdown: 'data-fixed-dropdown',
  };

  static openDropdowns = [];
  static globalClickListener = false;
  static hoverableMQ = window.matchMedia('(min-width: 830px)');
  static hoverSupport = Dropdown.evaluateHoverSupport();

  constructor(rootElement) {
    super();
    this.rootElement = rootElement;
    this.button = this.rootElement.querySelector(this.selectors.button);
    this.container = this.rootElement.querySelector(this.selectors.container);
    this.offset = parseInt((0,_utils_utils__WEBPACK_IMPORTED_MODULE_1__.getCssValue)(this.rootElement, '--dropdown-offset'), 10) || 0;
    this.isHoverable = Dropdown.hoverSupport && this.rootElement.hasAttribute(this.stateAttributes.dropdownHover);
    this.isToggler = this.rootElement.dataset.dropdownHover === 'toggler';
    this.hoverListeners = null;
    this.containerClone = null;
    this.isFixedDropdown = this.rootElement.hasAttribute(this.featureAttributes.fixedDropdown);
    this.state = this.getProxyState({ isOpen: false });
    rootElement._instance = this;

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
      if (this.isFixedDropdown) {
        this.createContainerClone();
      } else {
        this.positionContainer(this.container);
        this.container.classList.add(isVisible);
      }
      Dropdown.openDropdowns.push(this);
    } else {
      if (this.isFixedDropdown && this.containerClone) {
        document.body.removeChild(this.containerClone);
        this.containerClone = null;
      }
      this.container.classList.remove(isVisible);
      Dropdown.openDropdowns.splice(Dropdown.openDropdowns.indexOf(this), 1);
    }
  }

  createContainerClone() {
    this.containerClone = this.container.cloneNode(true);
    this.containerClone.style.position = 'fixed';
    document.body.appendChild(this.containerClone);
    this.positionContainer(this.containerClone);
    this.containerClone.classList.add(this.stateClasses.isVisible);
  }

  positionContainer(element) {
    const isSubDropdown = this.rootElement.parentElement.closest(dropdownSelector);
    // Floating UI
    computePosition(this.button, element, {
      middleware: [flip(), offset(this.offset), shift({ padding: 10 })],
      placement: isSubDropdown ? 'right' : 'bottom',
    }).then(({ x, y }) => {
      element.style.left = `${x}px`;
      element.style.top = `${y}px`;
    });
  }

  toggle() {
    this.state.isOpen = !this.state.isOpen;
  }

  open() {
    this.state.isOpen = true;
  }

  close() {
    this.state.isOpen = false;
    if (this.containerClone) {
      document.body.removeChild(this.containerClone);
      this.containerClone = null;
    }
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
      if (e.target.closest(this.selectors.button)) {
        const dropdown = e.target.closest(dropdownSelector)._instance;
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

/***/ "./js/components/categories-nav.js":
/*!*********************************************!*\
  !*** ./src/js/components/categories-nav.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   initCategoriesNav: () => (/* binding */ initCategoriesNav)
/* harmony export */ });
function initCategoriesNav() {
  const catNavOpeners = document.querySelectorAll('[data-cat-nav-opener]');
  const catNav = document.querySelector('.cat-nav');
  const catNavLinks = catNav.querySelectorAll('.sublist-toggler');
  const catSubLists = catNav.querySelectorAll('.nav__sublist');
  const catCloseButton = catNav.querySelector('.nav__close-button');
  const catOverlay = document.querySelector('.cat-overlay');
  const mqSmall = window.matchMedia('(max-width: 829.98px)');

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
    if (!catNav.contains(e.target) && !catNavOpeners[0].contains(e.target) && !catNavOpeners[1]?.contains(e.target)) {
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
      if (elements.length > 1) {
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
      buttonPlaceholder: 18,
      hiddenItemsContainer: '.greedy-hidden-items',
      minWidth: 830,
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
    const viewportWidth = window.innerWidth;

    if (viewportWidth < this.options.minWidth) {
      // Restore all hidden items to the visible container
      while (this.hiddenItemsContainer.children.length > 0) {
        this.visibleItemsContainer.appendChild(this.hiddenItemsContainer.firstElementChild);
      }
      this.placeholderNeeded = false;
      this.updateButtonState();
      return;
    }

    // Proceed with greedy logic
    while (true) {
      this.availableSpace = this.container.offsetWidth - (this.placeholderNeeded ? this.options.buttonPlaceholder : 0);
      this.sumOfVisibleItems = this.visibleItemsContainer.children.length;
      this.requiredSpace = this.itemBreakpoints[this.sumOfVisibleItems - 1];

      if (this.requiredSpace > this.availableSpace && this.sumOfVisibleItems > 0) {
        this.hiddenItemsContainer.prepend(this.visibleItemsContainer.lastElementChild);
        this.sumOfVisibleItems -= 1;
        this.placeholderNeeded = true;
      } else if (
        this.itemBreakpoints[this.totalItems - 1] <= this.container.offsetWidth &&
        this.hiddenItemsContainer.children.length > 0
      ) {
        this.visibleItemsContainer.appendChild(this.hiddenItemsContainer.firstElementChild);
        this.sumOfVisibleItems += 1;
        this.placeholderNeeded = false;
      } else if (
        this.availableSpace >= this.itemBreakpoints[this.sumOfVisibleItems] &&
        this.hiddenItemsContainer.children.length > 0
      ) {
        this.visibleItemsContainer.appendChild(this.hiddenItemsContainer.firstElementChild);
        this.sumOfVisibleItems += 1;
      } else {
        break;
      }
    }

    this.updateButtonState();
  }

  updateButtonState() {
    this.button.setAttribute('data-counter', this.totalItems - this.visibleItemsContainer.children.length);
    if (this.visibleItemsContainer.children.length === this.totalItems) {
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

  const headerSelects = customSelect('.header-select');
  const selects = customSelect('.select');

  // Improve custom select aria
  const selectOpeners = document.querySelectorAll('.custom-select-opener');

  selectOpeners.forEach((opener) => {
    opener.setAttribute('aria-label', 'Select category');
  });

  // Inject data-flag into custom options
  document.querySelectorAll('.header-lang').forEach((customSelect) => {
    const instance = customSelect.querySelector('.header-select').customSelect;
    const select = instance.select;
    const options = customSelect.querySelectorAll('option');
    const customItems = customSelect.parentNode.querySelectorAll('.custom-select-option');

    options.forEach((option, index) => {
      const flag = option.dataset.flag;
      if (flag && customItems[index]) {
        customItems[index].setAttribute('data-flag', flag);
      }
    });

    // Set initial flag on opener
    const selectedOption = select.options[select.selectedIndex];
    const initialFlag = selectedOption.dataset.flag;
    if (initialFlag) {
      instance.opener.setAttribute('data-flag', initialFlag);
    }

    select.addEventListener('change', () => {
      const selectedOption = select.options[select.selectedIndex];
      const flag = selectedOption.dataset.flag;

      if (flag) {
        instance.opener.setAttribute('data-flag', flag);
      }
    });
  });
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
/* harmony import */ var _components_greedy_nav__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/greedy-nav */ "./js/components/greedy-nav.js");
/* harmony import */ var _libs_just_validate_settings__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./libs/just-validate-settings */ "./js/libs/just-validate-settings.js");
/* harmony import */ var _libs_custom_select_settings__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./libs/custom-select-settings */ "./js/libs/custom-select-settings.js");
/* harmony import */ var _libs_intl_tel_settings__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./libs/intl-tel-settings */ "./js/libs/intl-tel-settings.js");
/* harmony import */ var _components_categories_nav__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/categories-nav */ "./js/components/categories-nav.js");
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
const greedyNav = new _components_greedy_nav__WEBPACK_IMPORTED_MODULE_5__["default"]('.greedy-nav');

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
  Vendor libs settings
==================== **/




(0,_libs_just_validate_settings__WEBPACK_IMPORTED_MODULE_6__.initJustValidate)();
(0,_libs_custom_select_settings__WEBPACK_IMPORTED_MODULE_7__.initCustomSelect)();
(0,_libs_intl_tel_settings__WEBPACK_IMPORTED_MODULE_8__.initIntlTel)();

/**
  Categories nav
=============- **/


(0,_components_categories_nav__WEBPACK_IMPORTED_MODULE_9__.initCategoriesNav)();

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
  parent.closest('.order-form').querySelector('.submit-button').textContent = submitText;
  parent.closest('.order-form').querySelector('.submit-button').value = submitText;
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