<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_4($object)
{
    $dir_module = dirname(_PS_MODULE_DIR_);
    require_once $dir_module . '/modules/connectif/classes/cn-account-utils.php';

    if (!Configuration::updateValue('CN_PRODUCT_IMAGE_SIZE', ImageType::getFormatedName('home'))) {
        return false;
    }

    if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
        $accounts = CnAccountUtils::getAccounts();
        foreach ($accounts as $key => $account) {
            $clientId = $account->client_id;
            $apiKey = $account->api_key;
            $isActive = $account->is_active;
            if (!$clientId || !$apiKey) {
                break;
            }
            $lang = $account->lang;
            $currency = $account->currency;
            $langName = $account->lang_name;
            $currencyName = $account->currency_name;
            $accountActivationResponse = updateAccountConfiguration(
                $clientId,
                $apiKey,
                $lang,
                $currency,
                $isActive,
                $langName,
                $currencyName
            );
            if ((!$accountActivationResponse || !$accountActivationResponse->isValid) && $isActive) {
                if (!CnAccountUtils::switchAccountStatus($key)) {
                    return false;
                }
            }
        }
        return true;
    } else {
        $clientId = (string) Configuration::get('CN_CLIENT_ID');
        $apiKey = (string) Configuration::get('CN_API');
        $isActive = (string) Configuration::get('CN_ACTIVE');
        if (!$clientId || !$apiKey) {
            return true;
        }
        $lang = null;
        $currency = null;
        $langName = null;
        $currencyName = null;
        $accountActivationResponse = updateAccountConfiguration(
            $clientId,
            $apiKey,
            $lang,
            $currency,
            $isActive,
            $langName,
            $currencyName
        );
        if (!$accountActivationResponse || !$accountActivationResponse->isValid) {
            if (!Configuration::updateValue('CN_ACTIVE', '')) {
                return false;
            }
        }
        return true;
    }

}

function updateAccountConfiguration($clientId, $apiKey, $lang, $currency, $isActive, $langName, $currencyName)
{
    $checksum = hash_hmac('sha1', $clientId, $apiKey);
    $account = (object) array(
        'client_id' => $clientId,
        'api_key' => $apiKey,
        'lang' => $lang,
        'currency' => $currency,
        'is_active' => $isActive,
        'checksum' => $checksum,
        'lang_name' => $langName,
        'currency_name' => $currencyName,
    );

    $configFileString = Tools::file_get_contents(_PS_MODULE_DIR_ . '/connectif/cn-application-config-string.json');
    $jsonApplicationConfig = json_decode($configFileString);
    $appConfig = $jsonApplicationConfig->applicationConfigString;

    $apiEndpoint = $appConfig->apiProtocol . '://'
    . $appConfig->apiDomain . ':'
    . $appConfig->apiPort;

    $optOutUrlPath = 'modules/connectif/opt-out-url.php';
    $serviceWorkerPath = 'modules/connectif/views/js/service-worker.js';

    if (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
        $link = new Link('https://', 'https://');
    } else {
        $link = new Link('http://', 'http://');
    }

    $optOutUrl = $link->getPageLink(null) . $optOutUrlPath;
    $serviceWorkerURL = $link->getPageLink(null) . $serviceWorkerPath;

    $activationResponse = CnAccountUtils::activateAccount(
        $account,
        '1.2.4',
        $apiEndpoint,
        true,
        $serviceWorkerURL,
        $optOutUrl
    );

    return $activationResponse;
}
