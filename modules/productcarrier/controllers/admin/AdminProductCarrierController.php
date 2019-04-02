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

class AdminProductCarrierController extends ModuleAdminController
{
    public $module = 'productcarrier';
    public $bootstrap = true;

    protected $_category;
    protected $id_current_category;

    public function __construct()
    {
        $this->table = 'product';
        $this->className = 'Product';
        $this->lang = true;
        $this->noLink = true;
        $this->delete = false;
        $this->view = false;
        $this->controller_type = 'admin';
        $this->context = Context::getContext();

        parent::__construct();

        if (Tools::getValue('reset_filter_category')) {
            $this->context->cookie->id_category_product_carrier_filter = false;
        }
        if (Shop::isFeatureActive() && $this->context->cookie->id_category_product_carrier_filter) {
            $category = new Category((int)$this->context->cookie->id_category_product_carrier_filter);
            if (!$category->inShop()) {
                $this->context->cookie->id_category_product_carrier_filter = false;
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductCarrier'));
            }
        }
        if ($id_category = (int)Tools::getValue('id_category')) {
            $this->id_current_category = $id_category;
            $this->context->cookie->id_category_product_carrier_filter = $id_category;
        } elseif ($id_category = $this->context->cookie->id_category_product_carrier_filter) {
            $this->id_current_category = $id_category;
        }
        if ($this->id_current_category) {
            $this->_category = new Category((int)$this->id_current_category);
        } else {
            $this->_category = new Category();
        }

        $carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);
        $last_carrier = end($carriers);

        $this->_carriers = '';
        $this->_carriers_reference = '';
        foreach ($carriers as $carrier) {
            $this->_carriers .= $carrier['id_carrier'].',';
            $this->_carriers_reference .= $carrier['id_reference'];
            if ($last_carrier['id_carrier'] != $carrier['id_carrier']) {
                $this->_carriers_reference .= ',';
            }
        }

        $this->deleteUnavailableCarriers();

