$(document).ready(function () {
    var aux = 1;
    var actionsUrl = [];
    var envialia = {
        envialia: false,
        type: false,
        mt: false,
        hour: false,
        envialia_charge: envialia_charge,
        envialia_prices: [],
        reset: function () {
            this.envialia = false;
            this.type = false;
            this.mt = false;
            this.hour = false;
        },
        getParameters: function () {
            if (getCookie('envialia'))
                return '?envialia=true&type=' + getCookie('type') + '&mt=' + getCookie('mt') + '&hour=' + getCookie('hour')+'&envialia_charge='+envialia.envialia_charge;
            return "";
        },
        resetPrices: function(){
            var cont = 0;
            $('.delivery-option').each(function(){
                if($(this).find('.envialia-price').length)
                    $(this).find('.envialia-price').first().html(envialia.envialia_prices[cont++]);
            })
        }
    };

    $('.delivery-option').each(function(){
        if($(this).find('.envialia-price').length)
            envialia.envialia_prices.push($(this).find('.envialia-price').first().html());
    })

    $('[name="payment-option"]').each(function () {
        actionsUrl.push($('#pay-with-payment-option-' + aux + '-form form').attr('action'))
        $(this).attr('data-number', aux++);
    });

    $(document).on('click', '[name="payment-option"]', function (e) {
        aux = $('#checkout-payment-step').find('input[type="radio"]:checked').data('number');
        var action = actionsUrl[aux - 1];
        $('#pay-with-payment-option-' + aux + '-form form').attr('action', action + envialia.getParameters());


    });

    $(document).on('click', '.shipping-radio', function () {
        envialia.resetPrices();
        $('input[name="envialia-radio"]').attr('checked', false);
        envialia.reset();
        if ($(this).parents('.delivery-option').find('input[name="envialia-radio"]').length) {
            envialia.envialia = true;
            $(this).parents('.delivery-option').find('input[name="envialia-radio"]').first().click();
        }
        else
            envialia.envialia = false;
    });

    $(document).on('click', 'input[name="envialia-radio"]', function () {
        envialia.resetPrices();
        envialia.type = $(this).val();
        $('select[name="hour"], select[name="mt"]').prop('selectedIndex',0);
    });

    $(document).on('change', 'select[name="mt"]', function () {
        envialia.envialia = true;
        envialia.resetPrices();
        envialia.hour=false;
        envialia.mt = $(this).val();
        var price = $(this).parents('.delivery-option').children().first().find('.envialia-price').first().html();
        price = addEnvialiaCharge(price);
        $(this).parents('.delivery-option').children().first().find('.envialia-price').first().html(price);
        var id = setInterval(function(){
            if($.active == 0){
                $('#cart-subtotal-shipping .value').html(price + " \u20AC");
                var total = parseFloat(getFloat($('#cart-subtotal-products .value').html()).replace(',', '.')) + parseFloat(price.replace(',', '.'));
                $('.cart-summary-line.cart-total .value').html(total.toString().replace('.', ',') + " \u20AC");
                clearInterval(id);
            }
        },1000);

    });

    $(document).on('change', 'select[name="hour"]', function () {
        envialia.envialia = true;
        envialia.resetPrices();
        envialia.mt = false;
        envialia.hour = $(this).val();
        var price = $(this).parents('.delivery-option').children().first().find('.envialia-price').first().html();
        price = addEnvialiaCharge(price);
        $(this).parents('.delivery-option').children().first().find('.envialia-price').first().html(price);
        var id = setInterval(function(){
            if($.active == 0){
                $('#cart-subtotal-shipping .value').html(price + " \u20AC");
                var total = parseFloat(getFloat($('#cart-subtotal-products .value').html()).replace(',', '.')) + parseFloat(price.replace(',', '.'));
                $('.cart-summary-line.cart-total .value').html(total.toFixed(2).toString().replace('.', ',') + " \u20AC");
                clearInterval(id);
            }
        },1000);
    });

    $(document).on('click', '[name="confirmDeliveryOption"]', function () {

        if (envialia.envialia) {
            setCookie('envialia', true);
            setCookie('type', envialia.type);
            setCookie('mt', envialia.mt);
            setCookie('hour', envialia.hour);
        }
        else {
            deleteCookie('envialia');
            deleteCookie('type');
            deleteCookie('mt');
            deleteCookie('hour');
        }
    });


    function addEnvialiaCharge(price){
        var priceNumber = getFloat(price);
        var priceFloat = priceNumber.toString().split(',');
        if(typeof priceFloat[1] != typeof undefined){
            priceFloat[1] = parseFloat(priceFloat[1])/100
            var price = parseFloat(priceFloat[0])+parseFloat(priceFloat[1])
        }
        else
        var price = parseFloat(priceFloat[0]);
        price = price + parseFloat(envialia.envialia_charge);
        return price.toFixed(2).toString().replace(".", ",");

    }

    function getFloat($string){
        var priceNumber = "";
        var priceString = "";
        var price = $string.toString().split('');
        for(var i = 0, assing = false, number = true, valString = false; i < price.length; i++){
            if(price[i] != '&' && price[i] != ' ' && priceString == "" && number){
                priceNumber += price[i];

            }
            else {priceString += price[i];}
        }
        return priceNumber;
    }

    function setCookie(cname, cvalue) {
        var d = new Date();
        d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return false;
    }

    function deleteCookie(cname) {
        document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

});