/* 1:1 with customerportal assets/Utils/CustomScrollbar.js (window build, no modules). */
/**
 * CustomScrollbar вЂ” Р»С‘РіРєРёР№ РєР°СЃС‚РѕРјРЅС‹Р№ СЃРєСЂРѕР»Р»Р±Р°СЂ Р±РµР· Р·Р°РІРёСЃРёРјРѕСЃС‚РµР№.
 *
 * Р”РІР° СЂРµР¶РёРјР° СЂР°Р±РѕС‚С‹:
 *
 * 1) РћР±С‹С‡РЅС‹Р№ (РєРѕРЅС‚РµР№РЅРµСЂ): РєРѕРЅС‚РµРЅС‚ Р·Р°РІРѕСЂР°С‡РёРІР°РµС‚СЃСЏ РІРѕ РІРЅСѓС‚СЂРµРЅРЅРёР№
 *    wrapper (.custom-scrollbar__wrapper), РєРѕС‚РѕСЂС‹Р№ СЏРІР»СЏРµС‚СЃСЏ РЅР°С‚РёРІРЅС‹Рј
 *    СЃРєСЂРѕР»Р»РµСЂРѕРј. Track + thumb РґРѕР±Р°РІР»СЏСЋС‚СЃСЏ РЎРћРЎР•Р”РћРњ wrapper'Р° Рё РїРѕС‚РѕРјСѓ
 *    РЅРµ СѓРµР·Р¶Р°СЋС‚ РІРјРµСЃС‚Рµ СЃ РєРѕРЅС‚РµРЅС‚РѕРј.
 *
 * 2) РЎС‚СЂР°РЅРёС‡РЅС‹Р№ ({ page: true } РёР»Рё С…РѕСЃС‚ = body/html): РЅР°С‚РёРІРЅС‹Р№ СЃРєСЂРѕР»Р»
 *    РѕСЃС‚Р°С‘С‚СЃСЏ РЅР° window (Inertia preserveScroll, РјРѕРґР°Р»РєРё Рё СЏРєРѕСЂСЏ Р¶РёРІСѓС‚
 *    РєР°Рє СЂР°РЅСЊС€Рµ), СЃРёСЃС‚РµРјРЅР°СЏ РїРѕР»РѕСЃР° РїСЂСЏС‡РµС‚СЃСЏ РєР»Р°СЃСЃРѕРј РЅР° <html>, Р° РЅР°С€Р°
 *    СЂРёСЃСѓРµС‚СЃСЏ С„РёРєСЃРёСЂРѕРІР°РЅРЅРѕР№ РїРѕРІРµСЂС… РєРѕРЅС‚РµРЅС‚Р°.
 *
 * РСЃРїРѕР»СЊР·РѕРІР°РЅРёРµ:
 *   import CustomScrollbar from '../Utils/CustomScrollbar.js';
 *
 *   const cs = new CustomScrollbar(el, {
 *       visibility: 'always', // 'always' | 'hover' | 'auto'
 *       idleDelay: 900,       // РјСЃ РґР»СЏ visibility: 'auto'
 *       page: false,          // СЃС‚СЂР°РЅРёС‡РЅС‹Р№ СЂРµР¶РёРј
 *   });
 *   cs.update();   // РїРµСЂРµСЃС‡С‘С‚ (РѕР±С‹С‡РЅРѕ РЅРµ РЅСѓР¶РµРЅ, РІСЃС‘ СЃР°РјРѕ)
 *   cs.destroy();  // РїРѕР»РЅС‹Р№ РґРµРјРѕРЅС‚Р°Р¶ c РІРѕСЃСЃС‚Р°РЅРѕРІР»РµРЅРёРµРј РёСЃС…РѕРґРЅРѕРіРѕ DOM
 */
