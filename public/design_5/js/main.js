$( function() {
    const announce = document.querySelector(".announce__item");
    const tempurl = window.location.href;
    let pathname = window.location.pathname;
    if (pathname != '/') {
        if (!tempurl.includes('affiliate') && !tempurl.includes('contact_us')) {
            setTimeout((() => {
                announce.classList.add("active");
                announce.parentNode.style = 'z-index: 300;';
            }), 5000);

            setTimeout((() => {
                announce.classList.remove("active");
                announce.parentNode.style = 'z-index: 0;';
            }), 9000);

        }
    }

    $(document).mouseup( function(e){
        var popup_part = $(".drop.active").find('ul');
        if ( !popup_part.is(e.target)
          && popup_part.has(e.target).length === 0
          && popup_part.css('display') == 'block' ) {
            popup_part.fadeOut();
            $(".drop.active").removeClass('active');
        }
    });

    $('.js-menu, .nav .close').on('click', function (e){
        e.preventDefault();
        $('.nav').toggleClass('active');
        $('body').toggleClass('hide');
    });

    $('.more').on('click', function (e){
        e.preventDefault();
        let block = $(this).parents('.text-box').find('.text');
        block.toggleClass('active');
        if($(this).text() == 'view all') {
            $(this).html('hide');
            block.css({'display': 'flex', 'flex-wrap': 'wrap'});
        } else {
            $(this).html('view all');
            block.css({'display': '-webkit-box'});
        }
    });

    $(document).on('click', '.categories_button', function (e) {
        e.preventDefault();
        $('.categories-sidebar').toggleClass('hide');
        $('.nav').toggleClass('active');
    });

    if (location.pathname != '/'){
        $('.main_bestsellers').parent().find('.spollers__title').removeClass('_spoller-active');
        document.getElementById("main_bestsellers").hidden = true;
        if (document.getElementById('main_bestsellers_body')) {
            document.getElementById('main_bestsellers_body').hidden = true;
        }
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
            document.getElementById('message_send_button').removeAttribute('disabled');
        } else {
            document.getElementById('affiliate_send_button').removeAttribute('disabled');
        }
    }

}

$(document).on('click', '#message_send_button', function () {
    var error = false;
    const name = document.getElementById("name").value;
    const email = document.getElementById('email').value;
    const subject = $('.select_current_subject').text();
    const subject_id = $('.select_current_subject').attr('curr_subject_id');
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    if (subject_id == 0) {
        alert($('#error_subject').val());
        error = true;
    }

    if (!error) {
        $.ajax({
            url: '/request_contact_us',
            type: "POST",
            cache: false,
            data: { 'name' : name,
            'email' : email,
            'subject' : subject_id,
            'message' : message,
            'captcha' : captcha,
            'submit' : submit },
            dataType: "json",
            success: function(data) { //Данные отправлены успешно
                if (data['status'] == 'error') {
                    alert(data['text']);
                    $('#captcha_image').attr('src', data['new_captcha']);
                } else {
                    $(".title-page").hide();
                    $('#message_send_form').hide();
                    $('.text-bottom-desc').hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
            }
        });
    }
});

$(document).on('click', '#affiliate_send_button', function () {
    var error = false;
    var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    const name = document.getElementById("name").value;
    if (name.length === 0) {
        document.getElementById("name").style.backgroundColor = "#FF8F8F";
        error = true;
    } else {
        document.getElementById("name").style.backgroundColor = "#f4f4f4";
    }
    const email = document.getElementById("email").value;
    if (!email.match(validRegex) || email.length === 0) {
        document.getElementById("email").style.backgroundColor = "#FF8F8F";
        error = true;
    } else {
        document.getElementById("email").style.backgroundColor = "#f4f4f4";
    }
    const jabber = document.getElementById("jabber").value;
    const message = document.getElementById("message").value;
    const captcha = document.getElementById("captcha").value;
    const submit = true;

    if (!error) {
        $.ajax({
            url:     '/request_affiliate',
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
                    // $(".contact-form").html(data);
                    $(".title-page").hide();
                    $('#message_send_form').hide();
                    $('.text-bottom-desc').hide();
                    $('.message_sended').removeClass('hidden');
                    $('.message_sended').addClass('active');

                    setTimeout((() => {
                        location.href = '/' + location.search;
                    }), 2000);
                }
        	}
     	});
    }
});

