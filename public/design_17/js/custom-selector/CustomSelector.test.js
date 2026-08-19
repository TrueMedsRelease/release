/**
 * CustomSelector — unit tests
 *
 * Run with:
 *   npm install --save-dev jest jest-environment-jsdom
 *   npx jest CustomSelector.test.js
 *
 * @jest-environment jsdom
 */

import CustomSelector from './CustomSelector.js';

const defaultOptions = {
  selectorType: 'single',
  features: ['search', 'keyboard-navigation', 'accessibility'],
  dataSource: {
    type: 'static',
    options: [
      { value: '1', label: 'Option 1' },
      { value: '2', label: 'Option 2' },
      { value: '3', label: 'Option 3' },
    ],
  },
};

function createFixture(attrs = {}) {
  const el = document.createElement('div');
  if (attrs.placeholder) el.dataset.placeholder = attrs.placeholder;
  if (attrs.id) el.id = attrs.id;
  document.body.appendChild(el);
  return el;
}

function cleanup(...instances) {
  instances.forEach((inst) => {
    if (inst && typeof inst.destroy === 'function') inst.destroy();
  });
  document.body.innerHTML = '';
}

// ══════════════════════════════════════════════════════════════
// 1. Constructor
// ══════════════════════════════════════════════════════════════

describe('Constructor', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('creates instance with element selector', () => {
    const el = createFixture({ id: 'test' });
    const cs = new CustomSelector('#test', defaultOptions);
    expect(cs).toBeInstanceOf(CustomSelector);
    expect(cs.el).toBe(el);
    cs.destroy();
  });

  test('creates instance with HTMLElement', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(cs).toBeInstanceOf(CustomSelector);
    cs.destroy();
  });

  test('throws when element is not found', () => {
    expect(() => new CustomSelector('#non-existent', defaultOptions)).toThrow();
  });

  test('adds container classes', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(el.classList.contains('cs-container')).toBe(true);
    expect(el.classList.contains('cs-light')).toBe(true);
    cs.destroy();
  });

  test('renders control element', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(el.querySelector('.cs-control')).toBeTruthy();
    expect(el.querySelector('.cs-dropdown')).toBeTruthy();
    expect(el.querySelector('.cs-options')).toBeTruthy();
    cs.destroy();
  });

  test('renders search input when feature is enabled', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(el.querySelector('.cs-search-input')).toBeTruthy();
    cs.destroy();
  });

  test('does not render search when feature is disabled', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, {
      ...defaultOptions,
      features: ['keyboard-navigation', 'accessibility'],
    });
    expect(el.querySelector('.cs-search-input')).toBeFalsy();
    cs.destroy();
  });

  test('uses data-placeholder attribute', () => {
    const el = createFixture({ placeholder: 'Select an item' });
    const cs = new CustomSelector(el, defaultOptions);
    expect(el.querySelector('.cs-placeholder').textContent).toBe('Select an item');
    cs.destroy();
  });

  test('initializes filteredOptions from static data', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(cs.state.filteredOptions.length).toBe(3);
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 2. Open / Close
// ══════════════════════════════════════════════════════════════

describe('Open / Close', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('open() adds cs-open class and shows dropdown', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    expect(cs.state.isOpen).toBe(true);
    expect(el.classList.contains('cs-open')).toBe(true);
    expect(el.querySelector('.cs-dropdown').hidden).toBe(false);
    cs.destroy();
  });

  test('close() removes cs-open class and hides dropdown', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    cs.close();
    expect(cs.state.isOpen).toBe(false);
    expect(el.classList.contains('cs-open')).toBe(false);
    expect(el.querySelector('.cs-dropdown').hidden).toBe(true);
    cs.destroy();
  });

  test('toggle() switches open state', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.toggle();
    expect(cs.state.isOpen).toBe(true);
    cs.toggle();
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });

  test('sets aria-expanded on open/close', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    cs.open();
    expect(control.getAttribute('aria-expanded')).toBe('true');
    cs.close();
    expect(control.getAttribute('aria-expanded')).toBe('false');
    cs.destroy();
  });

  test('does not open when disabled', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.disable();
    cs.open();
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });

  test('does not open when already open', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    const spy = jest.fn();
    cs.on('open', spy);
    cs.open();
    expect(spy).not.toHaveBeenCalled();
    cs.destroy();
  });

  test('fires open event', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('open', spy);
    cs.open();
    expect(spy).toHaveBeenCalledTimes(1);
    cs.destroy();
  });

  test('fires close event', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('close', spy);
    cs.open();
    cs.close();
    expect(spy).toHaveBeenCalledTimes(1);
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 3. Single Select
// ══════════════════════════════════════════════════════════════

describe('Single Select', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('select() sets state.selected', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '2', label: 'Option 2' });
    expect(cs.state.selected).toEqual({ value: '2', label: 'Option 2' });
    cs.destroy();
  });

  test('select() updates placeholder text', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    expect(el.querySelector('.cs-placeholder').textContent).toBe('Option 1');
    cs.destroy();
  });

  test('select() closes dropdown', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    cs.select({ value: '1', label: 'Option 1' });
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });

  test('deselect() clears selection', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.deselect({ value: '1', label: 'Option 1' });
    expect(cs.state.selected).toBeNull();
    cs.destroy();
  });

  test('deselect() restores placeholder text', () => {
    const el = createFixture({ placeholder: 'Pick one' });
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.deselect({ value: '1', label: 'Option 1' });
    expect(el.querySelector('.cs-placeholder').textContent).toBe('Pick one');
    cs.destroy();
  });

  test('fires change event on select', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('change', spy);
    cs.select({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy).toHaveBeenCalledWith({ value: { value: '1', label: 'Option 1' }, oldValue: null });
    cs.destroy();
  });

  test('fires change event on deselect', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    const spy = jest.fn();
    cs.on('change', spy);
    cs.deselect({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy).toHaveBeenCalledWith({ value: null });
    cs.destroy();
  });

  test('getValue() returns selected value', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(cs.getValue()).toBeNull();
    cs.select({ value: '2', label: 'Option 2' });
    expect(cs.getValue()).toBe('2');
    cs.destroy();
  });

  test('setValue() selects matching option', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.setValue('2');
    expect(cs.getValue()).toBe('2');
    expect(el.querySelector('.cs-placeholder').textContent).toBe('Option 2');
    cs.destroy();
  });

  test('setValue() with invalid value does nothing', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.setValue('non-existent');
    expect(cs.getValue()).toBeNull();
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 4. Multiple Select
// ══════════════════════════════════════════════════════════════

describe('Multiple Select', () => {
  const multiOptions = {
    ...defaultOptions,
    selectorType: 'multiple',
  };

  afterEach(() => { document.body.innerHTML = ''; });

  test('select() adds to selected array', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    expect(cs.state.selected.length).toBe(1);
    expect(cs.state.selected[0].value).toBe('1');
    cs.destroy();
  });

  test('select() multiple items', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '2', label: 'Option 2' });
    expect(cs.state.selected.length).toBe(2);
    cs.destroy();
  });

  test('does not add duplicate items', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '1', label: 'Option 1' });
    expect(cs.state.selected.length).toBe(1);
    cs.destroy();
  });

  test('renders tags', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '2', label: 'Option 2' });
    const tags = el.querySelectorAll('.cs-tag');
    expect(tags.length).toBe(2);
    expect(tags[0].querySelector('.cs-tag-label').textContent).toBe('Option 1');
    cs.destroy();
  });

  test('deselect() removes from selected array', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '2', label: 'Option 2' });
    cs.deselect({ value: '1', label: 'Option 1' });
    expect(cs.state.selected.length).toBe(1);
    expect(cs.state.selected[0].value).toBe('2');
    cs.destroy();
  });

  test('getValue() returns array of values', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '3', label: 'Option 3' });
    expect(cs.getValue()).toEqual(['1', '3']);
    cs.destroy();
  });

  test('setValue() selects multiple items', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    cs.setValue(['1', '3']);
    expect(cs.getValue()).toEqual(['1', '3']);
    cs.destroy();
  });

  test('fires change with array on multi select', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, multiOptions);
    const spy = jest.fn();
    cs.on('change', spy);
    cs.select({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalledWith({ value: [{ value: '1', label: 'Option 1' }] });
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 5. Search
// ══════════════════════════════════════════════════════════════

describe('Search', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('filterOptions filters by query', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs._filterOptions('Option 1');
    expect(cs.state.filteredOptions.length).toBe(1);
    expect(cs.state.filteredOptions[0].value).toBe('1');
    cs.destroy();
  });

  test('filterOptions is case-insensitive by default', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs._filterOptions('option');
    expect(cs.state.filteredOptions.length).toBe(3);
    cs.destroy();
  });

  test('filterOptions returns all when query is short', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs._filterOptions('O');
    expect(cs.state.filteredOptions.length).toBe(3);
    cs.destroy();
  });

  test('filterOptions returns all when query is empty', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs._filterOptions('');
    expect(cs.state.filteredOptions.length).toBe(3);
    cs.destroy();
  });

  test('filterOptions returns empty when no match', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs._filterOptions('zzzznotfound');
    expect(cs.state.filteredOptions.length).toBe(0);
    cs.destroy();
  });

  test('fires search event', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('search', spy);
    cs._filterOptions('Option 1');
    expect(spy).toHaveBeenCalledWith({ query: 'Option 1' });
    cs.destroy();
  });

  test('clears highlighted index on search', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.state.highlightedIndex = 2;
    cs._filterOptions('Option 1');
    expect(cs.state.highlightedIndex).toBe(-1);
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 6. Keyboard Navigation
// ══════════════════════════════════════════════════════════════

describe('Keyboard Navigation', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  function keydown(element, key) {
    element.dispatchEvent(new KeyboardEvent('keydown', { key, bubbles: true }));
  }

  test('Enter toggles open state', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    keydown(control, 'Enter');
    expect(cs.state.isOpen).toBe(true);
    keydown(control, 'Enter');
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });

  test('Space toggles open state', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    keydown(control, ' ');
    expect(cs.state.isOpen).toBe(true);
    cs.destroy();
  });

  test('Escape closes dropdown', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    const control = el.querySelector('.cs-control');
    keydown(control, 'Escape');
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });

  test('ArrowDown opens and highlights next', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    keydown(control, 'ArrowDown');
    expect(cs.state.isOpen).toBe(true);
    expect(cs.state.highlightedIndex).toBe(0);
    keydown(control, 'ArrowDown');
    expect(cs.state.highlightedIndex).toBe(1);
    cs.destroy();
  });

  test('ArrowUp highlights previous', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    keydown(control, 'ArrowDown');
    keydown(control, 'ArrowDown');
    keydown(control, 'ArrowUp');
    expect(cs.state.highlightedIndex).toBe(0);
    cs.destroy();
  });

  test('does not highlight below 0', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    keydown(control, 'ArrowDown');
    keydown(control, 'ArrowUp');
    expect(cs.state.highlightedIndex).toBe(0);
    keydown(control, 'ArrowUp');
    expect(cs.state.highlightedIndex).toBe(0);
    cs.destroy();
  });

  test('does not highlight above max', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    for (let i = 0; i < 10; i++) keydown(control, 'ArrowDown');
    expect(cs.state.highlightedIndex).toBe(2);
    cs.destroy();
  });

  test('Tab closes dropdown', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    const control = el.querySelector('.cs-control');
    keydown(control, 'Tab');
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 7. Disabled State
// ══════════════════════════════════════════════════════════════

describe('Disabled State', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('disable() sets isDisabled and adds class', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.disable();
    expect(cs.state.isDisabled).toBe(true);
    expect(el.classList.contains('cs-disabled')).toBe(true);
    cs.destroy();
  });

  test('disable() sets aria-disabled', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const control = el.querySelector('.cs-control');
    cs.disable();
    expect(control.getAttribute('aria-disabled')).toBe('true');
    cs.destroy();
  });

  test('enable() removes disabled state', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.disable();
    cs.enable();
    expect(cs.state.isDisabled).toBe(false);
    expect(el.classList.contains('cs-disabled')).toBe(false);
    cs.destroy();
  });

  test('disable() closes dropdown if open', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    cs.disable();
    expect(cs.state.isOpen).toBe(false);
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 8. Events (on/off)
// ══════════════════════════════════════════════════════════════

describe('Events', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('on() registers event listener', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('change', spy);
    cs.select({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalled();
    cs.destroy();
  });

  test('off() removes event listener', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('change', spy);
    cs.off('change', spy);
    cs.select({ value: '1', label: 'Option 1' });
    expect(spy).not.toHaveBeenCalled();
    cs.destroy();
  });

  test('on() returns this for chaining', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    const result = cs.on('change', spy);
    expect(result).toBe(cs);
    cs.destroy();
  });

  test('fires select event', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('select', spy);
    cs.select({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalledWith({ item: { value: '1', label: 'Option 1' } });
    cs.destroy();
  });

  test('fires deselect event', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('deselect', spy);
    cs.select({ value: '1', label: 'Option 1' });
    cs.deselect({ value: '1', label: 'Option 1' });
    expect(spy).toHaveBeenCalledWith({ item: { value: '1', label: 'Option 1' } });
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 9. Clear
// ══════════════════════════════════════════════════════════════

describe('clear()', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('clears single selection', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.clear();
    expect(cs.state.selected).toBeNull();
    cs.destroy();
  });

  test('clears multiple selection', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, { ...defaultOptions, selectorType: 'multiple' });
    cs.select({ value: '1', label: 'Option 1' });
    cs.select({ value: '2', label: 'Option 2' });
    cs.clear();
    expect(cs.state.selected).toEqual([]);
    cs.destroy();
  });

  test('restores placeholder after clear', () => {
    const el = createFixture({ placeholder: 'My placeholder' });
    const cs = new CustomSelector(el, defaultOptions);
    cs.select({ value: '1', label: 'Option 1' });
    cs.clear();
    expect(el.querySelector('.cs-placeholder').textContent).toBe('My placeholder');
    cs.destroy();
  });

  test('fires change event with null', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.fn();
    cs.on('change', spy);
    cs.clear();
    expect(spy).toHaveBeenCalledWith({ value: null });
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 10. getOptions / setOptions
// ══════════════════════════════════════════════════════════════

describe('getOptions / setOptions', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('getOptions returns filtered options', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    expect(cs.getOptions().length).toBe(3);
    cs.destroy();
  });

  test('setOptions replaces options', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.setOptions([{ value: 'a', label: 'Alpha' }, { value: 'b', label: 'Beta' }]);
    expect(cs.getOptions().length).toBe(2);
    expect(cs.getOptions()[0].value).toBe('a');
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 11. Destroy
// ══════════════════════════════════════════════════════════════

describe('destroy()', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('removes container classes', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.destroy();
    expect(el.classList.contains('cs-container')).toBe(false);
    expect(el.classList.contains('cs-open')).toBe(false);
    expect(el.classList.contains('cs-disabled')).toBe(false);
  });

  test('removes custom UI on destroy', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.destroy();
    expect(el.querySelector('.cs-control')).toBeNull();
    expect(el.querySelector('.cs-dropdown')).toBeNull();
    expect(el.querySelector('.cs-sr-only')).toBeNull();
  });

  test('nullifies state, dom, config', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.destroy();
    expect(cs.state).toBeNull();
    expect(cs.dom).toEqual({});
    expect(cs._config).toBeNull();
  });

  test('closes before destroy', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    cs.open();
    cs.destroy();
    expect(el.querySelector('.cs-dropdown')).toBeFalsy();
  });

  test('removes document click listener', () => {
    const el = createFixture();
    const cs = new CustomSelector(el, defaultOptions);
    const spy = jest.spyOn(document, 'removeEventListener');
    cs.destroy();
    expect(spy).toHaveBeenCalled();
    spy.mockRestore();
  });
});
