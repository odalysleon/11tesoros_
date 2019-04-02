<?php

/**
 * Documento principal del modulo eCommerce
 * @author      miguel.cejas
 * @date        14/12/2016
 */
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/ModuleConst.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/EnvialiaTipoServ.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/dinalib.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/sql/StoredProcedure.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/EnvialiaConfig.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/WebService.php');

if (!defined('_PS_VERSION_'))
  exit;

class envialiacarrier extends Module {

  private $_html = '';
  protected $_errors = array();

  // Constructor principal
  public function __construct() {
    $this->name = 'envialiacarrier';
    $this->tab = CONST_CONF_MODULE_TAB;
    $this->author = 'Dinaprise';
    $this->version = CONST_CONF_MODULE_VERSION;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7');
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->l(CONST_CONF_MODULE_DISPLAY_NAME);
    $this->description = $this->l(CONST_CONF_MODULE_DESC);
  }

  // Instala componentes
  public function install() {
		
    $sqlInstall = dirname(__FILE__) . '/sql/install.sql';
    $sqlTarPeso = dirname(__FILE__) . '/sql/insertTarifaPeso.sql';
    $sqlTarImp = dirname(__FILE__) . '/sql/insertTarifaImporte.sql';

    $this->installProcedure();
		
    Configuration::updateValue('envialia_bultos_fijo_num', '0', true);
    Configuration::updateValue('envialia_bultos_var_num', '0', true);
    Configuration::updateValue('envialia_tar_imp_fijo', '0', true);
    Configuration::updateValue('envialia_tar_imp_mani', '0', true);
    Configuration::updateValue('envialia_tar_marg', '0', true);
    Configuration::updateValue('envialia_env_grat_pen_imp_min', '0', true);
    Configuration::updateValue('envialia_env_grat_int_imp_min', '0', true);
    Configuration::updateValue('envialia_env_grat_pen', '0', true);
    Configuration::updateValue('envialia_env_grat_int', '0', true);
    Configuration::updateValue('envialia_perm_grab_pedido', '0', true);
    Configuration::updateValue('envialia_perm_imp_eti', '0', true);
    Configuration::updateValue('envialia_imp_eti_a4', '0', true);
    Configuration::updateValue('envialia_imp_tar_impo', '0', true);
    Configuration::updateValue('envialia_imp_tar_peso', '0', true);

    return parent::install() &&
            $this->createModuleTab('AdminParentTab', 'ENVIALIA', '') &&
            $this->createModuleTab('AdminEnvialiaEnvio', 'Envios', 'AdminParentTab') &&
            $this->createModuleTab('AdminEnvialiaTarifaPesos', 'Tarifa pesos', 'AdminParentTab') &&
            $this->createModuleTab('AdminEnvialiaTarifaImportes', 'Tarifa importes', 'AdminParentTab') &&
            $this->createModuleTab('AdminEnvialiaEstado', 'Estados', 'AdminParentTab') &&
            $this->installCarriers() &&
            $this->registerHook('displayAfterCarrier') &&
            $this->registerHook('actionCarrierUpdate') &&
            $this->registerHook('actionOrderStatusPostUpdate') &&
            DinaLib::cargaFicheroSQL($sqlInstall) &&
            DinaLib::cargaFicheroSQL($sqlTarPeso) &&
            DinaLib::cargaFicheroSQL($sqlTarImp);
  }

  // Desinstala componentes
  public function uninstall() {
    $sql_file = dirname(__FILE__) . '/sql/uninstall.sql';

    return parent::uninstall() &&
            $this->uninstallModuleTab('AdminEnvialiaEnvio') &&
            $this->uninstallModuleTab('AdminEnvialiaTarifaPesos') &&
            $this->uninstallModuleTab('AdminEnvialiaTarifaImportes') &&
            $this->uninstallModuleTab('AdminEnvialiaEstado') &&
            $this->uninstallModuleTab('AdminParentTab') &&
            $this->uninstallCarriers() &&
            DinaLib::cargaFicheroSQL($sql_file) &&
            $this->deletePersonalConfiguration();
  }

  public function installProcedure() {		
		$sql = CONST_PA_TAR_FIJA;
    Db::getInstance()->execute($sql);
		
		$sql = CONST_PA_TAR_PESO;
    Db::getInstance()->execute($sql);
		
		$sql = CONST_PA_TAR_IMPO;
    Db::getInstance()->execute($sql);
    return true;
  }

  // Hooks
  public function hookactionCarrierUpdate($params) {
    $carrier_afectado = $params['id_carrier'];
    $nuevo_id = $params['carrier']->id;

    $sql = 'UPDATE ' . _DB_PREFIX_ . 'envialia_tipo_serv SET ID_CARRIER = "' . $nuevo_id . '"
            WHERE ID_CARRIER = "' . $carrier_afectado . '"';

    Db::getInstance()->execute($sql);
  }
	
