/* Greedy navigation component */
/* Dinamically calculates total width of items and shows/hides them when needed */
export default class GreedyNav {
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
