<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class CnLogger
{

    public static function fatalHandler()
    {
        $errfile = "unknown file";
        $errstr = "shutdown";
        $errno = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if ($error !== null) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];
        }
        $dir_module = dirname(__FILE__);
        $logFile = fopen($dir_module . "/../" . "connectif_log.txt", "a");

        fwrite($logFile, "Time: " . date('Y-m-d') . " " . date('H:i:s', time() - date('Z')) . "\n");
        fwrite($logFile, "ErrorFile: " . $errfile . "\n");
        fwrite($logFile, "ErrorLine: " . $errline . "\n");
        fwrite($logFile, "ErrorMSG: " . $errstr . "\n");
        fwrite($logFile, "ErrorNo: " . $errno . "\n\n");

        fclose($logFile);
    }

    public static function initLogger($prestashopVersion)
    {

        $dir_module = dirname(__FILE__);
        $logFilePath = $dir_module . "/../" . "connectif_log.txt";

        $logFile = fopen($logFilePath, "a");
        fwrite($logFile, "Time: " . date('Y-m-d') . " " . date('H:i:s', time() - date('Z')) . "\n");
        fwrite($logFile, "PHP Version: " . phpversion() . "\n");
        fwrite($logFile, "Prestashop Version: " . $prestashopVersion . "\n\n");

        fclose($logFile);

        register_shutdown_function("self::fatalHandler");
    }
}
