/**
 * CheckoutIntegration — integration tests for CustomSelector in design 17 checkout
 *
 * Run with:
 *   npm install --save-dev jest jest-environment-jsdom
 *   npx jest CheckoutIntegration.test.js
 *
 * @jest-environment jsdom
 */

import CustomSelector from './CustomSelector.js';

function createSelect(options, attrs) {
  const select = document.createElement('select');
  select.className = 'select';
  if (attrs) {
    Object.entries(attrs).forEach(([k, v]) => select.setAttribute(k, v));
  }
  (options || []).forEach((opt) => {
    const o = document.createElement('option');
    o.value = opt.value;
    o.textContent = opt.label;
    if (opt.placeholder) o.setAttribute('placeholder', '');
    if (opt.selected) o.selected = true;
    if (opt.disabled) o.disabled = true;
    if (opt.dataFlag) o.setAttribute('data-flag', opt.dataFlag);
    if (opt.dataAsset) o.setAttribute('data-asset', opt.dataAsset);
    if (opt.dataLabel) o.setAttribute('data-label', opt.dataLabel);
    if (opt.dataCaption) o.setAttribute('data-caption', opt.dataCaption);
    select.appendChild(o);
  });
  return select;
}

function createSelectWrapper(options, attrs) {
  const wrapper = document.createElement('div');
  wrapper.className = 'select-wrapper';
  const select = createSelect(options, attrs);
  wrapper.appendChild(select);
  const chevron = document.createElement('span');
  chevron.className = 'icon select-wrapper__chevron';
  chevron.innerHTML = '<svg><use href="#chevron-down"></use></svg>';
  wrapper.appendChild(chevron);
  return wrapper;
}

function cleanup(...instances) {
  instances.forEach((i) => { if (i && typeof i.destroy === 'function') i.destroy(); });
  document.body.innerHTML = '';
}

const COUNTRY_OPTIONS = [
  { value: 'US', label: 'United States', dataFlag: 'us' },
  { value: 'GB', label: 'United Kingdom', dataFlag: 'gb' },
  { value: 'DE', label: 'Germany', dataFlag: 'de' },
];

const PAYMENT_OPTIONS = [
  { value: 'mastercard', label: 'MasterCard', dataAsset: '/img/pay/mastercard.svg' },
  { value: 'visa', label: 'Visa', dataAsset: '/img/pay/visa.svg' },
  { value: 'paypal', label: 'PayPal', dataAsset: '/img/pay/paypal.svg' },
];

const CARD_MONTH_OPTIONS = [
  { value: '', label: 'MM', placeholder: true },
  ...Array.from({ length: 12 }, (_, i) => ({
    value: String(i + 1).padStart(2, '0'),
    label: String(i + 1).padStart(2, '0'),
  })),
];

// ══════════════════════════════════════════════════════════════
// 1. Enhanced mode — lifecycle
// ══════════════════════════════════════════════════════════════

