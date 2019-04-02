/*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from EURL ébewè - www.ebewe.net
* Use, copy, modification or distribution of this source file without written
* license agreement from the EURL ébewè is strictly forbidden.
* In order to obtain a license, please contact us: contact@ebewe.net
* ...........................................................................
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe EURL ébewè - www.ebewe.net
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la EURL ébewè est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la EURL ébewè a l'adresse: contact@ebewe.net
* ...........................................................................
*
*  @package   Productcarrier
*  @author    Paul MORA
*  @copyright Copyright (c) 2011-2017 EURL ébewè - www.ebewe.net - Paul MORA
*  @license   Commercial license
*  Support by mail  :  contact@ebewe.net
*/

$(document).ready(function(){

    /* Product Carrier Update */
    var SuccessMessageProduct = $('#success_message_product').val();
    var ErrorMessageProduct = $('#error_message_product').val();
    var SuccessMessageCarrier1 = $('#success_message_carrier_1').val();
    var SuccessMessageCarrier2 = $('#success_message_carrier_2').val();
    var SuccessMessageCarrier3 = $('#success_message_carrier_3').val();
    var ErrorMessageCarrier = $('#error_message_carrier').val();

    var all_products = '';
    $('[id^="carrier_product_"]').each(function() {
        var id_product = $(this).attr('id');
        id_product = id_product.substr( id_product.lastIndexOf('_')+1 );
        all_products += id_product + ',';
    });
    $('#all_products').val(all_products);

    $('.update_carrier').click(function(e) {
        e.preventDefault();
        var id_product = $(this).attr('data-product');
        var product_name = $('#carrier_product_' + id_product).parent('td').prev('.product_name').html();
        var id_carrier = $(this).attr('data-carrier');

        if ( $(this).hasClass('action-enabled') )
            $('#carrier_product_' + id_product).val( $('#carrier_product_' + id_product).val().replace( id_carrier + ',', '' ) );
        else if ( $(this).hasClass('action-disabled') )
            $('#carrier_product_' + id_product).val( $('#carrier_product_' + id_product).val() + id_carrier + ',' );

        $(this).toggleClass('action-enabled').toggleClass('action-disabled');
        $(this).children('i').toggleClass('icon-check').toggleClass('icon-remove');

        if ( $('#carrier_product_' + id_product).val() == '' )
            var carriers = $('#all_carriers').val();
        else
            var carriers = $('#carrier_product_' + id_product).val();

        $.post( location.href, { 'id_product': id_product, 'carriers': carriers })
            .done(function(data) {
                $('[id^="product_carrier_' + id_product + '_"]').removeClass('action-enabled').addClass('action-disabled');
                $('[id^="product_carrier_' + id_product + '_"]').children('i').removeClass('icon-check').addClass('icon-remove');
                var carriers = '';
                JSON.parse(data, function (key, id_carrier) {
                    if ($.isNumeric(id_carrier)){
                        $('#product_carrier_' + id_product + '_' + id_carrier).removeClass('action-disabled').addClass('action-enabled');
                        $('#product_carrier_' + id_product + '_' + id_carrier).children('i').removeClass('icon-remove').addClass('icon-check');
                        carriers += id_carrier + ',';
                    }
                });
                $('#carrier_product_' + id_product).val( carriers );
                showSuccessMessage(SuccessMessageProduct + ' ' + id_product + ' - ' + product_name);
            })
            .fail(function() {
                showErrorMessage(ErrorMessageProduct + ' ' + id_product + ' - ' + product_name);
            });
    });

    /* Enable / Disable All products for a carrier */
    var all_carriers = $('#all_carriers').val();
    all_carriers = all_carriers.split(',');
    for (var i = 0; i < all_carriers.length; i++)
        if ( all_carriers[i] != '' )
            $('tr.filter').children('.carrier_' + all_carriers[i]).html('' +
                '<a data-carrier="' + all_carriers[i] + '" class="select_all uncheck_all btn btn-default"><i class="icon-remove"></i></a> ' +
                '<a data-carrier="' + all_carriers[i] + '" class="select_all check_all btn btn-default"><i class="icon-check"></i></a>' +
                '');

    $('.select_all').click(function() {
        var check = '';
        if ( $(this).hasClass('check_all') )
            check = true;
        else if ( $(this).hasClass('uncheck_all') )
            check = false;
        var id_carrier = $(this).attr('data-carrier');
        var carrier_name = $('th.carrier_' + id_carrier).first().children('.title_box').html();

        confirm(carrier_name, check, function(answer) {
            if ( answer == true) {

                var products = $('#all_products').val();

                $.post( location.href, { 'products': products, 'id_carrier': id_carrier, 'check': check })
                    .done(function(data) {
                        if (check == true) {
                            JSON.parse(data, function (key, id_product) {
                                $('tbody').children('tr').children('.carrier_' + id_carrier).children('a').removeClass('action-disabled').addClass('action-enabled');
                                $('tbody').children('tr').children('.carrier_' + id_carrier).children('a').children('i').removeClass('icon-remove').addClass('icon-check');
                                if ($.isNumeric(id_product)) {
                                    var carriers = $( '#carrier_product_' + id_product).val().split(',');
                                    if ( carriers.indexOf(id_carrier) == -1 )
                                        carriers.splice( 0, 0, id_carrier );
                                    $( '#carrier_product_' + id_product).val( carriers );
                                }
                            });
                            showSuccessMessage( SuccessMessageCarrier1 + ' ' + id_carrier + ' - ' + carrier_name + ' ' + SuccessMessageCarrier2 );
                        }
                        else if (check == false) {
                            JSON.parse(data, function (key, id_product) {
                                if ($.isNumeric(id_product)) {
                                    var carriers = $( '#carrier_product_' + id_product).val().split(',');
                                    if ( carriers.indexOf(id_carrier) != -1 )
                                        carriers.splice( carriers.indexOf(id_carrier), 1 );
                                    $( '#carrier_product_' + id_product).val( carriers );
                                    if ($( '#carrier_product_' + id_product).val() == '') {
                                        $('tbody').children('tr').children('td').children('[id^="product_carrier_'+id_product+'_"]').removeClass('action-disabled').addClass('action-enabled');
                                        $('tbody').children('tr').children('td').children('[id^="product_carrier_'+id_product+'_"]').children('i').removeClass('icon-remove').addClass('icon-check');
                                        $( '#carrier_product_' + id_product).val( $('#all_carriers').val() );
                                    } else {
                                        $('tbody').children('tr').children('td').children('a#product_carrier_'+id_product+'_'+id_carrier).removeClass('action-enabled').addClass('action-disabled');
                                        $('tbody').children('tr').children('td').children('a#product_carrier_'+id_product+'_'+id_carrier).children('i').removeClass('icon-check').addClass('icon-remove');
                                    }
                                }
                            });
                            showSuccessMessage( SuccessMessageCarrier1 + ' ' + id_carrier + ' - ' + carrier_name + ' ' + SuccessMessageCarrier3 );
                        }
                    })
                    .fail(function() {
                        showErrorMessage( ErrorMessageCarrier + ' ' + id_carrier + ' - ' + carrier_name );
                    });
            }
        });
    });

});

function confirm(name, state, callback) {
    $.fancybox("#confirm_" + state,{
        modal: true,
        beforeShow: function() {
            $(".fancybox_name").html(name);
        },
        afterShow: function() {
            $(".confirm").on("click", function(event){
                if($(event.target).is(".yes")){
                    ret = true;
                } else if ($(event.target).is(".no")){
                    ret = false;
                }
                $.fancybox.close();
            });
        },
        afterClose: function() {
            callback.call(this, ret);
        }
    });
}