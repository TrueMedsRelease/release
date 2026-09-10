/**
 * Селект языка 1:1 с customerportal:
 * assets/Components/LanguageSelect.vue + AppSelect.vue (header/login вариант).
 *
 * Структура (.select > .select__body > .select__title + .select__options),
 * раскрытие вверх (_drop-up) при нехватке места и CustomScrollbar
 * (visibility hover) на списке опций — как в AppSelect.
 */
(function () {
    'use strict';

    window.cabinetLangSelect = function () {
        return {
            langOpen: false,
            dropUp: false,

            toggle() {
                this.langOpen = !this.langOpen;
                if (this.langOpen) {
                    var self = this;
                    this.$nextTick(function () {
                        self.mountScroll();
                        self.measure();
                    });
                }
            },

            close() {
                this.langOpen = false;
            },

            mountScroll() {
                if (!window.CustomScrollbar) return;
                var el = this.$refs && this.$refs.scroll;
                if (!el) return;
                if (el.__customScrollbar) {
                    el.__customScrollbar.update();
                    return;
                }
                var inst = new CustomScrollbar(el, { visibility: 'hover' });
                if (!inst.valid) {
                    try { inst.destroy(); } catch (e) { /* noop */ }
                }
            },

            measure() {
                var body = this.$refs && this.$refs.body;
                var panel = this.$refs && this.$refs.panel;
                if (!body || !panel) return;
                var rect = body.getBoundingClientRect();
                var panelHeight = panel.getBoundingClientRect().height;
                var spaceBelow = window.innerHeight - rect.bottom;
                var spaceAbove = rect.top;
                this.dropUp = spaceBelow < panelHeight + 12 && spaceAbove > spaceBelow;
            },
        };
    };

    function initPageScrollbar() {
        if (!window.CustomScrollbar || document.body.__customScrollbar) return;
        try {
            var inst = new CustomScrollbar(document.body, { page: true, visibility: 'auto' });
            window.__pageScrollbar = inst.valid ? inst : null;
        } catch (e) {
            window.__pageScrollbar = null;
        }
    }

    // Кастомный скроллбар всей страницы вместо системного (app.js 1:1).
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPageScrollbar, { once: true });
    } else {
        initPageScrollbar();
    }
})();
