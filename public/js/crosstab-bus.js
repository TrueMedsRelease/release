(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.CrossTabBus = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    var VERSION = '1.1.0';
    var CHANNEL_NAME = '__crosstab_bus_v1__';
    var TAB_ID = (Date.now().toString(36) + Math.random().toString(36).slice(2, 9));

    var LOG_LEVELS = { none: 0, error: 1, warn: 2, info: 3, debug: 4 };
    var currentLogLevel = LOG_LEVELS.none;
    var externalLogger = null;
    var PREFIX = '[CrossTab:' + TAB_ID.slice(-4) + ']';

    function getConsole() {
        if (typeof console !== 'undefined') return console;
        return null;
    }

    function formatMsg(level, msg, data) {
        var entry = { level: level, msg: msg, tabId: TAB_ID, ts: Date.now() };
        if (data !== undefined) entry.data = data;
        return entry;
    }

    function log(level, msg, data) {
        if (LOG_LEVELS[level] > currentLogLevel) return;

        var entry = formatMsg(level, msg, data);

        if (externalLogger && typeof externalLogger[level] === 'function') {
            try { externalLogger[level](PREFIX + ' ' + msg, data); } catch(e){}
            return;
        }

        var c = getConsole();
        if (!c) return;

        var method = level === 'debug' && !c.debug ? 'log' : level;
        if (typeof c[method] === 'function') {
            try {
                if (data !== undefined) c[method](PREFIX, msg, data);
                else c[method](PREFIX, msg);
            } catch(e){}
        }
    }

    var logger = {
        setLevel: function(level) {
            if (typeof level === 'string' && LOG_LEVELS[level] !== undefined) {
                currentLogLevel = LOG_LEVELS[level];
            } else if (typeof level === 'number') {
                currentLogLevel = level;
            }
            log('info', 'Log level changed', { level: level });
        },
        setExternalLogger: function(ext) {
            externalLogger = ext;
            log('info', 'External logger attached');
        },
        error: function(msg, data) { log('error', msg, data); },
        warn:  function(msg, data) { log('warn', msg, data); },
        info:  function(msg, data) { log('info', msg, data); },
        debug: function(msg, data) { log('debug', msg, data); },
        getLevel: function() {
            for (var k in LOG_LEVELS) {
                if (LOG_LEVELS[k] === currentLogLevel) return k;
            }
            return 'none';
        }
    };

    var Transport = null;
    var transportType = 'none';

    if (typeof BroadcastChannel !== 'undefined') {
        try {
            var bc = new BroadcastChannel(CHANNEL_NAME);
            Transport = {
                post: function(msg) { bc.postMessage(msg); },
                onMessage: function(cb) { bc.onmessage = function(e) { cb(e.data); }; },
                close: function() { bc.close(); }
            };
            transportType = 'broadcast';
        } catch (e) {
            logger.warn('BroadcastChannel init failed, falling back', { error: e.message });
        }
    }

    if (!Transport && typeof window !== 'undefined' && window.localStorage) {
        var STORAGE_KEY = CHANNEL_NAME + '_msg';
        Transport = {
            post: function(msg) {
                try {
                    var key = STORAGE_KEY + '_' + Date.now() + '_' + Math.random();
                    window.localStorage.setItem(key, JSON.stringify(msg));
                    setTimeout(function() {
                        try { window.localStorage.removeItem(key); } catch(e){}
                    }, 200);
                } catch (e) {
                    logger.error('localStorage post failed', { error: e.message });
                }
            },
            onMessage: function(cb) {
                window.addEventListener('storage', function(e) {
                    if (!e.key || e.key.indexOf(STORAGE_KEY) !== 0) return;
                    try { cb(JSON.parse(e.newValue)); } catch(err) {
                        logger.warn('Failed to parse storage message', { key: e.key, error: err.message });
                    }
                });
            },
            close: function() {}
        };
        transportType = 'storage';
    }

    if (!Transport) {
        logger.warn('No cross-tab transport available. Events will not be delivered.');
    } else {
        logger.info('Transport initialized', { type: transportType });
    }

    // ─── Ядро ─────────────────────────────────────────────────────
    var listeners = {};
    var isDestroyed = false;
    var stats = { emitted: 0, received: 0, errors: 0 };

    function invokeHandlers(eventName, payload, meta) {
        var handlers = listeners[eventName] || [];
        var wildcardHandlers = listeners['*'] || [];
        var all = handlers.concat(wildcardHandlers);

        if (all.length === 0) {
            logger.debug('No handlers for event', { event: eventName });
            return;
        }

        logger.debug('Invoking handlers', {
            event: eventName,
            handlerCount: all.length,
            fromTab: meta.tabId
        });

        for (var i = 0; i < all.length; i++) {
            try {
                all[i](payload, meta);
            } catch (e) {
                stats.errors++;
                logger.error('Handler threw exception', {
                    event: eventName,
                    error: e.message,
                    stack: e.stack
                });
            }
        }
    }

    if (Transport) {
        Transport.onMessage(function(data) {
            if (isDestroyed) return;
            if (!data || data._tabId === TAB_ID) return;

            stats.received++;
            logger.debug('Event received <-', {
                event: data.event,
                fromTab: data._tabId,
                age: Date.now() - data._ts + 'ms'
            });

            invokeHandlers(data.event, data.payload, {
                tabId: data._tabId,
                timestamp: data._ts,
                transport: transportType
            });
        });
    }

    function emit(event, payload) {
        if (isDestroyed) {
            logger.warn('Emit called on destroyed instance', { event: event });
            return;
        }
        if (!Transport) {
            logger.warn('Emit skipped: no transport', { event: event });
            return;
        }

        stats.emitted++;
        logger.debug('Emit ->', { event: event, hasPayload: payload !== undefined });

        Transport.post({
            event: event,
            payload: payload,
            _tabId: TAB_ID,
            _ts: Date.now()
        });
    }

    function on(event, handler) {
        if (typeof handler !== 'function') {
            logger.error('on(): handler must be a function', { event: event });
            throw new Error('Handler must be a function');
        }
        if (!listeners[event]) listeners[event] = [];
        listeners[event].push(handler);

        logger.debug('Subscribed', { event: event, totalHandlers: listeners[event].length });

        return function unsubscribe() {
            if (!listeners[event]) return;
            var idx = listeners[event].indexOf(handler);
            if (idx > -1) {
                listeners[event].splice(idx, 1);
                logger.debug('Unsubscribed', { event: event, remaining: listeners[event].length });
            }
        };
    }

    function once(event, handler) {
        var unsub = on(event, function(payload, meta) {
            unsub();
            handler(payload, meta);
        });
        return unsub;
    }

    function destroy() {
        isDestroyed = true;
        listeners = {};
        if (Transport && Transport.close) Transport.close();
        logger.info('Instance destroyed', { finalStats: stats });
    }

    return {
        emit: emit,
        on: on,
        once: once,
        destroy: destroy,
        log: logger,
        getTabId: function() { return TAB_ID; },
        getTransport: function() { return transportType; },
        getStats: function() { return { emitted: stats.emitted, received: stats.received, errors: stats.errors }; },
        version: VERSION
    };
}));
