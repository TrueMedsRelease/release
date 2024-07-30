var products = $("#product").val();
var bonus = $("#bonus").val();
var discount = $("#discount").val();


function validateEmail(email) {
    return String(email)
    .toLowerCase()
    .match(
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
};

function validate_form()
{
    var phone = document.getElementById('phone');
    var email = document.getElementById('email');
    var firstname = document.getElementById('billing_firstname');
    var lastname = document.getElementById('billing_lastname');
    var billing_city = document.getElementById('billing_city');
    var billing_address = document.getElementById('billing_address');
    var billing_zip = document.getElementById('billing_zip');
    var different = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var shipping_city = document.getElementById('shipping_city');
    var shipping_address = document.getElementById('shipping_address');
    var shipping_zip = document.getElementById('shipping_zip');
    
    if(phone.value.length < 4 || phone.value.length > 16)
    {
        //alert(phone.value);
        phone.setCustomValidity('Incorrect phone');
        phone.reportValidity();
        return false;
    }

    if(!validateEmail(email.value))
    {
        email.setCustomValidity('Incorrect email');
        email.reportValidity();
        return false;
    }

    if(firstname.value.trim() == '')
    {
        firstname.setCustomValidity('Incorrect firstname');
        firstname.reportValidity();
        return false;
    }

    if(lastname.value.trim() == '')
    {
        lastname.setCustomValidity('Incorrect lastname');
        lastname.reportValidity();
        return false;
    }

    if(billing_city.value.trim() == '')
    {
        billing_city.setCustomValidity('Incorrect billing city');
        billing_city.reportValidity();
        return false;
    }

    if(billing_address.value.trim() == '')
    {
        billing_address.setCustomValidity('Incorrect billing address');
        billing_address.reportValidity();
        return false;
    }

    if(billing_zip.value.trim() == '')
    {
        billing_zip.setCustomValidity('Incorrect billing zip');
        billing_zip.reportValidity();
        return false;
    }

    if(different == 1)
    {
        if(shipping_city.value.trim() == '')
        {
            shipping_city.setCustomValidity('Incorrect shipping city');
            shipping_city.reportValidity();
            return false;
        }
    
        if(shipping_address.value.trim() == '')
        {
            shipping_address.setCustomValidity('Incorrect shipping address');
            shipping_address.reportValidity();
            return false;
        }
    
        if(shipping_zip.value.trim() == '')
        {
            shipping_zip.setCustomValidity('Incorrect shipping zip');
            shipping_zip.reportValidity();
            return false;
        }
    
    }

    return true;

    
    
}

$('#phone').bind('input', function() {
    var phone = document.getElementById('phone');
    phone.setCustomValidity('');
});

$('#email').bind('input', function() {
    var email = document.getElementById('email');
    email.setCustomValidity('');
});

$('#billing_firstname').bind('input', function() {
    var billing_firstname = document.getElementById('billing_firstname');
    var billing_lastname = document.getElementById('billing_lastname');
    billing_firstname.setCustomValidity('');
    var reference = document.getElementById('reference');
    reference.innerHTML = billing_firstname.value + " " + billing_lastname.value;
});

$('#billing_lastname').bind('input', function() {
    var billing_lastname = document.getElementById('billing_lastname');
    var billing_firstname = document.getElementById('billing_firstname');
    billing_lastname.setCustomValidity('');
    var reference = document.getElementById('reference');
    reference.innerHTML = billing_firstname.value + " " + billing_lastname.value;
});

$('#billing_city').bind('input', function() {
    var billing_city = document.getElementById('billing_city');
    billing_city.setCustomValidity('');
});

$('#billing_address').bind('input', function() {
    var billing_address = document.getElementById('billing_address');
    billing_address.setCustomValidity('');
});

$('#billing_zip').bind('input', function() {
    var billing_zip = document.getElementById('billing_zip');
    billing_zip.setCustomValidity('');
});

$('#shipping_city').bind('input', function() {
    var shipping_city = document.getElementById('shipping_city');
    shipping_city.setCustomValidity('');
});

$('#shipping_address').bind('input', function() {
    var shipping_address = document.getElementById('shipping_address');
    shipping_address.setCustomValidity('');
});

$('#shipping_zip').bind('input', function() {
    var shipping_zip = document.getElementById('shipping_zip');
    shipping_zip.setCustomValidity('');
});

$('#cvc_2').bind('input', function() {
    var cvc_2 = document.getElementById('cvc_2');
    cvc_2.setCustomValidity('');
});

function valid_credit_card(value) {
    // accept only digits, dashes or spaces
    if (/[^0-9-\s]+/.test(value)) return false;
    
    // The Luhn Algorithm. It's so pretty.
    var nCheck = 0, nDigit = 0, bEven = false;
    value = value.replace(/\D/g, "");
    
    for (var n = value.length - 1; n >= 0; n--) {
        var cDigit = value.charAt(n),
        nDigit = parseInt(cDigit, 10);
        
        if (bEven) {
            if ((nDigit *= 2) > 9) nDigit -= 9;
        }
        
        nCheck += nDigit;
        bEven = !bEven;
    }
    
    return (nCheck % 10) == 0;
}

function findWord(word, str) {
    return RegExp('\\b'+ word +'\\b').test(str)
}


// const getCookie = (name) => {
//     return document.cookie.split('; ').reduce((r, v) => {
//       const parts = v.split('=')
//       return parts[0] === name ? decodeURIComponent(parts[1]) : r
//     }, '')
//   }


if(!(typeof(window.countdownfunction1) !== "undefined" && window.countdownfunction1 !== null)) {
    var countDownDate = new Date().getTime() + 1800000;
    clearInterval(window.countdownfunction1);
    
    // Update the count down every 1 second
    window.countdownfunction1 = setInterval(function() {
        //alert('aaaa');

    // Get todays date and time
    var now = new Date().getTime();

    // Find the distance between now an the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Output the result in an element with id="demo"
    if(seconds < 10)
    {
        seconds = '0' + seconds;
    }

    document.getElementById("t1").innerHTML = minutes + ":" + seconds;
    document.getElementById("t2").innerHTML = minutes + ":" + seconds;
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(countdownfunction1);
        document.getElementById("t1").innerHTML = "0";
        document.getElementById("t2").innerHTML = "0";
    }
    }, 1000);
}

// $(document).ready(function(){
    

// });
        
