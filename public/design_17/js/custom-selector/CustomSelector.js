const { computePosition, flip, offset, shift } = window.FloatingUIDOM || {};

const LOG_PREFIX = '[CustomSelector]';
const LOG_LEVEL = window.LOG_LEVEL || 'DEBUG';

function log(level, message, data) {
  const levels = { DEBUG: 0, INFO: 1, WARN: 2, ERROR: 3 };
  if (levels[level] >= (levels[LOG_LEVEL] || 0)) {
    const fn = level === 'ERROR' ? console.error : level === 'WARN' ? console.warn : console.log;
    // fn(`${LOG_PREFIX} ${message}`, data || '');
  }
}

class LRUCache extends Map {
  constructor(maxSize = 100) {
    super();
    this.maxSize = maxSize;
  }
  get(key) {
    if (!this.has(key)) return undefined;
    const value = super.get(key);
    this.delete(key);
    super.set(key, value);
    return value;
  }
  set(key, value) {
    if (this.has(key)) this.delete(key);
    else if (this.size >= this.maxSize) {
      this.delete(this.keys().next().value);
    }
    super.set(key, value);
    return this;
  }
}

class CustomSelector {
  constructor(element, options) {
    if (!options || typeof options !== 'object') {
      throw new Error('CustomSelector: options must be an object');
    }

    this.el = typeof element === 'string' ? document.querySelector(element) : element;
    if (!this.el) {
      log('ERROR', 'constructor: element not found', { element });
      throw new Error('CustomSelector: element not found');
    }

    if (options.dataSource?.type === 'ajax' && !options.dataSource.url) {
      throw new Error('CustomSelector: URL required for AJAX data source');
    }

    this._config = this._mergeDefaults(options);
    this.state = this._createState();
    this.dom = {};
    this._listeners = {};
    this._abortController = null;
    this._debounceTimer = null;
    this._cache = new LRUCache(100);
    this._uid = Date.now().toString(36) + Math.random().toString(36).slice(2, 6);

    log('INFO', 'init', {
      selectorType: this._config.selectorType,
      features: this._config.features,
    });

    this._build();
    this._notify('ready');
    if (this._config.callbacks.onReady) this._config.callbacks.onReady();
  }

  _mergeDefaults(userOptions) {
    const defaults = {
      selectorType: 'single',
      features: [],
      dataSource: { type: 'static', options: [] },
      styling: { theme: 'light', placement: 'bottom' },
      accessibility: true,
      performance: { virtualScroll: false, maxOptions: 1000 },
      callbacks: {},
      search: { minLength: 2, debounceTime: 300, caseSensitive: false },
      ajax: { cacheEnabled: true, retryAttempts: 3, timeout: 5000 },
      locale: {
        placeholder: 'Select...',
        search: 'Search...',
        clear: 'Clear',
        remove: 'Remove',
        noResults: 'Nothing found',
        loading: 'Loading...',
      },
    };

    this._nativeSelect = null;

    if (userOptions?.enhancedSelect instanceof HTMLSelectElement) {
      this._nativeSelect = userOptions.enhancedSelect;
    } else if (userOptions?.enhancedSelect && typeof userOptions.enhancedSelect === 'string') {
      this._nativeSelect = document.querySelector(userOptions.enhancedSelect);
    }

    if (this._nativeSelect) {
      if (!userOptions.dataSource) userOptions.dataSource = {};
      if (!userOptions.dataSource.options || userOptions.dataSource.options.length === 0) {
        userOptions.dataSource.options = this._parseOptionsFromNativeSelect();
        log('DEBUG', 'parsed options from native select', {
          total: userOptions.dataSource.options.length,
        });
      }
      if (!userOptions.dataSource.type) userOptions.dataSource.type = 'static';
      log('INFO', 'enhanced mode', {
        selectName: this._nativeSelect.name || this._nativeSelect.id,
        optionsCount: userOptions.dataSource.options.length,
      });
    }

    const merged = { ...defaults };
    for (const key of Object.keys(userOptions || {})) {
      if (userOptions[key] && typeof userOptions[key] === 'object' && !Array.isArray(userOptions[key])) {
        if (key === 'enhancedSelect') continue;
        merged[key] = { ...(defaults[key] || {}), ...userOptions[key] };
      } else {
        merged[key] = userOptions[key];
      }
    }
    return merged;
  }

