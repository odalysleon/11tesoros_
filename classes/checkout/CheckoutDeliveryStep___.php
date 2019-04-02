<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class CheckoutDeliveryStep extends CheckoutDeliveryStepCore
{

    public function render(array $extraParams = array())
    {
        $options = $this->getCheckoutSession()->getDeliveryOptions();
        $newDeliveryOptions = [];
        if(!empty($options)){
            foreach ($options as $key => $value){
                $number = "";
                $string = "";
                $array = str_split($value['price']);
                foreach ($array as $char){
                    if(is_numeric($char) || $char == ','){
                        $number = $number.(string) $char;
                    }
                    else $string = $string.$char;
                }
                if($number)
                $value['price'] = '<span class="envialia-price">'.$number.'</span>'.$string;
                $newDeliveryOptions[$key] = $value;

            }
        }
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            array(
                'hookDisplayBeforeCarrier' => Hook::exec('displayBeforeCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'id_address' => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options' => $newDeliveryOptions,//$this->getCheckoutSession()->getDeliveryOptions(),
                'delivery_option' => $this->getCheckoutSession()->getSelectedDeliveryOption(),
                'recyclable' => $this->getCheckoutSession()->isRecyclable(),
                'recyclablePackAllowed' => $this->isRecyclablePackAllowed(),
                'delivery_message' => $this->getCheckoutSession()->getMessage(),
                'gift' => array(
                    'allowed' => $this->isGiftAllowed(),
                    'isGift' => $this->getCheckoutSession()->getGift()['isGift'],
                    'label' => $this->getTranslator()->trans(
                        'I would like my order to be gift wrapped %cost%',
                        array('%cost%' => $this->getGiftCostForLabel()),
                        'Shop.Theme.Checkout'
                    ),
                    'message' => $this->getCheckoutSession()->getGift()['message'],
                ),
            )
        );
    }
}
