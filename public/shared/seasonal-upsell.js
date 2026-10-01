(() => {
    'use strict';
    const dialog = document.getElementById('seasonal-upsell');
    if (!dialog || dialog.dataset.initialized || typeof dialog.showModal !== 'function') return;
    const list = dialog.querySelector('.su-items');
    const error = dialog.querySelector('.su-error');
    const proceed = dialog.querySelector('.su-continue');
    const back = dialog.querySelector('.su-back');
    if (!list || !error || !proceed || !back) {
        console.warn('[seasonal-upsell] required popup elements are missing');
        return;
    }
    dialog.dataset.initialized = '1';
    // const skip = dialog.querySelector('.su-skip');
    let loading = false, saving = false, token = '', rows = [], trigger, oldOverflow;

    function node(tag, className, text) {
        const el = document.createElement(tag);
        if (className) el.className = className;
        if (text !== undefined) el.textContent = text;
        return el;
    }
    let openPack = null, packSequence = 0;
    function closePack(restoreFocus = false) {
        if (!openPack) return;
        const {button, panel} = openPack;
        panel.hidden = true;
        button.setAttribute('aria-expanded', 'false');
        openPack = null;
        if (restoreFocus) button.focus();
    }
    function packagePicker(select) {
        const wrapper = node('div', 'su-pack');
        const button = node('button', 'su-pack-button');
        button.type = 'button';
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-expanded', 'false');
        const panel = node('div', 'su-pack-menu');
        panel.id = 'su-pack-menu-' + (++packSequence);
        panel.hidden = true;
        panel.setAttribute('role', 'listbox');
        panel.setAttribute('aria-label', select.getAttribute('aria-label'));
        button.setAttribute('aria-controls', panel.id);
        const options = [...select.options].map((option, index) => {
            const item = node('button', 'su-pack-option', option.textContent);
            item.type = 'button';
            item.setAttribute('role', 'option');
            item.tabIndex = -1;
            item.addEventListener('click', () => {
                select.value = option.value;
                select.dispatchEvent(new Event('change', {bubbles:true}));
                closePack(true);
            });
            item.addEventListener('keydown', event => {
                let next = index;
                if (event.key === 'ArrowDown') next = (index + 1) % options.length;
                else if (event.key === 'ArrowUp') next = (index + options.length - 1) % options.length;
                else if (event.key === 'Home') next = 0;
                else if (event.key === 'End') next = options.length - 1;
                else return;
                event.preventDefault();
                options[next].focus();
            });
            panel.append(item);
            return item;
        });
        const sync = () => {
            const label = select.options[select.selectedIndex]?.textContent || '';
            button.textContent = label;
            button.title = label;
            button.setAttribute('aria-label', select.getAttribute('aria-label') + ': ' + label);
            options.forEach((item, index) => item.setAttribute('aria-selected', String(index === select.selectedIndex)));
        };
        const open = () => {
            if (saving) return;
            if (openPack?.button === button) { closePack(); return; }
            closePack();
            panel.hidden = false;
            const rect = button.getBoundingClientRect();
            const bounds = dialog.getBoundingClientRect();
            const width = Math.min(Math.max(rect.width, 260), bounds.width - 24);
            const below = bounds.bottom - rect.bottom - 12;
            const above = rect.top - bounds.top - 12;
            const upwards = below < 160 && above > below;
            panel.style.width = width + 'px';
            panel.style.left = Math.max(bounds.left + 12, Math.min(rect.left, bounds.right - width - 12)) + 'px';
            panel.style.maxHeight = Math.max(50, Math.min(240, upwards ? above : below)) + 'px';
            panel.style.top = upwards ? 'auto' : (rect.bottom + 6) + 'px';
            panel.style.bottom = upwards ? (window.innerHeight - rect.top + 6) + 'px' : 'auto';
            openPack = {button, panel};
            button.setAttribute('aria-expanded', 'true');
            options[Math.max(0, select.selectedIndex)]?.focus({preventScroll:true});
        };
        button.addEventListener('click', open);
        button.addEventListener('keydown', event => {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                if (!openPack || openPack.button !== button) open();
            }
        });
        select.addEventListener('change', sync);
        select.classList.add('su-native-pack');
        select.hidden = true;
        wrapper.append(select, button);
        dialog.append(panel);
        sync();
        return wrapper;
    }
    dialog.addEventListener('keydown', event => {
        if (!openPack) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            closePack(true);
        } else if (event.key === 'Tab') closePack(true);
    }, true);
    document.addEventListener('pointerdown', event => {
        if (openPack && !openPack.panel.contains(event.target) && !openPack.button.contains(event.target)) closePack();
    });
    dialog.addEventListener('scroll', () => closePack(), {passive:true});
    window.addEventListener('resize', () => closePack());
    async function fetchJSON(url, options = {}, timeout = 6000) {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), timeout);
        try {
            const response = await fetch(url, {credentials:'same-origin', ...options, signal:controller.signal});
            const result = await response.json();
            if (!response.ok) throw new Error(result.errors?.items?.[0] || dialog.dataset.error);
            return result;
        } finally { clearTimeout(timer); }
    }
    function render(data) {
        closePack();
        dialog.querySelectorAll('.su-pack-menu').forEach(panel => panel.remove());
        list.replaceChildren();
        rows = [];
        token = data.token;
        for (const item of data.items) {
            if (!Array.isArray(item.packs) || !item.packs.length) continue;
            const row = node('div', 'su-row');
            const selected = node('input');
            selected.type = 'checkbox';
            selected.setAttribute('aria-label', `${dialog.dataset.selectLabel}: ${item.name}`);
            const choice = node('label', 'su-choice');
            const check = node('span', 'su-check');
            check.setAttribute('aria-hidden', 'true');
            choice.append(selected, check);
            const img = node('img', 'su-image');
            img.src = item.image;
            img.alt = '';
            const name = node('a', 'su-name', item.name);
            name.href = item.url;
            const pack = node('select');
            pack.setAttribute('aria-label', `${dialog.dataset.packLabel}: ${item.name}`);
            item.packs.forEach((p, index) => {
                const option = node('option', '', p.label);
                option.value = String(index);
                pack.append(option);
            });
            const initialPack = item.packs.findIndex(p => String(p.pack_id) === String(item.autocheck));
            pack.selectedIndex = initialPack >= 0 ? initialPack : 0;
            const quantity = node('div', 'su-quantity');
            const minus = node('button', '', '−');
            const plus = node('button', '', '+');
            minus.type = plus.type = 'button';
            minus.setAttribute('aria-label', `${dialog.dataset.quantityLabel} −: ${item.name}`);
            plus.setAttribute('aria-label', `${dialog.dataset.quantityLabel} +: ${item.name}`);
            const count = node('output', '', '1');
            count.setAttribute('aria-live', 'polite');
            const price = node('div', 'su-price');
            const comparison = node('div', 'su-price-comparison');
            const oldPrice = node('del', 'su-price-old');
            const discount = node('span', 'su-price-discount');
            const currentPrice = node('div', 'su-price-current');
            comparison.append(oldPrice, discount);
            price.append(comparison, currentPrice);
            const state = {selected, pack, item, quantity:1};
            const update = () => {
                count.textContent = String(state.quantity);
                const selectedPack = item.packs[Number(pack.value)];
                currentPrice.textContent = (dialog.dataset.pricePrefix ? dialog.dataset.pricePrefix + ' ' : '') + data.currency.symbol + (Number(selectedPack.price) * state.quantity).toFixed(2);
                const compare = Number(selectedPack.compare_price);
                const percent = Number(selectedPack.discount_percent);
                comparison.hidden = !(Number.isFinite(compare) && compare > Number(selectedPack.price) && percent > 0 && percent < 100);
                oldPrice.textContent = comparison.hidden ? '' : data.currency.symbol + (compare * state.quantity).toFixed(2);
                discount.textContent = comparison.hidden ? '' : '−' + percent + '%';
                minus.disabled = state.quantity <= 1;
                plus.disabled = state.quantity >= data.max_quantity;
            };
            minus.addEventListener('click', () => { state.quantity = Math.max(1, state.quantity - 1); update(); });
            plus.addEventListener('click', () => { state.quantity = Math.min(data.max_quantity, state.quantity + 1); update(); });
            pack.addEventListener('change', update);
            quantity.append(minus, count, plus);
            row.append(choice, img, name, packagePicker(pack), quantity, price);
            list.append(row);
            rows.push(state);
            update();
        }
    }
    document.addEventListener('click', async (event) => {
        const link = event.target.closest('a[href], button[data-seasonal-checkout]');
        if (!link || dialog.contains(link) || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        const checkoutHref = link.dataset.seasonalCheckout || link.href;
        const target = new URL(checkoutHref, location.href);
        const checkout = new URL(dialog.dataset.checkout, location.href);
        if (target.origin !== checkout.origin || target.pathname !== checkout.pathname) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        if (loading || dialog.open) return;
        loading = true;
        trigger = link;
        link.setAttribute('aria-busy', 'true');
        try {
            const data = await fetchJSON(dialog.dataset.api, {headers:{Accept:'application/json'}});
            if (!Array.isArray(data.items) || !data.items.length) {
                console.info('[seasonal-upsell] skipped:', data.reason || 'empty response');
                location.assign(checkoutHref); return;
            }
            render(data);
            if (!rows.length) { location.assign(checkoutHref); return; }
            error.hidden = true;
            // skip.hidden = true;
            dialog.showModal();
            oldOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
        } catch (e) {
            console.warn('[seasonal-upsell] API request failed:', e.message);
            location.assign(checkoutHref);
        }
        finally { loading = false; link.removeAttribute('aria-busy'); }
    }, true);
    back.addEventListener('click', () => { if (!saving) dialog.close(); });
    dialog.addEventListener('cancel', (event) => { if (saving) event.preventDefault(); });
    dialog.addEventListener('close', () => {
        closePack();
        document.body.style.overflow = oldOverflow || '';
        if (trigger?.isConnected) trigger.focus();
    });
    // skip.addEventListener('click', event => { if (saving) event.preventDefault(); });
    proceed.addEventListener('click', async () => {
        if (saving) return;
        const items = rows.filter(row => row.selected.checked).map(row => ({
            pack_id:row.item.packs[Number(row.pack.value)].pack_id, quantity:row.quantity
        }));
        if (!items.length) { location.assign(dialog.dataset.checkout); return; }
        closePack();
        saving = true;
        const controls = [...dialog.querySelectorAll('button,input,select')];
        const disabled = controls.map(el => el.disabled);
        controls.forEach(el => { el.disabled = true; });
        error.hidden = true;
        try {
            const result = await fetchJSON(dialog.dataset.add, {
                method:'POST', headers:{'Content-Type':'application/json', Accept:'application/json', 'X-CSRF-TOKEN':dialog.dataset.csrf},
                body:JSON.stringify({token, items})
            }, 20000);
            location.assign(result.redirect || dialog.dataset.checkout);
        } catch (e) {
            error.textContent = e.name === 'AbortError' ? dialog.dataset.error : e.message;
            error.hidden = false;
            // skip.hidden = false;
        } finally {
            saving = false;
            controls.forEach((el, i) => { el.disabled = disabled[i]; });
        }
    });
})();
