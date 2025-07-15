$( function() {
    const announce = document.querySelector(".announce__item");
    const tempurl = window.location.href;
    let pathname = window.location.pathname;
    if (pathname != '/') {
        if (!tempurl.includes('affiliate') && !tempurl.includes('contact_us')) {
            setTimeout((() => {
                announce.classList.add("active");
                announce.parentNode.style = 'z-index: 300;';
            }), 8000);

            setTimeout((() => {
                announce.classList.remove("active");
                announce.parentNode.style = 'z-index: 0;';
            }), 12000);

        }
    }

    $('.more').on('click', function (e){
        e.preventDefault();
        let block = $(this).parents('.product__image-block_links').find('.product__image_links');
        block.toggleClass('active');
        if($(this).text() == 'view all') {
            $(this).html('hide');
            block.css({'display': 'flex', 'flex-wrap': 'wrap'});
        } else {
            $(this).html('view all');
            block.css({'display': '-webkit-box'});
        }
    });

    if(!(typeof(window.countdownfunction1) !== "undefined" && window.countdownfunction1 !== null) && location.pathname == '/') {
        var countDownDate = new Date().getTime() + 1800000;
        clearInterval(window.countdownfunction1);
        window.countdownfunction1 = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            if(seconds < 10)
                seconds = '0' + seconds;
            document.getElementById("time").innerHTML = "00:" + minutes + ":" + seconds;
            if (distance < 0) {
                clearInterval(countdownfunction1);
                document.getElementById("time").innerHTML = "00:00:00";
            }}, 1000);
    }
} );

function undisabled(page) {
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let captcha = document.getElementById("captcha").value;
    let validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    if (name == '' || email == '' || !email.match(validRegex) || captcha == '') {
        if (page == 'contact_us') {
            document.getElementById('message_send_button').setAttribute('disabled', 'disabled');
        } else {
            document.getElementById('affiliate_send_button').setAttribute('disabled', 'disabled');
        }
    } else {
        if (page == 'contact_us') {
            document.getElementById('message_send_button').style.backgroundColor = 'var(--green)';
            document.getElementById('message_send_button').removeAttribute('disabled');
        } else {
            document.getElementById('affiliate_send_button').style.backgroundColor = 'var(--green)';
            document.getElementById('affiliate_send_button').removeAttribute('disabled');
        }
    }
}


$(document).on('change', '[data-quantity-value]', function () {
    let value = parseInt($(this).val());
    let id = parseInt($(this).attr('id'));
    let value_ship = $("input[name='delivery']:checked").val();
    let value_bonus = $("input[name='bonus']:checked").attr('id');
    $.ajax({
        url: "/app/ajax_cart.php",
        type: 'POST',
        data: {'num': value, 'id': id, 'bonus_id': value_bonus, 'shipping_code': value_ship},
        dataType: 'html',
        success : function(data) {
            $(".basket").html(data);
        }
    });
});

$(document).on('click', '#message_send_button', function () {
    const name = document.getElementById("name").value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById("subject").value;
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    $.ajax({
        url: routeRequestContactUs,
        type: "POST",
        cache: false,
        data: { 'name' : name,
        'email' : email,
        'subject' : subject,
        'message' : message,
        'captcha' : captcha,
        'submit' : submit },
        dataType: "json",
        success: function(data) { //Данные отправлены успешно
            if (data['status'] == 'error') {
                alert(data['text']);
                $('#captcha_image').attr('src', data['new_captcha']);
            } else {
                $('.page-text__top-row').hide();
                $('.page-text__inner').hide();
                $('.message_sended').removeClass('hidden');
                $('.message_sended').addClass('active');

                setTimeout((() => {
                    location.href = '/' + location.search;
                }), 2000);
            }
        }
     });
});

$(document).on('click', '#affiliate_send_button', function () {
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const jabber = document.getElementById("jabber").value;
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    $.ajax({
        url:     routeRequestAffiliate,
        type:     "POST",
        cache: false,
        data: { 'name' : name,
        'email' : email,
        'jabber' : jabber,
        'message' : message,
        'captcha' : captcha,
        'submit' : submit },
        dataType: "json",
        success: function(data) { //Данные отправлены успешно
            if (data['status'] == 'error') {
                alert(data['text']);
                $('#captcha_image').attr('src', data['new_captcha']);
            } else {
                $('.page-text__top-row').hide();
                $('.page-text__inner').hide();
                $('.message_sended').removeClass('hidden');
                $('.message_sended').addClass('active');

                setTimeout((() => {
                    location.href = '/' + location.search;
                }), 2000);
            }
        }
     });
});

$(document).on('click', '.search_button', function () {
    location.href='/app/search.php?search=' + $('[name="search"]').val();
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

// $(document).on('click', '.christmas', function () {
//     $(this).hide();
//     var date = new Date;
//     date.setDate(date.getDate() + 1);
//     date = date.toUTCString();
//     document.cookie = 'christmas=1; path=/; expires=' + date;
// });

if (window.innerWidth > 1925) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_biggest.png');
}
if (window.innerWidth > 769 && window.innerWidth < 1920) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_big.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_big.png');
}
if (window.innerWidth > 391 && window.innerWidth < 769) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_middle.png');
}
if (window.innerWidth < 391) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_small.png');
    // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
    $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_small.png');
}

window.addEventListener('resize', function (e) {
    if (window.innerWidth > 1925) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_biggest.png');
    }
    if (window.innerWidth > 769 && window.innerWidth < 1920) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_big.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_big.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_big.png');
    }
    if (window.innerWidth > 391 && window.innerWidth < 769) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_middle.png');
    }
    if (window.innerWidth < 391) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_small.png');
        // $('.christmas img').attr('src', '/pub_images/christmas_small.png');
        $('.christmas img').attr('src', '/pub_images/checkup_img/white/checkup_small.png');
    }
});

$(document).on('click', '.button_sub', function () {
    let email = $('#email_sub').val();
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

$(document).on('click', '.close_popup', function () {
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
    enableNotif();
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