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

    function handlePaymentResponse(resp) {
        var r = resp && (resp.response || resp);
        if (!r) { log.warn('payment empty response', resp); return; }
        log.debug('payment response', { status: r.status, hasUrl: !!r.url, hasForm3d: !!r.form3d_html, visaError: !!r.visa_error, riskCheck: !!r.risk_check });

        if (r.form3d_html) {
            if (typeof window.openPaymentRedirect === 'function') {
                window.openPaymentRedirect(r.form3d_html, 'form', r.redirect_url);
            } else {
                appendForm3d(r.form3d_html);
            }
            return;
        }
        if (r.url) {
            if (typeof window.openPaymentRedirect === 'function') {
                window.openPaymentRedirect(r.url, 'url', r.redirect_url);
            } else {
                window.location.replace(r.url);
            }
            return;
        }
        if (r.status === 'SUCCESS') { window.location.href = routes().complete; return; }

        var html = r.html;
        if (html && typeof html === 'object' && html.original) html = html.original.html;
        var msg = typeof r.message === 'string' ? r.message : (r.message && r.message[0]) || '';

        if (r.paymethod_error) {
            log.warn('paymethod_error — alert shown', { message: msg });
            window.LegacyUI.alert({ type: 'warning', message: msg || 'Payment method declined. Please try another card.', duration: 6000 });
            if (html) renderCheckout(html);
            return;
        }

        if (r.visa_error) {
            log.warn('visa_error — alert shown', { message: msg });
            window.LegacyUI.alert({ type: 'error', message: msg || 'Visa is temporarily unavailable. Please try another card.', duration: 6000 });
            if (html) renderCheckout(html);
            return;
        }

        if (r.risk_check) {
            log.warn('risk_check — alert shown', { message: msg });
            window.LegacyUI.alert({ type: 'warning', message: msg || 'Payment did not pass security check. Please try another method.', duration: 6000 });
            if (html) renderCheckout(html);
            return;
        }

        if (msg) {
            window.LegacyUI.alert({ type: 'error', message: msg, duration: 5000 });
        }
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
            if (data.status === 'error') { window.LegacyUI.alert({ type: 'error', message: data.text || 'Crypto error', duration: 5000 }); return; }
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
            if (!ok) { window.LegacyUI.alert({ type: 'error', message: (resp && (resp.text || resp.message)) || 'Validation failed', duration: 5000 }); if (resp && resp.errors) showFieldErrors(resp.errors); return; }
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
        collectBrowserInfo: collectBrowserInfo,
        stopCryptoTimer: stopCryptoTimer
    };

    log.info('checkout module ready', { level: window.CHECKOUT17_LOG_LEVEL || 'debug' });

})(jQuery);
