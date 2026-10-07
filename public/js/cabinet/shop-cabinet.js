/**
 * Живой кабинет в лейауте магазина: дополнение к account.js.
 * account.js (cabinetOrders/cabinetBonus/cabinetReferral/cabinetContact +
 * Alpine) переиспользуется как есть — биндится к тем же oc- и cabp-классам.
 * Здесь только то, чего в account.js нет: шапка разделов (me-chip) и чат.
 * Эндпоинты — те же /account/api/* (см. AccountController).
 */
(function () {
    'use strict';

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    async function ensureFingerprint() {
        try {
            if (typeof window.DeviceLib === 'undefined') return '';
            var r = await new window.DeviceLib().get();
            var fp = (r && r.hash && (r.hash.v4 || String(r.hash.numeric || ''))) || '';
            return String(fp || '');
        } catch (e) {
            return '';
        }
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
        var res = await fetch(url, fetchOpts);
        var data = await res.json().catch(function () { return {}; });
        if (res.status === 401 && data && data.login === false) {
            var err = new Error('Session expired, please log in again.');
            err.login = false;
            throw err;
        }
        if (!res.ok) {
            var msg = (data && (data.error || data.message)) || ('Request failed (' + res.status + ')');
            var e2 = new Error(msg);
            e2.status = res.status;
            e2.payload = data;
            throw e2;
        }
        return data;
    }

    function pickObject(body) {
        if (!body || typeof body !== 'object') return {};
        if (body.data && typeof body.data === 'object' && !Array.isArray(body.data)) return body.data;
        return body;
    }

    function scopeMessage(e, fallback, cfg) {
        if (e && e.status === 403) {
            return (cfg && cfg.tScopeForbidden) || 'This section is not available for this shop.';
        }
        return (e && e.message) || fallback;
    }

    function loginRedirect(cfg) {
        window.location.href = (cfg && cfg.loginUrl) || '/preview/cabinet-login/design_10/email';
    }

    // ---------- Шапка разделов: me-chip (инициалы/имя/бонус) ----------
    // Скоуп me выключен (403) — блок прячем целиком, как в cabinetShell.
    window.shopCabinetNav = function () {
        var cfg = window.CabinetConfig || {};
        return {
            meForbidden: false,
            meLoading: true,
            avatarInitials: '?',
            userName: '',
            userIdLabel: '',
            bonusText: '',
            async init() {
                try {
                    var d = pickObject(await api(cfg.meUrl || '/account/api/me'));
                    var name = d.name || d.email || '';
                    var id = d.id || d.customerId || d.customer_id || '';
                    var bonus = (d.bonus !== undefined && d.bonus !== null) ? d.bonus : d.balance;
                    if (name) this.userName = name;
                    if (id) this.userIdLabel = 'ID' + id;
                    this.avatarInitials = String(name || '?').trim().split(/\s+/)
                        .map(function (w) { return w[0]; }).slice(0, 2).join('').toUpperCase() || '?';
                    if (bonus !== undefined && bonus !== null && bonus !== '') {
                        this.bonusText = '$' + Number(bonus).toFixed(2);
                    }
                } catch (e) {
                    if (e && e.login === false) { loginRedirect(cfg); return; }
                    if (e && e.status === 403) this.meForbidden = true;
                }
                this.meLoading = false;
            },
        };
    };

    // ---------- Chat (Chat.vue 1:1): лента + композер ----------
    window.cabinetChat = function () {
        var cfg = window.CabinetConfig || {};
        return {
            loading: true,
            loadingMore: false,
            failed: '',
            sending: false,
            sendError: '',
            messages: [],
            nextOffset: null,
            subject: '',
            text: '',
            get empty() { return !this.loading && !this.failed && !this.messages.length; },
            async init() { await this.load(true); },
            async reload() { await this.load(true); },
            async load(replace) {
                var self = this;
                if (replace) {
                    this.loading = true;
                    this.failed = '';
                }
                try {
                    var query = { per_page: 20 };
                    if (!replace && this.nextOffset !== null) query.offset = this.nextOffset;
                    var body = await api(cfg.chatUrl || '/account/api/chat/messages', { query: query });
                    var d = pickObject(body);
                    var list = Array.isArray(body.messages) ? body.messages : (Array.isArray(d.messages) ? d.messages : []);
                    var offset = (body.next_offset !== undefined) ? body.next_offset : d.nextOffset;
                    // Бэк отдаёт newest-first — рисуем oldest-first.
                    list = list.slice().reverse();
                    this.messages = replace ? list : list.concat(this.messages);
                    this.nextOffset = (offset === undefined || offset === null) ? null : offset;
                    this.failed = '';
                    this.$nextTick(function () { self.scrollBottom(); });
                } catch (e) {
                    if (e && e.login === false) { loginRedirect(cfg); return; }
                    if (replace) {
                        this.failed = scopeMessage(e, 'Failed to load chat.', cfg);
                        this.messages = [];
                    }
                } finally {
                    if (replace) this.loading = false;
                    this.loadingMore = false;
                }
            },
            async loadMore() {
                if (this.loading || this.loadingMore || this.nextOffset === null) return;
                this.loadingMore = true;
                await this.load(false);
            },
            scrollBottom() {
                try {
                    var el = this.$refs && this.$refs.thread;
                    if (el) el.scrollTop = el.scrollHeight;
                } catch (e) {}
            },
            authorOf(m) {
                if (!m) return '';
                if (m.from === 'me') return (cfg.tYou || 'You');
                return m.author || '';
            },
            escapeHtml(s) {
                return String(s === undefined || s === null ? '' : s).replace(/[&<>"']/g, function (c) {
                    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
                });
            },
            // Тело письма — HTML (textHtml с портала, уже почищен).
            // Нет textHtml (старый кабинет) — экранируем текст сами.
            msgHtml(m) {
                if (!m) return '';
                if (m.textHtml) return m.textHtml;
                return this.escapeHtml(m.text).replace(/\n/g, '<br>');
            },
            async send() {
                if (this.sending) return;
                if (!this.text.trim()) return;
                this.sending = true;
                this.sendError = '';
                try {
                    var body = await api(cfg.chatSendUrl || '/account/api/chat/messages', {
                        method: 'POST',
                        body: { subject: this.subject.trim(), message: this.text.trim() },
                    });
                    var d = pickObject(body);
                    var m = d.message || null;
                    if (m) this.messages.push(m);
                    this.subject = '';
                    this.text = '';
                    this.$nextTick(function () {
                        try {
                            var el = this.$refs && this.$refs.thread;
                            if (el) el.scrollTop = el.scrollHeight;
                        } catch (e) {}
                    }.bind(this));
                } catch (e) {
                    if (e && e.login === false) { loginRedirect(cfg); return; }
                    this.sendError = scopeMessage(e, 'Could not send the message. Please try again.', cfg);
                } finally {
                    this.sending = false;
                }
            },
        };
    };
})();