$('input[name="shipping"]').bind('change', function(e) {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var shipping = $("input[name='shipping']:checked").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var phone = $("#phone").val();
    var email = $("#email").val();
    var theme = $("#theme").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    $.ajax({
            url:'/checkout/content_ajax.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'products':products,'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
            'shipping':shipping,'discount':discount,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email, 
            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
            'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
            
            success:function(data){$('.wrapper').html(data);}
            
        });
    }); 
    var btn1 = document.getElementById("SecretPackage");
    btn1.onclick = function() {
        var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var shipping = $("input[name='shipping']:checked").val();
    var theme = $("#theme").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    if ( document.getElementById("SecretPackage").classList.contains('checked') )
    {
        document.getElementById("SecretPackage").classList.remove('checked');
        document.getElementsByName("SecretPackage")[0].value = "0";
        
        $.ajax({
            url:'/checkout/content_ajax.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'products':products,'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,
            'SecretPackage': document.getElementsByName("SecretPackage")[0].value,'shipping':shipping,'discount':discount,
            'phone':phone,'billing_country':billing_country,'billing_state':billing_state,'email':email,'alter_email':alter_email,
            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
            'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},

            success:function(data){$('.wrapper').html(data);}
            
        });
    }
    else
    {
            document.getElementById("SecretPackage").classList.add('checked');   
            document.getElementsByName("SecretPackage")[0].value = $("#secret_price").val();;
            
            $.ajax({
                url:'/checkout/content_ajax.php',   
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {'products':products,'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,
                'SecretPackage': document.getElementsByName("SecretPackage")[0].value,'shipping':shipping,'discount':discount,
                'phone':phone,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
                'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
                'language':language, 'currency':currency, 'theme': theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
                
                success:function(data){$('.wrapper').html(data);}
                
            });
        }
    }
var btn2 = document.getElementById("change_insurance");
btn2.onclick = function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var shipping = $("input[name='shipping']:checked").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var theme = $("#theme").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    if ( document.getElementById("Insurance").classList.contains('checked'))
    {
        document.getElementById("Insurance").classList.remove('checked');
        document.getElementsByName("Insurance")[0].value = "0";
        
        $.ajax({
            url:'/checkout/content_ajax.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'products':products,'bonus':bonus,'SecretPackage': document.getElementsByName("SecretPackage")[0].value, 'insurance_change':1,
            'Insurance':document.getElementsByName("Insurance")[0].value,'shipping':shipping,'discount':discount,
            'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
            'language':language, 'currency':currency, 'theme': theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
            //beforeSend:async function(){alert('aaa');},
            success:function(data){$('.wrapper').html(data);}
            
        });
    }
    else
    {
        document.getElementById("Insurance").classList.add('checked');
        document.getElementsByName("Insurance")[0].value = $("#insurance_price").val();
        
        $.ajax({
            url:'/checkout/content_ajax.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'products':products,'bonus':bonus,'SecretPackage': document.getElementsByName("SecretPackage")[0].value, 'insurance_change':1,
            'Insurance':document.getElementsByName("Insurance")[0].value,'shipping':shipping,'discount':discount,
            'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
            'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
            
            success:function(data){$('.wrapper').html(data);}
            
        });
    }
}
var btn3 = document.getElementById("Insurance");
btn3.onclick = function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var shipping = $("input[name='shipping']:checked").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var theme = $("#theme").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    if ( document.getElementById("Insurance").classList.contains('checked'))
    {
        // document.getElementById("Insurance").classList.remove('checked');
        // document.getElementsByName("Insurance")[0].value = "0";
        
        // $.ajax({
        //     url:'/checkout/content_ajax.php',   
        //     type: 'POST',
        //     cache: false,
        //     dataType: 'html',
        //     data: {'products':products,'bonus':bonus,'SecretPackage': document.getElementsByName("SecretPackage")[0].value, 'insurance_change':1,
        //     'Insurance':document.getElementsByName("Insurance")[0].value,'shipping':shipping,'discount':discount,
        //     'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
        //     'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
        //     'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
        //     'language':language, 'currency':currency, 'theme': theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
        //     //beforeSend:async function(){alert('aaa');},
        //     success:function(data){$('.wrapper').html(data);}
            
        // });
    }
    else
    {
        document.getElementById("Insurance").classList.add('checked');
        document.getElementsByName("Insurance")[0].value = $("#insurance_price").val();
        
        $.ajax({
            url:'/checkout/content_ajax.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'products':products,'bonus':bonus,'SecretPackage': document.getElementsByName("SecretPackage")[0].value, 'insurance_change':1,
            'Insurance':document.getElementsByName("Insurance")[0].value,'shipping':shipping,'discount':discount,
            'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
            'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
            
            success:function(data){$('.wrapper').html(data);}
            
        });
    }
}

// $('#Insurance').change(function() {
    
//     if(!this.checked) {
//         var returnVal = confirm("Are you sure?");
//         alert(returnVal);
//         $(this).prop("checked", !returnVal);
//     }
//     alert(this.checked);
//     $('#Insurance').val(this.checked);        
// });

$('#c_6').bind('input', function() {
    if(this.value.match(/[^0-9]/g)){
        this.value = this.value.replace(/[^0-9]/g, "");
    };
});


$( ".card_type .select__option" ).click(function() {
    var type = $(this).attr('data-value');
    //alert(type);
    if(type == 'crypto')
    {
        
        if(!validate_form())
        {
            $(this).val($.data(this, 'current'));
            return false;
        }
        // document.getElementById("payment_content_card").hidden = true;
        // document.getElementById("proccess").style.visibility = 'hidden';
        // document.getElementById("payment_content_crypt").hidden = false;

        // document.getElementById("sure").hidden = true;
        // document.getElementById("please_wait").hidden = false;
        

        document.getElementById("phone").disabled = true;
        document.getElementById("Insurance").disabled = true;
        document.getElementById("SecretPackage").disabled = true;
        document.getElementById("c_86").disabled = true;
        document.getElementById("c_85").disabled = true;
        document.getElementById("email").disabled = true;
        document.getElementById("alter_email").disabled = true;
        //document.getElementById("messenger_id").disabled = true;
        document.getElementById("billing_firstname").disabled = true;
        document.getElementById("billing_lastname").disabled = true;
        document.getElementById("billing_country").disabled = true;

        if (document.getElementById("billing_state")) {
            document.getElementById("billing_state").disabled = true;
          }

        
        document.getElementById("billing_city").disabled = true;
        document.getElementById("billing_address").disabled = true;
        document.getElementById("billing_zip").disabled = true;
        document.getElementById("c_1").disabled = true;
        document.getElementById("shipping_country").disabled = true;

        if (document.getElementById("shipping_state")) {
            document.getElementById("shipping_state").disabled = true;
          }

        
        document.getElementById("shipping_city").disabled = true;
        document.getElementById("shipping_address").disabled = true;
        document.getElementById("shipping_zip").disabled = true;
        //document.getElementById("messenger").disabled = true;
    }
    else if(type == 'sepa')
    {
        if(!validate_form())
        {
            $(this).val($.data(this, 'current'));
            return false;
        }

        var billing_firstname = document.getElementById('billing_firstname');
        var billing_lastname = document.getElementById('billing_lastname');
        var reference = document.getElementById('reference');
        reference.innerHTML = billing_firstname.value + " " + billing_lastname.value;


        document.getElementById("phone").disabled = true;
        document.getElementById("Insurance").disabled = true;
        document.getElementById("SecretPackage").disabled = true;
        document.getElementById("c_86").disabled = true;
        document.getElementById("c_85").disabled = true;
        document.getElementById("email").disabled = true;
        document.getElementById("alter_email").disabled = true;
        //document.getElementById("messenger_id").disabled = true;
        document.getElementById("billing_firstname").disabled = true;
        document.getElementById("billing_lastname").disabled = true;
        document.getElementById("billing_country").disabled = true;

        if (document.getElementById("billing_state")) {
            document.getElementById("billing_state").disabled = true;
          }

        
        document.getElementById("billing_city").disabled = true;
        document.getElementById("billing_address").disabled = true;
        document.getElementById("billing_zip").disabled = true;
        document.getElementById("c_1").disabled = true;
        document.getElementById("shipping_country").disabled = true;

        if (document.getElementById("shipping_state")) {
            document.getElementById("shipping_state").disabled = true;
          }

        
        document.getElementById("shipping_city").disabled = true;
        document.getElementById("shipping_address").disabled = true;
        document.getElementById("shipping_zip").disabled = true;
    }
    else if(type == 'card')
    {
        // document.getElementById("payment_content_crypt").hidden = true;
        // document.getElementById("proccess").style.visibility = 'visible';
        // document.getElementById("payment_content_card").hidden = false;

        // document.getElementById("sure").hidden = false;
        // document.getElementById("please_wait").hidden = true;


        document.getElementById("phone").disabled = false;
        document.getElementById("email").disabled = false;
        document.getElementById("alter_email").disabled = false;
        //document.getElementById("messenger_id").disabled = false;
        document.getElementById("billing_firstname").disabled = false;
        document.getElementById("billing_lastname").disabled = false;
        document.getElementById("billing_country").disabled = false;
        document.getElementById("billing_state").disabled = false;
        document.getElementById("billing_city").disabled = false;
        document.getElementById("billing_address").disabled = false;
        document.getElementById("billing_zip").disabled = false;
        document.getElementById("c_1").disabled = false;
        document.getElementById("shipping_country").disabled = false;
        document.getElementById("shipping_state").disabled = false;
        document.getElementById("shipping_city").disabled = false;
        document.getElementById("shipping_address").disabled = false;
        document.getElementById("shipping_zip").disabled = false;
       // document.getElementById("messenger").disabled = false;
    }
});