describe('Enhanced mode: lifecycle', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('creates instance with enhancedSelect', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS, {
      'data-component': 'custom-selector',
      'data-action': 'change-country',
      name: 'billing_country',
    });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');

    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(cs).toBeInstanceOf(CustomSelector);
    expect(cs.getNativeSelect()).toBe(select);
    cs.destroy();
  });

  test('hides native select', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(select.style.display).toBe('none');
    cs.destroy();
  });

  test('restores native select on destroy', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    cs.destroy();
    expect(select.style.display).not.toBe('none');
  });

  test('reads options from native select', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS, { name: 'country' });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(cs.getOptions().length).toBe(3);
    expect(cs.getOptions()[0].value).toBe('US');
    expect(cs.getOptions()[0].flag).toBe('us');
    cs.destroy();
  });

  test('uses initial value from native select', () => {
    const wrapper = createSelectWrapper(
      COUNTRY_OPTIONS.map((o, i) => ({ ...o, selected: i === 1 })),
      { name: 'country' }
    );
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(cs.getValue()).toBe('GB');
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 2. Value sync
// ══════════════════════════════════════════════════════════════

describe('Enhanced mode: value sync', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('select() updates native select value', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    cs.select({ value: 'DE', label: 'Germany' });
    expect(select.value).toBe('DE');
    cs.destroy();
  });

  test('select() dispatches change event on native select', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    const spy = jest.fn();
    select.addEventListener('change', spy);
    cs.select({ value: 'DE', label: 'Germany' });
    expect(spy).toHaveBeenCalledTimes(1);
    cs.destroy();
  });

  test('clear() resets native select value', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    cs.select({ value: 'US', label: 'United States' });
    cs.clear();
    expect(select.value).toBe('');
    cs.destroy();
  });

  test('syncFromNativeSelect() reads current select value', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    select.value = 'GB';
    cs.syncFromNativeSelect();
    expect(cs.getValue()).toBe('GB');
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 3. Rich options rendering
// ══════════════════════════════════════════════════════════════

describe('Enhanced mode: rich options', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('renders data-flag in options', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    const options = wrapper.querySelectorAll('.cs-option');
    expect(options.length).toBe(3);
    const firstOption = options[0];
    expect(firstOption.querySelector('.cs-flag')).toBeTruthy();
    expect(firstOption.querySelector('.cs-flag').getAttribute('data-flag')).toBe('us');
    cs.destroy();
  });

  test('renders data-asset in options', () => {
    const wrapper = createSelectWrapper(PAYMENT_OPTIONS, {
      'data-component': 'custom-selector',
      'data-action': 'change-payment',
      name: 'payment_type',
    });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    const options = wrapper.querySelectorAll('.cs-option');
    expect(options.length).toBe(3);
    const firstOption = options[0];
    expect(firstOption.querySelector('.cs-asset-icon')).toBeTruthy();
    expect(firstOption.querySelector('.cs-asset-icon').getAttribute('src')).toBe('/img/pay/mastercard.svg');
    cs.destroy();
  });

  test('renders opener asset icon when selected', () => {
    const wrapper = createSelectWrapper(PAYMENT_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    cs.select({ value: 'visa', label: 'Visa', asset: '/img/pay/visa.svg' });
    const openerAsset = wrapper.querySelector('.cs-opener-asset');
    expect(openerAsset).toBeTruthy();
    expect(openerAsset.getAttribute('src')).toBe('/img/pay/visa.svg');
    cs.destroy();
  });

  test('renders data-label and data-caption', () => {
    const wrapper = createSelectWrapper([
      { value: 'free', label: 'Free Delivery', dataLabel: 'Free', dataCaption: '5-7 days' },
    ]);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    const option = wrapper.querySelector('.cs-option');
    expect(option.querySelector('.cs-label')).toBeTruthy();
    expect(option.querySelector('.cs-label').textContent).toBe('Free');
    expect(option.querySelector('.cs-caption')).toBeTruthy();
    expect(option.querySelector('.cs-caption').textContent).toBe('5-7 days');
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 4. Event delegation compatibility
// ══════════════════════════════════════════════════════════════

describe('Event delegation compatibility', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('change event bubbles from native select with data-action', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS, {
      'data-action': 'change-country',
      name: 'billing_country',
    });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });

    const spy = jest.fn();
    document.addEventListener('change', spy);

    cs.select({ value: 'DE', label: 'Germany' });

    expect(spy).toHaveBeenCalled();
    const event = spy.mock.calls[0][0];
    expect(event.target).toBe(select);
    expect(select.getAttribute('data-action')).toBe('change-country');
    cs.destroy();
    document.removeEventListener('change', spy);
  });

  test('change event works with jQuery-style delegation', () => {
    const wrapper = createSelectWrapper(PAYMENT_OPTIONS, {
      'data-action': 'change-payment',
      name: 'payment_type',
    });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });

    const matches = [];
    function delegatingHandler(e) {
      const target = e.target;
      if (target.matches && target.matches('[data-action="change-payment"]')) {
        matches.push({ value: target.value, action: target.getAttribute('data-action') });
      }
    }
    document.addEventListener('change', delegatingHandler);

    cs.select({ value: 'paypal', label: 'PayPal' });

    expect(matches.length).toBe(1);
    expect(matches[0].value).toBe('paypal');
    expect(matches[0].action).toBe('change-payment');
    cs.destroy();
    document.removeEventListener('change', delegatingHandler);
  });

  test('billing_country change dispatches from hidden select', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS, {
      'data-component': 'custom-selector',
      'data-action': 'change-country',
      name: 'billing_country',
    });
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });

    let capturedValue = null;
    document.addEventListener('change', function handler(e) {
      if (e.target.getAttribute('data-action') === 'change-country') {
        capturedValue = e.target.value;
      }
    });

    cs.select({ value: 'GB', label: 'United Kingdom', flag: 'gb' });

    expect(capturedValue).toBe('GB');
    cs.destroy();
  });
});

// ══════════════════════════════════════════════════════════════
// 5. Multiple selects lifecycle (like checkout initComponents)
// ══════════════════════════════════════════════════════════════

