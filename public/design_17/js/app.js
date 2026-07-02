function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) {
        return parts.pop().split(";").shift();
    } else {
        return '';
    }
}

function containsOnlyDigits(str) {
    const pattern = /^\d+$/; // Регулярное выражение для проверки только цифр
    return pattern.test(str);
}

if (location.pathname != '/'){
    $('.main_bestsellers').removeClass('is-open');
    $('.main_bestsellers').removeAttr('open');
}

$(document).on('click', '.button_close_message', function () {
    $('.popup_gray').hide();
    $('body').css({'overflow':'auto'});
});

$(document).on('click', '.popup_white .close_popup', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

if (getCookie('hide_push') != '' || Notification.permission === 'denied' || getCookie('user_push') != '' || $('#is_pwa_here').val() == 0 || $('#subsc_popup').val() == 0) {
    $('.popup_white').hide();
} else {
    setTimeout(function(){
        $('.popup_white').css('display', 'flex');
    }, 5000);
}

$(document).on('click', '.push_decline', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

$(document).on('click', '.push_allow', async function () {
    $('.popup_white').hide();

    const date = new Date();
    date.setDate(date.getDate() + 900);
    document.cookie = 'hide_push=1; path=/; expires=' + date.toUTCString();

    if (
        typeof window.allowPushSubscribeFromClick === 'function' &&
        typeof window.enableNotif === 'function'
    ) {
        window.allowPushSubscribeFromClick();
        await window.enableNotif();
    } else {
        console.error('[PUSH] enableNotif is not available');
    }
});

$(document).on('click', '.button_close', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

if ($('#order_info_session').val()) {
    $.ajax({
        url: routeSavePush,
        type: "POST",
        data: {
            method: 'update_customer',
            user_push: getCookie('user_push') ? getCookie('user_push') : '',
            order_info: $('#order_info_session').val(),
        },
        dataType: "json",
        success: function (res) {
            console.log('ok');
        }
    });
}

$(document).on('click', '.button_request_call', function () {
    let phone_code = $('.iti__selected-dial-code').text();
    let number = $('#callback-phone').val().replace(/[\s()\-]/g, '');

    if (number && containsOnlyDigits(number)) {
        $.ajax({
            url: routeRequestCall,
            type: "POST",
            cache: false,
            data: {phone: phone_code+number},
            dataType: "json",
            success: function (res) {
                if (res['status'] == 'success') {
                    $('.popup_bottom').hide();
                    $('[data-dialog="call"] .dialog__header').hide();
                    $('[data-dialog="call"] .callback-form').hide();

                    const mesa = document.querySelector('.message_sended');
                    mesa.classList.remove('hidden');
                    mesa.classList.add('active');
                } else {
                    alert(res['text']);
                }
            }
        });
    }
});

$(document).on('click', '.button_sub', function () {
    let email = $('[name="subscribe-email"]').val();

    if (email) {
        $.ajax({
            url: routeRequestSubscribe,
            type: "POST",
            cache: false,
            data: {email: email},
            dataType: "json",
            success: function (res) {
                if (res['status'] == 'error') {
                    alert(res['text']);
                } else {
                    $('.popup_gray').show();
                    $('.popup_bottom').hide();

                    const mesa = document.querySelector('.message_sended');
                    mesa.classList.remove('hidden');
                    mesa.classList.add('active');

                    setTimeout(function(){
                        $('.popup_gray').hide();
                        $('body').css({'overflow':'auto'});
                    }, 5000);
                }
            }
        });
    }
});

function sendAjaxContact() {
    var error = false;
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    const name = document.getElementById("contact-name").value;
    if (name.length === 0) {
        document.getElementById("contact-name").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-name").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const email = document.getElementById("contact-email").value;
    if (!email.match(validRegex) || email.length === 0) {
        document.getElementById("contact-email").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-email").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const subject_id = $('#subject_text').val();
    const message = document.getElementById("contact-message").value;

    const captcha = document.getElementById("contact-captcha").value;

    if (captcha.length === 0) {
        document.getElementById("contact-captcha").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-captcha").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const submit = true;

    if (subject_id == 0) {
        alert($('#error_subject').val());
        error = true;
    }

    if (!error) {
        $.ajax({
            url: routeRequestContactUs,
            type: "POST",
            cache: false,
            data: {
                'name' : name,
                'email' : email,
                'subject' : subject_id,
                'message' : message,
                'captcha' : captcha,
                'submit' : submit
            },
            dataType: "json",
            success: function(data) { //Данные отправлены успешно
                if (data['status'] == 'error') {
                    alert(data['text']);
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {

                    $(".contact-form").hide();
                    $(".main__heading").hide();
                    $('.h1').hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
        	}
 	    });
    }
}

function sendAjaxAffiliate() {
    var error = false;
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    const name = document.getElementById("affiliate-name").value;
    if (name.length === 0) {
        document.getElementById("affiliate-name").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("affiliate-name").style.borderColor = "rgba(255, 255, 255, 10%)";
    }
    const email = document.getElementById("affiliate-email").value;
    if (!email.match(validRegex) || email.length === 0) {
        document.getElementById("affiliate-email").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("affiliate-email").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const jabber = document.getElementById("affiliate-messenger").value;
    const message = document.getElementById("affiliate-message").value;
    const captcha = document.getElementById("affiliate-captcha").value;

    if (captcha.length === 0) {
        document.getElementById("contact-captcha").style.borderColor = "red";
        error = true;
    } else {
        document.getElementById("contact-captcha").style.borderColor = "rgba(255, 255, 255, 10%)";
    }

    const submit = true;

    if (!error) {
        $.ajax({
            url:     routeRequestAffiliate,
            type:     "POST",
            cache: false,
            data: {
                'name' : name,
                'email' : email,
                'jabber' : jabber,
                'message' : message,
                'captcha' : captcha,
                'submit' : submit
            },
            dataType: "json",
            success: function(data) { //Данные отправлены успешно
                if (data['status'] == 'error') {
                    alert(data['text']);
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {

                    $(".affiliate-form").hide();
                    $(".main__heading").hide();
                    $('.h1').hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
        	}
     	});
    }
}

if (flagc) {
    setTimeout(function() {
        $('.modal_cart').removeClass('hidden').css('opacity', 1);
        $('.modal_cart').css('z-index', 10);
    }, 2000);

    setTimeout(function() {
        $('.modal_cart').css('opacity', 0);
    }, 5000);

    setTimeout(function() {
        $('.modal_cart').css('z-index', 0);
        $('.modal_cart').addClass('hidden');
    }, 10000);
}
if (flagp) {
    setTimeout(function() {
        $('.cmcmodal').removeClass('hidden').css('opacity', 1);
        $('.cmcmodal').css('z-index', 10);
    }, 2000);

    setTimeout(function() {
        $('.cmcmodal').css('opacity', 0);
    }, 5000);

    setTimeout(function() {
        $('.cmcmodal').css('z-index', 0);
        $('.cmcmodal').addClass('hidden');
    }, 10000);
}

$(document).on('click', '.select_header_gift', function () {
    $(this).parent().toggleClass('is-active');
});

$(document).on('click', '.select_item_gift', function () {
    $('.select_current_gift').text($(this).text());
    $('.select_current_gift').attr('curr_packaging_id', $(this).attr('packaging_id'));
    $(this).parent().parent().removeClass('is-active');
});

$('.more').on('click', function (e){
    e.preventDefault();
    let block = $(this).parents('.text-box').find('.text');
    block.toggleClass('active');
    if($(this).text() == 'view all') {
        $(this).html('hide');
        block.css({'display': 'flex', 'flex-wrap': 'wrap', 'gap': '5px'});
    } else {
        $(this).html('view all');
        block.css({'display': '-webkit-box'});
    }
});

if (getCookie('christmas')) {
    $('.christmas').hide();
} else {
    $('.christmas').show();
}

$(document).on('click', '.christmas', function () {
    $(this).hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'christmas=1; path=/; expires=' + date;
});

function changeShippingFromSelect(select) {
    const selectedOption = select.options[select.selectedIndex];

    if (!selectedOption) {
        return;
    }

    const shippingType = selectedOption.value;
    const shippingPrice = Number(selectedOption.dataset.price || 0);

    change_shipping(shippingType, shippingPrice);
}

function changeBonusFromSelect(select) {
    const selectedOption = select.options[select.selectedIndex];

    if (!selectedOption) {
        return;
    }

    const packId = Number(selectedOption.value || 0);
    const price = Number(selectedOption.dataset.price || 0);

    change_bonus(packId, price);
}

(function () {
    function renderOption({ text, flag, image, label, caption }) {
        const hasMeta = image || flag || label;

        return `
            <span class="cs-text">${text}</span>
            ${
                hasMeta
                    ? `<span class="cs-meta">
                        ${
                            image
                                ? `<img src="${image}" class="cs-image" alt="">`
                                : flag
                                    ? `<span data-flag="${flag}" class="cs-flag"></span>`
                                    : ''
                        }
                        ${label ? `<span class="cs-label">${label}</span>` : ''}
                    </span>`
                    : ''
            }
            ${caption ? `<span class="cs-caption">${caption}</span>` : ''}
        `;
    }

    function setOrRemoveData(element, key, value) {
        if (!element) return;

        if (value) {
            element.dataset[key] = value;
        } else {
            delete element.dataset[key];
        }
    }

    function getSelectSignature(select) {
        return Array.from(select.options).map(function (option) {
            return [
                option.value,
                option.textContent.trim(),
                option.dataset.flag || '',
                option.dataset.image || '',
                option.dataset.label || '',
                option.dataset.caption || '',
                option.selected ? '1' : '0',
            ].join('|');
        }).join('||');
    }

    function getInstanceFromSelect(select) {
        if (select.customSelect) {
            return select.customSelect;
        }

        const container = select.closest('.custom-select-container');

        if (!container) {
            return null;
        }

        return {
            select: select,
            container: container,
            opener: container.querySelector('.custom-select-opener'),
            panel: container.querySelector('.custom-select-panel'),
        };
    }

    function updateOpener(instance) {
        const select = instance.select;
        const selected = select.options[select.selectedIndex] || select.options[0];

        if (!selected || !instance.opener) {
            return;
        }

        const text = selected.textContent.trim();
        const flag = selected.dataset.flag || '';
        const image = selected.dataset.image || '';
        const label = selected.dataset.label || '';
        const caption = selected.dataset.caption || '';

        setOrRemoveData(instance.opener, 'flag', flag);
        setOrRemoveData(instance.opener, 'image', image);
        setOrRemoveData(instance.opener, 'label', label);
        setOrRemoveData(instance.opener, 'caption', caption);

        instance.opener.innerHTML = `<span class="cs-opener-wrapper">${renderOption({
            text,
            flag,
            image,
            label,
            caption,
        })}</span>`;
    }

    function enhanceCustomSelect(instance) {
        if (!instance || !instance.select || !instance.opener || !instance.panel) {
            return;
        }

        const select = instance.select;
        const signature = getSelectSignature(select);

        if (select.dataset.customSelectImageSignature === signature) {
            return;
        }

        select.dataset.customSelectImageSignature = signature;

        const options = select.querySelectorAll('option');
        const items = instance.panel.querySelectorAll('.custom-select-option');

        options.forEach(function (option, index) {
            const item = items[index];

            if (!item) {
                return;
            }

            const text = option.textContent.trim();
            const flag = option.dataset.flag || '';
            const image = option.dataset.image || '';
            const label = option.dataset.label || '';
            const caption = option.dataset.caption || '';

            setOrRemoveData(item, 'flag', flag);
            setOrRemoveData(item, 'image', image);
            setOrRemoveData(item, 'label', label);
            setOrRemoveData(item, 'caption', caption);

            item.innerHTML = `<span class="cs-option-wrapper">${renderOption({
                text,
                flag,
                image,
                label,
                caption,
            })}</span>`;
        });

        updateOpener(instance);

        if (!select.dataset.customSelectImageChangeBound) {
            select.addEventListener('change', function () {
                delete select.dataset.customSelectImageSignature;
                enhanceCustomSelect(instance);
            });

            select.dataset.customSelectImageChangeBound = '1';
        }
    }

    function canInitSelect(select) {
        return Array.from(select.children).every(function (child) {
            return child.tagName === 'OPTION' || child.tagName === 'OPTGROUP';
        });
    }

    function initAndEnhanceSelect(select) {
        if (!select || select.dataset.customSelectPatchBusy === '1') {
            return;
        }

        let instance = getInstanceFromSelect(select);

        if (instance) {
            enhanceCustomSelect(instance);
            return;
        }

        if (typeof customSelect === 'undefined' || !canInitSelect(select)) {
            return;
        }

        select.dataset.customSelectPatchBusy = '1';

        const uid = 'patched-select-' + Date.now() + '-' + Math.random().toString(16).slice(2);
        select.classList.add(uid);

        const instances = customSelect('select.' + uid);

        instances.forEach(function (newInstance) {
            enhanceCustomSelect(newInstance);
        });

        select.classList.remove(uid);
        delete select.dataset.customSelectPatchBusy;
    }

    function refreshSelects(root) {
        const container = root || document;

        const selects = container.matches && container.matches('select.select')
            ? [container]
            : container.querySelectorAll('select.select');

        selects.forEach(function (select) {
            initAndEnhanceSelect(select);
        });
    }

    window.refreshCustomSelectImages = refreshSelects;

    document.addEventListener('DOMContentLoaded', function () {
        const shoppingCart = document.querySelector('#shopping_cart');

        if (!shoppingCart) {
            return;
        }

        refreshSelects(shoppingCart);

        let timer = null;

        const observer = new MutationObserver(function () {
            clearTimeout(timer);

            timer = setTimeout(function () {
                refreshSelects(shoppingCart);
            }, 150);
        });

        observer.observe(shoppingCart, {
            childList: true,
            subtree: false
        });
    });
})();
(function ($) {
    if (!$) {
        return;
    }

    const chatPendingMinDuration = 850;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getChatMessagesBox() {
        let $thread = $('.js-chat-search-thread').first();

        if (!$thread.length) {
            $thread = $(
                '<div class="thread-chat js-chat-search-thread">' +
                    '<div class="thread-chat__container">' +
                        '<div class="thread-chat__messages js-chat-search-messages"></div>' +
                    '</div>' +
                '</div>'
            );

            const $threadBox = $('.thread-box').first();
            if ($threadBox.length) {
                $thread.insertBefore($threadBox);
            } else {
                $('.thread').first().append($thread);
            }
        }

        $thread.removeAttr('hidden');
        return $thread.find('.js-chat-search-messages').first();
    }

    function appendUserMessage($messages, text) {
        const html =
            '<div class="chat-row chat-row--user">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' + escapeHtml(text) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        $messages.append(html);
    }

    function appendPendingMessage($messages) {
        const $pending = $(
            '<div class="chat-row chat-row--agent js-chat-search-pending">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' +
                            '<svg class="chat-message__pending" width="34" height="8" viewBox="0 0 34 8" aria-hidden="true">' +
                                '<circle cx="4" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="17" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="30" cy="4" r="4" fill="currentColor"></circle>' +
                            '</svg>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        $messages.append($pending);
        return $pending;
    }

    function replaceWithError($pending) {
        $pending.replaceWith(
            '<div class="chat-row chat-row--agent">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble chat-message__bubble--agent">Search request failed. Please try again.</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function runAfterPendingDelay(startedAt, callback) {
        const delay = Math.max(0, chatPendingMinDuration - (Date.now() - startedAt));
        window.setTimeout(callback, delay);
    }

    function scrollToSearchForm() {
        const $form = $('.js-chat-search-form').first();
        if (!$form.length) {
            return;
        }

        window.setTimeout(function () {
            $form[0].scrollIntoView({behavior: 'smooth', block: 'end'});
        }, 50);
    }

    $(document).on('submit', '.js-chat-search-form', function (event) {
        if (typeof routeSearchChat === 'undefined') {
            return true;
        }

        event.preventDefault();

        const $form = $(this);
        const $input = $form.find('[name="search_text"]').first();
        const searchText = $.trim($input.val());

        if (!searchText || $form.hasClass('is-loading')) {
            return false;
        }

        const $messages = getChatMessagesBox();
        $('.js-chat-start-heading').hide();

        appendUserMessage($messages, searchText);
        const $pending = appendPendingMessage($messages);
        const pendingStartedAt = Date.now();
        scrollToSearchForm();

        $form.addClass('is-loading');
        $input.prop('disabled', true);

        $.ajax({
            url: routeSearchChat,
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: {
                search_text: searchText,
                _token: $form.find('[name="_token"]').val()
            },
            success: function (response) {
                if (response.status === 'redirect' && response.redirect) {
                    location.href = response.redirect;
                    return;
                }

                runAfterPendingDelay(pendingStartedAt, function () {
                    if (response.status === 'success' && response.html) {
                        $pending.replaceWith(response.html);
                    } else {
                        replaceWithError($pending);
                    }
                });
            },
            error: function () {
                runAfterPendingDelay(pendingStartedAt, function () {
                    replaceWithError($pending);
                });
            },
            complete: function () {
                $form.removeClass('is-loading');
                $input.prop('disabled', false).val('').trigger('focus');
                scrollToSearchForm();
            }
        });

        return false;
    });
})(window.jQuery);
(function ($) {
    if (!$) {
        return;
    }

    const productPendingMinDuration = 850;

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getChatMessagesBox() {
        let $thread = $('.js-chat-search-thread').first();

        if (!$thread.length) {
            $thread = $(
                '<div class="thread-chat js-chat-search-thread">' +
                    '<div class="thread-chat__container">' +
                        '<div class="thread-chat__messages js-chat-search-messages"></div>' +
                    '</div>' +
                '</div>'
            );

            const $threadBox = $('.thread-box').first();
            if ($threadBox.length) {
                $thread.insertBefore($threadBox);
            } else {
                $('.thread').first().append($thread);
            }
        }

        $thread.removeAttr('hidden');
        return $thread.find('.js-chat-search-messages').first();
    }

    function appendUserMessage($messages, text) {
        const html =
            '<div class="chat-row chat-row--user">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' + escapeHtml(text) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        $messages.append(html);
    }

    function appendPendingMessage($messages) {
        const $pending = $(
            '<div class="chat-row chat-row--agent js-chat-product-pending">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble">' +
                            '<svg class="chat-message__pending" width="34" height="8" viewBox="0 0 34 8" aria-hidden="true">' +
                                '<circle cx="4" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="17" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="30" cy="4" r="4" fill="currentColor"></circle>' +
                            '</svg>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        $messages.append($pending);
        return $pending;
    }

    function replaceWithBotMessage($pending, message) {
        $pending.replaceWith(
            '<div class="chat-row chat-row--agent">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble chat-message__bubble--agent">' + escapeHtml(message) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function runAfterProductPendingDelay(startedAt, callback) {
        const delay = Math.max(0, productPendingMinDuration - (Date.now() - startedAt));
        window.setTimeout(callback, delay);
    }

    function scrollToSearchForm() {
        const $form = $('.js-chat-search-form').first();
        if (!$form.length) {
            return;
        }

        window.setTimeout(function () {
            $form[0].scrollIntoView({behavior: 'smooth', block: 'end'});
        }, 50);
    }

    function extractProductContent(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        let $source = $(doc).find('main .thread').first().clone();

        if (!$source.length) {
            $source = $(doc).find('main').first().clone();
        }

        if (!$source.length) {
            return '';
        }

        $source.find('script, style, link[rel="stylesheet"]').remove();
        $source.find('.thread-box, .search-bar, .js-chat-search-thread').remove();

        const htmlContent = $.trim($source.html());

        if (!htmlContent) {
            return '';
        }

        return htmlContent;
    }

    function makeAbsoluteUrl(url) {
        try {
            return new URL(url, window.location.href).toString();
        } catch (e) {
            return url;
        }
    }

    function updateCartCounters(data) {
        if (!data || data.status !== 'success') {
            return;
        }

        if (typeof data.count !== 'undefined') {
            $('.cart__counter').text(data.count);
            $('.footer-buttons__cart').attr('data-counter', data.count);
        }

        if (typeof data.total_html !== 'undefined') {
            $('.cart__total-price').text(data.total_html);
            $('.footer-buttons__cart .button__price').text(data.total_html);
        }

        if (typeof data.items_html !== 'undefined') {
            $('.cart__body .cart-items').html(data.items_html);
        }
    }

    function refreshCartState() {
        if (typeof routeCartState === 'undefined') {
            return $.Deferred().resolve().promise();
        }

        return $.ajax({
            url: routeCartState,
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: updateCartCounters
        });
    }

    function getPackIdFromRemoveButton(button) {
        const dataPackId = button.getAttribute('data-cart-remove-pack');

        if (dataPackId) {
            return dataPackId;
        }

        const onclickValue = button.getAttribute('onclick') || '';
        const match = onclickValue.match(/remove\(([^)]+)\)/);

        if (!match) {
            return '';
        }

        return match[1].replace(/['"\s]/g, '');
    }

    function sendCartRemoveRequest($button, packId) {
        if (!packId || typeof routeCartRemove === 'undefined') {
            return;
        }

        if ($button.data('cart-remove-busy')) {
            return;
        }

        $button.data('cart-remove-busy', true).addClass('is-loading').prop('disabled', true);

        $.ajax({
            url: routeCartRemove,
            type: 'POST',
            cache: false,
            data: {
                pack_id: packId,
                id: packId,
                packaging_id: packId,
            },
            success: function () {
                refreshCartState();
            },
            error: function () {
                alert('Could not remove this product from cart. Please try again.');
            },
            complete: function () {
                $button.removeData('cart-remove-busy').removeClass('is-loading').prop('disabled', false);
            }
        });
    }

    if (document.addEventListener) {
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.cart-item__remove-button, [data-cart-remove-pack]');

            if (!button) {
                return;
            }

            const packId = getPackIdFromRemoveButton(button);

            if (!packId) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            if (typeof event.stopImmediatePropagation === 'function') {
                event.stopImmediatePropagation();
            }

            sendCartRemoveRequest($(button), packId);
        }, true);
    }

    function isCartAddUrl(url) {
        const absoluteUrl = makeAbsoluteUrl(url);
        return /\/cart\/add_pack\/[^/?#]+/.test(absoluteUrl) || /\/cart\/add\/[^/?#]+/.test(absoluteUrl);
    }

    function resolveMethod($form, fallback) {
        const method = String($form.attr('method') || fallback || 'GET').toUpperCase();
        return method === 'POST' ? 'POST' : 'GET';
    }

    function sendCartRequest(url, method, data, $trigger) {
        if (!url || !isCartAddUrl(url)) {
            return;
        }

        if ($trigger && $trigger.data('chat-cart-busy')) {
            return;
        }

        if ($trigger) {
            $trigger.data('chat-cart-busy', true).addClass('is-loading').prop('disabled', true);
        }

        const $messages = getChatMessagesBox();
        const $pending = appendPendingMessage($messages);
        const pendingStartedAt = Date.now();
        scrollToSearchForm();

        $.ajax({
            url: url,
            type: method || 'GET',
            cache: false,
            data: data || {},
            success: function () {
                refreshCartState().always(function () {
                    runAfterProductPendingDelay(pendingStartedAt, function () {
                        replaceWithBotMessage($pending, 'Added to cart. Cart counter has been updated.');
                        scrollToSearchForm();
                    });
                });
            },
            error: function () {
                runAfterProductPendingDelay(pendingStartedAt, function () {
                    replaceWithBotMessage($pending, 'Could not add this pack to cart. Please try again.');
                    scrollToSearchForm();
                });
            },
            complete: function () {
                if ($trigger) {
                    $trigger.removeData('chat-cart-busy').removeClass('is-loading').prop('disabled', false);
                }
            }
        });
    }

    function prepareInjectedProduct($productBox) {
        $productBox.find('a[href]').each(function () {
            const $link = $(this);
            const href = $link.attr('href') || '';

            if (isCartAddUrl(href)) {
                $link.addClass('js-chat-cart-add-link');
                return;
            }

            if (/\.html(?:[/?#]|$)/.test(href)) {
                $link.addClass('js-chat-product-link');
            }
        });

        if (typeof window.refreshCustomSelectImages === 'function') {
            window.refreshCustomSelectImages($productBox[0]);
        }
    }

    $(document).on('click', '.js-chat-product-link', function (event) {
        const href = $(this).attr('href');

        if (!href) {
            return;
        }

        event.preventDefault();

        const $link = $(this);
        const productTitle = $link.data('product-title') || $.trim($link.text()) || 'Open product';
        const $messages = getChatMessagesBox();

        $('.js-chat-live-suggest').remove();
        $('.js-chat-start-heading').hide();

        appendUserMessage($messages, productTitle);
        const $pending = appendPendingMessage($messages);
        const pendingStartedAt = Date.now();
        scrollToSearchForm();

        $.ajax({
            url: href,
            type: 'GET',
            cache: false,
            dataType: 'html',
            success: function (responseHtml) {
                runAfterProductPendingDelay(pendingStartedAt, function () {
                    const productHtml = extractProductContent(responseHtml);

                    if (!productHtml) {
                        replaceWithBotMessage($pending, 'Could not load product information.');
                        return;
                    }

                    $pending.replaceWith(
                        '<div class="chat-row chat-row--page chat-row--product">' +
                            '<div class="chat-message">' +
                                '<div class="chat-message__content content"></div>' +
                                '<div class="chat-message__page chat-product-detail js-chat-product-detail" data-product-url="' + escapeHtml(makeAbsoluteUrl(href)) + '">' +
                                    productHtml +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );

                    prepareInjectedProduct($('.js-chat-product-detail').last());
                    scrollToSearchForm();
                });
            },
            error: function () {
                runAfterProductPendingDelay(pendingStartedAt, function () {
                    replaceWithBotMessage($pending, 'Could not load product information.');
                    scrollToSearchForm();
                });
            }
        });
    });

    $(document).on('click', '.js-chat-product-detail a[href]', function (event) {
        const href = $(this).attr('href') || '';

        if (!isCartAddUrl(href)) {
            return;
        }

        event.preventDefault();
        sendCartRequest(href, 'GET', {}, $(this));
    });

    $(document).on('submit', '.js-chat-product-detail form', function (event) {
        const $form = $(this);
        const action = $form.attr('action') || '';

        if (!isCartAddUrl(action)) {
            return;
        }

        event.preventDefault();
        sendCartRequest(action, resolveMethod($form, 'POST'), $form.serialize(), $form.find('[type="submit"]').first());
    });
})(window.jQuery);

(function ($) {
    if (!$) {
        return;
    }

    const suggestTypingDelay = 350;
    const suggestPendingMinDuration = 850;

    let suggestTimer = null;
    let suggestRequest = null;
    let latestSuggestQuery = '';
    let suggestPendingStartedAt = 0;

    function getChatMessagesBoxForSuggest() {
        let $thread = $('.js-chat-search-thread').first();

        if (!$thread.length) {
            $thread = $(
                '<div class="thread-chat js-chat-search-thread">' +
                    '<div class="thread-chat__container">' +
                        '<div class="thread-chat__messages js-chat-search-messages"></div>' +
                    '</div>' +
                '</div>'
            );

            const $threadBox = $('.thread-box').first();
            if ($threadBox.length) {
                $thread.insertBefore($threadBox);
            } else {
                $('.thread').first().append($thread);
            }
        }

        $thread.removeAttr('hidden');
        return $thread.find('.js-chat-search-messages').first();
    }

    function renderSuggestLoading($messages) {
        $('.js-chat-live-suggest').remove();
        suggestPendingStartedAt = Date.now();

        $messages.append(
            '<div class="chat-row chat-row--agent js-chat-live-suggest">' +
                '<div class="chat-message">' +
                    '<div class="chat-message__content content">' +
                        '<div class="chat-message__bubble chat-message__bubble--agent chat-message__bubble--suggest">' +
                            '<svg class="chat-message__pending" width="34" height="8" viewBox="0 0 34 8" aria-hidden="true">' +
                                '<circle cx="4" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="17" cy="4" r="4" fill="currentColor"></circle>' +
                                '<circle cx="30" cy="4" r="4" fill="currentColor"></circle>' +
                            '</svg>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function runAfterSuggestPendingDelay(callback) {
        const delay = Math.max(0, suggestPendingMinDuration - (Date.now() - suggestPendingStartedAt));
        window.setTimeout(callback, delay);
    }

    function abortSuggestRequest() {
        if (suggestRequest && suggestRequest.readyState !== 4) {
            suggestRequest.abort();
        }
    }

    $(document).on('input', '.js-chat-search-input', function () {
        if (typeof routeSearchChatSuggest === 'undefined') {
            return;
        }

        const $input = $(this);
        const query = $.trim($input.val());

        latestSuggestQuery = query;
        clearTimeout(suggestTimer);
        abortSuggestRequest();

        if (query.length < 2) {
            $('.js-chat-live-suggest').remove();
            return;
        }

        suggestTimer = window.setTimeout(function () {
            const currentQuery = $.trim($input.val());

            if (currentQuery.length < 2) {
                $('.js-chat-live-suggest').remove();
                return;
            }

            latestSuggestQuery = currentQuery;
            $('.js-chat-start-heading').hide();

            const $messages = getChatMessagesBoxForSuggest();
            renderSuggestLoading($messages);

            suggestRequest = $.ajax({
                url: routeSearchChatSuggest,
                type: 'GET',
                cache: false,
                dataType: 'json',
                data: {
                    q: currentQuery
                },
                success: function (response) {
                    runAfterSuggestPendingDelay(function () {
                        if (currentQuery !== latestSuggestQuery) {
                            return;
                        }

                        if (response.status === 'success' && response.html) {
                            $('.js-chat-live-suggest').replaceWith(response.html);
                        } else {
                            $('.js-chat-live-suggest').remove();
                        }
                    });
                },
                error: function (xhr) {
                    if (xhr.statusText !== 'abort') {
                        runAfterSuggestPendingDelay(function () {
                            if (currentQuery === latestSuggestQuery) {
                                $('.js-chat-live-suggest').remove();
                            }
                        });
                    }
                }
            });
        }, suggestTypingDelay);
    });

    $(document).on('click', '.js-chat-suggest-item', function (event) {
        event.preventDefault();

        const title = $(this).data('title') || $.trim($(this).text());
        const $form = $('.js-chat-search-form').first();
        const $input = $form.find('[name="search_text"]').first();

        if (!$form.length || !$input.length || !title) {
            return;
        }

        clearTimeout(suggestTimer);
        abortSuggestRequest();
        $('.js-chat-live-suggest').remove();

        $input.val(title);
        $form.trigger('submit');
    });

    $(document).on('submit', '.js-chat-search-form', function () {
        clearTimeout(suggestTimer);
        abortSuggestRequest();
        $('.js-chat-live-suggest').remove();
    });
})(window.jQuery);

(function () {
    const scrollButtonSelector = '.js-chat-scroll-down';
    const messagesSelector = '.js-chat-search-messages';
    const nearBottomOffset = 180;
    const temporaryButtonDuration = 2600;
    const newAnswerClassDuration = 2400;

    let hasUnreadAnswer = false;
    let hideTimer = null;
    let observer = null;

    function getScrollButton() {
        return document.querySelector(scrollButtonSelector);
    }

    function isNearPageBottom() {
        const doc = document.documentElement;
        const body = document.body;
        const scrollTop = window.pageYOffset || doc.scrollTop || body.scrollTop || 0;
        const viewportHeight = window.innerHeight || doc.clientHeight || 0;
        const pageHeight = Math.max(
            body.scrollHeight,
            body.offsetHeight,
            doc.clientHeight,
            doc.scrollHeight,
            doc.offsetHeight
        );

        return scrollTop + viewportHeight >= pageHeight - nearBottomOffset;
    }

    function showScrollButton(isNewAnswer) {
        const button = getScrollButton();

        if (!button) {
            return;
        }

        const textNode = button.querySelector('.chat-scroll-down__text');

        if (textNode) {
            textNode.textContent = isNewAnswer ? 'New answer' : 'Scroll down';
        }

        button.hidden = false;
        button.classList.add('is-visible');

        if (isNewAnswer) {
            button.classList.add('has-new-answer');
        } else {
            button.classList.remove('has-new-answer');
        }
    }

    function hideScrollButton() {
        const button = getScrollButton();

        if (!button) {
            return;
        }

        button.classList.remove('is-visible', 'has-new-answer');
        window.setTimeout(function () {
            if (!button.classList.contains('is-visible')) {
                button.hidden = true;
            }
        }, 220);
    }

    function updateScrollButtonVisibility() {
        if (isNearPageBottom()) {
            hasUnreadAnswer = false;
            hideScrollButton();
            return;
        }

        showScrollButton(hasUnreadAnswer);
    }

    function getPageBottomScrollTop() {
        const doc = document.documentElement;
        const body = document.body;
        const scrollingElement = document.scrollingElement || doc || body;
        const viewportHeight = window.innerHeight || doc.clientHeight || 0;
        const scrollHeight = Math.max(
            scrollingElement ? scrollingElement.scrollHeight : 0,
            body ? body.scrollHeight : 0,
            doc ? doc.scrollHeight : 0
        );

        return Math.max(0, scrollHeight - viewportHeight);
    }

    function scrollWindowToBottom(behavior) {
        const scrollBehavior = behavior || 'smooth';
        const scrollOptions = {
            top: getPageBottomScrollTop(),
            left: 0,
            behavior: scrollBehavior
        };

        try {
            window.scrollTo(scrollOptions);
        } catch (e) {
            window.scrollTo(0, scrollOptions.top);
        }
    }

    function scrollToLatestAnswer(behavior) {
        if (!document.querySelector(messagesSelector) && !document.querySelector('.js-chat-search-form')) {
            return;
        }

        hasUnreadAnswer = false;

        window.requestAnimationFrame(function () {
            scrollWindowToBottom(behavior);
        });

        window.setTimeout(function () {
            scrollWindowToBottom('auto');
            updateScrollButtonVisibility();
        }, 520);
    }

    function isPendingRow(row) {
        return row.classList.contains('js-chat-search-pending') ||
            row.classList.contains('js-chat-product-pending') ||
            row.querySelector('.chat-message__pending');
    }

    function isAnswerRow(row) {
        return row.classList.contains('chat-row') &&
            !isPendingRow(row) &&
            (
                row.classList.contains('chat-row--agent') ||
                row.classList.contains('chat-row--page') ||
                row.classList.contains('chat-row--results') ||
                row.classList.contains('chat-row--product') ||
                row.classList.contains('js-chat-live-suggest') ||
                row.classList.contains('js-chat-search-answer')
            );
    }

    function collectAddedAnswerRows(node, rows) {
        if (!node || node.nodeType !== 1) {
            return;
        }

        if (isAnswerRow(node)) {
            rows.push(node);
            return;
        }

        node.querySelectorAll('.chat-row').forEach(function (row) {
            if (isAnswerRow(row)) {
                rows.push(row);
            }
        });
    }

    function markNewAnswerRows(rows) {
        let firstLabeledRow = null;

        rows.forEach(function (row) {
            if (row.dataset.chatNewAnswerHandled === '1') {
                return;
            }

            row.dataset.chatNewAnswerHandled = '1';
            row.classList.add('is-chat-new-answer');

            if (!firstLabeledRow) {
                firstLabeledRow = row;
                row.classList.add('is-chat-new-answer-label');
            }

            window.setTimeout(function () {
                row.classList.remove('is-chat-new-answer', 'is-chat-new-answer-label');
            }, newAnswerClassDuration);
        });

        if (!rows.length) {
            return;
        }

        hasUnreadAnswer = true;
        showScrollButton(true);

        clearTimeout(hideTimer);
        hideTimer = window.setTimeout(function () {
            if (isNearPageBottom()) {
                hasUnreadAnswer = false;
                updateScrollButtonVisibility();
            }
        }, temporaryButtonDuration);
    }

    function watchMessagesBox(messagesBox) {
        if (!messagesBox || observer) {
            return;
        }

        observer = new MutationObserver(function (mutations) {
            const rows = [];

            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    collectAddedAnswerRows(node, rows);
                });
            });

            markNewAnswerRows(rows);
        });

        observer.observe(messagesBox, {
            childList: true,
            subtree: false
        });
    }

    function ensureObserver() {
        const messagesBox = document.querySelector(messagesSelector);

        if (messagesBox) {
            watchMessagesBox(messagesBox);
            return;
        }

        const thread = document.querySelector('.thread');

        if (!thread) {
            return;
        }

        const threadObserver = new MutationObserver(function () {
            const createdMessagesBox = document.querySelector(messagesSelector);

            if (!createdMessagesBox) {
                return;
            }

            watchMessagesBox(createdMessagesBox);
            threadObserver.disconnect();
        });

        threadObserver.observe(thread, {
            childList: true,
            subtree: true
        });
    }

    window.design17ChatScrollToBottom = scrollToLatestAnswer;
    window.design17ChatShowScrollButton = showScrollButton;

    document.addEventListener('click', function (event) {
        const button = event.target.closest(scrollButtonSelector);

        if (!button) {
            return;
        }

        event.preventDefault();
        scrollToLatestAnswer('smooth');
    });

    window.addEventListener('scroll', updateScrollButtonVisibility, {passive: true});
    window.addEventListener('resize', updateScrollButtonVisibility);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            ensureObserver();
            updateScrollButtonVisibility();
        });
    } else {
        ensureObserver();
        updateScrollButtonVisibility();
    }
})();
