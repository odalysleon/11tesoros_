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

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(_PS_ROOT_DIR_.'/header.php');
include(dirname(__FILE__).'/ceca.php');

$ceca = new Ceca();

if(false)
	$smarty = new Smarty();

if(false)
	$cookie = new Cookie();

$cart = new Cart((int)($cookie->id_cart));
$idCart = (int)($cookie->id_cart);
$products = $cart->getProducts();
$locale = Language::getIsoById($cookie->id_lang);
$sandbox_mode = Configuration::get('CECA_SANDBOX');

if($sandbox_mode == '1')
	$url_tpvv = Configuration::get('CECA_URL_TPVV_SIMULADOR');
else
	$url_tpvv = Configuration::get('CECA_URL_TPVV_PROD');

$cartProducts = "";
foreach ($products as $product){
	$cartProducts.= '- '.$product['name'];
	if(isset($product['attributes']) && $product['attributes'] != ''){
		$arrayAttributes = preg_split('/, /',$product['attributes']);
		foreach ($arrayAttributes as $attribute){
			$cartProducts.= ' - '.$attribute;
		}
	}

	$cartProducts .= "<br/><br/>";

}

// Display all and exit
	$amount = (float)($cart->getOrderTotal(true, Cart::BOTH))*100;
	$tipoMondea = '978';
	$exponente = '2';
	$urlOK = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/ceca/resultRedirect.php?result=0&cartId='.$idCart.(Configuration::get('CECA_SHOW_AS_IFRAME')==1?'&content_only=1':'');
	$urlNOK = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/ceca/resultRedirect.php?result=1&cartId='.$idCart.(Configuration::get('CECA_SHOW_AS_IFRAME')==1?'&content_only=1':'');

$smarty->assign(array(
	'id_cart' => $idCart,
	'url_tpvv' => $url_tpvv,
	'MerchantID' => Configuration::get('CECA_MERCHANT_ID'),
	'AcquirerBIN' => Configuration::get('CECA_ACQUIRER_BIN'),
	'TerminalID' => Configuration::get('CECA_TERMINAL_ID'),
	'url_OK' => $urlOK,
	'url_NOK' => $urlNOK,
	'Firma' => Ceca::getFirma($idCart, $amount, $tipoMondea, $exponente, $urlOK, $urlNOK),
	'orderId' => $idCart,
	'Importe' => $amount,
	'TipoMoneda' => $tipoMondea,
	'Exponente' => $exponente,
	'locale' => $locale,
	'cart_products' => $cartProducts,
	'showInIframe' => (Configuration::get('CECA_SHOW_AS_IFRAME')==1)
));


$smarty->display(_PS_MODULE_DIR_.$ceca->name.'/views/templates/front/redirect.tpl');

include(_PS_ROOT_DIR_.'/footer.php');
