import BaseComponent from './BaseComponent';

const itemSelector = '[data-accordion-item]';

class Accordion extends BaseComponent {
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

export default class initAccordion {
  constructor() {
    this.init();
  }

  init() {
    const items = document.querySelectorAll(itemSelector);
    items.forEach((item) => new Accordion(item));
  }
}
