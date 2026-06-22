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