describe('Multiple selects lifecycle (checkout initComponents simulation)', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  function initCustomSelector(el) {
    if (el.__customSelector) return;
    const select = el.tagName === 'SELECT' ? el : el.querySelector('select');
    if (!select) return;
    const container = el.tagName === 'SELECT' ? el.parentNode : el;
    if (container.__customSelector) return;
    const inst = new CustomSelector(container, { enhancedSelect: select });
    container.__customSelector = inst;
    el.setAttribute('data-init', 'true');
  }

  function destroyCustomSelector(el) {
    const container = el.tagName === 'SELECT' ? el.parentNode : el;
    if (container.__customSelector) {
      container.__customSelector.destroy();
      container.__customSelector = null;
    }
    el.removeAttribute('data-init');
  }

  function initCheckoutComponents(root) {
    const nodes = root.querySelectorAll('[data-component]:not([data-init])');
    for (let i = 0; i < nodes.length; i++) {
      const el = nodes[i];
      const type = el.getAttribute('data-component');
      if (type === 'custom-selector') initCustomSelector(el);
      else el.setAttribute('data-init', 'true');
    }
  }

  function destroyCheckoutComponents(root) {
    const nodes = root.querySelectorAll('[data-init]');
    for (let i = 0; i < nodes.length; i++) {
      const el = nodes[i];
      const type = el.getAttribute('data-component');
      if (type === 'custom-selector') destroyCustomSelector(el);
      else el.removeAttribute('data-init');
    }
  }

  test('initCheckoutComponents initializes all selectors', () => {
    const checkout = document.createElement('div');
    checkout.className = 'checkout_wrapper';

    const countryWrapper = createSelectWrapper(COUNTRY_OPTIONS, {
      'data-component': 'custom-selector', name: 'billing_country',
    });
    checkout.appendChild(countryWrapper);

    const paymentWrapper = createSelectWrapper(PAYMENT_OPTIONS, {
      'data-component': 'custom-selector', name: 'payment_type',
    });
    checkout.appendChild(paymentWrapper);

    const monthWrapper = createSelectWrapper(CARD_MONTH_OPTIONS, {
      'data-component': 'custom-selector', name: 'card_month',
    });
    checkout.appendChild(monthWrapper);

    document.body.appendChild(checkout);

    initCheckoutComponents(checkout);

    expect(countryWrapper.__customSelector).toBeTruthy();
    expect(paymentWrapper.__customSelector).toBeTruthy();
    expect(monthWrapper.__customSelector).toBeTruthy();
    expect(countryWrapper.__customSelector.getValue()).toBe('US');

    destroyCheckoutComponents(checkout);

    expect(countryWrapper.__customSelector).toBeFalsy();
    expect(paymentWrapper.__customSelector).toBeFalsy();
    expect(monthWrapper.__customSelector).toBeFalsy();
  });

  test('init -> destroy -> re-init cycle (like AJAX reload)', () => {
    const checkout = document.createElement('div');
    checkout.className = 'checkout_wrapper';

    const wrapper = createSelectWrapper(COUNTRY_OPTIONS, {
      'data-component': 'custom-selector', name: 'billing_country',
    });
    checkout.appendChild(wrapper);
    document.body.appendChild(checkout);

    initCheckoutComponents(checkout);
    const inst1 = wrapper.__customSelector;
    expect(inst1).toBeTruthy();

    destroyCheckoutComponents(checkout);
    expect(wrapper.__customSelector).toBeFalsy();

    initCheckoutComponents(checkout);
    const inst2 = wrapper.__customSelector;
    expect(inst2).toBeTruthy();
    expect(inst2).not.toBe(inst1);

    destroyCheckoutComponents(checkout);
  });
});

// ══════════════════════════════════════════════════════════════
// 6. Error handling & edge cases
// ══════════════════════════════════════════════════════════════

describe('Error handling & edge cases', () => {
  afterEach(() => { document.body.innerHTML = ''; });

  test('handles empty select with placeholder option', () => {
    const wrapper = createSelectWrapper([
      { value: '', label: 'Select...', placeholder: true },
      { value: '1', label: 'Option 1' },
    ]);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(cs.getOptions().length).toBe(2);
    expect(cs.getValue()).toBeNull();
    cs.destroy();
  });

  test('does not double-init', () => {
    const wrapper = createSelectWrapper(COUNTRY_OPTIONS);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');

    const cs1 = new CustomSelector(wrapper, { enhancedSelect: select });
    wrapper.__customSelector = cs1;

    const spy = jest.fn();
    const cs2 = new CustomSelector(wrapper, { enhancedSelect: select });
    cs2.on('change', spy);
    cs2.select({ value: 'DE', label: 'Germany' });
    expect(cs2).toBeTruthy();
    cs1.destroy();
  });

  test('supports selects with no options', () => {
    const wrapper = createSelectWrapper([]);
    document.body.appendChild(wrapper);
    const select = wrapper.querySelector('select');
    const cs = new CustomSelector(wrapper, { enhancedSelect: select });
    expect(cs.getOptions()).toEqual([]);
    cs.destroy();
  });
});
