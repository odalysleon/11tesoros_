<?php
	require_once('WebService.php');
	
	$xmlEnvios = $_POST['envios'];		
	$strAgencia = $_POST['agencia'];
	$strUrlWS = $_POST['urlWS'];
	$strCliente = $_POST['cliente'];
	$strDepto = $_POST['depto'];
	$strPass = $_POST['pass'];
	$strSesion = $_POST['sesion'];
	$boPaginaA4 = $_POST['ImprA4'];
	$strUrlWebService = WebService::getURLWS($strUrlWS);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
        return '0';
	}

    if ($strSesion == '') {
      $strSesion = WebService::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }

    if ($strSesion <> '') {
      $xmlEnvios = WebService::parseaXML($xmlEnvios);

      $xml = '<?xml version="1.0" encoding="utf-8"?>
              <soap:Envelope 
                    xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                    xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                <soap:Header>
                  <ROClientIDHeader xmlns="http://tempuri.org/">
                    <ID>' . $strSesion . '</ID>
                  </ROClientIDHeader>
                </soap:Header>
                <soap:Body>
                  <WebServService___ConsEtiquetaEnvioMasiva>
                    <strEnvios>' . $xmlEnvios . '</strEnvios>
                    <boPaginaA4>'.$boPaginaA4.'</boPaginaA4>
                    <intNumEtiqImpresasA4>0</intNumEtiqImpresasA4>
                  </WebServService___ConsEtiquetaEnvioMasiva>
                </soap:Body>
              </soap:Envelope>';

      $respuesta = WebService::peticionPost($strUrlWebService, $xml);
	
	  if ($respuesta === false) {
		return 'No se ha podido establecer la conexión';
	  }
	
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = WebService::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = WebService::peticionPost($strUrlWebService, $xmlNew);
		if ($respuesta === false) {
			$arrReturn['error'] = 'No se ha podido establecer la conexión';
			return $arrReturn;
		}
        $manejadorXml = new SimpleXMLElement($respuesta);
      }

      if ($manejadorXml->xpath('//v1:strEtiqueta')) {
        foreach ($manejadorXml->xpath('//v1:strEtiqueta') as $etiqueta) {
          $strEtiqueta = $etiqueta;
        }
		
		$decoded = base64_decode($strEtiqueta);
		$file = '../etiquetas/'.$strSesion.'.pdf';
		file_put_contents($file, $decoded);

		if (file_exists($file)) {
		  header('Content-Description: File Transfer');
		  header('Content-Type: application/pdf');
		  header('Content-Disposition: inline; filename="' . basename($file) . '"');
		  header('Expires: 0');
		  header('Cache-Control: must-revalidate');
		  header('Pragma: public');
		  @readfile($file);
		}
      }
    }
	
?>
