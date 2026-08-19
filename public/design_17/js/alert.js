(function ($) {
    'use strict';

    var log = {
        error: function () { console.error.apply(console, ['[alert]'].concat([].slice.call(arguments))); },
        warn:  function () { console.warn.apply(console,  ['[alert]'].concat([].slice.call(arguments))); },
        info:  function () { console.info.apply(console,  ['[alert]'].concat([].slice.call(arguments))); },
        debug: function () { console.debug.apply(console, ['[alert]'].concat([].slice.call(arguments))); }
    };

    var CONTAINER_ID = 'legacy-alert-container';

    function escapeHtml(str) {
        var map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
        return String(str).replace(/[&<>"']/g, function(m){return map[m];});
    }

    function getContainer() {
        var c = document.getElementById(CONTAINER_ID);
        if (c) return c;
        c = document.createElement('div');
        c.id = CONTAINER_ID;
        c.setAttribute('data-component', 'alert-container');
        document.body.appendChild(c);
        log.debug('container created');
        return c;
    }

    window.LegacyUI = window.LegacyUI || {};

    window.LegacyUI.alert = function (opts) {
        if (typeof opts === 'string') opts = { message: opts };
        var type = opts.type || 'info';
        var message = opts.message || '';
        var duration = opts.duration || 0;
        var onClose = typeof opts.onClose === 'function' ? opts.onClose : null;

        var container = getContainer();
        var id = 'alert-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8);

        var iconMap = {
            info: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
            success: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            warning: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            error: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>'
        };
        var icon = iconMap[type] || iconMap.info;
        var closeSvg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';

        var html = '<div class="legacy-alert legacy-alert--' + type + '" id="' + id + '" role="alert">'
            + '<span class="legacy-alert__icon">' + icon + '</span>'
            + '<span class="legacy-alert__text">' + escapeHtml(message) + '</span>'
            + '<button class="legacy-alert__close" data-alert-close="' + id + '" type="button" aria-label="Close">' + closeSvg + '</button>'
            + '</div>';

        container.insertAdjacentHTML('beforeend', html);

        requestAnimationFrame(function () {
            var el = document.getElementById(id);
            if (el) el.classList.add('is-visible');
        });

        log.info('alert shown', { type: type, message: message, id: id });

        if (duration > 0) {
            setTimeout(function () {
                removeAlert(id, onClose);
            }, duration);
        }

        return id;
    };

    window.LegacyUI.showAlert = window.LegacyUI.alert;

    function removeAlert(id, onClose) {
        var el = document.getElementById(id);
        if (!el) return;
        el.classList.remove('is-visible');
        el.classList.add('is-hiding');
        var remove = function () {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
                log.debug('alert removed', { id: id });
            }
            if (typeof onClose === 'function') onClose();
        };
        var done = false;
        var transitionEnd = function () { if (!done) { done = true; remove(); } };
        el.addEventListener('transitionend', transitionEnd, { once: true });
        setTimeout(function () { if (!done) { done = true; remove(); } }, 400);
    }

    $(document).on('click', '[data-alert-close]', function () {
        var id = this.getAttribute('data-alert-close');
        removeAlert(id);
    });

    function initAlerts(root) {
        var container = document.getElementById(CONTAINER_ID);
        if (!container) return;
        log.debug('initAlerts: container exists', { alerts: container.children.length });
    }

    function destroyAlerts() {
        var container = document.getElementById(CONTAINER_ID);
        if (!container) return;
        while (container.firstChild) {
            container.removeChild(container.firstChild);
        }
        log.debug('destroyAlerts: all alerts cleared');
    }

    initAlerts();

    window.LegacyUI.initAlerts = initAlerts;
    window.LegacyUI.destroyAlerts = destroyAlerts;

})(jQuery);
