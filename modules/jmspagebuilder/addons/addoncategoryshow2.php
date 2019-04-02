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
include_once(_PS_MODULE_DIR_.'jmspagebuilder/addons/addonbase.php');
include_once(_PS_MODULE_DIR_.'jmspagebuilder/classes/productHelper.php');
class JmsAddonCategoryShow2 extends JmsAddonBase
{
    public function __construct()
    {
        $this->addonname = 'categoryshow2';
        $this->modulename = 'jmspagebuilder';
        $this->addontitle = 'Show Category 2';
        $this->addondesc = 'Show Categories In Shop';
        $this->overwrite_tpl = '';
        $this->context = Context::getContext();

    }
    public function getInputs()
    {
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'lang' => '1',
                'desc' => 'Enter text which will be used as addon title. Leave blank if no title is needed.',
                'default' => 'Our Products'
            ),
            array(
                'type' => 'categories',
                'name' => 'ctcategories',
                'label' => $this->l('Categories'),
                'lang' => '0',
                'desc' => 'Select Categories to Show',
                'default' => '',
                'usecheckbox' => '1'
            ),
            array(
                'type' => 'text',
                'name' => 'overwrite_tpl',
                'label' => $this->l('Overwrite Tpl File'),
                'lang' => '0',
                'desc' => 'Use When you want overwrite template file'
            )
        );
        return $inputs;
    }
    public function returnValue($addon)
    {
        $id_lang = $this->context->language->id;
        if (Tools::strlen($addon->fields[1]->value) == 0) {
            return "Please select categories to show!";
        }
        $category_ids = explode(",", $addon->fields[1]->value);
        $categories = array();
        $img_cat_dir=$this->root_url.'/img/c/';
        foreach ($category_ids as $k => $id_category) {
            $category = new Category($id_category, (int)Context::getContext()->language->id);
            $categories[$k]['id_category'] = $id_category;
            $categories[$k]['name'] = $category->name;
            $categories[$k]['link_rewrite'] = $category->link_rewrite;
            if ($id_category != 1) {
                $result_product_count = Db::getInstance()->ExecuteS('
                SELECT COUNT(ac.`id_product`) as totalProducts
                FROM `'._DB_PREFIX_.'category_product` ac
                LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = ac.`id_product`
                WHERE ac.`id_category` = '.$id_category.' AND p.`active` = 1');
                $categories[$k]['product_count'] = $result_product_count[0]['totalProducts'];
            }
        }        
        $addon_tpl_dir = $this->loadTplDir();
        $this->context->smarty->assign(array(
                'link' => $this->context->link,
                'categories' => $categories,
                'addon_title' => $addon->fields[0]->value->$id_lang,
                'img_cat_dir' => $img_cat_dir,
                'addon_tpl_dir' => $addon_tpl_dir
        ));
        $this->overwrite_tpl = $addon->fields[count($addon->fields)-1]->value;
        $template_path = $this->loadTplPath();
        return $this->context->smarty->fetch($template_path);
    }
}