  _createState() {
    const raw = {
      isOpen: false,
      isDisabled: false,
      isLoading: false,
      selected: this._config.selectorType === 'single' ? null : [],
      highlightedIndex: -1,
      searchQuery: '',
      filteredOptions: [],
      currentPage: 1,
    };
    const self = this;
    return new Proxy(raw, {
      get(target, prop) { return target[prop]; },
      set(target, prop, value) {
        const old = target[prop];
        if (old === value) { target[prop] = value; return true; }
        target[prop] = value;
        log('DEBUG', `state.${prop}`, { from: old, to: value });
        if (prop !== 'highlightedIndex' || value !== -1) {
          self._notify('stateChanged', { property: prop, value, oldValue: old });
        }
        return true;
      },
    });
  }

  _build() {
    this.el.classList.add('cs-container', `cs-${this._config.styling.theme}`);
    this.el.setAttribute('data-selector-ready', 'true');

    const searchHtml = this._config.features.includes('search')
      ? `<div class="cs-search">
          <input type="text" class="cs-search-input" placeholder="${this._getLocaleText('search')}" aria-label="${this._getLocaleText('search')}" autocomplete="off">
         </div>`
      : '';

    const uiHtml = `
      <div class="cs-control" role="combobox" aria-haspopup="listbox" aria-expanded="false" tabindex="0">
        <span class="cs-placeholder">${this._getPlaceholderText()}</span>
        <button type="button" class="cs-clear-btn" aria-label="${this._getLocaleText('clear')}" hidden>&times;</button>
        <span class="cs-arrow">
          <svg width="1.6rem" height="1.6rem" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </span>
      </div>
      <div class="cs-dropdown" role="listbox" hidden>
        ${searchHtml}
        <div class="cs-options" role="group"></div>
      </div>`;

    this.el.insertAdjacentHTML('afterbegin', uiHtml);

    if (this._nativeSelect) {
      this._nativeSelect.style.display = 'none';
    }

    this._cacheDom();
    this._bindEvents();
    this._setAriaAttributes();

    if (this._config.dataSource.type === 'static') {
      const hasExplicitSelection = this._nativeSelect
        ? Array.from(this._nativeSelect.options).some(o => o.hasAttribute('selected'))
        : false;
      const initialValue = hasExplicitSelection ? this._nativeSelect.value : '';
      this.state.filteredOptions = [...this._config.dataSource.options];
      this._renderOptions();
      if (initialValue && initialValue !== '') {
        const item = this.state.filteredOptions.find(o => String(o.value) === String(initialValue));
        if (item) {
          this.state.selected = item;
          this.dom.placeholder.textContent = item.label;
          this._updateClearButton();
          this._updateOpenerAsset();
        }
      }
    }

    if (this._config.selectorType !== 'single') {
      this._renderTags();
    }

    log('DEBUG', '_build: component built');

    if (this._nativeSelect) {
      this._nativeSelect.addEventListener('change', this._nativeChangeHandler = () => {
        this.syncFromNativeSelect();
      });
    }
  }

  _cacheDom() {
    this.dom = {
      control: this.el.querySelector('.cs-control'),
      placeholder: this.el.querySelector('.cs-placeholder'),
      arrow: this.el.querySelector('.cs-arrow'),
      dropdown: this.el.querySelector('.cs-dropdown'),
      options: this.el.querySelector('.cs-options'),
      clearBtn: this.el.querySelector('.cs-clear-btn'),
      searchInput: this.el.querySelector('.cs-search-input'),
    };
  }

