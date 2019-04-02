<?php
	require_once('WebService.php');
	require_once '../../../config/config.inc.php';
	require_once '../../../classes/ObjectModel.php';
	require_once '../clases/EnvialiaConfig.php';

	$id_order = $_POST['id'];		
  $bultos = $_POST['bultos'];			
	
	$arrReturn = WebService::grabaEnvio($id_order, $bultos);
	
	$strError = $arrReturn['error'];
	if ($strError <> '') {
	
		$strError = str_replace("ó", "o", $strError);
		$strError = str_replace("í", "i", $strError);		
		$strError = str_replace('á', 'a', $strError);
		$strError = str_replace('ú', 'u', $strError);
		$strError = str_replace('é', 'e', $strError);
		
		header("HTTP/1.0 900 ".$strError);	
	}
?>
