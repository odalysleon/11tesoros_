<?php
/**
 * Biblioteca de métodos propios
 * @author      miguel.cejas
 * @date        14/12/2016
 */
class Etiqueta {
  
  public static function imprimir($etiqueta) {
    
    $decoded = base64_decode($etiqueta);
    $file = 'etiqueta.pdf';
    file_put_contents($file, $decoded);

    if (file_exists($file)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/pdf');
      header('Content-Disposition: attachment; filename="' . basename($file) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      @readfile($file);
    }
    
    return true;
  }
}
