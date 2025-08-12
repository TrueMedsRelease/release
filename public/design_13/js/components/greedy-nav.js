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