// $(document).on('click', '.number-spinner>.ns-btn>span', function () {
//     let btn = $(this),
//         oldValue = $('[data-quantity-value]').val(),
//         newVal = 0,
//         id = $('[data-quantity-value]').attr('id');

//     if (btn.attr('data-dir') === 'up') {
//         newVal = parseInt(oldValue) + 1;
//     } else {
//         if (oldValue > 1) {
//             newVal = parseInt(oldValue) - 1;
//         } else {
//             newVal = 1;
//         }
//     }
//     btn.closest('.number-spinner').find('input').val(newVal);

//     $.ajax({
//         url: "app/ajax_cart.php",
//         type: 'POST',
//         data: {'num': newVal, 'id': id},
//         dataType: 'html',
//         success : function(data) {
//             $(".basket").html(data);
//         }
//     });
// });

// $(document).on('click', '.back-to-main', function () {
//     location.href = '/';
// });

// $(document).on('click', '.button-checkout', function () {
//     location.href = '/checkout';
// });

// $(document).on('change', '[data-quantity-value]', function () {
//     let id = $('[data-quantity-value]').attr('id'),
//         value = $('[data-quantity-value]').val();
//     $.ajax({
//         url: "app/ajax_cart.php",
//         type: 'POST',
//         data: {'num': value, 'id': id},
//         dataType: 'html',
//         success : function(data) {
//             $(".basket").html(data);
//         }
//     });
// });

$(document).on('keypress', '.number-spinner>input', function (evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
});

$(document).on('click', '.request_call', function () {
    $('.popup_gray').show();
    $('body').css({'overflow':'hidden'});
    $('.simplebar-placeholder').hide();
});

$(document).on('click', '.close_popup', function () {
    $('.popup_gray').hide();
    $('body').css({'overflow':'auto'});
});