	public function hookactionOrderStatusPostUpdate($params) {
    if (Configuration::get('envialia_perm_grab_pedido') <> 0) {
      $id_order = $params['id_order'];
      $estado = $params['newOrderStatus'];
      $id_status = $estado->id;

      if (Configuration::get('envialia_estado_pedido') == $id_status) {
        $query = 'select o.id_order as ID_ENVIO
              from ' . _DB_PREFIX_ . 'orders o
              where o.id_order not in (select ee.id_order from ' . _DB_PREFIX_ . 'envialia_envio ee)
              and o.id_order = ' . $id_order;
							
        $resultado = Db::getInstance()->execute($query);
        if ($resultado) {
          $arrReturn = WebService::grabaEnvio($id_order);
          $strError = $arrReturn['error'];
          if ($strError <> '') {
            $this->errors[] = $this->l($strError);
          } else {
            $this->confirmations[] = "Envio generado correctamente.";
            if (Configuration::get("envialia_perm_imp_eti") == 1) {
              $etiqueta = $arrReturn['etiqueta'];
              if ($etiqueta <> '0') {
                $decoded = base64_decode($etiqueta);
                $file = 'etiqueta.pdf';
                file_put_contents($file, $decoded);

                if (file_exists($file)) {
                  header('Content-Description: File Transfer');
                  header('Content-Type: application/octet-stream');
                  header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                  header('Expires: 0');
                  header('Cache-Control: must-revalidate');
                  header('Pragma: public');
                  header('Content-Length: ' . filesize($file));
                  readfile($file);
                  exit;
                }
              }
            }
          }
        }
      }
    }
  }
	
	public function hookdisplayAfterCarrier($params) {
		
		$cart = $params['cart'];
		if (isset($cart->id_carrier)) {
			if ($cart->id_carrier > 0){
				$query = 'select c.id_carrier as ID_CARRIER
									from ' . _DB_PREFIX_ . 'carrier c
									join ' . _DB_PREFIX_ . 'envialia_tipo_serv ets 
										on ets.ID_CARRIER = c.id_carrier 
	 								where c.external_module_name = "envialiacarrier"
										and ets.T_INT = "0"
										and c.id_carrier = "'.$cart->id_carrier.'"';
				
        $resultado = Db::getInstance()->executeS($query);
        if ($resultado) {
					
					$query = 'select ad.id_address, ad.postcode, ecp.V_COD_PROV from ' . _DB_PREFIX_ . 'address ad
										left join ' . _DB_PREFIX_ . 'envialia_cp ecp
											on ecp.id_state = ad.id_state											
										where ad.id_address = ' . $cart->id_address_delivery. '
										  and substring(ad.postcode,1,2) <> ecp.V_COD_PROV';
											
					$resultado = Db::getInstance()->executeS($query);
					if ($resultado) {
						
						$query = 'UPDATE ' . _DB_PREFIX_ . 'address SET id_state = NULL, postcode = NULL
						WHERE id_address = "' . $cart->id_address_delivery . '"';
							
						//Db::getInstance()->execute($query);
						return "<script>
							alert('código postal incorrecto');
							location.href='pedido?id_address=". $cart->id_address_delivery . "&editAddress=delivery&token=".Tools::getToken()."';
							var x = document.getElementsByName('postcode').style.color = 'red';
						</script>";
					}
				}
			}
		}
	}
	
  // Tabs
  public function createModuleTab($className, $displayName, $tabParent) {
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = $className;
    $tab->name = array();
    foreach (Language::getLanguages(true) as $lang) {
      $tab->name[$lang['id_lang']] = $displayName;
    }
    if ($tabParent) {
      $tab->id_parent = (int) Tab::getIdFromClassName($tabParent);
    } else {
      $tab->id_parent = 0;
    }
    $tab->module = $this->name;
    return $tab->add();
  }

  public function uninstallModuleTab($class_name) {
    $id_tab = (int) Tab::getIdFromClassName($class_name);
    $tab = new Tab((int) $id_tab);
    return $tab->delete();
  }

  // Carriers
  public function installCarriers() {
    $carriers_list = array(
        array('cod' => 'E24', 'name' => 'Servicio E-Comm 24', 'active' => 0, 'deleted' => 1),
        array('cod' => 'E72', 'name' => 'Servicio E-Comm 72', 'active' => 0, 'deleted' => 1),
        array('cod' => 'EEU', 'name' => 'E-COMM EUROPE EXPRESS', 'active' => 0, 'deleted' => 1),
        array('cod' => 'EWW', 'name' => 'E-COMM WORLDWIDE', 'active' => 0, 'deleted' => 1)
    );

    foreach ($carriers_list as $values) {
      $carrier = new Carrier();
      $carrier->name = $values['name'];
      $carrier->id_tax_rules_group = 1;
      $carrier->need_range = true;
      $carrier->is_module = true;
      $carrier->external_module_name = $this->name;
      $carrier->active = $values['active'];
      $carrier->deleted = $values['deleted'];

      $languages = Language::getLanguages();
      foreach ($languages as $language) {
        $carrier->delay[(int) $language['id_lang']] = $values['name'];
      }

      if ($carrier->save() == false)
        return false;

      $values['id'] = (int) $carrier->id;

      copy(dirname(__FILE__) . '/views/images/' . $values['cod'] . '.jpg', _PS_SHIP_IMG_DIR_ . '/' . (int) $carrier->id . '.jpg');

      $groups = array();
      foreach (Group::getGroups((int) Context::getContext()->language->id) as $group)
        $groups[] = (int) $group['id_group'];

      if (!$carrier->setGroups($groups))
        return false;
    }
    return true;
  }

