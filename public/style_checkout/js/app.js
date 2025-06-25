(() => {
    var __webpack_modules__ = {
        807: module => {
            var canUseDOM = !!(typeof window !== "undefined" && window.document && window.document.createElement);
            module.exports = canUseDOM;
        }
    };
    var __webpack_module_cache__ = {};
    function __webpack_require__(moduleId) {
        var cachedModule = __webpack_module_cache__[moduleId];
        if (cachedModule !== void 0) return cachedModule.exports;
        var module = __webpack_module_cache__[moduleId] = {
            exports: {}
        };
        __webpack_modules__[moduleId](module, module.exports, __webpack_require__);
        return module.exports;
    }
    (() => {
        "use strict";
        const modules_flsModules = {};
        function isWebp() {
            function testWebP(callback) {
                let webP = new Image;
                webP.onload = webP.onerror = function () {
                    callback(webP.height == 2);
                };
                webP.src = "data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA";
            }
            testWebP((function (support) {
                let className = support === true ? "webp" : "no-webp";
                document.documentElement.classList.add(className);
            }));
        }
        var _slideUp = (target, duration = 500, showmore = 0) => {
            // if (!target.classList.contains("_slide")) {
            // target.classList.add("_slide");
            target.style.transitionProperty = "height, margin, padding";
            target.style.transitionDuration = duration + "ms";
            target.style.height = `${target.offsetHeight}px`;
            target.offsetHeight;
            target.style.overflow = "hidden";
            target.style.height = showmore ? `${showmore}px` : `0px`;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            window.setTimeout((() => {
                target.hidden = !showmore ? true : false;
                !showmore ? target.style.removeProperty("height") : null;
                target.style.removeProperty("padding-top");
                target.style.removeProperty("padding-bottom");
                target.style.removeProperty("margin-top");
                target.style.removeProperty("margin-bottom");
                !showmore ? target.style.removeProperty("overflow") : null;
                target.style.removeProperty("transition-duration");
                target.style.removeProperty("transition-property");
                target.classList.remove("_slide");
                document.dispatchEvent(new CustomEvent("slideUpDone", {
                    detail: {
                        target
                    }
                }));
            }), duration);
            // }
        };
        var _slideDown = (target, duration = 500, showmore = 0) => {
            if (!target.classList.contains("_slide")) {
                target.classList.add("_slide");
                target.hidden = target.hidden ? false : null;
                showmore ? target.style.removeProperty("height") : null;
                let height = target.offsetHeight;
                target.style.overflow = "hidden";
                target.style.height = showmore ? `${showmore}px` : `0px`;
                target.style.paddingTop = 0;
                target.style.paddingBottom = 0;
                target.style.marginTop = 0;
                target.style.marginBottom = 0;
                target.offsetHeight;
                target.style.transitionProperty = "height, margin, padding";
                target.style.transitionDuration = duration + "ms";
                target.style.height = height + "px";
                target.style.removeProperty("padding-top");
                target.style.removeProperty("padding-bottom");
                target.style.removeProperty("margin-top");
                target.style.removeProperty("margin-bottom");
                window.setTimeout((() => {
                    target.style.removeProperty("height");
                    target.style.removeProperty("overflow");
                    target.style.removeProperty("transition-duration");
                    target.style.removeProperty("transition-property");
                    target.classList.remove("_slide");
                    document.dispatchEvent(new CustomEvent("slideDownDone", {
                        detail: {
                            target
                        }
                    }));
                }), duration);
            }
        };
        let _slideToggle = (target, duration = 500) => {
            if (target.hidden) return _slideDown(target, duration); else return _slideUp(target, duration);
        };
        let bodyLockStatus = true;
        let bodyUnlock = (delay = 500) => {
            let body = document.querySelector("body");
            if (bodyLockStatus) {
                let lock_padding = document.querySelectorAll("[data-lp]");
                setTimeout((() => {
                    for (let index = 0; index < lock_padding.length; index++) {
                        const el = lock_padding[index];
                        el.style.paddingRight = "0px";
                    }
                    body.style.paddingRight = "0px";
                    document.documentElement.classList.remove("lock");
                }), delay);
                bodyLockStatus = false;
                setTimeout((function () {
                    bodyLockStatus = true;
                }), delay);
            }
        };
        let bodyLock = (delay = 500) => {
            let body = document.querySelector("body");
            if (bodyLockStatus) {
                let lock_padding = document.querySelectorAll("[data-lp]");
                for (let index = 0; index < lock_padding.length; index++) {
                    const el = lock_padding[index];
                    el.style.paddingRight = window.innerWidth - document.querySelector(".wrapper").offsetWidth + "px";
                }
                body.style.paddingRight = window.innerWidth - document.querySelector(".wrapper").offsetWidth + "px";
                document.documentElement.classList.add("lock");
                bodyLockStatus = false;
                setTimeout((function () {
                    bodyLockStatus = true;
                }), delay);
            }
        };
        function functions_FLS(message) {
            setTimeout((() => {
                if (window.FLS) console.log(message);
            }), 0);
        }
        class Popup {
            constructor(options) {
                let config = {
                    logging: true,
                    init: true,
                    attributeOpenButton: "data-popup",
                    attributeCloseButton: "data-close",
                    fixElementSelector: "[data-lp]",
                    youtubeAttribute: "data-popup-youtube",
                    youtubePlaceAttribute: "data-popup-youtube-place",
                    setAutoplayYoutube: true,
                    classes: {
                        popup: "popup",
                        popupContent: "popup__content",
                        popupActive: "popup_show",
                        bodyActive: "popup-show"
                    },
                    focusCatch: true,
                    closeEsc: true,
                    bodyLock: true,
                    hashSettings: {
                        location: true,
                        goHash: true
                    },
                    on: {
                        beforeOpen: function () { },
                        afterOpen: function () { },
                        beforeClose: function () { },
                        afterClose: function () { }
                    }
                };
                this.youTubeCode;
                this.isOpen = false;
                this.targetOpen = {
                    selector: false,
                    element: false
                };
                this.previousOpen = {
                    selector: false,
                    element: false
                };
                this.lastClosed = {
                    selector: false,
                    element: false
                };
                this._dataValue = false;
                this.hash = false;
                this._reopen = false;
                this._selectorOpen = false;
                this.lastFocusEl = false;
                this._focusEl = ["a[href]", 'input:not([disabled]):not([type="hidden"]):not([aria-hidden])', "button:not([disabled]):not([aria-hidden])", "select:not([disabled]):not([aria-hidden])", "textarea:not([disabled]):not([aria-hidden])", "area[href]", "iframe", "object", "embed", "[contenteditable]", '[tabindex]:not([tabindex^="-"])'];
                this.options = {
                    ...config,
                    ...options,
                    classes: {
                        ...config.classes,
                        ...options?.classes
                    },
                    hashSettings: {
                        ...config.hashSettings,
                        ...options?.hashSettings
                    },
                    on: {
                        ...config.on,
                        ...options?.on
                    }
                };
                this.bodyLock = false;
                this.options.init ? this.initPopups() : null;
            }
            initPopups() {
                //this.popupLogging(`Проснулся`);
                this.eventsPopup();
            }
            eventsPopup() {
                document.addEventListener("click", function (e) {
                    const buttonOpen = e.target.closest(`[${this.options.attributeOpenButton}]`);
                    if (buttonOpen) {
                        e.preventDefault();
                        this._dataValue = buttonOpen.getAttribute(this.options.attributeOpenButton) ? buttonOpen.getAttribute(this.options.attributeOpenButton) : "error";
                        this.youTubeCode = buttonOpen.getAttribute(this.options.youtubeAttribute) ? buttonOpen.getAttribute(this.options.youtubeAttribute) : null;
                        if (this._dataValue !== "error") {
                            if (!this.isOpen) this.lastFocusEl = buttonOpen;
                            this.targetOpen.selector = `${this._dataValue}`;
                            this._selectorOpen = true;
                            this.open();
                            return;
                        } //else this.popupLogging(`Ой ой, не заполнен атрибут у ${buttonOpen.classList}`);
                        return;
                    }
                    const buttonClose = e.target.closest(`[${this.options.attributeCloseButton}]`);
                    if (buttonClose || !e.target.closest(`.${this.options.classes.popupContent}`) && this.isOpen) {
                        e.preventDefault();
                        this.close();
                        return;
                    }
                }.bind(this));
                document.addEventListener("keydown", function (e) {
                    if (this.options.closeEsc && e.which == 27 && e.code === "Escape" && this.isOpen) {
                        e.preventDefault();
                        this.close();
                        return;
                    }
                    if (this.options.focusCatch && e.which == 9 && this.isOpen) {
                        this._focusCatch(e);
                        return;
                    }
                }.bind(this));
                if (this.options.hashSettings.goHash) {
                    window.addEventListener("hashchange", function () {
                        if (window.location.hash) this._openToHash(); else this.close(this.targetOpen.selector);
                    }.bind(this));
                    window.addEventListener("load", function () {
                        if (window.location.hash) this._openToHash();
                    }.bind(this));
                }
            }
            open(selectorValue) {
                if (bodyLockStatus) {
                    this.bodyLock = document.documentElement.classList.contains("lock") && !this.isOpen ? true : false;
                    if (selectorValue && typeof selectorValue === "string" && selectorValue.trim() !== "") {
                        this.targetOpen.selector = selectorValue;
                        this._selectorOpen = true;
                    }
                    if (this.isOpen) {
                        this._reopen = true;
                        this.close();
                    }
                    if (!this._selectorOpen) this.targetOpen.selector = this.lastClosed.selector;
                    if (!this._reopen) this.previousActiveElement = document.activeElement;
                    this.targetOpen.element = document.querySelector(this.targetOpen.selector);
                    if (this.targetOpen.element) {
                        if (this.youTubeCode) {
                            const codeVideo = this.youTubeCode;
                            const urlVideo = `//www.youtube.com/embed/${codeVideo}?rel=0&showinfo=0&autoplay=1`;
                            const iframe = document.createElement("iframe");
                            iframe.setAttribute("allowfullscreen", "");
                            const autoplay = this.options.setAutoplayYoutube ? "autoplay;" : "";
                            iframe.setAttribute("allow", `${autoplay}; encrypted-media`);
                            iframe.setAttribute("src", urlVideo);
                            if (!this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`)) {
                                this.targetOpen.element.querySelector(".popup__text").setAttribute(`${this.options.youtubePlaceAttribute}`, "");
                            }
                            this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`).appendChild(iframe);
                        }
                        if (this.options.hashSettings.location) {
                            this._getHash();
                            this._setHash();
                        }
                        this.options.on.beforeOpen(this);
                        document.dispatchEvent(new CustomEvent("beforePopupOpen", {
                            detail: {
                                popup: this
                            }
                        }));
                        this.targetOpen.element.classList.add(this.options.classes.popupActive);
                        document.documentElement.classList.add(this.options.classes.bodyActive);
                        if (!this._reopen) !this.bodyLock ? bodyLock() : null; else this._reopen = false;
                        this.targetOpen.element.setAttribute("aria-hidden", "false");
                        this.previousOpen.selector = this.targetOpen.selector;
                        this.previousOpen.element = this.targetOpen.element;
                        this._selectorOpen = false;
                        this.isOpen = true;
                        setTimeout((() => {
                            this._focusTrap();
                        }), 50);
                        this.options.on.afterOpen(this);
                        document.dispatchEvent(new CustomEvent("afterPopupOpen", {
                            detail: {
                                popup: this
                            }
                        }));
                        //this.popupLogging(`Открыл попап`);
                    } //else this.popupLogging(`Ой ой, такого попапа нет.Проверьте корректность ввода. `);
                }
            }
            close(selectorValue) {
                if (selectorValue && typeof selectorValue === "string" && selectorValue.trim() !== "") this.previousOpen.selector = selectorValue;
                if (!this.isOpen || !bodyLockStatus) return;
                this.options.on.beforeClose(this);
                document.dispatchEvent(new CustomEvent("beforePopupClose", {
                    detail: {
                        popup: this
                    }
                }));
                if (this.youTubeCode) if (this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`)) this.targetOpen.element.querySelector(`[${this.options.youtubePlaceAttribute}]`).innerHTML = "";
                this.previousOpen.element.classList.remove(this.options.classes.popupActive);
                this.previousOpen.element.setAttribute("aria-hidden", "true");
                if (!this._reopen) {
                    document.documentElement.classList.remove(this.options.classes.bodyActive);
                    !this.bodyLock ? bodyUnlock() : null;
                    this.isOpen = false;
                }
                this._removeHash();
                if (this._selectorOpen) {
                    this.lastClosed.selector = this.previousOpen.selector;
                    this.lastClosed.element = this.previousOpen.element;
                }
                this.options.on.afterClose(this);
                document.dispatchEvent(new CustomEvent("afterPopupClose", {
                    detail: {
                        popup: this
                    }
                }));
                setTimeout((() => {
                    this._focusTrap();
                }), 50);
                //this.popupLogging(`Закрыл попап`);
            }
            _getHash() {
                if (this.options.hashSettings.location) this.hash = this.targetOpen.selector.includes("#") ? this.targetOpen.selector : this.targetOpen.selector.replace(".", "#");
            }
            _openToHash() {
                let classInHash = document.querySelector(`.${window.location.hash.replace("#", "")}`) ? `.${window.location.hash.replace("#", "")}` : document.querySelector(`${window.location.hash}`) ? `${window.location.hash}` : null;
                const buttons = document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash}"]`) ? document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash}"]`) : document.querySelector(`[${this.options.attributeOpenButton} = "${classInHash.replace(".", "#")}"]`);
                if (buttons && classInHash) this.open(classInHash);
            }
            _setHash() {
                history.pushState("", "", this.hash);
            }
            _removeHash() {
                history.pushState("", "", window.location.href.split("#")[0]);
            }
            _focusCatch(e) {
                const focusable = this.targetOpen.element.querySelectorAll(this._focusEl);
                const focusArray = Array.prototype.slice.call(focusable);
                const focusedIndex = focusArray.indexOf(document.activeElement);
                if (e.shiftKey && focusedIndex === 0) {
                    focusArray[focusArray.length - 1].focus();
                    e.preventDefault();
                }
                if (!e.shiftKey && focusedIndex === focusArray.length - 1) {
                    focusArray[0].focus();
                    e.preventDefault();
                }
            }
            _focusTrap() {
                const focusable = this.previousOpen.element.querySelectorAll(this._focusEl);
                if (!this.isOpen && this.lastFocusEl) this.lastFocusEl.focus(); else focusable[0].focus();
            }
            // popupLogging(message) {
            //     this.options.logging ? functions_FLS(`[Попапос]: ${message}`) : null;
            // }
        }
        modules_flsModules.popup = new Popup({});
        let formValidate = {
            getErrors(form) {
                let error = 0;
                let formRequiredItems = form.querySelectorAll("*[data-required]");
                if (formRequiredItems.length) formRequiredItems.forEach((formRequiredItem => {
                    if ((formRequiredItem.offsetParent !== null || formRequiredItem.tagName === "SELECT") && !formRequiredItem.disabled) error += this.validateInput(formRequiredItem);
                }));
                return error;
            },
            validateInput(formRequiredItem) {
                let error = 0;
                if (formRequiredItem.dataset.required === "email") {
                    formRequiredItem.value = formRequiredItem.value.replace(" ", "");
                    if (this.emailTest(formRequiredItem)) {
                        this.addError(formRequiredItem);
                        error++;
                    } else this.removeError(formRequiredItem);
                } else if (formRequiredItem.type === "checkbox" && !formRequiredItem.checked) {
                    this.addError(formRequiredItem);
                    error++;
                } else if (!formRequiredItem.value.trim()) {
                    this.addError(formRequiredItem);
                    error++;
                } else this.removeError(formRequiredItem);
                return error;
            },
            addError(formRequiredItem) {
                formRequiredItem.classList.add("_form-error");
                formRequiredItem.parentElement.classList.add("_form-error");
                let inputError = formRequiredItem.parentElement.querySelector(".form__error");
                if (inputError) formRequiredItem.parentElement.removeChild(inputError);
                if (formRequiredItem.dataset.error) formRequiredItem.parentElement.insertAdjacentHTML("beforeend", `<div class="form__error">${formRequiredItem.dataset.error}</div>`);
            },
            removeError(formRequiredItem) {
                formRequiredItem.classList.remove("_form-error");
                formRequiredItem.parentElement.classList.remove("_form-error");
                if (formRequiredItem.parentElement.querySelector(".form__error")) formRequiredItem.parentElement.removeChild(formRequiredItem.parentElement.querySelector(".form__error"));
            },
            formClean(form) {
                form.reset();
                setTimeout((() => {
                    let inputs = form.querySelectorAll("input,textarea");
                    for (let index = 0; index < inputs.length; index++) {
                        const el = inputs[index];
                        el.parentElement.classList.remove("_form-focus");
                        el.classList.remove("_form-focus");
                        formValidate.removeError(el);
                    }
                    let checkboxes = form.querySelectorAll(".checkbox__input");
                    if (checkboxes.length > 0) for (let index = 0; index < checkboxes.length; index++) {
                        const checkbox = checkboxes[index];
                        checkbox.checked = false;
                    }
                    if (modules_flsModules.select) {
                        let selects = form.querySelectorAll(".select");
                        if (selects.length) for (let index = 0; index < selects.length; index++) {
                            const select = selects[index].querySelector("select");
                            modules_flsModules.select.selectBuild(select);
                        }
                    }
                }), 0);
            },
            emailTest(formRequiredItem) {
                return !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,8})+$/.test(formRequiredItem.value);
            }
        };
        class SelectConstructor {
            constructor(props, data = null) {
                let defaultConfig = {
                    init: true,
                    logging: true
                };
                this.config = Object.assign(defaultConfig, props);
                this.selectClasses = {
                    classSelect: "select",
                    classSelectBody: "select__body",
                    classSelectTitle: "select__title",
                    classSelectValue: "select__value",
                    classSelectLabel: "select__label",
                    classSelectInput: "select__input",
                    classSelectText: "select__text",
                    classSelectLink: "select__link",
                    classSelectOptions: "select__options",
                    classSelectOptionsScroll: "select__scroll",
                    classSelectOption: "select__option",
                    classSelectContent: "select__content",
                    classSelectRow: "select__row",
                    classSelectData: "select__asset",
                    classSelectDisabled: "_select-disabled",
                    classSelectTag: "_select-tag",
                    classSelectOpen: "_select-open",
                    classSelectActive: "_select-active",
                    classSelectFocus: "_select-focus",
                    classSelectMultiple: "_select-multiple",
                    classSelectCheckBox: "_select-checkbox",
                    classSelectOptionSelected: "_select-selected",
                    classSelectPseudoLabel: "select__pseudo-label"
                };
                this._this = this;
                if (this.config.init) {
                    const selectItems = data ? document.querySelectorAll(data) : document.querySelectorAll("select");
                    if (selectItems.length) {
                        this.selectsInit(selectItems);
                        //this.setLogging(`Проснулся, построил селектов: (${selectItems.length})`);
                    }
                }
            }
            getSelectClass(className) {
                return `.${className}`;
            }
            getSelectElement(selectItem, className) {
                return {
                    originalSelect: selectItem.querySelector("select"),
                    selectElement: selectItem.querySelector(this.getSelectClass(className))
                };
            }
            selectsInit(selectItems) {
                selectItems.forEach(((originalSelect, index) => {
                    this.selectInit(originalSelect, index + 1);
                }));
                document.addEventListener("click", function (e) {
                    this.selectsActions(e);
                }.bind(this));
                document.addEventListener("keydown", function (e) {
                    this.selectsActions(e);
                }.bind(this));
                document.addEventListener("focusin", function (e) {
                    this.selectsActions(e);
                }.bind(this));
                document.addEventListener("focusout", function (e) {
                    this.selectsActions(e);
                }.bind(this));
            }
            selectInit(originalSelect, index) {
                const _this = this;
                let selectItem = document.createElement("div");
                selectItem.classList.add(this.selectClasses.classSelect);
                originalSelect.parentNode.insertBefore(selectItem, originalSelect);
                selectItem.appendChild(originalSelect);
                originalSelect.hidden = true;
                index ? originalSelect.dataset.id = index : null;
                if (this.getSelectPlaceholder(originalSelect)) {
                    originalSelect.dataset.placeholder = this.getSelectPlaceholder(originalSelect).value;
                    if (this.getSelectPlaceholder(originalSelect).label.show) {
                        const selectItemTitle = this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement;
                        selectItemTitle.insertAdjacentHTML("afterbegin", `<span class="${this.selectClasses.classSelectLabel}">${this.getSelectPlaceholder(originalSelect).label.text ? this.getSelectPlaceholder(originalSelect).label.text : this.getSelectPlaceholder(originalSelect).value}</span>`);
                    }
                }
                selectItem.insertAdjacentHTML("beforeend", `<div class="${this.selectClasses.classSelectBody}"><div hidden class="${this.selectClasses.classSelectOptions}"></div></div>`);
                this.selectBuild(originalSelect);
                originalSelect.dataset.speed = originalSelect.dataset.speed ? originalSelect.dataset.speed : "150";
                originalSelect.addEventListener("change", (function (e) {
                    _this.selectChange(e);
                }));
            }
            selectBuild(originalSelect) {
                const selectItem = originalSelect.parentElement;
                selectItem.dataset.id = originalSelect.dataset.id;
                originalSelect.dataset.classModif ? selectItem.classList.add(`select_${originalSelect.dataset.classModif}`) : null;
                originalSelect.multiple ? selectItem.classList.add(this.selectClasses.classSelectMultiple) : selectItem.classList.remove(this.selectClasses.classSelectMultiple);
                originalSelect.hasAttribute("data-checkbox") && originalSelect.multiple ? selectItem.classList.add(this.selectClasses.classSelectCheckBox) : selectItem.classList.remove(this.selectClasses.classSelectCheckBox);
                this.setSelectTitleValue(selectItem, originalSelect);
                this.setOptions(selectItem, originalSelect);
                originalSelect.hasAttribute("data-search") ? this.searchActions(selectItem) : null;
                originalSelect.hasAttribute("data-open") ? this.selectAction(selectItem) : null;
                this.selectDisabled(selectItem, originalSelect);
            }
            selectsActions(e) {
                const targetElement = e.target;
                const targetType = e.type;
                if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelect)) || targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag))) {
                    const selectItem = targetElement.closest(".select") ? targetElement.closest(".select") : document.querySelector(`.${this.selectClasses.classSelect}[data-id="${targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag)).dataset.selectId}"]`);
                    const originalSelect = this.getSelectElement(selectItem).originalSelect;
                    if (targetType === "click") {
                        if (!originalSelect.disabled) if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag))) {
                            const targetTag = targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTag));
                            const optionItem = document.querySelector(`.${this.selectClasses.classSelect}[data-id="${targetTag.dataset.selectId}"] .select__option[data-value="${targetTag.dataset.value}"]`);
                            this.optionAction(selectItem, originalSelect, optionItem);
                        } else if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectTitle))) this.selectAction(selectItem); else if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelectOption))) {
                            const optionItem = targetElement.closest(this.getSelectClass(this.selectClasses.classSelectOption));
                            this.optionAction(selectItem, originalSelect, optionItem);
                        }
                    } else if (targetType === "focusin" || targetType === "focusout") {
                        if (targetElement.closest(this.getSelectClass(this.selectClasses.classSelect))) targetType === "focusin" ? selectItem.classList.add(this.selectClasses.classSelectFocus) : selectItem.classList.remove(this.selectClasses.classSelectFocus);
                    } else if (targetType === "keydown" && e.code === "Escape") this.selectsСlose();
                } else this.selectsСlose();
            }
            selectsСlose(selectOneGroup) {
                const selectsGroup = selectOneGroup ? selectOneGroup : document;
                const selectActiveItems = selectsGroup.querySelectorAll(`${this.getSelectClass(this.selectClasses.classSelect)}${this.getSelectClass(this.selectClasses.classSelectOpen)}`);
                if (selectActiveItems.length) selectActiveItems.forEach((selectActiveItem => {
                    this.selectСlose(selectActiveItem);
                }));
            }
            selectСlose(selectItem) {
                const originalSelect = this.getSelectElement(selectItem).originalSelect;
                const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
                if (!selectOptions.classList.contains("_slide")) {
                    selectItem.classList.remove(this.selectClasses.classSelectOpen);
                    _slideUp(selectOptions, originalSelect.dataset.speed);
                }
            }
            selectAction(selectItem) {
                const originalSelect = this.getSelectElement(selectItem).originalSelect;
                const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
                if (originalSelect.closest("[data-one-select]")) {
                    const selectOneGroup = originalSelect.closest("[data-one-select]");
                    this.selectsСlose(selectOneGroup);
                }
                if (!selectOptions.classList.contains("_slide")) {
                    selectItem.classList.toggle(this.selectClasses.classSelectOpen);
                    _slideToggle(selectOptions, originalSelect.dataset.speed);
                }
            }
            setSelectTitleValue(selectItem, originalSelect) {
                const selectItemBody = this.getSelectElement(selectItem, this.selectClasses.classSelectBody).selectElement;
                const selectItemTitle = this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement;
                if (selectItemTitle) selectItemTitle.remove();
                selectItemBody.insertAdjacentHTML("afterbegin", this.getSelectTitleValue(selectItem, originalSelect));
            }
            getSelectTitleValue(selectItem, originalSelect) {
                let selectTitleValue = this.getSelectedOptionsData(originalSelect, 2).html;
                if (originalSelect.multiple && originalSelect.hasAttribute("data-tags")) {
                    selectTitleValue = this.getSelectedOptionsData(originalSelect).elements.map((option => `<span role="button" data-select-id="${selectItem.dataset.id}" data-value="${option.value}" class="_select-tag">${this.getSelectElementContent(option)}</span>`)).join("");
                    if (originalSelect.dataset.tags && document.querySelector(originalSelect.dataset.tags)) {
                        document.querySelector(originalSelect.dataset.tags).innerHTML = selectTitleValue;
                        if (originalSelect.hasAttribute("data-search")) selectTitleValue = false;
                    }
                }
                selectTitleValue = selectTitleValue.length ? selectTitleValue : originalSelect.dataset.placeholder ? originalSelect.dataset.placeholder : "";
                let pseudoAttribute = "";
                let pseudoAttributeClass = "";
                if (originalSelect.hasAttribute("data-pseudo-label")) {
                    pseudoAttributeClass = ` ${this.selectClasses.classSelectPseudoLabel}`;
                    pseudoAttribute = originalSelect.dataset.pseudoLabel ? `<span class="${pseudoAttributeClass}" style="z-index:100;">${originalSelect.dataset.pseudoLabel}</span>` : ``;
                }
                this.getSelectedOptionsData(originalSelect).values.length ? selectItem.classList.add(this.selectClasses.classSelectActive) : selectItem.classList.remove(this.selectClasses.classSelectActive);
                if (originalSelect.hasAttribute("data-search")) return `<div class="${this.selectClasses.classSelectTitle}"><span${pseudoAttribute} class="${this.selectClasses.classSelectValue}"><input autocomplete="off" type="text" placeholder="${selectTitleValue}" data-placeholder="${selectTitleValue}" class="${this.selectClasses.classSelectInput}"></span></div>`; else {
                    const customClass = this.getSelectedOptionsData(originalSelect).elements.length && this.getSelectedOptionsData(originalSelect).elements[0].dataset.class ? ` ${this.getSelectedOptionsData(originalSelect).elements[0].dataset.class}` : "";
                    return `\n\t\t\t<button type="button" class="${this.selectClasses.classSelectTitle}">\n\t\t\t\t${pseudoAttribute}\n\t\t\t\t<span class="${this.selectClasses.classSelectValue}">\n\t\t\t\t\t<span class="${this.selectClasses.classSelectContent}${customClass}">${selectTitleValue}</span>\n\t\t\t\t\t<span class="select__icon"> \n\t\t\t\t\t\t<svg width="12" height="6">\n\t\t\t\t\t\t\t<use xlink:href="/style_checkout/images/icons/icons.svg#svg-arr-down"></use>\n\t\t\t\t\t\t</svg>\n\t\t\t\t\t</span>\n\t\t\t\t</span>\n\t\t\t</button>`;
                }
            }
            getSelectElementContent(selectOption) {
                const selectOptionData = selectOption.dataset.asset ? `${selectOption.dataset.asset}` : "";
                const selectOptionContryCode = selectOption.dataset.country ? `${selectOption.dataset.country}` : "";
                // const selectOptionDataHTML = selectOptionData.indexOf("images") >= 0 ? `<img loading="lazy" src="${selectOptionData}" alt="">` : selectOptionData;
                const selectOptionDataHTML = selectOptionData.indexOf("images") >= 0 ? `<svg width="18px" height="18px"><use href="${selectOptionData}"></svg>` : selectOptionData;
                let selectOptionContentHTML = ``;
                selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectRow}">` : "";
                selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectData}">` : "";
                selectOptionContentHTML += selectOptionData ? selectOptionDataHTML : "";
                selectOptionContentHTML += selectOptionContryCode ? `<span>` : "";
                selectOptionContentHTML += selectOptionContryCode ? selectOptionContryCode : "";
                selectOptionContentHTML += selectOptionContryCode ? `</span>` : "";
                selectOptionContentHTML += selectOptionData ? `</span>` : "";
                selectOptionContentHTML += selectOptionData ? `<span class="${this.selectClasses.classSelectText}">` : "";
                selectOptionContentHTML += selectOption.textContent;
                selectOptionContentHTML += selectOptionData ? `</span>` : "";
                selectOptionContentHTML += selectOptionData ? `</span>` : "";
                return selectOptionContentHTML;
            }
            getSelectPlaceholder(originalSelect) {
                const selectPlaceholder = Array.from(originalSelect.options).find((option => !option.value));
                if (selectPlaceholder) return {
                    value: selectPlaceholder.textContent,
                    show: selectPlaceholder.hasAttribute("data-show"),
                    label: {
                        show: selectPlaceholder.hasAttribute("data-label"),
                        text: selectPlaceholder.dataset.label
                    }
                };
            }
            getSelectedOptionsData(originalSelect, type) {
                let selectedOptions = [];
                if (originalSelect.multiple) selectedOptions = Array.from(originalSelect.options).filter((option => option.value)).filter((option => option.selected)); else selectedOptions.push(originalSelect.options[originalSelect.selectedIndex]);
                return {
                    elements: selectedOptions.map((option => option)),
                    values: selectedOptions.filter((option => option.value)).map((option => option.value)),
                    html: selectedOptions.map((option => this.getSelectElementContent(option)))
                };
            }
            getOptions(originalSelect) {
                let selectOptionsScroll = originalSelect.hasAttribute("data-scroll") ? `data-simplebar` : "";
                let selectOptionsScrollHeight = originalSelect.dataset.scroll ? `style="max-height:${originalSelect.dataset.scroll}px"` : "";
                let selectOptions = Array.from(originalSelect.options);
                if (selectOptions.length > 0) {
                    let selectOptionsHTML = ``;
                    if (this.getSelectPlaceholder(originalSelect) && !this.getSelectPlaceholder(originalSelect).show || originalSelect.multiple) selectOptions = selectOptions.filter((option => option.value));
                    selectOptionsHTML += selectOptionsScroll ? `<div ${selectOptionsScroll} ${selectOptionsScrollHeight} class="${this.selectClasses.classSelectOptionsScroll}">` : "";
                    selectOptions.forEach((selectOption => {
                        selectOptionsHTML += this.getOption(selectOption, originalSelect);
                    }));
                    selectOptionsHTML += selectOptionsScroll ? `</div>` : "";
                    return selectOptionsHTML;
                }
            }
            getOption(selectOption, originalSelect) {
                const selectOptionSelected = selectOption.selected && originalSelect.multiple ? ` ${this.selectClasses.classSelectOptionSelected}` : "";
                const selectOptionHide = selectOption.selected && !originalSelect.hasAttribute("data-show-selected") && !originalSelect.multiple ? `hidden` : ``;
                const selectOptionClass = selectOption.dataset.class ? ` ${selectOption.dataset.class}` : "";
                const selectOptionLink = selectOption.dataset.href ? selectOption.dataset.href : false;
                const selectOptionLinkTarget = selectOption.hasAttribute("data-href-blank") ? `target="_blank"` : "";
                let selectOptionHTML = ``;
                selectOptionHTML += selectOptionLink ? `<a ${selectOptionLinkTarget} ${selectOptionHide} href="${selectOptionLink}" data-value="${selectOption.value}" class="${this.selectClasses.classSelectOption}${selectOptionClass}${selectOptionSelected}">` : `<button ${selectOptionHide} class="${this.selectClasses.classSelectOption}${selectOptionClass}${selectOptionSelected}" data-value="${selectOption.value}" type="button">`;
                selectOptionHTML += this.getSelectElementContent(selectOption);
                selectOptionHTML += selectOptionLink ? `</a>` : `</button>`;
                return selectOptionHTML;
            }
            setOptions(selectItem, originalSelect) {
                const selectItemOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
                selectItemOptions.innerHTML = this.getOptions(originalSelect);
            }
            optionAction(selectItem, originalSelect, optionItem) {
                if (originalSelect.multiple) {
                    optionItem.classList.toggle(this.selectClasses.classSelectOptionSelected);
                    const originalSelectSelectedItems = this.getSelectedOptionsData(originalSelect).elements;
                    originalSelectSelectedItems.forEach((originalSelectSelectedItem => {
                        originalSelectSelectedItem.removeAttribute("selected");
                    }));
                    const selectSelectedItems = selectItem.querySelectorAll(this.getSelectClass(this.selectClasses.classSelectOptionSelected));
                    selectSelectedItems.forEach((selectSelectedItems => {
                        originalSelect.querySelector(`option[value="${selectSelectedItems.dataset.value}"]`).setAttribute("selected", "selected");
                    }));
                } else {
                    if (!originalSelect.hasAttribute("data-show-selected")) {
                        if (selectItem.querySelector(`${this.getSelectClass(this.selectClasses.classSelectOption)}[hidden]`)) selectItem.querySelector(`${this.getSelectClass(this.selectClasses.classSelectOption)}[hidden]`).hidden = false;
                        optionItem.hidden = true;
                    }
                    originalSelect.value = optionItem.hasAttribute("data-value") ? optionItem.dataset.value : optionItem.textContent;
                    this.selectAction(selectItem);
                }
                this.setSelectTitleValue(selectItem, originalSelect);
                this.setSelectChange(originalSelect);
            }
            selectChange(e) {
                const originalSelect = e.target;
                this.selectBuild(originalSelect);
                this.setSelectChange(originalSelect);
            }
            setSelectChange(originalSelect) {
                if (originalSelect.hasAttribute("data-validate")) formValidate.validateInput(originalSelect);
                if (originalSelect.hasAttribute("data-submit") && originalSelect.value) {
                    let tempButton = document.createElement("button");
                    tempButton.type = "submit";
                    originalSelect.closest("form").append(tempButton);
                    tempButton.click();
                    tempButton.remove();
                }
                const selectItem = originalSelect.parentElement;
                this.selectCallback(selectItem, originalSelect);
            }
            selectDisabled(selectItem, originalSelect) {
                if (originalSelect.disabled) {
                    selectItem.classList.add(this.selectClasses.classSelectDisabled);
                    this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement.disabled = true;
                } else {
                    selectItem.classList.remove(this.selectClasses.classSelectDisabled);
                    this.getSelectElement(selectItem, this.selectClasses.classSelectTitle).selectElement.disabled = false;
                }
            }
            searchActions(selectItem) {
                this.getSelectElement(selectItem).originalSelect;
                const selectInput = this.getSelectElement(selectItem, this.selectClasses.classSelectInput).selectElement;
                const selectOptions = this.getSelectElement(selectItem, this.selectClasses.classSelectOptions).selectElement;
                const selectOptionsItems = selectOptions.querySelectorAll(`.${this.selectClasses.classSelectOption}`);
                const _this = this;
                selectInput.addEventListener("input", (function () {
                    selectOptionsItems.forEach((selectOptionsItem => {
                        if (selectOptionsItem.textContent.toUpperCase().indexOf(selectInput.value.toUpperCase()) >= 0) selectOptionsItem.hidden = false; else selectOptionsItem.hidden = true;
                    }));
                    selectOptions.hidden === true ? _this.selectAction(selectItem) : null;
                }));
            }
            selectCallback(selectItem, originalSelect) {
                document.dispatchEvent(new CustomEvent("selectCallback", {
                    detail: {
                        select: originalSelect
                    }
                }));
            }
            setLogging(message) {
                this.config.logging ? functions_FLS(`[select]: ${message}`) : null;
            }
        }
        modules_flsModules.select = new SelectConstructor({});
        var can_use_dom = __webpack_require__(807);
        function isObject(value) {
            var type = typeof value;
            return value != null && (type == "object" || type == "function");
        }
        const lodash_es_isObject = isObject;
        var freeGlobal = typeof global == "object" && global && global.Object === Object && global;
        const _freeGlobal = freeGlobal;
        var freeSelf = typeof self == "object" && self && self.Object === Object && self;
        var root = _freeGlobal || freeSelf || Function("return this")();
        const _root = root;
        var now = function () {
            return _root.Date.now();
        };
        const lodash_es_now = now;
        var reWhitespace = /\s/;
        function trimmedEndIndex(string) {
            var index = string.length;
            while (index-- && reWhitespace.test(string.charAt(index)));
            return index;
        }
        const _trimmedEndIndex = trimmedEndIndex;
        var reTrimStart = /^\s+/;
        function baseTrim(string) {
            return string ? string.slice(0, _trimmedEndIndex(string) + 1).replace(reTrimStart, "") : string;
        }
        const _baseTrim = baseTrim;
        var Symbol = _root.Symbol;
        const _Symbol = Symbol;
        var objectProto = Object.prototype;
        var _getRawTag_hasOwnProperty = objectProto.hasOwnProperty;
        var nativeObjectToString = objectProto.toString;
        var symToStringTag = _Symbol ? _Symbol.toStringTag : void 0;
        function getRawTag(value) {
            var isOwn = _getRawTag_hasOwnProperty.call(value, symToStringTag), tag = value[symToStringTag];
            try {
                value[symToStringTag] = void 0;
                var unmasked = true;
            } catch (e) { }
            var result = nativeObjectToString.call(value);
            if (unmasked) if (isOwn) value[symToStringTag] = tag; else delete value[symToStringTag];
            return result;
        }
        const _getRawTag = getRawTag;
        var _objectToString_objectProto = Object.prototype;
        var _objectToString_nativeObjectToString = _objectToString_objectProto.toString;
        function objectToString(value) {
            return _objectToString_nativeObjectToString.call(value);
        }
        const _objectToString = objectToString;
        var nullTag = "[object Null]", undefinedTag = "[object Undefined]";
        var _baseGetTag_symToStringTag = _Symbol ? _Symbol.toStringTag : void 0;
        function baseGetTag(value) {
            if (value == null) return value === void 0 ? undefinedTag : nullTag;
            return _baseGetTag_symToStringTag && _baseGetTag_symToStringTag in Object(value) ? _getRawTag(value) : _objectToString(value);
        }
        const _baseGetTag = baseGetTag;
        function isObjectLike(value) {
            return value != null && typeof value == "object";
        }
        const lodash_es_isObjectLike = isObjectLike;
        var symbolTag = "[object Symbol]";
        function isSymbol(value) {
            return typeof value == "symbol" || lodash_es_isObjectLike(value) && _baseGetTag(value) == symbolTag;
        }
        const lodash_es_isSymbol = isSymbol;
        var NAN = 0 / 0;
        var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;
        var reIsBinary = /^0b[01]+$/i;
        var reIsOctal = /^0o[0-7]+$/i;
        var freeParseInt = parseInt;
        function toNumber(value) {
            if (typeof value == "number") return value;
            if (lodash_es_isSymbol(value)) return NAN;
            if (lodash_es_isObject(value)) {
                var other = typeof value.valueOf == "function" ? value.valueOf() : value;
                value = lodash_es_isObject(other) ? other + "" : other;
            }
            if (typeof value != "string") return value === 0 ? value : +value;
            value = _baseTrim(value);
            var isBinary = reIsBinary.test(value);
            return isBinary || reIsOctal.test(value) ? freeParseInt(value.slice(2), isBinary ? 2 : 8) : reIsBadHex.test(value) ? NAN : +value;
        }
        const lodash_es_toNumber = toNumber;
        var FUNC_ERROR_TEXT = "Expected a function";
        var nativeMax = Math.max, nativeMin = Math.min;
        function debounce(func, wait, options) {
            var lastArgs, lastThis, maxWait, result, timerId, lastCallTime, lastInvokeTime = 0, leading = false, maxing = false, trailing = true;
            if (typeof func != "function") throw new TypeError(FUNC_ERROR_TEXT);
            wait = lodash_es_toNumber(wait) || 0;
            if (lodash_es_isObject(options)) {
                leading = !!options.leading;
                maxing = "maxWait" in options;
                maxWait = maxing ? nativeMax(lodash_es_toNumber(options.maxWait) || 0, wait) : maxWait;
                trailing = "trailing" in options ? !!options.trailing : trailing;
            }
            function invokeFunc(time) {
                var args = lastArgs, thisArg = lastThis;
                lastArgs = lastThis = void 0;
                lastInvokeTime = time;
                result = func.apply(thisArg, args);
                return result;
            }
            function leadingEdge(time) {
                lastInvokeTime = time;
                timerId = setTimeout(timerExpired, wait);
                return leading ? invokeFunc(time) : result;
            }
            function remainingWait(time) {
                var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime, timeWaiting = wait - timeSinceLastCall;
                return maxing ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
            }
            function shouldInvoke(time) {
                var timeSinceLastCall = time - lastCallTime, timeSinceLastInvoke = time - lastInvokeTime;
                return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
            }
            function timerExpired() {
                var time = lodash_es_now();
                if (shouldInvoke(time)) return trailingEdge(time);
                timerId = setTimeout(timerExpired, remainingWait(time));
            }
            function trailingEdge(time) {
                timerId = void 0;
                if (trailing && lastArgs) return invokeFunc(time);
                lastArgs = lastThis = void 0;
                return result;
            }
            function cancel() {
                if (timerId !== void 0) clearTimeout(timerId);
                lastInvokeTime = 0;
                lastArgs = lastCallTime = lastThis = timerId = void 0;
            }
            function flush() {
                return timerId === void 0 ? result : trailingEdge(lodash_es_now());
            }
            function debounced() {
                var time = lodash_es_now(), isInvoking = shouldInvoke(time);
                lastArgs = arguments;
                lastThis = this;
                lastCallTime = time;
                if (isInvoking) {
                    if (timerId === void 0) return leadingEdge(lastCallTime);
                    if (maxing) {
                        clearTimeout(timerId);
                        timerId = setTimeout(timerExpired, wait);
                        return invokeFunc(lastCallTime);
                    }
                }
                if (timerId === void 0) timerId = setTimeout(timerExpired, wait);
                return result;
            }
            debounced.cancel = cancel;
            debounced.flush = flush;
            return debounced;
        }
        const lodash_es_debounce = debounce;
        var throttle_FUNC_ERROR_TEXT = "Expected a function";
        function throttle(func, wait, options) {
            var leading = true, trailing = true;
            if (typeof func != "function") throw new TypeError(throttle_FUNC_ERROR_TEXT);
            if (lodash_es_isObject(options)) {
                leading = "leading" in options ? !!options.leading : leading;
                trailing = "trailing" in options ? !!options.trailing : trailing;
            }
            return lodash_es_debounce(func, wait, {
                leading,
                maxWait: wait,
                trailing
            });
        }
        const lodash_es_throttle = throttle;
        var __assign = function () {
            __assign = Object.assign || function __assign(t) {
                for (var s, i = 1, n = arguments.length; i < n; i++) {
                    s = arguments[i];
                    for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
                }
                return t;
            };
            return __assign.apply(this, arguments);
        };
        var cachedScrollbarWidth = null;
        var cachedDevicePixelRatio = null;
        if (can_use_dom) window.addEventListener("resize", (function () {
            if (cachedDevicePixelRatio !== window.devicePixelRatio) {
                cachedDevicePixelRatio = window.devicePixelRatio;
                cachedScrollbarWidth = null;
            }
        }));
        function scrollbarWidth() {
            if (cachedScrollbarWidth === null) {
                if (typeof document === "undefined") {
                    cachedScrollbarWidth = 0;
                    return cachedScrollbarWidth;
                }
                var body = document.body;
                var box = document.createElement("div");
                box.classList.add("simplebar-hide-scrollbar");
                body.appendChild(box);
                var width = box.getBoundingClientRect().right;
                body.removeChild(box);
                cachedScrollbarWidth = width;
            }
            return cachedScrollbarWidth;
        }
        function getElementWindow$1(element) {
            if (!element || !element.ownerDocument || !element.ownerDocument.defaultView) return window;
            return element.ownerDocument.defaultView;
        }
        function getElementDocument$1(element) {
            if (!element || !element.ownerDocument) return document;
            return element.ownerDocument;
        }
        var getOptions$1 = function (obj) {
            var initialObj = {};
            var options = Array.prototype.reduce.call(obj, (function (acc, attribute) {
                var option = attribute.name.match(/data-simplebar-(.+)/);
                if (option) {
                    var key = option[1].replace(/\W+(.)/g, (function (_, chr) {
                        return chr.toUpperCase();
                    }));
                    switch (attribute.value) {
                        case "true":
                            acc[key] = true;
                            break;

                        case "false":
                            acc[key] = false;
                            break;

                        case void 0:
                            acc[key] = true;
                            break;

                        default:
                            acc[key] = attribute.value;
                    }
                }
                return acc;
            }), initialObj);
            return options;
        };
        function addClasses$1(el, classes) {
            var _a;
            if (!el) return;
            (_a = el.classList).add.apply(_a, classes.split(" "));
        }
        function removeClasses$1(el, classes) {
            if (!el) return;
            classes.split(" ").forEach((function (className) {
                el.classList.remove(className);
            }));
        }
        function classNamesToQuery$1(classNames) {
            return ".".concat(classNames.split(" ").join("."));
        }
        var helpers = Object.freeze({
            __proto__: null,
            getElementWindow: getElementWindow$1,
            getElementDocument: getElementDocument$1,
            getOptions: getOptions$1,
            addClasses: addClasses$1,
            removeClasses: removeClasses$1,
            classNamesToQuery: classNamesToQuery$1
        });
        var getElementWindow = getElementWindow$1, getElementDocument = getElementDocument$1, getOptions = getOptions$1, addClasses = addClasses$1, dist_removeClasses = removeClasses$1, classNamesToQuery = classNamesToQuery$1;
        var SimpleBarCore = function () {
            function SimpleBarCore(element, options) {
                if (options === void 0) options = {};
                var _this = this;
                this.removePreventClickId = null;
                this.minScrollbarWidth = 20;
                this.stopScrollDelay = 175;
                this.isScrolling = false;
                this.isMouseEntering = false;
                this.scrollXTicking = false;
                this.scrollYTicking = false;
                this.wrapperEl = null;
                this.contentWrapperEl = null;
                this.contentEl = null;
                this.offsetEl = null;
                this.maskEl = null;
                this.placeholderEl = null;
                this.heightAutoObserverWrapperEl = null;
                this.heightAutoObserverEl = null;
                this.rtlHelpers = null;
                this.scrollbarWidth = 0;
                this.resizeObserver = null;
                this.mutationObserver = null;
                this.elStyles = null;
                this.isRtl = null;
                this.mouseX = 0;
                this.mouseY = 0;
                this.onMouseMove = function () { };
                this.onWindowResize = function () { };
                this.onStopScrolling = function () { };
                this.onMouseEntered = function () { };
                this.onScroll = function () {
                    var elWindow = getElementWindow(_this.el);
                    if (!_this.scrollXTicking) {
                        elWindow.requestAnimationFrame(_this.scrollX);
                        _this.scrollXTicking = true;
                    }
                    if (!_this.scrollYTicking) {
                        elWindow.requestAnimationFrame(_this.scrollY);
                        _this.scrollYTicking = true;
                    }
                    if (!_this.isScrolling) {
                        _this.isScrolling = true;
                        addClasses(_this.el, _this.classNames.scrolling);
                    }
                    _this.showScrollbar("x");
                    _this.showScrollbar("y");
                    _this.onStopScrolling();
                };
                this.scrollX = function () {
                    if (_this.axis.x.isOverflowing) _this.positionScrollbar("x");
                    _this.scrollXTicking = false;
                };
                this.scrollY = function () {
                    if (_this.axis.y.isOverflowing) _this.positionScrollbar("y");
                    _this.scrollYTicking = false;
                };
                this._onStopScrolling = function () {
                    dist_removeClasses(_this.el, _this.classNames.scrolling);
                    if (_this.options.autoHide) {
                        _this.hideScrollbar("x");
                        _this.hideScrollbar("y");
                    }
                    _this.isScrolling = false;
                };
                this.onMouseEnter = function () {
                    if (!_this.isMouseEntering) {
                        addClasses(_this.el, _this.classNames.mouseEntered);
                        _this.showScrollbar("x");
                        _this.showScrollbar("y");
                        _this.isMouseEntering = true;
                    }
                    _this.onMouseEntered();
                };
                this._onMouseEntered = function () {
                    dist_removeClasses(_this.el, _this.classNames.mouseEntered);
                    if (_this.options.autoHide) {
                        _this.hideScrollbar("x");
                        _this.hideScrollbar("y");
                    }
                    _this.isMouseEntering = false;
                };
                this._onMouseMove = function (e) {
                    _this.mouseX = e.clientX;
                    _this.mouseY = e.clientY;
                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) _this.onMouseMoveForAxis("x");
                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) _this.onMouseMoveForAxis("y");
                };
                this.onMouseLeave = function () {
                    _this.onMouseMove.cancel();
                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) _this.onMouseLeaveForAxis("x");
                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) _this.onMouseLeaveForAxis("y");
                    _this.mouseX = -1;
                    _this.mouseY = -1;
                };
                this._onWindowResize = function () {
                    _this.scrollbarWidth = _this.getScrollbarWidth();
                    _this.hideNativeScrollbar();
                };
                this.onPointerEvent = function (e) {
                    if (!_this.axis.x.track.el || !_this.axis.y.track.el || !_this.axis.x.scrollbar.el || !_this.axis.y.scrollbar.el) return;
                    var isWithinTrackXBounds, isWithinTrackYBounds;
                    _this.axis.x.track.rect = _this.axis.x.track.el.getBoundingClientRect();
                    _this.axis.y.track.rect = _this.axis.y.track.el.getBoundingClientRect();
                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) isWithinTrackXBounds = _this.isWithinBounds(_this.axis.x.track.rect);
                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) isWithinTrackYBounds = _this.isWithinBounds(_this.axis.y.track.rect);
                    if (isWithinTrackXBounds || isWithinTrackYBounds) {
                        e.stopPropagation();
                        if (e.type === "pointerdown" && e.pointerType !== "touch") {
                            if (isWithinTrackXBounds) {
                                _this.axis.x.scrollbar.rect = _this.axis.x.scrollbar.el.getBoundingClientRect();
                                if (_this.isWithinBounds(_this.axis.x.scrollbar.rect)) _this.onDragStart(e, "x"); else _this.onTrackClick(e, "x");
                            }
                            if (isWithinTrackYBounds) {
                                _this.axis.y.scrollbar.rect = _this.axis.y.scrollbar.el.getBoundingClientRect();
                                if (_this.isWithinBounds(_this.axis.y.scrollbar.rect)) _this.onDragStart(e, "y"); else _this.onTrackClick(e, "y");
                            }
                        }
                    }
                };
                this.drag = function (e) {
                    var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l;
                    if (!_this.draggedAxis || !_this.contentWrapperEl) return;
                    var eventOffset;
                    var track = _this.axis[_this.draggedAxis].track;
                    var trackSize = (_b = (_a = track.rect) === null || _a === void 0 ? void 0 : _a[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _b !== void 0 ? _b : 0;
                    var scrollbar = _this.axis[_this.draggedAxis].scrollbar;
                    var contentSize = (_d = (_c = _this.contentWrapperEl) === null || _c === void 0 ? void 0 : _c[_this.axis[_this.draggedAxis].scrollSizeAttr]) !== null && _d !== void 0 ? _d : 0;
                    var hostSize = parseInt((_f = (_e = _this.elStyles) === null || _e === void 0 ? void 0 : _e[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _f !== void 0 ? _f : "0px", 10);
                    e.preventDefault();
                    e.stopPropagation();
                    if (_this.draggedAxis === "y") eventOffset = e.pageY; else eventOffset = e.pageX;
                    var dragPos = eventOffset - ((_h = (_g = track.rect) === null || _g === void 0 ? void 0 : _g[_this.axis[_this.draggedAxis].offsetAttr]) !== null && _h !== void 0 ? _h : 0) - _this.axis[_this.draggedAxis].dragOffset;
                    dragPos = _this.draggedAxis === "x" && _this.isRtl ? ((_k = (_j = track.rect) === null || _j === void 0 ? void 0 : _j[_this.axis[_this.draggedAxis].sizeAttr]) !== null && _k !== void 0 ? _k : 0) - scrollbar.size - dragPos : dragPos;
                    var dragPerc = dragPos / (trackSize - scrollbar.size);
                    var scrollPos = dragPerc * (contentSize - hostSize);
                    if (_this.draggedAxis === "x" && _this.isRtl) scrollPos = ((_l = SimpleBarCore.getRtlHelpers()) === null || _l === void 0 ? void 0 : _l.isScrollingToNegative) ? -scrollPos : scrollPos;
                    _this.contentWrapperEl[_this.axis[_this.draggedAxis].scrollOffsetAttr] = scrollPos;
                };
                this.onEndDrag = function (e) {
                    var elDocument = getElementDocument(_this.el);
                    var elWindow = getElementWindow(_this.el);
                    e.preventDefault();
                    e.stopPropagation();
                    dist_removeClasses(_this.el, _this.classNames.dragging);
                    elDocument.removeEventListener("mousemove", _this.drag, true);
                    elDocument.removeEventListener("mouseup", _this.onEndDrag, true);
                    _this.removePreventClickId = elWindow.setTimeout((function () {
                        elDocument.removeEventListener("click", _this.preventClick, true);
                        elDocument.removeEventListener("dblclick", _this.preventClick, true);
                        _this.removePreventClickId = null;
                    }));
                };
                this.preventClick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                };
                this.el = element;
                this.options = __assign(__assign({}, SimpleBarCore.defaultOptions), options);
                this.classNames = __assign(__assign({}, SimpleBarCore.defaultOptions.classNames), options.classNames);
                this.axis = {
                    x: {
                        scrollOffsetAttr: "scrollLeft",
                        sizeAttr: "width",
                        scrollSizeAttr: "scrollWidth",
                        offsetSizeAttr: "offsetWidth",
                        offsetAttr: "left",
                        overflowAttr: "overflowX",
                        dragOffset: 0,
                        isOverflowing: true,
                        forceVisible: false,
                        track: {
                            size: null,
                            el: null,
                            rect: null,
                            isVisible: false
                        },
                        scrollbar: {
                            size: null,
                            el: null,
                            rect: null,
                            isVisible: false
                        }
                    },
                    y: {
                        scrollOffsetAttr: "scrollTop",
                        sizeAttr: "height",
                        scrollSizeAttr: "scrollHeight",
                        offsetSizeAttr: "offsetHeight",
                        offsetAttr: "top",
                        overflowAttr: "overflowY",
                        dragOffset: 0,
                        isOverflowing: true,
                        forceVisible: false,
                        track: {
                            size: null,
                            el: null,
                            rect: null,
                            isVisible: false
                        },
                        scrollbar: {
                            size: null,
                            el: null,
                            rect: null,
                            isVisible: false
                        }
                    }
                };
                if (typeof this.el !== "object" || !this.el.nodeName) throw new Error("Argument passed to SimpleBar must be an HTML element instead of ".concat(this.el));
                this.onMouseMove = lodash_es_throttle(this._onMouseMove, 64);
                this.onWindowResize = lodash_es_debounce(this._onWindowResize, 64, {
                    leading: true
                });
                this.onStopScrolling = lodash_es_debounce(this._onStopScrolling, this.stopScrollDelay);
                this.onMouseEntered = lodash_es_debounce(this._onMouseEntered, this.stopScrollDelay);
                this.init();
            }
            SimpleBarCore.getRtlHelpers = function () {
                if (SimpleBarCore.rtlHelpers) return SimpleBarCore.rtlHelpers;
                var dummyDiv = document.createElement("div");
                dummyDiv.innerHTML = '<div class="simplebar-dummy-scrollbar-size"><div></div></div>';
                var scrollbarDummyEl = dummyDiv.firstElementChild;
                var dummyChild = scrollbarDummyEl === null || scrollbarDummyEl === void 0 ? void 0 : scrollbarDummyEl.firstElementChild;
                if (!dummyChild) return null;
                document.body.appendChild(scrollbarDummyEl);
                scrollbarDummyEl.scrollLeft = 0;
                var dummyContainerOffset = SimpleBarCore.getOffset(scrollbarDummyEl);
                var dummyChildOffset = SimpleBarCore.getOffset(dummyChild);
                scrollbarDummyEl.scrollLeft = -999;
                var dummyChildOffsetAfterScroll = SimpleBarCore.getOffset(dummyChild);
                document.body.removeChild(scrollbarDummyEl);
                SimpleBarCore.rtlHelpers = {
                    isScrollOriginAtZero: dummyContainerOffset.left !== dummyChildOffset.left,
                    isScrollingToNegative: dummyChildOffset.left !== dummyChildOffsetAfterScroll.left
                };
                return SimpleBarCore.rtlHelpers;
            };
            SimpleBarCore.prototype.getScrollbarWidth = function () {
                try {
                    if (this.contentWrapperEl && getComputedStyle(this.contentWrapperEl, "::-webkit-scrollbar").display === "none" || "scrollbarWidth" in document.documentElement.style || "-ms-overflow-style" in document.documentElement.style) return 0; else return scrollbarWidth();
                } catch (e) {
                    return scrollbarWidth();
                }
            };
            SimpleBarCore.getOffset = function (el) {
                var rect = el.getBoundingClientRect();
                var elDocument = getElementDocument(el);
                var elWindow = getElementWindow(el);
                return {
                    top: rect.top + (elWindow.pageYOffset || elDocument.documentElement.scrollTop),
                    left: rect.left + (elWindow.pageXOffset || elDocument.documentElement.scrollLeft)
                };
            };
            SimpleBarCore.prototype.init = function () {
                if (can_use_dom) {
                    this.initDOM();
                    this.rtlHelpers = SimpleBarCore.getRtlHelpers();
                    this.scrollbarWidth = this.getScrollbarWidth();
                    this.recalculate();
                    this.initListeners();
                }
            };
            SimpleBarCore.prototype.initDOM = function () {
                var _a, _b;
                this.wrapperEl = this.el.querySelector(classNamesToQuery(this.classNames.wrapper));
                this.contentWrapperEl = this.options.scrollableNode || this.el.querySelector(classNamesToQuery(this.classNames.contentWrapper));
                this.contentEl = this.options.contentNode || this.el.querySelector(classNamesToQuery(this.classNames.contentEl));
                this.offsetEl = this.el.querySelector(classNamesToQuery(this.classNames.offset));
                this.maskEl = this.el.querySelector(classNamesToQuery(this.classNames.mask));
                this.placeholderEl = this.findChild(this.wrapperEl, classNamesToQuery(this.classNames.placeholder));
                this.heightAutoObserverWrapperEl = this.el.querySelector(classNamesToQuery(this.classNames.heightAutoObserverWrapperEl));
                this.heightAutoObserverEl = this.el.querySelector(classNamesToQuery(this.classNames.heightAutoObserverEl));
                this.axis.x.track.el = this.findChild(this.el, "".concat(classNamesToQuery(this.classNames.track)).concat(classNamesToQuery(this.classNames.horizontal)));
                this.axis.y.track.el = this.findChild(this.el, "".concat(classNamesToQuery(this.classNames.track)).concat(classNamesToQuery(this.classNames.vertical)));
                this.axis.x.scrollbar.el = ((_a = this.axis.x.track.el) === null || _a === void 0 ? void 0 : _a.querySelector(classNamesToQuery(this.classNames.scrollbar))) || null;
                this.axis.y.scrollbar.el = ((_b = this.axis.y.track.el) === null || _b === void 0 ? void 0 : _b.querySelector(classNamesToQuery(this.classNames.scrollbar))) || null;
                if (!this.options.autoHide) {
                    addClasses(this.axis.x.scrollbar.el, this.classNames.visible);
                    addClasses(this.axis.y.scrollbar.el, this.classNames.visible);
                }
            };
            SimpleBarCore.prototype.initListeners = function () {
                var _this = this;
                var _a;
                var elWindow = getElementWindow(this.el);
                this.el.addEventListener("mouseenter", this.onMouseEnter);
                this.el.addEventListener("pointerdown", this.onPointerEvent, true);
                this.el.addEventListener("mousemove", this.onMouseMove);
                this.el.addEventListener("mouseleave", this.onMouseLeave);
                (_a = this.contentWrapperEl) === null || _a === void 0 ? void 0 : _a.addEventListener("scroll", this.onScroll);
                elWindow.addEventListener("resize", this.onWindowResize);
                if (!this.contentEl) return;
                if (window.ResizeObserver) {
                    var resizeObserverStarted_1 = false;
                    var resizeObserver = elWindow.ResizeObserver || ResizeObserver;
                    this.resizeObserver = new resizeObserver((function () {
                        if (!resizeObserverStarted_1) return;
                        elWindow.requestAnimationFrame((function () {
                            _this.recalculate();
                        }));
                    }));
                    this.resizeObserver.observe(this.el);
                    this.resizeObserver.observe(this.contentEl);
                    elWindow.requestAnimationFrame((function () {
                        resizeObserverStarted_1 = true;
                    }));
                }
                this.mutationObserver = new elWindow.MutationObserver((function () {
                    elWindow.requestAnimationFrame((function () {
                        _this.recalculate();
                    }));
                }));
                this.mutationObserver.observe(this.contentEl, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            };
            SimpleBarCore.prototype.recalculate = function () {
                if (!this.heightAutoObserverEl || !this.contentEl || !this.contentWrapperEl || !this.wrapperEl || !this.placeholderEl) return;
                var elWindow = getElementWindow(this.el);
                this.elStyles = elWindow.getComputedStyle(this.el);
                this.isRtl = this.elStyles.direction === "rtl";
                var contentElOffsetWidth = this.contentEl.offsetWidth;
                var isHeightAuto = this.heightAutoObserverEl.offsetHeight <= 1;
                var isWidthAuto = this.heightAutoObserverEl.offsetWidth <= 1 || contentElOffsetWidth > 0;
                var contentWrapperElOffsetWidth = this.contentWrapperEl.offsetWidth;
                var elOverflowX = this.elStyles.overflowX;
                var elOverflowY = this.elStyles.overflowY;
                this.contentEl.style.padding = "".concat(this.elStyles.paddingTop, " ").concat(this.elStyles.paddingRight, " ").concat(this.elStyles.paddingBottom, " ").concat(this.elStyles.paddingLeft);
                this.wrapperEl.style.margin = "-".concat(this.elStyles.paddingTop, " -").concat(this.elStyles.paddingRight, " -").concat(this.elStyles.paddingBottom, " -").concat(this.elStyles.paddingLeft);
                var contentElScrollHeight = this.contentEl.scrollHeight;
                var contentElScrollWidth = this.contentEl.scrollWidth;
                this.contentWrapperEl.style.height = isHeightAuto ? "auto" : "100%";
                this.placeholderEl.style.width = isWidthAuto ? "".concat(contentElOffsetWidth || contentElScrollWidth, "px") : "auto";
                this.placeholderEl.style.height = "".concat(contentElScrollHeight, "px");
                var contentWrapperElOffsetHeight = this.contentWrapperEl.offsetHeight;
                this.axis.x.isOverflowing = contentElOffsetWidth !== 0 && contentElScrollWidth > contentElOffsetWidth;
                this.axis.y.isOverflowing = contentElScrollHeight > contentWrapperElOffsetHeight;
                this.axis.x.isOverflowing = elOverflowX === "hidden" ? false : this.axis.x.isOverflowing;
                this.axis.y.isOverflowing = elOverflowY === "hidden" ? false : this.axis.y.isOverflowing;
                this.axis.x.forceVisible = this.options.forceVisible === "x" || this.options.forceVisible === true;
                this.axis.y.forceVisible = this.options.forceVisible === "y" || this.options.forceVisible === true;
                this.hideNativeScrollbar();
                var offsetForXScrollbar = this.axis.x.isOverflowing ? this.scrollbarWidth : 0;
                var offsetForYScrollbar = this.axis.y.isOverflowing ? this.scrollbarWidth : 0;
                this.axis.x.isOverflowing = this.axis.x.isOverflowing && contentElScrollWidth > contentWrapperElOffsetWidth - offsetForYScrollbar;
                this.axis.y.isOverflowing = this.axis.y.isOverflowing && contentElScrollHeight > contentWrapperElOffsetHeight - offsetForXScrollbar;
                this.axis.x.scrollbar.size = this.getScrollbarSize("x");
                this.axis.y.scrollbar.size = this.getScrollbarSize("y");
                if (this.axis.x.scrollbar.el) this.axis.x.scrollbar.el.style.width = "".concat(this.axis.x.scrollbar.size, "px");
                if (this.axis.y.scrollbar.el) this.axis.y.scrollbar.el.style.height = "".concat(this.axis.y.scrollbar.size, "px");
                this.positionScrollbar("x");
                this.positionScrollbar("y");
                this.toggleTrackVisibility("x");
                this.toggleTrackVisibility("y");
            };
            SimpleBarCore.prototype.getScrollbarSize = function (axis) {
                var _a, _b;
                if (axis === void 0) axis = "y";
                if (!this.axis[axis].isOverflowing || !this.contentEl) return 0;
                var contentSize = this.contentEl[this.axis[axis].scrollSizeAttr];
                var trackSize = (_b = (_a = this.axis[axis].track.el) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetSizeAttr]) !== null && _b !== void 0 ? _b : 0;
                var scrollbarRatio = trackSize / contentSize;
                var scrollbarSize;
                scrollbarSize = Math.max(~~(scrollbarRatio * trackSize), this.options.scrollbarMinSize);
                if (this.options.scrollbarMaxSize) scrollbarSize = Math.min(scrollbarSize, this.options.scrollbarMaxSize);
                return scrollbarSize;
            };
            SimpleBarCore.prototype.positionScrollbar = function (axis) {
                var _a, _b, _c;
                if (axis === void 0) axis = "y";
                var scrollbar = this.axis[axis].scrollbar;
                if (!this.axis[axis].isOverflowing || !this.contentWrapperEl || !scrollbar.el || !this.elStyles) return;
                var contentSize = this.contentWrapperEl[this.axis[axis].scrollSizeAttr];
                var trackSize = ((_a = this.axis[axis].track.el) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetSizeAttr]) || 0;
                var hostSize = parseInt(this.elStyles[this.axis[axis].sizeAttr], 10);
                var scrollOffset = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
                scrollOffset = axis === "x" && this.isRtl && ((_b = SimpleBarCore.getRtlHelpers()) === null || _b === void 0 ? void 0 : _b.isScrollOriginAtZero) ? -scrollOffset : scrollOffset;
                if (axis === "x" && this.isRtl) scrollOffset = ((_c = SimpleBarCore.getRtlHelpers()) === null || _c === void 0 ? void 0 : _c.isScrollingToNegative) ? scrollOffset : -scrollOffset;
                var scrollPourcent = scrollOffset / (contentSize - hostSize);
                var handleOffset = ~~((trackSize - scrollbar.size) * scrollPourcent);
                handleOffset = axis === "x" && this.isRtl ? -handleOffset + (trackSize - scrollbar.size) : handleOffset;
                scrollbar.el.style.transform = axis === "x" ? "translate3d(".concat(handleOffset, "px, 0, 0)") : "translate3d(0, ".concat(handleOffset, "px, 0)");
            };
            SimpleBarCore.prototype.toggleTrackVisibility = function (axis) {
                if (axis === void 0) axis = "y";
                var track = this.axis[axis].track.el;
                var scrollbar = this.axis[axis].scrollbar.el;
                if (!track || !scrollbar || !this.contentWrapperEl) return;
                if (this.axis[axis].isOverflowing || this.axis[axis].forceVisible) {
                    track.style.visibility = "visible";
                    this.contentWrapperEl.style[this.axis[axis].overflowAttr] = "scroll";
                    this.el.classList.add("".concat(this.classNames.scrollable, "-").concat(axis));
                } else {
                    track.style.visibility = "hidden";
                    this.contentWrapperEl.style[this.axis[axis].overflowAttr] = "hidden";
                    this.el.classList.remove("".concat(this.classNames.scrollable, "-").concat(axis));
                }
                if (this.axis[axis].isOverflowing) scrollbar.style.display = "block"; else scrollbar.style.display = "none";
            };
            SimpleBarCore.prototype.showScrollbar = function (axis) {
                if (axis === void 0) axis = "y";
                if (this.axis[axis].isOverflowing && !this.axis[axis].scrollbar.isVisible) {
                    addClasses(this.axis[axis].scrollbar.el, this.classNames.visible);
                    this.axis[axis].scrollbar.isVisible = true;
                }
            };
            SimpleBarCore.prototype.hideScrollbar = function (axis) {
                if (axis === void 0) axis = "y";
                if (this.axis[axis].isOverflowing && this.axis[axis].scrollbar.isVisible) {
                    dist_removeClasses(this.axis[axis].scrollbar.el, this.classNames.visible);
                    this.axis[axis].scrollbar.isVisible = false;
                }
            };
            SimpleBarCore.prototype.hideNativeScrollbar = function () {
                if (!this.offsetEl) return;
                this.offsetEl.style[this.isRtl ? "left" : "right"] = this.axis.y.isOverflowing || this.axis.y.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px";
                this.offsetEl.style.bottom = this.axis.x.isOverflowing || this.axis.x.forceVisible ? "-".concat(this.scrollbarWidth, "px") : "0px";
            };
            SimpleBarCore.prototype.onMouseMoveForAxis = function (axis) {
                if (axis === void 0) axis = "y";
                var currentAxis = this.axis[axis];
                if (!currentAxis.track.el || !currentAxis.scrollbar.el) return;
                currentAxis.track.rect = currentAxis.track.el.getBoundingClientRect();
                currentAxis.scrollbar.rect = currentAxis.scrollbar.el.getBoundingClientRect();
                if (this.isWithinBounds(currentAxis.track.rect)) {
                    this.showScrollbar(axis);
                    addClasses(currentAxis.track.el, this.classNames.hover);
                    if (this.isWithinBounds(currentAxis.scrollbar.rect)) addClasses(currentAxis.scrollbar.el, this.classNames.hover); else dist_removeClasses(currentAxis.scrollbar.el, this.classNames.hover);
                } else {
                    dist_removeClasses(currentAxis.track.el, this.classNames.hover);
                    if (this.options.autoHide) this.hideScrollbar(axis);
                }
            };
            SimpleBarCore.prototype.onMouseLeaveForAxis = function (axis) {
                if (axis === void 0) axis = "y";
                dist_removeClasses(this.axis[axis].track.el, this.classNames.hover);
                dist_removeClasses(this.axis[axis].scrollbar.el, this.classNames.hover);
                if (this.options.autoHide) this.hideScrollbar(axis);
            };
            SimpleBarCore.prototype.onDragStart = function (e, axis) {
                var _a;
                if (axis === void 0) axis = "y";
                var elDocument = getElementDocument(this.el);
                var elWindow = getElementWindow(this.el);
                var scrollbar = this.axis[axis].scrollbar;
                var eventOffset = axis === "y" ? e.pageY : e.pageX;
                this.axis[axis].dragOffset = eventOffset - (((_a = scrollbar.rect) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetAttr]) || 0);
                this.draggedAxis = axis;
                addClasses(this.el, this.classNames.dragging);
                elDocument.addEventListener("mousemove", this.drag, true);
                elDocument.addEventListener("mouseup", this.onEndDrag, true);
                if (this.removePreventClickId === null) {
                    elDocument.addEventListener("click", this.preventClick, true);
                    elDocument.addEventListener("dblclick", this.preventClick, true);
                } else {
                    elWindow.clearTimeout(this.removePreventClickId);
                    this.removePreventClickId = null;
                }
            };
            SimpleBarCore.prototype.onTrackClick = function (e, axis) {
                var _this = this;
                var _a, _b, _c, _d;
                if (axis === void 0) axis = "y";
                var currentAxis = this.axis[axis];
                if (!this.options.clickOnTrack || !currentAxis.scrollbar.el || !this.contentWrapperEl) return;
                e.preventDefault();
                var elWindow = getElementWindow(this.el);
                this.axis[axis].scrollbar.rect = currentAxis.scrollbar.el.getBoundingClientRect();
                var scrollbar = this.axis[axis].scrollbar;
                var scrollbarOffset = (_b = (_a = scrollbar.rect) === null || _a === void 0 ? void 0 : _a[this.axis[axis].offsetAttr]) !== null && _b !== void 0 ? _b : 0;
                var hostSize = parseInt((_d = (_c = this.elStyles) === null || _c === void 0 ? void 0 : _c[this.axis[axis].sizeAttr]) !== null && _d !== void 0 ? _d : "0px", 10);
                var scrolled = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
                var t = axis === "y" ? this.mouseY - scrollbarOffset : this.mouseX - scrollbarOffset;
                var dir = t < 0 ? -1 : 1;
                var scrollSize = dir === -1 ? scrolled - hostSize : scrolled + hostSize;
                var speed = 40;
                var scrollTo = function () {
                    if (!_this.contentWrapperEl) return;
                    if (dir === -1) {
                        if (scrolled > scrollSize) {
                            scrolled -= speed;
                            _this.contentWrapperEl[_this.axis[axis].scrollOffsetAttr] = scrolled;
                            elWindow.requestAnimationFrame(scrollTo);
                        }
                    } else if (scrolled < scrollSize) {
                        scrolled += speed;
                        _this.contentWrapperEl[_this.axis[axis].scrollOffsetAttr] = scrolled;
                        elWindow.requestAnimationFrame(scrollTo);
                    }
                };
                scrollTo();
            };
            SimpleBarCore.prototype.getContentElement = function () {
                return this.contentEl;
            };
            SimpleBarCore.prototype.getScrollElement = function () {
                return this.contentWrapperEl;
            };
            SimpleBarCore.prototype.removeListeners = function () {
                var elWindow = getElementWindow(this.el);
                this.el.removeEventListener("mouseenter", this.onMouseEnter);
                this.el.removeEventListener("pointerdown", this.onPointerEvent, true);
                this.el.removeEventListener("mousemove", this.onMouseMove);
                this.el.removeEventListener("mouseleave", this.onMouseLeave);
                if (this.contentWrapperEl) this.contentWrapperEl.removeEventListener("scroll", this.onScroll);
                elWindow.removeEventListener("resize", this.onWindowResize);
                if (this.mutationObserver) this.mutationObserver.disconnect();
                if (this.resizeObserver) this.resizeObserver.disconnect();
                this.onMouseMove.cancel();
                this.onWindowResize.cancel();
                this.onStopScrolling.cancel();
                this.onMouseEntered.cancel();
            };
            SimpleBarCore.prototype.unMount = function () {
                this.removeListeners();
            };
            SimpleBarCore.prototype.isWithinBounds = function (bbox) {
                return this.mouseX >= bbox.left && this.mouseX <= bbox.left + bbox.width && this.mouseY >= bbox.top && this.mouseY <= bbox.top + bbox.height;
            };
            SimpleBarCore.prototype.findChild = function (el, query) {
                var matches = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
                return Array.prototype.filter.call(el.children, (function (child) {
                    return matches.call(child, query);
                }))[0];
            };
            SimpleBarCore.rtlHelpers = null;
            SimpleBarCore.defaultOptions = {
                forceVisible: false,
                clickOnTrack: true,
                scrollbarMinSize: 25,
                scrollbarMaxSize: 0,
                ariaLabel: "scrollable content",
                classNames: {
                    contentEl: "simplebar-content",
                    contentWrapper: "simplebar-content-wrapper",
                    offset: "simplebar-offset",
                    mask: "simplebar-mask",
                    wrapper: "simplebar-wrapper",
                    placeholder: "simplebar-placeholder",
                    scrollbar: "simplebar-scrollbar",
                    track: "simplebar-track",
                    heightAutoObserverWrapperEl: "simplebar-height-auto-observer-wrapper",
                    heightAutoObserverEl: "simplebar-height-auto-observer",
                    visible: "simplebar-visible",
                    horizontal: "simplebar-horizontal",
                    vertical: "simplebar-vertical",
                    hover: "simplebar-hover",
                    dragging: "simplebar-dragging",
                    scrolling: "simplebar-scrolling",
                    scrollable: "simplebar-scrollable",
                    mouseEntered: "simplebar-mouse-entered"
                },
                scrollableNode: null,
                contentNode: null,
                autoHide: true
            };
            SimpleBarCore.getOptions = getOptions;
            SimpleBarCore.helpers = helpers;
            return SimpleBarCore;
        }();
        var extendStatics = function (d, b) {
            extendStatics = Object.setPrototypeOf || {
                __proto__: []
            } instanceof Array && function (d, b) {
                d.__proto__ = b;
            } || function (d, b) {
                for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p];
            };
            return extendStatics(d, b);
        };
        function __extends(d, b) {
            if (typeof b !== "function" && b !== null) throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
            extendStatics(d, b);
            function __() {
                this.constructor = d;
            }
            d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __);
        }
        var _a = SimpleBarCore.helpers, dist_getOptions = _a.getOptions, dist_addClasses = _a.addClasses;
        var SimpleBar = function (_super) {
            __extends(SimpleBar, _super);
            function SimpleBar() {
                var args = [];
                for (var _i = 0; _i < arguments.length; _i++) args[_i] = arguments[_i];
                var _this = _super.apply(this, args) || this;
                SimpleBar.instances.set(args[0], _this);
                return _this;
            }
            SimpleBar.initDOMLoadedElements = function () {
                document.removeEventListener("DOMContentLoaded", this.initDOMLoadedElements);
                window.removeEventListener("load", this.initDOMLoadedElements);
                Array.prototype.forEach.call(document.querySelectorAll("[data-simplebar]"), (function (el) {
                    if (el.getAttribute("data-simplebar") !== "init" && !SimpleBar.instances.has(el)) new SimpleBar(el, dist_getOptions(el.attributes));
                }));
            };
            SimpleBar.removeObserver = function () {
                var _a;
                (_a = SimpleBar.globalObserver) === null || _a === void 0 ? void 0 : _a.disconnect();
            };
            SimpleBar.prototype.initDOM = function () {
                var _this = this;
                var _a, _b, _c;
                if (!Array.prototype.filter.call(this.el.children, (function (child) {
                    return child.classList.contains(_this.classNames.wrapper);
                })).length) {
                    this.wrapperEl = document.createElement("div");
                    this.contentWrapperEl = document.createElement("div");
                    this.offsetEl = document.createElement("div");
                    this.maskEl = document.createElement("div");
                    this.contentEl = document.createElement("div");
                    this.placeholderEl = document.createElement("div");
                    this.heightAutoObserverWrapperEl = document.createElement("div");
                    this.heightAutoObserverEl = document.createElement("div");
                    dist_addClasses(this.wrapperEl, this.classNames.wrapper);
                    dist_addClasses(this.contentWrapperEl, this.classNames.contentWrapper);
                    dist_addClasses(this.offsetEl, this.classNames.offset);
                    dist_addClasses(this.maskEl, this.classNames.mask);
                    dist_addClasses(this.contentEl, this.classNames.contentEl);
                    dist_addClasses(this.placeholderEl, this.classNames.placeholder);
                    dist_addClasses(this.heightAutoObserverWrapperEl, this.classNames.heightAutoObserverWrapperEl);
                    dist_addClasses(this.heightAutoObserverEl, this.classNames.heightAutoObserverEl);
                    while (this.el.firstChild) this.contentEl.appendChild(this.el.firstChild);
                    this.contentWrapperEl.appendChild(this.contentEl);
                    this.offsetEl.appendChild(this.contentWrapperEl);
                    this.maskEl.appendChild(this.offsetEl);
                    this.heightAutoObserverWrapperEl.appendChild(this.heightAutoObserverEl);
                    this.wrapperEl.appendChild(this.heightAutoObserverWrapperEl);
                    this.wrapperEl.appendChild(this.maskEl);
                    this.wrapperEl.appendChild(this.placeholderEl);
                    this.el.appendChild(this.wrapperEl);
                    (_a = this.contentWrapperEl) === null || _a === void 0 ? void 0 : _a.setAttribute("tabindex", "0");
                    (_b = this.contentWrapperEl) === null || _b === void 0 ? void 0 : _b.setAttribute("role", "region");
                    (_c = this.contentWrapperEl) === null || _c === void 0 ? void 0 : _c.setAttribute("aria-label", this.options.ariaLabel);
                }
                if (!this.axis.x.track.el || !this.axis.y.track.el) {
                    var track = document.createElement("div");
                    var scrollbar = document.createElement("div");
                    dist_addClasses(track, this.classNames.track);
                    dist_addClasses(scrollbar, this.classNames.scrollbar);
                    track.appendChild(scrollbar);
                    this.axis.x.track.el = track.cloneNode(true);
                    dist_addClasses(this.axis.x.track.el, this.classNames.horizontal);
                    this.axis.y.track.el = track.cloneNode(true);
                    dist_addClasses(this.axis.y.track.el, this.classNames.vertical);
                    this.el.appendChild(this.axis.x.track.el);
                    this.el.appendChild(this.axis.y.track.el);
                }
                SimpleBarCore.prototype.initDOM.call(this);
                this.el.setAttribute("data-simplebar", "init");
            };
            SimpleBar.prototype.unMount = function () {
                SimpleBarCore.prototype.unMount.call(this);
                SimpleBar.instances["delete"](this.el);
            };
            SimpleBar.initHtmlApi = function () {
                this.initDOMLoadedElements = this.initDOMLoadedElements.bind(this);
                if (typeof MutationObserver !== "undefined") {
                    this.globalObserver = new MutationObserver(SimpleBar.handleMutations);
                    this.globalObserver.observe(document, {
                        childList: true,
                        subtree: true
                    });
                }
                if (document.readyState === "complete" || document.readyState !== "loading" && !document.documentElement.doScroll) window.setTimeout(this.initDOMLoadedElements); else {
                    document.addEventListener("DOMContentLoaded", this.initDOMLoadedElements);
                    window.addEventListener("load", this.initDOMLoadedElements);
                }
            };
            SimpleBar.handleMutations = function (mutations) {
                mutations.forEach((function (mutation) {
                    mutation.addedNodes.forEach((function (addedNode) {
                        if (addedNode.nodeType === 1) if (addedNode.hasAttribute("data-simplebar")) !SimpleBar.instances.has(addedNode) && document.documentElement.contains(addedNode) && new SimpleBar(addedNode, dist_getOptions(addedNode.attributes)); else addedNode.querySelectorAll("[data-simplebar]").forEach((function (el) {
                            if (el.getAttribute("data-simplebar") !== "init" && !SimpleBar.instances.has(el) && document.documentElement.contains(el)) new SimpleBar(el, dist_getOptions(el.attributes));
                        }));
                    }));
                    mutation.removedNodes.forEach((function (removedNode) {
                        if (removedNode.nodeType === 1) if (removedNode.getAttribute("data-simplebar") === "init") SimpleBar.instances.has(removedNode) && !document.documentElement.contains(removedNode) && SimpleBar.instances.get(removedNode).unMount(); else Array.prototype.forEach.call(removedNode.querySelectorAll('[data-simplebar="init"]'), (function (el) {
                            SimpleBar.instances.has(el) && !document.documentElement.contains(el) && SimpleBar.instances.get(el).unMount();
                        }));
                    }));
                }));
            };
            SimpleBar.instances = new WeakMap;
            return SimpleBar;
        }(SimpleBarCore);
        if (can_use_dom) SimpleBar.initHtmlApi();
        let addWindowScrollEvent = false;
        setTimeout((() => {
            if (addWindowScrollEvent) {
                let windowScroll = new Event("windowScroll");
                window.addEventListener("scroll", (function (e) {
                    document.dispatchEvent(windowScroll);
                }));
            }
        }), 0);
        setTimeout((function () {
            let addBillingBtns = document.querySelectorAll(".enter-info__checkbox.checkbox .checkbox__input");
            if (addBillingBtns) addBillingBtns.forEach((addBillingBtn => {
                let addContent = addBillingBtn.closest(".enter-info__block").querySelector(".add-info");
                if (addContent) addBillingBtn.addEventListener("change", (function () {
                    if (addBillingBtn.type === "checkbox") if (this.checked) _slideDown(addContent); else _slideUp(addContent); else if (this.value === "yes") _slideDown(addContent); else _slideUp(addContent);
                }));
            }));

            const bankCardInput = document.querySelector("[data-card]");
            let bankCardIconEl;
            if (bankCardInput) bankCardIconEl = bankCardInput.closest(".enter-info__input").querySelector(".enter-info__pay-systems");
            let imgSrc;
            let imgWidth;
            let imgHeight;
            let cvcInput;
            let imgEl;
            let imgName;
            function setCurrentPaySystem(x) {
                switch (x) {
                    case "4":
                        imgSrc = "visa.svg";
                        imgWidth = "51";
                        imgHeight = "17";

                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);

                        break;

                    case "5": case "2":
                        imgSrc = "mastercard.svg";
                        imgWidth = "33";
                        imgHeight = "20";
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        break;

                    case "3":
                        imgSrc = "amex.svg";
                        imgWidth = "33";
                        imgHeight = "20";

                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-amex.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);

                        break;

                    case "30": case "36": case "38": case "39":
                        imgSrc = "diners.svg";
                        imgWidth = "92";
                        imgHeight = "24";
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        break;

                    case "6":
                        imgSrc = "discover.svg";
                        imgWidth = "88";
                        imgHeight = "15";
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        break;

                    case "35":
                        imgSrc = "jcb.svg";
                        imgWidth = "32";
                        imgHeight = "24";
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        break;

                    case "62": case "60":
                        imgSrc = "union.svg";
                        imgWidth = "35";
                        imgHeight = "22";
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        break;

                    default:
                        cvcInput = document.querySelector("[data-card-cvc]");
                        imgEl = cvcInput.closest(".enter-info__input").querySelector(".enter-info__icon-input");
                        imgName = "cvc-other.svg";
                        imgEl.setAttribute("src", `/style_checkout/images/icons/${imgName}`);
                        imgSrc = "";
                }
                if (imgSrc) {
                    bankCardIconEl.setAttribute("src", `/style_checkout/images/pay-systems/${imgSrc}`);
                    bankCardIconEl.setAttribute("width", imgWidth);
                    bankCardIconEl.setAttribute("height", imgHeight);
                    bankCardIconEl.classList.remove("hide");
                } else bankCardIconEl.classList.add("hide");
            }
            function formatCardCode(value) {
                var cardCode = value.replace(/[^\d]/g, "").substring(0, 16);
                cardCode = cardCode != "" ? cardCode.match(/.{1,4}/g).join(" ") : "";
                return cardCode;
            }
            if (bankCardInput) bankCardInput.addEventListener("input", (function () {
                let ar = ["30", "36", "38", "39", "35", "62", "60"];
                if (ar.includes(bankCardInput.value.slice(0, 2))) {
                    let firstDigit = bankCardInput.value.slice(0, 2);
                    setCurrentPaySystem(firstDigit);
                }
                else {
                    let firstDigit = bankCardInput.value.slice(0, 1);
                    setCurrentPaySystem(firstDigit);
                }
                bankCardInput.value = formatCardCode(bankCardInput.value);
            }));
            const cardBlock = document.querySelector(".enter-info__card-content");
            const cryptoBlock = document.querySelector(".enter-info__crypto-content");
            const sepaBlock = document.querySelector(".enter-info__sepa-content");
            const paypalBlock = document.querySelector(".enter-info__paypal-content");
            const giftCardBlock = document.querySelector('.enter-info__gift-card-content');
            const googleBlock = document.querySelector(".enter-info__google-content");
            document.addEventListener("selectCallback", (function (e) {
                const currentSelect = e.detail.select;
                if (currentSelect.value === "crypto") {
                    _slideDown(cryptoBlock);
                    _slideUp(cardBlock);
                    if ($('#app_sepa_on').val() == '1') {
                       _slideUp(sepaBlock);
                    }
                    _slideUp(paypalBlock);
                    // _slideUp(giftCardBlock);
                    if ($('#app_google_on').val() == '1') {
                        _slideUp(googleBlock);
                    }
                } else if (currentSelect.value === "card" || currentSelect.value === "master" || currentSelect.value === "temp" || currentSelect.value === "other") {
                    _slideDown(cardBlock);
                    if ($('#app_sepa_on').val() == '1') {
                       _slideUp(sepaBlock);
                    }
                    _slideUp(cryptoBlock);
                    _slideUp(paypalBlock);
                    // _slideUp(giftCardBlock);
                    if ($('#app_google_on').val() == '1') {
                        _slideUp(googleBlock);
                    }
                } else if (currentSelect.value === "sepa") {
                    _slideDown(sepaBlock);
                    _slideUp(cryptoBlock);
                    _slideUp(cardBlock);
                    _slideUp(paypalBlock);
                    // _slideUp(giftCardBlock);
                    if ($('#app_google_on').val() == '1') {
                        _slideUp(googleBlock);
                    }
                } else if (currentSelect.value === "paypal") {
                    _slideDown(paypalBlock);
                    _slideUp(cryptoBlock);
                    _slideUp(cardBlock);
                    if ($('#app_sepa_on').val() == '1') {
                       _slideUp(sepaBlock);
                    }
                    // _slideUp(giftCardBlock);
                    if ($('#app_google_on').val() == '1') {
                        _slideUp(googleBlock);
                    }
                } else if (currentSelect.value === 'gift_card') {
                    // _slideDown(giftCardBlock);
                    _slideUp(paypalBlock);
                    _slideUp(cryptoBlock);
                    _slideUp(cardBlock);
                    if ($('#app_sepa_on').val() == '1') {
                       _slideUp(sepaBlock);
                    }
                    if ($('#app_google_on').val() == '1') {
                        _slideUp(googleBlock);
                    }
                } else if (currentSelect.value === "google") {
                    _slideDown(googleBlock);
                    _slideUp(cryptoBlock);
                    _slideUp(cardBlock);
                    if ($('#app_sepa_on').val() == '1') {
                       _slideUp(sepaBlock);
                    }
                    _slideUp(paypalBlock);
                    // _slideUp(giftCardBlock);
                }

                if (currentSelect.value != "crypto") {
                    document.getElementById("coupon").disabled = false;
                    document.getElementById("coupon_submit").disabled = false;
                    document.getElementById("c_82").disabled = false;
                    document.getElementById("c_83").disabled = false;
                    document.getElementById("c_85").disabled = false;
                    document.getElementById("c_86").disabled = false;
                }
            }));
            const copyBtns = document.querySelectorAll(".details-payment__copy-button");
            const unsecuredCopyToClipboard = text => {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand("copy");
                } catch (err) {
                    console.error("Unable to copy to clipboard", err);
                }
                document.body.removeChild(textArea);
            };
            const copyToClipboard = content => {
                if (window.isSecureContext && navigator.clipboard) navigator.clipboard.writeText(content); else unsecuredCopyToClipboard(content);
            };
            copyBtns.forEach((copyBtn => {
                copyBtn.addEventListener("click", (function (e) {
                    const currentRow = copyBtn.closest(".details-payment__row");
                    if (currentRow) {
                        const currentValue = currentRow.querySelector(".details-payment__amount").textContent;
                        if (currentValue) {
                            copyToClipboard(currentValue);
                            currentRow.classList.add("text-copied");
                            setTimeout((() => {
                                currentRow.classList.remove("text-copied");
                            }), 900);
                        }
                    }
                }));
            }));


        }));
        window["FLS"] = true;
        isWebp();
    })();
})();

