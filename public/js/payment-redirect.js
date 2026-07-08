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

    function openPaymentRedirect(target, type) {
        log('opening redirect tab', { type: type });

        var newTab = window.open('about:blank', '_blank');

        if (!newTab) {
            logError('popup blocked, falling back to same-tab redirect');
            if (type === 'url') {
                window.location.replace(target);
            } else {
                var formContainer = document.createElement('div');
                formContainer.innerHTML = target;
                document.body.appendChild(formContainer);
                var form3d = document.getElementById('form3d');
                if (form3d) {
                    form3d.submit();
                } else if (document.forms.length > 0) {
                    document.forms[document.forms.length - 1].submit();
                }
            }
            return;
        }

        var endpoint = (typeof paymentRedirectCreate !== 'undefined') ? paymentRedirectCreate : null;

        if (!endpoint) {
            logError('paymentRedirectCreate endpoint not defined');
            newTab.close();
            if (type === 'url') {
                window.location.replace(target);
            }
            return;
        }

        var formData = new FormData();
        formData.append('type', type);
        formData.append('target', target);

        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                if (data && data.redirect_url) {
                    log('token created, redirecting new tab', { url: data.redirect_url });
                    newTab.location.href = data.redirect_url;
                } else {
                    throw new Error('No redirect_url in response');
                }
            })
            .catch(function (error) {
                logError('failed to create token, falling back', { error: error.message });
                newTab.close();
                if (type === 'url') {
                    window.location.replace(target);
                } else {
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
            });
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
