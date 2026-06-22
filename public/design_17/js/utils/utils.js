/**
  Browser utilities
================= **/

/* Check if JavaScript is enabled */
function checkJS() {
  document.documentElement.classList.remove('no-js');
}

/* Detect browser */
function detectBrowser() {
  const test = (regexp) => regexp.test(navigator.userAgent);
  const browser = (() => {
    switch (true) {
      case test(/edg/i):
        return 'edge';
      case test(/trident/i):
        return 'ie';
      case test(/firefox|fxios/i):
        return 'firefox';
      case test(/opr\//i):
        return 'opera';
      case test(/chrome|chromium|crios/i):
        return 'chrome';
      case test(/safari|AppleWebKit/i):
        return 'safari';
      default:
        return 'unknown';
    }
  })();

  document.documentElement.classList.add(`ua-${browser}`);
}

/* Check if browser supports .webp-images */
function checkWebp() {
  const webp = new Image();
  webp.src =
    'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';

  webp.onload = webp.onerror = () => {
    const isWebpSupported = webp.height === 2;
    if (!isWebpSupported) document.documentElement.classList.remove('webp');
  };
}

/* Check if browser supports dialog element */
export function checkDialog() {
  document.addEventListener('DOMContentLoaded', function () {
    const dialog = document.querySelector('dialog');
    try {
      dialog && dialog.close();
    } catch (e) {
      const head = document.getElementsByTagName('HEAD')[0];
      const link = document.createElement('link');
      const script = document.createElement('script');
      const dialogs = document.querySelectorAll('dialog');
      link.rel = 'stylesheet';
      link.type = 'text/css';
      link.href = 'vendor/dialog-polyfill/dialog-polyfill.min.css';
      script.src = 'vendor/dialog-polyfill/dialog-polyfill.min.js';
      head.append(link, script);
      script.addEventListener('load', () => {
        dialogs.forEach((dialog) => {
          dialogPolyfill.registerDialog(dialog);
        });
      });
    }
  });
}

export function initBrowserChecks() {
  checkJS();
  detectBrowser();
  checkWebp();
  checkDialog();
}

/**
  DOM utilities
============= **/

/* Disable a[href="#!"] links */
function disableEmptyLinks() {
  document.addEventListener('click', (e) => {
    if (e.target.matches('a[href="#!"]')) {
      e.preventDefault();
    }
  });
}

/* Disable CSS transitions during window resize */
function disableTransitionsOnResize(className = 'no-transitions', delay = 300) {
  const handleResize = debounce(() => {
    document.body.classList.remove(className);
  }, delay);

  window.addEventListener('resize', () => {
    document.body.classList.add(className);
    handleResize();
  });
}

export function applyInitialSettings() {
  disableEmptyLinks();
  disableTransitionsOnResize();
}

/**
  General utilities
================= **/

/* Debounce function to limit its execution rate */
export function debounce(func, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), delay);
  };
}

/* Get CSS property value */
export function getCssValue(element, property) {
  return window.getComputedStyle(element).getPropertyValue(property);
}

/**
  UI utilities
============ **/

/* Create overlay element */
export function createOverlay(className, parent = document.body) {
  const overlay = document.createElement('div');
  overlay.classList.add(className, 'overlay');
  parent.appendChild(overlay);
  return overlay;
}

/* Lock scrolling */
export function lockScrolling(enableLock, source, delay = 0) {
  const { documentElement: html, body } = document;
  const currentSource = body.dataset.lockSource;
  const hasSameSource = currentSource === source;
  const scrollY = window.scrollY;

  // Toggle lock state and document scrollbar
  if (!currentSource || hasSameSource) {
    body.classList.toggle('is-locked', enableLock);
    html.classList.toggle('has-scrollbar', body.scrollHeight > window.innerHeight && enableLock);
  }

  // Toggle lock source
  if (enableLock && !currentSource) {
    body.dataset.lockSource = source;
  } else if (!enableLock && hasSameSource) {
    body.removeAttribute('data-lock-source');
  }

  // Manage scroll offset
  if (enableLock && scrollY > 0) {
    body.style.setProperty('--top-body-offset', `-${scrollY}px`);
  } else if (hasSameSource) {
    const offset = Math.abs(parseInt(getCssValue(body, '--top-body-offset') || '0', 10));
    setTimeout(() => {
      body.style.removeProperty('--top-body-offset');
    }, delay);
    requestAnimationFrame(() => {
      window.scrollTo({ top: offset, left: 0, behavior: 'instant' });
    });
  }
}
