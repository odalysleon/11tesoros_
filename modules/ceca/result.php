<?php
/**
 * 2011-2017 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2016 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(_PS_ROOT_DIR_.'/header.php');
include(dirname(__FILE__).'/ceca.php');
//include(dirname(__FILE__).'../../classes/Order.php');

$ceca = new Ceca();

$result = (int)(Tools::getValue('result'));
$cartId = (int)(Tools::getValue('cartId'));

// Display all and exit
$orderLink = "";

if($result == 0){

	$orderId = Order::getOrderByCartId($cartId);
	$order = new Order($orderId);
	$customer = new Customer($order->id_customer);
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cartId.'&id_module='.$ceca->id.'&id_order='.$orderId.'&key='.$customer->secure_key);

}
else{

	$order = new Order();
	$orderId = $order->getOrderByCartId($cartId);

	if(false)
		$smarty = new Smarty();

	$smarty->assign(array(
		'order_id' => $orderId,
		'link' => new Link(),
		'module_dir' => _MODULE_DIR_.$ceca->name,
		'clear_cart' => (int)Configuration::get('CECA_CLEAR_CART')
	));

	$smarty->display(_PS_MODULE_DIR_.$ceca->name.'/views/templates/front/resultErr.tpl');

}

// Display all and exit
	include(_PS_ROOT_DIR_.'/footer.php');