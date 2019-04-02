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

function upgrade_module_1_1_0($object)
{
    if (!Configuration::updateValue('CN_MULTILANG_STATUS', '')) {
        return false;
    }
    return true;
}
