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
        $('.popup_white').show();
    }, 5000);
}

$(document).on('click', '.push_decline', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
});

$(document).on('click', '.push_allow', function () {
    $('.popup_white').hide();
    let date = new Date;
    date.setDate(date.getDate() + 900);
    date = date.toUTCString();
    document.cookie = 'hide_push=1; path=/; expires=' + date;
    // enableNotif();
});

// if ($('#order_info_session').val()) {
//     $.ajax({
//         url: routeSavePush,
//         type: "POST",
//         data: {
//             method: 'update_customer',
//             user_push: getCookie('user_push') ? getCookie('user_push') : '',
//             order_info: $('#order_info_session').val(),
//         },
//         dataType: "json",
//         success: function (res) {
//             console.log('ok');
//         }
//     });
// }

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

if (document.documentElement.clientWidth > 1925) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
    // $('.christmas img').attr('src', 'pub_images/christmas_biggest.png');
    // $('.christmas img').attr('src', pathImagePayBiggest);
    // $('.christmas img').attr('src', pathImageBlackFridayBiggest);
    $('.christmas img').attr('src', pathImageChristmasBiggest);
    $('.checkup img').attr('src', pathImageCheckupBiggest);
}
if (document.documentElement.clientWidth > 769 && document.documentElement.clientWidth < 1920) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
    // $('.christmas img').attr('src', 'pub_images/christmas_big.png');
    // $('.christmas img').attr('src', pathImagePayBig);
    // $('.christmas img').attr('src', pathImageBlackFridayBig);
    $('.christmas img').attr('src', pathImageChristmasBig);
    $('.checkup img').attr('src', pathImageCheckupBig);
}
if (document.documentElement.clientWidth > 391 && document.documentElement.clientWidth < 769) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
    // $('.christmas img').attr('src', 'pub_images/christmas_middle.png');
    // $('.christmas img').attr('src', pathImagePayMiddle);
    // $('.christmas img').attr('src', pathImageBlackFridayMiddle);
    $('.christmas img').attr('src', pathImageChristmasMiddle);
    $('.checkup img').attr('src', pathImageCheckupMiddle);
}
if (document.documentElement.clientWidth < 391) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
    // $('.christmas img').attr('src', 'pub_images/christmas_small.png');
    // $('.christmas img').attr('src', pathImagePaySmall);
    // $('.christmas img').attr('src', pathImageBlackFridaySmall);
    $('.christmas img').attr('src', pathImageChristmasSmall);
    $('.checkup img').attr('src', pathImageCheckupSmall);
}

window.addEventListener('resize', function (e) {
    if (document.documentElement.clientWidth > 1925) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
        // $('.christmas img').attr('src', 'pub_images/christmas_biggest.png');
        // $('.christmas img').attr('src', pathImagePayBiggest);
        // $('.christmas img').attr('src', pathImageBlackFridayBiggest);
        $('.christmas img').attr('src', pathImageChristmasBiggest);
        $('.checkup img').attr('src', pathImageCheckupBiggest);
    }
    if (document.documentElement.clientWidth > 769 && document.documentElement.clientWidth < 1920) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
        // $('.christmas img').attr('src', 'pub_images/christmas_big.png');
        // $('.christmas img').attr('src', pathImagePayBig);
        // $('.christmas img').attr('src', pathImageBlackFridayBig);
        $('.christmas img').attr('src', pathImageChristmasBig);
        $('.checkup img').attr('src', pathImageCheckupBig);
    }
    if (document.documentElement.clientWidth > 391 && document.documentElement.clientWidth < 769) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
        // $('.christmas img').attr('src', 'pub_images/christmas_middle.png');
        // $('.christmas img').attr('src', pathImagePayMiddle);
        // $('.christmas img').attr('src', pathImageBlackFridayMiddle);
        $('.christmas img').attr('src', pathImageChristmasMiddle);
        $('.checkup img').attr('src', pathImageCheckupMiddle);
    }
    if (document.documentElement.clientWidth < 391) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
        // $('.christmas img').attr('src', 'pub_images/christmas_small.png');
        // $('.christmas img').attr('src', pathImagePaySmall);
        // $('.christmas img').attr('src', pathImageBlackFridaySmall);
        $('.christmas img').attr('src', pathImageChristmasSmall);
        $('.checkup img').attr('src', pathImageCheckupSmall);
    }
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