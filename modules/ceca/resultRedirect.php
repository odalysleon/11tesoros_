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
include(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/ceca.php');

$ceca = new Ceca();

if(false)
	$smarty = new Smarty();

$result = (int)(Tools::getValue('result'));
$cartId = (int)(Tools::getValue('cartId'));
$domain = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;

if (version_compare(_PS_VERSION_,'1.5','>')){
	$link = new Link();
	$url = $link->getModuleLink('ceca', 'result', array("result"=>$result,"cartId"=>$cartId), Configuration::get('PS_SSL_ENABLED'));
}
else
	$url = $domain."modules/ceca/result.php?result=".$result."&cartId=".$cartId;


//var_dump($domain);
$smarty->assign(array(
	'url' => $url,
	'module_dir' => _MODULE_DIR_.$ceca->name
));

$smarty->display(_PS_MODULE_DIR_.$ceca->name.'/views/templates/front/resultRedirect.tpl');