/**
 * Встроенный личный кабинет — клиентская логика (Alpine.js, без сборки).
 *
 * 1:1 с customerportal:
 *  - assets/Composables/useOrderActions.js (repeat/stop/formatMoney/itemName)
 *  - assets/Pages/Orders.vue (фильтры, infinite scroll sentinel 200px, скелетоны)
 *  - assets/Components/orders/* (copy 1.5с, shipping popover, tracking drawer 5 событий)
 *  - assets/Pages/Bonus.vue (copy tippy 900мс, expiring details, пагинация)
 *  - assets/Pages/Referral.vue (copy 900мс)
 *  - assets/Pages/Contact.vue (префилл из me, confirm при смене name/email, success #ok)
 *  - assets/Layouts/AppLayout.vue + AppHeader.vue (drawer, popover, бейдж корзины)
 *
 * Токены живут только в сессии сервера шопа. Браузер считает fingerprint
 * через f.js кабинета (window.CabinetFpUrl, глобал DeviceLib) и прикладывает его
 * к запросам прокси (X-FP → сервер перекладывает в X-Fingerprint).
 */
(function () {
    'use strict';

    var fpPromise = null;

    function loadScript(src, timeoutMs) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            var done = false;
            var timer = setTimeout(function () {
                if (!done) { done = true; reject(new Error('fingerprint load timeout')); }
            }, timeoutMs || 5000);
            s.onload = function () { if (!done) { done = true; clearTimeout(timer); resolve(); } };
            s.onerror = function () { if (!done) { done = true; clearTimeout(timer); reject(new Error('fingerprint load failed')); } };
            s.src = src;
            document.head.appendChild(s);
        });
    }

    function ensureFingerprint() {
        if (window.CabinetAccountFp) return Promise.resolve(window.CabinetAccountFp);
        if (fpPromise) return fpPromise;
        fpPromise = (async function () {
            try {
                // Своя копия в файлах шопа (same-origin): не режется баннерами
                // и не зависит от деплоя кабинета. Дальше — фолбэки на кабинет.
                if (!window.DeviceLib && !window.FingerprintLib) {
                    try {
                        await loadScript('/js/cabinet/device-lib.js', 5000);
                    } catch (e) {
                        var fpUrl = window.CabinetFpUrl || '';
                        if (fpUrl) {
                            try {
                                await loadScript(fpUrl, 5000);
                            } catch (e2) {
                                var legacy = fpUrl.replace(/\/f\.js([?#]|$)/, '/fingerprint.js$1');
                                if (legacy !== fpUrl) {
                                    try { await loadScript(legacy, 5000); } catch (e3) { /* ниже вернём '' */ }
                                }
                            }
                        }
                    }
                }
                var Ctor = window.DeviceLib || window.FingerprintLib;
                if (!Ctor) return '';
                var lib = new Ctor();
                var r = await lib.get();
                var fp = (r && r.hash && (r.hash.v4 || String(r.hash.numeric || ''))) || '';
                if (fp) window.CabinetAccountFp = String(fp);
                return window.CabinetAccountFp || '';
            } catch (e) {
                return '';
            } finally {
                fpPromise = null;
            }
        })();
        return fpPromise;
    }

    async function api(path, opts) {
        opts = opts || {};
        var url = path;
        if (opts.query) {
            var qs = new URLSearchParams();
            Object.keys(opts.query).forEach(function (k) {
                var v = opts.query[k];
                if (v !== undefined && v !== null && v !== '') qs.set(k, String(v));
            });
            var s = qs.toString();
            if (s) url += (url.indexOf('?') === -1 ? '?' : '&') + s;
        }
        var method = (opts.method || 'GET').toUpperCase();
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-FP': await ensureFingerprint(),
        };
        if (window.CabinetCsrfToken) headers['X-CSRF-TOKEN'] = window.CabinetCsrfToken;
        var fetchOpts = { method: method, headers: headers, credentials: 'same-origin' };
        if (method !== 'GET') {
            headers['Content-Type'] = 'application/json';
            fetchOpts.body = JSON.stringify(opts.body || {});
        }
        // E2EE: шифруем тела и ответы, шоп — слепой ретранслятор (e2ee.js).
        // Нет ключей — обычный plaintext (миграция без флага).
        var e2ee = (typeof window.E2EE !== 'undefined') ? window.E2EE : null;
        var e2eeEsk = null;
        if (e2ee) {
            try {
                var e2eeKeys = await e2ee.ensureKeys();
                if (e2eeKeys) {
                    headers['X-E2EE'] = '1';
                    if (method === 'GET') {
                        var e2eeG = e2ee.sealGet();
                        if (e2eeG) {
                            headers['X-E2EE-Epk'] = e2ee.b64uEncode(e2eeG.epk);
                            headers['X-E2EE-Kb'] = e2ee.hasCodeKey() ? '1' : '0';
                            e2eeEsk = e2eeG.esk;
                        }
                    } else {
                        var e2eeSealed = e2ee.sealRequest(url, method, opts.body || {}, e2eeKeys);
                        if (e2eeSealed) {
                            fetchOpts.body = JSON.stringify(e2eeSealed.envelope);
                        } else {
                            delete headers['X-E2EE'];
                        }
                    }
                }
            } catch (e2eeErr) {
                delete headers['X-E2EE'];
            }
        }
        var res = await fetch(url, fetchOpts);
        var data = await res.json().catch(function () { return {}; });
        if (e2eeEsk && res.ok && data && data.v === 2 && typeof data.ct === 'string') {
            try {
                data = e2ee.unwrapResponse(data, e2eeEsk);
            } catch (e2eeOpenErr) {
                var e2eeOpenError = new Error('E2EE: open failed');
                e2eeOpenError.status = res.status;
                throw e2eeOpenError;
            }
        }
        if (res.status === 401 && data && data.login === false) {
            var err = new Error('Session expired, please log in again.');
            err.login = false;
            throw err;
        }
        if (!res.ok) {
            if (e2ee && data && data.code === 'e2ee_rekey' && !opts._e2eeRetried) {
                e2ee.forgetKeys();
                opts._e2eeRetried = true;
                return api(path, opts);
            }
            var msg = (data && (data.error || data.message)) || ('Request failed (' + res.status + ')');
            var e2 = new Error(msg);
            e2.status = res.status;
            e2.payload = data;
            throw e2;
        }
        return data;
    }

    function formatMoney(amount) {
        var value = Number(amount || 0);
        try {
            var fmt = new Intl.NumberFormat(document.documentElement.lang || 'en', {
                style: 'currency', currency: 'USD', currencyDisplay: 'narrowSymbol',
            });
            return fmt.formatToParts(value).reduce(function (acc, part, i, arr) {
                if (part.type === 'literal' && /^\s+$/.test(part.value)) {
                    var prev = arr[i - 1] && arr[i - 1].type;
                    var next = arr[i + 1] && arr[i + 1].type;
                    if (prev === 'currency' || next === 'currency') return acc;
                }
                return acc + part.value;
            }, '');
        } catch (e) {
            return '$' + value.toFixed(2);
        }
    }

    // 1:1 с customerportal assets/Utils/orderView.js itemName().
    function itemName(item) {
        var parts = [item.name, item.dosage];
        if (item.packSize && item.quantity) parts.push('x ' + (item.packSize * item.quantity));
        if (item.type) parts.push(item.type);
        return parts.filter(function (p) { return p !== null && p !== undefined && String(p).trim() !== ''; }).join(' ');
    }

    function itemsTotal(items) {
        return (items || []).reduce(function (s, it) { return s + Number(it.price || 0) * Number(it.quantity || 0); }, 0);
    }

    function pillClass(statusKey) {
        switch (statusKey) {
            case 'delivered': return 'oc-pill--delivered';
            case 'approved': return 'oc-pill--processing';
            case 'declined':
            case 'refunded': return 'oc-pill--cancelled';
            case 'checking':
            default: return 'oc-pill--pending';
        }
    }

    async function copyText(value) {
        try {
            await navigator.clipboard.writeText(value);
            return true;
        } catch (e) {
            try {
                var ta = document.createElement('textarea');
                ta.value = value;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                ta.remove();
                return true;
            } catch (e2) {
                return false;
            }
        }
    }

    function showToast(ctx, message, ms) {
        ctx.toast = message;
        if (ctx.toastTimer) clearTimeout(ctx.toastTimer);
        ctx.toastTimer = setTimeout(function () { ctx.toast = ''; ctx.toastTimer = null; }, ms || 4000);
    }

    // Скоуп выключен у API-клиента (403 от кабинета): вместо технической
    // ошибки показываем понятный текст (cfg.tScopeForbidden со страницы).
    function scopeMessage(e, fallback, cfg) {
        if (e && e.status === 403) {
            return (cfg && cfg.tScopeForbidden) || 'This section is not available for this shop.';
        }
        return (e && e.message) || fallback;
    }

    function pickList(body) {
        if (!body) return { items: [], cursor: null };
        var d = body.data && typeof body.data === 'object' ? body.data : {};
        var items = [];
        if (Array.isArray(body.items)) items = body.items;
        else if (Array.isArray(d.items)) items = d.items;
        var cursor = body.nextCursor;
        if (cursor === undefined) cursor = body.next_cursor;
        if (cursor === undefined) cursor = d.nextCursor;
        if (cursor === undefined) cursor = d.next_cursor;
        if (cursor === undefined) cursor = null;
        return { items: items, cursor: cursor };
    }

    function pickObject(body) {
        if (!body || typeof body !== 'object') return {};
        if (body.data && typeof body.data === 'object' && !Array.isArray(body.data)) return body.data;
        return body;
    }

    // ---------- Каркас layout: drawer/popover/юзер (AppHeader 1:1) ----------
    window.cabinetShell = function () {
        return {
            menuOpen: false,
            headerStuck: false,
            userPopoverOpen: false,
            phonesOpen: false,
            // Скоуп me выключен у API-клиента (403 от /account/api/me) —
            // блок с данными кастомера не показываем вообще.
            meForbidden: false,
            // Пока /me грузится — спиннер вместо плейсхолдеров.
            meLoading: true,
            userName: '',
            userIdLabel: '',
            avatarInitials: '?',
            bonusText: '',

            async init() {
                var self = this;
                this.onScroll();
                window.addEventListener('scroll', function () { self.onScroll(); }, { passive: true });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        self.menuOpen = false;
                        self.userPopoverOpen = false;
                    }
                });
                this.$watch('menuOpen', function (open) {
                    document.body.classList.toggle('lock', open);
                });
                // Кастомные скроллбары drawer и списка телефонов (AppHeader 1:1).
                this.$nextTick(function () {
                    self.mountScrollbars();
                });
                try {
                    var me = await api(window.CabinetMeUrl || '/account/api/me');
                    var d = pickObject(me);
                    var name = d.name || d.email || '';
                    // Кабинет отдаёт /api/v1/me как {customer_id, email, balance} —
                    // полей name/id/bonus там нет, маппим их синонимы.
                    var id = d.id || d.customerId || d.customer_id || '';
                    var bonus = d.bonus;
                    if (bonus === undefined || bonus === null) bonus = d.balance;
                    if (name) this.userName = name;
                    if (id) this.userIdLabel = 'ID' + id;
                    var initials = String(name || '?').trim().split(/\s+/).map(function (w) { return w[0]; }).slice(0, 2).join('').toUpperCase();
                    this.avatarInitials = initials || '?';
                    if (bonus !== undefined && bonus !== null && bonus !== '') {
                        this.bonusText = '$' + Number(bonus).toFixed(2);
                    }
                } catch (e) {
                    // Скоуп me отключён — прячем блок, пустые плейсхолдеры не показываем.
                    if (e && e.status === 403) this.meForbidden = true;
                }
                this.meLoading = false;
            },

            mountScrollbars() {
                if (!window.CustomScrollbar) return;
                var drawer = this.$refs && this.$refs.drawerScroll;
                if (drawer && !drawer.__customScrollbar) {
                    try {
                        var a = new CustomScrollbar(drawer, { visibility: 'auto', idleDelay: 600 });
                        if (!a.valid) a.destroy();
                    } catch (e) { /* noop */ }
                }
                var phones = this.$refs && this.$refs.phonesList;
                if (phones && !phones.__customScrollbar) {
                    try {
                        var b = new CustomScrollbar(phones, { visibility: 'auto', idleDelay: 600 });
                        if (!b.valid) b.destroy();
                    } catch (e) { /* noop */ }
                }
            },

            onScroll() {
                this.headerStuck = window.scrollY > 0;
            },
        };
    };

    // Верхняя панель layout (legacy, оставлена для совместимости).
    window.cabinetTopbar = function () {
        var cfg = window.CabinetConfig || {};
        return {
            email: cfg.email || '',
            balance: null,
            async init() {
                try {
                    var me = await api('/account/api/me');
                    var d = pickObject(me);
                    if (d.email) this.email = d.email;
                    if (d.balance !== undefined && d.balance !== null) this.balance = d.balance;
                    else if (d.bonus !== undefined && d.bonus !== null) this.balance = d.bonus;
                } catch (e) { /* панель опциональна */ }
            },
        };
    };

    // ---------- My orders (Orders.vue + OrderCard* + OrdersSidebar 1:1) ----------
    window.cabinetOrders = function () {
        var cfg = window.CabinetConfig || {};
        return {
            items: [],
            nextCursor: null,
            loading: true,
            loadingMore: false,
            failed: '',
            activeFilter: null,
            filtersOpen: false,
            switchingFilter: false,
            openItems: {},
            shipOpen: {},
            shipLoading: {},
            shipFailed: {},
            shipLoaded: {},
            shipData: {},
            trackRendered: {},
            trackLoading: {},
            trackFailed: {},
            trackData: {},
            trackExpanded: {},
            orderCopiedId: null,
            orderCopyTimer: null,
            trackOrderCopied: null,
            trackCopied: null,
            trackCopyTimer: null,
            stoppingId: null,
            repeatingId: null,
            confirmStopId: null,
            toast: '',
            toastTimer: null,
            observer: null,

            get filters() {
                var t = cfg.tFilters || {};
                return [
                    { key: 'all', label: t.all || 'All' },
                    { key: 'checking', label: t.checking || 'Checking' },
                    { key: 'approved', label: t.approved || 'Approved' },
                    { key: 'delivered', label: t.delivered || 'Delivered' },
                    { key: 'declined', label: t.declined || 'Declined' },
                    { key: 'refunded', label: t.refunded || 'Refunded' },
                ];
            },

            get filtered() { return this.activeFilter !== null; },

            get listBusy() { return this.loading || this.loadingMore || this.switchingFilter; },

            get activeFilterLabel() {
                var self = this;
                var found = this.filters.filter(function (f) { return (f.key === 'all' ? null : f.key) === self.activeFilter; });
                return found.length ? found[0].label : (cfg.tFilters && cfg.tFilters.all) || 'All';
            },

            async init() {
                var self = this;
                await this.loadList(true);
                var sentinel = this.$refs && this.$refs.sentinel;
                if (sentinel && 'IntersectionObserver' in window) {
                    this.observer = new IntersectionObserver(function (entries) {
                        if (entries[0] && entries[0].isIntersecting) self.loadMore();
                    }, { rootMargin: '200px 0px' });
                    this.observer.observe(sentinel);
                }
            },

            destroy() {
                if (this.observer) { this.observer.disconnect(); this.observer = null; }
            },

            statusParam() {
                return this.activeFilter ? { status: this.activeFilter } : {};
            },

            async loadList(replace) {
                var self = this;
                if (replace) {
                    this.loading = true;
                    this.failed = '';
                }
                try {
                    var query = { per_page: 15 };
                    if (!replace && this.nextCursor !== null && this.nextCursor !== undefined && this.nextCursor !== '') {
                        query.cursor = this.nextCursor;
                    }
                    if (this.activeFilter) query.status = this.activeFilter;
                    var body = await api(cfg.ordersUrl || '/account/api/orders', { query: query });
                    var parsed = pickList(body);
                    this.items = replace ? parsed.items : this.items.concat(parsed.items);
                    this.nextCursor = parsed.cursor;
                    this.failed = '';
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    if (replace) {
                        this.failed = scopeMessage(e, 'Failed to load orders.', cfg);
                        this.items = [];
                    } else {
                        showToast(this, scopeMessage(e, 'Failed to load orders.', cfg));
                    }
                } finally {
                    if (replace) this.loading = false;
                    this.loadingMore = false;
                    this.switchingFilter = false;
                }
            },

            async reload() {
                await this.loadList(true);
            },

            async loadMore() {
                if (this.loading || this.loadingMore || this.switchingFilter) return;
                if (this.nextCursor === null || this.nextCursor === undefined || this.nextCursor === '') return;
                this.loadingMore = true;
                await this.loadList(false);
            },

            async selectFilter(key) {
                var next = key === 'all' ? null : key;
                if (next === this.activeFilter || this.loading || this.switchingFilter) {
                    this.filtersOpen = false;
                    return;
                }
                var prev = this.activeFilter;
                this.activeFilter = next;
                this.filtersOpen = false;
                this.switchingFilter = true;
                this.nextCursor = null;
                try {
                    var query = { per_page: 15 };
                    if (next) query.status = next;
                    var body = await api(cfg.ordersUrl || '/account/api/orders', { query: query });
                    var parsed = pickList(body);
                    this.items = parsed.items;
                    this.nextCursor = parsed.cursor;
                    this.failed = '';
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    this.activeFilter = prev;
                    showToast(this, scopeMessage(e, 'Failed to load orders.', cfg));
                } finally {
                    this.switchingFilter = false;
                }
            },

            // ----- Карточка -----
            statusLabel(order) {
                var map = cfg.tStatus || {};
                return map[order.statusKey] || order.status || '';
            },

            orderNumber(id) {
                return String(cfg.tOrderNumber || 'Order #:id').replace(':id', id);
            },

            itemsLabel(order) {
                var n = (order.items || []).length;
                return String(cfg.tItems || 'Items').replace(':count', n);
            },

            nextDateLine(order) {
                if (!order.nextRecurringDate || order.nextRecurringDate === 'none') return '';
                return String(cfg.tNextDate || '').replace(':date', order.nextRecurringDate);
            },

            showStop(order) {
                return !!(order.nextRecurringDate && order.nextRecurringDate !== 'none');
            },

            isItemsOpen(id) { return this.openItems[id] !== false; },
            toggleItems(id) { this.openItems[id] = !this.isItemsOpen(id); },

            async copyOrderNumber(id, forTrack) {
                if (await copyText(String(id))) {
                    var self = this;
                    if (forTrack) {
                        this.trackOrderCopied = id;
                    } else {
                        this.orderCopiedId = id;
                    }
                    if (this.orderCopyTimer) clearTimeout(this.orderCopyTimer);
                    this.orderCopyTimer = setTimeout(function () {
                        self.orderCopiedId = null;
                        self.trackOrderCopied = null;
                    }, 1500);
                }
            },

            // ----- Shipping popover -----
            isShipOpen(id) { return !!this.shipOpen[id]; },

            toggleShip(id) {
                this.shipOpen[id] = !this.shipOpen[id];
                if (this.shipOpen[id] && !this.shipLoaded[id] && !this.shipLoading[id]) this.loadShip(id);
            },

            closeShip(id) { this.shipOpen[id] = false; },

            async loadShip(id) {
                if (this.shipLoading[id]) return;
                this.shipLoading[id] = true;
                this.shipFailed[id] = false;
                try {
                    var body = await api((cfg.shipUrl || '/account/api/orders') + '/' + id + '/shipping');
                    var d = pickObject(body);
                    this.shipData[id] = d.shipping !== undefined ? { shipping: d.shipping, countryName: d.countryName || null } : (d.firstname || d.address ? { shipping: d, countryName: d.countryName || null } : null);
                    if (this.shipData[id] && !this.shipData[id].shipping) this.shipData[id] = null;
                    this.shipLoaded[id] = true;
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    this.shipFailed[id] = true;
                    this.shipLoaded[id] = true;
                } finally {
                    this.shipLoading[id] = false;
                }
            },

            shipFullName(id) {
                var s = this.shipData[id] && this.shipData[id].shipping;
                if (!s) return '';
                return ((s.firstname || '') + ' ' + (s.lastname || '')).trim();
            },

            shipAddress(id) {
                var s = this.shipData[id] && this.shipData[id].shipping;
                return (s && s.address) || '';
            },

            shipCityLine(id) {
                var s = this.shipData[id] && this.shipData[id].shipping;
                if (!s) return '';
                var city = s.city || '';
                var state = s.stateName || s.state || '';
                var zip = s.postalCode || '';
                return (city + (state ? ', ' + state : '') + (zip ? ' ' + zip : '')).trim();
            },

            shipCountryLine(id) {
                var entry = this.shipData[id];
                if (!entry || !entry.shipping) return '';
                var code = entry.shipping.countryCode || '';
                if (entry.countryName && code) return entry.countryName + ' (' + code + ')';
                return entry.countryName || code;
            },

            // ----- Tracking drawer -----
            openTrack(id) {
                this.trackRendered[id] = true;
                document.body.style.overflow = 'hidden';
                if (!this.trackData[id] && !this.trackLoading[id]) this.loadTrack(id);
            },

            closeTrack(id) {
                this.trackRendered[id] = false;
                document.body.style.overflow = '';
            },

            async loadTrack(id) {
                if (this.trackLoading[id]) return;
                this.trackLoading[id] = true;
                this.trackFailed[id] = false;
                try {
                    var body = await api((cfg.shipUrl || '/account/api/orders') + '/' + id + '/tracking');
                    var d = pickObject(body);
                    var tr = d.tracking || d;
                    this.trackData[id] = Array.isArray(tr.tracks) ? tr.tracks : [];
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    this.trackFailed[id] = true;
                } finally {
                    this.trackLoading[id] = false;
                }
            },

            trackList(id) { return this.trackData[id] || []; },

            trackFirst(id) {
                var list = this.trackList(id);
                for (var i = 0; i < list.length; i++) {
                    if (list[i] && list[i].latestStatus) return list[i];
                }
                return list[0] || null;
            },

            trackDestination(id) {
                var first = this.trackFirst(id);
                if (!first) return '—';
                return first.toCountry || first.toName || '—';
            },

            trackDays(id) {
                var first = this.trackFirst(id);
                if (first && first.days) return String(cfg.tDays || ':n days').replace(':n', first.days);
                return '—';
            },

            trackLatest(id) {
                var first = this.trackFirst(id);
                return (first && first.latestTime) || '—';
            },

            trackKey(id, track, pIdx) {
                return id + '|' + (track.number || '') + '|' + pIdx;
            },

            isProviderExpanded(id, track, pIdx) {
                return !!this.trackExpanded[this.trackKey(id, track, pIdx)];
            },

            toggleProvider(id, track, pIdx) {
                var k = this.trackKey(id, track, pIdx);
                this.trackExpanded[k] = !this.trackExpanded[k];
            },

            visibleEvents(id, track, pIdx) {
                var events = ((track.providers || [])[pIdx] && (track.providers || [])[pIdx].events) || [];
                if (this.isProviderExpanded(id, track, pIdx)) return events;
                return events.slice(0, 5);
            },

            showAllLabel(provider) {
                var n = ((provider || {}).events || []).length;
                return String(cfg.tShowAll || 'Show all (:count)').replace(':count', n);
            },

            async copyTrackNumber(number) {
                if (await copyText(String(number || ''))) {
                    var self = this;
                    this.trackCopied = number;
                    if (this.trackCopyTimer) clearTimeout(this.trackCopyTimer);
                    this.trackCopyTimer = setTimeout(function () { self.trackCopied = null; }, 1500);
                }
            },

            // ----- Repeat / Stop -----
            async repeatVirtual(order) {
                if (this.repeatingId) return;
                this.repeatingId = order.id;
                try {
                    // Локальный повтор: позиции с packagingId уходят в корзину шопа,
                    // API кабинета не дёргаем. Ответ: {status, checkoutUrl} или {status:'error', message}.
                    var body = await api((cfg.repeatUrl || '/account/api/orders') + '/' + order.id + '/repeat-virtual', {
                        method: 'POST',
                        body: { items: order.items || [] },
                    });
                    var d = pickObject(body);
                    if (!d || d.status !== 'success' || !d.checkoutUrl) {
                        throw new Error((d && d.message) || cfg.tRepeatFailed || 'Repeat failed');
                    }
                    window.location.href = d.checkoutUrl;
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    showToast(this, scopeMessage(e, cfg.tRepeatFailed || 'Repeat failed', cfg));
                } finally {
                    this.repeatingId = null;
                }
            },

            askStop(id) { this.confirmStopId = id; },
            cancelStop() { this.confirmStopId = null; },

            async confirmStop() {
                var id = this.confirmStopId;
                if (!id || this.stoppingId) return;
                this.stoppingId = id;
                try {
                    await api((cfg.stopUrl || '/account/api/orders') + '/' + id + '/stop-recurring', { method: 'POST' });
                    var self = this;
                    this.items = this.items.map(function (o) {
                        if (o.id === id) o.nextRecurringDate = 'none';
                        return o;
                    });
                    this.confirmStopId = null;
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    showToast(this, scopeMessage(e, 'Failed to stop auto repeat.', cfg));
                } finally {
                    this.stoppingId = null;
                }
            },

            pillClass: pillClass,
            formatMoney: formatMoney,
            itemName: itemName,
            itemsTotal: itemsTotal,
        };
    };

    // ---------- Bonus (Bonus.vue 1:1) ----------
    window.cabinetBonus = function () {
        var cfg = window.CabinetConfig || {};
        return {
            loading: true,
            failed: '',
            balance: 0,
            cardNumber: '',
            cardStatus: '',
            expiring: [],
            history: [],
            page: 1,
            totalPages: 1,
            perPage: 10,
            copied: '',
            copyTimer: null,

            get cardStatusLabel() {
                if (!this.cardStatus) return '—';
                return this.cardStatus.charAt(0).toUpperCase() + this.cardStatus.slice(1);
            },

            get pages() {
                var out = [];
                for (var i = 1; i <= this.totalPages; i++) out.push(i);
                return out;
            },

            async init() { await this.load(1); },

            async reload() { await this.load(this.page); },

            async load(page) {
                this.loading = true;
                this.failed = '';
                try {
                    var body = await api(cfg.bonusUrl || '/account/api/bonus', { query: { page: page, per_page: this.perPage } });
                    var d = pickObject(body);
                    this.balance = d.balance || 0;
                    this.cardNumber = d.cardNumber || '';
                    this.cardStatus = d.cardStatus || '';
                    this.expiring = d.expiringBonuses || [];
                    this.history = d.history || [];
                    this.page = d.page || page;
                    this.perPage = d.perPage || this.perPage;
                    this.totalPages = d.totalPages || 1;
                } catch (e) {
                    this.failed = scopeMessage(e, 'Failed to load bonus program.', cfg);
                } finally {
                    this.loading = false;
                }
            },

            async goPage(page) {
                if (page < 1 || page > this.totalPages || page === this.page) return;
                await this.load(page);
            },

            async copyCard() {
                if (!this.cardNumber) return;
                if (await copyText(String(this.cardNumber))) {
                    var self = this;
                    this.copied = 'card';
                    if (this.copyTimer) clearTimeout(this.copyTimer);
                    this.copyTimer = setTimeout(function () { self.copied = ''; }, 900);
                }
            },

            expireAmount(amount) {
                return String(cfg.tExpireAmount || 'expire :amount').replace(':amount', '$' + amount);
            },

            actionLabel(item) {
                var map = cfg.tActions || {};
                var tpl = map[item.action] || '-';
                return String(tpl).replace(':id', item.orderId || '');
            },

            deltaColor(delta) {
                if (delta > 0) return 'var(--primary-color)';
                if (delta < 0) return 'var(--red-light)';
                return undefined;
            },

            deltaText(delta) {
                if (delta > 0) return '$' + delta;
                if (delta < 0) return '-$' + Math.abs(delta);
                return '$0';
            },
        };
    };

    // ---------- Referral (Referral.vue 1:1) ----------
    window.cabinetReferral = function () {
        var cfg = window.CabinetConfig || {};
        return {
            loading: true,
            failed: '',
            link: '',
            qrCode: '',
            coupon: null,
            copied: '',
            copyTimer: null,

            async init() { await this.load(); },

            async reload() { await this.load(); },

            async load() {
                this.loading = true;
                this.failed = '';
                try {
                    var body = await api(cfg.referralUrl || '/account/api/referral');
                    var d = pickObject(body);
                    this.link = d.link || '';
                    this.qrCode = d.qrCode || '';
                    this.coupon = d.coupon || null;
                } catch (e) {
                    this.failed = scopeMessage(e, 'Failed to load referral program.', cfg);
                } finally {
                    this.loading = false;
                }
            },

            async copyValue(key, value) {
                if (await copyText(String(value || ''))) {
                    var self = this;
                    this.copied = key;
                    if (this.copyTimer) clearTimeout(this.copyTimer);
                    this.copyTimer = setTimeout(function () { self.copied = ''; }, 900);
                }
            },
        };
    };

    // ---------- Sessions (legacy, без изменений поведения) ----------
    window.cabinetSessions = function () {
        var cfg = window.CabinetConfig || {};
        return {
            loading: true,
            error: '',
            sessions: [],
            confirmOpen: false,
            revoking: false,
            async init() { await this.load(); },
            async load() {
                this.loading = true;
                this.error = '';
                try {
                    var data = await api('/account/api/sessions');
                    this.sessions = (data && data.sessions) || [];
                } catch (e) {
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    this.error = scopeMessage(e, 'Failed to load sessions.', cfg);
                } finally {
                    this.loading = false;
                }
            },
            formatDate: function (ts) {
                try {
                    return new Date(Number(ts) * 1000).toLocaleString();
                } catch (e) {
                    return '';
                }
            },
            askRevoke() { this.confirmOpen = true; },
            async confirmRevoke() {
                if (this.revoking) return;
                this.revoking = true;
                try {
                    await api('/account/api/sessions/revoke-all', { method: 'POST' });
                    try {
                        if (window.E2EE) window.E2EE.forgetAll();
                    } catch (e2eeErr) {}
                    window.location.href = cfg.startUrl || '/account';
                } catch (e) {
                    this.confirmOpen = false;
                    if (e && e.login === false) { window.location.href = cfg.startUrl || '/account'; return; }
                    alert(scopeMessage(e, 'Failed to revoke sessions.', cfg));
                } finally {
                    this.revoking = false;
                }
            },
        };
    };

    // ---------- Contact (Contact.vue 1:1, авторизованная ветка) ----------
    window.cabinetContact = function () {
        var cfg = window.CabinetConfig || {};
        return {
            sending: false,
            success: false,
            submitError: '',
            fieldErrors: {},
            name: '',
            email: cfg.email || '',
            subject: '',
            message: '',
            initialName: '',
            initialEmail: cfg.email || '',
            confirmOpen: false,

            async init() {
                try {
                    var body = await api(cfg.meUrl || '/account/api/me');
                    var d = pickObject(body);
                    if (d.name) {
                        this.name = d.name;
                        this.initialName = d.name;
                    }
                    if (d.email) {
                        this.email = d.email;
                        this.initialEmail = d.email;
                    }
                } catch (e) { /* префилл опционален */ }
            },

            fieldError(field) {
                return (this.fieldErrors && this.fieldErrors[field]) || '';
            },

            submit() {
                this.submitError = '';
                this.fieldErrors = {};
                if (!this.name.trim() || !this.email.trim() || !this.subject.trim() || !this.message.trim()) {
                    return;
                }
                if (this.name !== this.initialName || this.email !== this.initialEmail) {
                    this.confirmOpen = true;
                    return;
                }
                this.doPost();
            },

            onConfirm() {
                this.confirmOpen = false;
                this.doPost();
            },

            onCancel() {
                this.confirmOpen = false;
            },

            async doPost() {
                if (this.sending) return;
                this.sending = true;
                this.submitError = '';
                this.fieldErrors = {};
                try {
                    await api(cfg.contactUrl || '/account/api/contact', {
                        method: 'POST',
                        body: {
                            name: this.name.trim(),
                            email: this.email.trim(),
                            subject: this.subject.trim(),
                            message: this.message.trim(),
                        },
                    });
                    this.success = true;
                    this.subject = '';
                    this.message = '';
                    this.initialName = this.name;
                    this.initialEmail = this.email;
                } catch (e) {
                    if (e && e.login === false) { window.location.href = '/account'; return; }
                    var payload = (e && e.payload) || {};
                    if (payload.errors && typeof payload.errors === 'object') {
                        var flat = {};
                        Object.keys(payload.errors).forEach(function (k) {
                            var v = payload.errors[k];
                            flat[k] = Array.isArray(v) ? v[0] : String(v);
                        });
                        this.fieldErrors = flat;
                    } else {
                        this.submitError = scopeMessage(e, 'Failed to send message.', cfg);
                    }
                } finally {
                    this.sending = false;
                }
            },
        };
    };
})();
