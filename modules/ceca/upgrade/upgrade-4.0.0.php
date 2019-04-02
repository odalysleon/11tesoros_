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

if (!defined('_PS_VERSION_'))
	exit;

/**
 * Function used to update your module from previous versions to the version 3.0.0,
 * Don't forget to create one file per version.
 */
function upgrade_module_4_0_0($module)
{

	$sql = array();

	$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$module->name).'_notify` (
	    `id_'.pSQL($module->name).'_notify` int(11) NOT NULL AUTO_INCREMENT,
	    `id_customer` int(11) NOT NULL,
	    `id_cart` int(11) NOT NULL,
	    `id_order` int(11) DEFAULT 0,
	    `tpv_order` varchar(30) NOT NULL,
	    `amount_cart` decimal(11,2) NOT NULL,
	    `amount_paid` decimal(11,2) NOT NULL,
	    `amount_refunded` decimal(11,2) NOT NULL DEFAULT 0,
	    `error_code` varchar(25) NOT NULL,
	    `error_message` varchar(450) NOT NULL,
	    `debug_data` TEXT,
	    `type` ENUM( \'test\', \'real\', \'unknown\' ) NOT NULL DEFAULT \'unknown\',
	    `date_add` datetime NOT NULL,
	    PRIMARY KEY  (`id_'.pSQL($module->name).'_notify`)
		) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

	$sql[] = 'ALTER TABLE `'.pSQL(_DB_PREFIX_.$module->name).'_notify` ADD `id_tpv` INT( 11 ) NULL ;';

	$sql[] = 'ALTER TABLE `'.pSQL(_DB_PREFIX_.$module->name).'_notify` ADD `shop_id` INT( 11 ) NULL ;';

	$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$module->name).'_config` (
    `id_'.pSQL($module->name).'_config` int(11) NOT NULL AUTO_INCREMENT,
    `shop_id` INT(11) NOT NULL,
    `sandbox_mode` char(1) NOT NULL,
    `merchant_id` varchar(9) NOT NULL,
    `acquirer_bin` varchar(10) NOT NULL,
    `terminal_id` varchar(8) NOT NULL,
    `crypt_key_prod` varchar(125) NOT NULL,
    `crypt_key_simulador` varchar(125) NOT NULL,
    `iframe_mode` char(1) NOT NULL,
    `currency_code` varchar(5) NOT NULL DEFAULT 978,
    `iframe_width` varchar(5) NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    `active` TINYINT( 1 ) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id_'.pSQL($module->name).'_config`)
	) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

	$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$module->name).'_config_lang` (
    `id_'.pSQL($module->name).'_config` int(11) NOT NULL,
    `id_lang` int(11) NOT NULL,
    `payment_text` varchar(250) NOT NULL,
    PRIMARY KEY  (`id_'.pSQL($module->name).'_config`, `id_lang`)
	) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

	$sql[] = "CREATE TABLE IF NOT EXISTS `".pSQL(_DB_PREFIX_.$module->name)."_config_group` (
	`id_tpv` int(10) unsigned NOT NULL,
	`id_group` int(10) unsigned NOT NULL
	) ENGINE=".pSQL(_MYSQL_ENGINE_)." DEFAULT CHARSET=utf8;";

	$sql[] = 'UPDATE `'.pSQL(_DB_PREFIX_.$module->name).'_notify` SET `id_tpv` = 1 WHERE `id_tpv` IS NULL;';

	$sql[] = 'UPDATE `'.pSQL(_DB_PREFIX_.$module->name).'_notify` SET `shop_id` = 1 WHERE `shop_id` IS NULL;';


	foreach ($sql as $query)
		if (Db::getInstance()->execute($query) == false)
			return false;

	//Añadimos dos propiedades en la tabla configuration
	Configuration::updateValue('CECA_CLEART_CART', '1');
	Configuration::updateValue('CECA_EXPONENT', '2');

	//Para cada tienda configurada creamos su tpv
	$shops = Shop::getShops(true, null, true);
	foreach ($shops as $shop)
		insertConfiguredTpvs($module, $shop);

	//Para cada tpv configurado insertamos registros de grupos
	insertConfiguredGroups($module);

	//Eliminamos las propiedades de configuración anterior
	deleteConfiguration();

	return $module;
}

/**
 * Método que obtiene la configuración del tpv de la versión anterior y crea un registro en la tabla de tpv
 */