$('input[name="crypt_currency"]').click(function() {
    var currency = $(this).val();
    //alert(currency);
    var email = document.getElementById('email');
    var total = document.getElementById('total');

    if(currency != '')
    {   
        //document.body.classList.remove('loaded');

        document.getElementById("requisites_load").hidden = false;
        document.getElementById("requisites").hidden = true;
        
        $.ajax({
            url:'/checkout/crypto.php',   
            type: 'POST',
            cache: false,
            dataType: 'html',
            data: {'currency':currency, 'email': email.value, 'price': total.value},

        //        async: false,

            success:function(data){
                var result = JSON.parse(data);
                //document.body.classList.add('loaded');
                // alert(result.invoiceId);
                // alert(result.purse);
                // alert(result.amount);
                var cur = currency.split('_');
                cur = cur[0];
                var total = result.amount;
                //alert(total);
                document.getElementById('crypto_total').innerHTML = total;
                document.getElementById('purse').innerHTML = result.purse;
                document.getElementById('qr_code').src = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" + result.purse;
                document.getElementById('invoiceId').value = result.invoiceId;
                document.getElementById('invoce_p').innerHTML = result.invoiceId;
                pollFunc(CheckPayment, 1800000, 5000);
                document.getElementById("requisites_load").hidden = true;
                document.getElementById("requisites").hidden = false;
            }

        });

        var countDownDate = new Date().getTime() + 1800000;
        clearInterval(window.countdownfunction);
        
        // Update the count down every 1 second
        window.countdownfunction = setInterval(function() {
            //alert('aaaa');

        // Get todays date and time
        var now = new Date().getTime();

        // Find the distance between now an the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="demo"
        if(seconds < 10)
        {
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
    else
    {
        document.getElementById("requisites").hidden = true;
    }
});

$( ".select_billing_country .select__option" ).click(function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var billing_country = $(this).attr('data-value');
    var shipping = $("input[name='shipping']:checked").val();
    var billing_state = $("#billing_state option:selected").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    var theme = $("#theme").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
  $.ajax({
             url:'/checkout/content_ajax.php',   
             type: 'POST',
             cache: false,
             dataType: 'html',
             data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
             'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
             'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
             'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
             'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},

         //        async: false,

             success:function(data){
                $('.wrapper').html(data);
            }

         });
});

$( ".select_billing_state .select__option" ).click(function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $(this).attr('data-value');
    var shipping = $("input[name='shipping']:checked").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    var theme = $("#theme").val();
    //var messenger = $(".messenger option:selected").val();
    //var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    $.ajax({
               url:'/checkout/content_ajax.php',   
               type: 'POST',
               cache: false,
               dataType: 'html',
               data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
               'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
               'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
               'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
               'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
  
           //        async: false,
  
               success:function(data){
                  $('.wrapper').html(data);
              }
  
           });
  });

  $( ".select_shipping_country .select__option" ).click(function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var shipping_country = $(this).attr('data-value');
    var shipping = $("input[name='shipping']:checked").val();
    var shipping_state = $("#shipping_state option:selected").val();
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var theme = $("#theme").val();
    var billing_firstname = $("#billing_firstname").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    $.ajax({
               url:'/checkout/content_ajax.php',   
               type: 'POST',
               cache: false,
               dataType: 'html',
               data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
               'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
               'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
               'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
               'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
  
           //        async: false,
  
               success:function(data){
                  $('.wrapper').html(data);
              }
  
           });
  });

  $( ".select_shipping_state .select__option" ).click(function() {
    var language = $(".language option:selected").val();
    var currency = $(".currency option:selected").val();
    var phone_code = $(".phone_code option:selected").attr('id')
    var phone = $("#phone").val();
    var shipping_country = $("#shipping_country option:selected").val();
    var shipping_state = $(this).attr('data-value');
    var billing_country = $("#billing_country option:selected").val();
    var billing_state = $("#billing_state option:selected").val();
    var shipping = $("input[name='shipping']:checked").val();
    var email = $("#email").val();
    var alter_email = $("#alter_email").val();
    var billing_firstname = $("#billing_firstname").val();
    var theme = $("#theme").val();
    // var messenger = $(".messenger option:selected").val();
    // var messenger_id = $("#messenger_id").val();
    var billing_lastname = $("#billing_lastname").val();
    var billing_city = $("#billing_city").val();
    var billing_address = $("#billing_address").val();
    var billing_zip = $("#billing_zip").val();
    var shipping_city = $("#shipping_city").val();
    var shipping_address = $("#shipping_address").val();
    var shipping_zip = $("#shipping_zip").val();
    var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    var card_month = $(".card_month option:selected").val();
    var card_year = $(".card_year option:selected").val();
    var card_holder = $("#card_holder").val();
    var card_number = $("#card_numb").val();
    var card_cvv = $("#cvc_2").val();
    $.ajax({
               url:'/checkout/content_ajax.php',   
               type: 'POST',
               cache: false,
               dataType: 'html',
               data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
               'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
               'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
               'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
               'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
  
           //        async: false,
  
               success:function(data){
                  $('.wrapper').html(data);
              }
  
           });
  });
    var input = document.querySelector("#phone");
//     window.intlTelInput(input, {
//       // allowDropdown: false,
//       // autoHideDialCode: false,
//       // autoPlaceholder: "off",
//       // dropdownContainer: document.body,
//       // excludeCountries: ["us"],
//       // formatOnDisplay: false,
// //       geoIpLookup: function(callback) {
// //         $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
// //           var countryCode = (resp && resp.country) ? resp.country : "";
// //           callback(countryCode);
// //         });
// //       },
//        hiddenInput: "phone",
//       // initialCountry: "auto",
//       // localizedCountries: { 'de': 'Deutschland' },
//       // nationalMode: false,
//       // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
//      // placeholderNumberType: "MOBILE",
//       preferredCountries: ['us', 'gb', 'ca', 'au'],
//       // separateDialCode: true,
//       utilsScript: "/js/utils.js",
//     });

//     // $('#shipping_city').bind('change', function(e) {
//     //     var value = $(this).val();
//     //     value = value.trim();
//     //     if (value.length > 0)
//     //     {
//     //         $("#check_city").val(1);
//     //     }
//     //     else
//     //     {
//     //         $("#check_city").val(0);
//     //     }
//     // });

