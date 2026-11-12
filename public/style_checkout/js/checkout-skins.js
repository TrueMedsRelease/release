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
            var el = field(name);
            setState(el, 'ck-invalid', true);
            setState(el, 'ck-valid', false);
        });
    }

    function clearError(name) {
        var popup = document.getElementById('error_' + name);
        if (popup) popup.classList.remove('show');
        setState(field(name), 'ck-invalid', false);
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

        r.querySelectorAll('.ck-steps__item').forEach(function (li) {
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
                setState(field(name), 'ck-invalid', true);
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
            var code = o.textContent.trim();
            btn.setAttribute('data-ck-search', (name + ' ' + o.value + ' ' + code).toLowerCase());
            btn.setAttribute('data-ck-code', code.replace(/\D/g, ''));
            btn.setAttribute('data-ck-selected', btn.hidden ? '1' : '');
            // Название страны в списке не показываем, но искать по нему можно.
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
            var match = !q || (byCode
                ? (btn.getAttribute('data-ck-code') || '').indexOf(digits) === 0
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
            updateValidMark(el.name);
            updatePayLock();
        };
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
            var title = e.target && e.target.closest ? e.target.closest('.select_code .select__title') : null;
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
                if (validateSteps(steps)) {
                    showStep(to);
                    updatePayLock();
                    scrollToSteps();
                }
                return;
            }

            var go = t.closest('[data-ck-goto]');
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

        // soft_payments: клик по плитке оплаты при незаполненных данных — без переключения.
        document.addEventListener('click', function (e) {
            var panel = e.target && e.target.closest ? e.target.closest('.payment-methods-grid .payment-method-panel') : null;
            if (!panel || !payLocked()) return;
            e.preventDefault();
            e.stopPropagation();
            nudgeToMissing();
        }, true);

        // То же для клавиатуры (стрелки в группе радиокнопок): откатываем выбор
        // до того, как сработает обработчик app.js.
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
            // Enter ведёт к следующему незаполненному полю, а когда пусто только текущее
            // (или всё заполнено) — жмёт «Продолжить».
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
        if (has('multistep')) showStep(initialStep());
        updatePayLock();
        observeServerErrors();
    }

    init();
})();
