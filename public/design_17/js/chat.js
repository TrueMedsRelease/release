(function () {
    'use strict';

    var POLL_INTERVAL = 1500;
    var POLL_MAX_RETRIES = 80;
    var POLL_NETWORK_MAX_RETRIES = 3;
    var pollNetworkErrors = 0;
    var BADGE_HIDE_DELAY = 3000;

    var TAG = '[Design17Chat]';
    var State = { IDLE: 'idle', SENDING: 'sending', POLLING: 'polling', DONE: 'done', ERROR: 'error' };

    var state = State.IDLE;
    var pollTimer = null;
    var pollRetries = 0;
    var activeSkeletonRow = null;
    var activeMessageId = null;
    var loadingDots = true;
    var currencyPrefix = '$';
    var currencyCoef = 1;

    function formatPrice(price) {
        return currencyPrefix + (price * currencyCoef).toFixed(2);
    }

    function log(level, msg, data) {
        if (typeof console === 'undefined') return;
        var fullMsg = TAG + ' ' + msg;
        if (data !== undefined) {
            console[level](fullMsg, data);
        } else {
            console[level](fullMsg);
        }
    }

    function $(selector, root) {
        return (root || document).querySelector(selector);
    }

    function $$(selector, root) {
        return (root || document).querySelectorAll(selector);
    }

    function getCSRFToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function getChatContainer() {
        return $('.js-chat-container');
    }

    function getChatThread() {
        return $('.js-chat-thread');
    }

    function getChatForm() {
        return $('.js-chat-form');
    }

    function getChatInput() {
        return $('.js-chat-input');
    }

    function scrollToBottom() {
        var thread = getChatThread();
        if (thread) {
            requestAnimationFrame(function () {
                thread.scrollTop = thread.scrollHeight;
            });
        }
    }

    function setState(newState) {
        var old = state;
        state = newState;
        log('debug', 'state: ' + old + ' -> ' + newState);
    }

    function createElement(html) {
        var tpl = document.createElement('template');
        tpl.innerHTML = html.trim();
        return tpl.content.firstChild;
    }

    function activateChat() {
        if (getChatThread()) {
            return true;
        }

        var container = getChatContainer();
        if (!container) {
            return false;
        }

        var heading = container.querySelector('.js-chat-start-heading');
        if (!heading) {
            var existingMessages = container.querySelector('.thread-chat__messages');
            if (existingMessages && existingMessages.children.length > 0) {
                return false;
            }
        }

        if (heading && heading.parentNode) {
            heading.parentNode.removeChild(heading);
        }

        var form = getChatForm();
        var chat = createElement(
            '<div class="thread-chat js-chat-thread-wrap">' +
                '<div class="thread-chat__container">' +
                    '<div class="thread-chat__messages js-chat-thread"></div>' +
                '</div>' +
            '</div>'
        );

        if (form && form.parentNode === container) {
            container.insertBefore(chat, form);
        } else {
            container.appendChild(chat);
        }

        log('debug', 'activateChat: heading removed, thread-chat created');
        return true;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function route(url, messageId) {
        return url.replace('__MSGID__', encodeURIComponent(messageId));
    }

    function htmlToText(html) {
        var div = document.createElement('div');
        div.innerHTML = html;
        return div.textContent || div.innerText || '';
    }

    function formatAnswerText(text) {
        var html = escapeHtml(text);
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/\*(.+?)\*/g, '<em>$1</em>');
        html = html.replace(/\n/g, '<br>');
        return html;
    }

    function svgIcon(name, className) {
        var cls = className || '';
        return '<span class="icon ' + cls + '"><svg width="1em" height="1em" fill="currentColor">' +
            '<use href="' + (window.design17SvgSprite || 'svg/icons/sprite.svg') + '#' + name + '"></use>' +
            '</svg></span>';
    }

    // ── Message rendering ──

    function renderUserMessage(text) {
        var thread = getChatThread();
        if (!thread) return null;

        var row = createElement(
            '<div class="chat-row chat-row--user">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' + escapeHtml(text) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        thread.appendChild(row);
        scrollToBottom();
        return row;
    }

    function renderAgentSkeleton() {
        var thread = getChatThread();
        if (!thread) return null;

        var badgeHtml = '';
        if (loadingDots) {
            badgeHtml =
                '<div class="dc17-chat-status-badge dc17-chat-status-badge--queued">' +
                    '<span class="dc17-chat-status-badge__text">' + getText('status_queued') + '</span>' +
                '</div>';
        }

        var dotsHtml = '';
        if (loadingDots) {
            dotsHtml =
                '<div class="chat-message__pending">' +
                    '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                        '<rect width="48" height="48" rx="24" fill="white"/>' +
                        '<circle cx="17" cy="24" r="2" fill="#13163F"/>' +
                        '<circle cx="24" cy="24" r="2" fill="#13163F"/>' +
                        '<circle cx="31" cy="24" r="2" fill="#13163F"/>' +
                    '</svg>' +
                '</div>';
        }

        var row = createElement(
            '<div class="chat-row chat-row--agent dc17-chat-row--skeleton">' +
                badgeHtml +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        dotsHtml +
                        '<div class="dc17-chat-skeleton-lines dc17-chat-skeleton-lines--fallback">' +
                            '<div class="dc17-chat-skeleton-line"></div>' +
                            '<div class="dc17-chat-skeleton-line dc17-chat-skeleton-line--short"></div>' +
                            '<div class="dc17-chat-skeleton-line dc17-chat-skeleton-line--medium"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        thread.appendChild(row);
        activeSkeletonRow = row;
        scrollToBottom();
        return row;
    }

    function updateSkeletonStatus(status) {
        if (!activeSkeletonRow) return;

        var badge = activeSkeletonRow.querySelector('.dc17-chat-status-badge');
        var text = activeSkeletonRow.querySelector('.dc17-chat-status-badge__text');
        if (!badge || !text) return;

        badge.className = 'dc17-chat-status-badge';

        switch (status) {
            case 'queued':
                badge.className = 'dc17-chat-status-badge dc17-chat-status-badge--queued';
                text.textContent = getText('status_queued');
                break;
            case 'processing':
                badge.className = 'dc17-chat-status-badge dc17-chat-status-badge--processing';
                text.textContent = getText('status_processing');
                break;
            case 'done':
                badge.className = 'dc17-chat-status-badge dc17-chat-status-badge--done';
                text.textContent = getText('status_done');
                break;
            case 'error':
                badge.className = 'dc17-chat-status-badge dc17-chat-status-badge--error';
                text.textContent = getText('status_error');
                break;
        }

        if (status === 'done' || status === 'error') {
            setTimeout(function () {
                if (badge && badge.parentNode) {
                    badge.classList.add('dc17-chat-status-badge--fade-out');
                    setTimeout(function () {
                        if (badge.parentNode) {
                            badge.parentNode.removeChild(badge);
                        }
                    }, 500);
                }
            }, BADGE_HIDE_DELAY);
        }
    }

    function renderAgentAnswer(answer, products) {
        if (!activeSkeletonRow) return;

        var thread = getChatThread();
        if (!thread) return;

        var productCardsHtml = '';
        var hasProducts = products && products.length > 0;

        if (hasProducts) {
            productCardsHtml = renderProductCards(products);
        }

        var answerHtml = formatAnswerText(answer);

        var pageContent = '';
        if (hasProducts) {
            pageContent = '<div class="chat-message__page">' + productCardsHtml + '</div>';
        }

        var rowClass = hasProducts ? 'chat-row chat-row--agent chat-row--product chat-message--appear' : 'chat-row chat-row--agent chat-message--appear';
        var bubbleClass = hasProducts ? 'chat-message__bubble chat-message__bubble--agent' : 'chat-message__bubble';

        var row = createElement(
            '<div class="' + rowClass + '">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="' + bubbleClass + '">' +
                            '<div class="dc17-chat-answer-text">' + answerHtml + '</div>' +
                        '</div>' +
                    '</div>' +
                    pageContent +
                '</div>' +
            '</div>'
        );

        thread.replaceChild(row, activeSkeletonRow);
        activeSkeletonRow = null;
        activeMessageId = null;

        if (hasProducts) {
            bindProductCardClicks(row, products);
        }
        scrollToBottom();
    }

    function renderAgentError(message) {
        var thread = getChatThread();
        if (!thread) return;

        var row = createElement(
            '<div class="chat-row chat-row--agent dc17-chat-row--error">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble dc17-chat-message__bubble--error">' +
                            escapeHtml(message) +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        if (activeSkeletonRow) {
            thread.replaceChild(row, activeSkeletonRow);
            activeSkeletonRow = null;
            activeMessageId = null;
        } else {
            thread.appendChild(row);
        }
        scrollToBottom();
    }

    // ── Product cards ──

    function renderProductCards(products) {
        if (!products || products.length === 0) return '';

        var html = '<div class="product-cards"><div class="cards">';
        products.forEach(function (product, index) {
            var productUrl = product.slug ? ('/' + escapeHtml(product.slug)) : '#';
            var packs = product.packs || [];

            var imageHtml = '';
            if (product.image) {
                imageHtml =
                    '<div class="card__img">' +
                        '<picture>' +
                            '<source type="image/webp" srcset="' + escapeHtml(product.image) + '">' +
                            '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name || '') + '" loading="lazy">' +
                        '</picture>' +
                    '</div>';
            }

            var variantsHtml = '';
            if (packs.length > 0) {
                var seenDosages = {};
                var variants = '';
                packs.forEach(function (pack) {
                    if (pack.dosage && !seenDosages[pack.dosage]) {
                        seenDosages[pack.dosage] = true;
                        variants += '<div class="card__variant">' + escapeHtml(pack.dosage) + '</div>';
                    }
                });
                if (variants) {
                    variantsHtml = '<div class="card__variants">' + variants + '</div>';
                }
            }

            var discountHtml = '';
            var maxDiscount = 0;
            packs.forEach(function (pack) {
                if (pack.old_price && pack.old_price > pack.price) {
                    var d = Math.round((1 - pack.price / pack.old_price) * 100);
                    if (d > maxDiscount) maxDiscount = d;
                }
            });
            if (maxDiscount > 0) {
                discountHtml = '<span class="card__discount">-' + maxDiscount + '%</span>';
            }

            var priceHtml = formatPrice(product.min_price || 0);
            if (packs.length > 0) {
                var minPack = packs[0];
                packs.forEach(function (pack) {
                    if (pack.price < minPack.price) minPack = pack;
                });
                if (minPack.quantity && minPack.quantity > 0) {
                    var perPill = minPack.price / minPack.quantity;
                    priceHtml = formatPrice(perPill) + ' per pill';
                }
            }

            html +=
                '<a class="card-link" href="' + productUrl + '" data-product-index="' + index + '">' +
                    '<article class="card">' +
                        imageHtml +
                        '<div class="card__header">' +
                            '<h2 class="card__title"><span>' + escapeHtml(product.name || '') + '</span></h2>' +
                            (product.dosage
                                ? '<div class="card__description">' + escapeHtml(product.dosage) + '</div>'
                                : '') +
                        '</div>' +
                        variantsHtml +
                        '<div class="card__footer">' +
                            '<div class="card__price-wrapper">' +
                                '<span class="card__price">' + priceHtml + '</span>' +
                                discountHtml +
                            '</div>' +
                            '<button class="card__button button" type="button">' +
                                svgIcon('cart-white') + ' ' +
                                svgIcon('arrow-right') +
                            '</button>' +
                        '</div>' +
                    '</article>' +
                '</a>';
        });
        html += '</div></div>';

        return html;
    }

    function bindProductCardClicks(root, products) {
        var cardLinks = $$('.card-link', root || document);
        cardLinks.forEach(function (link) {
            var btn = link.querySelector('.card__button');
            if (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var index = parseInt(link.getAttribute('data-product-index'), 10);
                    if (!isNaN(index) && products && products[index]) {
                        renderProductDetail(products[index]);
                    }
                });
            }
        });
    }

    // ── Product drawer ──

    var drawerOverlay = null;

    function initProductDrawer() {
        var closeBtn = $('.js-product-drawer-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeProductDrawer);
        }
    }

    function getProductDrawerOverlay() {
        if (!drawerOverlay) {
            drawerOverlay = document.querySelector('.js-product-drawer-overlay');
            if (!drawerOverlay) {
                drawerOverlay = document.createElement('div');
                drawerOverlay.className = 'overlay js-product-drawer-overlay';
                document.body.appendChild(drawerOverlay);
                drawerOverlay.addEventListener('click', closeProductDrawer);
            }
        }
        return drawerOverlay;
    }

    function getProductDrawerBody() {
        return $('.js-product-drawer-body');
    }

    function getProductDrawer() {
        return $('.js-product-drawer');
    }

    function openProductDrawer() {
        var drawer = getProductDrawer();
        var overlay = getProductDrawerOverlay();
        if (!drawer) return;
        if (drawer.classList.contains('is-active')) return;

        drawer.classList.add('is-active');
        overlay.classList.add('is-visible');
        document.body.classList.add('is-locked');
    }

    function closeProductDrawer() {
        var drawer = getProductDrawer();
        var overlay = getProductDrawerOverlay();
        if (!drawer) return;

        drawer.classList.remove('is-active');
        overlay.classList.remove('is-visible');
        if (!document.querySelector('[data-drawer].is-active')) {
            document.body.classList.remove('is-locked');
        }
    }

    function setProductDrawerContent(html, title) {
        var body = getProductDrawerBody();
        var drawer = getProductDrawer();
        if (!body || !drawer) return;

        body.innerHTML = html;

        var titleEl = drawer.querySelector('.drawer__title');
        if (titleEl) {
            titleEl.textContent = title || '';
        }

        bindProductDetailAddButtons(body);
        openProductDrawer();
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var drawer = getProductDrawer();
            if (drawer && drawer.classList.contains('is-active')) {
                closeProductDrawer();
            }
        }
    });

    function renderProductDetail(product) {
        if (!product) return;

        var body = getProductDrawerBody();

        var packs = product.packs || [];
        var packsHtml = '';

        packs.forEach(function (pack) {
            var discountHtml = '';
            if (pack.old_price && pack.old_price > pack.price) {
                var discount = Math.round((1 - pack.price / pack.old_price) * 100);
                discountHtml =
                    '<div class="product__discount"><s>' + formatPrice(pack.old_price) + '</s> ' +
                    '<span>-' + discount + '%</span></div>';
            }

            var deliveryHtml = pack.delivery
                ? '<div class="product__delivery">' + escapeHtml(pack.delivery) + '</div>'
                : '';

            packsHtml +=
                '<tr class="product">' +
                    '<td class="product__info-wrapper">' +
                        '<div class="product__info">' +
                            '<div class="product__quantity">' + escapeHtml(String(pack.quantity)) +
                                (pack.unit ? ' ' + escapeHtml(pack.unit) : '') +
                            '</div>' +
                            deliveryHtml +
                        '</div>' +
                    '</td>' +
                    '<td class="product__price-per-pill"></td>' +
                    '<td>' +
                        '<div class="product__price-wrapper">' +
                            discountHtml +
                            '<div class="product__price">' + formatPrice(pack.price) + '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td class="product__button-wrapper">' +
                        '<button class="button product__button" type="button" data-pack-url="' + escapeHtml(pack.add_url || '') + '">' +
                            svgIcon('cart-white') +
                            '<span class="button__text">' + getText('add_to_cart') + '</span>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
        });

        var imageHtml = '';
        if (product.image) {
            imageHtml =
                '<div class="product-card__image">' +
                    '<picture>' +
                        '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name || '') + '" loading="lazy">' +
                    '</picture>' +
                '</div>';
        }

        var descriptionHtml = product.dosage
            ? '<div class="product-card__description">' + escapeHtml(product.dosage) + '</div>'
            : '';

        var contentHtml =
            '<div class="chat-message__page">' +
                '<div class="product-card">' +
                    imageHtml +
                    '<div class="product-card__content">' +
                        '<div class="product-card__name h1">' + escapeHtml(product.name || '') + '</div>' +
                        descriptionHtml +
                    '</div>' +
                '</div>' +
                '<div class="panel">' +
                    '<table class="table product-table">' +
                        '<thead>' +
                            '<tr>' +
                                '<th>' + getText('package') + '</th>' +
                                '<th>' + getText('per_item') + '</th>' +
                                '<th>' + getText('price') + '</th>' +
                                '<th></th>' +
                            '</tr>' +
                        '</thead>' +
                        '<tbody>' + packsHtml + '</tbody>' +
                    '</table>' +
                '</div>' +
            '</div>';

        if (body) {
            setProductDrawerContent(contentHtml, product.name || '');
        } else {
            var thread = getChatThread();
            if (!thread) return;

            var row = createElement(
                '<div class="chat-row chat-row--page chat-message--appear js-chat-product-detail">' +
                    '<div class="chat-message">' +
                        '<div class="chat-message__content content"></div>' +
                        contentHtml +
                    '</div>' +
                '</div>'
            );

            thread.appendChild(row);
            bindProductDetailAddButtons(row);
            scrollToBottom();
        }

        log('debug', 'renderProductDetail: ' + (product.name || ''));
    }

    function bindProductDetailAddButtons(root) {
        var buttons = $$('.product__button', root || document);
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var packUrl = btn.getAttribute('data-pack-url');
                addToCart(btn, packUrl);
            });
        });
    }

    function addToCart(btn, packUrl) {
        if (!packUrl) {
            log('error', 'addToCart: no URL');
            return;
        }

        var originalHtml = btn ? btn.innerHTML : null;
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = svgIcon('cart-white') + ' <span class="button__text">' + getText('adding') + '</span>';
        }

        fetch(packUrl, {
            method: 'GET',
            redirect: 'follow',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(function (response) {
            if (!response.ok && !response.redirected) {
                throw new Error('HTTP ' + response.status);
            }
            return refreshCartState();
        })
        .then(function () {
            log('debug', 'addToCart: success');

            if (typeof window.LegacyUI !== 'undefined' && window.LegacyUI.alert) {
                window.LegacyUI.alert(getText('added_to_cart'));
            }
        })
        .catch(function (err) {
            log('error', 'addToCart error: ' + err.message);
            if (typeof window.LegacyUI !== 'undefined' && window.LegacyUI.alert) {
                window.LegacyUI.alert(getText('cart_error'));
            }
        })
        .then(function () {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
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

            if (typeof data.count !== 'undefined') {
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
        })
        .catch(function () {});
    }

    // ── Polling ──

    function startPolling(messageId) {
        if (pollTimer) {
            clearInterval(pollTimer);
        }

        activeMessageId = messageId;
        pollRetries = 0;
        pollNetworkErrors = 0;

        pollTimer = setInterval(function () {
            pollMessage(messageId);
        }, POLL_INTERVAL);

        pollMessage(messageId);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    function pollMessage(messageId) {
        pollRetries++;

        if (pollRetries > POLL_MAX_RETRIES) {
            stopPolling();
            setState(State.ERROR);
            updateSkeletonStatus('error');
            renderAgentError(getText('error_timeout'));
            return;
        }

        var pollRoute = window.routeChatPoll || '/chat/poll/__MSGID__';
        var url = route(pollRoute, messageId);

        fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
            },
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                stopPolling();
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(data.message || getText('error_unknown'));
                return;
            }

            updateSkeletonStatus(data.status);

            if (data.status === 'done') {
                stopPolling();
                setState(State.DONE);
                if (data.currency && data.currency.prefix) {
                    currencyPrefix = data.currency.prefix;
                    currencyCoef = data.currency.coef || 1;
                }
                renderAgentAnswer(data.answer || '', data.products);
            } else if (data.status === 'error') {
                stopPolling();
                setState(State.ERROR);
                renderAgentError(data.message || getText('error_unknown'));
            }
        })
        .catch(function (err) {
            log('error', 'pollMessage: fetch error - ' + err.message);
            pollNetworkErrors++;
            if (pollNetworkErrors >= POLL_NETWORK_MAX_RETRIES || pollRetries >= POLL_MAX_RETRIES) {
                stopPolling();
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(getText('error_network'));
            }
        });
    }

    // ── Main flow ──

    function sendMessage(text) {
        if (state === State.SENDING || state === State.POLLING) {
            log('warn', 'sendMessage: blocked, current state=' + state);
            return;
        }

        var cleanText = text.trim();
        if (!cleanText) {
            return;
        }

        if (cleanText.length > 512) {
            if (typeof window.LegacyUI !== 'undefined' && window.LegacyUI.alert) {
                window.LegacyUI.alert(getText('error_too_long'));
            }
            return;
        }

        setState(State.SENDING);
        var activated = activateChat();
        if (!activated) {
            setState(State.IDLE);
            log('warn', 'sendMessage: could not activate chat thread');
            return;
        }
        renderUserMessage(cleanText);
        renderAgentSkeleton();
        setState(State.POLLING);

        var sendRoute = window.routeChatSend || '/chat/send';

        fetch(sendRoute, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: cleanText }),
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (err) {
                    throw new Error(err.message || 'HTTP ' + response.status);
                });
            }
            return response.json();
        })
        .then(function (data) {
            if (!data.success || !data.message_id) {
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(data.message || getText('error_unknown'));
                return;
            }

            startPolling(data.message_id);
        })
        .catch(function (err) {
            setState(State.ERROR);
            updateSkeletonStatus('error');
            renderAgentError(err.message || getText('error_network'));
        });
    }

    // ── Internationalization ──

    var texts = {
        status_queued: 'In queue',
        status_processing: 'Processing...',
        status_done: 'Done',
        status_error: 'Error',
        error_timeout: 'Request timed out. Please try again.',
        error_network: 'Network error. Please check your connection and try again.',
        error_unknown: 'Something went wrong. Please try again.',
        error_too_long: 'Message is too long. Please shorten it.',
        from: 'from',
        select: 'Select',
        add_to_cart: 'Add to cart',
        adding: 'Adding...',
        added_to_cart: 'Item added to cart',
        cart_error: 'Failed to add to cart. Please try again.',
        close: 'Close',
        package: 'Package',
        per_item: 'Per item',
        price: 'Price',
    };

    function getText(key) {
        if (window.design17ChatTexts && window.design17ChatTexts[key]) {
            return window.design17ChatTexts[key];
        }
        return texts[key] || key;
    }

    // ── History ──

    function loadHistory() {
        var container = getChatContainer();
        if (container) {
            var existingMessages = container.querySelector('.thread-chat__messages');
            if (existingMessages && existingMessages.children.length > 0) {
                return;
            }
        }

        var historyRoute = window.routeChatHistory || '/chat/history';
        log('debug', 'loadHistory');

        fetch(historyRoute, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success || !data.messages) return;
            if (!data.messages.length) return;

            activateChat();

            data.messages.forEach(function (msg) {
                if (msg.role === 'user') {
                    renderHistoryUserMessage(msg.content);
                } else if (msg.role === 'assistant') {
                    renderHistoryAssistantMessage(msg.content, msg.products);
                }
            });

            scrollToBottom();
            log('debug', 'loadHistory: ' + data.messages.length + ' messages restored');
        })
        .catch(function (err) {
            log('warn', 'loadHistory error: ' + err.message);
        });
    }

    function renderHistoryUserMessage(text) {
        var thread = getChatThread();
        if (!thread) return;
        var row = createElement(
            '<div class="chat-row chat-row--user">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' + escapeHtml(text) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
        thread.appendChild(row);
    }

    function renderHistoryAssistantMessage(text, products) {
        var thread = getChatThread();
        if (!thread) return;
        var hasProducts = products && products.length;
        var productCardsHtml = hasProducts ? renderProductCards(products) : '';
        var textHtml = '<div class="dc17-chat-answer-text">' + formatAnswerText(text) + '</div>';
        var pageContent = hasProducts ? '<div class="chat-message__page">' + productCardsHtml + '</div>' : '';
        var bubbleClass = hasProducts ? 'chat-message__bubble chat-message__bubble--agent' : 'chat-message__bubble';

        var row = createElement(
            '<div class="chat-row chat-row--agent">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="' + bubbleClass + '">' +
                            textHtml +
                        '</div>' +
                    '</div>' +
                    pageContent +
                '</div>' +
            '</div>'
        );
        thread.appendChild(row);
        if (hasProducts) {
            bindProductCardClicks(row, products);
        }
    }

    // ── Init ──

    function init() {
        log('debug', 'init');

        initProductDrawer();

        var formEl = getChatForm();
        var input = getChatInput();

        if (!formEl || !input) {
            log('warn', 'init: form or input not found');
            return;
        }

        loadHistory();

        var submitBtn = formEl.querySelector('.js-chat-submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var text = input.value;
                input.value = '';
                sendMessage(text);
            });
        }

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                var text = input.value;
                input.value = '';
                sendMessage(text);
            }
        });
    }

    // ── Public API ──

    window.Design17Chat = {
        init: init,
        sendMessage: sendMessage,
        pollMessage: pollMessage,
        renderUserMessage: renderUserMessage,
        renderAgentSkeleton: renderAgentSkeleton,
        renderAgentAnswer: renderAgentAnswer,
        renderProductCards: renderProductCards,
        renderProductDetail: renderProductDetail,
        openProductDrawer: openProductDrawer,
        closeProductDrawer: closeProductDrawer,
        setProductDrawerContent: setProductDrawerContent,
        getState: function () { return state; },
    };

    // ── Auto-init ──

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            if (getChatContainer()) {
                init();
            }
        });
    } else {
        if (getChatContainer()) {
            init();
        }
    }

})();
