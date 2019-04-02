<?php
    $strSesion = $_POST['sesion'];
	$file = '../etiquetas/'.$strSesion.'.pdf';

	if (file_exists($file)) {
	  header('Content-Description: File Transfer');
	  header('Content-Type: application/pdf');
	  header('Content-Disposition: inline; filename="' . basename($file) . '"');
	  header('Expires: 0');
	  header('Cache-Control: must-revalidate');
	  header('Pragma: public');
	  @readfile($file);
    }
	
?>
