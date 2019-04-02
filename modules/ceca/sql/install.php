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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_notify` (
    `id_'.pSQL($this->name).'_notify` int(11) NOT NULL AUTO_INCREMENT,
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
    `id_tpv` int(11) NULL,
    `shop_id` int(11) NULL,
    `operation_num` varchar(128) NOT NULL,
    PRIMARY KEY  (`id_'.pSQL($this->name).'_notify`)
) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

$sql[] = 'INSERT INTO `'.pSQL(_DB_PREFIX_.$this->name).'_notify` (`id_customer`, `id_cart`,`id_order`,`tpv_order`,
		`amount_cart`, `amount_paid`, `error_code`, `error_message`, `debug_data`, `type`, `date_add`)
		  VALUES (1, 1, 0, "0", 0,0, "TEST", "TEST", "TEST", "test", NOW() )';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_config` (
    `id_'.pSQL($this->name).'_config` int(11) NOT NULL AUTO_INCREMENT,
    `shop_id` INT(11) NOT NULL,
    `sandbox_mode` char(1) NOT NULL,
    `merchant_id` varchar(10) NOT NULL,
    `acquirer_bin` varchar(10) NOT NULL,
    `terminal_id` varchar(8) NOT NULL,
    `crypt_key_prod` varchar(125) NOT NULL,
    `crypt_key_simulador` varchar(125) NOT NULL,
	`min_amount` decimal(17,2) NOT NULL DEFAULT \'0.00\',
	`max_amount` decimal(17,2) NOT NULL DEFAULT \'0.00\',
    `iframe_mode` char(1) NOT NULL,
    `currency_code` varchar(5) NOT NULL DEFAULT 978,
	`currency_filter` TINYINT( 1 ) NOT NULL DEFAULT 0,
    `iframe_width` varchar(5) NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    `active` TINYINT( 1 ) NOT NULL DEFAULT 0,
    PRIMARY KEY  (`id_'.pSQL($this->name).'_config`)
) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'.pSQL(_DB_PREFIX_.$this->name).'_config_lang` (
    `id_'.pSQL($this->name).'_config` int(11) NOT NULL,
    `id_lang` int(11) NOT NULL,
    `payment_text` varchar(250) NOT NULL,
    PRIMARY KEY  (`id_'.pSQL($this->name).'_config`, `id_lang`)
) ENGINE='.pSQL(_MYSQL_ENGINE_).' DEFAULT CHARSET=utf8;';

$sql[] = "CREATE TABLE IF NOT EXISTS `".pSQL(_DB_PREFIX_.$this->name)."_config_group` (
		`id_tpv` int(10) unsigned NOT NULL,
		`id_group` int(10) unsigned NOT NULL
		) ENGINE=".pSQL(_MYSQL_ENGINE_)." DEFAULT CHARSET=utf8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `".pSQL(_DB_PREFIX_.$this->name)."_config_carrier` (
		`id_tpv` int(10) unsigned NOT NULL,
		`id_carrier` int(10) unsigned NOT NULL,
		`id_reference` int(10) unsigned NOT NULL
		) ENGINE=".pSQL(_MYSQL_ENGINE_)." DEFAULT CHARSET=utf8;";

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
