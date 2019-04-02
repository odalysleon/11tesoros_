<?php

/**
 * Biblioteca de métodos propios
 * @author      miguel.cejas
 * @date        14/12/2016
 */
 
 class DinaLib {

  public static function cargaFicheroSQL($sql_file) {
    $sql_content = file_get_contents($sql_file);
    $sql_content = str_replace('PREFIX_', _DB_PREFIX_, $sql_content);
    $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);
    $result = true;
		
    foreach ($sql_requests AS $request)
      if (!empty($request))
        $result &= Db::getInstance()->execute(trim($request));

    return $result;
  }
	
		
	public static function logs($mensaje){
    	//$archivo = 'C://envialia.log';
    	$archivo = _PS_MODULE_DIR_.'envialiacarrier/envialia.log';
        $manejador = fopen($archivo,'a');
        fwrite($manejador,"[".date("r")."] PrestaShop ver:"._PS_VERSION_." -> : $mensaje\n\r");
        fclose($manejador);
    }
}
