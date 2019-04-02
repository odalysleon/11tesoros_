<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class CnUtils
{

    public static function cnArrayColumn(array $input, $columnKey, $indexKey = null)
    {

        $array = array();
        foreach ($input as $value) {
            if (!array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    public static function findPropertyInArray(array $array, $propName, $v)
    {
        $item = null;
        foreach ($array as $struct) {
            $field = (array) $struct;
            if ($v == $field[$propName]) {
                $item = $struct;
                break;
            }
        }
        return $item;
    }

    public static function doPostRequestCn($action, $obj, $apiEndpoint)
    {
        $data = json_encode($obj);
        $response = "";
        $url = $apiEndpoint . $action;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . Tools::strlen($data),
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response == false || $response == '') {
            // Server is not responding
            return false;
        } else {
            return Tools::jsonDecode($response);
        }
    }

     public static function getShopUrl($shop) {
        $sslEverywhere = Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $base = (($ssl && $sslEverywhere) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        $pageLink = $base.$shop->getBaseURI();
        return $pageLink;
     }
}