$(".language .select__option").click(function () {
    var language = $(this).attr('data-value');
    window.location.replace(language);
});

$(".currency .select__option").click(function () {
    var currency = $(this).attr('data-value');
    window.location.replace(currency);
});

$(".select_billing_country .select__option").click(function () {
    var country = $(this).attr('data-value');
    $.ajax({
        url: '/checkout/change_country',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: { 'billing_country': country },
        success: function (data) {
            data = JSON.parse(data);
            $('.wrapper').html(data.html);
        }
    });
});

if ($('#c_82').is(':checked') == false) {
    $('#c_82').attr('onclick', 'Insurance(1)');
}

$('#c_82').off('click').click(function () {
    if ($(this).attr('checked')) {
        $('#insur_popup').show();
    }
});


$('.close_popup').off('click').click(function () {
    $('#c_82').prop('onclick', null);
    $('#c_82').prop('checked', true);
    $('#insur_popup').hide();
});

$(".card_type .select__option").click(function (e) {
    var type = $(this).attr('data-value');
    var form = $('form').serialize();

    flag = false;
    if (type == 'crypto') {
        document.getElementById('paid').style.display = "none";
        document.getElementById('waiting').style.display = "none";

        if (typeof $('input[name="crypt_currency"]:checked').val() != 'undefined'){

            let currency = $('input[name="crypt_currency"]:checked').val();

            $.ajax({
                url: '/crypto_info',
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: { 'currency': currency, 'email': $('#email').val() },
                success: function (data) {
                    // alert(data);
                    var result = JSON.parse(JSON.parse(data));
                    var cur = currency.split('_');
                    cur = cur[0];
                    var total = result.amount;
                    // //alert(total);
                    document.getElementById('crypto_total').innerHTML = total;
                    // document.getElementById('crypto_price').innerHTML =  result.crypto_total;
                    document.getElementById('crypto_discount_price').innerHTML = result.crypto_total;
                    document.getElementById('purse').innerHTML = result.purse;
                    document.getElementById('qr_code').src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + result.purse;
                    document.getElementById('invoiceId').value = result.invoiceId;
                    document.getElementById('invoce_p').innerHTML = result.invoiceId;

                    document.getElementById("requisites_load").hidden = true;
                    document.getElementById("requisites").hidden = false;
                    document.getElementById('paid').disabled = false;
                    // PollingManager.startPolling(CheckPayment, 1800000, 5000);

                    document.getElementById("coupon").disabled = true;
                    document.getElementById("coupon_submit").disabled = true;
                    document.getElementById("c_82").disabled = true;
                    document.getElementById("c_83").disabled = true;
                    document.getElementById("c_85").disabled = true;
                    document.getElementById("c_86").disabled = true;
                }
            });
        } else {
            $.ajax({
                url: '/validate_for_crypt',
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: form,
                async: false,
                success: function (data) {

                },
                error: function (data) {
                    flag = true;
                    var errors = JSON.parse(data.responseText);
                    errors.errors.forEach(function (error, i) {
                        console.log(i + '.' + error.message + ' (' + error.field + ')');
                        var popup = document.getElementById("error_" + error.field);
                        popup.classList.add("show");
                        if (i == 0) {
                            popup.scrollIntoView();
                        }
                    });
                }
            });
            if (flag) {
                previousIndex = this.selectedIndex;
                e.target.selectedIndex = previousIndex;
                return false;
            }
        }
    }
    else if(type == 'google')
    {
        $.ajax({
            url: '/validate_for_google',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: form,
            async: false,
            success: function (data) {

            },
            error: function (data) {
                flag = true;
                var errors = JSON.parse(data.responseText);
                errors.errors.forEach(function (error, i) {
                    console.log(i + '.' + error.message + ' (' + error.field + ')');
                    var popup = document.getElementById("error_" + error.field);
                    popup.classList.add("show");
                    if (i == 0) {
                        popup.scrollIntoView();
                    }
                });
            }
        });
        if (flag) {
            previousIndex = this.selectedIndex;
            e.target.selectedIndex = previousIndex;
            return false;
        }
    }
    else if(type == 'sepa')
    {
        $.ajax({
            url: '/validate_for_sepa',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: form,
            async: false,
            success: function (data) {

            },
            error: function (data) {
                flag = true;
                var errors = JSON.parse(data.responseText);
                errors.errors.forEach(function (error, i) {
                    console.log(i + '.' + error.message + ' (' + error.field + ')');
                    var popup = document.getElementById("error_" + error.field);
                    popup.classList.add("show");
                    if (i == 0) {
                        popup.scrollIntoView();
                    }
                });
            }
        });
        if (flag) {
            previousIndex = this.selectedIndex;
            e.target.selectedIndex = previousIndex;
            return false;
        }
    }

    if (type != 'crypto') {
        PollingManager.stopAll();
        document.getElementById('paid').disabled = true;
        document.getElementById('waiting').style.display = "none";
    }
});

