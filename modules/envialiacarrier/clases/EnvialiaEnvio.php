<?php

/**
 * Clase EnvialiaPedidos
 * Referente al tratamiento de pedidos de ENVIALIA
 * @author      miguel.cejas
 * @date        02/02/2017
 */
class EnvialiaEnvio extends ObjectModel {

  public $id_envialia_envio;
  public $id_order;
  public $reference;
  public $date_add;
  
  public static $definition = array(
      'table' => 'orders',
      'primary' => 'id_order',
      'multilang' => false,
      'fields' => array(
          'id_order' => array(
              'type' => self::TYPE_INT,
              'required' => true
          ),
          'reference' => array(
              'type' => self::TYPE_STRING,
              'required' => true
          ),
          'date_add' => array(
              'type' => self::TYPE_DATE
          ),
      ),
  );

}