  public function uninstallCarriers() {
    $carriers_list = array(
        'E24' => 'servicio E-Comm 24',
        'E72' => 'servicio E-Comm 72',
        'EEU' => 'E-COMM EUROPE EXPRESS',
        'EWW' => 'E-COMM WORLDWIDE',
    );

    foreach ($carriers_list as $key => $value) {
      $carrier_id = Configuration::get($key);
      $carrier = new Carrier($carrier_id);
      $carrier->delete();

      Configuration::deleteByName($key);
    }

    return true;
  }

  // Elimina la configuracion personal 
  public function deletePersonalConfiguration() {
    Configuration::deleteByName('envialia_ecomm_24');
    Configuration::deleteByName('envialia_ecomm_72');
    Configuration::deleteByName('envialia_ecomm_ee');
    Configuration::deleteByName('envialia_ecomm_ww');
    Configuration::deleteByName('envialia_bultos');
    Configuration::deleteByName('envialia_bultos_fijo_num');
    Configuration::deleteByName('envialia_bultos_var_num');
    Configuration::deleteByName('envialia_conf_tar');
    Configuration::deleteByName('envialia_tar_imp_fijo');
    Configuration::deleteByName('envialia_tar_impuesto');
    Configuration::deleteByName('envialia_tar_imp_mani');
    Configuration::deleteByName('envialia_tar_marg');
    Configuration::deleteByName('envialia_env_grat_pen');
    Configuration::deleteByName('envialia_env_grat_pen_tipo_serv');
    Configuration::deleteByName('envialia_env_grat_pen_imp_min');
    Configuration::deleteByName('envialia_env_grat_int');
    Configuration::deleteByName('envialia_env_grat_int_tipo_serv');
    Configuration::deleteByName('envialia_env_grat_int_imp_min');
    Configuration::deleteByName('envialia_perm_grab_pedido');
    Configuration::deleteByName('envialia_estado_pedido');
    Configuration::deleteByName('envialia_perm_imp_eti');
    Configuration::deleteByName('envialia_imp_eti_a4');
    Configuration::deleteByName('envialia_tarifa_impo');
    Configuration::deleteByName('envialia_tarifa_peso');

    return true;
  }

  public function getContent() {
    $this->context->controller->addJS($this->_path . 'js/config.js', 'all');
    $this->_html .= '<h2>' . $this->displayName . '.</h2>';

    if (Tools::isSubmit('btnSubmit') || Tools::isSubmit('btnSubmitTarifas')) {
      $this->_postValidation();

      if (!count($this->_errors))
        $this->_postProcess();
      else
        foreach ($this->_errors as $error)
          $this->_html .= $this->displayError($error);
    }

    $this->_displayForm();

    return $this->_html;
  }

  // Metodo privado donde se comprueba que la informacion
  // introducida en el formulario es correcta
  private function _postValidation() {
    if (Tools::isSubmit('btnSubmit')) {
      // CONTROL DE ERRORES EN DATOS DE CONEXION
      if (!Tools::getValue('envialia_cod_age')) {
        $this->_errors[] = $this->l('El codigo de agencia es obligatorio.');
      }
      if (!Tools::getValue('envialia_cod_cli')) {
        $this->_errors[] = $this->l('El codigo de cliente es obligatorio.');
      }
      if (!Tools::getValue('envialia_pass')) {
        $this->_errors[] = $this->l('La contrase&ntilde;a es obligatoria.');
      }

      if (!Tools::getValue('envialia_url_ws')) {
        $this->_errors[] = $this->l('La direccion del WS es obligatoria.');
      }

      // CONTROL DE ERRORES EN DATOS DE CONEXION
      if ((Tools::getValue('envialia_bultos') == 0) && (!Tools::getValue('envialia_bultos_fijo_num'))) {
        $this->_errors[] = $this->l('Debes indicar el numero de bultos fijos');
      }
      if ((Tools::getValue('envialia_bultos') == 1) && ((!Tools::getValue('envialia_bultos_var_num')) || (Tools::getValue('envialia_bultos_var_num') == '0'))) {
        $this->_errors[] = $this->l('Debes indicar el numero de unidades que equivalen a un bulto');
      }

      // CONTROL DE ERRORES EN CALCULO DE COSTES DE ENVIO
      if ((Tools::getValue('envialia_conf_tar') == 2) && (!Tools::getValue('envialia_tar_imp_fijo'))) {
        $this->_errors[] = $this->l('Debes indicar el importe fijo por envio');
      }

      // CONTROL ERRORES ENVIOS GRATUITOS
      if ((Tools::getValue('envialia_env_grat_pen') == 1) && (!Tools::getValue('envialia_env_grat_pen_imp_min'))) {
        $this->_errors[] = $this->l('Debes indicar el importe minimo para que el envio sea gratuito en peninsula');
      }
      if ((Tools::getValue('envialia_env_grat_int') == 1) && (!Tools::getValue('envialia_env_grat_int_imp_min'))) {
        $this->_errors[] = $this->l('Debes indicar el importe minimo para que el envio internacional sea gratuito');
      }
    }
  }

