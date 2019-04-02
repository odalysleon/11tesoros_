<?php

/**
 * Controlador AdmEnvialiaTarifaPesosController
 * Referente a pestaña ENVIALIA tarifa pesos
 * @author      miguel.cejas
 * @date        01/02/2017
 */
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/clases/EnvialiaEstado.php');
require_once(_PS_MODULE_DIR_ . 'envialiacarrier/lib/dinalib.php');

class AdminEnvialiaEstadoController extends ModuleAdminController {

  public function __construct() {

    $this->bootstrap = true;
    $this->table = 'envialia_estado';
    $this->className = 'EnvialiaEstado';
    $this->module = 'envialia_estado';
    $this->identifier = 'id_envialia_estado';
    $this->allow_export = true;

    $this->addRowAction('edit');
    $this->addRowAction('delete');

    $id_lenguage = 1;
    if ($id_lenguage == '') {
      $id_lenguage = 1;
    }

    $this->_select = 'osl.name as id_order_state, a.V_COD_TIPO_EST';
    $this->_join = 'JOIN ' . _DB_PREFIX_ . 'order_state_lang osl ON osl.id_order_state = a.id_order_state ';
    $this->_where = ' AND osl.id_lang = ' . $id_lenguage;
    $this->_defaultOrderBy = 'a.id_envialia_estado';


    parent::__construct();

    $this->fields_list = array(
        'id_order_state' => array(
            'title' => $this->l('Estado'),
            'align' => 'center',
            'width' => 'auto',
            'filter_key' => 'osl!name',
            'order_key' => 'name'),
        'V_COD_TIPO_EST' => array(
            'title' => $this->l('Estado envialia'),
            'align' => 'center'),
    );

    $this->bulk_actions = array(
        'delete' => array(
            'text' => $this->l('Eliminar equivalencia'),
            'icon' => 'icon-trash text-danger',
            'confirm' => $this->l('¿Eliminar equivalencias seleccionadas?')
        ),
    );

    $this->fields_form = array(
        'legend' => array(
            'title' => $this->trans('Nueva equivalencia de estados', array(), 'Admin.OrdersCustomers.Feature'),
            'icon' => 'icon-random'
        ),
        'input' => array(
            array(
                'type' => 'select',
                'label' => $this->trans('Estado', array(), 'Admin.Global'),
                'name' => 'id_order_state',
                'options' => array(
                    'query' => OrderState::getOrderStates($this->context->language->id),
                    'id' => 'id_order_state',
                    'name' => 'name'
                ),
                'required' => true,
                'hint' => $this->trans('Estado.', array(), 'Admin.OrdersCustomers.Help')
            ),
            array(
                'type' => 'text',
                'label' => $this->trans('Codigo estado envialia', array(), 'Admin.OrdersCustomers.Feature'),
                'name' => 'V_COD_TIPO_EST',
                'prefix' => '<i class="icon icon-random"></i>',
                'required' => true,
                'size' => 4,
                'col' => 1,
            ),
        ),
        'submit' => array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        ),
    );
  }

  public function initPageHeaderToolbar() {
    parent::initPageHeaderToolbar();
    if (empty($this->display)) {
      $this->page_header_toolbar_btn['new_estado'] = array(
          'href' => self::$currentIndex . '&addenvialia_estado&token=' . $this->token,
          'desc' => $this->trans('Agregar equivalencia de estado', array(), 'Admin.OrdersCustomers.Feature'),
          'icon' => 'process-icon-new'
      );
    }

    parent::initPageHeaderToolbar();
  }

  public function processSave() {

    $boHayError = false;
    $boModoInsert = true;

    if (Tools::getValue('id_envialia_estado') <> '') {
      $boModoInsert = false;
    }

    if (Tools::getValue('submitFormAjax')) {
      $this->redirect_after = false;
    }

    if (!Tools::getValue('id_order_state')) {
      $boHayError = true;
      $this->errors[] = $this->l('El estado es obligatorio.');
    }

    if ($boHayError) {
      return false;
    } else if ($boModoInsert) {

      $query = 'select id_order_state from ' . _DB_PREFIX_ . 'envialia_estado where id_order_state = ' . Tools::getValue('id_order_state') .
              ' or V_COD_TIPO_EST = "' . Tools::getValue('V_COD_TIPO_EST') . '"';

      $resultado = Db::getInstance()->executeS($query);
      if ($resultado) {
        $this->errors[] = $this->trans('Estados duplicados.', array(), 'Admin.OrdersCustomers.Notification');
      } else {
        $values = array('id_order_state' => (int) Tools::getValue('id_order_state'),
            'V_COD_TIPO_EST' => Tools::getValue('V_COD_TIPO_EST'));

        return Db::getInstance()->insert('envialia_estado', $values);
      }
    } else if (!$boModoInsert) {
      $query = 'select id_order_state from ' . _DB_PREFIX_ . 'envialia_estado where id_order_state = ' . Tools::getValue('id_order_state') .
              ' or V_COD_TIPO_EST = "' . Tools::getValue('V_COD_TIPO_EST') . '"';

      $resultado = Db::getInstance()->executeS($query);

      if (count($resultado) > 1) {
        $this->errors[] = $this->trans('No se puede modificar ya que existe otro registro para los datos introducidos.', array(), 'Admin.OrdersCustomers.Notification');
      } else {
        $values = array('id_order_state' => (int) Tools::getValue('id_order_state'),
            'V_COD_TIPO_EST' => Tools::getValue('V_COD_TIPO_EST'));

        Db::getInstance()->update('envialia_estado', $values, '`id_envialia_estado` = ' . Tools::getValue('id_envialia_estado'));
      }
    }
    return false;
  }

}