function insertConfiguredTpvs($module, $shop){
	//Añadimos los tpv de la versión anterior que se encuentran configurados
	//Obtenemos la configuración anterior de la tabla ps_configuration
	$sandbox = Configuration::get('CECA_SANDBOX', null, null, $shop);
	$merchant_id = Configuration::get('CECA_MERCHANT_ID', null, null, $shop);
	$acquirer_bin = Configuration::get('CECA_ACQUIRER_BIN', null, null, $shop);
	$terminal_id = Configuration::get('CECA_TERMINAL_ID', null, null, $shop);
	$crypt_key_prod = Configuration::get('CECA_CRYPT_KEY_PROD', null, null, $shop);
	$crypt_key_simulador = Configuration::get('CECA_CRYPT_KEY_SIMULADOR', null, null, $shop);
	$iframe_show = Configuration::get('CECA_SHOW_AS_IFRAME', null, null, $shop);
	$currency = '978';
	$iframe_width = Configuration::get('CECA_IFRAME_WIDTH', null, null, $shop);

	$sql = 'INSERT INTO `'.pSQL(_DB_PREFIX_.$module->name).'_config`
					(`shop_id`, `sandbox_mode`, `merchant_id`, `acquirer_bin`, `terminal_id`, `crypt_key_prod`, `crypt_key_simulador`,
					`iframe_mode`, `currency_code`, `iframe_width`, `date_add`, `date_upd`, `active`)
					VALUES
					('.$shop.',
					\''.$sandbox.'\',
					\''.$merchant_id.'\',
					\''.$acquirer_bin.'\',
					\''.$terminal_id.'\',
					\''.$crypt_key_prod.'\',
					\''.$crypt_key_simulador.'\',
					\''.$iframe_show.'\',
					\''.$currency.'\',
					\''.$iframe_width.'\',
					NOW(),
					NOW(),
					1
					)';

	Db::getInstance()->execute($sql);

	$tpv_id = Db::getInstance()->Insert_ID();

	//Para cada lenguaje configurado creamos un registro de idioma del tpv
	//Obtenemos los lenguajes configurados y cremos una etiqueta por idioma para el tpv
	$languages = Language::getLanguages(true);
	$payment_text = array();
	if($languages)
	foreach($languages as $lang){
		switch ($lang['iso_code'])
		{
			case "es":
				$payment_text[$lang['id_lang']] = 'Pago con tarjeta';
				break;
			case "ca":
				$payment_text[$lang['id_lang']] = 'Pagament amb targeta';
				break;
			case "en":
				$payment_text[$lang['id_lang']] = 'Card payment';
				break;
			case "fr":
				$payment_text[$lang['id_lang']] = 'Paiement par carte';
				break;
			default:
				$payment_text[$lang['id_lang']] = '';
				break;
		}
	}

	if($payment_text)
		foreach ($payment_text as $id_lang => $text)
		{
			$sql = 'INSERT INTO `'.pSQL(_DB_PREFIX_.$module->name).'_config_lang`
							(`id_'.pSQL($module->name).'_config`, `id_lang`, `payment_text`)
							VALUES
							('.$tpv_id.', '.$id_lang.', \''.$text.'\')
							ON DUPLICATE KEY UPDATE
							`payment_text` = \''.$text.'\'';

			Db::getInstance()->execute($sql);
		}
}

/**
 * Método que elimina de BBDD la configuración de los tpv en ps_configuration.
 */
function deleteConfiguration(){
	Configuration::deleteByName('CECA_SANDBOX');
	Configuration::deleteByName('CECA_MERCHANT_ID');
	Configuration::deleteByName('CECA_ACQUIRER_BIN');
	Configuration::deleteByName('CECA_TERMINAL_ID');
	Configuration::deleteByName('CECA_CRYPT_KEY_PROD');
	Configuration::deleteByName('CECA_CRYPT_KEY_SIMULADOR');
	Configuration::deleteByName('CECA_SHOW_AS_IFRAME');
	Configuration::deleteByName('CECA_CURRENCY');
	Configuration::deleteByName('CECA_IFRAME_WIDTH');
}

/**
 * Método que crea la tabla de config de grupos del tpv y añade los registros correspondientes para cada tpv creado
 */

function insertConfiguredGroups($module) {

	$tpvs = Db::getInstance()->executeS('SELECT `id_'.pSQL($module->name).'_config` as tpv_id FROM `'.pSQL(_DB_PREFIX_.$module->name).'_config`');

	$preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
	foreach($tpvs as $tpv){
		foreach($preselected as $preselected_group){
			Db::getInstance()->execute('
			INSERT INTO '.pSQL(_DB_PREFIX_.$module->name).'_config_group (id_group, id_tpv)
			VALUES('.(int)$preselected_group.','.(int)$tpv['tpv_id'].')
			');
		}
	}
}