  // Metodo privado que se encarga de almacenar en la base de datos
  // la informacion introducida en el formulario.
  // Los datos se guardan en la tabla configuration y configuration_lang.
  // Se emplea el metodo updateValue de la clase Configuration
  private function _postProcess() {
    if (Tools::isSubmit('btnSubmitTarifas')) {
      if (Tools::getValue('envialia_imp_tar_impo')) {
        $query = 'delete from ' . _DB_PREFIX_ . 'envialia_tarifa_importes';
        Db::getInstance()->execute($query);
        
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'envialia_tarifa_importes` (`V_COD_TIPO_SERV`, `I_COD_ZONA`, `F_IMPORTE`, `F_PRECIO`)
                  SELECT V_COD_TIPO_SERV, I_COD_ZONA, F_IMPORTE, F_PRECIO FROM ' . _DB_PREFIX_ . 'envialia_tarifa_importes_base';
        Db::getInstance()->execute($query);
      }
      if (Tools::getValue('envialia_imp_tar_peso')) {
        $query = 'delete from ' . _DB_PREFIX_ . 'envialia_tarifa_pesos';
        Db::getInstance()->execute($query);
        
        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'envialia_tarifa_pesos` (`V_COD_TIPO_SERV`, `I_COD_ZONA`, `F_PESO`, `F_PRECIO`)
                  SELECT V_COD_TIPO_SERV, I_COD_ZONA, F_PESO, F_PRECIO FROM ' . _DB_PREFIX_ . 'envialia_tarifa_pesos_base';
        Db::getInstance()->execute($query);
      }
      $this->_html .= $this->displayConfirmation($this->l('Importación de tarifas realizada correctamente.'));
    }

    if (Tools::isSubmit('btnSubmit')) {

      $arrConf = EnvialiaConfig::getConfig();

      if ($arrConf['V_ID_SESION'] <> '') {
        if (($arrConf['V_COD_AGE'] <> Tools::getValue('envialia_cod_age')) ||
                ($arrConf['V_COD_CLI'] <> Tools::getValue('envialia_cod_cli')) ||
                ($arrConf['V_COD_CLI_DEP'] <> Tools::getValue('envialia_cod_dep')) ||
                ($arrConf['BL_PASS'] <> Tools::getValue('envialia_pass')) ||
                ($arrConf['V_URL_WEB'] <> Tools::getValue('envialia_url_ws'))) {
          // Borra el id de sesion en caso de que se modifique algun parametro
          EnvialiaConfig::setSesion('');
        }
      }

      EnvialiaConfig::setConfigAcceso(Tools::getValue('envialia_cod_age'), Tools::getValue('envialia_cod_cli'), Tools::getValue('envialia_cod_dep'), Tools::getValue('envialia_url_ws'), Tools::getValue('envialia_pass'));
      Configuration::updateValue('envialia_ecomm_24', Tools::getValue('envialia_ecomm_24'), true);
      Configuration::updateValue('envialia_ecomm_72', Tools::getValue('envialia_ecomm_72'), true);
      Configuration::updateValue('envialia_ecomm_ee', Tools::getValue('envialia_ecomm_ee'), true);
      Configuration::updateValue('envialia_ecomm_ww', Tools::getValue('envialia_ecomm_ww'), true);
      Configuration::updateValue('envialia_bultos', Tools::getValue('envialia_bultos'), true);
      Configuration::updateValue('envialia_bultos_fijo_num', Tools::getValue('envialia_bultos_fijo_num'), true);
      Configuration::updateValue('envialia_bultos_var_num', Tools::getValue('envialia_bultos_var_num'), true);
      Configuration::updateValue('envialia_conf_tar', Tools::getValue('envialia_conf_tar'), true);
      Configuration::updateValue('envialia_tar_imp_fijo', Tools::getValue('envialia_tar_imp_fijo'), true);
      Configuration::updateValue('envialia_tar_impuesto', Tools::getValue('envialia_tar_impuesto'), true);
      Configuration::updateValue('envialia_tar_imp_mani', Tools::getValue('envialia_tar_imp_mani'), true);
      Configuration::updateValue('envialia_tar_marg', Tools::getValue('envialia_tar_marg'), true);
      Configuration::updateValue('envialia_env_grat_pen', Tools::getValue('envialia_env_grat_pen'), true);
      Configuration::updateValue('envialia_env_grat_pen_tipo_serv', Tools::getValue('envialia_env_grat_pen_tipo_serv'), true);
      Configuration::updateValue('envialia_env_grat_pen_imp_min', Tools::getValue('envialia_env_grat_pen_imp_min'), true);
      Configuration::updateValue('envialia_env_grat_int', Tools::getValue('envialia_env_grat_int'), true);
      Configuration::updateValue('envialia_env_grat_int_tipo_serv', Tools::getValue('envialia_env_grat_int_tipo_serv'), true);
      Configuration::updateValue('envialia_env_grat_int_imp_min', Tools::getValue('envialia_env_grat_int_imp_min'), true);
      Configuration::updateValue('envialia_perm_grab_pedido', Tools::getValue('envialia_perm_grab_pedido'), true);
      Configuration::updateValue('envialia_estado_pedido', Tools::getValue('envialia_estado_pedido'), true);
      Configuration::updateValue('envialia_perm_imp_eti', Tools::getValue('envialia_perm_imp_eti'), true);
      Configuration::updateValue('envialia_imp_eti_a4', Tools::getValue('envialia_imp_eti_a4'), true);

      // Configuracion transportistas
      $boWhereActivo = False;
      $boWhereDesactivo = False;
      $strWhereActivo = ' where 1 = 2';
      $strWhereDesactivo = ' where 1 = 2';

      if (Tools::getValue('envialia_ecomm_24')) {
        $boWhereActivo = True;
        $strWhereActivo = $strWhereActivo . ' or name = "Servicio E-Comm 24"';
      } else {
        $boWhereDesactivo = True;
        $strWhereDesactivo = $strWhereDesactivo . ' or name = "Servicio E-Comm 24"';
      }

      if (Tools::getValue('envialia_ecomm_72')) {
        $boWhereActivo = True;
        $strWhereActivo = $strWhereActivo . ' or name = "Servicio E-Comm 72"';
      } else {
        $boWhereDesactivo = True;
        $strWhereDesactivo = $strWhereDesactivo . ' or name = "Servicio E-Comm 72"';
      }

      if (Tools::getValue('envialia_ecomm_ee')) {
        $boWhereActivo = True;
        $strWhereActivo = $strWhereActivo . ' or name = "E-COMM EUROPE EXPRESS"';
      } else {
        $boWhereDesactivo = True;
        $strWhereDesactivo = $strWhereDesactivo . ' or name = "E-COMM EUROPE EXPRESS"';
      }

      if (Tools::getValue('envialia_ecomm_ww')) {
        $boWhereActivo = True;
        $strWhereActivo = $strWhereActivo . ' or name = "E-COMM WORLDWIDE"';
      } else {
        $boWhereDesactivo = True;
        $strWhereDesactivo = $strWhereDesactivo . ' or name = "E-COMM WORLDWIDE"';
      }

      // Configuracion de tarifas
      $strSetUpdate = ', c.shipping_method = ';
      if ((int) Tools::getValue('envialia_conf_tar') == 0) {
        $strSetUpdate = $strSetUpdate . Carrier::SHIPPING_METHOD_WEIGHT;
				$query = 'CALL PA_GRABA_TARIFA_PESO();';
      } else 
				if ((int) Tools::getValue('envialia_conf_tar') == 1) {
					$strSetUpdate = $strSetUpdate . Carrier::SHIPPING_METHOD_PRICE;
					$query = 'CALL PA_GRABA_TARIFA_IMPORTE();';
				} else {			
					$strSetUpdate = $strSetUpdate . Carrier::SHIPPING_METHOD_PRICE;
					$query = 'CALL PA_GRABA_TARIFA_FIJA();';
				}

      Db::getInstance()->execute($query);

      // UPDATE TRANSPORTISTA
      $query = 'update ' . _DB_PREFIX_ . 'carrier c join ' . _DB_PREFIX_ . 'envialia_tipo_serv ets ON ets.ID_CARRIER = c.id_carrier '.
			   'set c.active = 1, c.deleted = 0 ' . $strSetUpdate . $strWhereActivo;
      Db::getInstance()->execute($query);

      $query = 'update ' . _DB_PREFIX_ . 'carrier c join ' . _DB_PREFIX_ . 'envialia_tipo_serv ets ON ets.ID_CARRIER = c.id_carrier '.
			   'set c.active = 0, c.deleted = 1 ' . $strSetUpdate . $strWhereDesactivo;
      Db::getInstance()->execute($query);


      // Configuracion impuestos
      $query = 'delete ctrgs from ' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop ctrgs left join ' . _DB_PREFIX_ . 'carrier c on c.id_carrier = ctrgs.id_carrier' .
              ' where c.external_module_name = "envialiacarrier"';
      Db::getInstance()->execute($query);

      $id_shop = (int) Context::getContext()->shop->id;

      $query = 'insert into ' . _DB_PREFIX_ . 'carrier_tax_rules_group_shop (id_carrier, id_tax_rules_group, id_shop) ' .
              'select id_carrier, ' . Tools::getValue('envialia_tar_impuesto') . ', ' . $id_shop . ' from ' .
              _DB_PREFIX_ . 'carrier where external_module_name = "envialiacarrier"';
      Db::getInstance()->execute($query);

      $this->_html .= $this->displayConfirmation($this->l('Actualizada la configuracion de envialiacarrier.'));
    }
  }

