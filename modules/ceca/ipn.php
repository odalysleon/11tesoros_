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
include(dirname(__FILE__).'/ceca.php');

$data = array(
	'merchantID' => Tools::getValue('MerchantID'),
	'acquirerBIN' => Tools::getValue('AcquirerBIN'),
	'terminalID' => Tools::getValue('TerminalID'),
	'numOperacion' => Tools::getValue('Num_operacion'),
	'amount' => Tools::getValue('Importe'),
	'currency' => Tools::getValue('TipoMoneda'),
	'exponente' => Tools::getValue('Exponente'),
	'referencia' => Tools::getValue('Referencia'),
	'firma' => Tools::getValue('Firma'),
	'num_aut' => Tools::getValue('Num_aut'),
	'idioma' => Tools::getValue('Idioma'),
	'pais' => Tools::getValue('Pais'),
	'descripcion' => Tools::getValue('Descripcion')
);

if(empty($data))
	die('Transaction with no data');
if(!$data['numOperacion'])
	die('Transaction with incorrect data');

$numOperacion = preg_split('/_/', $data['numOperacion']);
$full_opertationNum = $data['numOperacion'];
if(count($numOperacion) != 4)
	die('Transaction with incorrect data');

$cartId = $numOperacion[0];
$tpvId = $numOperacion[1];
$operation_type = $numOperacion[2];

$ceca = new Ceca();

//obtenemos el tpv
$sql = 'SELECT *
		FROM `'.pSQL(_DB_PREFIX_.$ceca->name).'_config`
		WHERE `id_'.pSQL($ceca->name).'_config` = '.(int)$tpvId;
$tpv = Db::getInstance()->getRow($sql);

if(!$tpv)
	die('TPV configuration not found');

$ownFirma = Ceca::getFirmaIPNSHA2($data['numOperacion'], $data['amount'], $tpv, $data['exponente'], $data['referencia']);

if($data['firma'] != $ownFirma)
	die('Firmas does not match');
if ($data['merchantID'] != $tpv['merchant_id'])
	die('Merchant ID does not match');
if ($data['acquirerBIN'] != $tpv['acquirer_bin'])
	die('Acquirer BIN . does not match');
if ($data['terminalID'] != $tpv['terminal_id'])
	die('Terminal ID. does not match');

//FIN VALIDACIONES

$cart = new Cart((int)$cartId);
$codigoError = $data['num_aut'];
$errorMessage = 'Pago OK';

$mensaje = "DATOS RELEVANTES\n";
$mensaje .= "Precio: ".$data['amount']."\n";
$mensaje .= "Cart: ".$cartId."\n";
$mensaje .= "Codigo Comercio: ".$data['merchantID']."\n";
$mensaje .= "Codigo Entidad: ".$data['acquirerBIN']."\n";
$mensaje .= "Terminal Id: ".$data['terminalID']."\n";
$mensaje .= "Moneda: ".$data['currency']."\n";
$mensaje .= "Firma Servidor: ".$data['firma']."\n";
$mensaje .= "Idioma: ".$data['idioma']."\n";
$mensaje .= "Exponente: ".$data['exponente']."\n";
$mensaje .= "Referencia: ".$data['referencia']."\n";
$mensaje .= "Num aut: ".$data['num_aut']."\n";
$mensaje .= "Pais: ".$data['pais']."\n";
$mensaje .= "Descripcion: ".$data['descripcion']."\n";

//Currency_special: forzamos la moneda con la que ha pagado el cliente en TPV
if (version_compare(_PS_VERSION_, '1.7', '>='))
	$currencyId = Currency::getIdByIsoCode($data['currency']);
else
	$currencyId = Currency::getIdByIsoCodeNum($data['currency']);

if (!$currencyId)
	$currencyId = $cart->id_currency;

if (version_compare(_PS_VERSION_, '1.5', '>'))
{
	if (Context::getContext()->link == null)
		Context::getContext()->link = new Link();

		Context::getContext()->currency = new Currency($currencyId);
		Context::getContext()->customer = new Customer($cart->id_customer);
		Context::getContext()->cart = $cart;
}

/* ADD TO NOTIFICATION TABLE */

$transaction_info = 'MerchantParams: '.http_build_query($data);
$transaction_info .= ' | ';
$transaction_info .= 'GET: '.http_build_query($_GET);
$transaction_info .= ' | ';
$transaction_info .= 'POST: '.http_build_query($_POST);
$type = $tpv['sandbox_mode']?'test':'real';

/*Compra correcta*/
/*if ($codigoError == '000')*/ 
	$ceca->validateOrder((int)$cartId, _PS_OS_PAYMENT_, (int)$data['amount'] / 100, $ceca->displayName, $mensaje, array('transaction_id' => $data['referencia']), $currencyId, false, $cart->secure_key);


/*elseif($codigoError != '000' && Configuration::get('CECA_CLEAR_CART') == '1') 
	$ceca->validateOrder((int)$cartId, _PS_OS_ERROR_, 0, $ceca->displayName, $mensaje, array(), $currencyId, false, $cart->secure_key);
*/
	
$insertNotifySql = 'INSERT INTO `'.pSQL(_DB_PREFIX_.$ceca->name).'_notify` (
`id_customer`, `id_cart`, `amount_cart`, `amount_paid`, `tpv_order`,
`error_code`, `error_message`, `debug_data`, `type`, `date_add`, `id_tpv` ,`shop_id`, `operation_num`) VALUES (
\''.(int)$cart->id_customer.'\', \''.pSQL($cartId).'\', \''.((float)$cart->getOrderTotal(true, Cart::BOTH)).'\', \''.((float)$data['amount'] / 100).'\', \''.pSQL($data['referencia']).'\',
\''.pSQL($codigoError).'\', \''.pSQL($errorMessage).'\', \''.pSQL($transaction_info).'\', \''.pSQL($type).'\', \''.date('Y-m-d H:i:s').'\', \''.(int)$tpvId.'\', \''.(int)Context::getContext()->shop->id.'\', 
\''.$full_opertationNum.'\')';

Db::getInstance()->execute($insertNotifySql);
$notifyId = (int)Db::getInstance()->Insert_ID();

if ($ceca->currentOrder)
{
	$sqlUpdate = 'UPDATE `'.pSQL(_DB_PREFIX_.$ceca->name).'_notify` SET `id_order` = '.(int)$ceca->currentOrder.
	' WHERE `id_'.pSQL($ceca->name).'_notify` = '.(int)$notifyId;
	Db::getInstance()->execute($sqlUpdate);
}
	
/* END ADD TO NOTIFICATION TABLE */
echo '$*$OKY$*$';
?>