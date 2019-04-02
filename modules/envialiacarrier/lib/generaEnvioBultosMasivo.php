<?php				
	require_once('WebService.php');
	require_once '../../../config/config.inc.php';
	require_once '../../../classes/ObjectModel.php';
	require_once '../clases/EnvialiaConfig.php';
	
	$resultado = $_POST['arrPedidos'];		
  $bultos = $_POST['bultos'];
	
	$resultado = explode(",", $resultado);	
	$arrReturn = WebService::grabaEnvioMasivo($resultado, $bultos, true);
	
	$resultados = $arrReturn['resultados'];
	
	if ($resultados <> '') {
	
		$resultados = str_replace("ó", "o", $resultados);
		$resultados = str_replace("í", "i", $resultados);		
		$resultados = str_replace('á', 'a', $resultados);
		$resultados = str_replace('ú', 'u', $resultados);
		$resultados = str_replace('é', 'e', $resultados);
		
		header("HTTP/1.0 900 ".$resultados);
	}
?>