  // Genera el formulario de configuracion
  private function _displayForm() {

    $this->fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Datos conexion a sistema envialia'),
            'icon' => 'icon-cogs'
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Codigo de agencia'),
                'name' => 'envialia_cod_age',
                'prefix' => '<i class="icon icon-home"></i>',
                'col' => 4,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Codigo de cliente'),
                'name' => 'envialia_cod_cli',
                'prefix' => '<i class="icon icon-user"></i>',
                'col' => 4,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Codigo de departamento'),
                'name' => 'envialia_cod_dep',
                'prefix' => '<i class="icon icon-group"></i>',
                'col' => 4,
                'desc' => $this->l('(Solo para departamentos)'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Contrase&ntilde;a'),
                'name' => 'envialia_pass',
                'prefix' => '<i class="icon icon-unlock-alt"></i>',
                'col' => 4,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => $this->l('URL de WS'),
                'name' => 'envialia_url_ws',
                'prefix' => '<i class="icon icon-globe"></i>',
                'col' => 4,
                'required' => true
            ),
        )
    );

    $this->fields_form[1]['form'] = array(
        'legend' => array(
            'title' => $this->l('Servicios envialia disponibles'),
            'icon' => 'icon-truck'
        ),
        'input' => array(
            array(
                'type' => 'switch',
                'label' => $this->l('Habilitar el servicio E-Comm 24'),
                'name' => 'envialia_ecomm_24',
                'desc' => $this->l('Envios peninsulares'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_ecomm_24_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_ecomm_24_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Habilitar el servicio E-Comm 72'),
                'name' => 'envialia_ecomm_72',
                'desc' => $this->l('Envios peninsulares'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_ecomm_72_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_ecomm_72_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Habilitar el Servicio E-Comm Europe Express'),
                'name' => 'envialia_ecomm_ee',
                'desc' => $this->l('Envios Europa'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_ecomm_ee_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_ecomm_ee_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Habilitar el Servicio E-Comm WorldWide'),
                'name' => 'envialia_ecomm_ww',
                'desc' => $this->l('Envios internacionales'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_ecomm_ww_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_ecomm_ww_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
        )
    );


    $this->fields_form[2]['form'] = array(
        'legend' => array(
            'title' => $this->l('Generacion de envios'),
            'icon' => 'icon-dropbox'
        ),
        'input' => array(
            array(
                'type' => 'radio',
                'name' => 'envialia_bultos',
                'label' => $this->l('Bultos por envios'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_bultos',
                        'value' => 0,
                        'label' => $this->l('Fijo'),
                    ),
                    array(
                        'id' => 'envialia_bultos',
                        'value' => 1,
                        'label' => $this->l('Variable'),
                    ),
                    array(
                        'id' => 'envialia_bultos',
                        'value' => 2,
                        'label' => $this->l('Solicitar numero de bultos al generar el envio'),
                    ),
                )
            ),
            array(
                'type' => 'text',
                'name' => 'envialia_bultos_fijo_num',
                'col' => 1,
                'prefix' => '<i class="icon icon-cube"></i>',
                'label' => $this->l('Numero de bultos fijos'),
                'desc' => $this->l('(Solo con bulto fijo)'),
            ),
            array(
                'type' => 'text',
                'col' => 1,
                'prefix' => '<i class="icon icon-cube"></i>',
                'name' => 'envialia_bultos_var_num',
                'label' => $this->l('Numero de unidades que equivalen a un bulto'),
                'desc' => $this->l('(Solo con bulto variable)'),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Grabar envio cuando el pedido pase al estado seleccionado.'),
                'name' => 'envialia_perm_grab_pedido',
                'desc' => $this->l('Debes seleccionar un estado'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_perm_grab_pedido_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_perm_grab_pedido_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->trans('Estado', array(), 'Admin.Global'),
                'name' => 'envialia_estado_pedido',
                'options' => array(
                    'query' => OrderState::getOrderStates($this->context->language->id),
                    'id' => 'id_order_state',
                    'name' => 'name'
                ),
                'required' => true,
                'hint' => $this->l('Solo necesario si tienes la opcion anterior activada')
            ),
        )
    );

		$this->fields_form[3]['form']= array(
        'legend' => array(
            'title' => $this->l('Impresion de etiquetas'),
            'icon' => 'icon-print'
        ),
				'input' => array(
            array(
                'type' => 'switch',
                'label' => $this->l('Impresion automatica de etiquetas al generar el envio'),
                'name' => 'envialia_perm_imp_eti',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_perm_imp_eti_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_perm_imp_eti_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
						array(
                'type' => 'switch',
                'label' => $this->l('Impresion de etiquetas en formato A4'),
                'name' => 'envialia_imp_eti_a4',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_imp_eti_a4_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_imp_eti_a4_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
					)
		);
    $this->fields_form[4]['form'] = array(
        'legend' => array(
            'title' => $this->l('Calculo de costes del envio'),
            'icon' => 'icon-calculator'
        ),
				
        'input' => array(
            array(
                'type' => 'radio',
                'name' => 'envialia_conf_tar',
                'label' => $this->l('Configuracion de tarifas'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_conf_tar',
                        'value' => 0,
                        'label' => $this->l('Por zona y peso'),
                    ),
                    array(
                        'id' => 'envialia_conf_tar',
                        'value' => 1,
                        'label' => $this->l('Por zona e importe'),
                    ),
                    array(
                        'id' => 'envialia_conf_tar',
                        'value' => 2,
                        'label' => $this->l('Importe fijo por envio'),
                    ),
                )
            ),
            array(
                'type' => 'text',
                'col' => 1,
                'name' => 'envialia_tar_imp_fijo',
                'label' => $this->l('Importe fijo por envio'),
                'prefix' => '<i class="icon icon-eur"></i>',
                'desc' => $this->l('(Solo para la tarifa de importe fijo por envio)'),
            ),
            array(
                'type' => 'select',
                'label' => $this->trans('Tax', array(), 'Admin.Global'),
                'name' => 'envialia_tar_impuesto',
                'options' => array(
                    'query' => TaxRulesGroup::getTaxRulesGroups(true),
                    'id' => 'id_tax_rules_group',
                    'name' => 'name',
                    'default' => array(
                        'label' => $this->trans('No Tax', array(), 'Admin.Global'),
                        'value' => 0
                    )
                )
            ),
            array(
                'type' => 'text',
                'col' => 1,
                'name' => 'envialia_tar_imp_mani',
                'prefix' => '<i class="icon icon-eur"></i>',
                'label' => $this->l('Importe de manipulacion por pedido'),
                'desc' => $this->l('(Para todas las tarifas)'),
            ),
            array(
                'type' => 'text',
                'col' => 1,
                'name' => 'envialia_tar_marg',
                'label' => $this->l('Margen sobre gastos de envios (%)'),
                'prefix' => '<i class="icon icon-money"></i>',
                'desc' => $this->l('(Para todas las tarifas)'),
            ),
        )
    );

    $this->fields_form[5]['form'] = array(
        'legend' => array(
            'title' => $this->l('Envios gratuitos'),
            'icon' => 'icon-eur'
        ),
        'input' => array(
            array(
                'type' => 'switch',
                'label' => $this->l('Activar el envio gratuito en peninsula'),
                'name' => 'envialia_env_grat_pen',
                'is_bool' => true,
                'hint' => $this->l('Solo válido para tarifa de importe fijo o por zona e importe'),
                'values' => array(
                    array(
                        'id' => 'envialia_env_grat_pen_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_env_grat_pen_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Servicio con el que emitir el envio gratuito en peninsula'),
                'name' => 'envialia_env_grat_pen_tipo_serv',
                'options' => array(
                    'query' => EnvialiaTipoServ::getServiciosPen(),
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'name' => 'envialia_env_grat_pen_imp_min',
                'col' => 1,
                'prefix' => '<i class="icon icon-eur"></i>',
                'label' => $this->l('Importe minimo para que el envio sea gratuito en peninsula')
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Activar el envio gratuito internacional'),
                'name' => 'envialia_env_grat_int',
                'hint' => $this->l('Solo válido para tarifa de importe fijo o por zona e importe'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_env_grat_int_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_env_grat_int_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Servicio con el que emitir el envio gratuito en peninsula'),
                'name' => 'envialia_env_grat_int_tipo_serv',
                'options' => array(
                    'query' => EnvialiaTipoServ::getServiciosInt(),
                    'id' => 'id_option',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'text',
                'col' => 1,
                'prefix' => '<i class="icon icon-eur"></i>',
                'name' => 'envialia_env_grat_int_imp_min',
                'label' => $this->l('Importe minimo para que el envio internacional sea gratuito')
            ),
        ),
        'submit' => array(
            'name' => 'btnSubmit',
            'title' => $this->l('Guardar'),
        )
    );



    $this->fields_form[6]['form'] = array(
        'legend' => array(
            'title' => $this->l('Importacion de tarifas'),
            'icon' => 'icon-download'
        ),
        'input' => array(
            array(
                'type' => 'switch',
                'label' => $this->l('Importar tarifa basica por importe'),
                'name' => 'envialia_imp_tar_impo',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_imp_tar_impo_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_imp_tar_impo_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Importar tarifa basica por peso'),
                'name' => 'envialia_imp_tar_peso',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'envialia_imp_tar_peso_on',
                        'value' => true,
                        'label' => $this->l('Si')
                    ),
                    array(
                        'id' => 'envialia_imp_tar_peso_off',
                        'value' => false,
                        'label' => $this->l('No')
                    )
                ),
            ),
        ),
        'submit' => array(
            'name' => 'btnSubmitTarifas',
            'title' => $this->l('Importar'),
        )
    );

    // Obtiene los valores para los campos definidos con anterioridad
    $arrConfAcceso = EnvialiaConfig::getConfig();

    $confPrev = Configuration::getMultiple(array('envialia_ecomm_24', 'envialia_ecomm_72', 'envialia_ecomm_ee',
                'envialia_ecomm_ww', 'envialia_bultos', 'envialia_bultos_fijo_num', 'envialia_bultos_var_num',
                'envialia_conf_tar', 'envialia_tar_imp_fijo', 'envialia_tar_impuesto', 'envialia_tar_imp_mani',
                'envialia_tar_marg', 'envialia_env_grat_pen', 'envialia_env_grat_pen_tipo_serv', 'envialia_env_grat_pen_imp_min',
                'envialia_env_grat_int', 'envialia_env_grat_int_tipo_serv', 'envialia_env_grat_int_imp_min',
                'envialia_perm_grab_pedido', 'envialia_estado_pedido', 'envialia_perm_imp_eti', 'envialia_imp_eti_a4', 'envialia_imp_tar_impo', 'envialia_imp_tar_peso'));

    $this->fields_value['envialia_cod_age'] = $arrConfAcceso['V_COD_AGE'];
    $this->fields_value['envialia_cod_cli'] = $arrConfAcceso['V_COD_CLI'];
    $this->fields_value['envialia_cod_dep'] = $arrConfAcceso['V_COD_CLI_DEP'];
    $this->fields_value['envialia_pass'] = $arrConfAcceso['BL_PASS'];
    $this->fields_value['envialia_url_ws'] = $arrConfAcceso['V_URL_WEB'];

    $this->fields_value['envialia_ecomm_24'] = $confPrev['envialia_ecomm_24'];
    $this->fields_value['envialia_ecomm_72'] = $confPrev['envialia_ecomm_72'];
    $this->fields_value['envialia_ecomm_ee'] = $confPrev['envialia_ecomm_ee'];
    $this->fields_value['envialia_ecomm_ww'] = $confPrev['envialia_ecomm_ww'];
    $this->fields_value['envialia_bultos'] = $confPrev['envialia_bultos'];
    $this->fields_value['envialia_bultos_fijo_num'] = $confPrev['envialia_bultos_fijo_num'];
    $this->fields_value['envialia_bultos_var_num'] = $confPrev['envialia_bultos_var_num'];
    $this->fields_value['envialia_conf_tar'] = $confPrev['envialia_conf_tar'];
    $this->fields_value['envialia_tar_imp_fijo'] = $confPrev['envialia_tar_imp_fijo'];
    $this->fields_value['envialia_tar_impuesto'] = $confPrev['envialia_tar_impuesto'];
    $this->fields_value['envialia_tar_imp_mani'] = $confPrev['envialia_tar_imp_mani'];
    $this->fields_value['envialia_tar_marg'] = $confPrev['envialia_tar_marg'];
    $this->fields_value['envialia_env_grat_pen'] = $confPrev['envialia_env_grat_pen'];
    $this->fields_value['envialia_env_grat_pen_tipo_serv'] = $confPrev['envialia_env_grat_pen_tipo_serv'];
    $this->fields_value['envialia_env_grat_pen_imp_min'] = $confPrev['envialia_env_grat_pen_imp_min'];
    $this->fields_value['envialia_env_grat_int'] = $confPrev['envialia_env_grat_int'];
    $this->fields_value['envialia_env_grat_int_tipo_serv'] = $confPrev['envialia_env_grat_int_tipo_serv'];
    $this->fields_value['envialia_env_grat_int_imp_min'] = $confPrev['envialia_env_grat_int_imp_min'];
    $this->fields_value['envialia_perm_grab_pedido'] = $confPrev['envialia_perm_grab_pedido'];
    $this->fields_value['envialia_estado_pedido'] = $confPrev['envialia_estado_pedido'];
    $this->fields_value['envialia_perm_imp_eti'] = $confPrev['envialia_perm_imp_eti'];
    $this->fields_value['envialia_imp_eti_a4'] = $confPrev['envialia_imp_eti_a4'];

    $this->fields_value['envialia_imp_tar_impo'] = $confPrev['envialia_imp_tar_impo'];
    $this->fields_value['envialia_imp_tar_peso'] = $confPrev['envialia_imp_tar_peso'];

    $helper = new HelperForm();
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

    $helper->languages = $this->context->controller->_languages;
    $helper->default_form_language = $this->context->controller->default_form_language;
    $helper->allow_employee_form_lang = $this->context->controller->default_form_language;
    $helper->toolbar_scroll = false;
    $helper->show_toolbar = false;
    $helper->show_cancel_button = true;

    $helper->fields_value = $this->fields_value;
    $this->_html .= $helper->generateForm($this->fields_form);

    return;
  }

}

?>