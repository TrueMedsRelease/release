/* Dropdown component */
export default class Dropdown {
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
      if (
        !this.button.contains(e.target) &&
        (!this.containerClone || (!this.containerClone.contains(e.target) && !this.container.contains(e.target)))
      ) {
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
