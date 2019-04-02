<?php
/**
 * Controlador AdmEnvialiaPedidosController
 * Referente a pestaña ENVIALIA pedidos
 * @author      miguel.cejas
 * @date        07/02/2017
 */
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/EnvialiaEnvio.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/WebService.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/dinalib.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/Etiqueta.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/ModuleConst.php');

class AdminEnvialiaEnvioController extends ModuleAdminController {

  private static $boTokenLibre = True;

  public function __construct() {	  
    $this->bootstrap = true;
    $this->table = 'orders';
    $this->className = 'EnvialiaEnvio';
    $this->module = 'envialia_envio';
    $this->identifier = 'id_order';
    $this->allow_export = true;
	
    $id_lenguage = 1;

    if ($id_lenguage == '') {
      $id_lenguage = 1;
    }

    $this->_select = 'a.id_order as id_pedido, a.reference as referencia, a.date_add as fec, CONCAT(ee.V_COD_AGE_CARGO, ee.V_COD_AGE_ORI, ee.V_ALBARAN) as codigo,
                      osl.name as estado, case when ee.V_COD_AGE_CARGO is not null then ees.V_COD_TIPO_EST else "" end as estado_env, ee.D_FECHA,
                      ets.V_DES, a.id_order as id_seguimiento';
    $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'envialia_envio ee ON ee.id_order = a.id_order 
										JOIN ' . _DB_PREFIX_ . 'envialia_tipo_serv ets ON ets.ID_CARRIER = a.id_carrier
                    LEFT JOIN ' . _DB_PREFIX_ . 'order_state_lang osl ON osl.id_order_state = a.current_state and osl.id_lang = ' . $id_lenguage . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'envialia_estado ees ON ees.id_order_state = osl.id_order_state
                    CROSS JOIN ' . _DB_PREFIX_ . 'envialia_config ec';
    $this->_defaultOrderBy = 'a.id_order';

    parent::__construct();

    $this->fields_list = array(
        'id_order' => array(
            'title' => $this->l('ID'),
            'align' => 'left',
            'class' => 'fixed-width-xs'),
        'codigo' => array(
            'title' => $this->l('Envío'),
            'align' => 'left',
            'hint' => $this->l('Codigo de envío Envialia'),
            'filter_key' => 'ee!V_ALBARAN',
            'order_key' => 'V_ALBARAN'),
        'reference' => array(
            'title' => $this->l('Referencia'),
            'align' => 'left',
            'class' => 'fixed-width-xs'),
        'estado' => array(
            'title' => $this->l('Estado'),
            'align' => 'left',
            'filter_key' => 'osl!name',
            'order_key' => 'estado'),
        'estado_env' => array(
            'title' => $this->l('Estado Env.'),
            'align' => 'left',
            'filter_key' => 'ees!V_COD_TIPO_EST',
            'order_key' => 'V_COD_TIPO_EST',
            'class' => 'fixed-width-xs'),
        'V_DES' => array(
            'title' => $this->l('Transportista'),
            'align' => 'left'),
        'date_add' => array(
            'title' => $this->l('Fecha pedido'),
            'align' => 'left',
            'type' => 'datetime',),
        'D_FECHA' => array(
            'title' => $this->l('Fecha envío'),
            'align' => 'left',
            'hint' => $this->l('Fecha en la que se generó el envío Envialia'),
            'type' => 'date',),
        'id_seguimiento' => array(
            'title' => $this->trans('Seguimiento', array(), 'Admin.Global'),
            'align' => 'text-center',
            'callback' => 'printSeguiIcons',
            'orderby' => false,
            'search' => false,
            'class' => 'fixed-width-xs',
            'remove_onclick' => true
        )
    );

    // Add bulk actions - acciones agrupadas
    $this->bulk_actions = array(
        'sincEstados' => array(
            'text' => $this->l('Sincronizar estados'),
            'icon' => 'icon-refresh',
        ),
        'imprimeEtiqueta' => array(
            'text' => $this->l('Imprimir etiquetas'),
            'icon' => 'icon-file-text',
        ),
        'generaEnvio' => array(
            'text' => $this->l('Generar envíos'),
            'icon' => 'icon-dropbox text-success',
        ),
        'borraEnvio' => array(
            'text' => $this->l('Borrar envios'),
            'icon' => 'icon-trash text-danger',
            'confirm' => $this->l('¿Desea borrar los envios seleccionados?'),
        ),
    );
  }

