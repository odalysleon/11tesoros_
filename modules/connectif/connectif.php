<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

if ((basename(__FILE__) === 'connectif.php')) {
    $dir_module = dirname(__FILE__);
    require_once $dir_module . '/classes/cn-logger.php';
    require_once $dir_module . '/classes/cn-utils.php';
    require_once $dir_module . '/classes/cn-product-utils.php';
    require_once $dir_module . '/classes/cn-user-utils.php';
    require_once $dir_module . '/classes/cn-account-utils.php';
}

class Connectif extends Module
{
    protected $path;
    protected $domain;
    private $clientId;
    private $endPoint;
    private $apiSecret;
    private $locationId;
    private $apiKey;
    private $applicationConfig;
    private $apiProtocol;
    private $apiBase;
    private $optOutUrlPath = 'modules/connectif/opt-out-url.php';
    private $serviceWorkerPath = 'modules/connectif/views/js/service-worker.js';

    public function __construct()
    {
        $this->name = 'connectif';
        $this->tab = 'advertising_marketing';
        $this->displayName = 'Connectif';
        $this->version = '1.2.16';
        $this->author = 'Connectif';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.7');
        $this->module_key = 'e1cf046f5a1030e994da678590699ae8';
        parent::__construct();

        $this->displayName = $this->l('Connectif');
        $this->description = $this->l('Prestashop integration with Connectif');
        $this->confirmUninstall = $this->l('Are you sure you want to delete ?');
        if ($this->context->controller !== null) {
            $this->context->controller->addCSS($this->_path . 'views/css/style.css', 'all');
        }
        $this->bootstrap = true;

        $this->checkContent();
        $this->setApiData();
    }

    private function checkContent()
    {
        if (!Configuration::get('CN_CLIENT_ID') || Configuration::get('CN_CLIENT_ID') == '' ||
            !Configuration::get('CN_API') || Configuration::get('CN_API') == ''
        ) {
            $this->warning = $this->l('You need to configure this module.');
        }
    }

    private function checkApiData()
    {
        if (Configuration::get('CN_CLIENT_ID') == '' ||
            Configuration::get('CN_API') == ''
        ) {
            return false;
        } else {
            return true;
        }
    }

    private function setApiData()
    {
        $configFileString = Tools::file_get_contents(_PS_MODULE_DIR_ . '/connectif/cn-application-config-string.json');
        $jsonApplicationConfig = json_decode($configFileString);
        $appConfig = $jsonApplicationConfig->applicationConfigString;
        $this->applicationConfig = $appConfig;
        $this->apiProtocol = $this->applicationConfig->apiProtocol;
        $this->apiBase = $this->applicationConfig->apiDomain . ':' . $this->applicationConfig->apiPort;
        $this->cdnClientScriptPath = $this->getCdnClientScriptPath();
        $this->clientId = Configuration::get('CN_CLIENT_ID');
        $this->apiSecret = Configuration::get('CN_API');
    }

    private function getCdnClientScriptPath() 
    {
        return $this->applicationConfig->cdnProtocol . '://' . $this->applicationConfig->cdnDomain . '/' . $this->applicationConfig->cdnPath
            . '/' . $this->applicationConfig->clientScriptPath;
    }

    /**
     * install
     */
    public function install()
    {

        if (!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('top') ||
            !$this->registerHook('displayFooter') ||
            !$this->registerHook('productfooter') ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('actionCustomerAccountAdd') ||
            !$this->registerHook('actionAuthentication') ||
            !$this->registerHook('actionSearch') ||
            !$this->registerHook('actionValidateOrder') ||
            !$this->registerHook('displayOrderConfirmation') ||
            !$this->registerHook('displayConnectifTags') ||
            !$this->registerHook('displayHome') ||
            !$this->registerHook('displayLeftColumn') ||
            !$this->registerHook('displayRightColumn') ||
            !$this->registerHook('displayTopColumn') ||
            !$this->createContent()
        ) {
            return false;
        }

        return true;
    }

