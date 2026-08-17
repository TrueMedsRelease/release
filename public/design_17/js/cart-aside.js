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
            var hasItems = data.count > 0;

            if (!hasItems) {
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

            $$('.cart__checkout-button').forEach(function (el) {
                el.style.display = hasItems ? '' : 'none';
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

    function refreshCartPage() {
        var el = document.getElementById('shopping_cart');
        if (!el || typeof routeCartContent === 'undefined') return;

        fetch(routeCartContent, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (r) { return r.text(); })
        .then(function (text) {
            try {
                var data = JSON.parse(text);
                if (data && data.html) {
                    el.innerHTML = data.html;
                } else {
                    window.location.reload();
                }
            } catch (e) {
                window.location.reload();
            }
        })
        .catch(function () {
            window.location.reload();
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
            .then(function () {
                refreshCartPage();
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

    function showAddToast(message, isError) {
        var toast = document.getElementById('design17-toast');
        if (!toast) return;

        var textEl = toast.querySelector('.design17-toast__text');
        if (textEl && message) textEl.textContent = message;

        toast.classList.toggle('design17-toast--error', !!isError);
        toast.removeAttribute('hidden');
        toast.classList.remove('is-visible');
        void toast.offsetWidth;
        toast.classList.add('is-visible');

        clearTimeout(showAddToast._timer);
        showAddToast._timer = setTimeout(function () {
            toast.classList.remove('is-visible');
            setTimeout(function () {
                toast.setAttribute('hidden', '');
            }, 300);
        }, 2600);
    }

    function initCartAddForms() {
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('form.js-product-add-form');
            if (!form) return;

            e.preventDefault();

            var action = form.getAttribute('action') || '';
            var btn = form.querySelector('[type="submit"]');

            if (btn) {
                btn.disabled = true;
                btn.classList.add('is-loading');
            }

            fetch(action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            })
            .then(function () {
                return refreshCartState();
            })
            .then(function () {
                var texts = window.design17ChatTexts || {};
                showAddToast(texts.added_to_cart || 'Added to cart');
            })
            .catch(function () {
                var texts = window.design17ChatTexts || {};
                showAddToast(texts.cart_error || 'Could not add this product to cart. Please try again.', true);
            })
            .then(function () {
                if (btn) {
                    btn.classList.remove('is-loading');
                    btn.disabled = false;
                }
            });
        });
    }

    window.CartAside = {
        refresh: refreshCartState
    };

    initCartRemove();
    initCartSync();
    initCartAddForms();
})();

(function () {
    'use strict';

    function updateCheckoutButtonVisibility() {
        var cartDrawer = document.querySelector(
            '[data-drawer="cart"]'
        );

        if (!cartDrawer) {
            return;
        }

        var checkoutFooter = cartDrawer.querySelector(
            '.js-cart-checkout-footer'
        );

        if (!checkoutFooter) {
            return;
        }

        var hasCartItems = Boolean(
            cartDrawer.querySelector('.cart-items .cart-item')
        );

        if (hasCartItems) {
            checkoutFooter.removeAttribute('hidden');
        } else {
            checkoutFooter.setAttribute('hidden', '');
        }
    }

    function initCheckoutButtonVisibility() {
        var cartDrawer = document.querySelector(
            '[data-drawer="cart"]'
        );

        if (!cartDrawer) {
            return;
        }

        updateCheckoutButtonVisibility();

        var observer = new MutationObserver(function () {
            updateCheckoutButtonVisibility();
        });

        observer.observe(cartDrawer, {
            childList: true,
            subtree: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initCheckoutButtonVisibility
        );
    } else {
        initCheckoutButtonVisibility();
    }
})();