  public function renderList() {
    $this->addRowAction('Etiqueta');
    $this->addRowAction('Generar');
    $this->addRowAction('Borrar');
    $return = parent::renderList();
	
	return $return;
  }

  public function printSeguiIcons($id_order) {
    $order = new Order($id_order);

    if (!Validate::isLoadedObject($order)) {
      return '';
    }

    $query = 'SELECT REPLACE(REPLACE(ec.V_URL_SEG, "{GUID}", ee.V_GUID), "{FECHA}", DATE_FORMAT(ee.D_FECHA, "%d/%m/%Y")) AS url
              FROM ' . _DB_PREFIX_ . 'envialia_envio ee
              CROSS JOIN ' . _DB_PREFIX_ . 'envialia_config ec
              WHERE ee.id_order =' . $id_order;

    $resultado = Db::getInstance()->executeS($query);

    if ($resultado) {
      foreach ($resultado as $enlace) {
        $strEnlace = $enlace['url'];
      }
      $tpl = $this->context->smarty->createTemplate(dirname(__FILE__) . '/../../views/templates/admin/seguimiento.tpl');
      $tpl->assign(array(
          'url' => $strEnlace
      ));
      return $tpl->fetch();
    } else {
      return '';
    }
  }

  public function postProcess() {
    // Accede a los detalles del pedido
    if (('borraEnv' <> Tools::getValue('action')) && ('updateEstado' <> Tools::getValue('action')) &&
            ('generaEnv' <> Tools::getValue('action')) && ('impEtiq' <> Tools::getValue('action'))) {
      if (Tools::getValue('id_order') <> '') {
        if ($this->access('edit')) {
          $idO = Tools::getValue('id_order');
          Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders') . '&id_order=' . $idO . '&vieworder&token=' . Tools::getAdminTokenLite('AdminOrders'));
        }
      }
    }

    // Actualiza los estados de los envios generados junto con el estado prestashop
    if ('updateEstado' === Tools::getValue('action')) {
      if (self::$boTokenLibre) {
        self::$boTokenLibre = FALSE;
        $query = 'select V_COD_AGE_CARGO, V_COD_AGE_ORI, V_ALBARAN from '
                . _DB_PREFIX_ . 'envialia_envio '
                . 'where D_FECHA > DATE_FORMAT(date_sub(NOW(), INTERVAL ' . CONST_DIAS_SINC . ' DAY),"%Y-%m-%d 00:00:00")';

        $resultado = Db::getInstance()->executeS($query);

        if ($resultado) {
          $strXML = '<ENVIOS>';
          foreach ($resultado as $envioSinc) {
            $strXML .= '<ENVIO V_COD_AGE_CARGO = "' . $envioSinc['V_COD_AGE_CARGO'] .
                    '" V_COD_AGE_ORI = "' . $envioSinc['V_COD_AGE_ORI'] .
                    '" V_ALBARAN = "' . $envioSinc['V_ALBARAN'] . '" />';
          }
          $strXML .= '</ENVIOS>';
          $strError = WebService::SincronizaEstadosEnvios($strXML);
          if ($strError <> '') {
            $this->errors[] = $this->l($strError);
            self::$boTokenLibre = TRUE;
          } else {
            $this->confirmations[] = "Estados sincronizados correctamente.";
            self::$boTokenLibre = TRUE;
          }
        } else {
          $this->warnings[] = "No existen envios a sincronizar.";
          self::$boTokenLibre = TRUE;
        }
      }
    }

    // Genera el envio
    if ('generaEnv' === Tools::getValue('action')) {
      if (self::$boTokenLibre) {
        self::$boTokenLibre = FALSE;
		
		$query = 'select o.id_order
			  from ' . _DB_PREFIX_ . 'orders o
			  where o.id_order not in (select ee.id_order from ' . _DB_PREFIX_ . 'envialia_envio ee)
			  and o.id_order = ' . Tools::getValue($this->identifier);

		$resultado = Db::getInstance()->executeS($query);

		if ($resultado) {
			foreach ($resultado as $envioGrabar) {
				$id = $envioGrabar['id_order'];
			}
			if ((Configuration::get("envialia_bultos_fijo_num") == 0) && (Configuration::get("envialia_bultos_var_num") == 0)) {
	
				$arrConf = EnvialiaConfig::getConfig();
				$strUrlWebService = WebService::getURLWS($arrConf['V_URL_WEB']);
				if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
					return 'No ha configurado la url de conexión.';
				}
				
				$strAgeAux = trim($arrConf['V_COD_AGE']);
				$strCliAux = trim($arrConf['V_COD_CLI']);
				$strCliDepAux = trim($arrConf['V_COD_CLI_DEP']);
				
				$strSesion = WebService::login($strUrlWebService, $strAgeAux, $strCliAux, $strCliDepAux, $arrConf['BL_PASS']);
				
				$pos = strpos($strSesion, '{');
				if ($pos === false) {
					$this->errors[] = $this->l($strSesion);					
				}
				else{
					echo "<script>
						var imprime_etiqueta = '".Configuration::get("envialia_perm_imp_eti")."';
						var bultos = prompt('Indica el número de bultos', '1');
						if ((bultos != null) && (bultos > 0)) {
							var http = new XMLHttpRequest();
							var url = '../modules/EnvialiaCarrier/lib/generaEnvioBultos.php';
							var params = 'bultos=' + bultos + '&id=".$id."';
							http.open('POST', url, true);
							http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
							http.send(params);
							http.onreadystatechange = function() {//Call a function when the state changes.
								if(http.readyState == 4 && http.status == 200) {
									alert('Envío generado correctamente');
									
									if (imprime_etiqueta) {
										window.open('index.php?controller=AdminEnvialiaEnvio&action=impEtiq&id_order=".$id."&token=".$this->token."');
										window.close;
									} else {
										window.close;
									}
									location.href='index.php?controller=AdminEnvialiaEnvio&token=".$this->token."';
									return;
								}
								
								if(http.readyState == 4 && http.status == 900) {
									alert(http.statusText);
									window.close();
								}							
							}
						} else {
							alert('No ha indicado el número de bultos');
						}
					</script>";
				}
			} else {
			  $arrReturn = WebService::grabaEnvio($id);
			  $strError = $arrReturn['error'];
			  if ($strError <> '') {
				$this->errors[] = $this->l($strError);
				self::$boTokenLibre = TRUE;
			  } else {
				$this->confirmations[] = "Envío generado correctamente.";
				self::$boTokenLibre = TRUE;
			  }
			}
		  } else {
			  $this->warnings[] = "El envio ya se ha generado.";
			  self::$boTokenLibre = TRUE;
			}
	  }
    }

