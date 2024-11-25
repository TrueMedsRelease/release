/* Draggable navigation component */
export default class DragNav {
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
