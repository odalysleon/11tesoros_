<?php

/**
 * Clase EnvialiaConfig 
 * Referente a la configuracion interna del modulo
 * @author      miguel.cejas
 * @date        15/02/2017
 */
require_once('ModuleConst.php');

class EnvialiaConfig extends ObjectModel {

  public static function getConfig() {
    $sql = 'SELECT V_COD_AGE, V_COD_CLI, V_COD_CLI_DEP, V_URL_WEB, V_URL_SEG, V_ID_SESION, CAST(AES_DECRYPT(BL_PASS, "' . CONST_TOKEN . '") as CHAR) as BL_PASS				
              FROM ' . _DB_PREFIX_ . 'envialia_config';

    $resultado = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

    foreach ($resultado as $result) {
      
    }
    return $result;
  }

  public static function setConfigAcceso($strAgencia, $strCliente, $strDepto, $strUrlWeb, $blPass) {
    $sql = 'UPDATE ' . _DB_PREFIX_ . 'envialia_config SET V_COD_AGE = "' . $strAgencia . '", 
        V_COD_CLI = "' . $strCliente . '", V_COD_CLI_DEP = "' . $strDepto . '", V_URL_WEB = "' . $strUrlWeb . '", 
        BL_PASS = AES_ENCRYPT("' . $blPass . '","' . CONST_TOKEN . '")';

    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);

    return $result;
  }

  public static function setSesion($strSesion) {
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE ' . _DB_PREFIX_ . 'envialia_config SET V_ID_SESION = "' . $strSesion . '"');

    return $result;
  }

  public static function setSesionYUrlSeg($strSesion, $strUrlSeg) {
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('UPDATE ' . _DB_PREFIX_ . 'envialia_config SET V_ID_SESION = "' . $strSesion . '", V_URL_SEG = "' . $strUrlSeg . '"');

    return $result;
  }

}