    // Borra el envio
    if ('borraEnv' === Tools::getValue('action')) {
      if (self::$boTokenLibre) {
        $query = 'select id_order, V_ALBARAN from '
                . _DB_PREFIX_ . 'envialia_envio where id_order =' . Tools::getValue($this->identifier);

        $resultado = Db::getInstance()->executeS($query);

        if ($resultado) {
          foreach ($resultado as $envioBorrar) {
            $strError = WebService::borraEnvio($envioBorrar['id_order'], $envioBorrar['V_ALBARAN']);

            if ($strError <> '') {
              $this->errors[] = $this->l($strError);
              self::$boTokenLibre = TRUE;
            } else {
              $this->confirmations[] = "Envío borrado correctamente.";
              self::$boTokenLibre = TRUE;
            }
          }
        } else {
          $this->warnings[] = "El envío aun no se ha generado.";
          self::$boTokenLibre = TRUE;
        }
      }
    }

    // Imprime la etiqueta
    if ('impEtiq' === Tools::getValue('action')) {

      $query = 'select V_ALBARAN from '
              . _DB_PREFIX_ . 'envialia_envio where id_order =' . Tools::getValue($this->identifier);

      $resultado = Db::getInstance()->executeS($query);

      if ($resultado) {
        foreach ($resultado as $envioImpr) {
          $strResultado = WebService::ImprimeEnvio($envioImpr['V_ALBARAN']);

          if ($strResultado <> '0') {
            $decoded = base64_decode($strResultado);
            $file = 'etiqueta.pdf';
            file_put_contents($file, $decoded);

            $mi_pdf = fopen("etiqueta.pdf", "r");
            header('Content-type: application/pdf');
            fpassthru($mi_pdf); // Esto hace la magia
            fclose($mi_pdf);
          } else {
            $this->errors[] = $this->l('Error al genera la etiqueta');
          }
        }
      } else {
        $this->warnings[] = "El envío aun no se ha generado.";
      }
    }
    return parent::postProcess();
  }

  public function initPageHeaderToolbar() {
    parent::initPageHeaderToolbar();

    if (empty($this->display)) {
      $intDias = CONST_DIAS_SINC;
      $this->page_header_toolbar_btn['upd_estado'] = array(
          'href' => self::$currentIndex . '&action=updateEstado&token=' . $this->token,
          'desc' => $this->trans('Actualizar estados (' . $intDias . ' dias)', array(), 'Admin.Design.Feature'),
          'icon' => 'process-icon-refresh'
      );

      if ($this->context->mode) {
        unset($this->toolbar_btn['new']);
      }
    }
  }

  /* ___Metodos para las acciones agrupadas___ */

  // Accion agrupada -> sincroniza estados de envios seleccionados
  protected function processBulksincEstados() {
    $query = 'select V_COD_AGE_CARGO, V_COD_AGE_ORI, V_ALBARAN from '
            . _DB_PREFIX_ . 'envialia_envio'
            . ' where id_order in (' . implode(',', $this->boxes) . ')';

    $resultado = Db::getInstance()->executeS($query);

    if ($resultado) {
      $strXML = '<ENVIOS>';
      foreach ($resultado as $envioSinc) {
        $strXML .= '<ENVIO V_COD_AGE_CARGO = "' . $envioSinc['V_COD_AGE_CARGO'] .
                '" V_COD_AGE_ORI = "' . $envioSinc['V_COD_AGE_ORI'] .
                '" V_ALBARAN = "' . $envioSinc['V_ALBARAN'] . '" />';
      }
      $strXML .= '</ENVIOS>';
      $strError = WebService::SincronizaEstadosEnvios($strXML);
      if ($strError <> '') {
        $this->errors[] = $this->l($strError);
      } else {
        $this->confirmations[] = "Estados sincronizados correctamente.";
      }
    } else {
      $this->warnings[] = "No existen envios a sincronizar.";
    }
  }

  // Accion agrupada -> sincroniza imprime etiqueta de envio
  protected function processBulkimprimeEtiqueta() {
    $this->imprimeEtiqueta = False;
    if (self::$boTokenLibre) {
      self::$boTokenLibre = FALSE;
			if (Tools::getValue('pedido') <> '') {
				$idOrders = Tools::getValue('pedido');
				$idOrders = str_replace("-", ",", $idOrders);
				
				$query = 'select V_COD_AGE_ORI, V_ALBARAN
									from ' . _DB_PREFIX_ . 'envialia_envio
									where id_order in (' . $idOrders . ')';
			}
			else{
				$query = 'select V_COD_AGE_ORI, V_ALBARAN
									from ' . _DB_PREFIX_ . 'envialia_envio
									where id_order in (' . implode(',', $this->boxes) . ')';
			}
      $resultado = Db::getInstance()->executeS($query);

      if ($resultado) {
        $strXmlEnvio = '<Envios>';
        foreach ($resultado as $envioImprime) {
          $strXmlEnvio .= '<Envio strCodAgeOri = "' . $envioImprime['V_COD_AGE_ORI'] . '" strAlbaran = "' . $envioImprime['V_ALBARAN'] . '" />';
        }
        $strXmlEnvio .= '</Envios>';
        $strResultado = WebService::ImprimeEnvioMasivo($strXmlEnvio);
        if (($strResultado <> '0') && ($strResultado <> '')) {
					self::$boTokenLibre = TRUE;
          $arrConf = EnvialiaConfig::getConfig();	
					
					if (Configuration::get("envialia_imp_eti_a4") == 1) {
						$boImprA4 = 1;
					} else {
						$boImprA4 = 0;
					}
					
							echo "<script>
							function borraEtiquetaMasivaGenerada() {
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
							var url = '../modules/EnvialiaCarrier/lib/imprimeEtiquetaMasiva.php';
							var params = 'envios=".$strXmlEnvio."&ImprA4=".$boImprA4 ."&agencia=".$arrConf['V_COD_AGE']."&urlWS=".$arrConf['V_URL_WEB']."&cliente=".$arrConf['V_COD_CLI']."&depto=".$arrConf['V_COD_CLI_DEP']."&pass=".$arrConf['BL_PASS']."&sesion=".$arrConf['V_ID_SESION']."';
							http.open('POST', url, true);

							//Send the proper header information along with the request
							http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

							http.onreadystatechange = function() {//Call a function when the state changes.
								if(http.readyState == 4 && http.status == 200) {
									window.open('../modules/EnvialiaCarrier/etiquetas/".$arrConf['V_ID_SESION'].".pdf');
									//Dos segundos después de abrir la etiqueta la borramos
									setTimeout(function() { borraEtiquetaMasivaGenerada(); }, 2000);
									return;
								}
							
							}
							http.send(params);
							</script>";
        } else {
          $this->errors[] = $this->l('Error al genera la etiqueta');
        }
      } else {
        $this->warnings[] = "No existen envios seleccionados.";
      }
    }
  }

  // Accion agrupada -> genera envios
  protected function processBulkgeneraEnvio() {
	$this->imprimeEtiqueta = False;
    if (self::$boTokenLibre) {
      self::$boTokenLibre = FALSE;
       $query = 'select o.id_order as ID_ENVIO
        from ' . _DB_PREFIX_ . 'orders o
        where o.id_order not in (select ee.id_order from ' . _DB_PREFIX_ . 'envialia_envio ee)
        and o.id_order in (' . implode(',', $this->boxes) . ')';

        $resultado = Db::getInstance()->executeS($query);

        if ($resultado) {
					if ((Configuration::get("envialia_bultos_fijo_num") == 0) && (Configuration::get("envialia_bultos_var_num") == 0)) {
	
						$arrConf = EnvialiaConfig::getConfig();
						$strUrlWebService = WebService::getURLWS($arrConf['V_URL_WEB']);
						if (!isset($strUrlWebService) || trim($strUrlWebService)==='') {
							return 'No ha configurado la url de conexión.';
						}
						
						$strAgeAux = trim($arrConf['V_COD_AGE']);
						$strCliAux = trim($arrConf['V_COD_CLI']);
						$strCliDepAux = trim($arrConf['V_COD_CLI_DEP']);
				
						$strSesion = WebService::login($strUrlWebService, $strAgeAux, $strCliAux, $strCliDepAux, $arrConf['BL_PASS']);
						$arrPedidos = '';
						$boAux = 0;
						foreach ($resultado as $idPedido) {			
							if ($boAux == 0){
								$arrPedidos = $idPedido["ID_ENVIO"];
								$boAux = 1;
							} else {
								$arrPedidos = $arrPedidos . ','.$idPedido["ID_ENVIO"];
							}
						}
						$arrPedidosAux = str_replace(",", "-", $arrPedidos);
						
						echo "<script> 
							var imprime_etiqueta = '".Configuration::get("envialia_perm_imp_eti")."';
							var bultos = prompt('Indica el número de bultos', '1');
							if ((bultos != null) && (bultos > 0)) {
								var http = new XMLHttpRequest();
								var url = '../modules/EnvialiaCarrier/lib/generaEnvioBultosMasivo.php';
								var params = 'bultos=' + bultos + '&arrPedidos=".$arrPedidos."';
								http.open('POST', url, true);
								http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
								http.send(params);
								http.onreadystatechange = function() {//Call a function when the state changes.
									if(http.readyState == 4 && http.status == 200) {
										alert('Envíos generados correctamente');
										
										if (imprime_etiqueta) {
											location.href='index.php?controller=AdminEnvialiaEnvio&submitBulkimprimeEtiquetaorders&token=".$this->token."&pedido=".$arrPedidosAux."#orders';
										} else {
											window.close;
											location.href='index.php?controller=AdminEnvialiaEnvio&token=".$this->token."';
										}
										return;
									}
									if(http.readyState == 4 && http.status == 900) {
									var strMensajeError = http.statusText;
										alert(strMensajeError);
										window.close();
									}					
								}
							} else {
								alert('No ha indicado el número de bultos');
							}
						</script>";
					}else{						
						$arrReturn = WebService::grabaEnvioMasivo($resultado);
						$resultados = $arrReturn['resultados'];

						if ($arrReturn == false) {
							$this->errors[] = "No se ha podido establecer la conexión con el servidor";
						} else {
							$resultados = $arrReturn['resultados'];
				
							if ($resultados <> '') {
								$this->errors[] = $this->l($resultados);
							} else {
								if (Configuration::get("envialia_perm_imp_eti") == 1) {
									$envios_generados = $arrReturn['envios'];
									if ($envios_generados <> '') {
										$this->confirmations[] = "Envíos generados correctamente.";
										self::$boTokenLibre = TRUE;
										
										$arrConf = EnvialiaConfig::getConfig();	
										if (Configuration::get("envialia_imp_eti_a4") == 1) {
											$boImprA4 = 1;
										} else {
											$boImprA4 = 0;
										}
										echo "<script>
										function borraEtiquetaMasivaGenerada() {
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
										var url = '../modules/EnvialiaCarrier/lib/imprimeEtiquetaMasiva.php';
										var params = 'envios=".$envios_generados."&ImprA4=".$boImprA4 ."&agencia=".$arrConf['V_COD_AGE']."&urlWS=".$arrConf['V_URL_WEB']."&cliente=".$arrConf['V_COD_CLI']."&depto=".$arrConf['V_COD_CLI_DEP']."&pass=".$arrConf['BL_PASS']."&sesion=".$arrConf['V_ID_SESION']."';
										http.open('POST', url, true);

										//Send the proper header information along with the request
										http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

										http.onreadystatechange = function() {//Call a function when the state changes.
											if(http.readyState == 4 && http.status == 200) {
												window.open('../modules/EnvialiaCarrier/etiquetas/".$arrConf['V_ID_SESION'].".pdf');
												//Dos segundos después de abrir la etiqueta la borramos
												setTimeout(function() { borraEtiquetaMasivaGenerada(); }, 2000);
												return;
											}
										
										}
										http.send(params);
										</script>";
									} else {
										$this->errors[] = $this->l('Error al genera la etiqueta');
									}
								} else {
									$this->confirmations[] = "Envíos generados correctamente.";
									self::$boTokenLibre = TRUE;
								}
							}
						}
					}
        } else {
			$this->warnings[] = "Los envíos ya han sido generados.";
        } 
    }
  }

  // Accion agrupada -> borrado envios
  protected function processBulkborraEnvio() {
    if (self::$boTokenLibre) {
      self::$boTokenLibre = FALSE;
      $query = 'select V_COD_AGE_CARGO, V_COD_AGE_ORI, V_ALBARAN from '
              . _DB_PREFIX_ . 'envialia_envio where id_order in(' . implode(',', $this->boxes) . ')';

      $resultado = Db::getInstance()->executeS($query);

      if ($resultado) {
        $strError = WebService::borraEnvioMasivo($resultado);

        if ($strError <> '') {
          $this->errors[] = $this->l($strError);
          self::$boTokenLibre = TRUE;
        } else {
          $this->confirmations[] = "Envíos borrados correctamente.";
          self::$boTokenLibre = TRUE;
        }
      } else {
        $this->warnings[] = "No existen envios seleccionados.";
        self::$boTokenLibre = TRUE;
      }
    }
  }

  /* ___Metodos para los botones de grid___ */

  // Boton imprime etiqueta en grid
  public function displayEtiquetaLink($token = null, $id, $name = null) {
    $tpl = $this->context->smarty->createTemplate(dirname(__FILE__) . '/../../views/templates/admin/list_action_generic.tpl');
    if (!array_key_exists('Etiqueta', self::$cache_lang))
      self::$cache_lang['Etiqueta'] = $this->l('Imp. etiqueta', 'Helper');

    $tpl->assign(array(
        'href' => self::$currentIndex . '&action=impEtiq&' . $this->identifier . '=' . $id . '&token=' . $this->token,
        'action' => self::$cache_lang['Etiqueta'],
        'id' => $id,
        'icon' => 'icon-file-text',
		'target' => '_blank'
    ));

    return $tpl->fetch();
  }

  // Boton genera envio en grid
  public function displayGenerarLink($token = null, $id, $name = null) {
    $tpl = $this->context->smarty->createTemplate(dirname(__FILE__) . '/../../views/templates/admin/list_action_generic.tpl');
    if (!array_key_exists('Generar', self::$cache_lang))
      self::$cache_lang['Generar'] = $this->l('Generar envio', 'Helper');
  
    $tpl->assign(array(
        'href' => self::$currentIndex . '&action=generaEnv&' . $this->identifier . '=' . $id . '&token=' . $this->token,
        'action' => self::$cache_lang['Generar'],
        'id' => $id,
        'icon' => 'icon-dropbox',
		'target' => ''
    ));

    return $tpl->fetch();
  }

  // Boton borra envio en grid
  public function displayBorrarLink($token = null, $id, $name = null) {
    $tpl = $this->context->smarty->createTemplate(dirname(__FILE__) . '/../../views/templates/admin/list_action_generic.tpl');
    if (!array_key_exists('Borrar', self::$cache_lang))
      self::$cache_lang['Borrar'] = $this->l('Borrar envio', 'Helper');

    $tpl->assign(array(
        'href' => self::$currentIndex . '&action=borraEnv&' . $this->identifier . '=' . $id . '&token=' . $this->token,
        'action' => self::$cache_lang['Borrar'],
        'id' => $id,
        'icon' => 'icon-trash',
		'target' => ''
    ));

    return $tpl->fetch();
  }

}
