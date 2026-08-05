(function () {
    'use strict';

    var POLL_INTERVAL = window.medbotPollInterval || 15000;
    var POLL_MAX_RETRIES = 24;
    var POLL_NETWORK_MAX_RETRIES = 10;
    var pollNetworkErrors = 0;
    var BADGE_HIDE_DELAY = 3000;
    var STATUS_TEXT_ROTATE_INTERVAL = 3000;

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

    // ── Cross-tab sync ──
    var isLeader = false;
    var leaderTabId = null;
    var heartbeatTimer = null;
    var leaderClaimTimer = null;
    var leaderTimeout = null;
    var remoteSkeletonRow = null;
    var crossBus = null;
    var currentQuery = '';
    var statusTextTimer = null;
    var activeStatusName = null;

    function initCrossTabSync() {
        if (typeof CrossTabBus === 'undefined') return;
        crossBus = CrossTabBus;
        crossBus.log.setLevel('warn');

        crossBus.on('chat:leader', function (payload) {
            if (!isLeader && !leaderTabId) {
                leaderTabId = payload.tabId;
                log('info', 'Leader: ' + leaderTabId.slice(-4));
                resetLeaderTimeout();
            }
        });

        crossBus.on('chat:claim', function (payload) {
            if (isLeader && payload.tabId !== crossBus.getTabId()) {
                crossBus.emit('chat:leader', { tabId: crossBus.getTabId() });
            }
        });

        crossBus.on('chat:leader-gone', function () {
            if (!isLeader) {
                log('info', 'Leader gone');
                leaderTabId = null;
                stopLeaderWatch();
                tryClaimLeader();
            }
        });

        crossBus.on('chat:heartbeat', function (payload) {
            if (!isLeader && payload.tabId === leaderTabId) {
                resetLeaderTimeout();
            }
        });

        crossBus.on('chat:query', function (payload) {
            if (isLeader) {
                if (state === State.SENDING || state === State.POLLING) {
                    crossBus.emit('chat:response', {
                        status: 'error',
                        message: getText('chat_busy'),
                    });
                    return;
                }
                log('info', 'Follower query: ' + (payload.message || '').slice(0, 30));
                sendMessage(payload.message);
            }
        });

        crossBus.on('chat:response', function (payload) {
            if (isLeader) return;

            if (activeSkeletonRow || remoteSkeletonRow) {
                if (payload.currency) {
                    currencyPrefix = payload.currency.prefix;
                    currencyCoef = payload.currency.coef || 1;
                }
                if (payload.status === 'done') {
                    renderAgentAnswer(payload.answer || '', payload.products);
                } else if (payload.status === 'error') {
                    renderAgentError(payload.message || getText('error_unknown'));
                }
                remoteSkeletonRow = null;
            } else {
                renderRemoteMessage(payload);
            }
        });

        crossBus.on('chat:busy', function (payload) {
            if (isLeader) return;
            var input = getChatInput();
            var submit = document.querySelector('.js-chat-submit');
            if (input) {
                input.disabled = payload.busy;
                input.placeholder = payload.busy ? getText('chat_waiting') : getText('chat_placeholder');
            }
            if (submit) {
                submit.disabled = payload.busy;
            }
        });

        window.addEventListener('beforeunload', function () {
            if (isLeader) {
                crossBus.emit('chat:leader-gone', {});
            }
            stopHeartbeat();
            stopLeaderWatch();
        });

        tryClaimLeader();
    }

    function tryClaimLeader() {
        if (!crossBus) return;
        if (leaderClaimTimer) clearTimeout(leaderClaimTimer);
        crossBus.emit('chat:claim', { tabId: crossBus.getTabId() });
        leaderClaimTimer = setTimeout(function () {
            if (leaderTabId && leaderTabId !== crossBus.getTabId()) return;
            isLeader = true;
            leaderTabId = crossBus.getTabId();
            log('info', 'Became leader');
            crossBus.emit('chat:leader', { tabId: crossBus.getTabId() });
            startHeartbeat();
        }, 200);
    }

    function startHeartbeat() {
        stopHeartbeat();
        if (!crossBus) return;
        heartbeatTimer = setInterval(function () {
            crossBus.emit('chat:heartbeat', { tabId: crossBus.getTabId() });
        }, 5000);
    }

    function stopHeartbeat() {
        if (heartbeatTimer) {
            clearInterval(heartbeatTimer);
            heartbeatTimer = null;
        }
    }

    function resetLeaderTimeout() {
        if (leaderTimeout) clearTimeout(leaderTimeout);
        leaderTimeout = setTimeout(function () {
            log('warn', 'Leader timeout');
            leaderTabId = null;
            tryClaimLeader();
        }, 15000);
    }

    function startLeaderWatch() {
        resetLeaderTimeout();
    }

    function stopLeaderWatch() {
        if (leaderTimeout) {
            clearTimeout(leaderTimeout);
            leaderTimeout = null;
        }
    }

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

    function scrollToLastUserRequest(targetRow, behavior) {
        var thread = getChatThread();
        if (!thread) return;

        var row = targetRow;

        if (!row) {
            var userRows = thread.querySelectorAll('.chat-row--user');
            row = userRows.length ? userRows[userRows.length - 1] : null;
        }

        if (!row) return;

        requestAnimationFrame(function () {
            var header = document.querySelector('.header, .site-header, header');
            var headerHeight = header ? header.getBoundingClientRect().height : 0;
            var top = window.scrollY + row.getBoundingClientRect().top - headerHeight - 16;

            window.scrollTo({
                top: Math.max(0, top),
                behavior: behavior || 'smooth',
            });
        });
    }

    function isAtBottom() {
        var thread = getChatThread();
        if (!thread) return true;
        return thread.scrollHeight - thread.scrollTop - thread.clientHeight < 60;
    }

    var newMessagesBtn = null;

    function createNewMessagesIndicator() {
        if (newMessagesBtn) return;
        var container = getChatContainer();
        if (!container) return;
        newMessagesBtn = document.createElement('button');
        newMessagesBtn.className = 'dc17-chat-new-msgs';
        newMessagesBtn.type = 'button';
        newMessagesBtn.textContent = getText('new_messages');
        newMessagesBtn.addEventListener('click', function () {
            var thread = getChatThread();
            if (thread) {
                thread.scrollTo({ top: thread.scrollHeight, behavior: 'smooth' });
            }
            hideNewMessagesIndicator();
        });
        container.appendChild(newMessagesBtn);
    }

    function showNewMessagesIndicator() {
        createNewMessagesIndicator();
        if (newMessagesBtn) {
            newMessagesBtn.classList.add('is-visible');
        }
    }

    function hideNewMessagesIndicator() {
        if (newMessagesBtn) {
            newMessagesBtn.classList.remove('is-visible');
        }
    }

    function initScrollTracking() {
        var thread = getChatThread();
        if (!thread) return;
        thread.addEventListener('scroll', function () {
            if (isAtBottom()) {
                hideNewMessagesIndicator();
            }
        });
    }

    function setState(newState) {
        var old = state;
        state = newState;
        log('debug', 'state: ' + old + ' -> ' + newState);

        var input = getChatInput();
        var submit = document.querySelector('.js-chat-submit');
        var blocked = (newState === State.SENDING || newState === State.POLLING);

        if (input) {
            input.disabled = blocked;
            input.placeholder = blocked ? getText('chat_waiting') : getText('chat_placeholder');
        }
        if (submit) {
            submit.disabled = blocked;
        }

        if (isLeader && crossBus) {
            crossBus.emit('chat:busy', { busy: blocked });
        }
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

        var form = getChatForm();

        var existingChat = container.querySelector('.thread-chat');
        if (existingChat) {
            return true;
        }

        var heading = container.querySelector('.js-chat-start-heading');
        if (heading && heading.parentNode) {
            heading.parentNode.removeChild(heading);
        }

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

        setTimeout(initScrollTracking, 100);

        log('debug', 'activateChat: thread-chat created');
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
        scrollToLastUserRequest(row, 'smooth');
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

        if (loadingDots) {
            startStatusTextRotation('queued');
        }

        return row;
    }

    function stopStatusTextRotation() {
        if (statusTextTimer) {
            clearInterval(statusTextTimer);
            statusTextTimer = null;
        }

        activeStatusName = null;
    }

    function rotateActiveStatusText() {
        if (!activeSkeletonRow || !activeStatusName) {
            stopStatusTextRotation();
            return;
        }

        var badge = activeSkeletonRow.querySelector('.dc17-chat-status-badge');
        var text = activeSkeletonRow.querySelector('.dc17-chat-status-badge__text');

        if (!badge || !text) {
            stopStatusTextRotation();
            return;
        }

        text.textContent = getText('status_' + activeStatusName);
    }

    function startStatusTextRotation(status) {
        stopStatusTextRotation();

        if (status !== 'queued' && status !== 'processing') {
            return;
        }

        activeStatusName = status;
        statusTextTimer = setInterval(
            rotateActiveStatusText,
            STATUS_TEXT_ROTATE_INTERVAL
        );
    }

    function scheduleStatusBadgeRemoval(badge) {
        if (!badge) return;

        setTimeout(function () {
            if (!badge.parentNode) return;

            badge.classList.add('dc17-chat-status-badge--fade-out');

            setTimeout(function () {
                if (badge.parentNode) {
                    badge.parentNode.removeChild(badge);
                }
            }, 500);
        }, BADGE_HIDE_DELAY);
    }

    function getActiveStatusText(status) {
        var className = 'dc17-chat-status-badge--' + status;

        if (activeSkeletonRow) {
            var badge = activeSkeletonRow.querySelector('.dc17-chat-status-badge');
            var text = activeSkeletonRow.querySelector('.dc17-chat-status-badge__text');

            if (
                badge &&
                text &&
                badge.classList.contains(className) &&
                text.textContent.trim()
            ) {
                return text.textContent.trim();
            }
        }

        return getText('status_' + status);
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
                if (!isAtBottom()) {
                    showNewMessagesIndicator();
                }
                break;
            case 'error':
                badge.className = 'dc17-chat-status-badge dc17-chat-status-badge--error';
                text.textContent = getText('status_error');
                break;
            default:
                return;
        }

        if (status === 'queued' || status === 'processing') {
            startStatusTextRotation(status);
        } else {
            stopStatusTextRotation();
        }

        if (status === 'done' || status === 'error') {
            scheduleStatusBadgeRemoval(badge);
        }
    }

    function renderAgentAnswer(answer, products) {
        if (!activeSkeletonRow) return;

        stopStatusTextRotation();
        selectedProductIds = {};

        var thread = getChatThread();
        if (!thread) return;

        var doneStatusText = getActiveStatusText('done');
        var hasProducts = products && products.length > 0;
        var totalProducts = hasProducts ? products.length : 0;
        var needsPagination = totalProducts > 6;

        var productCardsHtml = '';
        if (hasProducts) {
            productCardsHtml = renderProductCards(products);
        }

        var paginationHtml = '';
        if (needsPagination) {
            var remaining = totalProducts - 6;
            paginationHtml =
                '<div class="dc17-chat-products__pagination">' +
                    '<button class="button button--secondary js-chat-show-more" type="button">' +
                        '<span class="js-chat-pagination-label">' + getText('show_more') + ' (' + remaining + ')</span>' +
                    '</button>' +
                '</div>';
        }

        var answerHtml = formatAnswerText(answer);

        var pageContent = '';
        if (hasProducts) {
            pageContent = '<div class="chat-message__page">' + productCardsHtml + paginationHtml + '</div>';
        }

        var rowClass = hasProducts ? 'chat-row chat-row--agent chat-row--product chat-message--appear' : 'chat-row chat-row--agent chat-message--appear';
        var bubbleClass = hasProducts ? 'chat-message__bubble chat-message__bubble--agent' : 'chat-message__bubble';

        var row = createElement(
            '<div class="' + rowClass + '">' +
                '<div class="dc17-chat-status-badge dc17-chat-status-badge--done">' +
                    '<span class="dc17-chat-status-badge__text">' + escapeHtml(doneStatusText) + '</span>' +
                '</div>' +
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

        scheduleStatusBadgeRemoval(
            row.querySelector('.dc17-chat-status-badge--done')
        );

        if (hasProducts) {
            bindProductCardClicks(row, products);
            if (needsPagination) {
                initPagination(row, products);
            }
        }
    }

    function renderAgentError(message) {
        stopStatusTextRotation();

        var thread = getChatThread();
        if (!thread) return;

        var errorStatusText = getActiveStatusText('error');

        var row = createElement(
            '<div class="chat-row chat-row--agent dc17-chat-row--error">' +
                '<div class="dc17-chat-status-badge dc17-chat-status-badge--error">' +
                    '<span class="dc17-chat-status-badge__text">' + escapeHtml(errorStatusText) + '</span>' +
                '</div>' +
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

        scheduleStatusBadgeRemoval(
            row.querySelector('.dc17-chat-status-badge--error')
        );

        scrollToLastUserRequest(null, 'smooth');
    }

    // ── Dosage sorting ──

    function normalizeDosageUnit(unit) {
        unit = String(unit || '')
            .toLowerCase()
            .replace(/\s+/g, '');

        var aliases = {
            'μg': 'mcg',
            'µg': 'mcg',
            'ug': 'mcg',
            'microgram': 'mcg',
            'micrograms': 'mcg',
            'milligram': 'mg',
            'milligrams': 'mg',
            'gram': 'g',
            'grams': 'g',
            'kilogram': 'kg',
            'kilograms': 'kg',
            'milliliter': 'ml',
            'milliliters': 'ml',
            'millilitre': 'ml',
            'millilitres': 'ml',
            'liter': 'l',
            'liters': 'l',
            'litre': 'l',
            'litres': 'l',
            'unit': 'iu',
            'units': 'iu',
        };

        return aliases[unit] || unit;
    }

    function normalizeDosagePart(value, unit) {
        var normalizedUnit = normalizeDosageUnit(unit);
        var group = normalizedUnit || 'number';
        var normalizedValue = value;

        switch (normalizedUnit) {
            case 'mcg':
                group = 'mass';
                normalizedValue = value / 1000;
                break;
            case 'mg':
                group = 'mass';
                break;
            case 'g':
                group = 'mass';
                normalizedValue = value * 1000;
                break;
            case 'kg':
                group = 'mass';
                normalizedValue = value * 1000000;
                break;
            case 'ml':
                group = 'volume';
                break;
            case 'l':
                group = 'volume';
                normalizedValue = value * 1000;
                break;
            case '%':
                group = 'percent';
                break;
            case 'iu':
                group = 'iu';
                break;
            default:
                group = normalizedUnit || 'number';
                break;
        }

        return {
            group: group,
            value: normalizedValue,
            rawValue: value,
        };
    }

    function getDosageSortParts(dosage) {
        var text = String(dosage || '')
            .replace(/,/g, '.');

        var parts = [];
        var pattern = /(-?\d+(?:\.\d+)?)\s*(mcg|μg|µg|ug|mg|g|kg|ml|l|%|iu|units?|micrograms?|milligrams?|grams?|kilograms?|millilit(?:er|re)s?|lit(?:er|re)s?)?/gi;
        var match;

        while ((match = pattern.exec(text)) !== null) {
            var value = parseFloat(match[1]);

            if (!isNaN(value)) {
                parts.push(
                    normalizeDosagePart(value, match[2] || '')
                );
            }
        }

        return parts;
    }

    function compareDosages(a, b) {
        var aParts = getDosageSortParts(a);
        var bParts = getDosageSortParts(b);
        var length = Math.max(aParts.length, bParts.length);

        for (var i = 0; i < length; i++) {
            var aPart = aParts[i];
            var bPart = bParts[i];

            if (!aPart && bPart) return -1;
            if (aPart && !bPart) return 1;
            if (!aPart && !bPart) break;

            /*
             * Если единицы относятся к одной группе, сравниваем уже
             * нормализованные значения: 500 mcg < 1 mg < 2 g.
             */
            if (
                aPart.group === bPart.group &&
                aPart.value !== bPart.value
            ) {
                return aPart.value - bPart.value;
            }

            /*
             * Для редких смешанных обозначений сравниваем числовую часть,
             * чтобы сортировка оставалась предсказуемой.
             */
            if (aPart.rawValue !== bPart.rawValue) {
                return aPart.rawValue - bPart.rawValue;
            }

            if (aPart.group !== bPart.group) {
                return aPart.group.localeCompare(bPart.group);
            }
        }

        return String(a || '').localeCompare(
            String(b || ''),
            undefined,
            {
                numeric: true,
                sensitivity: 'base',
            }
        );
    }

    function sortDosages(dosages, direction) {
        var multiplier = direction === 'desc' ? -1 : 1;

        return dosages.slice().sort(function (a, b) {
            return compareDosages(a, b) * multiplier;
        });
    }

    function getUniqueDosages(packs, direction) {
        var seen = {};
        var dosages = [];

        (packs || []).forEach(function (pack) {
            var dosage = String(pack.dosage || '').trim();
            var key = dosage.toLowerCase();

            if (dosage && !seen[key]) {
                seen[key] = true;
                dosages.push(dosage);
            }
        });

        return sortDosages(dosages, direction || 'asc');
    }

    function buildDosageVariantsHtml(packs) {
        var dosages = getUniqueDosages(packs, 'asc');

        if (!dosages.length) {
            return '';
        }

        return '<div class="card__variants">' +
            dosages.map(function (dosage) {
                return '<div class="card__variant">' +
                    escapeHtml(dosage) +
                '</div>';
            }).join('') +
        '</div>';
    }

    // ── Product cards ──

    function renderProductCards(products) {
        if (!products || products.length === 0) return '';

        var html = '<div class="product-cards"><div class="cards">';
        products.forEach(function (product, index) {
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

            var variantsHtml = buildDosageVariantsHtml(packs);

            var discountHtml = '';
            var maxDiscount = 0;
            var cardMaxPillPrice = 0;
            packs.forEach(function (pack) {
                if (pack.quantity && pack.quantity > 0 && pack.price > 0) {
                    var pp = pack.price / pack.quantity;
                    if (pp > cardMaxPillPrice) cardMaxPillPrice = pp;
                }
            });
            packs.forEach(function (pack) {
                if (cardMaxPillPrice > 0 && pack.quantity > 0 && pack.price > 0) {
                    var oldPrice = cardMaxPillPrice * pack.quantity;
                    if (oldPrice > pack.price) {
                        var d = Math.round((1 - pack.price / oldPrice) * 100);
                        if (d > maxDiscount) maxDiscount = d;
                    }
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
                    var perUnitPrice = minPack.price / minPack.quantity;
                    var unitLabel = (product.product_type || 'pill').toLowerCase();
                    // priceHtml = formatPrice(perUnitPrice) + ' per ' + unitLabel;
                    priceHtml = formatPrice(perUnitPrice);
                }
            }

            html +=
                '<div class="card-link" data-product-index="' + index + '">' +
                    '<article class="card">' +
                        imageHtml +
                        '<div class="card__header">' +
                            '<h2 class="card__title"><span>' + escapeHtml(product.name || '') + '</span></h2>' +
                            variantsHtml +
                        '</div>' +
                        // variantsHtml +
                        '<div class="card__footer">' +
                            '<div class="card__price-wrapper">' +
                                '<span class="card__price">' + priceHtml + '</span>' +
                                discountHtml +
                            '</div>' +
                            '<button class="card__button button" type="button">' +
                                svgIcon('cart-white') +
                            '</button>' +
                        '</div>' +
                    '</article>' +
                '</div>';
        });
        html += '</div></div>';

        return html;
    }

    // function bindProductCardClicks(root, products) {
    //     var buttons = $$('.card__button', root || document);
    //     buttons.forEach(function (btn) {
    //         btn.addEventListener('click', function (e) {
    //             e.preventDefault();
    //             e.stopPropagation();
    //             var cardLink = btn.closest('.card-link');
    //             if (!cardLink) return;
    //             var index = parseInt(cardLink.getAttribute('data-product-index'), 10);
    //             if (!isNaN(index) && products && products[index]) {
    //                 showProductInChat(products[index]);
    //             }
    //         });
    //     });
    // }

    function bindProductCardClicks(root, products) {
        var cards = $$('.card-link', root || document);

        cards.forEach(function (card) {

            if (card.dataset.chatBound) {
                return;
            }

            card.dataset.chatBound = '1';

            card.addEventListener('click', function (e) {
                if (e.target.closest('.card__button')) {
                    return;
                }

                e.preventDefault();

                var button = card.querySelector('.card__button');

                if (button) {
                    button.click();
                }
            });
        });

        var buttons = $$('.card__button', root || document);

        buttons.forEach(function (btn) {

            if (btn.dataset.chatBound) {
                return;
            }

            btn.dataset.chatBound = '1';

            btn.addEventListener('click', function (e) {

                e.preventDefault();
                e.stopPropagation();

                var cardLink = btn.closest('.card-link');

                if (!cardLink) {
                    return;
                }

                var index = parseInt(cardLink.dataset.productIndex, 10);

                if (!isNaN(index) && products && products[index]) {
                    showProductInChat(products[index]);
                }
            });
        });
    }

    function initPagination(root, products) {
        var cards = root.querySelectorAll('.card-link');
        var btn = root.querySelector('.js-chat-show-more');
        var label = root.querySelector('.js-chat-pagination-label');
        if (!cards.length || !btn || !label) return;

        var PER_PAGE = 6;
        var total = products.length;
        var visibleCount = PER_PAGE;

        for (var i = PER_PAGE; i < cards.length; i++) {
            cards[i].style.display = 'none';
        }

        function updateLabel() {
            var hidden = total - visibleCount;
            if (visibleCount >= total) {
                label.textContent = getText('show_less');
            } else {
                label.textContent = getText('show_more') + ' (' + hidden + ')';
            }
        }

        btn.addEventListener('click', function () {
            if (visibleCount >= total) {
                for (var j = PER_PAGE; j < cards.length; j++) {
                    cards[j].style.display = 'none';
                }
                visibleCount = PER_PAGE;
            } else {
                var next = Math.min(visibleCount + PER_PAGE, total);
                for (var k = visibleCount; k < next; k++) {
                    cards[k].style.display = '';
                }
                visibleCount = next;
            }
            updateLabel();
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

        if (window.scrollY > 0) {
            document.body.style.setProperty('--top-body-offset', '-' + window.scrollY + 'px');
        }
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
            var offset = Math.abs(parseInt(document.body.style.getPropertyValue('--top-body-offset') || '0', 10));
            document.body.style.removeProperty('--top-body-offset');
            if (offset > 0) {
                window.scrollTo({ top: offset, left: 0, behavior: 'instant' });
            }
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

    var selectedProductIds = {};

    function showProductInChat(product) {
        if (!product) return;

        if (selectedProductIds[product.id]) return;
        selectedProductIds[product.id] = true;

        var thread = getChatThread();
        if (!thread) return;

        var userName = product.name || '';

        var userRow = createElement(
            '<div class="chat-row chat-row--user chat-message--appear">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' + escapeHtml(userName) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
        thread.appendChild(userRow);

        var packs = product.packs || [];

        var variantsHtml = getUniqueDosages(packs, 'asc')
            .map(function (dosage) {
                return escapeHtml(dosage);
            })
            .join(' | ');

        var agentText = variantsHtml
            ? escapeHtml(userName) + ' - ' + variantsHtml
            : escapeHtml(userName);
        var agentRow = createElement(
            '<div class="chat-row chat-row--agent chat-message--appear">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble chat-message__bubble--agent">' + agentText + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
        thread.appendChild(agentRow);

        var detailHtml = buildProductDetailHtml(product);
        var pageRow = createElement(
            '<div class="chat-row chat-row--page chat-message--appear">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content"></div>' +
                    '<div class="chat-message__page">' + detailHtml + '</div>' +
                '</div>' +
            '</div>'
        );
        thread.appendChild(pageRow);

        bindProductDetailAddButtons(pageRow);

        var descBody = pageRow.querySelector('.js-product-desc-body');
        var descToggle = pageRow.querySelector('.js-product-desc-toggle');
        if (descBody && descToggle) {
            var descExpanded = false;
            descToggle.addEventListener('click', function () {
                descExpanded = !descExpanded;
                if (descExpanded) {
                    descBody.style.maxHeight = 'none';
                    descBody.style.overflow = 'visible';
                    descToggle.textContent = getText('show_less');
                } else {
                    descBody.style.maxHeight = '16rem';
                    descBody.style.overflow = 'hidden';
                    descToggle.textContent = getText('read_more');
                }
            });
        }

        setTimeout(function () {
            scrollToLastUserRequest(userRow, 'smooth');
        }, 100);
    }

    function groupPacksByDosage(packs) {
        var groups = {};
        packs.forEach(function (pack) {
            var key = pack.dosage || '';
            if (!groups[key]) {
                groups[key] = [];
            }
            groups[key].push(pack);
        });
        return groups;
    }

    function buildPackTableHtml(packs, productName) {
        if (!packs.length) return '';

        var maxPillPrice = 0;
        packs.forEach(function (pack) {
            if (pack.quantity && pack.quantity > 0 && pack.price > 0) {
                var pp = pack.price / pack.quantity;
                if (pp > maxPillPrice) maxPillPrice = pp;
            }
        });

        var rowsHtml = '';
        packs.forEach(function (pack, idx) {
            var discountHtml = '';
            if (maxPillPrice > 0 && pack.quantity > 0 && pack.price > 0) {
                var oldPrice = maxPillPrice * pack.quantity;
                if (oldPrice > pack.price) {
                    var discount = Math.round((1 - pack.price / oldPrice) * 100);
                    discountHtml =
                        '<div class="product__discount"><s>' + formatPrice(oldPrice) + '</s> ' +
                        '<span>-' + discount + '%</span></div>';
                }
            }

            var deliveryHtml = pack.delivery
                ? '<div class="product__delivery">' + escapeHtml(pack.delivery) + '</div>'
                : '';

            var perPillHtml = '';
            if (pack.quantity && pack.quantity > 0 && pack.price > 0) {
                perPillHtml = formatPrice(pack.price / pack.quantity);
            }

            rowsHtml +=
                '<tr class="product">' +
                    '<td class="product__info-wrapper">' +
                        '<div class="product__info' + (discountHtml ? ' product__info--sale' : '') + '">' +
                            '<div class="product__quantity">' + escapeHtml(String(pack.quantity)) +
                                (pack.unit ? ' ' + escapeHtml(pack.unit) : '') +
                            '</div>' +
                            deliveryHtml +
                        '</div>' +
                    '</td>' +
                    '<td class="product__price-per-pill">' + perPillHtml + '</td>' +
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

        var dosageLabel = packs[0].dosage || '';
        var headerHtml = dosageLabel
            ? '<div class="panel__header"><h2 class="h2">' + escapeHtml(productName || '') + ' ' + escapeHtml(dosageLabel) + '</h2></div>'
            : '';

        return '' +
            '<div class="panel">' +
                headerHtml +
                '<table class="table product-table">' +
                    '<thead>' +
                        '<tr>' +
                            '<th>' + getText('package') + '</th>' +
                            '<th>' + getText('per_item') + '</th>' +
                            '<th>' + getText('price') + '</th>' +
                            '<th></th>' +
                        '</tr>' +
                    '</thead>' +
                    '<tbody>' + rowsHtml + '</tbody>' +
                '</table>' +
            '</div>';
    }

    function buildProductDetailHtml(product) {
        var packs = product.packs || [];
        var packsByDosage = groupPacksByDosage(packs);

        var dosageKeys = sortDosages(
            Object.keys(packsByDosage),
            'desc'
        );

        var packsHtml = '';
        dosageKeys.forEach(function (dosage) {
            packsHtml += buildPackTableHtml(packsByDosage[dosage], product.name || '');
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

        var variantsHtml = buildDosageVariantsHtml(packs);

        var descHTML = '';
        if (product.desc) {
            descHTML =
            '<div class="chat-row chat-row--agent">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<span>' + product.desc + '</span>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        var descriptionHtml = buildDosageVariantsHtml(packs);

        var fullDescHtml = '';
        if (product.full_desc) {
            fullDescHtml =
                '<div class="content js-product-desc">' +
                    '<div class="js-product-desc-body" style="max-height:16rem;overflow:hidden">' +
                        product.full_desc +
                    '</div>' +
                    '<button class="button button--secondary button--sm js-product-desc-toggle" type="button" style="margin-top:1rem">' +
                        getText('read_more') +
                    '</button>' +
                '</div>';
        }

        return '' +
            '<div class="product-card">' +
                imageHtml +
                '<div class="product-card__content">' +
                    '<div class="product-card__name h1">' + escapeHtml(product.name || '') + '</div>' +
                    variantsHtml +
                    descHTML +
                '</div>' +
            '</div>' +
            packsHtml +
            fullDescHtml;
    }

    function renderProductDetail(product) {
        if (!product) return;

        var body = getProductDrawerBody();

        var packs = product.packs || [];
        var packsByDosage = groupPacksByDosage(packs);

        var dosageKeys = sortDosages(
            Object.keys(packsByDosage),
            'desc'
        );

        var packsHtml = '';
        dosageKeys.forEach(function (dosage) {
            packsHtml += buildPackTableHtml(packsByDosage[dosage], product.name || '');
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

        var descriptionHtml = buildDosageVariantsHtml(packs);

        var contentHtml =
            '<div class="chat-message__page">' +
                '<div class="product-card">' +
                    imageHtml +
                    '<div class="product-card__content">' +
                        '<div class="product-card__name h1">' + escapeHtml(product.name || '') + '</div>' +
                        descriptionHtml +
                    '</div>' +
                '</div>' +
                packsHtml +
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
            scrollToLastUserRequest(null, 'smooth');
        }

        log('debug', 'renderProductDetail: ' + (product.name || ''));
    }

    function bindProductDetailAddButtons(root) {
        var buttons = $$('.product__button', root || document);
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var tableButtons = $$('.product-table .product__button', root);
                var allButtons = tableButtons.length > 0 ? tableButtons : [btn];
                var packUrl = btn.getAttribute('data-pack-url');
                addToCart(btn, allButtons, packUrl);
            });
        });
    }

    function addToCart(btn, allButtons, packUrl) {
        if (!packUrl) {
            log('error', 'addToCart: no URL');
            return;
        }

        var originalHtml = btn ? btn.innerHTML : null;
        if (btn) {
            btn.innerHTML = svgIcon('cart-white') + ' <span class="button__text">' + getText('adding') + '</span>';
        }
        allButtons.forEach(function (b) { b.disabled = true; });

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
            return window.CartAside.refresh();
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
            allButtons.forEach(function (b) { b.disabled = false; });
            if (btn) {
                btn.innerHTML = originalHtml;
            }
        });
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
                if (response.status >= 500) {
                    throw new Error('SERVER:' + response.status);
                }
                return response.json().then(function (errData) {
                    return { _httpError: true, message: errData.message, httpStatus: response.status };
                }).catch(function () {
                    return { _httpError: true, message: null, httpStatus: response.status };
                });
            }
            return response.json();
        })
        .then(function (data) {
            if (data._httpError) {
                stopPolling();
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(data.message || getText('error_unknown'));
                return;
            }

            if (!data.success) {
                stopPolling();
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(data.message || getText('error_unknown'));
                return;
            }

            pollNetworkErrors = 0;

            updateSkeletonStatus(data.status);

            if (data.status === 'done') {
                stopPolling();
                setState(State.DONE);
                if (data.currency && data.currency.prefix) {
                    currencyPrefix = data.currency.prefix;
                    currencyCoef = data.currency.coef || 1;
                }
                renderAgentAnswer(data.answer || '', data.products);

                if (isLeader && crossBus) {
                    crossBus.emit('chat:response', {
                        status: 'done',
                        query: currentQuery,
                        answer: data.answer || '',
                        products: data.products || [],
                        currency: data.currency || { prefix: currencyPrefix, code: 'usd', coef: currencyCoef },
                    });
                }
            } else if (data.status === 'error') {
                stopPolling();
                setState(State.ERROR);
                renderAgentError(data.message || getText('error_unknown'));

                if (isLeader && crossBus) {
                    crossBus.emit('chat:response', {
                        status: 'error',
                        query: currentQuery,
                        message: data.message || getText('error_unknown'),
                    });
                }
            }
        })
        .catch(function (err) {
            log('error', 'pollMessage: fetch error - ' + err.message);

            var isServerError = String(err.message).indexOf('SERVER:') === 0;
            if (isServerError) {
                stopPolling();
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(getText('error_server'));
                return;
            }

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

            if (typeof window.LegacyUI !== 'undefined' && window.LegacyUI.alert) {
                window.LegacyUI.alert('Could not start chat. Please refresh the page.');
            }
            return;
        }

        renderUserMessage(cleanText);
        currentQuery = cleanText;

        if (!isLeader && crossBus && leaderTabId) {
            renderAgentSkeleton();
            scrollToLastUserRequest(null, 'smooth');
            setState(State.POLLING);
            remoteSkeletonRow = activeSkeletonRow;
            crossBus.emit('chat:query', { message: cleanText, tabId: crossBus.getTabId() });
            return;
        }

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
            return response.json();
        })
        .then(function (data) {
            if (data.captcha_required) {
                setState(State.IDLE);
                showCaptchaModal(data.captcha_src, data.message, cleanText);
                return;
            }

            renderAgentSkeleton();
            scrollToLastUserRequest(null, 'smooth');

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
        status_queued: [
            'Your request is on the pharmacy counter',
            'Opening the digital medicine cabinet',
            'Getting the catalogue ready',
            'Your search is next in line',
            'Preparing the pharmacy shelves',
            'Warming up the product finder',
            'Getting the ingredient index ready',
            'Setting up a catalogue check',
            'The pharmacy assistant is getting ready',
            'One moment — arranging the search desk',
        ],
        status_processing: [
            'Scanning the pharmacy shelves',
            'Matching names and active ingredients',
            'Comparing dosages and pack sizes',
            'Checking available product options',
            'Reading labels across the catalogue',
            'Sorting the closest matches',
            'Checking prices and packages',
            'Narrowing down the medicine cabinet',
            'Double-checking the product cards',
            'Almost there — arranging the results',
        ],
        status_done: [
            'The pharmacy shelf is ready',
            'Your product shortlist is ready',
            'The catalogue check is complete',
            'Matching products are on the counter',
            'The search basket is ready',
            'The best catalogue matches are below',
            'Shelf check complete',
            'Your options are ready to review',
            'Products found and organised',
            'The digital medicine cabinet is open',
        ],
        status_error: [
            'The pharmacy shelf did not load',
            'The catalogue check hit a snag',
            'The product finder needs another try',
            'The digital medicine cabinet is temporarily closed',
            'The shelf scan could not finish',
            'The catalogue did not answer in time',
            'The product cards could not be prepared',
            'The search basket was not filled',
            'The pharmacy assistant lost the connection',
            'Please send the request once more',
        ],
        error_timeout: 'Request timed out. Please try again.',
        error_network: 'Network error. Please check your connection and try again.',
        error_server: 'Server error. Please try again later.',
        error_history: 'Could not load chat history. You can still start a new search.',
        error_unknown: 'Something went wrong. Please try again.',
        error_too_long: 'Message is too long. Please shorten it.',
        from: 'from',
        select: 'Select',
        add_to_cart: 'Add',
        adding: 'Adding...',
        added_to_cart: 'Item added to cart',
        cart_error: 'Failed to add to cart. Please try again.',
        close: 'Close',
        package: 'Package',
        per_item: 'Per item',
        price: 'Price',
        show_more: 'Show more',
        show_less: 'Collapse',
        new_messages: '↓ New messages',
        read_more: 'Read more',
        loading_chat: 'Loading chat...',
        select_product: 'Select',
        chat_waiting: 'Waiting for response...',
        chat_busy: 'Another request is being processed. Please wait.',
        chat_placeholder: 'Enter a drug name or active ingredient...',
        captcha_title: 'Verify you are human',
        captcha_placeholder: 'Enter code',
        captcha_submit: 'Continue',
    };

    var previousTextSelections = {};

    function pickRandomText(key, values) {
        var available = (values || []).filter(function (value) {
            return (
                typeof value === 'string' &&
                value.trim() !== '' &&
                value.indexOf('text.') !== 0
            );
        });

        if (!available.length) {
            return key;
        }

        if (available.length > 1 && previousTextSelections[key]) {
            var withoutPrevious = available.filter(function (value) {
                return value !== previousTextSelections[key];
            });

            if (withoutPrevious.length) {
                available = withoutPrevious;
            }
        }

        var selected = available[
            Math.floor(Math.random() * available.length)
        ];

        previousTextSelections[key] = selected;
        return selected;
    }

    function getText(key) {
        var value = null;

        if (window.design17ChatTexts) {
            value = window.design17ChatTexts[key];
        }

        if (
            typeof value === 'string' &&
            value.indexOf('text.') === 0
        ) {
            value = null;
        }

        if (value == null) {
            value = texts[key];
        }

        if (Array.isArray(value)) {
            return pickRandomText(key, value);
        }

        if (typeof value === 'string' && value.trim() !== '') {
            return value;
        }

        return key;
    }

    // ── History ──

    function showChatLoader() {
        var container = getChatContainer();
        if (!container) return;

        var existing = container.querySelector('.dc17-chat-loader');
        if (existing) return;

        var heading = container.querySelector('.js-chat-start-heading');
        if (heading && heading.parentNode) {
            heading.parentNode.removeChild(heading);
        }

        var loader = createElement(
            '<div class="dc17-chat-loader">' +
                '<div class="dc17-chat-loader__spinner"></div>' +
                '<div class="dc17-chat-loader__text">' + getText('loading_chat') + '</div>' +
            '</div>'
        );

        var form = getChatForm();
        if (form && form.parentNode === container) {
            container.insertBefore(loader, form);
        } else {
            container.appendChild(loader);
        }
    }

    function hideChatLoader() {
        var loader = document.querySelector('.dc17-chat-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(function () {
                if (loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                }
            }, 300);
        }
    }

    function loadHistory() {
        var container = getChatContainer();
        if (!container) return;

        var existingMessages = container.querySelector('.thread-chat__messages');
        if (existingMessages && existingMessages.children.length > 0) {
            return;
        }

        var heading = container.querySelector('.js-chat-start-heading');

        if (!heading && !existingMessages) {
            restoreHeading();
            return;
        }

        if (heading) {
            showChatLoader();
        }

        var historyRoute = window.routeChatHistory || '/chat/history';
        log('debug', 'loadHistory');

        fetch(historyRoute, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            hideChatLoader();

            if (!data.success || !data.messages || !data.messages.length) {
                restoreHeading();
                return;
            }

            if (data.currency && data.currency.prefix) {
                currencyPrefix = data.currency.prefix;
                currencyCoef = data.currency.coef || 1;
            }

            var headingEl = container.querySelector('.js-chat-start-heading');
            if (headingEl && headingEl.parentNode) {
                headingEl.parentNode.removeChild(headingEl);
            }

            activateChat();

            var thread = getChatThread();
            if (!thread) {
                log('warn', 'loadHistory: thread not found after activation');
                return;
            }

            data.messages.forEach(function (msg) {
                if (msg.role === 'user') {
                    renderHistoryUserMessage(msg.content);
                } else if (msg.role === 'assistant') {
                    renderHistoryAssistantMessage(msg.content, msg.products);
                }
            });

            scrollToLastUserRequest(null, 'smooth');
            log('debug', 'loadHistory: ' + data.messages.length + ' messages restored');
        })
        .catch(function (err) {
            hideChatLoader();
            log('warn', 'loadHistory error: ' + err.message);
            restoreHeading();
        });
    }

    function restoreHeading() {
        var container = getChatContainer();
        if (!container) return;

        var existingHeading = container.querySelector('.js-chat-start-heading');
        if (existingHeading) return;

        var thread = container.querySelector('.thread-chat__messages');
        if (thread && thread.children.length > 0) return;

        var chatWrap = container.querySelector('.thread-chat');
        if (chatWrap && chatWrap.parentNode) {
            chatWrap.parentNode.removeChild(chatWrap);
        }

        var heading = createElement(
            '<h1 class="main-heading js-chat-start-heading">' +
                '<span class="main-heading__title">Its\' True Meds Bot for buying Drugs</span>' +
                '<span class="main-heading__caption">Easier, Safer, Faster</span>' +
            '</h1>'
        );

        var form = getChatForm();
        if (form && form.parentNode === container) {
            container.insertBefore(heading, form);
        } else {
            container.appendChild(heading);
        }
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

        initCrossTabSync();
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

    function showCaptchaModal(src, errorMsg, savedText) {
        var existing = document.querySelector('.dc17-captcha-overlay');
        if (existing) existing.parentNode.removeChild(existing);

        var overlay = createElement(
            '<div class="dc17-captcha-overlay">' +
                '<div class="dc17-captcha-modal">' +
                    '<div class="dc17-captcha-title">' + getText('captcha_title') + '</div>' +
                    (errorMsg ? '<div class="dc17-captcha-error">' + escapeHtml(errorMsg) + '</div>' : '') +
                    '<div class="dc17-captcha-img-wrap">' +
                        '<div class="dc17-captcha-spinner"></div>' +
                        '<img class="dc17-captcha-img" src="' + src + '" alt="Captcha">' +
                    '</div>' +
                    '<input class="dc17-captcha-input" type="text" placeholder="' + getText('captcha_placeholder') + '" maxlength="8">' +
                    '<button class="button dc17-captcha-submit" type="button">' + getText('captcha_submit') + '</button>' +
                '</div>' +
            '</div>'
        );
        document.body.appendChild(overlay);

        var img = overlay.querySelector('.dc17-captcha-img');
        var spinner = overlay.querySelector('.dc17-captcha-spinner');
        if (img && spinner) {
            img.addEventListener('load', function () { spinner.style.display = 'none'; });
            img.addEventListener('error', function () { spinner.style.display = 'none'; });
        }

        var input = overlay.querySelector('.dc17-captcha-input');
        var submit = overlay.querySelector('.dc17-captcha-submit');

        function doSend() {
            var code = input.value.trim();
            if (!code) return;
            overlay.parentNode.removeChild(overlay);
            sendMessageWithCaptcha(savedText, code);
        }

        submit.addEventListener('click', doSend);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') doSend();
        });
        input.focus();
    }

    function sendMessageWithCaptcha(text, captchaCode) {
        setState(State.SENDING);
        activateChat();
        renderAgentSkeleton();
        scrollToLastUserRequest(null, 'smooth');
        setState(State.POLLING);
        currentQuery = text;

        if (!isLeader && crossBus && leaderTabId) {
            remoteSkeletonRow = activeSkeletonRow;
            crossBus.emit('chat:query', { message: text, tabId: crossBus.getTabId() });
            return;
        }

        var sendRoute = window.routeChatSend || '/chat/send';
        fetch(sendRoute, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text, captcha_code: captchaCode }),
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (data.captcha_required) {
                setState(State.IDLE);
                if (activeSkeletonRow) {
                    stopStatusTextRotation();
                    activeSkeletonRow.parentNode.removeChild(activeSkeletonRow);
                    activeSkeletonRow = null;
                    activeMessageId = null;
                }
                showCaptchaModal(data.captcha_src, data.message, text);
                return;
            }
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

    function renderRemoteMessage(payload) {
        var thread = getChatThread();
        if (!thread) return;

        if (payload.currency) {
            currencyPrefix = payload.currency.prefix;
            currencyCoef = payload.currency.coef || 1;
        }

        var query = payload.query || '';
        var answer = payload.answer || '';
        var products = payload.products || [];

        if (query) {
            thread.appendChild(createElement(
                '<div class="chat-row chat-row--user">' +
                    '<div class="chat-message">' +
                        '<div class="chat-message__content content">' +
                            '<div class="chat-message__bubble">' + escapeHtml(query) + '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            ));
        }

        if (answer) {
            thread.appendChild(createElement(
                '<div class="chat-row chat-row--agent">' +
                    '<div class="chat-message">' +
                        '<div class="chat-message__content content">' +
                            '<div class="chat-message__bubble chat-message__bubble--agent">' +
                                '<div class="dc17-chat-answer-text">' + formatAnswerText(answer) + '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            ));
        }

        if (products && products.length > 0) {
            var productCardsHtml = renderProductCards(products);
            thread.appendChild(createElement(
                '<div class="chat-row chat-row--page">' +
                    '<div class="chat-message">' +
                        '<div class="chat-message__content content"></div>' +
                        '<div class="chat-message__page">' + productCardsHtml + '</div>' +
                    '</div>' +
                '</div>'
            ));
        }

        scrollToLastUserRequest(null, 'smooth');
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