  _bindEvents() {
    this.dom.control.addEventListener('click', (e) => {
      if (this.state.isDisabled) return;
      if (e.target.closest('.cs-clear-btn')) { this.clear(); return; }
      this.toggle();
    });

    document.addEventListener('click', this._clickOutsideHandler = (e) => {
      if (!this.el.contains(e.target)) this.close();
    });

    this.dom.options.addEventListener('click', (e) => {
      const optionEl = e.target.closest('.cs-option');
      if (!optionEl) return;
      const index = parseInt(optionEl.dataset.index, 10);
      const item = this.state.filteredOptions[index];
      if (!item || item.disabled) return;
      if (!this._isSelected(item)) { this.select(item); }
    });

    if (this.dom.searchInput) {
      this.dom.searchInput.addEventListener('input', (e) => {
        const query = e.target.value;
        this.state.searchQuery = query;
        if (this._debounceTimer) clearTimeout(this._debounceTimer);
        this._debounceTimer = setTimeout(() => {
          if (this._config.dataSource.type === 'ajax') {
            this._fetchData(query);
          } else {
            this._filterOptions(query);
          }
          this._notify('search', { query });
          if (this._config.callbacks.onSearch) this._config.callbacks.onSearch(query);
        }, this._config.search.debounceTime);
      });

      this.dom.searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') this.close();
      });
    }

    if (this._config.features.includes('keyboard-navigation')) {
      this._bindKeyboard();
    }
  }

  _bindKeyboard() {
    this.dom.control.addEventListener('keydown', (e) => {
      if (this.state.isDisabled) return;
      switch (e.key) {
        case 'Enter':
        case ' ':
          e.preventDefault();
          this.toggle();
          break;
        case 'Escape':
          this.close();
          break;
        case 'ArrowDown':
          e.preventDefault();
          this.open();
          this._highlightNext();
          break;
        case 'ArrowUp':
          e.preventDefault();
          this._highlightPrev();
          break;
        case 'Tab':
          this.close();
          break;
      }
    });

    this.dom.dropdown.addEventListener('keydown', (e) => {
      switch (e.key) {
        case 'ArrowDown':
          e.preventDefault();
          this._highlightNext();
          break;
        case 'ArrowUp':
          e.preventDefault();
          this._highlightPrev();
          break;
        case 'Enter':
          e.preventDefault();
          this._selectHighlighted();
          break;
        case 'Escape':
          this.close();
          break;
      }
    });
  }

  _setAriaAttributes() {
    if (!this._config.accessibility) return;
    this.dom.control.setAttribute('aria-disabled', 'false');
    this.dom.control.setAttribute('aria-label', this._getPlaceholderText());
    this.dom.control.setAttribute('aria-activedescendant', '');
    const listboxId = `cs-listbox-${Date.now()}`;
    this.dom.dropdown.id = listboxId;
    this.dom.control.setAttribute('aria-controls', listboxId);

    this._liveRegion = document.createElement('div');
    this._liveRegion.setAttribute('role', 'status');
    this._liveRegion.setAttribute('aria-live', 'polite');
    this._liveRegion.setAttribute('aria-atomic', 'true');
    this._liveRegion.className = 'cs-sr-only';
    this.el.appendChild(this._liveRegion);
  }

  _announce(message) {
    if (!this._liveRegion || !this._config.accessibility) return;
    this._liveRegion.textContent = '';
    requestAnimationFrame(() => {
      this._liveRegion.textContent = message;
    });
  }

  open() {
    if (this.state.isDisabled || this.state.isOpen) return;

    this._notify('beforeOpen');
    if (this._config.callbacks.beforeOpen) this._config.callbacks.beforeOpen();

    this.state.isOpen = true;
    this.dom.dropdown.hidden = false;
    this.dom.control.setAttribute('aria-expanded', 'true');
    this.el.classList.add('cs-open');

    this._positionDropdown();

    if (this.dom.searchInput) {
      setTimeout(() => this.dom.searchInput.focus(), 0);
    }

    log('INFO', 'open');
    this._notify('open');
    if (this._config.callbacks.onOpen) this._config.callbacks.onOpen();

    const count = this.state.filteredOptions.length;
    this._announce(count > 0 ? `${count} options available` : this._getLocaleText('noResults'));

    if (this._config.dataSource.type === 'ajax' && this.state.filteredOptions.length === 0) {
      this._fetchData('');
    }
  }

  _positionDropdown() {
    if (computePosition && this.dom.control && this.dom.dropdown) {
      const placement = this._config.styling.placement || 'bottom';
      computePosition(this.dom.control, this.dom.dropdown, {
        placement,
        middleware: [
          flip(),
          offset(4),
          shift({ padding: 8 }),
        ],
      }).then(({ x, y }) => {
        this.dom.dropdown.style.left = `${x}px`;
        this.dom.dropdown.style.top = `${y}px`;
        this.dom.dropdown.style.transform = 'none';
        this.dom.dropdown.style.width = '';
      }).catch(() => {
        this._resetDropdownPosition();
      });
    } else {
      this._resetDropdownPosition();
    }
  }

  _resetDropdownPosition() {
    this.dom.dropdown.style.left = '';
    this.dom.dropdown.style.top = '';
    this.dom.dropdown.style.transform = '';
    this.dom.dropdown.style.width = '';
  }

  close() {
    if (!this.state.isOpen) return;

    this.state.isOpen = false;
    this.dom.dropdown.hidden = true;
    this.dom.control.setAttribute('aria-expanded', 'false');
    this.el.classList.remove('cs-open');
    this.state.highlightedIndex = -1;

    log('DEBUG', 'close');
    this._notify('close');
    if (this._config.callbacks.onClose) this._config.callbacks.onClose();

    this._notify('afterClose');
    if (this._config.callbacks.afterClose) this._config.callbacks.afterClose();
  }

  toggle() {
    this.state.isOpen ? this.close() : this.open();
  }

  select(item) {
    if (item.disabled) return;

    this._notify('beforeSelect', { item });
    if (this._config.callbacks.beforeSelect) this._config.callbacks.beforeSelect(item);

    log('INFO', 'select', { value: item.value, label: item.label });

    if (this._config.selectorType === 'single') {
      const oldValue = this.state.selected;
      this.state.selected = item;
      this.dom.placeholder.textContent = item.label;
      this.dom.placeholder.style.color = '';
      this.close();
      this._notify('change', { value: item, oldValue });
      if (this._config.callbacks.onChange) this._config.callbacks.onChange(item);
    } else {
      if (this.state.selected.some(s => s.value === item.value)) return;
      this.state.selected.push(item);
      this._renderTags();
      this._updatePlaceholderVisibility();
      this._notify('change', { value: [...this.state.selected] });
      if (this._config.callbacks.onChange) this._config.callbacks.onChange([...this.state.selected]);
    }

    this._notify('select', { item });
    this._updateClearButton();
    this._updateOpenerAsset();
    this._renderOptions();
    this._syncNativeSelect();
    this._announce(`${item.label} selected`);
  }

  _setActiveDescendant() {
    if (!this._config.accessibility) return;
    const highlighted = this.dom.options.querySelector('.cs-option--highlighted');
    this.dom.control.setAttribute('aria-activedescendant', highlighted ? highlighted.id : '');
  }

  deselect(item) {
    log('INFO', 'deselect', { value: item.value, label: item.label });

    if (this._config.selectorType === 'single') {
      this.state.selected = null;
      this.dom.placeholder.textContent = this._getPlaceholderText();
      this._notify('change', { value: null });
      if (this._config.callbacks.onChange) this._config.callbacks.onChange(null);
    } else {
      this.state.selected = this.state.selected.filter(s => s.value !== item.value);
      this._renderTags();
      this._updatePlaceholderVisibility();
      this._notify('change', { value: [...this.state.selected] });
      if (this._config.callbacks.onChange) this._config.callbacks.onChange([...this.state.selected]);
    }

    this._notify('deselect', { item });
    if (this._config.callbacks.afterDeselect) this._config.callbacks.afterDeselect(item);
    this._updateClearButton();
    this._updateOpenerAsset();
    this._renderOptions();
    this._syncNativeSelect();
  }

  getValue() {
    if (this._config.selectorType === 'single') {
      return this.state.selected ? this.state.selected.value : null;
    }
    return this.state.selected.map(s => s.value);
  }

  setValue(value) {
    log('DEBUG', 'setValue', { value });
    if (this._config.selectorType === 'single') {
      const item = this.state.filteredOptions.find(o => o.value === value);
      if (item) { this.clear(); this.select(item); }
    } else {
      const items = this.state.filteredOptions.filter(o => value.includes(o.value));
      this.clear();
      items.forEach(item => this.select(item));
    }
  }

  getOptions() {
    return [...this.state.filteredOptions];
  }

  setOptions(options) {
    log('DEBUG', 'setOptions', { count: options.length });
    this.state.filteredOptions = [...options];
    this._renderOptions();
  }

  clear() {
    log('DEBUG', 'clear');
    if (this._config.selectorType === 'single') {
      this.state.selected = null;
    } else {
      this.state.selected = [];
      this._renderTags();
    }
    this.dom.placeholder.textContent = this._getPlaceholderText();
    this.dom.placeholder.style.color = '';
    this._updateClearButton();
    this._updatePlaceholderVisibility();
    if (this.dom.searchInput) {
      this.dom.searchInput.value = '';
      this.state.searchQuery = '';
      if (this._config.dataSource.type === 'static') {
        this.state.filteredOptions = [...this._config.dataSource.options];
        this._renderOptions();
      }
    }
    this._notify('change', { value: null });
    if (this._config.callbacks.onChange) this._config.callbacks.onChange(null);
    this._syncNativeSelect();
  }

  enable() {
    log('DEBUG', 'enable');
    this.state.isDisabled = false;
    this.el.classList.remove('cs-disabled');
    this.dom.control.setAttribute('aria-disabled', 'false');
    this.dom.control.tabIndex = 0;
  }

  disable() {
    log('DEBUG', 'disable');
    this.state.isDisabled = true;
    this.el.classList.add('cs-disabled');
    this.dom.control.setAttribute('aria-disabled', 'true');
    this.dom.control.tabIndex = -1;
    this.close();
  }

  on(event, callback) {
    if (!this._listeners[event]) this._listeners[event] = [];
    this._listeners[event].push(callback);
    return this;
  }

  off(event, callback) {
    if (!this._listeners[event]) return this;
    this._listeners[event] = this._listeners[event].filter(cb => cb !== callback);
    return this;
  }

  _notify(event, data) {
    if (this._listeners[event]) {
      this._listeners[event].forEach(cb => {
        try { cb(data); } catch (e) { log('ERROR', `_notify: listener error for "${event}"`, e); }
      });
    }
  }

  destroy() {
    log('INFO', 'destroy');
    this._notify('beforeDestroy');
    if (this._config.callbacks.beforeDestroy) this._config.callbacks.beforeDestroy();
    this.close();
    if (this._debounceTimer) { clearTimeout(this._debounceTimer); this._debounceTimer = null; }
    if (this._abortController) { this._abortController.abort(); this._abortController = null; }
    if (this._clickOutsideHandler) { document.removeEventListener('click', this._clickOutsideHandler); }
    if (this._nativeSelect) {
      this._nativeSelect.style.display = '';
      if (this._nativeChangeHandler) { this._nativeSelect.removeEventListener('change', this._nativeChangeHandler); }
      log('DEBUG', 'destroy: restore native select visibility');
    }
    // Remove custom UI elements, keep native select in DOM
    const controlEl = this.el.querySelector('.cs-control');
    const dropdownEl = this.el.querySelector('.cs-dropdown');
    const liveRegionEl = this.el.querySelector('.cs-sr-only');
    if (controlEl) controlEl.remove();
    if (dropdownEl) dropdownEl.remove();
    if (liveRegionEl) liveRegionEl.remove();

    this.el.removeAttribute('data-selector-ready');
    this.el.classList.remove('cs-container', 'cs-open', 'cs-disabled', 'cs-loading');
    this._liveRegion = null;
    this._notify('destroy');
    if (this._config && this._config.callbacks.onDestroy) this._config.callbacks.onDestroy();
    this._listeners = {};
    this.state = null;
    this.dom = {};
    this._config = null;
    this._cache.clear();
    log('DEBUG', 'destroy: complete');
  }

  _parseOptionsFromNativeSelect() {
    const options = [];
    for (let i = 0; i < this._nativeSelect.options.length; i++) {
      const opt = this._nativeSelect.options[i];
      const item = {
        value: opt.value,
        label: opt.text,
        disabled: opt.disabled,
      };
      const flag = opt.getAttribute('data-flag');
      if (flag) item.flag = flag;
      const asset = opt.getAttribute('data-asset');
      if (asset) item.asset = asset;
      const labelAttr = opt.getAttribute('data-label');
      if (labelAttr) item.dataLabel = labelAttr;
      const caption = opt.getAttribute('data-caption');
      if (caption) item.caption = caption;
      const image = opt.getAttribute('data-image');
      if (image) item.image = image;
      options.push(item);
    }
    return options;
  }

  _syncNativeSelect() {
    if (!this._nativeSelect) return;
    const value = this.getValue();
    const nativeVal = value == null ? '' : (Array.isArray(value) ? value.join(',') : String(value));
    if (this._nativeSelect.value !== nativeVal) {
      this._nativeSelect.value = nativeVal;
      this._nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
      log('DEBUG', 'native sync', { value: nativeVal });
    }
  }

  _updateOpenerAsset() {
    if (!this._nativeSelect || !this.dom.placeholder) return;
    const item = this.state.selected;
    const existing = this.dom.control.querySelector('.cs-opener-asset');
    if (item?.asset) {
      if (!existing) {
        const img = document.createElement('img');
        img.className = 'cs-opener-asset';
        img.src = item.asset;
        img.alt = '';
        img.style.cssText = 'width:24px;height:16px;object-fit:contain;display:inline-block;vertical-align:middle;margin-right:6px;pointer-events:none;';
        this.dom.control.insertBefore(img, this.dom.placeholder);
      } else {
        existing.src = item.asset;
        existing.style.display = '';
      }
    } else if (existing) {
      existing.style.display = 'none';
    }
  }

  getNativeSelect() {
    return this._nativeSelect;
  }

  syncFromNativeSelect() {
    if (!this._nativeSelect) return;
    const val = this._nativeSelect.value;
    log('DEBUG', 'syncFromNativeSelect', { value: val });
    if (this._config.selectorType === 'single') {
      if (!val) { this.clear(); return; }
      const item = this.state.filteredOptions.find(o => String(o.value) === String(val));
      if (item && (!this.state.selected || this.state.selected.value !== item.value)) {
        this.select(item);
      }
    }
  }

  _renderOptions() {
    if (this._config.performance.virtualScroll && this.state.filteredOptions.length > this._config.performance.maxOptions) {
      return this._renderVirtualScroll();
    }

    const options = this.state.filteredOptions;

    if (options.length === 0) {
      this.dom.options.innerHTML = `
        <div class="cs-no-results">
          <svg class="cs-no-results-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <p>${this._getLocaleText('noResults')}</p>
        </div>`;
      if (this._config.accessibility) {
        this.dom.control.setAttribute('aria-activedescendant', '');
      }
      return;
    }

    const existingCount = this.dom.options.children.length;
    const fragment = document.createDocumentFragment();

    options.forEach((item, index) => {
      let optionEl;

      if (index < existingCount) {
        optionEl = this.dom.options.children[index];
        optionEl.className = 'cs-option';
        optionEl.innerHTML = '';
      } else {
        optionEl = document.createElement('div');
        optionEl.className = 'cs-option';
      }

      optionEl.dataset.index = index;
      optionEl.setAttribute('role', 'option');
      const optionId = `cs-opt-${this._uid}-${index}`;
      optionEl.id = optionId;
      optionEl.setAttribute('aria-selected', this._isSelected(item) ? 'true' : 'false');

      if (item.disabled) optionEl.classList.add('cs-option--disabled');
      if (this._isSelected(item)) optionEl.classList.add('cs-option--selected');
      const isHighlighted = index === this.state.highlightedIndex;
      if (isHighlighted) {
        optionEl.classList.add('cs-option--highlighted');
        if (this._config.accessibility) {
          this.dom.control.setAttribute('aria-activedescendant', optionId);
        }
      }

      if (this._config.features.includes('custom-rendering') && this._config.callbacks.onRenderOption) {
        optionEl.innerHTML = this._config.callbacks.onRenderOption(item);
      } else if (item.flag || item.asset || item.dataLabel || item.caption) {
        const flagHtml = item.flag ? `<span class="cs-flag" data-flag="${item.flag}"></span>` : '';
        const assetHtml = item.asset ? `<img class="cs-asset-icon" src="${item.asset}" alt="" width="24" height="16">` : '';
        const labelHtml = item.dataLabel ? `<span class="cs-label">${item.dataLabel}</span>` : '';
        const captionHtml = item.caption ? `<span class="cs-caption">${item.caption}</span>` : '';
        const labelText = item.label || item.text || '';
        const displayText = this.state.searchQuery
          ? this._highlightMatch(labelText, this.state.searchQuery)
          : this._escapeHtml(labelText);
        optionEl.innerHTML = `
          <span class="cs-option-wrapper">
            ${flagHtml}${assetHtml}
            <span class="cs-option-content">
              ${labelHtml}${displayText}
              ${captionHtml}
            </span>
          </span>`;
      } else {
        optionEl.innerHTML = this.state.searchQuery
          ? this._highlightMatch(item.label, this.state.searchQuery)
          : this._escapeHtml(item.label);
      }

      if (index >= existingCount) {
        fragment.appendChild(optionEl);
      }
    });

    while (this.dom.options.children.length > options.length) {
      this.dom.options.removeChild(this.dom.options.lastChild);
    }

    if (fragment.children.length) {
      this.dom.options.appendChild(fragment);
    }

    if (this.state.highlightedIndex === -1 && this._config.accessibility) {
      this.dom.control.setAttribute('aria-activedescendant', '');
    }

    log('DEBUG', '_renderOptions', { count: options.length, reused: Math.min(existingCount, options.length) });
  }

  _renderTags() {
    if (this._config.selectorType === 'single') return;
    let tagsContainer = this.el.querySelector('.cs-tags');
    if (!tagsContainer) {
      tagsContainer = document.createElement('div');
      tagsContainer.className = 'cs-tags';
      this.dom.control.insertBefore(tagsContainer, this.dom.placeholder);
    }
    tagsContainer.innerHTML = this.state.selected
      .map(item => `<span class="cs-tag" data-value="${item.value}">
        <span class="cs-tag-label">${this._escapeHtml(item.label)}</span>
        <button type="button" class="cs-tag-remove" aria-label="${this._getLocaleText('remove')} ${this._escapeHtml(item.label)}" data-action="remove-tag">&times;</button>
      </span>`)
      .join('');
    tagsContainer.querySelectorAll('[data-action="remove-tag"]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const tag = btn.closest('.cs-tag');
        const item = this.state.selected.find(s => String(s.value) === tag.dataset.value);
        if (item) this.deselect(item);
      });
    });
  }

  _updatePlaceholderVisibility() {
    if (!this.dom.placeholder) return;
    const hasSelection = this._config.selectorType === 'single'
      ? this.state.selected !== null
      : this.state.selected.length > 0;
    this.dom.placeholder.style.display = hasSelection && this._config.selectorType !== 'single' ? 'none' : '';
  }

  _updateClearButton() {
    if (!this.dom.clearBtn) return;
    if (this.el.hasAttribute('data-no-clear')) {
      this.dom.clearBtn.hidden = true;
      return;
    }
    const hasSelection = this._config.selectorType === 'single'
      ? this.state.selected !== null
      : this.state.selected.length > 0;
    this.dom.clearBtn.hidden = !hasSelection;
  }

  _highlightMatch(text, query) {
    if (!text || !query) return this._escapeHtml(text || '');
    const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const flags = this._config.search.caseSensitive ? 'g' : 'gi';
    const regex = new RegExp(`(${escaped})`, flags);
    const safeText = this._escapeHtml(text + '');
    return safeText.replace(regex, '<mark class="cs-highlight">$1</mark>');
  }

  _escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  _renderVirtualScroll() {
    const items = this.state.filteredOptions;
    const visibleCount = Math.min(items.length, this._config.performance.maxOptions);
    const fragment = document.createDocumentFragment();
    for (let i = 0; i < visibleCount; i++) {
      const item = items[i];
      const optionEl = document.createElement('div');
      optionEl.className = 'cs-option';
      optionEl.dataset.index = i;
      optionEl.setAttribute('role', 'option');
      optionEl.setAttribute('aria-selected', this._isSelected(item) ? 'true' : 'false');
      optionEl.textContent = item.label || item.text || '';
      fragment.appendChild(optionEl);
    }
    this.dom.options.innerHTML = '';
    this.dom.options.appendChild(fragment);
  }

  _highlightNext() {
    const max = this.state.filteredOptions.length - 1;
    if (this.state.highlightedIndex < max) {
      this.state.highlightedIndex++;
      this._scrollToHighlighted();
      this._renderOptions();
    }
  }

  _highlightPrev() {
    if (this.state.highlightedIndex > 0) {
      this.state.highlightedIndex--;
      this._scrollToHighlighted();
      this._renderOptions();
    }
  }

  _scrollToHighlighted() {
    const highlighted = this.dom.options.children[this.state.highlightedIndex];
    if (highlighted) highlighted.scrollIntoView({ block: 'nearest' });
  }

  _selectHighlighted() {
    const item = this.state.filteredOptions[this.state.highlightedIndex];
    if (item) this.select(item);
  }

  _isSelected(item) {
    if (this._config.selectorType === 'single') {
      return this.state.selected && this.state.selected.value === item.value;
    }
    return this.state.selected.some(s => s.value === item.value);
  }

  _getPlaceholderText() {
    return this.el.dataset.placeholder || this._getLocaleText('placeholder');
  }

  _getLocaleText(key) {
    return this._config.locale[key] || '';
  }

  _showLoading() {
    this.state.isLoading = true;
    this.el.classList.add('cs-loading');
  }

  _hideLoading() {
    this.state.isLoading = false;
    this.el.classList.remove('cs-loading');
  }

  async _fetchData(query) {
    this._showLoading();
    if (this._abortController) this._abortController.abort();
    this._abortController = new AbortController();
    const { url, method = 'GET', headers = {} } = this._config.dataSource;
    const cacheKey = `${method}:${url}:${query}`;

    if (this._config.ajax.cacheEnabled && this._cache.has(cacheKey)) {
      const data = this._cache.get(cacheKey);
      this._handleAjaxResponse(data);
      this._hideLoading();
      return;
    }

    for (let attempt = 1; attempt <= this._config.ajax.retryAttempts; attempt++) {
      try {
        const controller = this._abortController;
        const timeoutPromise = new Promise((_, reject) => {
          setTimeout(() => reject(new Error('Timeout')), this._config.ajax.timeout);
        });
        const fetchPromise = fetch(`${url}?q=${encodeURIComponent(query)}`, {
          method,
          headers: { 'Content-Type': 'application/json', ...headers },
          signal: controller.signal,
        });
        const response = await Promise.race([fetchPromise, timeoutPromise]);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        this._handleAjaxResponse(data);
        if (this._config.ajax.cacheEnabled) this._cache.set(cacheKey, data);
        this._hideLoading();
        return;
      } catch (error) {
        if (error.name === 'AbortError') { this._hideLoading(); return; }
        if (attempt === this._config.ajax.retryAttempts) {
          this._handleAjaxError(error);
          this._hideLoading();
        }
      }
    }
  }

  _handleAjaxResponse(data) {
    const items = Array.isArray(data) ? data : (data.items || data.data || data.results || []);
    this.state.filteredOptions = items;
    this.state.highlightedIndex = -1;
    this._renderOptions();
  }

  _handleAjaxError(error) {
    log('ERROR', '_fetchData: failed', error);
    this._notify('error', { error });
    if (this._config.callbacks.onError) this._config.callbacks.onError(error);
    this.dom.options.innerHTML = `
      <div class="cs-error-message">
        <p>Failed to load data</p>
        <button class="cs-retry-btn">Retry</button>
      </div>`;
    const retryBtn = this.dom.options.querySelector('.cs-retry-btn');
    if (retryBtn) {
      retryBtn.addEventListener('click', () => { this._fetchData(this.state.searchQuery); });
    }
  }

  _filterOptions(query) {
    if (!query || query.length < this._config.search.minLength) {
      this.state.filteredOptions = [...this._config.dataSource.options];
    } else {
      const q = this._config.search.caseSensitive ? query : query.toLowerCase();
      this.state.filteredOptions = this._config.dataSource.options.filter(item => {
        const searchText = item.label || item.text || '';
        const label = this._config.search.caseSensitive ? searchText : searchText.toLowerCase();
        return label.includes(q);
      });
    }
    this.state.highlightedIndex = -1;
    this._renderOptions();
  }
}

export default CustomSelector;
