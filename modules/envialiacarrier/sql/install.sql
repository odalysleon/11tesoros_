CREATE TABLE IF NOT EXISTS `PREFIX_envialia_tipo_serv` (
	`V_COD_TIPO_SERV` VARCHAR(3) NOT NULL PRIMARY KEY, 
	`ID_CARRIER` INT(10) UNSIGNED NOT NULL ,
	`V_DES` VARCHAR(60) NOT NULL, 
	`T_INT` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',	
	`T_EUR` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_zonas` (
	`id_envialia_zonas` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`id_zone` INT(10) UNSIGNED NOT NULL,
  `t_inter` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `t_europa` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_envio` (
	`id_envialia_envio` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`id_order` INT(10) UNSIGNED NOT NULL,
	`V_COD_AGE_CARGO` VARCHAR(6) NOT NULL, 
	`V_COD_AGE_ORI` VARCHAR(6) NOT NULL, 
	`V_ALBARAN` VARCHAR(10) NOT NULL, 
	`V_GUID` VARCHAR(40), 
	`D_FECHA` DATETIME
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_tarifa_pesos` ( 
	`id_envialia_tarifa_pesos` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`V_COD_TIPO_SERV` VARCHAR(3) NOT NULL , 
	`I_COD_ZONA` INT(10) UNSIGNED NOT NULL , 
	`F_PESO` FLOAT NOT NULL ,
	`F_PRECIO` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_envialia_tarifa_pesos` ADD CONSTRAINT `FK_TAR_PESO_ZONA` FOREIGN KEY (`I_COD_ZONA`) REFERENCES `PREFIX_zone`(`id_zone`) ON DELETE CASCADE ON UPDATE NO ACTION; 
ALTER TABLE `PREFIX_envialia_tarifa_pesos` ADD CONSTRAINT `FK_TAR_PESO_SERV` FOREIGN KEY (`V_COD_TIPO_SERV`) REFERENCES `PREFIX_envialia_tipo_serv`(`V_COD_TIPO_SERV`) ON DELETE CASCADE ON UPDATE NO ACTION; 

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_tarifa_importes` ( 
	`id_envialia_tarifa_importes` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`V_COD_TIPO_SERV` VARCHAR(3) NOT NULL , 
	`I_COD_ZONA` INT(10) UNSIGNED NOT NULL , 
	`F_IMPORTE` FLOAT NOT NULL ,
	`F_PRECIO` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_envialia_tarifa_importes` ADD CONSTRAINT `FK_TAR_IMP_ZONA` FOREIGN KEY (`I_COD_ZONA`) REFERENCES `PREFIX_zone`(`id_zone`) ON DELETE CASCADE ON UPDATE NO ACTION; 
ALTER TABLE `PREFIX_envialia_tarifa_importes` ADD CONSTRAINT `FK_TAR_IMP_SERV` FOREIGN KEY (`V_COD_TIPO_SERV`) REFERENCES `PREFIX_envialia_tipo_serv`(`V_COD_TIPO_SERV`) ON DELETE CASCADE ON UPDATE NO ACTION;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_tarifa_pesos_base` ( 
	`id_envialia_tarifa_pesos_base` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`V_COD_TIPO_SERV` VARCHAR(3) NOT NULL , 
	`I_COD_ZONA` INT(10) UNSIGNED NOT NULL , 
	`F_PESO` FLOAT NOT NULL ,
	`F_PRECIO` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_tarifa_importes_base` ( 
	`id_envialia_tarifa_importes_base` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`V_COD_TIPO_SERV` VARCHAR(3) NOT NULL , 
	`I_COD_ZONA` INT(10) UNSIGNED NOT NULL , 
	`F_IMPORTE` FLOAT NOT NULL ,
	`F_PRECIO` FLOAT NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_estado` (
	`id_envialia_estado` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`id_order_state` INT(10) UNSIGNED NOT NULL ,
	`V_COD_TIPO_EST` VARCHAR(4) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_envialia_estado` ADD CONSTRAINT `FK_ENV_ESTADO` FOREIGN KEY (`id_order_state`) REFERENCES `PREFIX_order_state`(`id_order_state`) ON DELETE CASCADE ON UPDATE NO ACTION;

CREATE TABLE IF NOT EXISTS `PREFIX_envialia_config` (
  `id_envialia_config` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `V_COD_AGE` VARCHAR(6) NULL,
  `V_COD_CLI` VARCHAR(5) NULL,
  `V_COD_CLI_DEP` VARCHAR(5) NULL,
  `BL_PASS` BLOB NULL,
  `V_URL_WEB` VARCHAR(250) NULL,
  `V_ID_SESION` VARCHAR(40) NULL,
  `V_URL_SEG` VARCHAR(250) NULL
	) ENGINE = InnoDB DEFAULT CHARSET=utf8;
	
CREATE TABLE IF NOT EXISTS `PREFIX_envialia_cp` (
  `id_envialia_cp` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_state` INT(10) UNSIGNED NOT NULL, 
  `V_COD_PROV` VARCHAR(2) NULL
	) ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `PREFIX_envialia_config` (`V_URL_WEB`, `V_ID_SESION`) VALUES ("ws.envialia.com", "");

INSERT INTO `PREFIX_envialia_tipo_serv` (`V_COD_TIPO_SERV`, `ID_CARRIER`, `V_DES`, `T_INT`, `T_EUR`) SELECT 'E24', id_carrier, name, 0, 0 FROM PREFIX_carrier WHERE name = 'servicio E-Comm 24' order by id_carrier desc limit 1; 
INSERT INTO `PREFIX_envialia_tipo_serv` (`V_COD_TIPO_SERV`, `ID_CARRIER`, `V_DES`, `T_INT`, `T_EUR`) SELECT 'E72', id_carrier, name, 0, 0 FROM PREFIX_carrier WHERE name = 'servicio E-Comm 72' order by id_carrier desc limit 1; 
INSERT INTO `PREFIX_envialia_tipo_serv` (`V_COD_TIPO_SERV`, `ID_CARRIER`, `V_DES`, `T_INT`, `T_EUR`) SELECT 'EEU', id_carrier, name, 0, 1 FROM PREFIX_carrier WHERE name = 'E-COMM EUROPE EXPRESS' order by id_carrier desc limit 1; 
INSERT INTO `PREFIX_envialia_tipo_serv` (`V_COD_TIPO_SERV`, `ID_CARRIER`, `V_DES`, `T_INT`, `T_EUR`) SELECT 'EWW', id_carrier, name, 1, 1 FROM PREFIX_carrier WHERE name = 'E-COMM WORLDWIDE' order by id_carrier desc limit 1; 

UPDATE `PREFIX_carrier` set shipping_handling = 0 where external_module_name = "envialiacarrier";

INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('PROVINCIAL', 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "PROVINCIAL";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('REGIONAL' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "REGIONAL";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('PENINSULA' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "PENINSULA";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('PALMA MALLORCA' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "PALMA MALLORCA";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('BALEARES (I.M.)' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "BALEARES (I.M.)";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('ANDORRA' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "ANDORRA";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('L.PALMAS - TENERIFE' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "L.PALMAS - TENERIFE";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('CANARIAS (I.M.)' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "CANARIAS (I.M.)";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('CEUTA - GIBRALTAR' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "CEUTA - GIBRALTAR";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('PORTUGAL' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "PORTUGAL";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('PORTUGAL ISLAS' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "PORTUGAL ISLAS";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('MELILLA' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 0 FROM PREFIX_zone WHERE name = "MELILLA";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('EUROPA' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 0, 1 FROM PREFIX_zone WHERE name = "EUROPA";
INSERT INTO `PREFIX_zone` (`name`, `active`) VALUES ('INTERNACIONAL' , 1);
INSERT INTO `PREFIX_envialia_zonas` (`id_zone`, `t_inter`, `t_europa`) SELECT id_zone, 1, 1 FROM PREFIX_zone WHERE name = "INTERNACIONAL";


INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '01' FROM PREFIX_state WHERE iso_code = "ES-VI";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '02' FROM PREFIX_state WHERE iso_code = "ES-AB";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '03' FROM PREFIX_state WHERE iso_code = "ES-A";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '04' FROM PREFIX_state WHERE iso_code = "ES-AL"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '05' FROM PREFIX_state WHERE iso_code = "ES-AV"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '06' FROM PREFIX_state WHERE iso_code = "ES-BA"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '07' FROM PREFIX_state WHERE iso_code = "ES-PM"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '08' FROM PREFIX_state WHERE iso_code = "ES-B"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '09' FROM PREFIX_state WHERE iso_code = "ES-BU"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '10' FROM PREFIX_state WHERE iso_code = "ES-CC";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '11' FROM PREFIX_state WHERE iso_code = "ES-CA"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '12' FROM PREFIX_state WHERE iso_code = "ES-CS";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '13' FROM PREFIX_state WHERE iso_code = "ES-CR";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '14' FROM PREFIX_state WHERE iso_code = "ES-CO";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '15' FROM PREFIX_state WHERE iso_code = "ES-C";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '16' FROM PREFIX_state WHERE iso_code = "ES-CU";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '17' FROM PREFIX_state WHERE iso_code = "ES-GI";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '18' FROM PREFIX_state WHERE iso_code = "ES-GR";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '19' FROM PREFIX_state WHERE iso_code = "ES-GU";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '20' FROM PREFIX_state WHERE iso_code = "ES-SS";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '21' FROM PREFIX_state WHERE iso_code = "ES-H";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '22' FROM PREFIX_state WHERE iso_code = "ES-HU"; 
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '23' FROM PREFIX_state WHERE iso_code = "ES-J";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '24' FROM PREFIX_state WHERE iso_code = "ES-LE";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '25' FROM PREFIX_state WHERE iso_code = "ES-L";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '26' FROM PREFIX_state WHERE iso_code = "ES-LO";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '27' FROM PREFIX_state WHERE iso_code = "ES-LU";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '28' FROM PREFIX_state WHERE iso_code = "ES-M";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '29' FROM PREFIX_state WHERE iso_code = "ES-MA";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '30' FROM PREFIX_state WHERE iso_code = "ES-MU";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '31' FROM PREFIX_state WHERE iso_code = "ES-NA";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '32' FROM PREFIX_state WHERE iso_code = "ES-OR";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '33' FROM PREFIX_state WHERE iso_code = "ES-O";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '34' FROM PREFIX_state WHERE iso_code = "ES-P";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '35' FROM PREFIX_state WHERE iso_code = "ES-GC";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '36' FROM PREFIX_state WHERE iso_code = "ES-PO";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '37' FROM PREFIX_state WHERE iso_code = "ES-SA";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '38' FROM PREFIX_state WHERE iso_code = "ES-TF";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '39' FROM PREFIX_state WHERE iso_code = "ES-S";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '40' FROM PREFIX_state WHERE iso_code = "ES-SG";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '41' FROM PREFIX_state WHERE iso_code = "ES-SE";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '42' FROM PREFIX_state WHERE iso_code = "ES-SO";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '43' FROM PREFIX_state WHERE iso_code = "ES-T";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '44' FROM PREFIX_state WHERE iso_code = "ES-TE";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '45' FROM PREFIX_state WHERE iso_code = "ES-TO";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '46' FROM PREFIX_state WHERE iso_code = "ES-V";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '47' FROM PREFIX_state WHERE iso_code = "ES-VA";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '48' FROM PREFIX_state WHERE iso_code = "ES-BI";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '49' FROM PREFIX_state WHERE iso_code = "ES-ZA";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '50' FROM PREFIX_state WHERE iso_code = "ES-Z";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '51' FROM PREFIX_state WHERE iso_code = "ES-CE";
INSERT INTO `PREFIX_envialia_cp` (`id_state`, `V_COD_PROV`) SELECT id_state, '52' FROM PREFIX_state WHERE iso_code = "ES-ML";


UPDATE `PREFIX_tab` SET `icon` = 'transform' WHERE `class_name` = 'AdminEnvialiaEstado';
UPDATE `PREFIX_tab` SET `icon` = 'playlist_add' WHERE `class_name` = 'AdminEnvialiaTarifaPesos';
UPDATE `PREFIX_tab` SET `icon` = 'playlist_add' WHERE `class_name` = 'AdminEnvialiaTarifaImportes';
UPDATE `PREFIX_tab` SET `icon` = 'inbox' WHERE `class_name` = 'AdminEnvialiaEnvio';

ALTER TABLE `PREFIX_order_carrier` CHANGE COLUMN `tracking_number` `tracking_number` VARCHAR(250) NULL DEFAULT NULL ;