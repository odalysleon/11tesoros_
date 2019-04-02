<?php

/**
 * Clase EnvialiaEstado 
 * Referente al tratamiento de servicios de ENVIALIA
 * @author      miguel.cejas
 * @date        02/02/2017
 */

class EnvialiaEstado extends ObjectModel {
	
	public $id_envialia_estado;
  public $id_order_state;
  public $V_COD_TIPO_EST;

  public static $definition = array(
      'table' => 'envialia_estado',
      'primary' => 'id_envialia_estado',
      'multilang' => false,
      'fields' => array(
					'id_order_state' => array(
              'type' => self::TYPE_INT,
              'required' => true
          ),
          'V_COD_TIPO_EST' => array(
              'type' => self::TYPE_STRING,
              'required' => true
          ),
      ),
  );

}