    /**
     * uninstall
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->deleteContent()
        ) {
            return false;
        }
        return true;
    }

    private function createContent()
    {
        $length = 10;
        $randomString = Tools::substr(
            str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),
            0,
            $length
        );

        $shop = Context::getContext()->shop;
        $optOutUrl = CnUtils::getShopUrl($shop) . $this->optOutUrlPath;
        $serviceWorkerURl = CnUtils::getShopUrl($shop) . $this->serviceWorkerPath;

        if (!Configuration::updateValue('CN_CLIENT_ID', '') ||
            !Configuration::updateValue('CN_API', '') ||
            !Configuration::updateValue('CN_PRESTA', $randomString) ||
            !Configuration::updateValue('CN_ACTIVE', '') ||
            !Configuration::updateValue('CN_PRODUCT_REFERENCE', '') ||
            !Configuration::updateValue('CN_PURCHASE_EVENT', '1') ||
            !Configuration::updateValue('CN_SERVICE_WORKER_URL', $serviceWorkerURl) ||
            !Configuration::updateValue('CN_OPT_OUT_URL', $optOutUrl) ||
            !Configuration::updateValue('CN_REGISTER_EVENT', '1') ||
            !Configuration::updateValue('CN_SEARCH_EVENT', '1') ||
            !Configuration::updateValue('CN_ROUND_PRICE', '') ||
            !Configuration::updateValue('CN_LOGS_STATUS', '') ||
            !Configuration::updateValue('CN_MULTILANG_STATUS', '') ||
            !Configuration::updateValue('CN_CART_STATUS_EVENT', '1') ||
            !Configuration::updateValue('CN_NEWSLETTER_EVENT', '1') ||
            !Configuration::updateValue('CN_LOGIN_EVENT', '1') ||
            !Configuration::updateValue('CN_ACTIVE_REGISTER_FIELDS', array()) ||
            !Configuration::updateValue('CN_ACTIVE_REGISTER_FIELDS_ADDR', array()) ||
            !Configuration::updateValue('CN_PRODUCT_IMAGE_SIZE', ImageType::getFormatedName('home'))
        ) {
            return false;
        }

        return true;
    }

    private function deleteContent()
    {
        if (!Configuration::deleteByName('CN_CLIENT_ID') ||
            !Configuration::deleteByName('CN_API') ||
            !Configuration::deleteByName('CN_ACTIVE') ||
            !Configuration::deleteByName('CN_PRODUCT_REFERENCE') ||
            !Configuration::deleteByName('CN_PURCHASE_EVENT') ||
            !Configuration::deleteByName('CN_SERVICE_WORKER_URL') ||
            !Configuration::deleteByName('CN_OPT_OUT_URL') ||
            !Configuration::deleteByName('CN_REGISTER_EVENT') ||
            !Configuration::deleteByName('CN_SEARCH_EVENT') ||
            !Configuration::deleteByName('CN_ROUND_PRICE') ||
            !Configuration::deleteByName('CN_LOGS_STATUS') ||
            !Configuration::deleteByName('CN_MULTILANG_STATUS') ||
            !Configuration::deleteByName('CN_CART_STATUS_EVENT') ||
            !Configuration::deleteByName('CN_NEWSLETTER_EVENT') ||
            !Configuration::deleteByName('CN_LOGIN_EVENT') ||
            !Configuration::deleteByName('CN_WEB_PUSH_CODE') ||
            !Configuration::deleteByName('CN_PRESTA') ||
            !Configuration::deleteByName('CN_ACCOUNTS') ||
            !Configuration::deleteByName('CN_BANNERS') ||
            !Configuration::deleteByName('CN_ACTIVE_REGISTER_FIELDS') ||
            !Configuration::deleteByName('CN_ACTIVE_REGISTER_FIELDS_ADDR') ||
            !Configuration::deleteByName('CN_PRODUCT_IMAGE_SIZE')
        ) {
            return false;
        }
        return true;
    }

    /**
     * admin page
     */
    public function getContent()
    {
        $message = '';
        $html = '';

        $cn_shop_languages = Language::getLanguages(true, $this->context->shop->id);
        $cn_shop_currencies = Currency::getCurrenciesByIdShop($this->context->shop->id);

        if (Tools::isSubmit('downloadLogs_' . $this->name)) {
            $this->downloadLogs();
            return;
        }

        if (Tools::isSubmit('submit_' . $this->name)) {
            $message = $this->saveContent();
        } elseif (Tools::isSubmit('submit_new_banner')) {
            $message = $this->newBanner();
        } elseif (Tools::isSubmit('submit_new_connectif_account')) {
            $message = $this->newAccount();
        } elseif (Tools::isSubmit('delete_connectif_account')) {
            $account_index = Tools::getValue('account_index');
            $message = $this->deleteAccount($account_index);
        } elseif (Tools::isSubmit('deletebanner')) {
            $banner_index = Tools::getValue('banner_index');
            $message = $this->deleteBanner($banner_index);
        } elseif (Tools::isSubmit('enablebanner')) {
            $banner_index = Tools::getValue('banner_index');
            $message = $this->enableBanner($banner_index);
        } elseif (Tools::isSubmit('switch_connectif_account_status')) {
            $account_index = Tools::getValue('account_index');
            $message = $this->switchAccountStatus($account_index);
        }

        $this->displayContent($message);
        $this->context->controller->addJS(($this->_path) . 'views/js/riot+compiler.js');
        $isLogsEnabled = Configuration::get('CN_LOGS_STATUS') ? true : false;
        $isMultilangEnabled = Configuration::get('CN_MULTILANG_STATUS') ? true : false;
        $this->context->smarty->assign('logs_enabled', $isLogsEnabled);
        $this->context->smarty->assign('multilang_enabled', $isMultilangEnabled);
        $this->context->smarty->assign('module_templates', dirname(__FILE__) . '/views/templates/');
        $this->context->smarty->assign('is_plugin_active', $this->isAnyAccountActive());

        if (Tools::isSubmit('updatebanner')) {
            $this->context->smarty->assign('CN_BANNER_ID', '');
            $this->context->smarty->assign('CN_BANNER_TYPE', '');
            $this->context->smarty->assign('CN_BANNER_ACTIVE', '');
            $html = $this->display(__FILE__, 'views/templates/admin/cn_update_banner.tpl');
        } elseif (Tools::isSubmit('new_connectif_account')) {
            $this->context->smarty->assign('CN_SHOP_LANG_SELECTOR', '');
            $this->context->smarty->assign('CN_NEW_CLIENT_ID', '');
            $this->context->smarty->assign('CN_NEW_API', '');
            $this->context->smarty->assign('CN_SHOP_CURRENCY_SELECTOR', '');
            $this->context->smarty->assign('CN_NEW_ACCOUNT_ACTIVE', '');
            $this->context->smarty->assign('CN_LANGUAGES', $cn_shop_languages);
            $this->context->smarty->assign('CN_CURRENCIES', $cn_shop_currencies);
            $html = $this->display(__FILE__, 'views/templates/admin/cn_new_connectif_account.tpl');
        } else {
            $html = $this->display(__FILE__, 'views/templates/admin/connectif.tpl');
        }

        return $html . $this->display(__FILE__, 'views/templates/admin/ps-tags.tpl');
    }

