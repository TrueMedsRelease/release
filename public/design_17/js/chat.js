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

    var LOG_LEVELS = { error: 0, warn: 1, info: 2, debug: 3 };
    var currentLogLevel = (function () {
        var v = String(window.DESIGN17_CHAT_LOG_LEVEL || 'error').toLowerCase();
        return LOG_LEVELS[v] != null ? LOG_LEVELS[v] : LOG_LEVELS.error;
    })();

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

    function isChatDisabledPage() {
        return window.design17ChatDisabled === true;
    }


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
                    setState(State.DONE);
                    renderAgentAnswer(payload.answer || '', payload.products, payload);
                } else if (payload.status === 'error') {
                    setState(State.ERROR);
                    renderAgentError(payload.message || getText('error_unknown'));
                }
                pollInFlight = false;
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
        if ((LOG_LEVELS[level] != null ? LOG_LEVELS[level] : 0) > currentLogLevel) return;
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

        if (thread.scrollHeight > thread.clientHeight + 10) {
            return thread.scrollHeight - thread.scrollTop - thread.clientHeight < 60;
        }

        var rect = thread.getBoundingClientRect();
        return rect.bottom - window.innerHeight < 60;
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
            if (thread && thread.lastElementChild) {
                // работает и для внутреннего скролла, и для оконного
                thread.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' });
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

        var onScroll = function () {
            if (isAtBottom()) {
                hideNewMessagesIndicator();
            } else {
                showNewMessagesIndicator();   // ← пользователь уехал вверх
            }
        };

        thread.addEventListener('scroll', onScroll);
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll);
        onScroll();
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

    function escapeAttr(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function routeBrowse(type, slug) {
        var tpl = window.routeChatBrowse || '/chat/browse/__TYPE__/__SLUG__';

        return tpl
            .replace('__TYPE__', encodeURIComponent(type))
            .replace('__SLUG__', encodeURIComponent(slug));
    }

    function slugFromBrowseUrl(url) {
        if (!url) {
            return '';
        }

        var clean = String(url).split('?')[0];
        var parts = clean.split('/');

        return decodeURIComponent(parts[parts.length - 1] || '');
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

    function buildCatalogLinkHtml(meta) {
        if (!meta || !meta.show_catalog_link || !meta.catalog_url) {
            return '';
        }

        return (
            '<div class="dc17-chat-fallback-catalog">' +
                '<a href="' + escapeAttr(meta.catalog_url) + '" target="_blank" rel="noopener noreferrer">' +
                    escapeHtml(meta.catalog_label || getText('catalog_link')) +
                '</a>' +
            '</div>'
        );
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
        scrollThreadToBottom();
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

        scrollThreadToBottom();

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

    function renderAgentAnswer(answer, products, meta) {
        meta = meta || {};
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
        var catalogHtml = buildCatalogLinkHtml(meta);

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
                            catalogHtml +
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

            bindBrowseChips(row);
        }

        // Не скроллим при получении результата (на мобильной версии скролл уже сделан при отправке запроса)
        // scrollThreadToBottom();
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
        scrollThreadToBottom();
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
            var browseHtml = buildBrowseChipsHtml(product);

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
                            browseHtml +
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
        bindBrowseChips(body);
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

    function normalizeAutoProduct(product) {
        var p = Object.assign({}, product);
        var flat = [];

        function pushRow(row, dosageFallback) {
            flat.push({
                id: row.id,
                quantity: row.num != null ? row.num : row.quantity,
                dosage: row.dosage || dosageFallback || '',
                price: parseFloat(row.price) || 0,
                old_price: parseFloat(row.old_price) || 0,
                delivery: row.delivery_info || row.delivery || '',
                add_url: row.add_url || (row.id != null ? '/cart/add_pack/' + row.id : ''),
                unit: row.unit || '',
                type_id: row.type_id,
            });
        }

        function walk(node, dosageFallback) {
            if (Array.isArray(node)) {
                node.forEach(function (child) { walk(child, dosageFallback); });
                return;
            }
            if (!node || typeof node !== 'object') return; // числа/строки (max_pill_price) — мимо

            // это строка пака
            if (node.id != null || node.num != null || node.quantity != null) {
                pushRow(node, dosageFallback);
                return;
            }
            // это группа: recurse; нечисловой ключ считаем дозировкой
            Object.keys(node).forEach(function (k) {
                walk(node[k], isNaN(Number(k)) ? k : dosageFallback);
            });
        }

        walk(p.packs || [], '');
        p.packs = flat;

        if (!p.active && p.aktiv) p.active = p.aktiv;
        return p;
    }

    function renderProductExchange(product) {
        var thread = getChatThread();
        if (!thread) return;

        selectedProductIds[product.id] = true;

        var userName = product.name || '';
        var userRow = createElement(
            '<div class="chat-row chat-row--user chat-message--appear">' +
                '<div class="chat-message"><div class="chat-message__content content">' +
                    '<div class="chat-message__bubble">' + escapeHtml(userName) + '</div>' +
                '</div></div>' +
            '</div>'
        );
        thread.appendChild(userRow);

        var packs = product.packs || [];
        var variantsHtml = getUniqueDosages(packs, 'asc')
            .map(function (d) { return escapeHtml(d); })
            .join(' | ');
        var agentText = variantsHtml ? escapeHtml(userName) + ' - ' + variantsHtml : escapeHtml(userName);
        thread.appendChild(createElement(
            '<div class="chat-row chat-row--agent chat-message--appear">' +
                '<div class="chat-message"><div class="chat-message__content content">' +
                    '<div class="chat-message__bubble chat-message__bubble--agent">' + agentText + '</div>' +
                '</div></div>' +
            '</div>'
        ));

        var pageRow = createElement(
            '<div class="chat-row chat-row--page chat-message--appear">' +
                '<div class="chat-message"><div class="chat-message__content content"></div>' +
                    '<div class="chat-message__page">' + buildProductDetailHtml(product) + '</div>' +
                '</div>' +
            '</div>'
        );
        thread.appendChild(pageRow);

        bindProductDetailAddButtons(pageRow);
        bindBrowseChips(pageRow);

        var descBody = pageRow.querySelector('.js-product-desc-body');
        var descToggle = pageRow.querySelector('.js-product-desc-toggle');
        if (descBody && descToggle) {
            var descExpanded = false;
            descToggle.addEventListener('click', function () {
                descExpanded = !descExpanded;
                descBody.style.maxHeight = descExpanded ? 'none' : '16rem';
                descBody.style.overflow = descExpanded ? 'visible' : 'hidden';
                descToggle.textContent = descExpanded ? getText('show_less') : getText('read_more');
            });
        }

        scrollThreadToBottom();
        setTimeout(function () { scrollToLastUserRequest(userRow, 'smooth'); }, 100);
    }

    var selectedProductIds = {};

    function showProductInChat(product) {
        if (!product) return;
        if (selectedProductIds[product.id]) return;
        renderProductExchange(product);
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
            var isFirstRow = (idx === 0);
            var isLastRow = (idx === packs.length - 1);
            var discountHtml = '';
            if (!isLastRow && maxPillPrice > 0 && pack.quantity > 0 && pack.price > 0) {
                var oldPrice = maxPillPrice * pack.quantity;
                var discount = Math.round((1 - pack.price / oldPrice) * 100);
                if (discount > 0) {
                    discountHtml =
                        '<div class="product__discount"><s>' + formatPrice(oldPrice) + '</s> ' +
                        '<span>-' + discount + '%</span></div>';
                }
            }

            var discountHtmlFirstRow = isFirstRow && discountHtml !== '';

            var deliveryText = pack.delivery || '';

            if (!deliveryText) {
                if (pack.price >= 300) {
                    deliveryText = getText('delivery_express');
                } else if (pack.price >= 200) {
                    deliveryText = getText('delivery_regular');
                }
            }

            var deliveryHtml = deliveryText
                ? '<div class="product__delivery">' + escapeHtml(deliveryText) + '</div>'
                : '';

            var perPillHtml = '';
            if (pack.quantity && pack.quantity > 0 && pack.price > 0) {
                perPillHtml = formatPrice(pack.price / pack.quantity);
            }

            rowsHtml +=
                '<tr class="product">' +
                    '<td class="product__info-wrapper">' +
                        '<div class="product__info' + (discountHtmlFirstRow ? ' product__info--sale' : '') + '"' +
                            (isFirstRow ? ' style="height: auto;"' : '') + '>' +
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
        var browseHtml = buildBrowseChipsHtml(product);

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
                    browseHtml +
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
            bindBrowseChips(row);
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

    function getPackIdFromAddUrl(url) {
        var m = String(url || '').match(/\/cart\/add(?:_pack)?\/([^/?#]+)/);
        return m ? m[1] : '';
    }

    function highlightAddedCartItem(packId) {
        if (!packId) return;

        var drawer = document.querySelector('[data-drawer="cart"]');
        if (!drawer) return;

        var btn = drawer.querySelector('[data-cart-remove-pack="' + packId + '"]');
        var item = btn ? btn.closest('.cart-item') : null;
        if (!item) return;

        item.classList.remove('is-added-highlight');
        void item.offsetWidth;
        item.classList.add('is-added-highlight');

        setTimeout(function () {
            item.classList.remove('is-added-highlight');
        }, 3400);
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
            showAddToast(getText('added_to_cart'));
            highlightAddedCartItem(getPackIdFromAddUrl(packUrl));
        })
        .catch(function (err) {
            log('error', 'addToCart error: ' + err.message);
            showAddToast(getText('cart_error'), true);
        })
        .then(function () {
            allButtons.forEach(function (b) { b.disabled = false; });
            if (btn) {
                btn.innerHTML = originalHtml;
            }
        });
    }

    // ── Polling ──

    // ── Polling (строго 1 запрос одновременно) ──
    var POLL_REQUEST_TIMEOUT = 60000; // максимум ждём ответ одного poll
    var pollTimer = null;       // id setTimeout для СЛЕДУЮЩЕГО poll
    var pollActive = false;     // идёт ли поллинг вообще
    var pollInFlight = false;   // летит ли сейчас запрос (защита от параллельных)

    function startPolling(messageId) {
        stopPolling();
        pollActive = true;
        activeMessageId = messageId;
        pollRetries = 0;
        pollNetworkErrors = 0;
        pollMessage(messageId); // первый запрос уходит сразу
    }

    function stopPolling() {
        pollActive = false;
        pollInFlight = false;
        if (pollTimer) {
            clearTimeout(pollTimer);
            pollTimer = null;
        }
    }

    function scheduleNextPoll(messageId) {
        if (!pollActive || pollTimer) return;
        pollTimer = setTimeout(function () {
            pollTimer = null;
            pollMessage(messageId);
        }, POLL_INTERVAL);
    }

    function fetchWithTimeout(url, options, timeoutMs) {
        var opts = options || {};
        if (typeof AbortController !== 'undefined') {
            var controller = new AbortController();
            opts.signal = controller.signal;
            setTimeout(function () { controller.abort(); }, timeoutMs);
        }
        return fetch(url, opts);
    }

    // Ошибка → останавливаем поллинг и уводим пользователя в обычный поиск
    function fallbackToRegularSearch(message) {
        stopPolling();
        pollInFlight = false;

        if (isLeader && crossBus) {
            crossBus.emit('chat:response', { status: 'error', message: message || '' });
        }

        var query = (currentQuery || '').trim();
        if (!query) {
            setState(State.ERROR);
            updateSkeletonStatus('error');
            renderAgentError(message || getText('error_unknown'));
            return;
        }

        window.location.href = '/search/' + encodeURIComponent(query);
    }

    function pollMessage(messageId) {
        if (!pollActive || pollInFlight) return; // ← никогда не летит 2 запроса сразу
        if (messageId !== activeMessageId) return; // ← устаревший poll от предыдущего сообщения
        pollInFlight = true;

        pollRetries++;
        if (pollRetries > POLL_MAX_RETRIES) {
            fallbackToRegularSearch(getText('error_timeout'));
            return;
        }

        var pollRoute = window.routeChatPoll || '/chat/poll/__MSGID__';
        var url = route(pollRoute, messageId);

        fetchWithTimeout(url, {
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json' },
        }, POLL_REQUEST_TIMEOUT)
        .then(function (response) {
            if (!response.ok) {
                if (response.status >= 500) {
                    throw new Error('SERVER:' + response.status);
                }
                return response.json().then(function (errData) {
                    return { _httpError: true, message: errData.message };
                }).catch(function () {
                    return { _httpError: true, message: null };
                });
            }
            return response.json();
        })
        .then(function (data) {
            pollInFlight = false;

            // Любая ошибка от сервера → обычный поиск
            if (data._httpError || !data.success) {
                fallbackToRegularSearch(data.message || getText('error_unknown'));
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
                renderAgentAnswer(data.answer || '', data.products, data);
                if (isLeader && crossBus) {
                    crossBus.emit('chat:response', {
                        status: 'done',
                        query: currentQuery,
                        answer: data.answer || '',
                        products: data.products || [],
                        currency: data.currency || { prefix: currencyPrefix, code: 'usd', coef: currencyCoef },
                        fallback: data.fallback,
                        fallback_reason: data.fallback_reason,
                        show_catalog_link: data.show_catalog_link,
                        catalog_url: data.catalog_url,
                        catalog_label: data.catalog_label,
                    });
                }
            } else if (data.status === 'error') {
                fallbackToRegularSearch(data.message || getText('error_unknown'));
            } else {
                // queued/processing: СЛЕДУЮЩИЙ запрос уйдёт только после завершения этого
                scheduleNextPoll(messageId);
            }
        })
        .catch(function (err) {
            pollInFlight = false;
            log('error', 'pollMessage: fetch error - ' + err.message);

            // 500 с сервера или таймаут запроса → сразу в обычный поиск
            if (String(err.message).indexOf('SERVER:') === 0 || err.name === 'AbortError') {
                fallbackToRegularSearch(getText('error_server'));
                return;
            }

            pollNetworkErrors++;
            if (pollNetworkErrors >= POLL_NETWORK_MAX_RETRIES) {
                fallbackToRegularSearch(getText('error_network'));
            } else {
                scheduleNextPoll(messageId);
            }
        });
    }

    // ── Main flow ──

    var sendInFlight = false;

    function sendMessage(text) {
        if (sendInFlight || state === State.SENDING || state === State.POLLING) {
            log('warn', 'sendMessage: blocked, current state=' + state + ', sendInFlight=' + sendInFlight);
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

        sendInFlight = true;
        setState(State.SENDING);

        var activated = activateChat();
        if (!activated) {
            sendInFlight = false;
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
            sendInFlight = false;
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
            sendInFlight = false;

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
            sendInFlight = false;
            setState(State.ERROR);
            updateSkeletonStatus('error');
            renderAgentError(err.message || getText('error_network'));
        });
    }

    function sendBrowseQuery(type, slug, label) {
        if (state === State.SENDING || state === State.POLLING) {
            log('warn', 'sendBrowseQuery: blocked, current state=' + state);
            return;
        }

        slug = String(slug || '').trim();
        label = String(label || slug || '').trim();

        if (!slug) {
            return;
        }

        setState(State.SENDING);

        var activated = activateChat();

        if (!activated) {
            setState(State.IDLE);
            log('warn', 'sendBrowseQuery: could not activate chat thread');
            return;
        }

        renderUserMessage(label);
        currentQuery = label;

        renderAgentSkeleton();
        scrollToLastUserRequest(null, 'smooth');

        setState(State.POLLING);

        fetch(routeBrowse(type, slug), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCSRFToken(),
            },
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (!data.success) {
                setState(State.ERROR);
                updateSkeletonStatus('error');
                renderAgentError(data.message || getText('error_unknown'));
                return;
            }

            if (data.currency && data.currency.prefix) {
                currencyPrefix = data.currency.prefix;
                currencyCoef = data.currency.coef || 1;
            }

            updateSkeletonStatus('done');
            setState(State.DONE);

            renderAgentAnswer(data.answer || '', data.products || [], data);
        })
        .catch(function (err) {
            log('error', 'sendBrowseQuery error: ' + err.message);

            setState(State.ERROR);
            updateSkeletonStatus('error');
            renderAgentError(err.message || getText('error_network'));
        });
    }

    function buildBrowseChipsHtml(product) {
        if (!product) {
            return '';
        }

        var links = [];
        var seen = {};

        function addLink(type, name, slug) {
            type = String(type || '').trim();
            name = String(name || '').trim();
            slug = String(slug || '').trim();

            if (!type || !name || !slug) {
                return;
            }

            var key = type + ':' + slug.toLowerCase();

            if (seen[key]) {
                return;
            }

            seen[key] = true;

            links.push({
                type: type,
                name: name,
                slug: slug,
            });
        }

        if (product.active && product.active.length) {
            product.active.forEach(function (item) {
                addLink(
                    item.type || 'active',
                    item.name,
                    item.slug || slugFromBrowseUrl(item.url)
                );
            });
        }

        if (product.browse_links && product.browse_links.length) {
            product.browse_links.forEach(function (item) {
                addLink(
                    item.type || 'active',
                    item.name,
                    item.slug || slugFromBrowseUrl(item.url)
                );
            });
        }

        if (!links.length) {
            return '';
        }

        var html = '<div class="chat-product__browse-links">';

        links.forEach(function (link) {
            html +=
                '<button ' +
                    'type="button" ' +
                    'class="chat-chip js-chat-browse" ' +
                    'data-browse-type="' + escapeAttr(link.type) + '" ' +
                    'data-browse-slug="' + escapeAttr(link.slug) + '" ' +
                    'data-browse-label="' + escapeAttr(link.name) + '">' +
                    escapeHtml(link.name) +
                '</button>';
        });

        html += '</div>';

        return html;
    }

    function bindBrowseChips(root) {
        var chips = $$('.js-chat-browse', root || document);

        chips.forEach(function (chip) {
            if (chip.dataset.chatBound) {
                return;
            }

            chip.dataset.chatBound = '1';

            chip.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var type = chip.dataset.browseType || 'active';
                var slug = chip.dataset.browseSlug || '';
                var label = chip.dataset.browseLabel || chip.textContent || '';

                sendBrowseQuery(type, slug, label);
            });
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
        new_messages: '↓',
        read_more: 'Read more',
        loading_chat: 'Loading chat...',
        select_product: 'Select',
        chat_waiting: 'Waiting for response...',
        chat_busy: 'Another request is being processed. Please wait.',
        chat_placeholder: 'Enter a drug name or active ingredient...',
        captcha_title: 'Verify you are human',
        captcha_placeholder: 'Enter code',
        captcha_submit: 'Continue',
        catalog_link: 'View our catalog',
        delivery_express: 'Free Express Delivery',
        delivery_regular: 'Free Regular Delivery',
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
        var serverChat = container.querySelector('.thread-chat');

        // Страницы без чат-контента (product, about и т.д.) — старое поведение
        if (!heading && !serverChat && !window.design17AutoBrowse && !window.design17AutoProduct) {
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
                runAutoBrowse([]);
                runAutoProduct([]);
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

            // ВАЖНО: убираем серверный .thread-chat ДО activateChat(),
            // иначе activateChat() увидит его и не создаст .js-chat-thread
            var serverChatEl = container.querySelector('.thread-chat');
            if (serverChatEl && serverChatEl.parentNode) {
                serverChatEl.parentNode.removeChild(serverChatEl);
            }

            activateChat();

            var thread = getChatThread();
            if (!thread) {
                log('warn', 'loadHistory: thread not found after activation');
                runAutoBrowse(data.messages);
                runAutoProduct(data.messages);
                return;
            }

            data.messages.forEach(function (msg) {
                if (msg.role === 'user') {
                    renderHistoryUserMessage(msg.content);
                } else if (msg.role === 'assistant') {
                    renderHistoryAssistantMessage(msg.content, msg.products, msg);
                }
            });

            scrollToLastUserRequest(null, 'smooth');
            scrollThreadToBottom();
            log('debug', 'loadHistory: ' + data.messages.length + ' messages restored');

            runAutoBrowse(data.messages);
            runAutoProduct(data.messages);
        })
        .catch(function (err) {
            hideChatLoader();
            log('warn', 'loadHistory error: ' + err.message);
            restoreHeading();
            runAutoBrowse([]);
            runAutoProduct([]);
        });
    }

    function restoreHeading() {
        if (isChatDisabledPage()) {
            return;
        }

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

    function renderHistoryAssistantMessage(text, products, meta) {
        meta = meta || {};
        var thread = getChatThread();
        if (!thread) return;
        var hasProducts = products && products.length;
        var totalProducts = hasProducts ? products.length : 0;
        var needsPagination = totalProducts > 6;
        var productCardsHtml = hasProducts ? renderProductCards(products) : '';
        var catalogHtml = buildCatalogLinkHtml(meta);

        var paginationHtml = '';
        if (needsPagination) {
            paginationHtml =
                '<div class="dc17-chat-products__pagination">' +
                    '<button class="button button--secondary js-chat-show-more" type="button">' +
                        '<span class="js-chat-pagination-label">' + getText('show_more') + ' (' + (totalProducts - 6) + ')</span>' +
                    '</button>' +
                '</div>';
        }

        var textHtml = '<div class="dc17-chat-answer-text">' + formatAnswerText(text) + '</div>';
        var pageContent = hasProducts ? '<div class="chat-message__page">' + productCardsHtml + paginationHtml + '</div>' : '';
        var bubbleClass = hasProducts ? 'chat-message__bubble chat-message__bubble--agent' : 'chat-message__bubble';

        var row = createElement(
            '<div class="chat-row chat-row--agent">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="' + bubbleClass + '">' + textHtml + catalogHtml + '</div>' +
                    '</div>' +
                    pageContent +
                '</div>' +
            '</div>'
        );
        thread.appendChild(row);
        if (hasProducts) {
            bindProductCardClicks(row, products);
            if (needsPagination) initPagination(row, products);   // ← добавлено
            bindBrowseChips(row);
        }
    }

    // ── Init ──

    function init() {
        if (isChatDisabledPage()) {
            log('debug', 'init: chat disabled on this page');
            return;
        }

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
            // sendMessageWithCaptcha(savedText, code);
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
            var remoteCatalogHtml = buildCatalogLinkHtml(payload);

            thread.appendChild(createElement(
                '<div class="chat-row chat-row--agent">' +
                    '<div class="chat-message">' +
                        '<div class="chat-message__content content">' +
                            '<div class="chat-message__bubble chat-message__bubble--agent">' +
                                '<div class="dc17-chat-answer-text">' + formatAnswerText(answer) + '</div>' +
                                remoteCatalogHtml +
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

    function runAutoBrowse(historyMessages) {
        var cfg = window.design17AutoBrowse;
        if (!cfg || !cfg.type || !cfg.slug) return;

        window.design17AutoBrowse = null;

        var label = String(cfg.label || cfg.slug).trim();
        var messages = historyMessages || [];

        for (var i = messages.length - 1; i >= 0; i--) {
            if (messages[i].role === 'user') {
                if ((messages[i].content || '').trim().toLowerCase() === label.toLowerCase()) {
                    log('debug', 'runAutoBrowse: already in history, skip');
                    return;
                }
                break;
            }
        }

        log('debug', 'runAutoBrowse: ' + cfg.type + ' / ' + cfg.slug);
        setTimeout(function () {
            sendBrowseQuery(cfg.type, cfg.slug, label);
        }, 200);
    }

    function scrollThreadToBottom(behavior) {
        var thread = getChatThread();
        if (!thread) return;
        if (thread.scrollHeight > thread.clientHeight + 10) {
            try {
                thread.scrollTo({ top: thread.scrollHeight, behavior: behavior || 'smooth' });
            } catch (e) {
                thread.scrollTop = thread.scrollHeight;
            }
        } else if (thread.lastElementChild) {
            thread.lastElementChild.scrollIntoView({ behavior: behavior || 'smooth', block: 'end' });
        }
    }

    function runAutoProduct(historyMessages) {
        var raw = window.design17AutoProduct;
        if (!raw || !raw.id) return;
        window.design17AutoProduct = null;

        if (raw.currency_prefix) currencyPrefix = raw.currency_prefix;
        if (raw.currency_coef) currencyCoef = parseFloat(raw.currency_coef) || 1;

        var product = normalizeAutoProduct(raw);

        // не дублируем, если в истории уже есть такое user-сообщение
        var messages = historyMessages || [];
        var name = String(product.name || '').trim().toLowerCase();
        for (var i = messages.length - 1; i >= 0; i--) {
            if (messages[i].role === 'user') {
                if ((messages[i].content || '').trim().toLowerCase() === name) {
                    log('debug', 'runAutoProduct: already in history, skip');
                    return;
                }
                break;
            }
        }

        var serverContent = document.querySelector('.js-product-server-content');
        if (serverContent) serverContent.style.display = 'none';

        setTimeout(function () {
            activateChat();
            renderProductExchange(product);
        }, 200);
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
        sendBrowseQuery: sendBrowseQuery,
    };

    // ── Auto-init ──

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            if (!isChatDisabledPage() && getChatContainer()) {
                init();
            }
        });
    } else {
        if (!isChatDisabledPage() && getChatContainer()) {
            init();
        }
    }

})();