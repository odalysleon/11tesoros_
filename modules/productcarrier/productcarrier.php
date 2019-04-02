<?php
/**
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
 * @package   Productcarrier
 * @author    Paul MORA
 * @copyright Copyright (c) 2011-2017 EURL ébewè - www.ebewe.net - Paul MORA
 * @license   Commercial license
 * Support by mail  :  contact@ebewe.net
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Productcarrier extends Module
{
    public function __construct()
    {
        $this->name = 'productcarrier';
        $this->tab = 'shipping_logistics';
        $this->version = '2.1.5';
        $this->author = 'ébewè';
        $this->module_key = 'a2a506e5771acdd82ba180c607567ceb';
        parent::__construct();
        $this->displayName = $this->l('Product Carrier');
        $this->description = $this->l('Select available carriers for each product');
    }

    /**
    * Install Module
    *
    **/
    public function install()
    {
        if (!parent::install()
            || !$this->installModuleTab(
                'AdminProductCarrier',
                array((int)(Configuration::get('PS_LANG_DEFAULT'))=>'Product Carrier'),
                'AdminParentShipping'
            )
            || !$this->registerHook('backOfficeHeader')
        ) {
            return false;
        }
        return true;
    }

    /**
    * Uninstall Module
    *
    **/
    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallModuleTab('AdminProductCarrier')
        ) {
            return false;
        }
        return true;
    }

    /**
    * install Tab
    *
    * @param mixed $tabClass
    * @param mixed $tabName
    * @param mixed $idTabParent
    * @return bool $pass
    */
    private function installModuleTab($tabClass, $tabName, $idTabParent)
    {
        $idTab = Tab::getIdFromClassName($idTabParent);
        $pass = true ;
        @copy(_PS_MODULE_DIR_.$this->name.'/logo.gif', _PS_IMG_DIR_.'t/'.$tabClass.'.gif');
        $tab = new Tab();
        $tab->name = $tabName;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTab;
        $pass = $tab->save();

        return($pass);
    }

    /**
    * uninstall Tab
    *
    * @param mixed $tabClass
    * @return bool $pass
    */
    private function uninstallModuleTab($tabClass)
    {
        $pass = true ;
        @unlink(_PS_IMG_DIR_.'t/'.$tabClass.'.gif');
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $pass = $tab->delete();
        }
        return($pass);
    }


    /**
     * Admin page
     */
    public function getContent()
    {
        Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminProductCarrier'));
    }

    /**
     * Add JavaScript file in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminProductCarrier') {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'views/js/back.js');
        }
    }
}