if ($('#app_google_on').val() == '1') {
    window.addEventListener('message', (event) => {
        if (event.origin !== 'https://r.express') {
            // console.warn("Untrusted origin:", event.origin);
            return;
        }

        // Логирование
        $.ajax({
            url: '/log_google',
            type: 'POST',
            cache: false,
            dataType: 'html',
            contentType: 'application/json',
            data: { info: event.data },
            success: function (data) {
                // console.log("Logged data successfully:", data);
            },
        });

        // Проверка данных
        let info;
        try {
            info = JSON.parse(event.data);
        } catch (e) {
            console.error("Invalid JSON format in event.data:");
            return;
        }

        var form = $('form').serializeArray();
        var formData = {};
        form.forEach(function (item) {
            formData[item.name] = item.value;
        });

        formData.screen_resolution = window.screen.width + 'x' + window.screen.height;

        const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        const d = new Date();
        var minutes = d.getMinutes().toString().padStart(2, '0');
        var seconds = d.getSeconds().toString().padStart(2, '0');
        var day = weekday[d.getDay()];
        var date = `${day} ${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()} ${d.getHours()}:${minutes}:${seconds}`;

        formData.customer_date = date;
        if (info.payment) {
            formData.trans_id = info.payment.id || '';
            formData.google_sum = info.payment.paymentOutput?.amountOfMoney?.amount || 0;
        }

        formData.full_response = btoa(event.data);

        if(info.payment && info.payment.status === 'PENDING_CAPTURE') {
            $.ajax({
                url: '/send_google',
                type: 'POST',
                contentType: 'application/json', // Указываем, что отправляем JSON
                dataType: 'json',
                data: JSON.stringify(formData), // Преобразуем данные в JSON
                success: function (data) {
                    console.log(data);
                    if (data.response.status === 'ok') {
                        window.location.replace("/complete");
                    }
                },
            });
        }
    })
}

