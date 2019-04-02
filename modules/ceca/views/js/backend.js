/**
 * 2011-2017 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2016 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

$(document).ready(function () {
	$('#Refund').click(function() {
		notifId = $(this).data('tpv-notification-id');
		$('#refundNotificationId').val(notifId);
		$('#amount').val(0);		
		if(confirm(confirmFullRefund)) {			
			makeRefund();
		} else return false;
	});
	$('#PartialRefund').click(function() {
		notifId = $(this).data('tpv-notification-id');
		$('#refundNotificationId').val(notifId);
		$('#amount').val($('#amountToRefund').val());
		if(!validationRefund()) return false;
		if(confirm(confirmPartialRefund)) {				
			makeRefund();
		} else return false;
	});
});
		
$(document).ready(function () {
	$('#viewNotificationMoreInfo').click(function() {
		$('#notificationMoreInfo').toggle();
	});
});


function validationRefund(){
	//Validaciones	
	var amountToRefund = $('#amountToRefund').val();	
	var amountPaid = document.getElementById("amountPaid").value;	
	var amountRefunded = document.getElementById("amountRefunded").value;
		
	var resRefund = amountToRefund.replace(',', '.');
	if (amountToRefund == '' || amountToRefund == 0 || isNaN(resRefund)) {
        alert(amountError);
        return false;
    }
		
	if (resRefund < 0) {
		 alert(amountErrorNegative);
        return false;
    }
		
	if((parseFloat(resRefund) + parseFloat(amountRefunded)) > amountPaid){		
		alert(amountErrorExcessive);
		return false;
	}	
	return true;
}

/***
 * Realiza la devolución regenerando la firma.
 */
function makeRefund() {	
	
	// Obtenemos valores formulario
	var amountToRefund = document.getElementById("amount").value;	
	var amountPaid = document.getElementById("amountPaid").value;	
	var amountRefunded = document.getElementById("amountRefunded").value;
	var notification_id = document.getElementById("refundNotificationId").value;        
    var urlGenerateSignature = document.getElementById("urlGenerateSignature").value;

    // Devolución Total    
	if(amountToRefund == 0){				
		amountToRefund = amountPaid - amountRefunded;		
	} 
	// Formateamos la cantidad a devolver		
	if(isNaN(amountToRefund)){				
		amountToRefund = amountToRefund.replace(',', '.');		
	}		    
	var amountToRefundFormat = Math.floor((amountToRefund * 100).toFixed(2)) ;
    
    var datos = "AMOUNT=" + amountToRefundFormat;    
    datos += "&NOTIFICATION_ID=" + notification_id;
 
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("POST", urlGenerateSignature, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(datos);

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
            var respuesta = xmlhttp.responseText;        
            var jsonRespuesta = JSON.parse(respuesta);
            alert(jsonRespuesta['message']);
            if(jsonRespuesta['result'] == true) location.reload();
            
            //jsonRespuesta = jsonRespuesta['MerchParams'];
            //doRequest(jsonRespuesta);
            /*document.getElementById("MerchantID").value = jsonRespuesta['MerchantID'];
            document.getElementById("AcquirerBIN").value = jsonRespuesta['AcquirerBIN'];
            document.getElementById("TerminalID").value = jsonRespuesta['TerminalID'];
            document.getElementById("TIPO_ANU").value = jsonRespuesta['TIPO_ANU'];
            document.getElementById("URL_OK").value = jsonRespuesta['URL_OK'];
            document.getElementById("URL_NOK").value = jsonRespuesta['URL_NOK'];
            document.getElementById("Firma").value = jsonRespuesta['Firma'];
            document.getElementById("Num_operacion").value = jsonRespuesta['Num_operacion'];
            document.getElementById("Importe").value = jsonRespuesta['Importe'];
            document.getElementById("Referencia").value = jsonRespuesta['Referencia'];
            document.getElementById("TipoMoneda").value = jsonRespuesta['TipoMoneda'];               
            document.forms["refundForm"].submit();*/          
        }
    }
}

/*function doRequest(datos){
	var url_ceca = document.getElementById('refundForm').action;
	var x;
	var datos_string = '';
	for(x in datos) { datos_string += x + '='+datos[x]+'&'; }
	datos_string.substr(0, datos_string.length - 1);

	var xmlhttp = new XMLHttpRequest({mozSystem: true});
    xmlhttp.open("POST", url_ceca, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(datos_string);

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4) {
        	var response = xmlhttp.responseText;
        	var msg = response.substr(response.indexOf('<TRANSACCION'), response.length);
        	alert(msg);
        }
    }

}*/