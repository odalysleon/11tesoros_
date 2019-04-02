<?php
/**
* 2007-2014 PrestaShop
*
* Jms Blog
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

/**
 * @since 1.5.0
 */
include_once(_PS_MODULE_DIR_.'jmsblog/JmsBlogItem.php');
class JMSBlogItemModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();	
		$item_id 	= (int)Tools::getValue('item_id');				
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'jmsblog_items` SET views = views+1 WHERE item_id = '.$item_id);						 		
		$item 		= $this->getItem($item_id);				
		$item 		= $item[0]; 	
		$cat 	= $this->getCat($item['cat_id']);
		$this->context->controller->addCSS($this->module->getPathUri().'css/style.css', 'all');				
		$this->context->smarty->assign(array(
			'item' => $item,
			'cat' => $cat[0],
			'image_baseurl' => $this->module->getPathUri().'img/'
		));
		$this->setTemplate('item.tpl');
	}
	public function getCat($cat_id = 0)
	{
		$this->context = Context::getContext();		
		$id_lang = $this->context->language->id;		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`cat_id` as cat_id, hssl.`title` 			
			FROM '._DB_PREFIX_.'jmsblog_cats hss			
			LEFT JOIN '._DB_PREFIX_.'jmsblog_cats_lang hssl ON (hss.cat_id = hssl.cat_id)
			WHERE hssl.id_lang = '.(int)$id_lang.
			' AND hss.`cat_id` = '.$cat_id.' 
			ORDER BY hss.ordering'
		);
	}
	public function getItem($item_id = 0)
	{
		$this->context = Context::getContext();		
		$id_lang = $this->context->language->id;		
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hss.`item_id` as id_item, hssl.`image`,hss.`cat_id`, hss.`ordering`, hss.`active`, hssl.`title`, hss.`created`, hss.`modified`, hss.`views` ,
			hssl.`alias`,hssl.`fulltext`,hssl.`introtext`,hssl.`meta_desc`,hssl.`meta_key`,hssl.`key_ref`
			FROM '._DB_PREFIX_.'jmsblog_items hss			
			LEFT JOIN '._DB_PREFIX_.'jmsblog_items_lang hssl ON (hss.item_id = hssl.item_id)
			WHERE hssl.id_lang = '.(int)$id_lang.
			' AND hss.`item_id` = '.$item_id.' 
			ORDER BY hss.ordering'
		);
	}
}
