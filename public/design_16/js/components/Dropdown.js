import BaseComponent from './BaseComponent';
import { debounce, getCssValue } from '../utils/utils';
const { computePosition, offset, flip, shift } = window.FloatingUIDOM;

const dropdownSelector = '[data-dropdown]';

class Dropdown extends BaseComponent {
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
    this.offset = parseInt(getCssValue(this.rootElement, '--dropdown-offset'), 10) || 0;
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

export default class initDropdown {
  constructor() {
    this.init();
  }

  init() {
    const dropdowns = document.querySelectorAll(dropdownSelector);
    dropdowns.forEach((dropdown) => new Dropdown(dropdown));

    const handleResize = debounce(() => {
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
