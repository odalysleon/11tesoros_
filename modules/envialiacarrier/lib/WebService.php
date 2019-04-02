<?php

/**
 * Biblioteca de acceso y métodos WebService
 * @author      miguel.cejas
 * @date        08/02/2017
 */
class WebService {

  // Array de codigos de error de borrado de envios
  public static $arrErrBorEnv = array(
      1 => 'El envio no existe.',
      2 => 'El usuario no tiene permiso para borrar este envio.',
      3 => 'La fecha esta fuera del rango permitido.');

  public static function login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass) {
    $strSesion = '';
		$strDepto = trim($strDepto);
    if ($strDepto == '') {
      $xml = '<?xml version="1.0" encoding="utf-8"?>
							<soap:Envelope 
								xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
								xmlns:xsd="http://www.w3.org/2001/XMLSchema">
								<soap:Body>
									<LoginWSService___LoginCli2>
										<strCodAge>' . $strAgencia . '</strCodAge>
										<strCliente>' . $strCliente . '</strCliente>
										<strPass>' . $strPass . '</strPass>
									</LoginWSService___LoginCli2>
								</soap:Body>
							</soap:Envelope>';
    } else {
      $xml = '<?xml version="1.0" encoding="utf-8"?>
							<soap:Envelope 
								xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
								xmlns:xsd="http://www.w3.org/2001/XMLSchema">
								<soap:Body>
									<LoginWSService___LoginDep2>
										<strCodAge>' . $strAgencia . '</strCodAge>
										<strCodCli>' . $strCliente . '</strCodCli>
										<strDepartamento>' . $strDepto . '</strDepartamento>
										<strPass>' . $strPass . '</strPass>
									</LoginWSService___LoginDep2>
								</soap:Body>
							</soap:Envelope>';
    }
    $respuesta = self::peticionPost($strUrlWebService, $xml);
	
	if ($respuesta === false) {
      return 'No se ha podido establecer la conexión';
	}
	
    $manejadorXml = new SimpleXMLElement($respuesta);

    if (!$manejadorXml->xpath('//faultcode')) {
      foreach ($manejadorXml->xpath('//v1:strSesion') as $sesion) {
        $strSesion = $sesion;
      }
      foreach ($manejadorXml->xpath('//v1:strURLDetSegEnv') as $urlSeg) {
        $strUrlSeg = $urlSeg;
      }
      EnvialiaConfig::setSesionYUrlSeg($strSesion, $strUrlSeg);
      return $strSesion;
    } else {
      foreach ($manejadorXml->xpath('//faultstring') as $error) {
        $strError = $error;
      }
      return $strError;
    }
  }
  
  public static function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
  }

  public static function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
  }
  
  public static function getURLWS($url_WS) {  
	
	$url = strtolower($url_WS);
	$strUrlFormateada = "";
	
	if (self::startsWith($url, "http"))  {
		$strUrlFormateada = $url;
	} else {
		$strUrlFormateada = 'http://' . $url;
	}
	
	if (!self::endsWith($url, "/soap"))  {
		$strUrlFormateada = $url . '/SOAP';
	}
	
	return $strUrlFormateada;
  }

  public static function grabaEnvio($id_order, $paqPedido = '1' ) {
    $arrConf = EnvialiaConfig::getConfig();
		$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
		
		if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
			$arrReturn['error'] = 'No ha configurado la url de conexión.';
					return $arrReturn;
		}
	
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

    if ($strSesion <> '') {
      $query = 'SELECT o.reference, ets.V_COD_TIPO_SERV, CONCAT(a.firstname, " ", a.lastname) as V_NOM, c.email,
                  a.postcode, a.city, a.phone, a.phone_mobile, CONCAT(a.address1, " ", a.address2) as V_DIR, co.iso_code,
									o.module, o.total_paid_real
                  FROM ' . _DB_PREFIX_ . 'orders o
                  JOIN ' . _DB_PREFIX_ . 'envialia_tipo_serv ets
                  on o.id_carrier = ets.ID_CARRIER
                  JOIN ' . _DB_PREFIX_ . 'customer c 
                  on c.id_customer = o.id_customer
                  JOIN ' . _DB_PREFIX_ . 'address a
                  on a.id_address = o.id_address_delivery
                  JOIN ' . _DB_PREFIX_ . 'country co
                  on co.id_country = a.id_country
                  WHERE o.id_order = ' . $id_order;
									
      $resultado = Db::getInstance()->executeS($query);
      if ($resultado) {
        foreach ($resultado as $datosEnvio) {
          $strRef = $datosEnvio["reference"];
          $strTipoServ = $datosEnvio["V_COD_TIPO_SERV"];
          $strNomDes = $datosEnvio["V_NOM"];
          $strEmailDes = $datosEnvio["email"];
          $strCpDes = $datosEnvio["postcode"];
          $strPobDes = $datosEnvio["city"];
          $strTlfDes = $datosEnvio["phone"];
          $strMovilDes = $datosEnvio["phone_mobile"];
          $strDirDes = $datosEnvio["V_DIR"];
          $strPaisDes = $datosEnvio["iso_code"];
					$strModule = $datosEnvio["module"];
					$precio = $datosEnvio["total_paid_real"];
          if ($strTlfDes == '') {
            $strTlfDes = $strMovilDes;
          }
        }
      }

      $query = 'select SUM(cp.quantity) as productos from ' . _DB_PREFIX_ . 'cart_product cp 
                where cp.id_cart = (select o.id_cart from ' . _DB_PREFIX_ . 'orders o where o.id_order = ' . $id_order . ')';

      $resultado = Db::getInstance()->executeS($query);
      if ($resultado) {
        foreach ($resultado as $datosEnvio) {
          $ContProducto = $datosEnvio["productos"];
        }
      }

      $peso = '0';
      $medidas = '0';
			$reembolso = '0';
			if ($strModule == 'ps_cashondelivery'){
				$reembolso = $precio; 
			}

      $query = 'select ROUND(SUM(p.weight) ,2) as peso, ROUND(POWER(SUM(p.width * p.height * p.depth), 1.0 / 3.0), 2) as medidas 
                from ' . _DB_PREFIX_ . 'product p
                where p.id_product in (select cp.id_product from ' . _DB_PREFIX_ . 'cart_product cp
                inner join ' . _DB_PREFIX_ . 'orders o on o.id_cart = cp.id_cart
                where o.id_order = ' . $id_order . ')';

      $resultado = Db::getInstance()->executeS($query);
      if ($resultado) {
        foreach ($resultado as $datosEnvio) {
          $peso = $datosEnvio["peso"];
          $medidas = $datosEnvio["medidas"];
        }
      }
			
			// En caso de que el tipo de servicio sea internacional, se envía el país
			$strPaisDesAux = '';
			$query = 'select V_COD_TIPO_SERV from ' . _DB_PREFIX_ . 'envialia_tipo_serv where V_COD_TIPO_SERV = "'.$strTipoServ.'" and (T_INT = 1 or T_EUR = 1)';
			
      $resultado = Db::getInstance()->executeS($query);
      if ($resultado) {
					$strPaisDesAux = $strPaisDes;
      }			

      $confPrev = Configuration::getMultiple(array('envialia_bultos', 'envialia_bultos_fijo_num', 'envialia_bultos_var_num', 'PS_SHOP_NAME',
                  'PS_SHOP_ADDR1', 'PS_SHOP_ADDR2', 'PS_SHOP_CODE', 'PS_SHOP_CITY', 'PS_SHOP_PHONE'));

      if ($confPrev['envialia_bultos'] == '0') {
        $bultos = $confPrev['envialia_bultos_fijo_num'];
      } else if ($confPrev['envialia_bultos'] == '1') {
        $bultos = ceil($ContProducto / $confPrev['envialia_bultos_var_num']);
      } else {
        $bultos = $paqPedido;
      }

      $strNomOri = $confPrev['PS_SHOP_NAME'];
      $strDirOri = $confPrev['PS_SHOP_ADDR1'] . ' ' . $confPrev['PS_SHOP_ADDR2'];
      $strCpOri = $confPrev['PS_SHOP_CODE'];
      $strPobOri = $confPrev['PS_SHOP_CITY'];
      $strTlfOri = $confPrev['PS_SHOP_PHONE'];

      $fecha = date("Y/m/d");

      $xml = '<?xml version="1.0" encoding="utf-8"?>
              <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
                <soap:Header>
                  <ROClientIDHeader xmlns="http://tempuri.org/">
                    <ID>' . $strSesion . '</ID>
                  </ROClientIDHeader>
                </soap:Header>
                <soap:Body>
                  <WebServService___GrabaEnvio8 xmlns="http://tempuri.org/">
                    <strCodAgeCargo>' . $strAgencia . '</strCodAgeCargo>
                    <strCodAgeOri>' . $strAgencia . '</strCodAgeOri>
                    <strAlbaran></strAlbaran>
                    <dtFecha>' . $fecha . '</dtFecha>
                    <strCodAgeDes></strCodAgeDes>
                    <strCodTipoServ>' . $strTipoServ . '</strCodTipoServ>
                    <strCodCli>' . $strCliente . '</strCodCli>
                    <strCodCliDep>' . $strDepto . '</strCodCliDep>
                    <strNomOri>' . $strNomOri . '</strNomOri>
                    <strTipoViaOri></strTipoViaOri>
                    <strDirOri>' . $strDirOri . '</strDirOri>
                    <strNumOri></strNumOri>
                    <strPisoOri></strPisoOri>
                    <strPobOri>' . $strPobOri . '</strPobOri>
                    <strCPOri>' . $strCpOri . '</strCPOri>
                    <strCodProOri></strCodProOri>
                    <strTlfOri>' . $strTlfOri . '</strTlfOri>
                    <strNomDes>' . $strNomDes . '</strNomDes>
                    <strTipoViaDes></strTipoViaDes>
                    <strDirDes>' . $strDirDes . '</strDirDes>
                    <strNumDes></strNumDes>
                    <strPisoDes></strPisoDes>
                    <strPobDes>' . $strPobDes . '</strPobDes>
                    <strCPDes>' . $strCpDes . '</strCPDes>
                    <strCodProDes></strCodProDes>
                    <strTlfDes>' . $strTlfDes . '</strTlfDes>
                    <intDoc>0</intDoc>
                    <intPaq>' . $bultos . '</intPaq>
                    <dPesoOri>' . $peso . '</dPesoOri>
                    <dAltoOri>' . $medidas . '</dAltoOri>
                    <dAnchoOri>' . $medidas . '</dAnchoOri>
                    <dLargoOri>' . $medidas . '</dLargoOri>
                    <dReembolso>' . $reembolso . '</dReembolso>
                    <dValor>0</dValor>
                    <dAnticipo>0</dAnticipo>
                    <dCobCli>0</dCobCli>
                    <strObs></strObs>
                    <boSabado>0</boSabado>
                    <boRetorno>0</boRetorno>
                    <boGestOri>0</boGestOri>
                    <boGestDes>0</boGestDes>
                    <boAnulado>0</boAnulado>
                    <boAcuse>0</boAcuse>
                    <strRef>' . $strRef . '</strRef>
                    <strCodSalRuta></strCodSalRuta>
                    <dBaseImp>0</dBaseImp>
                    <dImpuesto>0</dImpuesto>
                    <boPorteDebCli>0</boPorteDebCli>
                    <strPersContacto></strPersContacto>
                    <strCodPais>' . $strPaisDesAux . '</strCodPais>
                    <strDesMoviles>' . $strMovilDes . '</strDesMoviles>
                    <strDesDirEmails>' . $strEmailDes . '</strDesDirEmails>
                    <strFranjaHoraria></strFranjaHoraria>
                    <dtHoraEnvIni></dtHoraEnvIni>
                    <dtHoraEnvFin></dtHoraEnvFin>
                    <boInsert>1</boInsert>
                    <strCampo1></strCampo1>
                    <strCampo2></strCampo2>
                    <strCampo3></strCampo3>
                    <strCampo4></strCampo4>
                    <boCampo5>0</boCampo5>
                    <boPagoDUAImp>0</boPagoDUAImp>
                    <boPagoImpDes>0</boPagoImpDes>
                  </WebServService___GrabaEnvio8>
                </soap:Body>
              </soap:Envelope>';
      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);
      $arrReturn = array();
      if (!$manejadorXml->xpath('//faultcode')) {
        foreach ($manejadorXml->xpath('//v1:strAlbaranOut') as $albaran) {
          $strAlbaran = $albaran;
        }
        foreach ($manejadorXml->xpath('//v1:dtFecHoraAltaOut') as $fecEnvio) {
          $strFechaEnvio = $fecEnvio;
        }
        foreach ($manejadorXml->xpath('//v1:strGuidOut') as $guid) {
          $strGuid = $guid;

          $strGuid = str_replace("{", "", $strGuid);
          $strGuid = str_replace("}", "", $strGuid);
        }

        $query = 'insert into ' . _DB_PREFIX_ . 'envialia_envio(id_order, V_COD_AGE_CARGO, V_COD_AGE_ORI, V_ALBARAN, V_GUID, D_FECHA) 
                 values (' . $id_order . ', "' . $strAgencia . '", "' . $strAgencia . '", "' . $strAlbaran .
                '", "' . $strGuid . '","' . $strFechaEnvio . '")';

        Db::getInstance()->execute($query);

        // Añade el seguimiento
        self::actualizaSeguimiento($id_order, $strGuid);

        // Si tiene activa la configuración, imprime su etiqueta
        if (Configuration::get("envialia_perm_imp_eti") == 1) {
					$strEtiqueta = WebService::ImprimeEnvio($strAlbaran);
					
					if ($strEtiqueta <> '0') {
						$decoded = base64_decode($strEtiqueta);
						$file = '../modules/EnvialiaCarrier/etiquetas/'.$strSesion.'.pdf';
						file_put_contents($file, $decoded);
					}
					$arrConf = EnvialiaConfig::getConfig();	
					echo "<script>
					function borraEtiquetaGenerada() {
						var http2 = new XMLHttpRequest();
						var url2 = '../modules/EnvialiaCarrier/lib/borraEtiqueta.php';
						var params2 = 'sesion=".$arrConf['V_ID_SESION']."'; 
						http2.open('POST', url2, true);
						http2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
						http2.send(params2);
						http2.onreadystatechange = function() {//Call a function when the state changes.
							if(http2.readyState == 4 && http2.status == 200) {
								return;
							}		
						}
					}
					
					var http = new XMLHttpRequest();
					var url = '../modules/EnvialiaCarrier/lib/imprimeEtiqueta.php';
					var params = 'albaran=".$strAlbaran."&urlWS=".$arrConf['V_URL_WEB']."&sesion=".$arrConf['V_ID_SESION']."';
					http.open('POST', url, true);

					//Send the proper header information along with the request
					http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

					http.onreadystatechange = function() {//Call a function when the state changes.
						if(http.readyState == 4 && http.status == 200) {
							window.open('../modules/EnvialiaCarrier/etiquetas/".$arrConf['V_ID_SESION'].".pdf');
							//Dos segundos después de abrir la etiqueta la borramos
							setTimeout(function() { borraEtiquetaGenerada(); }, 2000);
							return;
						}
					}
					http.send(params);
					</script>";
		  
		  
		  
          //$arrReturn['etiqueta'] = $etiqueta;
        }
        $arrReturn['error'] = '';
        return $arrReturn;
      } else {
        // Error al grabar el envio
        foreach ($manejadorXml->xpath('//faultstring') as $error) {
          $strError = $error;
        }
        $arrReturn['error'] = $strError;
        return $arrReturn;
      }
    } else {
		 $arrReturn['error'] = 'No hay sesion.';
	}

    return $arrReturn;
  }

  // Borra un solo envio
  public static function borraEnvio($strPedido, $strAlbaran) {
    $arrConf = EnvialiaConfig::getConfig();
	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
		return 'No ha configurado la url de conexión.';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }

    if ($strSesion <> '') {
      $xml = '<?xml version="1.0" encoding="utf-8"?>
              <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
              <soap:Header>
                <ROClientIDHeader xmlns="http://tempuri.org/">
                  <ID>' . $strSesion . '</ID>
                </ROClientIDHeader>
              </soap:Header>
              <soap:Body>
                <WebServService___BorraEnvio xmlns="http://tempuri.org/">
                  <strCodAgeCargo>' . $strAgencia . '</strCodAgeCargo>
                  <strCodAgeOri>' . $strAgencia . '</strCodAgeOri>
                  <strAlbaran>' . $strAlbaran . '</strAlbaran>
                </WebServService___BorraEnvio>
              </soap:Body>
            </soap:Envelope>';

      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = self::peticionPost($strUrlWebService, $xmlNew);
        $manejadorXml = new SimpleXMLElement($respuesta);
      }

      foreach ($manejadorXml->xpath('//v1:intCodError') as $error) {
        $strError = $error;
      }

      if ($strError == '0') {
        $query = 'delete from ' . _DB_PREFIX_ . 'envialia_envio where id_order = ' . $strPedido;
        $resultado = Db::getInstance()->execute($query);
        return '';
      } else {
        return self::$arrErrBorEnv["$strError"];
      }
    }
    return 'Error al conectar con el servicio';
  }

  // Imprime la etiqueta de un solo envio
  public static function ImprimeEnvio($strAlbaran) {
    $arrConf = EnvialiaConfig::getConfig();
	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
        return '0';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }
		
		if (Configuration::get("envialia_imp_eti_a4") == 1) {
			$boImprA4 = 1;
		} else {
			$boImprA4 = 0;
		}
					

    if ($strSesion <> '') {
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
                  <WebServService___ConsEtiquetaEnvio6>
                    <strCodAgeOri>' . $strAgencia . '</strCodAgeOri>
                    <strAlbaran>' . $strAlbaran . '</strAlbaran>
                    <strBulto></strBulto>
                    <boPaginaA4>'.$boImprA4.'</boPaginaA4>
                    <intNumEtiqImpresasA4>0</intNumEtiqImpresasA4>
                    <strFormato>PDF</strFormato>
                  </WebServService___ConsEtiquetaEnvio6>
                </soap:Body>
              </soap:Envelope>';
			  
      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = self::peticionPost($strUrlWebService, $xmlNew);
        $manejadorXml = new SimpleXMLElement($respuesta);
      }

      if ($manejadorXml->xpath('//v1:strEtiquetas')) {
        foreach ($manejadorXml->xpath('//v1:strEtiquetas') as $etiqueta) {
          $strEtiqueta = $etiqueta;
        }
        return $strEtiqueta;
      } else {
        return '0';
      }
    }
    return '0';
  }

  // Imprime etiquetas de forma masiva
  public static function ImprimeEnvioMasivo($xmlEnvios) {
    $arrConf = EnvialiaConfig::getConfig();

	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
        return '0';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }
		
		if (Configuration::get("envialia_imp_eti_a4") == 1) {
			$boImprA4 = 1;
		} else {
			$boImprA4 = 0;
		}

    if ($strSesion <> '') {
      $xmlEnvios = self::parseaXML($xmlEnvios);

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
                    <boPaginaA4>'.$boImprA4.'</boPaginaA4>
                    <intNumEtiqImpresasA4>0</intNumEtiqImpresasA4>
                  </WebServService___ConsEtiquetaEnvioMasiva>
                </soap:Body>
              </soap:Envelope>';

      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = self::peticionPost($strUrlWebService, $xmlNew);
        $manejadorXml = new SimpleXMLElement($respuesta);
      }

      if ($manejadorXml->xpath('//v1:strEtiqueta')) {
        foreach ($manejadorXml->xpath('//v1:strEtiqueta') as $etiqueta) {
          $strEtiqueta = $etiqueta;
        }

        return $strEtiqueta;
      } else {
        return '0';
      }
    }
    return '0';
  }

  // Sincroniza los estados de envios con Envialianet
  public static function SincronizaEstadosEnvios($xmlEnvios) {
    $arrConf = EnvialiaConfig::getConfig();

	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
		return 'No ha configurado la url de conexión.';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }

    if ($strSesion <> '') {
      $xmlEnvios = self::parseaXML($xmlEnvios);

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
                  <WebServService___ConsEstadoEnvioMasivo>
                    <strEnvios>' . $xmlEnvios . '</strEnvios>
                  </WebServService___ConsEstadoEnvioMasivo>
                </soap:Body>
              </soap:Envelope>';
							
      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = self::peticionPost($strUrlWebService, $xmlNew);
        $manejadorXml = new SimpleXMLElement($respuesta);
      }
      if ($manejadorXml->xpath('//v1:strEstados')) {
        foreach ($manejadorXml->xpath('//v1:strEstados') as $xmlEstados) {
          $strEstados = $xmlEstados;
        }
        // Obtiene el xml de estados de la respuesta
        $manejadorXml = new SimpleXMLElement($strEstados);
        if ($manejadorXml->xpath('//ENV_ESTADOS')) {
          // Actializa el estado a los envios
          foreach ($manejadorXml->xpath('//ENV_ESTADOS') as $xmlEstEnvio) {

            $query = 'update ' . _DB_PREFIX_ . 'orders
                      set current_state = (select ee.id_order_state 
                      from ' . _DB_PREFIX_ . 'envialia_estado ee
                      where ee.V_COD_TIPO_EST = "' . $xmlEstEnvio['V_COD_TIPO_EST'] . '")
                      where id_order = (select een.id_order 
                      from ' . _DB_PREFIX_ . 'envialia_envio een
                      where een.V_COD_AGE_CARGO = "' . $xmlEstEnvio['V_COD_AGE_CARGO'] . '"
                        and een.V_COD_AGE_ORI = "' . $xmlEstEnvio['V_COD_AGE_ORI'] . '"
                        and een.V_ALBARAN = "' . $xmlEstEnvio['V_ALBARAN'] . '")
                      and exists (select id_envialia_estado 
                                  from ' . _DB_PREFIX_ . 'envialia_estado where V_COD_TIPO_EST = "' . $xmlEstEnvio['V_COD_TIPO_EST'] . '")';
																	
            Db::getInstance()->execute($query);
						$query = 'insert into ' . _DB_PREFIX_ . 'order_history (id_order, id_order_state, date_add) 
											select o.id_order, o.current_state, NOW() from ' . _DB_PREFIX_ . 'orders o where o.id_order = (select een.id_order 
                      from ' . _DB_PREFIX_ . 'envialia_envio een
                      where een.V_COD_AGE_CARGO = "' . $xmlEstEnvio['V_COD_AGE_CARGO'] . '"
                        and een.V_COD_AGE_ORI = "' . $xmlEstEnvio['V_COD_AGE_ORI'] . '"
                        and een.V_ALBARAN = "' . $xmlEstEnvio['V_ALBARAN'] . '")
											and o.current_state <> (select oh.id_order_state from ' . _DB_PREFIX_ . 'order_history oh where oh.id_order = o.id_order order by id_order_history desc limit 1)';
						Db::getInstance()->execute($query);
						
          }
          return '';
        } else {
          return 'Envios no encontrados.';
        }
      } else {
        return 'Error al conectar con el servidor.';
      }
    }
    return 'Error al conectar con el servidor.';
  }

  // Borrado de envios masivo
  public static function borraEnvioMasivo($arrEnviosBorrar) {
    $arrConf = EnvialiaConfig::getConfig();

	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
		return 'No ha configurado la url de conexión.';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }

    if ($strSesion <> '') {
      $xmlEnvios = '<ENVIOS>';
      foreach ($arrEnviosBorrar as $envioBorrar) {
        $xmlEnvios .= '<ENVIO V_COD_AGE_CARGO = "' . $envioBorrar['V_COD_AGE_CARGO'] .
                '" V_COD_AGE_ORI = "' . $envioBorrar['V_COD_AGE_ORI'] .
                '" V_ALBARAN = "' . $envioBorrar['V_ALBARAN'] . '" />';
      }
      $xmlEnvios .= '</ENVIOS>';

      $xmlEnvios = self::parseaXML($xmlEnvios);

      $xml = '<?xml version="1.0" encoding="utf-8"?>
              <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
              <soap:Header>
                <ROClientIDHeader xmlns="http://tempuri.org/">
                  <ID>' . $strSesion . '</ID>
                </ROClientIDHeader>
              </soap:Header>
              <soap:Body>
                <WebServService___BorraEnvioMasivo>
                  <strEnvios>' . $xmlEnvios . '</strEnvios>
                </WebServService___BorraEnvioMasivo>
              </soap:Body>
            </soap:Envelope>';

      $respuesta = self::peticionPost($strUrlWebService, $xml);
      $manejadorXml = new SimpleXMLElement($respuesta);

      if ($manejadorXml->xpath('//faultcode')) {
        $strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

        $xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

        $respuesta = self::peticionPost($strUrlWebService, $xmlNew);
        $manejadorXml = new SimpleXMLElement($respuesta);
      }
      if ($manejadorXml->xpath('//v1:strResultados')) {
        foreach ($manejadorXml->xpath('//v1:strResultados') as $xmlResultados) {
          $strResultados = $xmlResultados;
        }

        // Obtiene el xml de envios de la respuesta
        $manejadorXml = new SimpleXMLElement($strResultados);
        if ($manejadorXml->xpath('//ENV_BORRADOS')) {
          $strResultados = '';

          // Borra los envios y muestra los errores encontrados
          foreach ($manejadorXml->xpath('//ENV_BORRADOS') as $xmlResBorr) {
            if ($xmlResBorr['I_COD_ERROR'] == 0) {

              $query = 'delete from ' . _DB_PREFIX_ . 'envialia_envio where V_COD_AGE_CARGO = "' . $xmlResBorr['V_COD_AGE_CARGO'] .
                      '" and V_COD_AGE_ORI = "' . $xmlResBorr['V_COD_AGE_ORI'] . '" and V_ALBARAN = "' . $xmlResBorr['V_ALBARAN'] . '"';
              Db::getInstance()->execute($query);
            } else {
              $intId = $xmlResBorr['I_COD_ERROR'];
              $strResultados .= " " . $xmlResBorr['V_COD_AGE_CARGO'] . $xmlResBorr['V_COD_AGE_ORIF'] .
                      $xmlResBorr['V_ALBARAN'] . " -> " . self::$arrErrBorEnv["$intId"] . "<br/>";
            }
          }

          return $strResultados;
        } else {
          return 'Envios no encontrados.';
        }
      } else {
        return 'Error al conectar con el servidor.';
      }
    }
    return 'Error al conectar con el servicio';
  }

  // Genera envios masivo
  public static function GrabaEnvioMasivo($arrPedidos, $paqPedido = '1', $boImpr = false) {
    $arrConf = EnvialiaConfig::getConfig();
		
	$strUrlWebService = self::getURLWS($arrConf['V_URL_WEB']);
	if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
		return 'No ha configurado la url de conexión.';
	}
    $strAgencia = $arrConf['V_COD_AGE'];
    $strCliente = $arrConf['V_COD_CLI'];
    $strDepto = $arrConf['V_COD_CLI_DEP'];
    $strPass = $arrConf['BL_PASS'];
    $strSesion = $arrConf['V_ID_SESION'];

    if ($strSesion == '') {
      $strSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);
      $pos = strpos($strSesion, '{');
      if ($pos === false) {
        return $strSesion;
      }
    }
	
    if ($strSesion <> '') {
      // Inicializamos las variables de impresion masiva
      $boImprimeMasivo = (Configuration::get("envialia_perm_imp_eti") == 1);
      $boTieneAlbaranes = false;
      Configuration::updateValue('envialia_etiqueta', '', true);

      $xmlEnvios = '<ENVIOS>';
      foreach ($arrPedidos as $idPedido) {
				if ($boImpr === true) {
					$strIdPedido = $idPedido;
				} else {
					$strIdPedido = $idPedido["ID_ENVIO"];
				}
				
        $query = 'SELECT o.reference, ets.V_COD_TIPO_SERV, CONCAT(a.firstname, " ", a.lastname) as V_NOM, c.email,
                  a.postcode, a.city, a.phone, a.phone_mobile, CONCAT(a.address1, " ", a.address2) as V_DIR, co.iso_code,
									o.module, o.total_paid_real
                  FROM ' . _DB_PREFIX_ . 'orders o
                  JOIN ' . _DB_PREFIX_ . 'envialia_tipo_serv ets
                  on o.id_carrier = ets.ID_CARRIER
                  JOIN ' . _DB_PREFIX_ . 'customer c 
                  on c.id_customer = o.id_customer
                  JOIN ' . _DB_PREFIX_ . 'address a
                  on a.id_address = o.id_address_delivery
                  JOIN ' . _DB_PREFIX_ . 'country co
                  on co.id_country = a.id_country
                  WHERE o.id_order = ' . $strIdPedido;

        $resultado = Db::getInstance()->executeS($query);
        if ($resultado) {
          foreach ($resultado as $datosEnvio) {
            $strRef = $datosEnvio["reference"];
            $strTipoServ = $datosEnvio["V_COD_TIPO_SERV"];
            $strNomDes = $datosEnvio["V_NOM"];
            $strEmailDes = $datosEnvio["email"];
            $strCpDes = $datosEnvio["postcode"];
            $strPobDes = $datosEnvio["city"];
            $strTlfDes = $datosEnvio["phone"];
            $strTlfDes = $datosEnvio["phone"];
            $strDirDes = $datosEnvio["V_DIR"];
            $strPaisDes = $datosEnvio["iso_code"];
            $strMovilDes = $datosEnvio["phone_mobile"];
						$strModule = $datosEnvio["module"];
						$precio = $datosEnvio["total_paid_real"];
						$precio = (string)$precio;
						$precio = str_replace('.', ',', $precio);

            if ($strTlfDes == '') {
              $strTlfDes = $strMovilDes;
            }
          }
        }

        $query = 'select SUM(cp.quantity) as productos from ' . _DB_PREFIX_ . 'cart_product cp 
                where cp.id_cart = (select o.id_cart from ' . _DB_PREFIX_ . 'orders o where o.id_order = ' . $strIdPedido . ')';

        $resultado = Db::getInstance()->executeS($query);
        if ($resultado) {
          foreach ($resultado as $datosEnvio) {
            $ContProducto = $datosEnvio["productos"];
          }
        }
				
				// En caso de que el tipo de servicio sea internacional, se envía el país
				$strPaisDesAux = '';
				$query = 'select V_COD_TIPO_SERV from ' . _DB_PREFIX_ . 'envialia_tipo_serv where V_COD_TIPO_SERV = "'.$strTipoServ.'" and (T_INT = 1 or T_EUR = 1)';
				
				$resultado = Db::getInstance()->executeS($query);
				if ($resultado) {
						$strPaisDesAux = $strPaisDes;
				}			

				$reembolso = '0';
			
				if ($strModule == 'ps_cashondelivery'){
					$reembolso = $precio; 
				}
        $peso = '0';
        $medidas = '0';

        $query = 'select ROUND(SUM(p.weight), 2) as peso, ROUND(POWER(SUM(p.width * p.height * p.depth), 1.0 / 3.0), 2) as medidas 
                from ' . _DB_PREFIX_ . 'product p
                where p.id_product in (select cp.id_product from ' . _DB_PREFIX_ . 'cart_product cp
                inner join ' . _DB_PREFIX_ . 'orders o on o.id_cart = cp.id_cart
                where o.id_order = ' . $strIdPedido . ')';

        $resultado = Db::getInstance()->executeS($query);
        if ($resultado) {
          foreach ($resultado as $datosEnvio) {            
						$peso = $datosEnvio["peso"];
						$peso = (string)$peso;
						$peso = str_replace('.', ',', $peso);
						$medidas = $datosEnvio["medidas"];
						$medidas = (string)$medidas;
						$medidas = str_replace('.', ',', $medidas);
          }
        }

        $confPrev = Configuration::getMultiple(array('envialia_bultos', 'envialia_bultos_fijo_num', 'envialia_bultos_var_num', 'PS_SHOP_NAME',
                    'PS_SHOP_ADDR1', 'PS_SHOP_ADDR2', 'PS_SHOP_CODE', 'PS_SHOP_CITY', 'PS_SHOP_PHONE'));

        if ($confPrev['envialia_bultos'] == '0') {
          $bultos = $confPrev['envialia_bultos_fijo_num'];
        } else if ($confPrev['envialia_bultos'] == '1') {
          $bultos = ceil($ContProducto / $confPrev['envialia_bultos_var_num']);
        } else {
          $bultos = $paqPedido;
        }

        $strNomOri = $confPrev['PS_SHOP_NAME'];
        $strDirOri = $confPrev['PS_SHOP_ADDR1'] . ' ' . $confPrev['PS_SHOP_ADDR2'];
        $strCpOri = $confPrev['PS_SHOP_CODE'];
        $strPobOri = $confPrev['PS_SHOP_CITY'];
        $strTlfOri = $confPrev['PS_SHOP_PHONE'];

        $fecha = date("d/m/Y");

        $xmlEnvios .= '<ENVIO 
          V_COD_AGE_CARGO = "' . $strAgencia . '" 
          V_COD_AGE_ORI = "' . $strAgencia . '" 
          V_ALBARAN = "" 
          D_FECHA = "' . $fecha . '" 
          V_COD_AGE_DES = "" 
          V_COD_TIPO_SERV = "' . $strTipoServ . '" 
          V_COD_CLI = "' . $strCliente . '" 
          V_COD_CLI_DEP = "' . $strDepto . '" 
          V_NOM_ORI = "' . $strNomOri . '" 
          V_TIPO_VIA_ORI = "" 
          V_DIR_ORI = "' . $strDirOri . '" 
          V_NUM_ORI = "" 
          V_PISO_ORI = "" 
          V_POB_ORI = "' . $strPobOri . '" 
          V_CP_ORI = "' . $strCpOri . '" 
          V_COD_PRO_ORI = "" 
          V_TLF_ORI = "' . $strTlfOri . '" 
          V_NOM_DES = "' . $strNomDes . '" 
          V_TIPO_VIA_DES = "" 
          V_DIR_DES = "' . $strDirDes . '" 
          V_NUM_DES = "" 
          V_PISO_DES = "" 
          V_POB_DES = "' . $strPobDes . '" 
          V_CP_DES = "' . $strCpDes . '" 
          V_COD_PRO_DES = "" 
          V_TLF_DES = "' . $strTlfDes . '" 
          I_DOC = "0" 
          I_PAQ = "' . $bultos . '" 
          F_PESO_ORI = "' . $peso . '" 
          F_ALTO_ORI = "' . $medidas . '" 
          F_ANCHO_ORI = "' . $medidas . '" 
          F_LARGO_ORI = "' . $medidas . '" 
          F_REEMBOLSO = "' . $reembolso . '" 
          F_VALOR = "0" 
          F_ANTICIPO = "0" 
          F_COB_CLI = "0" 
          V_OBS = "" 
          B_SABADO = "0" 
          B_RETORNO = "0" 
          B_GEST_ORI = "0" 
          B_GEST_DES = "0" 
          B_ANULADO = "0" 
          B_ACUSE = "0" 
          V_REF = "' . $strRef . '" 
          V_COD_SAL_RUTA = "" 
          F_BASE_IMP = "0" 
          F_IMPUESTO = "0" 
          B_PORTE_DEB_CLI = "0" 
          V_PERS_CONTACTO = "" 
          V_COD_PAIS = "' . $strPaisDesAux . '" 
          V_DES_MOVILES = "' . $strMovilDes . '" 
          V_DES_DIR_EMAILS = "' . $strEmailDes . '" 
          V_FRANJA_HORARIA = "" 
          SD_HORA_ENV_INI = "00:00" 
          SD_HORA_ENV_FIN = "00:00" 
          B_INSERT = "1" 
          V_CAMPO_1 = "" 
          V_CAMPO_2 = "" 
          V_CAMPO_3 = "" 
          V_CAMPO_4 = "" 
          B_CAMPO_5 = "0" 
          B_PAGO_DUA_IMP = "0" 
          B_PAGO_IMP_DES = "0"
          V_ID = "' . $strIdPedido . '" />';
      }

      $xmlEnvios .= '</ENVIOS>';	  
			
      $xmlEnvios = utf8_decode($xmlEnvios);
      $xmlEnvios = self::parseaXML($xmlEnvios);
			

      $xml = '<?xml version="1.0" encoding="utf-8"?>
              <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
              <soap:Header>
                <ROClientIDHeader xmlns="http://tempuri.org/">
                  <ID>' . $strSesion . '</ID>
                </ROClientIDHeader>
              </soap:Header>
              <soap:Body>
                <WebServService___GrabaEnvioMasivo>
                  <strEnvios>' . $xmlEnvios . '</strEnvios>
                </WebServService___GrabaEnvioMasivo>
              </soap:Body>
            </soap:Envelope>';

      $respuesta = self::peticionPost($strUrlWebService, $xml);
	  
		if ($respuesta === false) {
		  return false;
		} else {	  
		  $manejadorXml = new SimpleXMLElement($respuesta);
		  if ($manejadorXml->xpath('//faultcode')) {
			$strNewSesion = self::login($strUrlWebService, $strAgencia, $strCliente, $strDepto, $strPass);

			$xmlNew = str_replace('<ID>' . $strSesion . '</ID>', '<ID>' . $strNewSesion . '</ID>', $xml);

			$respuesta = self::peticionPost($strUrlWebService, $xmlNew);
			$manejadorXml = new SimpleXMLElement($respuesta);
		  }

		  if ($manejadorXml->xpath('//v1:strResultados')) {
			foreach ($manejadorXml->xpath('//v1:strResultados') as $xmlResultados) {
			  $strResultados = $xmlResultados;
			}
			// Obtiene el xml de envios de la respuesta
			$manejadorXml = new SimpleXMLElement($strResultados);

			$arrReturn = array();
			if ($manejadorXml->xpath('//ENV_GRABADOS')) {
			  $strResultados = '';
			  // Comenzamos a generar el XML para la impresión masiva
			  $strXmlEnvio = '<Envios>';
			  // Graba los envios y muestra los errores encontrados
			  foreach ($manejadorXml->xpath('//ENV_GRABADOS') as $xmlResGra) {

				if ($xmlResGra['V_ERROR'] == '') {

				  $strGuid = $xmlResGra['V_GUID'];
				  $strGuid = str_replace("{", "", $strGuid);
				  $strGuid = str_replace("}", "", $strGuid);
					
					$strFecha = $xmlResGra['D_FEC_HORA_ALTA'];
					$strDia = substr($strFecha, 0, 2);
					$strMes = substr($strFecha, 3, 2);
					$strAnno = substr($strFecha, 6, 4);
					
				  $query = 'insert into ' . _DB_PREFIX_ . 'envialia_envio(id_order, V_COD_AGE_CARGO, V_COD_AGE_ORI, V_ALBARAN, V_GUID, D_FECHA) 
						   values (' . $xmlResGra['V_ID'] . ', "' . $strAgencia . '", "' . $xmlResGra['V_COD_AGE_ORI'] . '", "' . $xmlResGra['V_ALBARAN'] .
						  '", "' . $strGuid . '","' . $strAnno . '-' . $strMes . '-' . $strDia. '")';

				  if ($boImprimeMasivo) {
					$strXmlEnvio .= '<Envio strCodAgeOri = "' . $strAgencia . '" strAlbaran = "' . $xmlResGra['V_ALBARAN'] . '" />';
					$boTieneAlbaranes = true;
				  }

				  Db::getInstance()->execute($query);
				} else {
				  $strResultados .= " " . $xmlResGra['V_ID'] . " -> " . $xmlResGra['V_ERROR'] . "<br/>";
				}
			  }
			  
			  $arrReturn['envios'] = '';
			  if (($boImprimeMasivo) && ($boTieneAlbaranes)) {
				$strXmlEnvio .= '</Envios>';
				
				$arrReturn['envios'] = $strXmlEnvio;
				
				/*$etiqueta = self::ImprimeEnvioMasivo($strXmlEnvio);
				$arrReturn['etiqueta'] = $etiqueta;*/
			  }

			  $arrReturn['resultados'] = $strResultados;

			  return $arrReturn;
			} else {
			  return 'Envios no encontrados.';
			}
		  } else {
			return 'Error al conectar con el servidor.';
			}
		}
    }
    return 'Error al conectar con el servicio';
  }

  public static function peticionPost($strUrl, $xmlPeticion) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $strUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlPeticion);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
    $respuesta = curl_exec($ch);
	curl_close($ch);
    return $respuesta;
  }

  public static function parseaXML($xml) {
    $xml = str_replace('&', '&amp;', $xml);
    $xml = str_replace('<', '&lt;', $xml);
    $xml = str_replace('>', '&gt;', $xml);
    $xml = str_replace('\'', '&apos;', $xml);
    $xml = str_replace('"', '&quot;', $xml);
    $xml = str_replace('Á', 'A', $xml);
    $xml = str_replace('É', 'E', $xml);
    $xml = str_replace('Í', 'I', $xml);
    $xml = str_replace('Ó', 'O', $xml);
    $xml = str_replace('Ú', 'U', $xml);
    $xml = str_replace('á', 'a', $xml);
    $xml = str_replace('é', 'e', $xml);
    $xml = str_replace('í', 'i', $xml);
    $xml = str_replace('ó', 'o', $xml);
    $xml = str_replace('ú', 'u', $xml);
    $xml = str_replace('º', 'o', $xml);

    return $xml;
  }

  public static function actualizaSeguimiento($idOrder) {

    $query = 'select REPLACE(REPLACE(ec.V_URL_SEG, "{GUID}", ee.V_GUID), "{FECHA}", DATE_FORMAT(ee.D_FECHA, "%d/%m/%Y")) AS url
              from ' . _DB_PREFIX_ . 'envialia_envio ee
              cross join ' . _DB_PREFIX_ . 'envialia_config ec
              where ee.id_order =' . $idOrder;

    $resultado = Db::getInstance()->executeS($query);

    if ($resultado) {
      foreach ($resultado as $enlace) {
        $strEnlace = $enlace['url'];
      }
    }

    $query = 'update ' . _DB_PREFIX_ . 'order_carrier 
              set tracking_number = "' . $strEnlace . '" 
              where id_order = ' . $idOrder;

    Db::getInstance()->execute($query);
  }

}
