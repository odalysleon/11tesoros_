<?php
/**
* 2007-2017 PrestaShop
*
* Jms Page Builder
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2017 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once(_PS_MODULE_DIR_.'jmspagebuilder/classes/jmsHelper.php');
include_once(_PS_MODULE_DIR_.'jmspagebuilder/params.php');
class AdminJmspagebuilderSettingController extends ModuleAdminController
{
    private $_themeskins = array();
    private $_producthovers = array();
    public function __construct()
    {
        $this->name = 'jmspagebuilder';
        $this->tab = 'front_office_features';
        $this->bootstrap = true;
        $this->lang = true;
        $this->context = Context::getContext();
        $this->secure_key = Tools::encrypt($this->name);
        $_controller = Tools::getValue('controller');
        $this->classname = $_controller;
        parent::__construct();
        if (_JPB_THEMESKINS_) {
            $this->_themeskins = explode(",", _JPB_THEMESKINS_);
        }
        if (_JPB_PRODUCTHOVERS_) {
            $hover_strs = explode(",", _JPB_PRODUCTHOVERS_);
            foreach ($hover_strs as $hover_str) {
                $_fields = explode(":", $hover_str);
                $this->_producthovers[$_fields[0]] = $_fields[1];
            }
        }
        $this->root_url = JmsPageBuilderHelper::getRootUrl();
    }
    public function renderList()
    {
        $this->_html = '';
        /* Validate & process */
        if (Tools::isSubmit('submitSetting')) {
            if ($this->_postValidation()) {
                $this->_postProcess();
            }
        } else {
            $this->_html .= $this->renderSettingForm();
        }
        return $this->_html;
    }

    private function _postValidation()
    {
        return true;
    }

    private function _postProcess()
    {
        $errors = array();
        /* Processes*/
        if (Tools::isSubmit('submitSetting')) {
            $res = Configuration::updateValue('JPB_SKIN', Tools::getValue('JPB_SKIN'));
            $res &= Configuration::updateValue('JPB_HOMEPAGE', (int)(Tools::getValue('JPB_HOMEPAGE')));
            $res &= Configuration::updateValue('JPB_PRODUCTHOVER', Tools::getValue('JPB_PRODUCTHOVER'));
            $res &= Configuration::updateValue('JPB_PRODUCTCOLOR', (int)Tools::getValue('JPB_PRODUCTCOLOR'));
            $res &= Configuration::updateValue('JPB_RTL', (int)(Tools::getValue('JPB_RTL')));
            $res &= Configuration::updateValue('JPB_SETTINGPANEL', (int)(Tools::getValue('JPB_SETTINGPANEL')));
            $res &= Configuration::updateValue('JPB_JCAROUSEL', (int)(Tools::getValue('JPB_JCAROUSEL')));
            $res &= Configuration::updateValue('JPB_AWESOME', (int)(Tools::getValue('JPB_AWESOME')));
            $res &= Configuration::updateValue('JPB_ANIMATE', (int)(Tools::getValue('JPB_ANIMATE')));
            $res &= Configuration::updateValue('JPB_COUNTDOWN', (int)(Tools::getValue('JPB_COUNTDOWN')));
            $res &= Configuration::updateValue('JPB_MOBILEMENU', (int)(Tools::getValue('JPB_MOBILEMENU')));
            $res &= Configuration::updateValue('JPB_CONVERTURL', (int)(Tools::getValue('JPB_CONVERTURL')));
            $res &= Configuration::updateValue('JPB_GRID', (int)(Tools::getValue('JPB_GRID')));

        }
        if (!$res) {
            $errors[] = $this->displayError($this->l('The configuration could not be updated.'));
        } else {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminJmspagebuilderSetting', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
        }
    }

    public function renderSettingForm()
    {
        $this->context->controller->addCSS(_MODULE_DIR_.$this->module->name.'/views/css/setting.css', 'all');
        $homepages = JmsPageBuilderHelper::getHomePages('1');
        $_phovers = array();
        foreach ($this->_producthovers as $phkey => $phvalue) {
            $_phovers[] = array('id' => $phkey, 'name' => $phvalue);
        }
        $_tskins = array();
        foreach ($this->_themeskins as $tsvalue) {
            $_tskins[] = array('id' => $tsvalue, 'name' => $tsvalue);
        }
        $_layouts = array();
        $_layouts[] = array('id' => 'leftcolumn', 'name' => 'Left Column');
        $_layouts[] = array('id' => 'rightcolumn', 'name' => 'Right Column');
        $_layouts[] = array('id' => 'withoutcolumn', 'name' => 'Without Column');
        $input_arr = array();
        if (count($this->_themeskins) > 0) {
            $input_arr[] =  array(
                'type' => 'skin',
                'label' => $this->l('Theme Skin'),
                'name' => 'JPB_SKIN',
                'skins' => $this->_themeskins,
                'jpb_skin' => configuration::get('JPB_SKIN'),
                'themename' => _THEME_NAME_,
                'img_url' => $this->root_url.'themes/'._THEME_NAME_.'/skin-icons/',
                'tab' => 'general'
            );
        }
        $input_arr[] =  array(
                'type' => 'select',
                'label' => $this->l('Home Page'),
                'name' => 'JPB_HOMEPAGE',
                'options' => array('query' => $homepages,'id' => 'id_homepage','name' => 'title'),
                'tab' => 'general'
            );
        if (count($_phovers) > 0) {
            $input_arr[] =  array(
                'type' => 'select',
                'label' => $this->l('Product Image Hover'),
                'name' => 'JPB_PRODUCTHOVER',
                'options' => array('query' => $_phovers,'id' => 'id','name' => 'name'),
                'tab' => 'general'
            );
        }
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Product Color Pick'),
                'name' => 'JPB_PRODUCTCOLOR',
                'desc' => $this->l('Product Color Pick : Show color list to pick on product box.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'general'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Mobile Menu'),
                'name' => 'JPB_MOBILEMENU',
                'desc' => $this->l('Mobile Menu : Off-Canvas effect for Mobile menu.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'general'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('RTL'),
                'name' => 'JPB_RTL',
                'desc' => $this->l('Direction : Right to Left.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'general'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Setting Panel'),
                'name' => 'JPB_SETTINGPANEL',
                'desc' => $this->l('Show or Hide setting panel on front.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'general'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Grid View'),
                'name' => 'JPB_GRID',
                'desc' => $this->l('Grid or Listing View.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'layout'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Load Jcarousel'),
                'name' => 'JPB_JCAROUSEL',
                'desc' => $this->l('Enable/Disable Jcarousel Library.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'library'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Load Awesome Icon'),
                'name' => 'JPB_AWESOME',
                'desc' => $this->l('Enable/Disable Awesome Icon Library.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'library'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Load Animate Css'),
                'name' => 'JPB_ANIMATE',
                'desc' => $this->l('Enable/Disable Animate Css Library.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'library'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Load Countdown'),
                'name' => 'JPB_COUNTDOWN',
                'desc' => $this->l('Enable/Disable Countdown Library.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'library'
            );
        $input_arr[] =  array(
                'type' => 'switch',
                'label' => $this->l('Editor Auto Convert URL'),
                'name' => 'JPB_CONVERTURL',
                'desc' => $this->l('Enable/Disable Auto Convert URL. Auto add site url to image src.'),
                'values'    => array(
                    array('id'    => 'active_on','value' => 1,'label' => $this->l('Enabled')),
                    array('id'    => 'active_off','value' => 0,'label' => $this->l('Disabled'))
                ),
                'tab' => 'library'
            );
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Setting'),
                'icon' => 'icon-cogs'
            ),
            'tabs' => array('general' => 'General', 'layout' => 'Listing Layout', 'library' => 'Library'),
            'input' => $input_arr,
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitSetting'
            )
        );
        $this->tpl_folder = 'form/';
        $this->fields_value = $this->getConfigFieldsValues();
        return adminController::renderForm();
    }
    public function getConfigFieldsValues()
    {
        return array(
            'JPB_SKIN' => Tools::getValue('JPB_SKIN', Configuration::get('JPB_SKIN')),
            'JPB_HOMEPAGE' => Tools::getValue('JPB_HOMEPAGE', Configuration::get('JPB_HOMEPAGE')),
            'JPB_PRODUCTHOVER' => Tools::getValue('JPB_PRODUCTHOVER', Configuration::get('JPB_PRODUCTHOVER')),
            'JPB_PRODUCTCOLOR' => Tools::getValue('JPB_PRODUCTCOLOR', Configuration::get('JPB_PRODUCTCOLOR')),
            'JPB_RTL' => Tools::getValue('JPB_RTL', Configuration::get('JPB_RTL')),
            'JPB_SETTINGPANEL' => Tools::getValue('JPB_SETTINGPANEL', Configuration::get('JPB_SETTINGPANEL')),
            'JPB_JCAROUSEL' => Tools::getValue('JPB_JCAROUSEL', Configuration::get('JPB_JCAROUSEL')),
            'JPB_AWESOME' => Tools::getValue('JPB_AWESOME', Configuration::get('JPB_AWESOME')),
            'JPB_ANIMATE' => Tools::getValue('JPB_ANIMATE', Configuration::get('JPB_ANIMATE')),
            'JPB_COUNTDOWN' => Tools::getValue('JPB_COUNTDOWN', Configuration::get('JPB_COUNTDOWN')),
            'JPB_MOBILEMENU' => Tools::getValue('JPB_MOBILEMENU', Configuration::get('JPB_MOBILEMENU')),
            'JPB_CONVERTURL' => Tools::getValue('JPB_CONVERTURL', Configuration::get('JPB_CONVERTURL')),
            'JPB_PRODUCTLAYOUT' => Tools::getValue('JPB_PRODUCTLAYOUT', Configuration::get('JPB_PRODUCTLAYOUT')),
            'JPB_LISTINGLAYOUT' => Tools::getValue('JPB_LISTINGLAYOUT', Configuration::get('JPB_LISTINGLAYOUT')),
            'JPB_BLOGLAYOUT' => Tools::getValue('JPB_BLOGLAYOUT', Configuration::get('JPB_BLOGLAYOUT')),
            'JPB_GRID' => Tools::getValue('JPB_GRID', Configuration::get('JPB_GRID'))
        );
    }
}
