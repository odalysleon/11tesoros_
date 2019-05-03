<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Alcides Rodriguez <alcidesrh@gmail.com.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Homecategoriesimages extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'homecategoriesimages';
        $this->author = 'alcidesrh';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Despliega las imagenes de categorias en la pagina de inicio');
        $this->description = $this->trans('Despliega las imagenes de categorias en la pagina de inicio');

        $this->templateFile = 'module:homecategoriesimages/views/templates/hook/homecategoriesimages.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        return parent::install()
            && $this->registerHook('categoryUpdate')
        ;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function hookCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('homecategoriesimages'))) {

            $category = new Category((int) (int) Context::getContext()->shop->getCategory());
            $categories = $category->getSubCategories($this->context->language->id);
            unset($categories[count($categories) - 1]);
            $categories2 = $categories;
            foreach ($categories2 as $category){
                $childrens = Category::getChildren((int)$category['id_category'], (int)$this->context->language->id, true, (int)$this->context->shop->id);
                foreach ($childrens as $children){
                    if($children['id_image2'])$categories[] = $children;
                }
            }
            if (empty($categories)) {
                return false;
            }
            $this->smarty->assign(['categories' => $categories]);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('homecategoriesimages'));
    }
//Esta función hay que mantenerla sino ad un error
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {

    }
}
