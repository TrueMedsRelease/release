import BaseComponent from './BaseComponent';
import { debounce } from '../utils/utils';

const stickySelector = '[data-sticky]';

class Sticky extends BaseComponent {
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
      debounce(() => {
        this.recalculateOffset();
      }, 300),
    );
  }
}

export default class initSticky {
  constructor() {
    this.init();
  }

  init() {
    const stickyElements = document.querySelectorAll(stickySelector);
    stickyElements.forEach((element) => new Sticky(element));
  }
}
