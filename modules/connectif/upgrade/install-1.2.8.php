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

function upgrade_module_1_2_8($object)
{
    $dir_module = dirname(_PS_MODULE_DIR_);
    require_once $dir_module . '/modules/connectif/classes/cn-account-utils.php';

    if (Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDRESS')) {
        if (!Configuration::updateValue('CN_ACTIVE_REGISTER_FIELDS_ADDR', Configuration::get('CN_ACTIVE_REGISTER_FIELDS_ADDRESS'))) {
            return false;
        }
        if (!Configuration::deleteByName('CN_ACTIVE_REGISTER_FIELDS_ADDRESS')) {
            return false;
        }

    }
}