window.CustomScrollbar = class CustomScrollbar {
    constructor(el, { minThumbSize = 24, visibility = 'always', idleDelay = 900, debug = false, page = false } = {}) {
        this.el = null;
        this.scroller = null;
        this.track = null;
        this.thumb = null;
        this.valid = false;
        this.pageMode = false;
        // Р›РѕРіРё: РѕРїС†РёСЏ debug РёР»Рё window.__CS_DEBUG = true РІ РєРѕРЅСЃРѕР»Рё Р±СЂР°СѓР·РµСЂР°.
        this.debug = debug === true || (typeof window !== 'undefined' && window.__CS_DEBUG === true);

        /* ================= РЎРўР РђРќРР§РќР«Р™ Р Р•Р–РРњ ================= */
        const isPageTarget = typeof document !== 'undefined'
            && (el === document.body || el === document.documentElement || el === document.scrollingElement);

        if (page === true || isPageTarget) {
            if (!document?.documentElement || !document.body) {
                return this._log('init FAIL (page): no document');
            }
            if (document.body.__customScrollbar) {
                return this._log('init FAIL (page): already initialized');
            }

            this.el = el instanceof HTMLElement ? el : document.body;
            this.scroller = document.scrollingElement || document.documentElement;
            this.pageMode = true; // Р±РµР· СЌС‚РѕРіРѕ _metrics/destroy СѓС…РѕРґСЏС‚ РІ РєРѕРЅС‚РµР№РЅРµСЂРЅСѓСЋ РІРµС‚РєСѓ
            this.minThumbSize = minThumbSize;
            this.visibility = ['always', 'hover', 'auto'].includes(visibility) ? visibility : 'always';
            this.idleDelay = Math.max(0, idleDelay);
            this.dragging = false;
            this._idleTimer = 0;
            this._hovered = false;
            this._focused = false;
            this.el.__customScrollbar = this;

            document.documentElement.classList.add('custom-scrollbar-page');

            this.track = document.createElement('div');
            this.track.className = `custom-scrollbar__track custom-scrollbar__track--page${this.visibility !== 'always' ? ' custom-scrollbar__track--lazy' : ''}`;
            this.thumb = document.createElement('div');
            this.thumb.className = 'custom-scrollbar__thumb';
            this.track.appendChild(this.thumb);
            this.el.appendChild(this.track);

            this._onScroll = () => this._onActivity();
            this._onResize = () => this.update();
            this._onThumbPointerDown = e => this._startDrag(e);
            this._onTrackPointerDown = e => this._jump(e);
            this._onPointerMove = e => this._drag(e);
            this._onPointerUp = () => this._endDrag();
            this._onPointerEnter = () => {
                this._hovered = true;
                this._syncVisibility();
            };
            this._onPointerLeave = () => {
                this._hovered = false;
                this._syncVisibility();
            };

            window.addEventListener('scroll', this._onScroll, { passive: true });
            window.addEventListener('resize', this._onResize);
            this.el.addEventListener('pointerenter', this._onPointerEnter);
            this.el.addEventListener('pointerleave', this._onPointerLeave);
            this.thumb.addEventListener('pointerdown', this._onThumbPointerDown);
            this.track.addEventListener('pointerdown', this._onTrackPointerDown);

            // Р’С‹СЃРѕС‚Р° РґРѕРєСѓРјРµРЅС‚Р° РјРµРЅСЏРµС‚СЃСЏ Р±РµР· resize (Vue СЂРµРЅРґРµСЂ, РєР°СЂС‚РёРЅРєРё,
            // С€СЂРёС„С‚С‹) вЂ” Р»РѕРІРёРј С‡РµСЂРµР· MutationObserver РїРѕ РїРѕРґРґРµСЂРµРІСѓ body.
            if ('MutationObserver' in window) {
                this._mutationObserver = new MutationObserver(() => this.update());
                this._mutationObserver.observe(document.body, { childList: true, subtree: true });
            }
            if (document.fonts?.ready) {
                document.fonts.ready.then(() => this.update()).catch(() => {});
            }

            this.valid = true;
            this.update();
            this._syncVisibility();
            this._log('init done (page)', { visibility: this.visibility, geometry: this._metrics() });
            return;
        }

        /* ================= Р Р•Р–РРњ РљРћРќРўР•Р™РќР•Р Рђ ================= */
        if (!(el instanceof HTMLElement)) return this._log('init FAIL: el is not HTMLElement', el);
        if (el.__customScrollbar) return this._log('init FAIL: already initialized');

        this.el = el;
        this.minThumbSize = minThumbSize;
        this.visibility = ['always', 'hover', 'auto'].includes(visibility) ? visibility : 'always';
        this.idleDelay = Math.max(0, idleDelay);
        this.dragging = false;
        this._idleTimer = 0;
        this._hovered = false;
        this._focused = false;
        el.__customScrollbar = this;
        el.setAttribute('data-custom-scrollbar', '');

        // РўСЂРµРєСѓ РЅСѓР¶РµРЅ РїРѕР·РёС†РёРѕРЅРёСЂРѕРІР°РЅРЅС‹Р№ РєРѕРЅС‚РµР№РЅРµСЂ, РЅРѕ РїСЂРёРЅСѓРґРёС‚РµР»СЊРЅС‹Р№
        // position:relative Р»РѕРјР°Р» Р±С‹ С…РѕСЃС‚С‹ СЃ position:fixed/absolute
        // (РЅР°РїСЂРёРјРµСЂ, С„РёРєСЃРёСЂРѕРІР°РЅРЅРѕРµ РјРѕР±РёР»СЊРЅРѕРµ РјРµРЅСЋ). Relative РґРѕР±Р°РІР»СЏРµРј
        // РєР»Р°СЃСЃРѕРј С‚РѕР»СЊРєРѕ СЃС‚Р°С‚РёС‡РЅС‹Рј СЌР»РµРјРµРЅС‚Р°Рј.
        if (getComputedStyle(el).position === 'static') {
            this._hostClassAdded = true;
            el.classList.add('custom-scrollbar__host');
        }

        // Р’РђР–РќРћ: track РЅРµ РјРѕР¶РµС‚ Р±С‹С‚СЊ СЂРµР±С‘РЅРєРѕРј СЃРєСЂРѕР»Р»СЏС‰РµРіРѕСЃСЏ СЌР»РµРјРµРЅС‚Р° вЂ”
        // Р°Р±СЃРѕР»СЋС‚РЅС‹Рµ РґРµС‚Рё СЃРєСЂРѕР»Р»-РєРѕРЅС‚РµР№РЅРµСЂР° РїСЂРѕРєСЂСѓС‡РёРІР°СЋС‚СЃСЏ РІРјРµСЃС‚Рµ СЃ РєРѕРЅС‚РµРЅС‚РѕРј.
        // РџРѕСЌС‚РѕРјСѓ РІРµСЃСЊ РєРѕРЅС‚РµРЅС‚ РїРµСЂРµРµР·Р¶Р°РµС‚ РІ .custom-scrollbar__wrapper,
        // Р° track СЃС‚Р°РЅРѕРІРёС‚СЃСЏ СЃРѕСЃРµРґРѕРј wrapper'Р°.
        this.scroller = document.createElement('div');
        this.scroller.className = 'custom-scrollbar__wrapper';
        while (el.firstChild) this.scroller.appendChild(el.firstChild);
        el.appendChild(this.scroller);

        this.track = document.createElement('div');
        this.track.className = `custom-scrollbar__track${this.visibility !== 'always' ? ' custom-scrollbar__track--lazy' : ''}`;
        this.thumb = document.createElement('div');
        this.thumb.className = 'custom-scrollbar__thumb';
        this.track.appendChild(this.thumb);
        el.appendChild(this.track);

        this._onScroll = () => this._onActivity();
        this._onThumbPointerDown = e => this._startDrag(e);
        this._onTrackPointerDown = e => this._jump(e);
        this._onPointerMove = e => this._drag(e);
        this._onPointerUp = () => this._endDrag();
        this._onPointerEnter = () => {
            this._hovered = true;
            this._syncVisibility();
        };
        this._onPointerLeave = () => {
            this._hovered = false;
            this._syncVisibility();
        };
        this._onFocusIn = () => {
            this._focused = true;
            this._syncVisibility();
        };
        this._onFocusOut = () => {
            this._focused = false;
            this._syncVisibility();
        };

        // РЎРєСЂРѕР»Р» вЂ” РЅР° wrapper; С…РѕРІРµСЂ/С„РѕРєСѓСЃ вЂ” РЅР° РІРёРґРёРјСѓСЋ РѕР±Р»Р°СЃС‚СЊ (el).
        this.scroller.addEventListener('scroll', this._onScroll, { passive: true });
        el.addEventListener('pointerenter', this._onPointerEnter);
        el.addEventListener('pointerleave', this._onPointerLeave);
        el.addEventListener('focusin', this._onFocusIn);
        el.addEventListener('focusout', this._onFocusOut);
        this.thumb.addEventListener('pointerdown', this._onThumbPointerDown);
        this.track.addEventListener('pointerdown', this._onTrackPointerDown);
        this._log('listeners attached');

        if ('ResizeObserver' in window) {
            // РЎР»РµРґРёРј Р·Р° С…РѕСЃС‚РѕРј, СЃРєСЂРѕР»Р»РµСЂРѕРј Рё СЃРѕРґРµСЂР¶РёРјС‹Рј: СЂР°Р·РјРµСЂС‹ РјРѕРіСѓС‚
            // РјРµРЅСЏС‚СЊСЃСЏ РЅРµР·Р°РІРёСЃРёРјРѕ (С€СЂРёС„С‚С‹, С„РёР»СЊС‚СЂР°С†РёСЏ СЃРїРёСЃРєР° Рё С‚.Рґ.).
            this._resizeObserver = new ResizeObserver(() => this.update());
            this._resizeObserver.observe(el);
            this._resizeObserver.observe(this.scroller);
        }
        if ('MutationObserver' in window) {
            this._mutationObserver = new MutationObserver(() => this._observeChildren());
            this._mutationObserver.observe(this.scroller, { childList: true, subtree: false });
        }
        if (document.fonts?.ready) {
            document.fonts.ready.then(() => this.update()).catch(() => {});
        }

        // valid РґРѕР»Р¶РµРЅ Р±С‹С‚СЊ РІС‹СЃС‚Р°РІР»РµРЅ Р”Рћ РїРµСЂРІРѕРіРѕ update(): РёРЅР°С‡Рµ СЃС‚Р°СЂС‚РѕРІС‹Р№
        // СЂР°СЃС‡С‘С‚ РїСЂРѕРїСѓСЃРєР°РµС‚СЃСЏ Рё Р±РµРіСѓРЅРѕРє РѕСЃС‚Р°С‘С‚СЃСЏ СЃ height:0 РёР· CSS.
        this.valid = true;
        this._observeChildren();
        this._syncVisibility();

        this._log('init done', {
            visibility: this.visibility,
            geometry: this._metrics(),
        });
    }

    /** РџРµСЂРµСЃС‡РёС‚Р°С‚СЊ СЂР°Р·РјРµСЂ/РїРѕР·РёС†РёСЋ Р±РµРіСѓРЅРєР°. */
    update() {
        if (!this.valid) return this._log('update SKIP: not valid');

        const metrics = this._metrics();
        if (!metrics) return;

        const scrollable = metrics.scrollHeight > metrics.clientHeight + 1;
        this.track.style.display = scrollable ? '' : 'none';
        if (!scrollable) return;

        // РЎРІРµР¶РёРµ Р·Р°РјРµСЂС‹ РЅР° РєР°Р¶РґС‹Р№ РІС‹Р·РѕРІ вЂ” РЅРёРєР°РєРёС… РєСЌС€РµР№.
        const trackHeight = this.track.clientHeight || metrics.clientHeight;
        const thumbHeight = Math.max(
            this.minThumbSize,
            Math.round((metrics.clientHeight / metrics.scrollHeight) * metrics.clientHeight),
        );
        const maxOffset = Math.max(0, trackHeight - thumbHeight);
        const maxScroll = metrics.scrollHeight - metrics.clientHeight;
        const progress = maxScroll > 0 ? metrics.scrollTop / maxScroll : 0;
        const offset = Math.round(progress * maxOffset);

        this.thumb.style.height = `${thumbHeight}px`;
        this.thumb.style.transform = `translate3d(0, ${offset}px, 0)`;
        this._log('update', { scrollTop: Math.round(metrics.scrollTop), thumbHeight, offset });
    }

    /** РџРѕРјРµРЅСЏС‚СЊ СЂРµР¶РёРј РїРѕСЏРІР»РµРЅРёСЏ РЅР° Р¶РёРІРѕРј РёРЅСЃС‚Р°РЅСЃРµ. */
    setOptions({ visibility } = {}) {
        if (!this.valid || !visibility || !['always', 'hover', 'auto'].includes(visibility)) return;
        this.visibility = visibility;
        this.track.classList.toggle('custom-scrollbar__track--lazy', visibility !== 'always');
        this._syncVisibility();
    }

    /* ---------- РІРЅСѓС‚СЂРµРЅРЅРµРµ ---------- */

    /** Р›РѕРіРё С‚РѕР»СЊРєРѕ РїСЂРё debug: РѕРїС†РёСЏ РєРѕРЅСЃС‚СЂСѓРєС‚РѕСЂР° РёР»Рё window.__CS_DEBUG = true. */
    _log(...args) {
        if (this.debug) console.log('[CustomScrollbar]', ...args);
    }

    /** Р•РґРёРЅР°СЏ С‚РѕС‡РєР° РіРµРѕРјРµС‚СЂРёРё РґР»СЏ РѕР±РѕРёС… СЂРµР¶РёРјРѕРІ. */
    _metrics() {
        const s = this.scroller;
        if (!s) return null;
        if (this.pageMode) {
            return {
                clientHeight: window.innerHeight || s.clientHeight,
                scrollHeight: s.scrollHeight,
                scrollTop: s.scrollTop,
            };
        }
        return {
            clientHeight: s.clientHeight,
            scrollHeight: s.scrollHeight,
            scrollTop: s.scrollTop,
        };
    }

    _setScroll(y) {
        if (this.pageMode) {
            window.scrollTo(0, y);
        } else {
            this.scroller.scrollTop = y;
        }
    }

    _observeChildren() {
        const count = this.scroller ? this.scroller.children.length : 0;
        if (this._resizeObserver && this.scroller) {
            Array.from(this.scroller.children).forEach(child => this._resizeObserver.observe(child));
        }
        this._log('_observeChildren', { children: count });
        this.update();
    }

    /** РЎРєСЂРѕР»Р»/РґСЂР°Рі: РїРµСЂРµСЃС‡РёС‚Р°С‚СЊ РїРѕР·РёС†РёСЋ СЃСЂР°Р·Сѓ Рё РїСЂРѕРґР»РёС‚СЊ РІРёРґРёРјРѕСЃС‚СЊ РґР»СЏ 'auto'. */
    _onActivity() {
        this.update();
        if (this.visibility === 'auto') {
            this._setVisible(true);
            clearTimeout(this._idleTimer);
            this._idleTimer = setTimeout(() => this._syncVisibility(), this.idleDelay);
        }
    }

    _syncVisibility() {
        if (!this.valid || !this.track) return;
        if (this.visibility === 'always') return this._setVisible(true);
        if (this.visibility === 'auto') {
            this._setVisible(this.dragging || this._hovered);
            return;
        }
        // 'hover': РєСѓСЂСЃРѕСЂ РЅР°Рґ РѕР±Р»Р°СЃС‚СЊСЋ РёР»Рё С„РѕРєСѓСЃ РІРЅСѓС‚СЂРё (РєР»Р°РІРёР°С‚СѓСЂР°)
        this._setVisible(this.dragging || this._hovered || this._focused);
    }

    _setVisible(show) {
        this.track.classList.toggle('is-visible', !!show);
    }

    _startDrag(e) {
        e.preventDefault();
        e.stopPropagation();
        this.dragging = true;
        this._dragStartY = e.clientY;
        this._dragStartScrollTop = this._metrics()?.scrollTop ?? 0;
        this.track.classList.add('is-active');
        this._log('drag start', { y: e.clientY, scrollTop: this._dragStartScrollTop });

        try {
            this.thumb.setPointerCapture(e.pointerId);
        } catch {
            /* СѓРєР°Р·Р°С‚РµР»СЊ СѓР¶Рµ СѓС€С‘Р» вЂ” РЅРµ РєСЂРёС‚РёС‡РЅРѕ */
        }
        this.thumb.addEventListener('pointermove', this._onPointerMove);
        this.thumb.addEventListener('pointerup', this._onPointerUp);
        this.thumb.addEventListener('pointercancel', this._onPointerUp);
        this._syncVisibility();
    }

    _drag(e) {
        if (!this.dragging) return;
        const metrics = this._metrics();
        const maxScroll = metrics.scrollHeight - metrics.clientHeight;
        const trackTravel = this.track.clientHeight - this.thumb.offsetHeight;
        if (maxScroll <= 0 || trackTravel <= 0) return;

        const dy = e.clientY - this._dragStartY;
        this._setScroll(this._dragStartScrollTop + dy * (maxScroll / trackTravel));
    }

    _endDrag() {
        this.dragging = false;
        this.track.classList.remove('is-active');
        this.thumb.removeEventListener('pointermove', this._onPointerMove);
        this.thumb.removeEventListener('pointerup', this._onPointerUp);
        this.thumb.removeEventListener('pointercancel', this._onPointerUp);
        this._syncVisibility();
    }

    _jump(e) {
        if (e.target === this.thumb) return;
        const rect = this.track.getBoundingClientRect();
        const y = e.clientY - rect.top - this.thumb.offsetHeight / 2;
        const travel = rect.height - this.thumb.offsetHeight;
        const progress = travel > 0 ? Math.min(1, Math.max(0, y / travel)) : 0;
        const metrics = this._metrics();
        const maxScroll = metrics.scrollHeight - metrics.clientHeight;
        this._setScroll(progress * maxScroll);
        this._onActivity();
    }

    destroy() {
        if (!this.el) return;
        this._log('destroy');
        clearTimeout(this._idleTimer);

        if (this.pageMode) {
            window.removeEventListener('scroll', this._onScroll);
            window.removeEventListener('resize', this._onResize);
            this._mutationObserver?.disconnect();
            this.el.removeEventListener('pointerenter', this._onPointerEnter);
            this.el.removeEventListener('pointerleave', this._onPointerLeave);
            this.thumb?.removeEventListener('pointerdown', this._onThumbPointerDown);
            this.track?.removeEventListener('pointerdown', this._onTrackPointerDown);
            this._endDrag();
            this.track?.remove();
            document.documentElement.classList.remove('custom-scrollbar-page');
            delete this.el.__customScrollbar;
            this.el = null;
            this.valid = false;
            return;
        }

        this._resizeObserver?.disconnect();
        this._mutationObserver?.disconnect();
        this.scroller.removeEventListener('scroll', this._onScroll);
        this.el.removeEventListener('pointerenter', this._onPointerEnter);
        this.el.removeEventListener('pointerleave', this._onPointerLeave);
        this.el.removeEventListener('focusin', this._onFocusIn);
        this.el.removeEventListener('focusout', this._onFocusOut);
        this.thumb?.removeEventListener('pointerdown', this._onThumbPointerDown);
        this.track?.removeEventListener('pointerdown', this._onTrackPointerDown);
        this._endDrag();
        this.track?.remove();
        // Р’РѕСЃСЃС‚Р°РЅР°РІР»РёРІР°РµРј РёСЃС…РѕРґРЅС‹Р№ DOM: РєРѕРЅС‚РµРЅС‚ РІРѕР·РІСЂР°С‰Р°РµРј РёР· wrapper'Р°.
        while (this.scroller.firstChild) this.el.appendChild(this.scroller.firstChild);
        this.scroller.remove();
        if (this._hostClassAdded) this.el.classList.remove('custom-scrollbar__host');
        delete this.el.__customScrollbar;
        this.el.removeAttribute('data-custom-scrollbar');
        this.el = null;
        this.valid = false;
    }
}

