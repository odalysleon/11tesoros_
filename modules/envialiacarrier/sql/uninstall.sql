DROP TABLE IF EXISTS `PREFIX_envialia_tarifa_pesos`;
DROP TABLE IF EXISTS `PREFIX_envialia_tarifa_importes`;
DROP TABLE IF EXISTS `PREFIX_envialia_estado`;
DROP TABLE IF EXISTS `PREFIX_envialia_envio`;
DROP TABLE IF EXISTS `PREFIX_envialia_config`;
DROP TABLE IF EXISTS `PREFIX_envialia_cp`;
DROP TABLE IF EXISTS `PREFIX_envialia_tarifa_pesos_base`;
DROP TABLE IF EXISTS `PREFIX_envialia_tarifa_importes_base`;
DROP TABLE IF EXISTS `PREFIX_envialia_tipo_serv`;

DELETE z FROM `PREFIX_zone` z INNER JOIN `PREFIX_envialia_zonas` ez ON ez.id_zone = z.id_zone;
DROP TABLE IF EXISTS `PREFIX_envialia_zonas`;

DELETE FROM `PREFIX_carrier_zone` WHERE `PREFIX_carrier_zone`.`id_carrier` IN (SELECT `id_carrier` FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier');
DELETE FROM `PREFIX_carrier_tax_rules_group_shop` WHERE `PREFIX_carrier_tax_rules_group_shop`.`id_carrier` IN (SELECT `id_carrier` FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier');
DELETE FROM `PREFIX_carrier_shop` WHERE `PREFIX_carrier_shop`.`id_carrier` IN (SELECT `id_carrier` FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier');
DELETE FROM `PREFIX_carrier_lang` WHERE `PREFIX_carrier_lang`.`id_carrier` IN (SELECT `id_carrier` FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier');
DELETE FROM `PREFIX_carrier_group` WHERE `PREFIX_carrier_group`.`id_carrier` IN (SELECT `id_carrier` FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier');
DELETE FROM `PREFIX_carrier` WHERE `PREFIX_carrier`.`external_module_name` = 'envialiacarrier';

DROP PROCEDURE IF EXISTS PA_GRABA_TARIFA_FIJA;
DROP PROCEDURE IF EXISTS PA_GRABA_TARIFA_PESO;
DROP PROCEDURE IF EXISTS PA_GRABA_TARIFA_IMPORTE;