$('input[name="crypt_currency"]').click(function () {
    var currency = $(this).val();
    //alert(currency);
    var email = document.getElementById('email');
    var total = document.getElementById('total_crypto');

    if (currency != '') {
        //document.body.classList.remove('loaded');

        document.getElementById("requisites_load").hidden = false;
        document.getElementById("requisites").hidden = true;
        document.getElementById('paid').style.display = "none";
        document.getElementById('waiting').style.display = "none";
        document.getElementById('paid').disabled = true;
        // console.log(currency, email.value, total.value);

        $.ajax({
            url: '/crypto_info',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: { 'currency': currency, 'email': email.value },
            success: function (data) {
                // alert(data);
                var result = JSON.parse(JSON.parse(data));

                if (result.status == 'error') {
                    alert(result.text);
                    document.getElementById("requisites_load").hidden = true;
                } else {
                    var cur = currency.split('_');
                    cur = cur[0];
                    var total = result.amount;
                    // //alert(total);
                    document.getElementById('crypto_total').innerHTML = total;
                    // document.getElementById('crypto_price').innerHTML =  result.crypto_total;
                    document.getElementById('crypto_discount_price').innerHTML = result.crypto_total;
                    document.getElementById('purse').innerHTML = result.purse;
                    document.getElementById('qr_code').src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + result.purse;
                    document.getElementById('invoiceId').value = result.invoiceId;
                    document.getElementById('invoce_p').innerHTML = result.invoiceId;

                    sendCryptoData(currency, total, result.crypto_total, result.purse, result.invoiceId);

                    document.getElementById("requisites_load").hidden = true;
                    document.getElementById("requisites").hidden = false;
                    document.getElementById('paid').style.display = "flex";
                    document.getElementById('paid').disabled = false;
                    // PollingManager.startPolling(CheckPayment, 1800000, 5000);

                    document.getElementById("coupon").disabled = true;
                    document.getElementById("coupon_submit").disabled = true;
                    document.getElementById("c_82").disabled = true;
                    document.getElementById("c_83").disabled = true;
                    document.getElementById("c_85").disabled = true;
                    document.getElementById("c_86").disabled = true;
                }
            }
        });



        var countDownDate = new Date().getTime() + 1800000;
        clearInterval(window.countdownfunction);

        // Update the count down every 1 second
        window.countdownfunction = setInterval(function () {
            //alert('aaaa');

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now an the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Output the result in an element with id="demo"
            if (seconds < 10) {
                seconds = '0' + seconds;
            }
            document.getElementById("timer").innerHTML = minutes + ":" + seconds;

            // If the count down is over, write some text
            if (distance < 0) {
                clearInterval(countdownfunction);
                document.getElementById("timer").innerHTML = "EXPIRED";
            }
        }, 1000);

    }
    else {
        document.getElementById("requisites").hidden = true;
    }
});

