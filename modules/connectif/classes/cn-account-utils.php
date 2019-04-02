<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class CnAccountUtils
{
    public static function activateAccount(
        $account,
        $connectifModuleVersion,
        $apiEndpoint,
        $forceServiceWorkerOverride,
        $serviceWorkerUrl,
        $optOutUrl
    ) {
        $phpVersion = phpversion();
        $prestashopVersion = _PS_VERSION_;
        // Module is activating
        $data = array(
            'phpVersion' => $phpVersion,
            'prestashopVersion' => $prestashopVersion,
            'moduleVersion' => $connectifModuleVersion,
            'serviceWorkerUrl' => $serviceWorkerUrl,
            'forceServiceWorkerOverride' => $forceServiceWorkerOverride || false,
            'optOutUrl' => $optOutUrl,
            'checksum' => $account->checksum,
            'clientId' => $account->client_id,
            'language' => $account->lang_name,
            'currency' => $account->currency_name,
        );
        $actionPath = '/integration-type/prestashop/' . $account->client_id . '/auto-config';
        $activationResponse = CnUtils::doPostRequestCn($actionPath, $data, $apiEndpoint);
        return $activationResponse;
    }

    public static function getAccounts()
    {
        $cnAccounts = array();
        $cnConfigAccounts = Configuration::get('CN_ACCOUNTS');
        if (!is_null($cnConfigAccounts) && is_array(json_decode($cnConfigAccounts))) {
            $cnAccounts = json_decode($cnConfigAccounts);
        }
        return $cnAccounts;
    }

    public static function switchAccountStatus($account_index)
    {
        $cnAccounts = self::getAccounts(null);

        if (count($cnAccounts) >= $account_index + 1) {
            $isActive = $cnAccounts[$account_index]->is_active;

            if ($isActive == "1") {
                $cnAccounts[$account_index]->is_active = "0";
            } elseif ($isActive == "0") {
                $activationResponse = self::activateAccount($cnAccounts[$account_index],
                    '1.2.16',
                    self::getApiEndPoint(),
                    false,
                    Configuration::get('CN_SERVICE_WORKER_URL'),
                    Configuration::get('CN_OPT_OUT_URL'));
                if (!$activationResponse || !$activationResponse->isValid) {
                    return false;
                }
                $cnAccounts[$account_index]->is_active = "1";
            }

            if (!Configuration::updateValue('CN_ACCOUNTS', json_encode($cnAccounts))) {
                return false;
            }
            return true;
        }
    }

    private static function getApiEndPoint() {
        $configFileString = Tools::file_get_contents(_PS_MODULE_DIR_ . '/connectif/cn-application-config-string.json');
        $jsonApplicationConfig = json_decode($configFileString);
        $appConfig = $jsonApplicationConfig->applicationConfigString;

        return $apiEndpoint = $appConfig->apiProtocol . '://' . $appConfig->apiDomain . ':' . $appConfig->apiPort;
    }
}