    private function downloadLogs()
    {
        $dir_module = dirname(__FILE__);
        $filename = $dir_module . "\\" . "connectif_log.txt";
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: application/txt');

        header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filename));

        readfile($filename);
    }


    private function saveContent()
    {
        $activationResponse = false;

        $shop = Context::getContext()->shop;
        $optOutUrl = CnUtils::getShopUrl($shop) . $this->optOutUrlPath;
        $serviceWorkerURl = CnUtils::getShopUrl($shop) . $this->serviceWorkerPath;

        if (!Configuration::get('CN_MULTILANG_STATUS') && !Tools::getValue('CN_MULTILANG_STATUS')) {
            $clientId = (string) Tools::getValue('CN_CLIENT_ID');
            $apiKey = (string) Tools::getValue('CN_API');
            $checksum = hash_hmac('sha1', $clientId, $apiKey);
            $isActive = (string) Tools::getValue('CN_ACTIVE');
            $lang = null;
            $currency = null;
            $langName = null;
            $currencyName = null;

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

            if ($account->is_active === '1' && (!Configuration::get('CN_ACTIVE')
                || $clientId !== Configuration::get('CN_CLIENT_ID')
                || $apiKey !== Configuration::get('CN_API'))) {
                $activationResponse = $this->activateAccount($account);
                if (!$activationResponse || !$activationResponse->isValid) {
                    return $this->displayError($this->l('An error occurred while activating your module.'));
                }
            }

            if (!Configuration::updateValue('CN_CLIENT_ID', (string) Tools::getValue('CN_CLIENT_ID')) ||
                !Configuration::updateValue('CN_API', (string) Tools::getValue('CN_API')) ||
                !Configuration::updateValue('CN_ACTIVE', Tools::getValue('CN_ACTIVE') === '1')
            ) {
                $message = $this->displayError($this->l('An error occurred.'));
                return $message;
            }
        }

        $registerActiveFields = json_encode($this->getEncodedRegisterActiveFields());
        $registerActiveAddressFields = json_encode($this->getEncodedRegisterActiveAddressFields());
        if (Configuration::updateValue('CN_REGISTER_EVENT', Tools::getValue('CN_REGISTER_EVENT') === '1') &&
            Configuration::updateValue('CN_SEARCH_EVENT', Tools::getValue('CN_SEARCH_EVENT') === '1') &&
            Configuration::updateValue('CN_ROUND_PRICE', Tools::getValue('CN_ROUND_PRICE') === '1') &&
            Configuration::updateValue('CN_LOGS_STATUS', Tools::getValue('CN_LOGS_STATUS') === '1') &&
            Configuration::updateValue('CN_MULTILANG_STATUS', Tools::getValue('CN_MULTILANG_STATUS') === '1') &&
            Configuration::updateValue('CN_CART_STATUS_EVENT', Tools::getValue('CN_CART_STATUS_EVENT') === '1') &&
            Configuration::updateValue('CN_NEWSLETTER_EVENT', Tools::getValue('CN_NEWSLETTER_EVENT') === '1') &&
            Configuration::updateValue('CN_LOGIN_EVENT', Tools::getValue('CN_LOGIN_EVENT') === '1') &&
            Configuration::updateValue('CN_PRODUCT_REFERENCE', Tools::getValue('CN_PRODUCT_REFERENCE') === '1') &&
            Configuration::updateValue('CN_PURCHASE_EVENT', Tools::getValue('CN_PURCHASE_EVENT') === '1') &&
            Configuration::updateValue('CN_SERVICE_WORKER_URL', $serviceWorkerURl) &&
            Configuration::updateValue('CN_OPT_OUT_URL', $optOutUrl) &&
            Configuration::updateValue('CN_ACTIVE_REGISTER_FIELDS', $registerActiveFields) &&
            Configuration::updateValue('CN_ACTIVE_REGISTER_FIELDS_ADDR', $registerActiveAddressFields) &&
            Configuration::updateValue('CN_PRODUCT_IMAGE_SIZE', Tools::getValue('CN_PRODUCT_IMAGE_SIZE'))
        ) {
            $isLogsEnabled = Configuration::get('CN_LOGS_STATUS');
            if ($isLogsEnabled) {
                CnLogger::initLogger(_PS_VERSION_);
            }

            if ($activationResponse && !$activationResponse->isValid) {
                Configuration::updateValue('CN_ACTIVE', false);
                return $this->displayError($this->l('The settings are not valid.'));
            } else {
                return $this->displayConfirmation($this->l('The settings have been saved.'));
            }
        } else {
            return $this->displayError($this->l('An error occurred.'));
        }
    }

    private function newAccount()
    {
        $clientId = (string) Tools::getValue('CN_NEW_CLIENT_ID');
        $apiKey = (string) Tools::getValue('CN_NEW_API');
        $langId = (string) Tools::getValue('CN_SHOP_LANG_SELECTOR');
        $currencyId = (string) Tools::getValue('CN_SHOP_CURRENCY_SELECTOR');
        $isActive = (string) Tools::getValue('CN_NEW_ACCOUNT_ACTIVE');
        $checksum = hash_hmac('sha1', $clientId, $apiKey);
        $lang = Language::getLanguage($langId);
        $langName = $lang['name'];
        $currency = Currency::getCurrency($currencyId);
        $currencyName = $currency['name'];


        $cnAccounts = CnAccountUtils::getAccounts();
        $account = (object) array(
            'client_id' => $clientId,
            'api_key' => $apiKey,
            'lang' => $langId,
            'currency' => $currencyId,
            'is_active' => $isActive,
            'checksum' => $checksum,
            'lang_name' => $langName,
            'currency_name' => $currencyName,
        );

        if ($isActive == '1') {
            $activationResponse = $this->activateAccount($account);
            if (!$activationResponse || !$activationResponse->isValid) {
                return $this->displayError($this->l('An error occurred while activating your module.'));
            }
        }

        array_push($cnAccounts, $account);
        Configuration::updateValue('CN_ACCOUNTS', json_encode($cnAccounts));
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function getEncodedRegisterActiveAddressFields()
    {
        $cn_available_register_fields_address = CnUserUtils::getAvailableRegisterFieldsAddress();
        return $this->getActiveRegisterFields($cn_available_register_fields_address);
    }

    private function getEncodedRegisterActiveFields()
    {
        $cn_available_register_fields = CnUserUtils::getAvailableRegisterFieldsCustomer();
        return $this->getActiveRegisterFields($cn_available_register_fields);
    }

    private function getActiveRegisterFields($availableFields)
    {
        $activeFields = array();
        foreach ($availableFields as $key => $field) {
            $submitedField = Tools::getValue($field["Field"]);
            if ($submitedField === '1') {
                array_push($activeFields, $field);
            }
        }
        return $activeFields;
    }

    private function activateAccount($account)
    {
        $apiEndpoint = $this->applicationConfig->apiProtocol . '://'
        . $this->applicationConfig->apiDomain . ':'
        . $this->applicationConfig->apiPort;
        return CnAccountUtils::activateAccount(
            $account,
            $this->version,
            $apiEndpoint,
            false,
            Configuration::get('CN_SERVICE_WORKER_URL'),
            Configuration::get('CN_OPT_OUT_URL')
        );
    }

    private function deleteAccount($account_index)
    {
        $cnAccounts = CnAccountUtils::getAccounts();

        if (count($cnAccounts) >= $account_index + 1) {
            array_splice($cnAccounts, $account_index, 1);
            Configuration::updateValue('CN_ACCOUNTS', json_encode($cnAccounts));
        } else {
            return $this->displayError($this->l('The account could not be deleted.'));
        }
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function switchAccountStatus($account_index)
    {
        $result = CnAccountUtils::switchAccountStatus($account_index);
        if (!$result) {
            return $this->displayError($this->l('An error occurred while activating your account.'));
        }
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function newBanner()
    {
        $bannerId = (string) Tools::getValue('CN_BANNER_ID');
        $bannerType = (string) Tools::getValue('CN_BANNER_TYPE');
        $bannerIsActive = (string) Tools::getValue('CN_BANNER_ACTIVE');

        $cnBanners = $this->getBanners(null);

        array_push($cnBanners, array(
            'banner_id' => $bannerId,
            'banner_type' => $bannerType,
            'banner_is_active' => $bannerIsActive,
        ));

        Configuration::updateValue('CN_BANNERS', json_encode($cnBanners));
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function deleteBanner($banner_index)
    {
        $cnBanners = $this->getBanners(null);

        if (count($cnBanners) >= $banner_index + 1) {
            array_splice($cnBanners, $banner_index, 1);
            Configuration::updateValue('CN_BANNERS', json_encode($cnBanners));
        } else {
            return $this->displayError($this->l('The banner could not be deleted.'));
        }
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function enableBanner($banner_index)
    {
        $cnBanners = $this->getBanners(null);

        if (count($cnBanners) >= $banner_index + 1) {
            $isActive = $cnBanners[$banner_index]->banner_is_active;
            if ($isActive == "1") {
                $cnBanners[$banner_index]->banner_is_active = "0";
            } elseif ($isActive == "0") {
                $cnBanners[$banner_index]->banner_is_active = "1";
            }
            Configuration::updateValue('CN_BANNERS', json_encode($cnBanners));
        } else {
            return $this->displayError($this->l('The banner could not be activated.'));
        }
        $redirectPath = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        Tools::redirectAdmin($redirectPath);
    }

    private function getBanners($banner_type)
    {
        $cnBanners = array();
        $cnConfigBanners = Configuration::get('CN_BANNERS');

        if (!is_null($cnConfigBanners) && is_array(json_decode($cnConfigBanners))) {
            $cnBanners = json_decode($cnConfigBanners);
        }

        if (!is_null($banner_type)) {
            foreach ($cnBanners as $key => $banner) {
                if ($banner->banner_type != $banner_type) {
                    unset($cnBanners[$key]);
                }
            }
        }

        return $cnBanners;
    }

    private function displayContent($message)
    {

        $cnBanners = $this->getBanners(null);
        $cnAccounts = CnAccountUtils::getAccounts();

        $cn_register_fields_customer = $this->getRegisterFields('customer');
        $cn_register_fields_address = $this->getRegisterFields('address');

        $baseActionUrl = $this->context->link->getAdminLink('AdminModules')
        . '&configure=' . $this->name
        . '&module_name=' . $this->name;
        $newConnectifAccountUrl = $baseActionUrl . '&new_connectif_account';
        $deleteConnectifAccountUrl = $baseActionUrl . '&delete_connectif_account&account_index=';
        $switchAccountStatusUrl = $baseActionUrl . '&switch_connectif_account_status&account_index=';
        $deleteBannerUrl = $baseActionUrl . '&deletebanner&banner_index=';
        $enableBannerUrl = $baseActionUrl . '&enablebanner&banner_index=';
        $updateBannerUrl = $baseActionUrl . '&updatebanner';

        $imageSizesAvailable = ImageType::getImagesTypes();

        $this->context->smarty->assign(array(
            'module_name' => $this->name,
            'message' => $message,
            'CN_CLIENT_ID' => Configuration::get('CN_CLIENT_ID'),
            'CN_API' => Configuration::get('CN_API'),
            'CN_PRESTA' => Configuration::get('CN_PRESTA'),
            'CN_ACTIVE' => Configuration::get('CN_ACTIVE'),
            'CN_REGISTER_EVENT' => Configuration::get('CN_REGISTER_EVENT'),
            'CN_SEARCH_EVENT' => Configuration::get('CN_SEARCH_EVENT'),
            'CN_ROUND_PRICE' => Configuration::get('CN_ROUND_PRICE'),
            'CN_LOGS_STATUS' => Configuration::get('CN_LOGS_STATUS'),
            'CN_MULTILANG_STATUS' => Configuration::get('CN_MULTILANG_STATUS'),
            'CN_CART_STATUS_EVENT' => Configuration::get('CN_CART_STATUS_EVENT'),
            'CN_NEWSLETTER_EVENT' => Configuration::get('CN_NEWSLETTER_EVENT'),
            'CN_LOGIN_EVENT' => Configuration::get('CN_LOGIN_EVENT'),
            'CN_PRODUCT_REFERENCE' => Configuration::get('CN_PRODUCT_REFERENCE'),
            'CN_PURCHASE_EVENT' => Configuration::get('CN_PURCHASE_EVENT'),
            'CN_SERVICE_WORKER_URL' => Configuration::get('CN_SERVICE_WORKER_URL'),
            'CN_OPT_OUT_URL' => Configuration::get('CN_OPT_OUT_URL'),
            'lang' => Context::getContext()->language->iso_code,
            'CN_BANNERS' => $cnBanners,
            'CN_ACCOUNTS' => $cnAccounts,
            'new_connectif_account_url' => $newConnectifAccountUrl,
            'delete_connectif_account_url' => $deleteConnectifAccountUrl,
            'switch_account_status_url' => $switchAccountStatusUrl,
            'delete_banner_url' => $deleteBannerUrl,
            'enable_banner_url' => $enableBannerUrl,
            'update_banner_url' => $updateBannerUrl,
            'CN_REGISTER_FIELDS' => $cn_register_fields_customer,
            'CN_REGISTER_FIELDS_ADDRESS' => $cn_register_fields_address,
            'CN_IMAGE_SIZES_AVAILABLE' => $imageSizesAvailable,
            'CN_PRODUCT_IMAGE_SIZE' => Configuration::get('CN_PRODUCT_IMAGE_SIZE'),
        ));
    }

    private function getRegisterFields($type)
    {
        if ($type === 'customer') {
            $cn_available_register_fields = CnUserUtils::getAvailableRegisterFieldsCustomer();
            $activeFieldsConfig = Configuration::get('CN_ACTIVE_REGISTER_FIELDS');
            if (!Configuration::get('CN_ACTIVE_REGISTER_FIELDS')) {
                $activeFieldsConfig = json_encode(array());
            }
        } elseif ($type === 'address') {
            $cn_available_register_fields = CnUserUtils::getAvailableRegisterFieldsAddress();
            $activeFieldsConfig = Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDR');
            if (!Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDR')) {
                $activeFieldsConfig = json_encode(array());
            }
        } else {
            return array();
        }
        if (!is_null($activeFieldsConfig) && is_array(json_decode($activeFieldsConfig))) {
            $activeFields = json_decode($activeFieldsConfig);
        } else {
            $activeFields = array();
        }
        $cn_register_fields = array();
        foreach ($cn_available_register_fields as $key => $field) {
            if (CnUtils::findPropertyInArray($activeFields, "Field", $field["Field"])) {
                array_push($cn_register_fields, (array(
                    'name' => $field["Field"],
                    'isActive' => true,
                )));
            } else {
                array_push($cn_register_fields, (array(
                    'name' => $field["Field"],
                    'isActive' => false,
                )));
            }
        }
        return $cn_register_fields;
    }

    private function getCurrentClientID()
    {
        if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
            // Get current language
            $language = $this->context->language->id;
            // Get current currency
            $currency = $this->context->currency;
            // Get client_id associated with those values
            $accountConfiguration = $this->getMultilangConfiguration($language, $currency);
            if ($accountConfiguration && $accountConfiguration->client_id) {
                return $accountConfiguration->client_id;
            }
            return null;
        } else {
            return Configuration::get('CN_CLIENT_ID');
        }
    }

    private function getCurrentApiKey()
    {
        if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
            // Get current language
            $language = $this->context->language->id;
            // Get current currency
            $currency = $this->context->currency;
            // Get client_id associated with those values
            $accountConfiguration = $this->getMultilangConfiguration($language, $currency);
            if ($accountConfiguration && $accountConfiguration->api_key) {
                return $accountConfiguration->api_key;
            }
            return null;
        } else {
            return Configuration::get('CN_API');
        }
    }

    private function getMultilangConfiguration($language, $currency)
    {
        $cnMultilangConfigurationsArray = CnAccountUtils::getAccounts();
        $cnAccountConfiguration = null;
        foreach ($cnMultilangConfigurationsArray as $key => $accountConfiguration) {
            if ($accountConfiguration->lang == (string) $language
                && $accountConfiguration->currency == (string) $currency->id) {
                $cnAccountConfiguration = $cnMultilangConfigurationsArray[$key];
                break;
            }
        }
        return $cnAccountConfiguration;
    }

    private function isAnyAccountActive()
    {
        if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
            $accounts = CnAccountUtils::getAccounts();
            $isAnyActive = false;
            foreach ($accounts as $key => $account) {
                if ($account->is_active) {
                    $isAnyActive = true;
                    break;
                }
            }
            return $isAnyActive;
        } else {
            return Configuration::get('CN_ACTIVE') ? true : false;
        }
    }

    // BACK OFFICE HOOKS

    /**
     * Hook for back office dashboard
     */

    /**
     * Action Authentication
     * Set cookie to display auth action tags on display top hook
     */
    public function hookActionCustomerAccountAdd($params)
    {
        if ($this->checkConfiguration()) {
            setcookie('cn_account_add', true, 0, '/');
        }
    }

    /**
     * Action Authentication
     * Set cookie to display auth action tags on display top hook
     */
    public function hookActionAuthentication($params)
    {
        if ($this->checkConfiguration()) {
            setcookie('cn_account_auth', true, 0, '/');
        }
    }

    /**
     * Display order confirmation hook
     * To get all info on purchase
     */
    public function hookDisplayOrderConfirmation($params)
    {

        $isPurchaseEventEnabled = Configuration::get('CN_PURCHASE_EVENT');
        if (!$this->checkConfiguration() || !$isPurchaseEventEnabled) {
            return false;
        }

        $cart = $this->context->cart;
        $allCnProducts = array();

        if (isset($params['objOrder'])) {
            $order = $params['objOrder'];
        } elseif (isset($params['order'])) {
            $order = $params['order'];
        } else {
            return false;
        }
        $products = $order->getProducts();

        foreach ($products as $pro) {
            $isProductReference = Configuration::get('CN_PRODUCT_REFERENCE');
            $cnProduct = CnProductUtils::getProductDetails($pro['id_product'], $this->context, $isProductReference);
            //Price is total when more than one product
            if (isset($pro['total_wt'])) {
                $cnProduct['price'] = $pro['total_wt'];
            }
            if (isset($pro['product_quantity'])) {
                $cnProduct['quantity'] = $pro['product_quantity'];
            } else {
                $cnProduct['quantity'] = $pro['quantity'];
            }

            array_push($allCnProducts, $cnProduct);
        }

        $this->context->smarty->assign('purchase_products', $allCnProducts);
        $this->context->smarty->assign('purchase_cart_id', $order->id_cart);
        $this->context->smarty->assign('purchase_total_quantity', $cart->getNbProducts($order->id_cart));
        $this->context->smarty->assign('purchase_total_price', $order->total_paid);
        $this->context->smarty->assign('payment_method', $order->payment);
        $this->context->smarty->assign('purchase_id', $order->id);
        $this->context->smarty->assign('purchase_date', date(DATE_ISO8601, strtotime($order->date_add)));
        $this->context->smarty->assign('purchase_done', true);

        return $this->display(__FILE__, 'views/templates/hooks/cn_purchase.tpl');
    }

    /**
     * Actrion Search Hook
     * To get search terms on search action
     */
    public function hookActionSearch($params)
    {
        if ($this->checkConfiguration() && isset($_REQUEST['controller'])) {
            $searchTerm = $params['expr'];
            if (!isset($this->context->connectif)) {
                $this->context->connectif = new stdClass();
            }
            $this->context->connectif->searchTerm = $searchTerm;
        }
        return true;
    }

    /**
     * Top hook
     * To show cart status tags
     */
    public function hookTop()
    {
        $isSetHookExec = isset($this->context->smarty->tpl_vars['hook_top_exec']);
        $isHookTopExec = $isSetHookExec ? $this->context->smarty->tpl_vars['hook_top_exec'] : false;
        if ($this->checkConfiguration() && !$isHookTopExec) {
            /*CART*/
            $cart = $this->context->cart;
            $cartExist = false;
            $allCnProducts = array();
            $isCartStatusEventEnabled = Configuration::get('CN_CART_STATUS_EVENT');
            if ($cart != null && $this->context->cart->id && $isCartStatusEventEnabled) {
                $cartExist = true;
                $products = $cart->getProducts();

                foreach ($products as $pro) {
                    $isProductReference = Configuration::get('CN_PRODUCT_REFERENCE');
                    $cnProduct = CnProductUtils::getProductDetails(
                        $pro['id_product'],
                        $this->context,
                        $isProductReference
                    );
                    //Price is total when more than one product
                    if (isset($pro['total_wt'])) {
                        $cnProduct['price'] = $pro['total_wt'];
                    }
                    $cnProduct['quantity'] = $pro['quantity'];
                    array_push($allCnProducts, $cnProduct);
                }

                $this->context->smarty->assign('products', $allCnProducts);
                $this->context->smarty->assign('cart_id', $cart->id);
                $this->context->smarty->assign('total_quantity', $cart->getNbProducts($cart->id));
                $this->context->smarty->assign('total_price', $cart->getOrderTotal(true));
            }

            $this->context->smarty->assign('cart_exist', $cartExist);
            /*END CART*/

            /*ACCOUNT ADD*/
            $this->context->smarty->assign('is_account_add', false);
            $isRegisterEventEnabled = Configuration::get('CN_REGISTER_EVENT');
            if (isset($_COOKIE['cn_account_add']) && $isRegisterEventEnabled) {
                $userDetails = CnUserUtils::getUserDetails($this->context);
                $this->context->smarty->assign('account_add', $userDetails);
                $this->context->smarty->assign('is_account_add', true);
                setcookie('cn_account_add', false, time() - 3600, '/');
            } elseif (isset($_COOKIE['cn_account_add']) && !$isRegisterEventEnabled) {
                setcookie('cn_account_add', false, time() - 3600, '/');
            }
            /*END ACCOUNT ADD*/

            /*ACCOUNT AUTH*/
            $this->context->smarty->assign('is_account_auth', false);
            $isLoginEventEnabled = Configuration::get('CN_LOGIN_EVENT');
            if (isset($_COOKIE['cn_account_auth']) && $isLoginEventEnabled) {
                $userDetails = CnUserUtils::getUserDetails($this->context);
                $this->context->smarty->assign('account_auth', $userDetails);
                $this->context->smarty->assign('is_account_auth', true);
                setcookie('cn_account_auth', false, time() - 3600, '/');
            } elseif (isset($_COOKIE['cn_account_auth']) && !$isLoginEventEnabled) {
                setcookie('cn_account_auth', false, time() - 3600, '/');
            }
            /*END ACCOUNT AUTH*/

            /*CLIENT INFO*/
            $userDetails = CnUserUtils::getUserDetails($this->context);

            $clientEmail = array_key_exists('email', $userDetails) ? $userDetails['email'] : '';
            $clientName = array_key_exists('firstName', $userDetails) ? $userDetails['firstName'] : '';
            $clientSurname = array_key_exists('lastName', $userDetails) ? $userDetails['lastName'] : '';
            $clientBirthday = array_key_exists('birthday', $userDetails) ? $userDetails['birthday'] : '';
            $clientEmailStatus = array_key_exists('client_email_status', $userDetails) ?
            $userDetails['client_email_status'] : '';
            $clientExtendedPoperties = array_key_exists('extendedProperties', $userDetails) ?
            $userDetails['extendedProperties'] : array();

            $this->context->smarty->assign('client_email', $clientEmail);
            $this->context->smarty->assign('client_name', $clientName);
            $this->context->smarty->assign('client_surname', $clientSurname);
            $this->context->smarty->assign('client_birthday', $clientBirthday);
            $this->context->smarty->assign('client_email_status', $clientEmailStatus);
            $this->context->smarty->assign('client_extended_properties', $clientExtendedPoperties);
            $this->context->smarty->assign('tracker_id', '');
            /*END CLIENT INFO*/

             /*PAGE CATEGORY*/
             $categories = array();
            if (Tools::getValue('controller') == "category") {
                $controller = Context::getContext()->controller;
                if (method_exists($controller, 'getCategory')) {
                    $categoryId = $controller->getCategory()->id;
                    $category = self::concatCategoryPath($categoryId);
                    if ($category != '') {
                        array_push($categories, $category);
                    }
                }
            }
             $this->context->smarty->assign('categories', $categories);
             /*END PAGE CATEGORY*/

            $this->context->smarty->assign('hook_top_exec', true);
            $this->context->smarty->assign('module_templates', dirname(__FILE__) . '/views/templates/');
            return $this->display(__FILE__, 'views/templates/hooks/cn_top_hook.tpl');
        }
    }

    private static function concatCategoryPath($categoryId)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $category_list = array();

        $category = new Category((int) $categoryId, $id_lang);

        if (Validate::isLoadedObject($category) && (int) $category->active === 1) {
            foreach ($category->getParentsCategories($id_lang) as $parent_category) {
                if (isset($parent_category['name'], $parent_category['active'])
                    && (int) $parent_category['active'] === 1
                ) {
                    $category_list[] = (string) $parent_category['name'];
                }
            }
        }

        if (empty($category_list)) {
            return '';
        }

        return '/' . implode('/', array_reverse($category_list));
    }

    /**
     * Product hook
     * To show product details tags
     */
    public function hookProductFooter(array $params)
    {
        if ($this->checkConfiguration()) {
            $pro = $params['product'];
            $isProductReference = Configuration::get('CN_PRODUCT_REFERENCE');
            $productId = isset($pro->id) ? $pro->id : $pro['id'];
            if ($productId) {
                $cnProduct = CnProductUtils::getProductDetails($productId, $this->context, $isProductReference);
                $this->context->smarty->assign('product', $cnProduct);
                $this->context->smarty->assign('module_templates', dirname(__FILE__) . '/views/templates/');
                $this->context->smarty->assign('content_div_styles', '');
                $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayProductFooter'));
                return $this->display(__FILE__, 'views/templates/hooks/cn_footer_product.tpl');
            }
        }
    }

    /**
     * Footer hook
     * To load Connectif client script
     */
    public function hookDisplayFooter()
    {
        if ($this->checkConfiguration()) {
            /*SEARCH KEY*/
            $isSearchEventEnabled = Configuration::get('CN_SEARCH_EVENT');
            $this->context->smarty->assign('search_done', false);
            if (isset($this->context->connectif)
                && isset($this->context->connectif->searchTerm)
                && $isSearchEventEnabled) {
                $this->context->smarty->assign('search_term', $this->context->connectif->searchTerm);
                $this->context->smarty->assign('search_done', true);
                unset($this->context->connectif->searchTerm);
            } elseif (isset($this->context->connectif)
                && isset($this->context->connectif->searchTerm)
                && !$isSearchEventEnabled) {
                unset($this->context->connectif->searchTerm);
            }
            /*END SEARCH KEY*/

            //NEWSLETTER
            $isNewsletterEventEnabled = Configuration::get('CN_NEWSLETTER_EVENT');
            $this->context->smarty->assign('is_newsletter_subscribe', false);
            if (Tools::isSubmit('submitNewsletter') && $isNewsletterEventEnabled) {
                $email = Tools::getValue('email');
                if (!empty($email) && Validate::isEmail(Tools::getValue('email'))) {
                    $email = pSQL(Tools::getValue('email'));
                    if (Tools::getValue('action') == '0') {
                        $this->context->smarty->assign('is_newsletter_subscribe', true);
                        $this->context->smarty->assign('newsletter_email', $email);
                    }
                }
            }

            $this->context->smarty->assign('module_templates', dirname(__FILE__) . '/views/templates/');
            $this->context->smarty->assign('content_div_styles', 'display: inline-block;');
            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayFooter'));
            return $this->display(__FILE__, 'views/templates/hooks/cn_footer_hook.tpl');
        }
    }

    public function hookDisplayHeader()
    {
        if ($this->checkConfiguration()) {
            $clientId = $this->getCurrentClientID();
            $this->context->smarty->assign(array(
                'client_id' => $clientId,
                'cdn_client_script_path' => $this->cdnClientScriptPath,                
                /* REMARK: api_base and api_protocol are no longer used in the template.
                 * cdnClientScriptPath is used now instead starting module version 1.2.7. 
                 * However we need to keep the two variables to avoid issues with Prestashop installation 
                 * where performance settings are configured to never clear template cache */
                'api_base' => $this->apiBase,
                'api_protocol' => $this->apiProtocol
            ));

            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayHeader'));
            $this->context->smarty->assign('content_div_styles', '');
            $this->context->smarty->assign('module_templates', dirname(__FILE__) . '/views/templates/');
            return $this->display(__FILE__, 'views/templates/hooks/cntracking.tpl');
        }
    }

    public function hookHeader()
    {
        return $this->hookDisplayHeader();
    }

    /**Generic
     * Generic Connectif custom hook to display basic tags on all pages
     */
    public function hookDisplayConnectifTags()
    {
        return $this->hookTop();
    }

    /**
     * Check Configuration
     * To avoid module actions when disabled
     */
    private function checkConfiguration()
    {
        $isActive = false;
        if (Configuration::get('CN_MULTILANG_STATUS') == '1') {
            // Get current language
            $language = $this->context->language->id;
            // Get current currency
            $currency = $this->context->currency;
            // Get client_id associated with those values
            $accountConfiguration = $this->getMultilangConfiguration($language, $currency);
            if ($accountConfiguration && $accountConfiguration->is_active) {
                $isActive = $accountConfiguration->is_active;
            }
        } else {
            $isActive = Configuration::get('CN_ACTIVE');
        }
        return $isActive;
    }

    /**
     * Only Dynamic content hooks
     */

    public function hookDisplayHome()
    {
        if ($this->checkConfiguration()) {
            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayHome'));
            $this->context->smarty->assign('content_div_styles', '');
            return $this->display(__FILE__, 'views/templates/hooks/cn_dynamic_content.tpl');
        }
    }

    public function hookDisplayLeftColumn()
    {
        if ($this->checkConfiguration()) {
            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayLeftColumn'));
            $this->context->smarty->assign('content_div_styles', '');
            return $this->display(__FILE__, 'views/templates/hooks/cn_dynamic_content.tpl');
        }
    }

    public function hookDisplayRightColumn()
    {
        if ($this->checkConfiguration()) {
            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayRightColumn'));
            $this->context->smarty->assign('content_div_styles', '');
            return $this->display(__FILE__, 'views/templates/hooks/cn_dynamic_content.tpl');
        }
    }

    public function hookDisplayTopColumn()
    {
        if ($this->checkConfiguration()) {
            $this->context->smarty->assign('cn_banners', $this->getBanners('DisplayTopColumn'));
            $this->context->smarty->assign('content_div_styles', '');
            return $this->display(__FILE__, 'views/templates/hooks/cn_dynamic_content.tpl');
        }
    }
}