        $id_category = Tools::getIsset('id_category') ? abs((int)(Tools::getValue('id_category'))) : '';
        $this->fieldsDisplay = array(
            'id_product' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 20),
            'image' => array('title' => $this->l('Image'), 'align' => 'center', 'image' => 'p', 'width' => 45,
                'orderby' => false, 'filter' => false, 'search' => false),
            'name' => array('title' => $this->l('Name'), 'filter_key' => 'b!name'),
            'price_final' => array('title' => $this->l('Price'), 'width' => 70, 'price' => true, 'align' => 'right',
                'havingFilter' => true, 'orderby' => false),
            'a!active' => array('title' => $this->l('Status'), 'active' => 'status', 'filter_key' => 'a!active',
                'align' => 'center', 'type' => 'bool', 'orderby' => false)
        );

        $id_shop = Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? (int)$this->context->shop->id
            : 'a.id_shop_default';
        $this->_join = ' JOIN `'._DB_PREFIX_.'product_shop` sa ON (a.`id_product` = sa.`id_product`
            AND sa.id_shop = '.$id_shop.')
            LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = a.`id_product` AND i.`cover` = 1)';
        $this->_select = 'a.`id_product` AS product, i.`id_image`, a.`price` AS price_final';
        $this->_group = 'GROUP BY a.`id_product`';

        if (!empty($id_category)) {
            $this->_join .= ' INNER JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_product` = a.`id_product` AND
                cp.`id_category` = '.(int)$id_category.') ';
            $this->_select .= ' , cp.`position`, ';
        }

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->module->l('ID', 'AdminProductCarrierController'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        $this->fields_list['reference'] = array(
            'title' => $this->module->l('Reference', 'AdminProductCarrierController'),
            'align' => 'center',
            'class' => 'fixed-width-xs'
        );
        $this->fields_list['active'] = array(
            'title' => $this->module->l('Status', 'AdminProductCarrierController'),
            'active' => 'status',
            'filter_key' => 'a!active',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false
        );
        $this->fields_list['price_final'] = array(
            'title' => $this->module->l('Price', 'AdminProductCarrierController'),
            'type' => 'price',
            'align' => 'text-right',
            'havingFilter' => true,
            'search' => false
        );
        $this->fields_list['image'] = array(
            'title' => $this->module->l('Image', 'AdminProductCarrierController'),
            'align' => 'center',
            'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['name'] = array(
            'title' => $this->module->l('Name', 'AdminProductCarrierController'),
            'filter_key' => 'b!name',
            'class' => 'product_name'
        );
        foreach ($carriers as $ind => $carrier) {
            $this->fields_list['carrier'.$carrier['id_carrier']] = array(
                'title' => $carrier['name'],
                'align' => 'center carrier_'.$carrier['id_carrier'],
                'type' => 'checkbox',
                'orderby' => false,
                'all_carriers' => $this->_carriers,
                'carrier' => $carrier['id_carrier'],
                'search' => false,
            );
            if ($ind == 0) {
                $this->fields_list['carrier'.$carrier['id_carrier']]['ind'] = true;
            }
        }
    }

    /**
     * Empty Toolbar
     */
    public function initToolbar()
    {
    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->l('Bad SQL query', 'Helper').'<br />'.htmlspecialchars($this->_list_error));
            return false;
        }

        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    /**
     * Function used to render the sort by category
     */
    public function initContent()
    {
        if ($id_category = (int)$this->id_current_category) {
            self::$currentIndex .= '&id_category='.(int)$this->id_current_category;
        }

        // If products from all categories are displayed, we don't want to use sorting by position
        if (!$id_category) {
            $this->_defaultOrderBy = $this->identifier;
            if ($this->context->cookie->{$this->table.'Orderby'} == 'position') {
                unset($this->context->cookie->{$this->table.'Orderby'});
                unset($this->context->cookie->{$this->table.'Orderway'});
            }
        }
        if (!$id_category) {
            $id_category = 1;
        }
        $this->tpl_list_vars['is_category_filter'] = (bool)$this->id_current_category;

        // Generate category selection tree
        $tree = new HelperTreeCategories('categories-tree', $this->l('Filter by category'));
        $tree->setAttribute('is_category_filter', (bool)$this->id_current_category)
            ->setAttribute(
                'base_url',
                preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token
            )
            ->setInputName('id-category')
            ->setRootCategory(Configuration::get('PS_ROOT_CATEGORY'))
            ->setSelectedCategories(array((int)$id_category));
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            $tree->setFullTree(1);
        }
        $this->tpl_list_vars['category_tree'] = $tree->render();

        // used to build the new url when changing category
        $this->tpl_list_vars['base_url'] =
            preg_replace('#&id_category=[0-9]*#', '', self::$currentIndex).'&token='.$this->token;

        // hidden input with all carriers
        $this->tpl_list_vars['all_carriers'] = $this->_carriers;

        parent::initContent();
    }

    public function run()
    {
        if (Tools::getValue('id_product') && Tools::getValue('carriers')) {
            $carriers = array_unique(array_filter(explode(',', Tools::getValue('carriers'))));
            $this->setCarriers($carriers, Tools::getValue('id_product'));
            echo Tools::jsonEncode($carriers);
        } elseif (Tools::getValue('products') && Tools::getValue('id_carrier') && Tools::getValue('check')) {
            $products = array_unique(array_filter(explode(',', Tools::getValue('products'))));
            $this->enableCarrier($products, Tools::getValue('id_carrier'), Tools::getValue('check'));
            echo Tools::jsonEncode($products);
        } else {
            parent::run();
        }
    }

    /**
     * Delete all configuration for unavailable carriers
     */
    public function deleteUnavailableCarriers()
    {
        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'product_carrier`
                WHERE id_carrier_reference NOT IN ('.(string)$this->_carriers_reference.')
                AND id_shop IN ('.implode(',', Shop::getContextListShopID()).')'
        );
    }

    /**
     * Enables/Disables carriers assigned to the product
     */
    public function enableCarrier($products, $id_carrier, $check)
    {
        $id_carrier_reference = Db::getInstance()->getValue('SELECT SQL_CALC_FOUND_ROWS c.id_reference FROM
            `'._DB_PREFIX_.'carrier` c WHERE c.`id_carrier` = '.$id_carrier);
        $data = array();

        $carriers_reference = array_unique(array_filter(explode(',', $this->_carriers_reference)));

        if (is_array($shops = Shop::getContextListShopID())) {
            $shop_sql = 'IN ('.implode(',', Shop::getContextListShopID()).')';
        } else {
            $shop_sql = '= '.(int)$shops;
        }

        foreach ($products as $id_product) {
            $already = Db::getInstance()->getValue('SELECT SQL_CALC_FOUND_ROWS pc.id_product FROM
                `'._DB_PREFIX_.'product_carrier` pc WHERE pc.`id_product` = '.$id_product.'
                AND id_shop '.(string)$shop_sql);
            if (empty($already) && $check == 'false') {
                $data_temp = array();
                foreach ($carriers_reference as $id_carrier_reference_temp) {
                    if (is_array($shops = Shop::getContextListShopID())) {
                        foreach ($shops as $shop) {
                            $data_temp[] = array(
                                'id_product' => (int)$id_product,
                                'id_carrier_reference' => (int)$id_carrier_reference_temp,
                                'id_shop' => (int)$shop
                            );
                        }
                    } else {
                        $data_temp[] = array(
                            'id_product' => (int)$id_product,
                            'id_carrier_reference' => (int)$id_carrier_reference_temp,
                            'id_shop' => (int)$shops
                        );
                    }
                }
                $uniqueArray_temp = array();
                foreach ($data_temp as $subArray_temp) {
                    if (!in_array($subArray_temp, $uniqueArray_temp)) {
                        $uniqueArray_temp[] = $subArray_temp;
                    }
                }

                if (is_array($uniqueArray_temp) && !empty($uniqueArray_temp)) {
                    Db::getInstance()->insert('product_carrier', $uniqueArray_temp, false, true, Db::INSERT_IGNORE);
                }
            }

            if (!empty($already) || (empty($already) && $check == 'false')) {
                if (is_array($shops = Shop::getContextListShopID())) {
                    foreach ($shops as $shop) {
                        $data[] = array(
                            'id_product' => (int)$id_product,
                            'id_carrier_reference' => (int)$id_carrier_reference,
                            'id_shop' => (int)$shop
                        );
                    }
                } else {
                    $data[] = array(
                        'id_product' => (int)$id_product,
                        'id_carrier_reference' => (int)$id_carrier_reference,
                        'id_shop' => (int)$shops
                    );
                }
            }

            Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'product_carrier`
                WHERE id_product = '.(int)$id_product.'
                AND id_carrier_reference = '.(int)$id_carrier_reference.'
                AND id_shop '.(string)$shop_sql
            );
        }

        if ($check == 'true') {
            $uniqueArray = array();
            foreach ($data as $subArray) {
                if (!in_array($subArray, $uniqueArray)) {
                    $uniqueArray[] = $subArray;
                }
            }

            if (is_array($uniqueArray) && !empty($uniqueArray)) {
                Db::getInstance()->insert('product_carrier', $uniqueArray, false, true, Db::INSERT_IGNORE);
            }
        }
    }

    /**
     * Sets carriers assigned to the product
     */
    public function setCarriers($carrier_list, $id_product)
    {
        $data = array();

        foreach ($carrier_list as $carrier) {
            $id_carrier_reference = Db::getInstance()->getValue('SELECT SQL_CALC_FOUND_ROWS c.id_reference FROM
                `'._DB_PREFIX_.'carrier` c WHERE c.`id_carrier` = '.$carrier);
            if (is_array($shops = Shop::getContextListShopID())) {
                foreach ($shops as $shop) {
                    $data[] = array(
                        'id_product' => (int)$id_product,
                        'id_carrier_reference' => (int)$id_carrier_reference,
                        'id_shop' => (int)$shop
                    );
                }
                $shop_sql = 'IN ('.implode(',', Shop::getContextListShopID()).')';
            } else {
                $data[] = array(
                    'id_product' => (int)$id_product,
                    'id_carrier_reference' => (int)$id_carrier_reference,
                    'id_shop' => (int)$shops
                );
                $shop_sql = '= '.(int)$shops;
            }
        }
        Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'product_carrier`
            WHERE id_product = '.(int)$id_product.'
            AND id_shop '.(string)$shop_sql
        );

        $uniqueArray = array();
        foreach ($data as $subArray) {
            if (!in_array($subArray, $uniqueArray)) {
                $uniqueArray[] = $subArray;
            }
        }

        if (is_array($uniqueArray) && !empty($uniqueArray)) {
            Db::getInstance()->insert('product_carrier', $uniqueArray, false, true, Db::INSERT_IGNORE);
        }
    }

    /**
     * Function used to get Carriers available for Products
     */
    public static function getProductsCarriers($id_product)
    {
        $product = new Product((int)$id_product);
        $carriers = $product->getCarriers();

        $products_carriers = array();
        foreach ($carriers as $result) {
            $products_carriers[$id_product][] = $result['id_carrier'];
            $products_carriers[$id_product] =
                array_unique(array_filter($products_carriers[$id_product]));
        }

        return $products_carriers;
    }
}