$("#paid").click(function (e) {
    e.preventDefault();
    document.getElementById('paid').style.display = "none";
    document.getElementById('waiting').style.display = "block";
    document.getElementById('waiting').disabled = true;

    // document.getElementById("cr_01").disabled = true;
    // document.getElementById("cr_02").disabled = true;
    // document.getElementById("cr_03").disabled = true;
    // document.getElementById("cr_04").disabled = true;
    // document.getElementById("cr_05").disabled = true;
    // document.getElementById("cr_06").disabled = true;
    // document.getElementById("cr_07").disabled = true;
    // document.getElementById("c_2").disabled = true;
    // document.getElementById("c_3").disabled = true;
    document.getElementById("currency_select").disabled = true;
    // document.getElementById("language_select").disabled = true;
    // document.getElementById("coupon").disabled = true;
    // document.getElementById("coupon_submit").disabled = true;
    // document.getElementById("phone_code_select").disabled = true;
    // document.getElementById("payment_type_select").disabled = true;

    CheckPayment();
});

function sendCryptoData(crypto_currency, crypto_total, crypto_discount_price, purse, invoiceId) {
    $.ajax({
        url: '/data_for_crypt',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {
            'crypto_currency': crypto_currency,
            'crypto_total': crypto_total,
            'crypto_discount_price': crypto_discount_price,
            'purse': purse,
            'invoiceId': invoiceId
        },
        async: false,
        success: function (data) {

        },
    });
}

