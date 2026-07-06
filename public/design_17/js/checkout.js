(function ($) {
    'use strict';

    var LEVELS = { error: 0, warn: 1, info: 2, debug: 3 };
    var currentLevel = (function () {
        var v = String(window.CHECKOUT17_LOG_LEVEL || 'debug').toLowerCase();
        return LEVELS[v] != null ? LEVELS[v] : 3;
    })();

    function levelOf(name) { return LEVELS[name] != null ? LEVELS[name] : 0; }

    function tag(args, prefix) {
        var a = [prefix || '[checkout17]'];
        return a.concat([].slice.call(args));
    }

    var log = {
        error: function () { if (currentLevel >= levelOf('error')) console.error.apply(console, tag(arguments)); },
        warn: function () { if (currentLevel >= levelOf('warn')) console.warn.apply(console, tag(arguments)); },
        info: function () { if (currentLevel >= levelOf('info')) console.info.apply(console, tag(arguments)); },
        debug: function () { if (currentLevel >= levelOf('debug')) console.debug.apply(console, tag(arguments)); }
    };

    function routes() { return window.checkoutRoutes || {}; }
    function config() { return window.checkoutConfig || {}; }

    function initCustomSelector(el) {
        if (typeof window.CustomSelector === 'undefined') return;
        var select = el.tagName === 'SELECT' ? el : el.querySelector('select');
        if (!select) return;
        var container = el.tagName === 'SELECT' ? el.parentNode : el;
        if (container.__customSelector) return;
        var locale = (window.checkoutConfig && window.checkoutConfig.selectorLocale) || {};
        var options = {
            enhancedSelect: select,
            features: ['keyboard-navigation', 'accessibility'],
            dataSource: { type: 'static', options: [] },
            locale: locale,
        };
        var action = select.getAttribute('data-action');
        if (action === 'change-country' || action === 'change-payment' || select.hasAttribute('data-search')) {
            options.features.push('search');
        }
        try {
            var inst = new window.CustomSelector(container, options);
            container.__customSelector = inst;
            log.debug('initCustomSelector', {
                selectName: select.name || select.id,
                optionsCount: select.options.length,
                locale: locale
            });
        } catch (e) {
            log.error('initCustomSelector error', { error: e.message });
        }
    }

    function destroyCustomSelector(el) {
        var container = el.tagName === 'SELECT' ? el.parentNode : el;
        if (container.__customSelector) {
            try {
                container.__customSelector.destroy();
            } catch (e) {}
            container.__customSelector = null;
        }
    }

    function initIntlTel(input) {
        if (typeof intlTelInput === 'undefined') return;
        if (input.__intlTel) return;
        var countryIsoInput = document.getElementById('country_iso');
        var initialCountryInput = document.getElementById('initial_country');
        var onlyCountries = [];
        try {
            onlyCountries = JSON.parse(countryIsoInput ? countryIsoInput.value : '[]');
        } catch (e) { onlyCountries = []; }
        var initialCountry = initialCountryInput ? initialCountryInput.value : 'us';
        var utils = config().intlTelUtils || '';
        var iti = intlTelInput(input, {
            utilsScript: utils,
            useFullscreenPopup: false,
            showSelectedDialCode: true,
            initialCountry: initialCountry,
            onlyCountries: onlyCountries
        });
        input.__intlTel = iti;
    }

    function destroyIntlTel(input) {
        if (input.__intlTel) {
            try { if (typeof input.__intlTel.destroy === 'function') input.__intlTel.destroy(); } catch (e) {}
            input.__intlTel = null;
        }
    }

    function initSwiper(el) {
        if (typeof Swiper === 'undefined') return;
        if (el.__swiper) return;
        var opts = {};
        try { var d = el.getAttribute('data-swiper-options'); if (d) opts = JSON.parse(d); } catch (e) {}
        el.__swiper = new Swiper(el, opts);
    }

    function destroySwiper(el) {
        if (el.__swiper) {
            try { el.__swiper.destroy(true, true); } catch (e) {}
            el.__swiper = null;
        }
    }



    function initCheckoutComponents(root) {
        root = root || document;
        var nodes = root.querySelectorAll('[data-component]:not([data-init])');
        var count = 0;
        for (var i = 0; i < nodes.length; i++) {
            var el = nodes[i];
            var type = el.getAttribute('data-component');
            try {
                switch (type) {
                    case 'custom-selector': initCustomSelector(el); break;
                    case 'intl-tel': initIntlTel(el); break;
                    case 'swiper': initSwiper(el); break;

                    default: break;
                }
            } catch (e) {
                log.error('init component failed', { type: type, error: e.message });
            }
            el.setAttribute('data-init', 'true');
            count++;
        }
        log.debug('initCheckoutComponents', { count: count });
    }

    function destroyCheckoutComponents(root) {
        root = root || document;
        var nodes = root.querySelectorAll('[data-init]');
        var count = 0;
        for (var i = 0; i < nodes.length; i++) {
            var el = nodes[i];
            var type = el.getAttribute('data-component');
            try {
                switch (type) {
                    case 'custom-selector': destroyCustomSelector(el); break;
                    case 'intl-tel': destroyIntlTel(el); break;
                    case 'swiper': destroySwiper(el); break;

                    default: break;
                }
            } catch (e) {
                log.error('destroy component failed', { type: type, error: e.message });
            }
            el.removeAttribute('data-init');
            count++;
        }
        log.debug('destroyCheckoutComponents', { count: count });
    }

    function renderCheckout(html) {
        log.debug('renderCheckout start', { htmlLen: html ? html.length : 0 });
        var $w = $('.checkout_wrapper');
        if (!$w.length) {
            log.error('renderCheckout: .checkout_wrapper not found');
            return;
        }
        destroyCheckoutComponents($w[0]);
        $w.html(html);
        initCheckoutComponents($w[0]);
        initCryptoState();
        if (cryptoTimerInterval && !$w[0].querySelector('#paid')) {
            clearInterval(cryptoTimerInterval);
            cryptoTimerInterval = null;
            log.debug('crypto timer stopped (left crypto)');
        }
        var paySelect = $w[0].querySelector('[data-action="change-payment"]');
        if (paySelect) $(paySelect).data('prev-value', paySelect.value);
        document.body.classList.add('loaded_hiding');
        window.setTimeout(function () {
            document.body.classList.add('loaded');
            document.body.classList.remove('loaded_hiding');
        }, 500);
        log.debug('renderCheckout done');
    }

    function clearFieldErrors() {
        var errs = document.querySelectorAll('.form__field.has-error, .text-field.has-error');
        for (var i = 0; i < errs.length; i++) errs[i].classList.remove('has-error');
        var popups = document.querySelectorAll('[data-error-for].show, .poopuptext.show');
        for (var j = 0; j < popups.length; j++) popups[j].classList.remove('show');
        var popups2 = document.querySelectorAll('.poopuptext');
        for (var k = 0; k < popups2.length; k++) popups2[k].classList.remove('show');
    }

    function showFieldErrors(errors) {
        clearFieldErrors();
        (errors || []).forEach(function (e) {
            var field = e.field || e.key;
            var popup = document.querySelector('[data-error-for="' + field + '"]');
            if (popup) {
                popup.classList.add('show');
                var wrap = popup.closest('.form__field') || popup.closest('.text-field');
                if (wrap) wrap.classList.add('has-error');
            }
        });
        log.warn('field errors', { count: (errors || []).length, fields: (errors || []).map(function (e) { return e.field; }) });
    }

    function validateCheckoutForm() {
        var required = ['phone', 'email', 'firstname', 'lastname', 'billing_country', 'billing_city', 'billing_address', 'billing_zip'];
        var errors = [];
        required.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el || !el.value.trim()) {
                errors.push({ field: id, message: (window.checkoutTexts && window.checkoutTexts.required) || 'Required field' });
            }
        });
        var stateEl = document.getElementById('billing_state');
        if (stateEl && !stateEl.value.trim()) {
            errors.push({ field: 'billing_state', message: (window.checkoutTexts && window.checkoutTexts.required) || 'Required field' });
        }
        var shipCheck = document.getElementById('shipping-address');
        if (shipCheck && shipCheck.checked) {
            var shipRequired = ['shipping_country', 'shipping_city', 'shipping_zip', 'shipping_address'];
            shipRequired.forEach(function (id) {
                var el = document.getElementById(id);
                if (!el || !el.value.trim()) {
                    errors.push({ field: id, message: (window.checkoutTexts && window.checkoutTexts.required) || 'Required field' });
                }
            });
            var shipStateEl = document.getElementById('shipping_state');
            if (shipStateEl && !shipStateEl.value.trim()) {
                errors.push({ field: 'shipping_state', message: (window.checkoutTexts && window.checkoutTexts.required) || 'Required field' });
            }
        }
        if (errors.length) { showFieldErrors(errors); return false; }
        return true;
    }

    function disableCheckoutFields(disabled) {
        var ids = ['phone', 'Insurance', 'SecretPackage', 'email', 'alter_email', 'firstname', 'lastname', 'billing_country', 'billing_state', 'billing_city', 'billing_address', 'billing_zip', 'shipping_country', 'shipping_state', 'shipping_city', 'shipping_address', 'shipping_zip'];
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el) el.disabled = disabled;
        });
    }

    function getLoaderMessages() {
        var texts = window.checkoutTexts && window.checkoutTexts.loaderMessages;
        if (texts && typeof texts === 'object') return texts;
        return {};
    }

    function showAjaxLoader(msg) {
        var l = document.getElementById('checkout-ajax-loader');
        if (!l) return;
        var textEl = l.querySelector('.checkout-ajax-loader__text');
        if (textEl && msg) textEl.textContent = msg;
        l.removeAttribute('hidden');
    }

    function hideAjaxLoader() {
        var l = document.getElementById('checkout-ajax-loader');
        if (!l) return;
        l.setAttribute('hidden', '');
    }
    function showFatalError(msg) {
        var el = document.getElementById('checkout-fatal');
        var txt = msg || (window.checkoutTexts || {}).fatalError || 'Something went wrong. Please try again.';
        if (!el) { alert(txt); return; }
        el.textContent = txt;
        el.removeAttribute('hidden');
        log.error('fatal error shown', { msg: txt });
    }
    function clearFatalError() { var el = document.getElementById('checkout-fatal'); if (el) el.setAttribute('hidden', ''); }

    var PAYMENT_BLOCK_MAP = {
        visa: 'card', mastercard: 'card', amex: 'card', discover: 'card',
        crypto: 'crypto',
        sepa_local: 'local', fps: 'local', domestic: 'local', ach: 'local', interac: 'local', usd_swift: 'local', gbp_swift: 'local',
        paypal: 'paypal', sepa: 'sepa', google: 'google', zelle: 'zelle',
        bonus_card: 'bonus-card', gift_card: 'gift-card',
        revolut: 'open-banking', open_banking: 'open-banking',
        google_pay: 'google', apple_pay: 'google'
    };
    function showPaymentBlock(type) {
        var wanted = PAYMENT_BLOCK_MAP[type] || 'card';
        var blocks = document.querySelectorAll('.payment-information__card-content, .payment-information__crypto-content, .payment-information__local-content, .payment-information__paypal-content, .payment-information__sepa-content, .payment-information__google-content, .payment-information__zelle-content, .payment-information__bonus-card-content, .payment-information__gift-card-content, .payment-information__open-banking-content');
        for (var i = 0; i < blocks.length; i++) {
            var m = blocks[i].className.match(/payment-information__([a-z-]+)-content/);
            var name = m ? m[1] : '';
            if (name === wanted) blocks[i].removeAttribute('hidden');
            else blocks[i].setAttribute('hidden', '');
        }
        log.debug('showPaymentBlock', { type: type, wanted: wanted });
    }

    function parseResponse(resp) {
        if (resp && typeof resp === 'object') return resp;
        if (typeof resp !== 'string') return null;
        var text = resp.trim();
        if (!text) return null;
        try {
            return JSON.parse(text);
        } catch (e) {
            var preview = text.slice(0, 120);
            log.warn('response not JSON', { preview: preview });
            if (/^\s*(?:<!DOCTYPE|<html|<head|<body)/i.test(text)) {
                log.error('received full HTML page instead of JSON — likely server error');
                return null;
            }
            return { html: text };
        }
    }

    function requestCheckout(routeKey, payload, opts) {
        opts = opts || {};
        var method = opts.method || 'POST';
        var route = routes()[routeKey];
        if (!route) {
            log.error('requestCheckout: unknown route', { routeKey: routeKey });
            return $.Deferred().reject({ status: 0, statusText: 'unknown route ' + routeKey }).promise();
        }
        var skipForm = opts.skipForm;
        var form = skipForm ? null : $('#order_form');
        var formData = form && form.length ? form.serialize() : '';
        var extra = $.param(payload || {});
        var data = extra ? (formData ? (formData + '&' + extra) : extra) : formData;
        var t0 = (window.performance && performance.now) ? performance.now() : Date.now();

        log.debug('request ->', { routeKey: routeKey, method: method, extraKeys: Object.keys(payload || {}), hasForm: !!form.length });

        showAjaxLoader(getLoaderMessages()[routeKey]);
        return $.ajax({
            url: route,
            method: method,
            data: data,
            dataType: 'text'
        }).then(function (resp) {
            var ms = Math.round(((window.performance && performance.now) ? performance.now() : Date.now()) - t0);
            var parsed = parseResponse(resp);
            log.debug('request <-', { routeKey: routeKey, method: method, ms: ms, keys: parsed ? Object.keys(parsed) : [] });
            hideAjaxLoader();
            clearFatalError();
            if (opts.raw) return parsed;
            if (parsed && parsed.html) {
                renderCheckout(parsed.html);
            }
            return parsed;
        }, function (xhr) {
            var ms = Math.round(((window.performance && performance.now) ? performance.now() : Date.now()) - t0);
            hideAjaxLoader();
            if (xhr.status === 422) {
                var err = parseResponse(xhr.responseText);
                log.warn('validation 422', { routeKey: routeKey, ms: ms, errors: err && err.errors });
                if (!opts.silentValidation && err && err.errors) showFieldErrors(err.errors);
            } else {
                log.error('request failed', { routeKey: routeKey, method: method, ms: ms, status: xhr.status, statusText: xhr.statusText });
                showFatalError();
            }
            return $.Deferred().reject(xhr).promise();
        });
    }

    function hidePreloader() { var p = document.getElementById('checkout-preloader'); if (p) { p.setAttribute('hidden', ''); p.style.display = 'none'; } }

    function loadCheckoutContent() {
        log.debug('loadCheckoutContent start', { route: routes().content });
        showAjaxLoader('Loading...');
        var t0 = (window.performance && performance.now) ? performance.now() : Date.now();
        return $.ajax({
            url: routes().content,
            method: 'GET',
            dataType: 'text'
        }).then(function (resp) {
            var ms = Math.round(((window.performance && performance.now) ? performance.now() : Date.now()) - t0);
            hideAjaxLoader();
            hidePreloader();
            var parsed = parseResponse(resp);
            if (parsed && parsed.html) {
                renderCheckout(parsed.html);
                log.debug('loadCheckoutContent done', { ms: ms, htmlLen: parsed.html.length });
            } else {
                log.warn('loadCheckoutContent: no html in response', { ms: ms, parsed: parsed });
                showFatalError();
            }
        }, function (xhr) {
            hideAjaxLoader();
            hidePreloader();
            log.error('loadCheckoutContent failed', { status: xhr.status, statusText: xhr.statusText });
            showFatalError();
        });
    }

    $(document).on('change', '[data-action="toggle-insurance"]', function () {
        log.debug('action toggle-insurance', { val: this.checked ? 1 : 0 });
        requestCheckout('insurance', { val: this.checked ? 1 : 0 });
    });

    $(document).on('change', '[data-action="toggle-secret"]', function () {
        log.debug('action toggle-secret', { checked: this.checked });
        requestCheckout('secretPackage', {});
    });

    $(document).on('change', '[data-action="change-shipping"]', function () {
        var $el = $(this);
        var payload = { shipping_name: $el.data('shipping-name'), shipping_price: $el.data('shipping-price') };
        log.debug('action change-shipping', payload);
        requestCheckout('shipping', payload);
    });

    $(document).on('change', '[data-action="change-country"]', function () {
        log.debug('action change-country', { billing_country: this.value });
        requestCheckout('country', { billing_country: this.value });
    });

    $(document).on('click', '[data-action="apply-coupon"]', function (e) {
        e.preventDefault();
        var input = document.querySelector('#order_form [name="coupon"]');
        var val = input ? input.value.trim() : '';
        if (!val) { log.warn('apply-coupon: empty value'); return; }
        log.debug('action apply-coupon', { coupon: val });
        requestCheckout('coupon', { coupon: val });
    });

    $(document).on('click', '[data-action="apply-gift-card"]', function (e) {
        e.preventDefault();
        var input = document.querySelector('#order_form [name="gift_card"]');
        var val = input ? input.value.trim() : '';
        if (!val) { log.warn('apply-gift-card: empty value'); return; }
        log.debug('action apply-gift-card', { gift_card: val });
        requestCheckout('giftCard', { gift_card: val });
    });

    $(document).on('click', '[data-action="apply-bonus-card"]', function (e) {
        e.preventDefault();
        var input = document.querySelector('#order_form [name="bonus_card"]');
        var val = input ? input.value.replace(/\s+/g, '') : '';
        if (!val) { log.warn('apply-bonus-card: empty value'); return; }
        if (val.length !== 15) { alert('Invalid bonus card format'); log.warn('apply-bonus-card: bad length', { len: val.length }); return; }
        log.debug('action apply-bonus-card', { bonus_card: val });
        requestCheckout('bonusCardInfo', { bonus_card: val });
    });

    $(document).on('click', '[data-action="switch-bonus"]', function (e) {
        e.preventDefault();
        var type = $(this).data('bonus');
        log.debug('action switch-bonus', { checked_bonus: type });
        requestCheckout('changeBonus', { checked_bonus: type });
    });

    $(document).on('click', '[data-action="forget-bonus"]', function (e) {
        e.preventDefault();
        var which = $(this).data('forget');
        log.debug('action forget-bonus', { witch_forget: which });
        requestCheckout('forgetBonuses', { witch_forget: which });
    });

    $(document).on('change', '[data-action="auth-email"]', function () {
        var email = this.value;
        if (!email) return;
        log.debug('action auth-email', { email: email });
        requestCheckout('auth', {}).then(function (resp) {
            log.debug('auth response', { hasHtml: !!(resp && resp.html) });
        }, function () {});
    });

    $(document).on('change', '[data-action="toggle-shipping-address"]', function () {
        var checked = this.checked;
        var fields = document.querySelector('.shipping-address-fields');
        if (!fields) { log.warn('toggle-shipping-address: fields not found'); return; }
        if (checked) { fields.removeAttribute('hidden'); } else { fields.setAttribute('hidden', ''); }
        log.debug('action toggle-shipping-address', { checked: checked });
    });

    $(document).on('click', '[data-action="copy-text"]', function (e) {
        e.preventDefault();
        var btn = this;
        var field = $(btn).closest('.copy-field')[0];
        if (!field) { log.warn('copy-text: no .copy-field ancestor'); return; }
        var textEl = field.querySelector('.copy-text');
        if (!textEl) return;
        var text = (textEl.textContent || '').trim();
        log.debug('action copy-text', { text: text });
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                var bt = btn.querySelector('.button-text');
                if (bt) { bt.classList.add('is-visible'); window.setTimeout(function () { bt.classList.remove('is-visible'); }, 1500); }
            }, function (err) { log.warn('clipboard write failed', { error: err && err.message }); });
        }
    });

    $(document).on('change', '[data-action="change-payment"]', function () {
        var select = this;
        var type = select.value;
        log.debug('action change-payment', { type: type });

        function recalc() {
            return requestCheckout('recalculation', { bonus_checkout_payment: type }, { raw: true, silentValidation: true }).then(function (resp) {
                if (resp && resp.html) renderCheckout(resp.html);
            }, function () {});
        }

        function savePaymentType() {
            var loaderMsgs = getLoaderMessages();
            showAjaxLoader(loaderMsgs.recalculation || 'Saving...');
            $.ajax({ url: routes().recalculation, method: 'POST', data: $('#order_form').serialize() + '&bonus_checkout_payment=' + encodeURIComponent(type) }).always(function () {
                hideAjaxLoader();
            });
        }

        if (type === 'crypto') {
            var valid = validateCheckoutForm();
            if (!valid) { log.warn('crypto: form invalid, reverting'); return; }
            disableCheckoutFields(true);
            showPaymentBlock(type);
            savePaymentType();
            return;
        } else {
            disableCheckoutFields(false);
        }

        showPaymentBlock(type);

        var localTypes = ['sepa_local', 'fps', 'domestic', 'ach', 'interac', 'usd_swift', 'gbp_swift'];
        if (localTypes.indexOf(type) !== -1) {
            requestCheckout('localPaymentInfo', { local_payment: type }, { raw: true, silentValidation: true }).then(function (resp) {
                if (resp && resp.success) {
                    $.ajax({ url: routes().dataForLocalPayment, method: 'POST', data: { form: $('#order_form').serialize() } });
                    renderCheckout(resp.html);
                } else {
                    recalc();
                }
            }, function () { recalc(); });
            return;
        }

        recalc();
    });

    $(document).on('change', '[data-component="custom-selector"]', function () {
        var el = this;
        var container = el.tagName === 'SELECT' ? el.parentNode : el;
        if (container && container.__customSelector) {
            container.__customSelector.syncFromNativeSelect();
        }
    });



    var cryptoTimerInterval = null;

    function setText(id, val) { var el = document.getElementById(id); if (el && val != null) el.textContent = val; }
    function setSrc(id, val) { var el = document.getElementById(id); if (el && val != null) el.setAttribute('src', val); }
    function setVal(id, val) { var el = document.getElementById(id); if (el && val != null) el.value = val; }

    function collectBrowserInfo() {
        var now = new Date();
        var nav = navigator || {};
        var scr = window.screen || {};
        return {
            screen_resolution: (scr.width || 0) + 'x' + (scr.height || 0),
            customer_date: now.toString(),
            browser_details: {
                browser_accept_header: '',
                browser_language: nav.language || '',
                browser_color_depth: scr.colorDepth || '',
                browser_screen_height: scr.height || '',
                browser_screen_width: scr.width || '',
                browser_timezone: now.getTimezoneOffset(),
                browser_user_agent: nav.userAgent || '',
                browser_java_enable: typeof nav.javaEnabled === 'function' ? nav.javaEnabled() : false,
                window_height: window.innerHeight || '',
                window_width: window.innerWidth || ''
            }
        };
    }

    function appendForm3d(html) {
        var div = document.createElement('div');
        div.style.display = 'none';
        div.innerHTML = html;
        document.body.appendChild(div);
        var form3d = div.querySelector('#form3d') || document.getElementById('form3d');
        if (form3d) form3d.submit();
        else log.error('3DS form #form3d not found in injected html');
    }

    function alertMessages(msg) {
        if (!msg) return;
        alert(typeof msg === 'string' ? msg : (Array.isArray(msg) ? msg.join('\n') : String(msg)));
    }

    var PAYMENT_METHOD_LABELS = {
        visa: 'Visa', mastercard: 'Mastercard', amex: 'Amex', discover: 'Discover',
        paypal: 'PayPal', crypto: 'Crypto', sepa: 'SEPA', fps: 'FPS', domestic: 'Domestic',
        ach: 'ACH / Wire', interac: 'Interac / EFT', usd_swift: 'USD SWIFT', gbp_swift: 'GBP SWIFT',
        zelle: 'ZELLE', revolut: 'Revolut', open_banking: 'Open Banking',
        google_pay: 'Google Pay', apple_pay: 'Apple Pay',
        bonus_card: 'Bonus Card', gift_card: 'Gift Card', sepa_local: 'SEPA'
    };

    var PAYMENT_METHOD_ICON_CLASS = {
        mastercard: 'pem-icon--banking', visa: 'pem-icon--danger', amex: 'pem-icon--banking', discover: 'pem-icon--banking',
        apple_pay: 'pem-icon--banking', google_pay: 'pem-icon--banking',
        open_banking: 'pem-icon--banking', revolut: 'pem-icon--banking',
        sepa: 'pem-icon--transfer', fps: 'pem-icon--transfer', domestic: 'pem-icon--transfer',
        ach: 'pem-icon--transfer', interac: 'pem-icon--transfer', usd_swift: 'pem-icon--transfer',
        gbp_swift: 'pem-icon--transfer', zelle: 'pem-icon--transfer', sepa_local: 'pem-icon--transfer',
        crypto: 'pem-icon--crypto', paypal: 'pem-icon--banking'
    };

    var paymethodSuggestionQueue = [];
    var paymethodSuggestionIndex = -1;
    var paymethodSuggestionProcessing = false;
    var paymethodErrorOrderSuccess = false;
    var paymethodSavedHtml = null;

    var CARD_METHODS = ['mastercard', 'visa', 'amex', 'discover'];

    function getAvailableMethods(exclude) {
        var paySelect = document.querySelector('[data-action="change-payment"]');
        var methods = [];
        if (paySelect && paySelect.options) {
            for (var i = 0; i < paySelect.options.length; i++) {
                var val = paySelect.options[i].value;
                if (val && val !== '' && val !== exclude) methods.push(val);
            }
        }
        return methods;
    }

    function getPaymentMethodLabel(method) { return PAYMENT_METHOD_LABELS[method] || method; }

    function isRiskCheckFailed(response) {
        if (!response || !response.message) return false;
        var msg = response.message;
        if (Array.isArray(msg)) return msg.indexOf('risk_check_failed') !== -1;
        return msg === 'risk_check_failed' || (typeof msg === 'string' && msg.indexOf('risk_check_failed') !== -1);
    }

    function getFallbackPaymentQueue(errorType) {
        var available = getAvailableMethods();
        var failedMethod = document.querySelector('[data-action="change-payment"]');
        var failedVal = failedMethod ? failedMethod.value : '';

        var priority = [
            'mastercard', 'apple_pay', 'google_pay', 'open_banking', 'revolut',
            'sepa', 'fps', 'domestic', 'zelle', 'ach', 'interac',
            'usd_swift', 'gbp_swift', '__another_card__', 'crypto'
        ];

        if (errorType === 'risk_check_failed') {
            priority = [
                'crypto', 'apple_pay', 'google_pay', 'open_banking', 'revolut',
                'sepa', 'fps', 'domestic', 'zelle', 'ach', 'interac',
                'usd_swift', 'gbp_swift', '__another_card__', 'mastercard'
            ];
        }

        return priority.filter(function (m) {
            if (m === '__another_card__') return true;
            return m !== failedVal && available.indexOf(m) !== -1;
        });
    }

    function renderSuggestionState(method, checkoutHtml) {
        var texts = window.checkoutTexts || {};
        var iconEl = document.getElementById('pem-icon');
        var titleEl = document.getElementById('pem-title');
        var descEl = document.getElementById('pem-desc');
        var recommendEl = document.getElementById('pem-recommend');
        var recommendLabel = document.getElementById('pem-recommend-label');
        var recommendMethod = document.getElementById('pem-recommend-method');
        var benefitsEl = document.getElementById('pem-benefits');
        var cardTilesEl = document.getElementById('pem-card-tiles');
        var primaryBtn = document.getElementById('pem-btn-primary');
        var secondaryBtn = document.getElementById('pem-btn-secondary');
        var linkBtn = document.getElementById('pem-btn-link');

        if (iconEl) { iconEl.className = 'pem-icon-wrap ' + (PAYMENT_METHOD_ICON_CLASS[method] || 'pem-icon--neutral'); }
        if (titleEl) titleEl.textContent = texts.paymethodUnavailable || 'Unfortunately, this payment method is currently unavailable';
        if (descEl) descEl.textContent = texts.paymentErrorRiskCheckMessage || "Payment didn't pass security check. Please try another method.";
        if (recommendLabel) recommendLabel.textContent = (texts.paymethodRecommend || 'We recommend') + ':';
        if (recommendMethod) recommendMethod.textContent = getPaymentMethodLabel(method);
        if (recommendEl) recommendEl.style.display = '';
        if (benefitsEl) {
            benefitsEl.innerHTML = '';
            var benefitItems = getBenefitItems(method, texts);
            if (benefitItems.length > 0) {
                benefitItems.forEach(function (item) {
                    var li = document.createElement('li');
                    li.innerHTML = '<span class="pem-benefit-icon">' + item.icon + '</span> <span>' + item.text + '</span>';
                    benefitsEl.appendChild(li);
                });
                benefitsEl.hidden = false;
            } else {
                benefitsEl.hidden = true;
            }
        }
        if (cardTilesEl) cardTilesEl.hidden = true;
        if (primaryBtn) {
            primaryBtn.textContent = (texts.paymethodPayWith || 'Pay with') + ' ' + getPaymentMethodLabel(method);
            primaryBtn.disabled = false;
            primaryBtn.style.display = '';
        }
        if (secondaryBtn) {
            var hasNext = paymethodSuggestionIndex < paymethodSuggestionQueue.length - 1;
            secondaryBtn.textContent = texts.paymethodShowOther || 'Show other options';
            secondaryBtn.style.display = hasNext ? '' : 'none';
        }
        if (linkBtn) linkBtn.style.display = 'none';

        paymethodSavedHtml = checkoutHtml;
    }

    var CARD_ICONS = {
        mastercard: '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="20" fill="none" viewBox="0 0 34 20"><path fill="#FF5F00" d="M21.685 2.43h-8.727v15.14h8.727V2.43Z"/><path fill="#EB001B" d="M13.513 10a9.34 9.34 0 0 1 1-4.208 9.685 9.685 0 0 1 2.809-3.362 10.17 10.17 0 0 0-5.1-2.004 10.285 10.285 0 0 0-5.421.914 9.862 9.862 0 0 0-4.096 3.552A9.386 9.386 0 0 0 1.185 10c0 1.806.527 3.578 1.52 5.109A9.863 9.863 0 0 0 6.8 18.662a10.286 10.286 0 0 0 5.423.913 10.17 10.17 0 0 0 5.099-2.003 9.686 9.686 0 0 1-2.808-3.361A9.34 9.34 0 0 1 13.513 10Z"/><path fill="#F79E1B" d="M33.458 10a9.39 9.39 0 0 1-1.519 5.109 9.86 9.86 0 0 1-4.096 3.552 10.284 10.284 0 0 1-5.422.913 10.17 10.17 0 0 1-5.1-2.003 9.703 9.703 0 0 0 2.807-3.362 9.36 9.36 0 0 0 1.003-4.21 9.36 9.36 0 0 0-1.002-4.207 9.703 9.703 0 0 0-2.807-3.363A10.17 10.17 0 0 1 22.42.425a10.286 10.286 0 0 1 5.423.914A9.863 9.863 0 0 1 31.94 4.89 9.386 9.386 0 0 1 33.458 10Z"/></svg>',
        visa: '<svg xmlns="http://www.w3.org/2000/svg" width="51" height="18" fill="none" viewBox="0 0 51 18"><path fill="#0085F3" d="m20.42 1.411-2.59 15.21h4.144l2.594-15.21h-4.147Zm-6.067.017-4.06 10.373-.433-1.566c-.8-1.887-3.074-4.598-5.742-6.306L7.83 16.614l4.386-.008 6.53-15.18-4.393.002Z"/><path fill="#0085F3" d="M8.286 2.517c-.24-.927-.939-1.203-1.806-1.236H.053L0 1.584c5.001 1.213 8.31 4.137 9.684 7.652L8.286 2.517Zm24.684 1.81a7.653 7.653 0 0 1 3.102.582l.374.176.561-3.296c-.82-.309-2.107-.64-3.713-.64-4.096 0-6.984 2.064-7.006 5.02-.027 2.185 2.057 3.405 3.63 4.133 1.616.746 2.157 1.22 2.15 1.887-.013 1.018-1.288 1.485-2.48 1.485-1.66 0-2.541-.229-3.902-.796l-.535-.243-.583 3.404c.97.426 2.76.791 4.619.81 4.357 0 7.19-2.038 7.219-5.197.018-1.728-1.087-3.047-3.483-4.13-1.45-.705-2.337-1.174-2.329-1.887 0-.632.753-1.308 2.376-1.308Z"/></svg>',
        amex: '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="20" fill="none" viewBox="0 0 34 20"><path fill="#0085F3" d="M6.982 10.639h1.337l-.673-1.705-.664 1.705Z"/><path fill="#0085F3" d="M32.485 0h-30.1C1.55 0 .872.678.872 1.515v16.97c0 .837.678 1.515 1.515 1.515h30.099C33.32 20 34 19.322 34 18.485V1.515C34 .678 33.32 0 32.485 0ZM10.709 13.092a.304.304 0 0 1-.252.134h-.91a.304.304 0 0 1-.284-.193l-.328-.834H6.374l-.324.832a.303.303 0 0 1-.285.195h-.907a.305.305 0 0 1-.285-.416l2.333-5.987a.303.303 0 0 1 .285-.194h.904c.126 0 .237.076.284.193l2.362 5.987c.037.094.025.2-.032.283Zm6.404-.22a.305.305 0 0 1-.305.305h-.784a.305.305 0 0 1-.305-.305v-2.623l-1.337 2.642a.304.304 0 0 1-.544 0l-1.35-2.649v2.63a.305.305 0 0 1-.306.305h-.784a.305.305 0 0 1-.306-.305v-5.92c0-.169.137-.305.306-.305h.632c.115 0 .22.063.272.166l1.804 3.534 1.787-3.533a.304.304 0 0 1 .273-.167h.642c.169 0 .305.136.305.305v5.92Zm6.772.065a.306.306 0 0 1-.305.305h-3.75a.306.306 0 0 1-.304-.306V7.012c0-.169.137-.306.305-.306h3.645c.168 0 .305.137.305.306v.722a.305.305 0 0 1-.305.305h-2.618v1.27h2.132c.168 0 .305.136.305.304v.723a.305.305 0 0 1-.305.305h-2.132v1.27h2.722c.168 0 .305.136.305.304v.723Z"/></svg>',
        discover: '<svg xmlns="http://www.w3.org/2000/svg" width="88" height="16" fill="none" viewBox="0 0 88 16"><path fill="#1D1D1B" fill-rule="evenodd" d="M27.366 7.96c0 4.144 3.253 7.357 7.44 7.357 1.184 0 2.198-.233 3.448-.822V11.26c-1.1 1.1-2.073 1.544-3.32 1.544-2.77 0-4.735-2.008-4.735-4.863 0-2.706 2.028-4.841 4.607-4.841 1.312 0 2.305.468 3.448 1.586V1.45c-1.207-.613-2.2-.866-3.383-.866-4.166 0-7.506 3.278-7.506 7.377Zm-7.057-3.423c0 .762.484 1.164 2.134 1.775 3.128 1.144 4.055 2.158 4.055 4.398 0 2.728-2.005 4.628-4.862 4.628-2.092 0-3.614-.823-4.88-2.683l1.776-1.711c.633 1.224 1.69 1.88 3 1.88 1.228 0 2.136-.846 2.136-1.988 0-.592-.275-1.1-.825-1.459-.276-.17-.824-.423-1.9-.803-2.583-.929-3.469-1.923-3.469-3.866 0-2.307 1.903-4.039 4.397-4.039 1.546 0 2.96.529 4.142 1.563l-1.439 1.884c-.716-.803-1.393-1.142-2.217-1.142-1.185 0-2.048.675-2.048 1.563Z" clip-rule="evenodd"/><path fill="url(#a)" fill-rule="evenodd" d="M52.968 12.04a7.498 7.498 0 0 0-2.277-10.357 7.499 7.499 0 1 0-8.08 12.635 7.499 7.499 0 0 0 10.357-2.278Z" clip-rule="evenodd"/><defs><linearGradient id="a" x1="57.008" x2="44.373" y1="5.723" y2="-2.358" gradientUnits="userSpaceOnUse"><stop stop-color="#F6A000"/><stop offset=".624" stop-color="#E47E02"/><stop offset="1" stop-color="#D36002"/></linearGradient></defs></svg>'
    };

    function renderCardSelectionState() {
        var texts = window.checkoutTexts || {};
        var paySelect = document.querySelector('[data-action="change-payment"]');
        var currentMethod = paySelect ? paySelect.value : '';

        var iconEl = document.getElementById('pem-icon');
        var titleEl = document.getElementById('pem-title');
        var descEl = document.getElementById('pem-desc');
        var recommendEl = document.getElementById('pem-recommend');
        var benefitsEl = document.getElementById('pem-benefits');
        var cardTilesEl = document.getElementById('pem-card-tiles');
        var primaryBtn = document.getElementById('pem-btn-primary');
        var secondaryBtn = document.getElementById('pem-btn-secondary');
        var linkBtn = document.getElementById('pem-btn-link');

        if (iconEl) iconEl.className = 'pem-icon-wrap pem-icon--banking';
        if (titleEl) titleEl.textContent = texts.paymethodTryDifferentCard || 'Try a different card';
        if (descEl) descEl.textContent = texts.paymethodCardDesc || 'Select a card type. Previous card details will be cleared.';
        if (recommendEl) recommendEl.style.display = 'none';
        if (benefitsEl) benefitsEl.hidden = true;
        if (primaryBtn) primaryBtn.style.display = 'none';
        if (linkBtn) linkBtn.textContent = texts.paymethodSkipOther || 'Skip, show other options';
        if (linkBtn) linkBtn.style.display = '';

        if (cardTilesEl) {
            cardTilesEl.innerHTML = '';
            cardTilesEl.hidden = false;
            CARD_METHODS.forEach(function (card) {
                if (card === currentMethod) return;
                var iconSvg = CARD_ICONS[card] || '';
                var tile = document.createElement('button');
                tile.className = 'pem-card-tile';
                tile.type = 'button';
                tile.innerHTML = '<span class="pem-card-tile-icon">' + iconSvg + '</span><span class="pem-card-tile-label">' + getPaymentMethodLabel(card) + '</span>';
                tile.addEventListener('click', function () { selectAnotherCard(card); });
                cardTilesEl.appendChild(tile);
            });
        }
    }

    function renderExhaustedState() {
        var texts = window.checkoutTexts || {};
        var iconEl = document.getElementById('pem-icon');
        var titleEl = document.getElementById('pem-title');
        var descEl = document.getElementById('pem-desc');
        var recommendEl = document.getElementById('pem-recommend');
        var benefitsEl = document.getElementById('pem-benefits');
        var cardTilesEl = document.getElementById('pem-card-tiles');
        var primaryBtn = document.getElementById('pem-btn-primary');
        var secondaryBtn = document.getElementById('pem-btn-secondary');
        var linkBtn = document.getElementById('pem-btn-link');

        if (iconEl) iconEl.className = 'pem-icon-wrap pem-icon--neutral';
        if (titleEl) titleEl.textContent = texts.paymethodExhaustedTitle || 'Unfortunately, no payment methods worked';
        if (descEl) descEl.textContent = texts.paymethodExhaustedDesc || 'Please contact support to complete your order.';
        if (recommendEl) recommendEl.style.display = 'none';
        if (benefitsEl) benefitsEl.hidden = true;
        if (cardTilesEl) cardTilesEl.hidden = true;
        if (primaryBtn) {
            primaryBtn.textContent = texts.paymethodExhaustedClose || 'Close';
            primaryBtn.style.display = '';
        }
        if (secondaryBtn) secondaryBtn.style.display = 'none';
        if (linkBtn) linkBtn.style.display = 'none';
    }

    function getBenefitItems(method, texts) {
        if (method === 'apple_pay' || method === 'google_pay') {
            return [
                { icon: '📱', text: texts.paymethodBenefitExpress || 'Express checkout' },
                { icon: '🔒', text: texts.paymethodBenefitSecureBiometric || 'Secure — authenticated with biometrics' },
                { icon: '👍', text: texts.paymethodBenefitSimpleWallet || 'Simple — no card details needed' }
            ];
        }
        if (method === 'open_banking' || method === 'revolut') {
            return [
                { icon: '⚡', text: texts.paymethodBenefitInstant || 'Instant processing' },
                { icon: '🔒', text: texts.paymethodBenefitSecureBank || 'Secure — through your bank' },
                { icon: '👍', text: texts.paymethodBenefitSimpleBank || 'Simple — no card details needed' }
            ];
        }
        if (method === 'sepa' || method === 'fps' || method === 'domestic' || method === 'zelle' || method === 'ach' || method === 'interac' || method === 'usd_swift' || method === 'gbp_swift' || method === 'sepa_local') {
            return [
                { icon: '🔐', text: texts.paymethodBenefitReliable || 'Reliable bank transfer' },
                { icon: '📋', text: texts.paymethodBenefitTransparent || 'All details shown' }
            ];
        }
        if (method === 'crypto') {
            return [
                { icon: '🌐', text: texts.paymethodBenefitUniversal || 'Works from any country' },
                { icon: '🔄', text: texts.paymethodBenefitInstantConfirm || 'Instant blockchain confirmation' }
            ];
        }
        return [];
    }

    function showPaymentSuggestion(errorType, message, checkoutHtml) {
        paymethodSuggestionQueue = getFallbackPaymentQueue(errorType);
        paymethodSuggestionIndex = paymethodSuggestionQueue.length > 0 ? 0 : -1;
        paymethodSuggestionProcessing = false;
        paymethodErrorOrderSuccess = false;

        log.debug('showPaymentSuggestion', { errorType: errorType, queue: paymethodSuggestionQueue });

        renderCurrentSuggestion(checkoutHtml);
        showPaymentModal();
    }

    function renderCurrentSuggestion(checkoutHtml) {
        if (paymethodSuggestionIndex === -1) {
            renderExhaustedState();
            return;
        }
        var method = paymethodSuggestionQueue[paymethodSuggestionIndex];
        if (method === '__another_card__') {
            renderCardSelectionState();
        } else {
            renderSuggestionState(method, checkoutHtml);
        }
    }

    function showPaymentModal() {
        var overlay = document.getElementById('pem-overlay');
        if (!overlay) { log.error('pem-overlay not found'); return; }
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function hidePaymentModal() {
        var overlay = document.getElementById('pem-overlay');
        if (overlay) overlay.hidden = true;
        document.body.style.overflow = '';
    }

    function acceptPaymethodSuggestion() {
        if (paymethodSuggestionProcessing) return;
        var method = paymethodSuggestionQueue[paymethodSuggestionIndex];
        if (!method) return;

        paymethodSuggestionProcessing = true;
        log.info('user accepted payment switch to ' + method);

        if (method === 'mastercard' && paymethodSavedHtml) {
            hidePaymentModal();
            renderCheckout(paymethodSavedHtml);
            paymethodSuggestionProcessing = false;
            return;
        }

        var paySelect = document.querySelector('[data-action="change-payment"]');
        if (paySelect) {
            paySelect.value = method;
            $(paySelect).trigger('change');
        }
        hidePaymentModal();
        paymethodSavedHtml = null;
        paymethodSuggestionProcessing = false;
    }

    function declinePaymethodSuggestion() {
        if (paymethodSuggestionIndex < paymethodSuggestionQueue.length - 1) {
            paymethodSuggestionIndex++;
            renderCurrentSuggestion(paymethodSavedHtml);
            log.debug('declinePaymethodSuggestion', { newIndex: paymethodSuggestionIndex, method: paymethodSuggestionQueue[paymethodSuggestionIndex] });
        } else {
            paymethodSuggestionIndex = -1;
            renderExhaustedState();
        }
    }

    function selectAnotherCard(card) {
        log.info('user selected another card: ' + card);
        var paySelect = document.querySelector('[data-action="change-payment"]');
        if (paySelect) {
            paySelect.value = card;
            $(paySelect).trigger('change');
        }
        hidePaymentModal();
    }

    function dismissPaymethodSuggestion() {
        log.warn('user dismissed payment suggestion modal');
        hidePaymentModal();

        if (paymethodErrorOrderSuccess) {
            paymethodErrorOrderSuccess = false;
            window.location.href = routes().complete;
            return;
        }
    }

    function handlePaymentResponse(resp) {
        var r = resp && (resp.response || resp);
        if (!r) { log.warn('payment empty response', resp); return; }
        log.debug('payment response', { status: r.status, hasUrl: !!r.url, hasForm3d: !!r.form3d_html, visaError: !!r.visa_error, riskCheck: !!r.risk_check });

        if (r.form3d_html) { appendForm3d(r.form3d_html); return; }
        if (r.url) { window.location.replace(r.url); return; }
        if (r.status === 'SUCCESS') { window.location.href = routes().complete; return; }

        if (r.paymethod_error) {
            if (r.status === 'SUCCESS') paymethodErrorOrderSuccess = true;
            var html = r.html;
            if (html && typeof html === 'object' && html.original) html = html.original.html;
            log.debug('handlePaymentResponse -> paymethod error, showing suggestion');
            showPaymentSuggestion('visa_error', r.message, html);
            return;
        }

        if (r.visa_error) {
            var html = r.html;
            if (html && typeof html === 'object' && html.original) html = html.original.html;
            log.debug('handlePaymentResponse -> visa_error, showing suggestion');
            showPaymentSuggestion('visa_error', r.message, html);
            return;
        }

        if (r.risk_check || isRiskCheckFailed(r)) {
            var html = r.html;
            if (html && typeof html === 'object' && html.original) html = html.original.html;
            log.debug('handlePaymentResponse -> risk_check, showing suggestion');
            showPaymentSuggestion('risk_check_failed', r.message, html);
            return;
        }

        alertMessages(r.message || r.text);
    }

    function processPayment(routeKey, extra) {
        var info = $.extend({}, collectBrowserInfo(), extra || {});
        log.debug('processPayment', { routeKey: routeKey, extraKeys: Object.keys(extra || {}) });
        return requestCheckout(routeKey, info, { raw: true }).then(handlePaymentResponse, function () {
            log.error('processPayment rejected', { routeKey: routeKey });
        });
    }

    function startCryptoTimer(remaining) {
        if (cryptoTimerInterval) clearInterval(cryptoTimerInterval);
        if (remaining == null) remaining = 30 * 60;
        cryptoTimerInterval = window.setInterval(function () {
            remaining--;
            if (remaining <= 0) { clearInterval(cryptoTimerInterval); cryptoTimerInterval = null; setText('timer', '00:00'); return; }
            var m = Math.floor(remaining / 60), s = remaining % 60;
            setText('timer', (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        }, 1000);
    }

    function stopCryptoTimer() {
        if (cryptoTimerInterval) { clearInterval(cryptoTimerInterval); cryptoTimerInterval = null; log.debug('crypto timer stopped'); }
    }

    function selectCryptoCurrency(currency) {
        var emailEl = document.getElementById('email');
        var email = emailEl ? emailEl.value : '';
        log.debug('crypto select currency', { currency: currency, email: email });
        var req = document.getElementById('requisites');
        var loading = document.getElementById('crypto-loading');
        if (loading) loading.removeAttribute('hidden');
        var loaderMsgs = getLoaderMessages();
        showAjaxLoader(loaderMsgs.cryptoInfo || 'Loading crypto...');
        requestCheckout('cryptoInfo', { currency: currency, email: email }, { raw: true }).then(function (resp) {
            hideAjaxLoader();
            if (loading) loading.setAttribute('hidden', '');
            var data = resp;
            if (typeof data === 'string') { try { data = JSON.parse(data); } catch (e) { data = null; } }
            if (!data) { log.warn('crypto_info empty response'); return; }
            if (data.status === 'error') { alertMessages(data.text); return; }
            if (req) req.removeAttribute('hidden');
            log.debug('crypto_info data', { keys: Object.keys(data) });
            setText('crypto_total', data.amount != null ? data.amount : data.crypto_total);
            setText('purse', data.purse);
            setSrc('qr_code', data.qr);
            setVal('invoiceId', data.invoiceId);
            setText('invoce_p', data.invoiceId);
            if (data.crypto_total != null) setText('crypto_discount_price', data.crypto_total);
            sessionStorage.setItem('crypto_ts_' + data.invoiceId, Date.now().toString());
            var paid = document.getElementById('paid');
            if (paid) paid.disabled = false;
            $.ajax({
                url: routes().dataForCrypt,
                method: 'POST',
                data: {
                    crypto_currency: currency,
                    crypto_total: data.crypto_total,
                    crypto_discount_price: data.crypto_total,
                    purse: data.purse,
                    invoiceId: data.invoiceId
                }
            });
            startCryptoTimer();
            if (req && req.scrollIntoView) req.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, function () { hideAjaxLoader(); if (loading) loading.setAttribute('hidden', ''); });
    }

    function initCryptoState() {
        var currency = document.getElementById('crypto_selected');
        if (!currency || !currency.value) return;
        var items = document.querySelectorAll('.crypto-item');
        for (var i = 0; i < items.length; i++) {
            if (items[i].getAttribute('data-value') === currency.value) {
                items[i].classList.add('is-active');
                break;
            }
        }
        var req = document.getElementById('requisites');
        if (req && !req.hasAttribute('hidden')) {
            var invoiceId = document.getElementById('invoiceId');
            var tsKey = 'crypto_ts_' + (invoiceId ? invoiceId.value : '');
            var created = sessionStorage.getItem(tsKey);
            if (created) {
                var elapsed = Math.floor((Date.now() - parseInt(created, 10)) / 1000);
                var remaining = Math.max(0, 1800 - elapsed);
                if (remaining > 0) {
                    startCryptoTimer(remaining);
                    if (req.scrollIntoView) setTimeout(function () { req.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 500);
                    return;
                }
            }
            startCryptoTimer();
        }
    }

    $(document).on('click', '.crypto-item', function () {
        var currency = this.getAttribute('data-value');
        if (!currency) return;
        $('.crypto-item').removeClass('is-active');
        $(this).addClass('is-active');
        log.debug('crypto method selected', { currency: currency });
        selectCryptoCurrency(currency);
    });

    $(document).on('submit', '#order_form', function (e) {
        e.preventDefault();
        log.debug('action submit-order (form submit)');
        var info = collectBrowserInfo();
        requestCheckout('order', info, { raw: true }).then(handlePaymentResponse, function () {
            log.error('order request rejected (validation or network)');
        });
    });

    $(document).on('click', '[data-action="check-payment"]', function (e) {
        e.preventDefault();
        if (this.disabled) return;
        var loading = document.getElementById('crypto-loading');
        if (loading) loading.removeAttribute('hidden');
        var paidBtn = document.getElementById('paid');
        if (paidBtn) paidBtn.disabled = true;
        processPayment('checkPayment').always(function () {
            if (loading) loading.setAttribute('hidden', '');
        });
    });

    $(document).on('click', '[data-action="process-paypal"]', function (e) { e.preventDefault(); processPayment('paypal'); });
    $(document).on('click', '[data-action="process-sepa"]', function (e) { e.preventDefault(); processPayment('sendSepa'); });
    $(document).on('click', '[data-action="process-local-payment"]', function (e) { e.preventDefault(); processPayment('localPayment'); });
    $(document).on('click', '[data-action="process-zelle"]', function (e) { e.preventDefault(); processPayment('zelle'); });
    $(document).on('click', '[data-action="process-bonus-card"]', function (e) { e.preventDefault(); processPayment('bonusCardProcess'); });
    $(document).on('click', '[data-action="process-gift-card"]', function (e) { e.preventDefault(); processPayment('giftCardProcess'); });

    $(document).on('click', '[data-action="process-open-banking"]', function (e) {
        e.preventDefault();
        var pt = $(this).data('payment-type');
        processPayment('openBanking', pt === 'revolut' ? { is_revolut: 1 } : {});
    });

    $(document).on('click', '[data-action="process-wallet"]', function (e) {
        e.preventDefault();
        var type = $(this).data('wallet') || 'google_pay';
        log.debug('process-wallet', { wallet: type });
        requestCheckout('validateForWallet', { wallet: type }, { raw: true }).then(function (resp) {
            var ok = !resp || resp.success === undefined || resp.success === true;
            if (!ok) { alertMessages(resp && (resp.text || resp.message)); if (resp && resp.errors) showFieldErrors(resp.errors); return; }
            processPayment('walletProcess', { wallet: type });
        }, function () { log.error('validate_for_wallet rejected'); });
    });

    $(document).on('click', '[data-action="get-zelle-data"]', function (e) {
        e.preventDefault();
        var btn = this;
        processPayment('zelleData').then(function (resp) {
            var r = resp && (resp.response || resp);
            if (r && r.status === 'SUCCESS') {
                setText('zelle_orderId', r.orderId || r.order_id);
                setText('zelle_email', r.email);
                setText('zelle_recipient', r.recipient);
                var req = document.getElementById('zelle_requisites');
                if (req) req.removeAttribute('hidden');
                btn.setAttribute('hidden', '');
            } else {
                handlePaymentResponse(resp);
            }
        });
    });

    window.addEventListener('message', function (event) {
        if (!event.origin || !/r\.express$/.test(event.origin)) return;
        log.debug('google postMessage', { origin: event.origin, data: event.data });
        try {
            $.ajax({ url: routes().logGoogle, method: 'POST', contentType: 'application/json', data: JSON.stringify({ info: event.data }) });
        } catch (err) { log.warn('log_google failed', { error: err.message }); }
        var raw = typeof event.data === 'string' ? event.data : JSON.stringify(event.data || '');
        if (raw.indexOf('PENDING_CAPTURE') !== -1) {
            var transId = (raw.match(/"trans_id"\s*:\s*"([^"]+)"/i) || raw.match(/trans_id=([^&"]+)/i) || [])[1] || '';
            var googleSum = (raw.match(/"google_sum"\s*:\s*"?([\d.]+)"?/i) || raw.match(/google_sum=([\d.]+)/i) || [])[1] || '';
            var formData = {
                trans_id: transId,
                google_sum: googleSum,
                full_response: window.btoa ? btoa(raw) : raw,
                customer_date: new Date().toString(),
                screen_resolution: (window.screen.width || 0) + 'x' + (window.screen.height || 0)
            };
            log.debug('send_google', { transId: transId, googleSum: googleSum });
            $.ajax({ url: routes().sendGoogle, method: 'POST', contentType: 'application/json', data: JSON.stringify(formData) }).then(function (resp) {
                handlePaymentResponse(resp);
            }, function () { log.error('send_google rejected'); });
        }
    });

    $(document).on('click', '#pem-btn-primary', function () {
        if (paymethodSuggestionIndex === -1) { dismissPaymethodSuggestion(); return; }
        acceptPaymethodSuggestion();
    });

    $(document).on('click', '#pem-btn-secondary', function () {
        declinePaymethodSuggestion();
    });

    $(document).on('click', '#pem-btn-link', function () {
        declinePaymethodSuggestion();
    });

    $(document).on('click', '#pem-btn-close', function () {
        dismissPaymethodSuggestion();
    });

    $(document).on('click', '#pem-overlay', function (e) {
        if (e.target === this) dismissPaymethodSuggestion();
    });

    window.Checkout17 = {
        log: log,
        initCheckoutComponents: initCheckoutComponents,
        destroyCheckoutComponents: destroyCheckoutComponents,
        renderCheckout: renderCheckout,
        requestCheckout: requestCheckout,
        showFieldErrors: showFieldErrors,
        clearFieldErrors: clearFieldErrors,
        loadCheckoutContent: loadCheckoutContent,
        selectCryptoCurrency: selectCryptoCurrency,
        processPayment: processPayment,
        handlePaymentResponse: handlePaymentResponse,
        showPaymentSuggestion: showPaymentSuggestion,
        collectBrowserInfo: collectBrowserInfo,
        stopCryptoTimer: stopCryptoTimer
    };

    log.info('checkout module ready', { level: window.CHECKOUT17_LOG_LEVEL || 'debug' });

})(jQuery);
