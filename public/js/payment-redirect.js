(function () {
    'use strict';

    var PAYMENT_COMPLETED_EVENT = 'payment:completed';

    function log() {
        if (typeof console !== 'undefined' && console.log) {
            console.log.apply(console, ['[payment-redirect]'].concat(Array.prototype.slice.call(arguments)));
        }
    }

    function logError() {
        if (typeof console !== 'undefined' && console.error) {
            console.error.apply(console, ['[payment-redirect]'].concat(Array.prototype.slice.call(arguments)));
        }
    }

    function openPaymentRedirect(target, type, redirectUrl) {
        log('opening redirect tab', { type: type, hasPrebuiltToken: !!redirectUrl });

        if (redirectUrl) {
            log('using pre-built token', { url: redirectUrl });
            var newTab = window.open('about:blank', '_blank');

            if (!newTab) {
                logError('popup blocked, same-tab redirect');
                window.location.replace(redirectUrl);
                return;
            }

            newTab.location.href = redirectUrl;
            return;
        }

        logError('no redirect_url provided, falling back to direct redirect');
        if (type === 'url') {
            if (target) {
                window.location.replace(target);
            } else {
                logError('target url is also empty');
                alert('Something went wrong, please reload the page and try again.');
            }
        } else {
            if (!target) {
                logError('no target provided for form submission');
                alert('Something went wrong, please reload the page and try again.');
                return;
            }
            var container = document.createElement('div');
            container.innerHTML = target;
            document.body.appendChild(container);
            var form = document.getElementById('form3d');
            if (form) {
                form.submit();
            } else if (document.forms.length > 0) {
                document.forms[document.forms.length - 1].submit();
            }
        }
    }

    function initCheckoutListener() {
        if (typeof CrossTabBus === 'undefined') {
            log('CrossTabBus not available, listener disabled');
            return;
        }

        CrossTabBus.on(PAYMENT_COMPLETED_EVENT, function (payload) {
            log('cross-tab event received', payload);
            if (payload && payload.url) {
                window.location.href = payload.url;
            }
        });

        log('checkout listener initialized');
    }

    function initResultEmitter() {
        if (typeof CrossTabBus === 'undefined') {
            log('CrossTabBus not available, emitter disabled');
            return;
        }

        var status = 'success';
        if (document.body && document.body.dataset.paymentStatus) {
            status = document.body.dataset.paymentStatus;
        }

        CrossTabBus.emit(PAYMENT_COMPLETED_EVENT, {
            url: window.location.href,
            status: status
        });

        log('result emitted', { url: window.location.href, status: status });
    }

    function init() {
        var pageType = '';
        if (document.body && document.body.dataset.page) {
            pageType = document.body.dataset.page;
        }

        if (pageType === 'checkout') {
            initCheckoutListener();
        } else if (pageType === 'payment-result') {
            initResultEmitter();
        }
    }

    window.openPaymentRedirect = openPaymentRedirect;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