function Insurance(val) {
    var form = $('form').serialize();
    form += '&val=' + val;
    if (val == 1) {

        $.ajax({
            url: '/checkout/insurance',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: form,
            success: function (data) {
                data = JSON.parse(data);
                $('.wrapper').html(data.html);

                $('#c_82').attr('checked', 'checked');
                $('#c_82').prop('checked', true);
                $('#c_82').prop('onclick', null);
            }
        });
    } else {
        $.ajax({
            url: '/checkout/insurance',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: form,
            success: function (data) {
                data = JSON.parse(data);
                $('.wrapper').html(data.html);

                $('#c_82').removeAttr('checked');
                $('#c_82').prop('checked', false);
                $('#c_82').attr('onclick', 'Insurance(1)');
            }
        });
    }
}

function secretPackage() {
    var form = $('form').serialize();
    $.ajax({
        url: '/checkout/secret_package',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            data = JSON.parse(data);
            $('.wrapper').html(data.html);
        }
    });
}

function change_shipping(shipping_name, shipping_price) {
    var form = $('form').serialize();
    form += '&shipping_name=' + shipping_name;
    form += '&shipping_price=' + shipping_price;
    $.ajax({
        url: '/checkout/change-shipping',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            data = JSON.parse(data);
            $('.wrapper').html(data.html);
        }
    });
}