//     // $('#shipping_address').bind('change', function(e) {
//     //     var value = $(this).val();
//     //     value = value.trim();
//     //     if (value.length > 0)
//     //     {
//     //         $("#check_address").val(1);
//     //     }
//     //     else
//     //     {
//     //         $("#check_address").val(0);
//     //     }
//     // });

//     // $('#shipping_zip').bind('change', function(e) {
//     //     var value = $(this).val();
//     //     value = value.trim();
//     //     if (value.length > 0)
//     //     {
//     //         $("#check_zip").val(1);
//     //     }
//     //     else
//     //     {
//     //         $("#check_zip").val(0);
//     //     }
//     // });
 

    $('#card_numb').bind('change', function(e) {      
        var popup = document.getElementById("myPopup");
        var card_number = $("#card_numb").val();
        card_number = card_number.replace(/\s/g, "");
        //var valid = false;

        if(!valid_credit_card(card_number))
        {
            popup.classList.add("show");
            $("#check_number").val(0);
        }
        else
        {
            popup.classList.remove("show");
            $("#check_number").val(1);
        }  
    });

    $('#card_numb').bind('input', function() {
        if(this.value.match(/[^0-9]/g)){
            this.value = this.value.replace(/[^0-9]/g, "");
        };
    });

    $('#card_month').bind('input', function() {
        if(this.value.match(/[^0-9]/g)){
            this.value = this.value.replace(/[^0-9]/g, "");
        };
    });

    $('#card_year').bind('input', function() {
        if(this.value.match(/[^0-9]/g)){
            this.value = this.value.replace(/[^0-9]/g, "");
        };
    });

    $('#cvc_2').bind('input', function() {
        if(this.value.match(/[^0-9]/g)){
            this.value = this.value.replace(/[^0-9]/g, ""); 
        };
    });

    $("#myPopup").click(function() {
        var popup = document.getElementById("myPopup");
        popup.classList.toggle("show");
    });

    $("#myPopup1").click(function() {
        var popup = document.getElementById("myPopup1");
        popup.classList.toggle("show");
    });

    $("#myPopup2").click(function() {
        var popup = document.getElementById("myPopup2");
        popup.classList.toggle("show");
    });

    $("#myPopup3").click(function() {
        var popup = document.getElementById("myPopup3");
        popup.classList.toggle("show");
    });

    $("#myPopup4").click(function() {
        var popup = document.getElementById("myPopup4");
        popup.classList.toggle("show");
    });

    $("#myPopup5").click(function() {
        var popup = document.getElementById("myPopup5");
        popup.classList.toggle("show");
    });

    $("#myPopup6").click(function() {
        var popup = document.getElementById("myPopup6");
        popup.classList.toggle("show");
    });

    $("#myPopup7").click(function() {
        var popup = document.getElementById("myPopup7");
        popup.classList.toggle("show");
    });


    // $( ".card_month .select__option" ).click(function() {
    //     var year = $(".card_year .select__content").text();
    //     if(year != '')
    //     {
    //         var today = new Date();
    //         var mm = today.getMonth() + 1; 
    //         var popup = document.getElementById("myPopup1");
    //         var month = parseInt($(this).attr('data-value'));
    //         if(parseInt(year) == today.getFullYear())
    //         {
    //             if(month < mm)
    //             {
    //                 popup.classList.add("show");
    //                 $("#check_expire").val(0);
    //             }
    //             else{
    //                 popup.classList.remove("show");
    //                 $("#check_expire").val(1);  
    //             }
    //         }
    //         else
    //         {
    //             popup.classList.remove("show");
    //             $("#check_expire").val(1); 
    //         }
    //     }
    // });

    // $( ".card_year .select__option" ).click(function() {
    //     var year = $(this).attr('data-value');
    //     var today = new Date();
    //     var popup = document.getElementById("myPopup1");
    //     if(parseInt(year) < today.getFullYear())
    //     {
    //         popup.classList.add("show"); 
    //         $("#check_expire").val(0);
    //     }
    //     else if(parseInt(year) == today.getFullYear())
    //     {
    //         if(month < mm)
    //         {
    //             var mm = today.getMonth() + 1; 
    //             var month = parseInt($(".card_month .select__content").text());
    //             var popup = document.getElementById("myPopup1");
    //             popup.classList.add("show");
    //             $("#check_expire").val(0);
    //         }
    //         else{
    //             popup.classList.remove("show");
    //             $("#check_expire").val(1);  
    //         }
    //     }
    //     else
    //     {
    //         popup.classList.remove("show");
    //         $("#check_expire").val(1); 
    //     }
    // });

    $('#email').bind('change', function(e) {
        var language = $(".language option:selected").val();
        var currency = $(".currency option:selected").val();
        var phone_code = $(".phone_code option:selected").attr('id')
        var phone = $("#phone").val();
        //var shipping_country = $("#shipping_country option:selected").val();
        //var shipping_state = $(this).attr('data-value');
        //var billing_country = $("#billing_country option:selected").val();
        //var billing_state = $("#billing_state option:selected").val();
        var shipping = $("input[name='shipping']:checked").val();
        var email = $("#email").val();
        var alter_email = $("#alter_email").val();
        var billing_firstname = $("#billing_firstname").val();
        // var messenger = $(".messenger option:selected").val();
        // var messenger_id = $("#messenger_id").val();
        var billing_lastname = $("#billing_lastname").val();
        //var billing_city = $("#billing_city").val();
        //var billing_address = $("#billing_address").val();
        //var billing_zip = $("#billing_zip").val();
        //var shipping_city = $("#shipping_city").val();
        //var shipping_address = $("#shipping_address").val();
        //var shipping_zip = $("#shipping_zip").val();
        //var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
        var card_month = $(".card_month option:selected").val();
        var card_year = $(".card_year option:selected").val();
        var card_holder = $("#card_holder").val();
        var card_number = $("#card_numb").val();
        var card_cvv = $("#cvc_2").val();
        var theme = $("#theme").val();

        var popup5 = document.getElementById("myPopup5");

        if(validateEmail(email) === null)
        {
            $("#check_email").val(0);
            popup5.classList.add("show");
        }
        else
        {
            $("#check_email").val(1);
            popup5.classList.remove("show");

            $.ajax({
                url:'/checkout/content_ajax.php',   
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {'products':products,'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                'shipping':shipping,'discount':discount,'phone':phone,'email':email,'alter_email':alter_email,
                'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'authorization': 1,
                'language':language, 'theme':theme, 'currency':currency, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
   
            //        async: false,
   
                success:function(data){
                    if(data.includes("/status/"))
                    {
                        window.location.replace(data); 
                        document.body.classList.remove('loaded');
                    }
                    else if(data == "no")
                    {

                    }
                    else
                    {
                        $('.wrapper').html(data);
                    }
               }
   
            });
        }
    });

    // $('#phone').bind('change', function(e) {
    //     var language = $(".language option:selected").val();
    //     var currency = $(".currency option:selected").val();
    //     if($("#phone").val() != '')
    //     {
    //         //alert(products);
    //         var phone = $("#phone").val();
    //         var shipping_country = $("#shipping_country option:selected").val();
    //         var shipping_state = $(this).attr('data-value');
    //         var billing_country = $("#billing_country option:selected").val();
    //         var billing_state = $("#billing_state option:selected").val();
    //         var shipping = $("input[name='shipping']:checked").val();
    //         var email = $("#email").val();
    //         var alter_email = $("#alter_email").val();
    //             var billing_firstname = $("#billing_firstname").val();
    //var messenger = $(".messenger option:selected").val();
   // var messenger_id = $("#messenger_id").val();
    //         var billing_lastname = $("#billing_lastname").val();
    //         var billing_city = $("#billing_city").val();
    //         var billing_address = $("#billing_address").val();
    //         var billing_zip = $("#billing_zip").val();
    //         var shipping_city = $("#shipping_city").val();
    //         var shipping_address = $("#shipping_address").val();
    //         var shipping_zip = $("#shipping_zip").val();
    //         var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
    //         $.ajax({
    //                    url:'/checkout/content_ajax.php',   
    //                    type: 'POST',
    //                    cache: false,
    //                    dataType: 'html',
    //                    data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
    //                    'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
    //                    'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
    //                    'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open, 'authorization': 1,
    //                    'language':language, 'currency':currency},
          
    //                //        async: false,
          
    //                    success:function(data){
    //                       $('.wrapper').html(data);
    //                   }
          
    //                });
    //     }
       
    // });


    $(".discount-line__button").click(function(e) {
        e.preventDefault();
        var language = $(".language option:selected").val();
        var currency = $(".currency option:selected").val();
        var phone_code = $(".phone_code option:selected").attr('id')
        var phone = $("#phone").val();
        var shipping = $("input[name='shipping']:checked").val();
        var billing_country = $("#billing_country option:selected").val();
        var billing_state = $("#billing_state option:selected").val();
        var email = $("#email").val();
        var alter_email = $("#alter_email").val();
        var billing_firstname = $("#billing_firstname").val();
        var billing_lastname = $("#billing_lastname").val();
        var billing_city = $("#billing_city").val();
        var billing_address = $("#billing_address").val();
        var billing_zip = $("#billing_zip").val();
        var shipping_country = $("#shipping_country option:selected").val();
        var shipping_state = $("#shipping_state option:selected").val();
        var shipping_city = $("#shipping_city").val();
        var shipping_address = $("#shipping_address").val();
        var shipping_zip = $("#shipping_zip").val();
        var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
        var theme = $("#theme").val();
        var coupon = document.getElementById("coupon").value;//$("#coupon").val();
        var card_month = $(".card_month option:selected").val();
        var card_year = $(".card_year option:selected").val();
        var card_holder = $("#card_holder").val();
        var card_number = $("#card_numb").val();
        var card_cvv = $("#cvc_2").val();

        $.ajax({
        url:'/checkout/content_ajax.php',   
        type: 'POST',
        cache: false,
        dataType: 'html',
        data: {'products':products,'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
        'shipping':shipping,'discount':discount,
        'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
        'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
        'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
        'language':language, 'currency':currency, 'theme':theme, 'coupon':coupon, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},

        success:function(data){

            $('.wrapper').html(data);
        }

        });
    });


    $('#proccess_sepa').click(function() {
        if(validate_form()){
            document.body.classList.remove('loaded');
            //alert('aaaaa');
            var language = $(".language option:selected").val();
            var currency = $(".currency option:selected").val();
            var phone_code = $(".phone_code option:selected").attr('id')
            var products = $("#product").val();
            var bonus = $("#bonus").val();
            var shipping = $("input[name='shipping']:checked").val();
            var billing_country = $("#billing_country option:selected").val();
            var billing_state = $("#billing_state option:selected").val();
            var phone = '+' + $(".phone_code option:selected").val() + $("#phone").val();
            var email = $("#email").val();
            var alter_email = $("#alter_email").val();
            var billing_firstname = $("#billing_firstname").val();
            // var messenger = $(".messenger option:selected").val();
            // var messenger_id = $("#messenger_id").val();
            var theme = $("#theme").val();
            //alert(messenger);
            //alert(messenger_id);
            var billing_lastname = $("#billing_lastname").val();
            var billing_city = $("#billing_city").val();
            var billing_address = $("#billing_address").val();
            var billing_zip = $("#billing_zip").val();
            var shipping_country = $("#shipping_country option:selected").val();
            var shipping_state = $("#shipping_state option:selected").val();
            var shipping_city = $("#shipping_city").val();
            var shipping_address = $("#shipping_address").val();
            var shipping_zip = $("#shipping_zip").val();
            var different = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
            var payment_type = $(".card_type option:selected").val();
            var total = $("#total").val();
            var product_total = $("#product_total").val();
            var reorder_discount = $("#reorder_discount").val();
            var coupon_discount = $("#coupon_discount").val();
            var shipping_price = $("#shipping_price").val();
            var aff = $("#aff").val();
            var saff = $("#saff").val();
            var curr = $("#curr").val();
            var store_skin = $("#store_skin").val();
            var domain_from = $("#domain_from").val();
            var ref = $("#ref").val();
            var fingerprint = $("#fingerprint").val();
            var invoiceId = $("#invoiceId").val();
            var recurring = $("input[name='recurring2']:checked").val();
            var refc = $("#refc").val(); 
            if(recurring = 'yes')
            {
                var recurring_period = $("input[name='recurring_period2']:checked").val();
            }
            else
            {
                var recurring_period = 0;
            }
            var coupon = $("#coupon").val();

            var resolution = window.screen.width + 'x' + window.screen.height;

            const weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

            const d = new Date();
            var day = weekday[d.getDay()];
            var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();


            $.ajax({
                url:'/checkout/sepa_order.php',
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {'products':products, 'screen_resolution':resolution, 'customer_date':date, 'coupon':coupon, 'refc':refc, 'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                'shipping':shipping,'discount':discount,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email, 
                'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'different':different,
                'proccess':1,'payment_type':payment_type, 'total':total,'product_total':product_total, 'reorder_discount':reorder_discount, 'shipping_price':shipping_price,'aff':aff,'saff':saff,'curr':curr,'store_skin':store_skin,'domain_from':domain_from,
                'language':language, 'currency':currency, 'ref':ref, 'fingerprint':fingerprint, 'coupon_discount':coupon_discount, 'invoiceId': invoiceId, 'theme':theme, 'phone_code':phone_code, 'recurring_period':recurring_period},

                success:function(data){
                    quit_flag = 1;
                    //alert(data);
                    //console.log(data);
                    if(data.includes('error'))
                    {
                        var error = data.split('|');
                        if(error[1] == '1')
                        {
                            document.body.classList.add('loaded');
                            var popup1 = document.getElementById("myPopup1");
                            popup1.classList.add("show");
                        }
                        if(error[1] == '3')
                        {
                            document.body.classList.add('loaded');
                            var popup1 = document.getElementById("myPopup6");
                            popup1.classList.add("show");
                        }
                    }
                    else
                    {
                        window.location.replace(data);
                    }
                    //$('.wrapper').html(data);
                }

                });

        }

    });


    $('#order_form').unbind('submit').bind('submit',function(e) {
            e.preventDefault();

 

            var value = $('#shipping_city').val();
            value = value.trim();
            if (value.length > 0)
            {
                $("#check_city").val(1);
            }
            else
            {
                $("#check_city").val(0);
            }
    
            var value = $('#shipping_address').val();
            value = value.trim();
            if (value.length > 0)
            {
                $("#check_address").val(1);
            }
            else
            {
                $("#check_address").val(0);
            }
    
            var value = $('#shipping_zip').val();
            value = value.trim();
            if (value.length > 0)
            {
                $("#check_zip").val(1);
            }
            else
            {
                $("#check_zip").val(0);
            }

        if(!$(".info-form__add-inputs").is(":hidden"))
        {
            if($('#check_city').val() == 1 && $('#check_address').val() == 1 && $('#check_zip').val() == 1)
            {
                var shipping_check = true;
            }
            else
            {
                var shipping_check = false;
            }
        }
        else{
            var shipping_check = true;
        }

        var card_month = $("#card_month").val();
        var card_year = $("#card_year").val();
        var today = new Date();
        var mm = today.getMonth() + 1;

        if(parseInt(card_year) == today.getFullYear())
        {
            if(card_month < mm || card_month > 12)
            {
                $("#check_expire").val(0);
            }
            else{
                $("#check_expire").val(1);  
            }
        }
        else if(parseInt(card_year) > today.getFullYear() && card_month < 12)
        {
            $("#check_expire").val(1);
        }
        else
        {
            $("#check_expire").val(0);
        }

        
        if(!valid_credit_card($("#card_numb").val().replace(/\s/g, "")) || $("#card_numb").val().replace(/\s/g, "") == "")
        {
            $("#check_number").val(0);
        }
        else
        {
            $("#check_number").val(1);
        } 

        //alert($("#check_number").val());


        var cvv = document.getElementById('cvc_2');
        var cvv_flag = true;

        if(cvv.value.length < 3 || cvv.value.length > 4)
        {
            //alert(cvv.value);
            cvv.setCustomValidity('Incorrect cvv');
            cvv.reportValidity();
            cvv_flag = false;
        }
        else
        {
            cvv_flag = true;
        }

        //alert(cvv.value);

        if($('#check_number').val() == 1 && shipping_check && $('#check_expire').val() == 1 && $('#check_email').val() == 1 && cvv_flag)//if($('#check_number').val() == 1 && $('#check_expire').val() == 1 && shipping_check)
        {
            //alert("AAAAA");
            document.body.classList.remove('loaded');
            //alert('aaaaa');
            var language = $(".language option:selected").val();
            var currency = $(".currency option:selected").val();
            var phone_code = $(".phone_code option:selected").attr('id')
            var products = $("#product").val();
            var bonus = $("#bonus").val();
            var shipping = $("input[name='shipping']:checked").val();
            var recurring = $("input[name='recurring']:checked").val();
            if(recurring = 'yes')
            {
                var recurring_period = $("input[name='recurring_period']:checked").val();
            }
            else
            {
                var recurring_period = 0;
            }
            
            //alert(recurring_period);
            var billing_country = $("#billing_country option:selected").val();
            var billing_state = $("#billing_state option:selected").val();
            var phone = '+' + $(".phone_code option:selected").val() + $("#phone").val();
            //alert(phone);
            var email = $("#email").val();
            var alter_email = $("#alter_email").val();
            var billing_firstname = $("#billing_firstname").val();
            // var messenger = $(".messenger option:selected").val();
            // var messenger_id = $("#messenger_id").val();
            //alert(messenger);
            //alert(messenger_id);
            var billing_lastname = $("#billing_lastname").val();
            var billing_city = $("#billing_city").val();
            var billing_address = $("#billing_address").val();
            var billing_zip = $("#billing_zip").val();
            var shipping_country = $("#shipping_country option:selected").val();
            var shipping_state = $("#shipping_state option:selected").val();
            var shipping_city = $("#shipping_city").val();
            var shipping_address = $("#shipping_address").val();
            var shipping_zip = $("#shipping_zip").val();
            var different = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
            var payment_type = $(".card_type option:selected").val();
            var card_month = $(".card_month option:selected").val();
            var card_year = $(".card_year option:selected").val();
            var card_holder = $("#card_holder").val();
            var card_number = $("#card_numb").val().replace(/\s/g, "");
            //alert(card_number);
            var card_cvv = $("#cvc_2").val();
            var total = $("#total").val();
            var product_total = $("#product_total").val();
            var reorder_discount = $("#reorder_discount").val();
            var coupon_discount = $("#coupon_discount").val();
            var shipping_price = $("#shipping_price").val();
            var aff = $("#aff").val();
            var saff = $("#saff").val();
            var theme = $("#theme").val();
            var curr = $("#curr").val();
            var store_skin = $("#store_skin").val();
            var domain_from = $("#domain_from").val();
            var ref = $("#ref").val();
            var fingerprint = $(".fingerprint").val();
            var coupon = $("#coupon").val();
            var refc = $("#refc").val(); 

            var resolution = window.screen.width + 'x' + window.screen.height;

            const weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

            const d = new Date();
            var day = weekday[d.getDay()];
            var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();

            $.ajax({
                url:'/checkout/checkout.php',
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {'products':products, 'screen_resolution':resolution, 'customer_date':date, 'coupon':coupon, 'refc':refc, 'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                'shipping':shipping,'discount':discount,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email, 
                'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'different':different,
                'proccess':1,'card_month':card_month,'payment_type':payment_type,'card_year':card_year,'card_holder':card_holder, 'card_number':card_number,'card_cvv':card_cvv,
                'total':total,'product_total':product_total, 'reorder_discount':reorder_discount, 'shipping_price':shipping_price,'aff':aff,'saff':saff,'curr':curr,'store_skin':store_skin,'domain_from':domain_from,
                'language':language, 'currency':currency, 'ref':ref, 'fingerprint':fingerprint, 'coupon_discount':coupon_discount, 'theme':theme, 'phone_code': phone_code, 'recurring_period':recurring_period},

                success:function(data){
                    quit_flag = 1;
                    //alert(data);
                    //console.log(data);
                    if(data.includes('error'))
                    {
                        var error = data.split('|');
                        if(error[1] == '1')
                        {
                            document.body.classList.add('loaded');
                            var popup1 = document.getElementById("myPopup1");
                            popup1.classList.add("show");
                        }

                        if(error[1] == '2')
                        {
                            //alert("a");
                            document.body.classList.add('loaded');
                            var popup5 = document.getElementById("myPopup5");
                            popup5.classList.add("show");
                        }

                        if(error[1] == '3')
                        {
                            document.body.classList.add('loaded');
                            var popup1 = document.getElementById("myPopup6");
                            popup1.classList.add("show");
                        }
                    }
                    else
                    {
                        window.location.replace(data);
                    }
                    //$('.wrapper').html(data);
                }

                });
    }
    else
    {
        var popup = document.getElementById("myPopup");
        var popup1 = document.getElementById("myPopup1");
        var popup2 = document.getElementById("myPopup2");
        var popup3 = document.getElementById("myPopup3");
        var popup4 = document.getElementById("myPopup4");
        var popup5 = document.getElementById("myPopup5");

        if($('#check_number').val() == 0)
        {
            popup.classList.add("show");
        }

        if($('#check_expire').val() == 0)
        {
            popup1.classList.add("show");
        }

        if($('#check_city').val() == 0)
        {
            popup2.classList.add("show");
        }

        if($('#check_address').val() == 0)
        {
            popup3.classList.add("show");
        }

        if($('#check_zip').val() == 0)
        {
            popup4.classList.add("show");
        }

        if($('#check_email').val() == 0)
        {
            popup5.classList.add("show");
        }

    }
    });


    $( ".language .select__option" ).click(function() {
        var phone = $("#phone").val();
        var shipping_country = $("#shipping_country option:selected").val();
        var shipping_state = $("#shipping_state option:selected").val();
        var phone_code = $(".phone_code option:selected").attr('id')
        var language = $(this).attr('data-value');
        var currency = $(".currency option:selected").val();
        var billing_country = $("#billing_country option:selected").val();
        var billing_state = $("#billing_state option:selected").val();
        var shipping = $("input[name='shipping']:checked").val();
        var email = $("#email").val();
        var alter_email = $("#alter_email").val();
        var billing_firstname = $("#billing_firstname").val();
        // var messenger = $(".messenger option:selected").val();
        // var messenger_id = $("#messenger_id").val();
        var billing_lastname = $("#billing_lastname").val();
        var billing_city = $("#billing_city").val();
        var billing_address = $("#billing_address").val();
        var billing_zip = $("#billing_zip").val();
        var theme = $("#theme").val();
        var shipping_city = $("#shipping_city").val();
        var shipping_address = $("#shipping_address").val();
        var shipping_zip = $("#shipping_zip").val();
        var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
        var card_month = $(".card_month option:selected").val();
        var card_year = $(".card_year option:selected").val();
        var card_holder = $("#card_holder").val();
        var card_number = $("#card_numb").val();
        var card_cvv = $("#cvc_2").val();
        $.ajax({
                   url:'/checkout/content_ajax.php',   
                   type: 'POST',
                   cache: false,
                   dataType: 'html',
                   data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                   'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
                   'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                   'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
                   'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
      
               //        async: false,
      
                   success:function(data){
                      $('.wrapper').html(data);
                  }
      
               });
    });

    $( ".currency .select__option" ).click(function() {
        var phone = $("#phone").val();
        var shipping_country = $("#shipping_country option:selected").val();
        var shipping_state = $("#shipping_state option:selected").val();
        var phone_code = $(".phone_code option:selected").attr('id')
        var language = $(".language option:selected").val();
        var currency = $(this).attr('data-value');
        var billing_country = $("#billing_country option:selected").val();
        var billing_state = $("#billing_state option:selected").val();
        var shipping = $("input[name='shipping']:checked").val();
        var email = $("#email").val();
        var alter_email = $("#alter_email").val();
        var billing_firstname = $("#billing_firstname").val();
        // var messenger = $(".messenger option:selected").val();
        // var messenger_id = $("#messenger_id").val();
        var theme = $("#theme").val();
        var billing_lastname = $("#billing_lastname").val();
        var billing_city = $("#billing_city").val();
        var billing_address = $("#billing_address").val();
        var billing_zip = $("#billing_zip").val();
        var shipping_city = $("#shipping_city").val();
        var shipping_address = $("#shipping_address").val();
        var shipping_zip = $("#shipping_zip").val();
        var shipping_open = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
        var card_month = $(".card_month option:selected").val();
        var card_year = $(".card_year option:selected").val();
        var card_holder = $("#card_holder").val();
        var card_number = $("#card_numb").val();
        var card_cvv = $("#cvc_2").val();
        $.ajax({
                   url:'/checkout/content_ajax.php',   
                   type: 'POST',
                   cache: false,
                   dataType: 'html',
                   data: {'products':products,'bonus':bonus,'billing_country':billing_country,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                   'shipping':shipping,'discount':discount, 'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email,
                   'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                   'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'shipping_open':shipping_open,
                   'language':language, 'currency':currency, 'theme':theme, 'phone_code':phone_code, 'card_month':card_month, 'card_year':card_year, 'card_holder':card_holder, 'card_number':card_number, 'card_cvv':card_cvv},
      
               //        async: false,
      
                   success:function(data){
                      $('.wrapper').html(data);
                  }
      
               });
    });



    function pollFunc(fn, timeout, interval) {
        var startTime = (new Date()).getTime();
        interval = interval || 1000,
        canPoll = true;
    
        (function p() {
            canPoll = ((new Date).getTime() - startTime ) <= timeout;
            if (!fn() && canPoll)  { // ensures the function exucutes
                setTimeout(p, interval);
            }
        })();
    }
    
    

    function CheckPayment() {
        var invoiceId = document.getElementById('invoiceId');
        //alert($("#crypto_currency option:selected").val());
        if (invoiceId.value) {
            $.ajax({
                url:'/checkout/check_payment.php', 
                type: 'POST',
                cache: false,
                dataType: 'html',
                data: {'invoiceId':invoiceId.value},
   
            //        async: false,
   
                success:function(data){
                    var result = JSON.parse(data);
                   if(result.status == 3){                    
                        document.body.classList.remove('loaded');
                        //alert('aaaaa');
                        var language = $(".language option:selected").val();
                        var currency = $(".currency option:selected").val();
                        var phone_code = $(".phone_code option:selected").attr('id')
                        var products = $("#product").val();
                        var bonus = $("#bonus").val();
                        var shipping = $("input[name='shipping']:checked").val();
                        var billing_country = $("#billing_country option:selected").val();
                        var billing_state = $("#billing_state option:selected").val();
                        var phone = '+' + $(".phone_code option:selected").val() + $("#phone").val();
                        var email = $("#email").val();
                        var alter_email = $("#alter_email").val();
                        var billing_firstname = $("#billing_firstname").val();
                        // var messenger = $(".messenger option:selected").val();
                        // var messenger_id = $("#messenger_id").val();
                        //alert(messenger);
                        //alert(messenger_id);
                        var theme = $("#theme").val();
                        var billing_lastname = $("#billing_lastname").val();
                        var billing_city = $("#billing_city").val();
                        var billing_address = $("#billing_address").val();
                        var billing_zip = $("#billing_zip").val();
                        var shipping_country = $("#shipping_country option:selected").val();
                        var shipping_state = $("#shipping_state option:selected").val();
                        var shipping_city = $("#shipping_city").val();
                        var shipping_address = $("#shipping_address").val();
                        var shipping_zip = $("#shipping_zip").val();
                        var different = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
                        var payment_type = $(".card_type option:selected").val();
                        var total = $("#total").val();
                        var product_total = $("#product_total").val();
                        var reorder_discount = $("#reorder_discount").val();
                        var coupon_discount = $("#coupon_discount").val();
                        var shipping_price = $("#shipping_price").val();
                        var aff = $("#aff").val();
                        var saff = $("#saff").val();
                        var curr = $("#curr").val();
                        var store_skin = $("#store_skin").val();
                        var domain_from = $("#domain_from").val();
                        var ref = $("#ref").val();
                        var fingerprint = $("#fingerprint").val();
                        var invoiceId = $("#invoiceId").val();
                        var refc = $("#refc").val();
                        var crypto_currency = $('input[name="crypt_currency"]:checked').val();
                        //alert(crypto_currency);
                        var recurring = $("input[name='recurring1']:checked").val();
                        if(recurring = 'yes')
                        {
                            var recurring_period = $("input[name='recurring_period1']:checked").val();
                        }
                        else
                        {
                            var recurring_period = 0;
                        }
                        var coupon = $("#coupon").val();

                        var resolution = window.screen.width + 'x' + window.screen.height;

                        const weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            
                        const d = new Date();
                        var day = weekday[d.getDay()];
                        var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();            
            
                        $.ajax({
                            url:'/checkout/crypto_order.php',
                            type: 'POST',
                            cache: false,
                            dataType: 'html',
                            data: {'products':products, 'screen_resolution':resolution, 'customer_date':date, 'coupon':coupon, 'refc':refc, 'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                            'shipping':shipping,'discount':discount,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email, 
                            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'different':different,
                            'proccess':1,'payment_type':payment_type, 'total':total,'product_total':product_total, 'reorder_discount':reorder_discount, 'shipping_price':shipping_price,'aff':aff,'saff':saff,'curr':curr,'store_skin':store_skin,'domain_from':domain_from,
                            'language':language, 'currency':currency, 'ref':ref, 'fingerprint':fingerprint, 'coupon_discount':coupon_discount, 'crypto_currency':crypto_currency, 'invoiceId': invoiceId, 'theme':theme, 'phone_code':phone_code, 'recurring_period':recurring_period,
                            'merchant_id':result.merchantId,'purse':result.purse,'amount':result.amount,'amountInPayCurrency':result.amountInPayCurrency,'commission':result.complexCommission},
            
                            success:function(data){
                                quit_flag = 1;
                                // alert(data);
                                console.log(data);
                                window.location.replace(data);
                                throw new Error("ok");
                                //$('.wrapper').html(data);
                            }
            
                            });

                   }
                   else if(data == 'Unpaid')
                   {
                    //alert('pizdec');
                   }
                   else if(data == 'Waiting')
                   {
                    //alert('wait');
                   }
                   else if(result.status == 5)
                   {
                        document.body.classList.remove('loaded');
                        //alert('aaaaa');
                        var language = $(".language option:selected").val();
                        var currency = $(".currency option:selected").val();
                        var phone_code = $(".phone_code option:selected").attr('id')
                        var products = $("#product").val();
                        var bonus = $("#bonus").val();
                        var shipping = $("input[name='shipping']:checked").val();
                        var billing_country = $("#billing_country option:selected").val();
                        var billing_state = $("#billing_state option:selected").val();
                        var phone = '+' + $(".phone_code option:selected").val() + $("#phone").val();
                        var email = $("#email").val();
                        var alter_email = $("#alter_email").val();
                        var billing_firstname = $("#billing_firstname").val();
                        // var messenger = $(".messenger option:selected").val();
                        // var messenger_id = $("#messenger_id").val();
                        //alert(messenger);
                        //alert(messenger_id);
                        var theme = $("#theme").val();
                        var billing_lastname = $("#billing_lastname").val();
                        var billing_city = $("#billing_city").val();
                        var billing_address = $("#billing_address").val();
                        var billing_zip = $("#billing_zip").val();
                        var shipping_country = $("#shipping_country option:selected").val();
                        var shipping_state = $("#shipping_state option:selected").val();
                        var shipping_city = $("#shipping_city").val();
                        var shipping_address = $("#shipping_address").val();
                        var shipping_zip = $("#shipping_zip").val();
                        var different = $(".info-form__add-inputs").is(":hidden") ? 0 : 1;
                        var payment_type = $(".card_type option:selected").val();
                        var total = $("#total").val();
                        total = total * 0.9;
                        var product_total = $("#product_total").val();
                        var reorder_discount = $("#reorder_discount").val();
                        var coupon_discount = $("#coupon_discount").val();
                        var shipping_price = $("#shipping_price").val();
                        var aff = $("#aff").val();
                        var saff = $("#saff").val();
                        var curr = $("#curr").val();
                        var store_skin = $("#store_skin").val();
                        var domain_from = $("#domain_from").val();
                        var ref = $("#ref").val();
                        var fingerprint = $("#fingerprint").val();
                        var invoiceId = $("#invoiceId").val();
                        var refc = $("#refc").val();
                        var coupon = $("#coupon").val();
                        var crypto_currency = $('input[name="crypt_currency"]:checked').val();
                        var recurring = $("input[name='recurring1']:checked").val();
                        if(recurring = 'yes')
                        {
                            var recurring_period = $("input[name='recurring_period1']:checked").val();
                        }
                        else
                        {
                            var recurring_period = 0;
                        }

                        var resolution = window.screen.width + 'x' + window.screen.height;

                        const weekday = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
            
                        const d = new Date();
                        var day = weekday[d.getDay()];
                        var date = day + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();  
            
                        $.ajax({
                            url:'/checkout/crypto_order.php',
                            type: 'POST',
                            cache: false,
                            dataType: 'html',
                            data: {'underpaid':1,'products':products, 'screen_resolution':resolution, 'customer_date':date, 'coupon':coupon, 'refc':refc, 'bonus':bonus,'Insurance':  document.getElementsByName("Insurance")[0].value,'SecretPackage': document.getElementsByName("SecretPackage")[0].value,
                            'shipping':shipping,'discount':discount,'billing_country':billing_country,'billing_state':billing_state,'phone':phone,'email':email,'alter_email':alter_email, 
                            'billing_firstname':billing_firstname, 'billing_lastname':billing_lastname, 'billing_city':billing_city, 'billing_address':billing_address, 'billing_zip':billing_zip,
                            'shipping_country':shipping_country,'shipping_state':shipping_state,'shipping_city':shipping_city, 'shipping_address':shipping_address,'shipping_zip':shipping_zip, 'different':different,
                            'proccess':1,'payment_type':payment_type, 'total':total,'product_total':product_total, 'reorder_discount':reorder_discount, 'shipping_price':shipping_price,'aff':aff,'saff':saff,'curr':curr,'store_skin':store_skin,'domain_from':domain_from,
                            'language':language, 'currency':currency, 'ref':ref, 'fingerprint':fingerprint, 'coupon_discount':coupon_discount, 'crypto_currency':crypto_currency, 'invoiceId': invoiceId, 'theme':theme, 'phone_code':phone_code, 'recurring_period':recurring_period,
                            'merchant_id':result.merchantId,'purse':result.purse,'amount':result.amount,'amountInPayCurrency':result.amountInPayCurrency,'commission':result.complexCommission},
            
                            success:function(data){
                                quit_flag = 1;
                                window.location.replace(data);
                                throw new Error("ok");
                            }
            
                            });
                    }
               }
   
            });         
        }
    }


    // $( "#crypto_total" ).click(function() {
    //     var text = document.getElementById('crypto_total').innerHTML;
    //     text = text.split(' ');
    //     text = text[0];
        
    //     navigator.clipboard.writeText(text)
    //     .then(() => {
    //         //console.log('Text copied to clipboard');

    //         var popup = document.getElementById("myPopup6");
    //         popup.innerHTML = 'Copied!';
    //         popup.classList.add("show");
    //     })
    //     .catch(err => {
    //         console.error('Error in copying text: ', err);
    //     });
    // });

    $( "#paid" ).click(function(e) {
        e.preventDefault();
        document.getElementById('paid').style.display="none";
        //document.getElementById('qr_code').style.display="none";
        document.getElementById('waiting').style.display="block";

        //alert(recurring_period);

    });

    $( "#waiting" ).click(function(e) {
        e.preventDefault();
    });

    // $( "#purse").click(function() {
    //     var text = document.getElementById('purse').innerHTML;
    //     text = text.split(' ');
    //     text = text[0];
        
    //     navigator.clipboard.writeText(text)
    //     .then(() => {
    //         //console.log('Text copied to clipboard');
    //         var popup = document.getElementById("myPopup7");
    //         popup.innerHTML = 'Copied!';
    //         popup.classList.add("show");
    //     })
    //     .catch(err => {
    //         console.error('Error in copying text: ', err);
    //     });
    // });

    $( "#crypto_total" ).hover(function() {
        var popup = document.getElementById("myPopup6");
        popup.innerHTML = 'Click to copy!';
        popup.classList.add("show");
    }, function() {
        var popup = document.getElementById("myPopup6");
        popup.classList.remove("show");
    });

    $( "#purse").hover(function() {
        var popup = document.getElementById("myPopup7");
        popup.innerHTML = 'Click to copy!';
        popup.classList.add("show");
    }, function() {
        var popup = document.getElementById("myPopup7");
        popup.classList.remove("show");
    });


