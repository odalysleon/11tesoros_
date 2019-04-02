<?php

/**
 * Clase EnvialiaTipoServ 
 * Referente al tratamiento de servicios de ENVIALIA
 * @author      miguel.cejas
 * @date        20/12/2016
 */

class EnvialiaTipoServ extends ObjectModel {

  // Todos los servicios
  public static function getServicios() {
    $cacheId = 'EnvialiaTipoServ::getServicios_';
    if (!Cache::isStored($cacheId)) {
      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT V_COD_TIPO_SERV as id_option, V_DES as name
				FROM `' . _DB_PREFIX_ . 'envialia_tipo_serv`');
      Cache::store($cacheId, $result);

      return $result;
    }

    return Cache::retrieve($cacheId);
  }

  // Servicios peninsulares
  public static function getServiciosPen() {
    $cacheId = 'EnvialiaTipoServ::getServiciosPen_';
    if (!Cache::isStored($cacheId)) {
      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT V_COD_TIPO_SERV as id_option, V_DES as name
				FROM `' . _DB_PREFIX_ . 'envialia_tipo_serv`
        WHERE T_INT = 0
				  and T_EUR = 0');
      Cache::store($cacheId, $result);

      return $result;
    }

    return Cache::retrieve($cacheId);
  }

  // Servicios internacionales
  public static function getServiciosInt() {
    $cacheId = 'EnvialiaTipoServ::getServiciosInt_';
    if (!Cache::isStored($cacheId)) {
      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT V_COD_TIPO_SERV as id_option, V_DES as name
				FROM `' . _DB_PREFIX_ . 'envialia_tipo_serv`
        WHERE T_EUR = 1');
      Cache::store($cacheId, $result);

      return $result;
    }

    return Cache::retrieve($cacheId);
  }

}