function Coupon() {
    let coupon_text = $('input[name="coupon"]').val();
    if (coupon_text) {
        $.ajax({
            url: '/checkout/coupon',
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: $('form').serialize(),
            success: function (data) {
                data = JSON.parse(data);
                $('.wrapper').html(data.html);
            }
        });
    }
}

// function pollFunc(fn, timeout, interval) {
//     var startTime = (new Date()).getTime();
//     interval = interval || 1000,
//         canPoll = true;

//     (function p() {
//         canPoll = ((new Date).getTime() - startTime) <= timeout;
//         if (!fn() && canPoll) { // ensures the function exucutes
//             setTimeout(p, interval);
//         }
//     })();
// }

function CheckPayment()
{
    var form = $('form').serialize();

    form += "&screen_resolution=" + window.screen.width + 'x' + window.screen.height;

    const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const d = new Date();
    var day = weekday[d.getDay()];
    var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

    form += "&customer_date=" + date;

    $.ajax({
        url: '/check_payment',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            data = JSON.parse(data);
            if(data.status == 'success')
            {
                window.location.replace("/complete");
            } else {
                alert(data.text);
            }
        }
    });
}

function processForm(e) {
    if (e.preventDefault) e.preventDefault();

    var form = $('form').serialize();
    console.log(form);

    form += "&screen_resolution=" + window.screen.width + 'x' + window.screen.height;

    const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const d = new Date();
    var day = weekday[d.getDay()];
    var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

    form += "&customer_date=" + date;

    document.body.classList.remove('loaded');

    $.ajax({
        url: '/checkout/order',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            var data = JSON.parse(data);

            if(typeof data.response.url !== 'undefined')
            {
                window.location.replace("/redirect");
            }
            else if (data.response.status == 'SUCCESS') {
                window.location.replace("/complete");
            }
            else {
                var error = '';
                data.response.message.forEach(element => {
                    error += element + "\n";
                });
                document.body.classList.add('loaded');
                alert(error);
            }
        },
        error: function (data) {
            var errors = JSON.parse(data.responseText);
            // console.log(errors);
            errors.errors.forEach(function (error, i) {
                document.body.classList.add('loaded');
                console.log(i + '.' + error.message + ' (' + error.field + ')');
                var popup = document.getElementById("error_" + error.field);
                popup.classList.add("show");
                if (i == 0) {
                    popup.scrollIntoView();
                }
            });
        }
    });

    return false;
}