$(document).on('click', '.button_request_call', function () {
    let phone_code = $('[name="phone_code"]').val();
    let number = $('#phone').val();
    console.log(phone_code+number);

    if (number) {
        $.ajax({
            url: '/request_call',
            type: "POST",
            cache: false,
            data: {phone: phone_code+number},
            dataType: "json",
            success: function (res) {
                if (res['status'] == 'success') {
                    $('.popup_bottom').hide();

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

$(document).on('click', '.christmas', function () {
    $(this).hide();
    var date = new Date;
    date.setDate(date.getDate() + 1);
    date = date.toUTCString();
    document.cookie = 'christmas=1; path=/; expires=' + date;
});

if (window.innerWidth > 1925) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
    $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
}
if (window.innerWidth > 769 && window.innerWidth < 1920) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_big.png');
    $('.christmas img').attr('src', '/pub_images/christmas_big.png');
}
if (window.innerWidth > 391 && window.innerWidth < 769) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
    $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
}
if (window.innerWidth < 391) {
    //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
    // $('.christmas img').attr('src', '/pub_images/pay_small.png');
    $('.christmas img').attr('src', '/pub_images/christmas_small.png');
}

window.addEventListener('resize', function (e) {
    if (window.innerWidth > 1925) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_biggest.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_biggest.png');
        $('.christmas img').attr('src', '/pub_images/christmas_biggest.png');
    }
    if (window.innerWidth > 769 && window.innerWidth < 1920) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_big.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_big.png');
        $('.christmas img').attr('src', '/pub_images/christmas_big.png');
    }
    if (window.innerWidth > 391 && window.innerWidth < 769) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_middle.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_middle.png');
        $('.christmas img').attr('src', '/pub_images/christmas_middle.png');
    }
    if (window.innerWidth < 391) {
        //$('.christmas').css('background', 'url("' + $('#path_image').val() + '/pay_small.png") no-repeat ');
        // $('.christmas img').attr('src', '/pub_images/pay_small.png');
        $('.christmas img').attr('src', '/pub_images/christmas_small.png');
    }
});

$(document).on('click', '.button_sub', function () {
    let email = $('#email_sub').val();
    if (email) {
        $.ajax({
            url: '/request_subscribe',
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
    enableNotif();
});

if ($('#order_info_session').val()) {
    $.ajax({
        url: '/push/save_push',
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

$(document).on('click', '.select_header_gift', function () {
    $(this).parent().toggleClass('is-active');
});

$(document).on('click', '.select_item_gift', function () {
    $('.select_current_gift').text($(this).text());
    $('.select_current_gift').attr('curr_packaging_id', $(this).attr('packaging_id'));
    $(this).parent().parent().removeClass('is-active');
});

$(document).on('click', '.visible.gift', function () {
    if ($(this).hasClass('get-gift')) {
        $(this).removeClass('get-gift');
        $('.gift_bottom_block').hide();
    } else {
        $(this).addClass('get-gift');
        $('.gift_bottom_block').css('display', 'flex');
    }
});

$(document).on('click', '.select_header_subject', function () {
    $(this).parent().toggleClass('is-active');
});

$(document).on('click', '.select_item_subject', function () {
    $('.select_current_subject').text($(this).text());
    $('.select_current_subject').attr('curr_subject_id', $(this).attr('subject_id'));
    $(this).parent().parent().removeClass('is-active');
});

// function addCard() {
//     let value_card = $('.select_current_gift').attr('curr_packaging_id');
//     $.ajax({
//         url: "/app/ajax_cart.php",
//         type: 'POST',
//         data: {
//             'card_pack_id': value_card
//         },
//         dataType: 'html',
//         success : function(data) {
//             $('.basket').html(data);
//         },
//     });
// }

$('.feedback:not(.slick-initialized)').slick({
    dots: false,
    arrows: true,
    infinite: true,
    speed: 500,
    fade: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    slidesPerRow: 1,
    rows: 1,
    prevArrow: '<button type="button" class="slick-prev btn btn-nav"><svg width="10" height="15" viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path d="M3.16421 7.5L8.95711 1.70711C9.34763 1.31658 9.34763 0.683416 8.95711 0.292892C8.56658 -0.0976315 7.93342 -0.0976315 7.54289 0.292892L1.04289 6.79289C0.652369 7.18342 0.652369 7.81658 1.04289 8.20711L7.54289 14.7071C7.93342 15.0976 8.56658 15.0976 8.95711 14.7071C9.34763 14.3166 9.34763 13.6834 8.95711 13.2929L3.16421 7.5Z" fill="#262D38"/>\n' +
            '</svg>\n</button>',
    nextArrow: '<button type="button" class="slick-next btn btn-nav"><svg width="10" height="15" viewBox="0 0 10 15" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path d="M6.83579 7.5L1.04289 13.2929C0.652369 13.6834 0.652369 14.3166 1.04289 14.7071C1.43342 15.0976 2.06658 15.0976 2.45711 14.7071L8.95711 8.20711C9.34763 7.81658 9.34763 7.18342 8.95711 6.79289L2.45711 0.292893C2.06658 -0.0976311 1.43342 -0.0976311 1.04289 0.292893C0.652369 0.683418 0.652369 1.31658 1.04289 1.70711L6.83579 7.5Z" fill="#262D38"/>\n' +
            '</svg>\n</button>',
    mobileFirst: true,
    responsive: [
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1,
                slidesPerRow: 1,
                rows: 2,
            }
        }
    ]
});