<?php

/**
 * Clase				EnvialiaTarifaPesos
 * Referente a pestaña ENVIALIA tarifa pesos
 * @author      miguel.cejas
 * @date        14/12/2016
 */
 
class EnvialiaTarifaPesos extends ObjectModel {
  
  public $id_envialia_tarifa_pesos;
  public $V_COD_TIPO_SERV;
  public $I_COD_ZONA;
  public $F_PESO;
  public $F_PRECIO;

  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
      'table' => 'envialia_tarifa_pesos',
      'primary' => 'id_envialia_tarifa_pesos',
      'multilang' => false,
      'fields' => array(
          'V_COD_TIPO_SERV' => array(
              'type' => self::TYPE_STRING,
              'required' => true
          ),
          'I_COD_ZONA' => array(
              'type' => self::TYPE_INT,
              'required' => true
          ),
          'F_PESO' => array(
              'type' => self::TYPE_FLOAT,
              'required' => true, 
							'validate' => 'isUnsignedFloat'
          ),
          'F_PRECIO' => array(
							'type' => self::TYPE_FLOAT, 
							'validate' => 'isUnsignedFloat'
					),
      ),
  );
	
	public static function getZonesEnvialia(){
        $cacheId = 'Zone::getZonesEnvialia';
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT z.*
				FROM `'._DB_PREFIX_.'zone` z
				INNER JOIN `'._DB_PREFIX_.'envialia_zonas` ez
				on ez.id_zone = z.id_zone
				WHERE z.active = 1
			');
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
   }
  
}
