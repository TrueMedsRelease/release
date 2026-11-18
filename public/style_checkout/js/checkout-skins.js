(function () {
    'use strict';

    function root() {
        return document.querySelector('main.enter-info');
    }

    function has(flag) {
        var r = root();
        return !!r && r.classList.contains('ck-f-' + flag);
    }

    function field(name) {
        var form = document.getElementById('order_form');
        return form ? form.querySelector('[name="' + name + '"]') : null;
    }

    function val(name) {
        var el = field(name);
        return el ? String(el.value || '').trim() : '';
    }

    function setState(el, cls, on) {
        if (!el) return;
        el.classList.toggle(cls, on);
        var line = el.closest('.enter-info__line');
        if (line) line.classList.toggle(cls, on);
        // Место под текст ошибки (inline_errors), чтобы он не наползал на следующее поле
        if (cls === 'ck-invalid') {
            var wrap = el.closest('.poopup');
            if (wrap) wrap.classList.toggle('ck-error-gap', on);
        }
    }

    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    // Шаг 1 — контакты и адрес одним блоком, шаг 2 — оплата.
    function stepRules(step) {
        return step === 1 ? contactRules().concat(addressRules()) : [];
    }

    function contactRules() {
        return [
            { name: 'email', ok: function (v) { return EMAIL_RE.test(v); } },
            { name: 'phone', ok: function (v) { return v.replace(/\D/g, '').length >= 5; } }
        ];
    }

    function addressRules() {
        var rules = ['firstname', 'lastname', 'billing_city', 'billing_address', 'billing_zip'];
        if (field('billing_state')) rules.splice(2, 0, 'billing_state');

        var differentShipping = document.getElementById('c_1');
        if (differentShipping && differentShipping.checked) {
            rules.push('shipping_city', 'shipping_address', 'shipping_zip');
            if (field('shipping_state')) rules.push('shipping_state');
        }
        return rules.map(function (name) {
            return { name: name, ok: function (v) { return v !== ''; } };
        });
    }

    function invalidFields(step) {
        return stepRules(step)
            .filter(function (rule) { return field(rule.name) && !rule.ok(val(rule.name)); })
            .map(function (rule) { return rule.name; });
    }

    function markErrors(names) {
        names.forEach(function (name) {
            var popup = document.getElementById('error_' + name);
            if (popup) popup.classList.add('show');
            var el = errorTarget(name);
            setState(el, 'ck-invalid', true);
            setState(el, 'ck-valid', false);
        });
    }

    // Поле для подсветки. У составных полей (expire_date = card_month + card_year)
    // нет одного input с таким name — тогда подсвечиваем обёртку .poopup целиком.
    function errorTarget(name) {
        var el = field(name);
        if (el) return el;
        var popup = document.getElementById('error_' + name);
        return popup ? popup.closest('.poopup') : null;
    }

    function clearError(name) {
        var popup = document.getElementById('error_' + name);
        if (popup) popup.classList.remove('show');
        setState(errorTarget(name), 'ck-invalid', false);
    }

    function scrollToField(name) {
        var el = field(name);
        if (!el) return;
        var target = el.closest('.poopup') || el;
        var y = target.getBoundingClientRect().top + window.pageYOffset - 120;
        window.scrollTo({ top: y, behavior: 'smooth' });
        try { el.focus({ preventScroll: true }); } catch (e) { /* старые браузеры */ }
    }

    function validateSteps(steps) {
        var bad = [];
        steps.forEach(function (s) { bad = bad.concat(invalidFields(s)); });
        if (!bad.length) return true;

        if (has('multistep')) {
            var firstStep = stepOf(field(bad[0]));
            if (firstStep) showStep(firstStep);
        }
        markErrors(bad);
        scrollToField(bad[0]);
        return false;
    }

    function ruleFor(name) {
        var rules = stepRules(1);
        for (var i = 0; i < rules.length; i++) if (rules[i].name === name) return rules[i];
        return null;
    }

    function updateValidMark(name) {
        if (!has('valid_marks')) return;
        var el = field(name);
        var rule = ruleFor(name);
        if (!el || !rule || el.tagName === 'SELECT') return;
        setState(el, 'ck-valid', rule.ok(val(name)));
    }

    function initValidMarks() {
        if (!has('valid_marks')) return;
        stepRules(1).forEach(function (rule) { updateValidMark(rule.name); });
    }

    var AUTOCOMPLETE = {
        email: 'email',
        alt_email: 'email',
        phone: 'tel-national',
        alt_phone: 'tel-national',
        firstname: 'given-name',
        lastname: 'family-name',
        billing_city: 'billing address-level2',
        billing_address: 'billing address-line1',
        billing_zip: 'billing postal-code',
        shipping_city: 'shipping address-level2',
        shipping_address: 'shipping address-line1',
        shipping_zip: 'shipping postal-code',
        card_numb: 'cc-number',
        cvc_2: 'cc-csc'
    };

    var PLACEHOLDERS = {
        email: 'name@example.com',
        alt_email: 'name@example.com'
    };

    function initAutofill() {
        if (!has('autofill')) return;

        var secure = window.location.protocol === 'https:';
        Object.keys(AUTOCOMPLETE).forEach(function (name) {
            var el = field(name);
            if (!el) return;
            if (!secure && AUTOCOMPLETE[name].indexOf('cc-') === 0) return;
            el.setAttribute('autocomplete', AUTOCOMPLETE[name]);
            if (name === 'phone' || name === 'alt_phone' || name === 'card_numb' || name === 'cvc_2') {
                el.setAttribute('inputmode', 'numeric');
            }
            if (PLACEHOLDERS[name] && !el.getAttribute('placeholder')) {
                el.setAttribute('placeholder', PLACEHOLDERS[name]);
            }
        });
    }

    // ---------- soft_payments ----------

    function updatePayLock() {
        if (!has('soft_payments')) return;
        var grid = document.querySelector('.payment-methods-grid');
        if (!grid) return;
        var locked = invalidFields(1).length > 0;
        grid.classList.toggle('ck-pay-locked', locked);
    }

    function payLocked() {
        if (!has('soft_payments')) return false;
        updatePayLock();
        var grid = document.querySelector('.payment-methods-grid');
        return !!grid && grid.classList.contains('ck-pay-locked');
    }

    // Пока данные не заполнены, клик по способу оплаты не переключает его и не ходит
    // на сервер. Вместо этого — подсказка у плиток, красная подсветка всех незаполненных
    // полей (как делала серверная проверка) и переход к первому из них.
    function nudgeToMissing() {
        var grid = document.querySelector('.payment-methods-grid');
        if (grid) {
            grid.classList.add('ck-pay-hint-show');
            clearTimeout(window.__ckHintTimer);
            window.__ckHintTimer = setTimeout(function () {
                grid.classList.remove('ck-pay-hint-show');
            }, 2500);
        }

        // Красная подсветка (поле + сообщение об ошибке) и прокрутка к первому пустому полю.
        validateSteps([1, 2]);
    }

    function selectedPaymentRadio() {
        var panel = document.querySelector('.payment-methods-grid .payment-method-panel.is_selected');
        return panel ? panel.querySelector('input[name="payment_type"]') : null;
    }

    // ---------- multistep ----------

    function stepOf(el) {
        var holder = el && el.closest('[data-ck-step]');
        return holder ? parseInt(holder.getAttribute('data-ck-step'), 10) : null;
    }

    function showStep(step) {
        var r = root();
        if (!r || !has('multistep')) return;

        window.__ckStep = step;

        r.querySelectorAll('[data-ck-step]').forEach(function (el) {
            el.classList.toggle('ck-step-hidden', parseInt(el.getAttribute('data-ck-step'), 10) !== step);
        });

        r.querySelectorAll('.ck-steps__item, .ck-journey__item[data-ck-goto]').forEach(function (li) {
            var n = parseInt(li.getAttribute('data-ck-goto'), 10);
            li.classList.toggle('is_done', n < step);
            li.classList.toggle('is_current', n === step);
        });
    }

    function scrollToSteps() {
        var nav = document.querySelector('[data-ck-steps]');
        if (!nav) return;
        var y = nav.getBoundingClientRect().top + window.pageYOffset - 140;
        if (y < window.pageYOffset) window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
    }

    function initialStep() {
        if (window.__ckStep) return window.__ckStep;
        return invalidFields(1).length ? 1 : 2;
    }

    function checkEmailOnServer(done) {
        if (typeof checkoutValidateEmail === 'undefined' || !field('email')) return done([]);

        var email = val('email');
        var alt = val('alt_email');
        var key = email + '|' + alt;
        var cache = window.__ckEmailCheck || (window.__ckEmailCheck = {});
        if (cache[key]) return done(cache[key]);

        var body = new FormData();
        body.append('email', email);
        if (alt) body.append('alt_email', alt);

        fetch(checkoutValidateEmail, {
            method: 'POST',
            body: body,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                if (res.status === 422) {
                    return res.json().then(function (json) {
                        return (json.errors || []).map(function (err) { return err.field; });
                    });
                }
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return [];
            })
            .then(function (bad) {
                cache[key] = bad;
                done(bad);
            })
            .catch(function () { done([]); });
    }

    function showEmailErrors(bad) {
        if (!bad.length) return;
        if (bad.indexOf('alt_email') !== -1) openOptional();
        markErrors(bad);
        scrollToField(bad[0]);
    }

    function goToStep(to, btn) {
        var proceed = function () {
            showStep(to);
            updatePayLock();
            scrollToSteps();
        };
        if (to < 2 || !field('email')) return proceed();
        if (btn && btn.classList.contains('ck-loading')) return;

        if (btn) {
            btn.classList.add('ck-loading');
            btn.setAttribute('aria-busy', 'true');
        }
        checkEmailOnServer(function (bad) {
            if (btn) {
                btn.classList.remove('ck-loading');
                btn.removeAttribute('aria-busy');
            }
            if (bad.length) {
                showStep(1);
                showEmailErrors(bad);
                return;
            }
            proceed();
        });
    }

    function verifyEmailOnStep2() {
        if ((window.__ckStep || 1) < 2) return;
        checkEmailOnServer(function (bad) {
            if (!bad.length || (window.__ckStep || 1) < 2) return;
            showStep(1);
            updatePayLock();
            showEmailErrors(bad);
        });
    }

    function updateJourney() {
        var r = root();
        if (!r || !has('journey_steps') || has('multistep')) return;
        var pay = r.querySelector('section[data-ck-step="2"]');
        if (!pay) return;
        var reached = pay.getBoundingClientRect().top < window.innerHeight * 0.6;
        var filled = invalidFields(1).length === 0;
        r.querySelectorAll('.ck-journey__item[data-ck-goto]').forEach(function (li) {
            var n = parseInt(li.getAttribute('data-ck-goto'), 10);
            li.classList.toggle('is_done', n === 1 && filled && reached);
            li.classList.toggle('is_current', reached ? n === 2 : n === 1);
        });
    }

    function scrollToSection(step) {
        var r = root();
        var section = r && r.querySelector('section[data-ck-step="' + step + '"]');
        if (!section) return;
        var y = section.getBoundingClientRect().top + window.pageYOffset - 90;
        window.scrollTo({ top: Math.max(0, y), behavior: 'smooth' });
    }

    // ---------- email_typo_hint: "gmial.con → gmail.com?" ----------

    var EMAIL_DOMAINS = [
        'gmail.com', 'googlemail.com', 'yahoo.com', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.de', 'yahoo.es', 'yahoo.it',
        'ymail.com', 'hotmail.com', 'hotmail.co.uk', 'hotmail.fr', 'hotmail.de', 'hotmail.it', 'hotmail.es',
        'outlook.com', 'outlook.fr', 'outlook.de', 'live.com', 'live.co.uk', 'msn.com', 'aol.com', 'icloud.com',
        'me.com', 'mac.com', 'mail.com', 'gmx.com', 'gmx.de', 'gmx.net', 'web.de', 't-online.de', 'orange.fr',
        'free.fr', 'laposte.net', 'sfr.fr', 'wanadoo.fr', 'libero.it', 'virgilio.it', 'comcast.net', 'att.net',
        'verizon.net', 'sbcglobal.net', 'bellsouth.net', 'cox.net', 'charter.net', 'shaw.ca', 'rogers.com',
        'sympatico.ca', 'bigpond.com', 'optusnet.com.au', 'btinternet.com', 'sky.com', 'protonmail.com',
        'proton.me', 'yandex.com', 'seznam.cz', 'wp.pl', 'o2.pl', 'onet.pl', 'telenet.be', 'ziggo.nl', 'kpnmail.nl'
    ];

    // Расстояние Дамерау–Левенштейна (перестановка соседних букв = 1 правка).
    function editDistance(a, b) {
        var d = [];
        for (var i = 0; i <= a.length; i++) { d[i] = [i]; }
        for (var j = 0; j <= b.length; j++) { d[0][j] = j; }
        for (i = 1; i <= a.length; i++) {
            for (j = 1; j <= b.length; j++) {
                var cost = a[i - 1] === b[j - 1] ? 0 : 1;
                d[i][j] = Math.min(d[i - 1][j] + 1, d[i][j - 1] + 1, d[i - 1][j - 1] + cost);
                if (i > 1 && j > 1 && a[i - 1] === b[j - 2] && a[i - 2] === b[j - 1]) {
                    d[i][j] = Math.min(d[i][j], d[i - 2][j - 2] + 1);
                }
            }
        }
        return d[a.length][b.length];
    }

    function emailSuggestion(value) {
        var m = /^([^\s@]+)@([^\s@]+)$/.exec(String(value || '').trim());
        if (!m) return null;
        var domain = m[2].toLowerCase();
        if (EMAIL_DOMAINS.indexOf(domain) !== -1) return null;
        // "john@gmail" — забыли ".com"
        if (domain.indexOf('.') === -1 && EMAIL_DOMAINS.indexOf(domain + '.com') !== -1) {
            return m[1] + '@' + domain + '.com';
        }
        var best = null;
        var bestDistance = 99;
        EMAIL_DOMAINS.forEach(function (candidate) {
            var dist = editDistance(domain, candidate);
            if (dist < bestDistance) { bestDistance = dist; best = candidate; }
        });
        // Короткие домены правим только на 1 букву, чтобы не "исправлять" чужие (ibm.com ≠ aol.com).
        var limit = domain.length >= 9 ? 2 : 1;
        return best && bestDistance <= limit ? m[1] + '@' + best : null;
    }

    function removeEmailHint(input) {
        var wrap = input && (input.closest('.enter-info__input') || input.parentNode);
        var old = wrap && wrap.querySelector('.ck-email-hint');
        if (old) old.remove();
    }

    function showEmailHint(input) {
        removeEmailHint(input);
        var suggestion = emailSuggestion(input.value);
        if (!suggestion) return;

        var r = root();
        var hint = document.createElement('div');
        hint.className = 'ck-email-hint';
        hint.setAttribute('role', 'status');

        var text = document.createElement('span');
        text.appendChild(document.createTextNode((r.getAttribute('data-ck-text-typo') || 'Did you mean') + ' '));
        var b = document.createElement('b');
        b.textContent = suggestion;
        text.appendChild(b);
        text.appendChild(document.createTextNode('?'));
        hint.appendChild(text);

        var fix = document.createElement('button');
        fix.type = 'button';
        fix.className = 'ck-email-hint__fix';
        fix.textContent = r.getAttribute('data-ck-text-typo-fix') || 'Fix';
        fix.addEventListener('click', function () {
            input.value = suggestion;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            removeEmailHint(input);
            input.focus();
        });
        hint.appendChild(fix);

        (input.closest('.enter-info__input') || input.parentNode).appendChild(hint);
    }

    // ---------- collapse_optional ----------

    function initOptional() {
        var r = root();
        if (!r || !has('collapse_optional')) return;
        if (window.__ckOptionalOpen || val('alt_phone') || val('alt_email')) {
            r.classList.add('ck-optional-open');
        }
    }

    function openOptional() {
        var r = root();
        if (!r) return;
        window.__ckOptionalOpen = true;
        r.classList.add('ck-optional-open');
    }

    function observeServerErrors() {
        if (window.__ckObserver) window.__ckObserver.disconnect();
        var r = root();
        if (!r || typeof MutationObserver === 'undefined') return;

        var handled = false;
        window.__ckObserver = new MutationObserver(function (mutations) {
            var first = null;
            mutations.forEach(function (m) {
                var el = m.target;
                if (!el.classList || !el.classList.contains('poopuptext') || !el.classList.contains('show')) return;
                if (!el.id || el.id.indexOf('error_') !== 0) return;
                var name = el.id.slice(6);
                setState(errorTarget(name), 'ck-invalid', true);
                if (name === 'alt_phone' || name === 'alt_email') openOptional();
                if (!first) first = el;
            });

            if (first && !handled && has('multistep')) {
                var step = stepOf(first);
                if (step && step !== window.__ckStep) {
                    handled = true;
                    showStep(step);
                    scrollToField(first.id.slice(6));
                    setTimeout(function () { handled = false; }, 300);
                }
            }
        });

        window.__ckObserver.observe(r, { subtree: true, attributes: true, attributeFilter: ['class'] });
    }

    function enhanceCodeSelect(selectItem) {
        var original = selectItem.querySelector('select');
        var options = selectItem.querySelector('.select__options');
        if (!original || !options) return;

        var byValue = {};
        Array.prototype.forEach.call(original.options, function (o) { byValue[o.value] = o; });

        options.querySelectorAll('.select__option').forEach(function (btn) {
            if (btn.hasAttribute('data-ck-search')) return;
            var o = byValue[btn.getAttribute('data-value')];
            if (!o) return;
            var name = o.getAttribute('data-name') || '';
            var text = o.textContent.trim();
            btn.setAttribute('data-ck-search', (name + ' ' + o.value + ' ' + text).toLowerCase());

            btn.setAttribute('data-ck-code', selectItem.classList.contains('select_code') ? text.replace(/\D/g, '') : '');
            btn.setAttribute('data-ck-selected', btn.hidden ? '1' : '');

            if (name) btn.setAttribute('title', name);
        });

        if (!options.querySelector('.ck-code-search')) {
            var box = document.createElement('div');
            box.className = 'ck-code-search';
            var input = document.createElement('input');
            input.type = 'text';
            input.className = 'ck-code-search__input';
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('placeholder', original.getAttribute('data-search-placeholder') || 'Search');
            input.setAttribute('aria-label', original.getAttribute('data-search-placeholder') || 'Search');
            box.appendChild(input);
            options.insertBefore(box, options.firstChild);

            var empty = document.createElement('div');
            empty.className = 'ck-code-empty';
            empty.hidden = true;
            empty.textContent = original.getAttribute('data-search-empty') || '';
            options.appendChild(empty);
        }

        filterCodeSelect(selectItem, '');
        options.querySelector('.ck-code-search__input').value = '';
    }

    function filterCodeSelect(selectItem, query) {
        var options = selectItem.querySelector('.select__options');
        if (!options) return;
        var q = String(query || '').trim().toLowerCase();
        var byCode = /^\+?\d+$/.test(q);
        var digits = q.replace(/\D/g, '');
        var any = false;

        options.querySelectorAll('.select__option').forEach(function (btn) {
            var code = btn.getAttribute('data-ck-code') || '';
            var match = !q || (byCode && code
                ? code.indexOf(digits) === 0
                : (btn.getAttribute('data-ck-search') || '').indexOf(q) !== -1);

            btn.hidden = !match || (!q && btn.getAttribute('data-ck-selected') === '1');
            if (!btn.hidden) any = true;
        });

        var empty = options.querySelector('.ck-code-empty');
        if (empty) empty.hidden = any;
    }

    function canAutofocus() {
        return !window.matchMedia || window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    }

    function bindOnce() {
        if (window.__ckBound) return;
        window.__ckBound = true;

        var onEdit = function (e) {
            var el = e.target;
            if (!el || !el.name || !el.closest || !el.closest('#order_form')) return;
            clearError(el.name);
            if ((el.name === 'card_month' || el.name === 'card_year') && val('card_month') && val('card_year')) {
                clearError('expire_date');
            }
            updateValidMark(el.name);
            updatePayLock();
            updateJourney();
            if (e.type === 'input' && (el.name === 'email' || el.name === 'alt_email')) removeEmailHint(el);
        };


        document.addEventListener('focusout', function (e) {
            var el = e.target;
            if (!el || !el.name || !el.closest || !el.closest('#order_form')) return;
            if (has('email_typo_hint') && (el.name === 'email' || el.name === 'alt_email')) showEmailHint(el);
            if (!has('validate_on_blur') || el.tagName === 'SELECT') return;
            var rule = ruleFor(el.name);
            var v = val(el.name);
            if (rule && v !== '' && !rule.ok(v)) {
                markErrors([el.name]);
                return;
            }


            if (has('multistep') && (el.name === 'email' || el.name === 'alt_email') && v !== '') {
                checkEmailOnServer(function (bad) {
                    if (bad.indexOf(el.name) !== -1 && val(el.name) === v) markErrors([el.name]);
                });
            }
        });

        var journeyTick = false;
        window.addEventListener('scroll', function () {
            if (journeyTick) return;
            journeyTick = true;
            window.requestAnimationFrame(function () { journeyTick = false; updateJourney(); });
        }, { passive: true });
        document.addEventListener('input', function (e) {
            var t = e.target;
            if (t && t.classList && t.classList.contains('ck-code-search__input')) {
                filterCodeSelect(t.closest('.select'), t.value);
                return;
            }
            onEdit(e);
        });
        document.addEventListener('change', onEdit);

        document.addEventListener('click', function (e) {
            var title = e.target && e.target.closest ? e.target.closest('.select_code .select__title, .select_search .select__title') : null;
            if (!title) return;
            var selectItem = title.closest('.select');
            if (selectItem.classList.contains('_select-open')) return;
            enhanceCodeSelect(selectItem);
            if (canAutofocus()) {
                setTimeout(function () {
                    var input = selectItem.querySelector('.ck-code-search__input');
                    if (input && selectItem.classList.contains('_select-open')) input.focus();
                }, 60);
            }
        }, true);

        document.addEventListener('click', function (e) {
            var t = e.target;
            if (!t || !t.closest) return;

            document.querySelectorAll('[data-ck-phones-more][open]').forEach(function (d) {
                if (!d.contains(t)) d.removeAttribute('open');
            });

            var next = t.closest('[data-ck-next]');
            if (next) {
                e.preventDefault();
                var to = parseInt(next.getAttribute('data-ck-next'), 10);
                var steps = [];
                for (var s = 1; s < to; s++) steps.push(s);
                if (validateSteps(steps)) goToStep(to, next);
                return;
            }

            var go = t.closest('[data-ck-goto]');
            if (go && !has('multistep') && go.classList.contains('ck-journey__item')) {
                e.preventDefault();
                scrollToSection(parseInt(go.getAttribute('data-ck-goto'), 10));
                return;
            }
            if (go && has('multistep')) {
                var n = parseInt(go.getAttribute('data-ck-goto'), 10);
                if (n < (window.__ckStep || 1)) {
                    e.preventDefault();
                    showStep(n);
                    scrollToSteps();
                }
                return;
            }

            // collapse_optional
            if (t.closest('[data-ck-optional-toggle]')) {
                e.preventDefault();
                openOptional();
                var alt = field('alt_email');
                if (alt) alt.focus();
                return;
            }

        });

        document.addEventListener('click', function (e) {
            var panel = e.target && e.target.closest ? e.target.closest('.payment-methods-grid .payment-method-panel') : null;
            if (!panel || !payLocked()) return;
            e.preventDefault();
            e.stopPropagation();
            nudgeToMissing();
        }, true);


        document.addEventListener('change', function (e) {
            var t = e.target;
            if (!t || t.name !== 'payment_type' || !payLocked()) return;
            e.stopPropagation();
            var prev = selectedPaymentRadio();
            if (prev && prev !== t) prev.checked = true;
            nudgeToMissing();
        }, true);

        document.addEventListener('keydown', function (e) {

            if (e.target && e.target.classList && e.target.classList.contains('ck-code-search__input')) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    var first = e.target.closest('.select__options').querySelector('.select__option:not([hidden])');
                    if (first) first.click();
                }
                return;
            }
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-ck-phones-more][open]').forEach(function (d) {
                    d.removeAttribute('open');
                });
            }
            if (e.key !== 'Enter' || !has('multistep')) return;
            var el = e.target;
            if (!el || el.tagName !== 'INPUT' || !el.closest('#order_form')) return;
            if (stepOf(el) !== 1) return;
            e.preventDefault();
            
            var missing = invalidFields(1).filter(function (name) { return name !== el.name; });
            if (missing.length) {
                scrollToField(missing[0]);
                return;
            }
            var next = root().querySelector('.ck-step-actions[data-ck-step="1"] [data-ck-next]');
            if (next) next.click();
        });
    }

    function init() {
        if (!root()) return;
        bindOnce();
        initAutofill();
        initOptional();
        initValidMarks();
        if (has('multistep')) {
            showStep(initialStep());
            verifyEmailOnStep2();
        }
        updatePayLock();
        updateJourney();
        observeServerErrors();
    }

    init();
})();