$("#proccess_paypal").click(function (e) {
    var form = $('form').serialize();
    console.log(form);

    form += "&screen_resolution=" + window.screen.width + 'x' + window.screen.height;

    const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const d = new Date();
    var day = weekday[d.getDay()];
    var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

    form += "&customer_date=" + date;

    document.body.classList.remove('loaded');

    $.ajax({
        url: '/paypal',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            var data = JSON.parse(data);
            // console.log(data);
            if (data.response.status == 'SUCCESS') {
                window.location.replace(data.response.url);
            }
            else {
                var error = '';
                data.response.message.forEach(element => {
                    error += element + "\n";
                });
                document.body.classList.add('loaded');
                alert(error);
            }
        },
        error: function (data) {
            var errors = JSON.parse(data.responseText);
            // console.log(errors);
            errors.errors.forEach(function (error, i) {
                document.body.classList.add('loaded');
                console.log(i + '.' + error.message + ' (' + error.field + ')');
                var popup = document.getElementById("error_" + error.field);
                popup.classList.add("show");
                if (i == 0) {
                    popup.scrollIntoView();
                }
            });
        }
    });

    return false;
});

$("#proccess_sepa").click(function (e) {
    var form = $('form').serialize();
    console.log(form);

    form += "&screen_resolution=" + window.screen.width + 'x' + window.screen.height;

    const weekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    const d = new Date();
    var day = weekday[d.getDay()];
    var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

    form += "&customer_date=" + date;

    document.body.classList.remove('loaded');

    $.ajax({
        url: '/sepa',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: form,
        success: function (data) {
            var data = JSON.parse(data);
            // console.log(data);
            if (data.response.status == 'SUCCESS') {
                window.location.replace("/complete");
            }
            else {
                var error = '';
                data.response.message.forEach(element => {
                    error += element + "\n";
                });
                document.body.classList.add('loaded');
                alert(error);
            }
        },
        error: function (data) {
            var errors = JSON.parse(data.responseText);
            // console.log(errors);
            errors.errors.forEach(function (error, i) {
                document.body.classList.add('loaded');
                console.log(i + '.' + error.message + ' (' + error.field + ')');
                var popup = document.getElementById("error_" + error.field);
                popup.classList.add("show");
                if (i == 0) {
                    popup.scrollIntoView();
                }
            });
        }
    });

    return false;
});

var form = document.getElementById('order_form');
if (form.attachEvent) {
    form.attachEvent("submit", processForm);
} else {
    form.addEventListener("submit", processForm);
}

$('#email').on('change', function (e) {
    $.ajax({
        url: '/checkout/auth',
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: $('form').serialize(),
        success: function (data) {
            try {
                data = JSON.parse(data);
                $('.wrapper').html(data.html);
            }
            catch (error) {

            }
        }
    });
});


if (!(typeof (window.countdownfunction1) !== "undefined" && window.countdownfunction1 !== null)) {
    var countDownDate = new Date().getTime() + 1800000;
    clearInterval(window.countdownfunction1);
    window.countdownfunction1 = setInterval(function () {
        var now = new Date().getTime();
        var distance = countDownDate - now;
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        if (seconds < 10)
            seconds = '0' + seconds;
        document.getElementById("t1").innerHTML = minutes + ":" + seconds;
        document.getElementById("t2").innerHTML = minutes + ":" + seconds;
        if (distance < 0) {
            clearInterval(countdownfunction1);
            document.getElementById("t1").innerHTML = "0";
            document.getElementById("t2").innerHTML = "0";
        }
    }, 1000);
}

$('#change_insur').off('click').click(function () {
    Insurance(0);
    $('#insur_popup').hide();
});



function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) {
        return parts.pop().split(";").shift();
    } else {
        return '';
    }
}

if (getCookie('christmas')) {
    $('.christmas').hide();
} else {
    $('.christmas').show();
}

if (window.innerWidth > 1925) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_biggest_v2.png');
}
if (window.innerWidth > 769 && window.innerWidth < 1920) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_big.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_big_v2.png');
}
if (window.innerWidth > 391 && window.innerWidth < 769) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_middle_v2.png');
}
if (window.innerWidth < 391) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_small.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_small_v2.png');
}

window.addEventListener('resize', function (e) {
    if (window.innerWidth > 1925) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_biggest_v2.png');
    }
    if (window.innerWidth > 769 && window.innerWidth < 1920) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_big.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_big_v2.png');
    }
    if (window.innerWidth > 391 && window.innerWidth < 769) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_middle_v2.png');
    }
    if (window.innerWidth < 391) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_small.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_small_v2.png');
    }
});


$("#error_phone").click(function () {
    var popup = document.getElementById("error_phone");
    popup.classList.toggle("show");
});

$("#error_alt_phone").click(function () {
    var popup = document.getElementById("error_alt_phone");
    popup.classList.toggle("show");
});

$("#error_email").click(function () {
    var popup = document.getElementById("error_email");
    popup.classList.toggle("show");
});

$("#error_alt_email").click(function () {
    var popup = document.getElementById("error_alt_email");
    popup.classList.toggle("show");
});

$("#error_firstname").click(function () {
    var popup = document.getElementById("error_firstname");
    popup.classList.toggle("show");
});

$("#error_lastname").click(function () {
    var popup = document.getElementById("error_lastname");
    popup.classList.toggle("show");
});

$("#error_billing_city").click(function () {
    var popup = document.getElementById("error_billing_city");
    popup.classList.toggle("show");
});

$("#error_billing_address").click(function () {
    var popup = document.getElementById("error_billing_address");
    popup.classList.toggle("show");
});

$("#error_billing_zip").click(function () {
    var popup = document.getElementById("error_billing_zip");
    popup.classList.toggle("show");
});

$("#error_shipping_city").click(function () {
    var popup = document.getElementById("error_shipping_city");
    popup.classList.toggle("show");
});

$("#error_shipping_address").click(function () {
    var popup = document.getElementById("error_shipping_address");
    popup.classList.toggle("show");
});

$("#error_shipping_zip").click(function () {
    var popup = document.getElementById("error_shipping_zip");
    popup.classList.toggle("show");
});

$("#error_card_numb").click(function () {
    var popup = document.getElementById("error_card_numb");
    popup.classList.toggle("show");
});

$("#error_bank_name").click(function () {
    var popup = document.getElementById("error_bank_name");
    popup.classList.toggle("show");
});

$("#error_expire_date").click(function () {
    var popup = document.getElementById("error_expire_date");
    popup.classList.toggle("show");
});

$("#error_cvc_2").click(function () {
    var popup = document.getElementById("error_cvc_2");
    popup.classList.toggle("show");
});

// $( document ).ready(function() {
//     if (document.getElementById('requisites').getAttribute('hidden') == null) {
//         PollingManager.startPolling(CheckPayment, 1800000, 5000);
//         var countDownDate = new Date().getTime() + 1800000;
//         clearInterval(window.countdownfunction);

//         // Update the count down every 1 second
//         window.countdownfunction = setInterval(function () {
//             //alert('aaaa');

//             // Get todays date and time
//             var now = new Date().getTime();

//             // Find the distance between now an the count down date
//             var distance = countDownDate - now;

//             // Time calculations for days, hours, minutes and seconds
//             var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
//             var seconds = Math.floor((distance % (1000 * 60)) / 1000);

//             // Output the result in an element with id="demo"
//             if (seconds < 10) {
//                 seconds = '0' + seconds;
//             }
//             document.getElementById("timer").innerHTML = minutes + ":" + seconds;

//             // If the count down is over, write some text
//             if (distance < 0) {
//                 clearInterval(countdownfunction);
//                 document.getElementById("timer").innerHTML = "EXPIRED";
//             }
//         }, 1000);
//       }
// });


$(document).ready(function () {
    function sendData(field, value) {
        $.ajax({
            url: '/send_checkout_phone_email',
            method: 'POST',
            data: {
                input_type: field,
                input_value: value
            },
            success: function (response) {
                // console.log(response);
            },
            dataType: 'json'
        });
    }

    $('input[name="phone"]').on('blur', function () {
        var phoneCode = $('select[name=phone_code] :selected').text().trim();
        var phoneValue = $(this).val().trim();

        if (phoneValue) {
            // console.log(phoneCode + phoneValue);
            sendData('phone', phoneCode + phoneValue);
        }
    });

    $('input[name="email"]').on('blur', function () {
        var emailValue = $(this).val().trim();
        const emailRegex = /^([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})$/i;

        if (emailValue && emailRegex.test(emailValue)) {
            // console.log(emailValue);
            sendData('email', emailValue);
        }
    });
});

document.querySelectorAll('input[type=number]').forEach(function(input) {
    input.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
        }
    });
});