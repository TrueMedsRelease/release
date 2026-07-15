(function () {
    'use strict';

    function $(selector, root) {
        return (root || document).querySelector(selector);
    }

    function $$(selector, root) {
        return (root || document).querySelectorAll(selector);
    }

    function refreshCartState() {
        var cartRoute = window.routeCartState || '/cart_state';
        return fetch(cartRoute, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data || data.status !== 'success') return;
            applyCartState(data);
            broadcastCartState(data);
        })
        .catch(function () {});
    }

    function applyCartState(data) {
        if (typeof data.count !== 'undefined') {
            if (data.count === 0 || data.count === '0') {
                var onCheckout = document.querySelector('.checkout_wrapper');
                if (onCheckout) {
                    window.location.href = '/';
                    return;
                }
            }

            $$('.cart__counter').forEach(function (el) {
                el.textContent = data.count;
            });
            $$('.footer-buttons__cart').forEach(function (el) {
                el.setAttribute('data-counter', data.count);
            });
        }

        if (typeof data.total_html !== 'undefined') {
            $$('.cart__total-price').forEach(function (el) {
                el.textContent = data.total_html;
            });
            $$('.footer-buttons__cart .button__price').forEach(function (el) {
                el.textContent = data.total_html;
            });
        }

        if (typeof data.items_html !== 'undefined') {
            $$('.cart__body .cart-items').forEach(function (el) {
                el.innerHTML = data.items_html;
            });
        }

        refreshCheckoutIfNeeded();
    }

    function refreshCheckoutIfNeeded() {
        if (!document.querySelector('.checkout_wrapper')) return;
        if (typeof window.Checkout17 !== 'undefined' && typeof window.Checkout17.loadCheckoutContent === 'function') {
            window.Checkout17.loadCheckoutContent();
        }
    }

    function broadcastCartState(data) {
        if (typeof CrossTabBus === 'undefined') return;
        CrossTabBus.emit('cart:state', {
            count: data.count,
            total_html: data.total_html,
            items_html: data.items_html,
        });
    }

    function initCartSync() {
        if (typeof CrossTabBus === 'undefined') return;
        CrossTabBus.on('cart:state', function (payload) {
            applyCartState(payload);
        });
    }

    function initCartRemove() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.cart-item__remove-button, [data-cart-remove-pack]');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            if (btn.classList.contains('is-loading')) return;

            var packId = btn.getAttribute('data-cart-remove-pack');
            if (!packId) {
                var onclick = btn.getAttribute('onclick') || '';
                var match = onclick.match(/remove\(([^)]+)\)/);
                packId = match ? match[1].replace(/['"\s]/g, '') : '';
            }
            if (!packId) return;

            var removeRoute = window.routeCartRemove || '/cart/remove';
            btn.classList.add('is-loading');
            btn.disabled = true;

            fetch(removeRoute, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                },
                body: 'pack_id=' + encodeURIComponent(packId) + '&id=' + encodeURIComponent(packId) + '&packaging_id=' + encodeURIComponent(packId),
            })
            .then(function () {
                return refreshCartState();
            })
            .catch(function () {
                if (typeof window.LegacyUI !== 'undefined' && window.LegacyUI.alert) {
                    window.LegacyUI.alert('Could not remove this product from cart. Please try again.');
                }
            })
            .then(function () {
                btn.classList.remove('is-loading');
                btn.disabled = false;
            });
        }, true);
    }

    window.CartAside = {
        refresh: refreshCartState
    };

    initCartRemove();
    initCartSync();
})();
