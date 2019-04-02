<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2018 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_9($object)
{
    if (!Configuration::updateValue('CN_ROUND_PRICE', '')) {
        return false;
    }
    return true;
}
