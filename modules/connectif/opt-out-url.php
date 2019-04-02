<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

include(dirname(__FILE__) . '/classes/cn-basic-opt.php');

$email = Tools::getValue('email');
$request_checksum = Tools::getValue('checksum');
$sql = 'UPDATE ' . _DB_PREFIX_ . 'customer SET newsletter = 0 WHERE email="' . pSQL($email) . '"';


if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
    $cnMultilangConfigurationsArray = CnAccountUtils::getAccounts();
    foreach ($cnMultilangConfigurationsArray as $key => $accountConfiguration) {
        $local_checksum = hash_hmac('sha1', $email, $accountConfiguration->api_key);
        if ($local_checksum == $request_checksum) {
            $results = Db::getInstance()->execute($sql);
            break;
        }
    }
} else {
    $cn_api_key = Configuration::get('CN_API');
    $local_checksum = hash_hmac('sha1', $email, $cn_api_key);
    if ($local_checksum == $request_checksum) {
        $results = Db::getInstance()->execute($sql);
    }
}
