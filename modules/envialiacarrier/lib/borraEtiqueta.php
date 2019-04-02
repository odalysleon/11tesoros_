<?php
	$sesion = $_POST['sesion'];
	if (file_exists("../etiquetas/".$sesion.".pdf")) {
		unlink("../etiquetas/".$sesion.".pdf");
	}	
?>
