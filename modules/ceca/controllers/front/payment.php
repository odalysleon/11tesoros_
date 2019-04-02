<?php
/**
 * 2011-2017 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2016 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

/**
 * @since 1.5.0
 */
class CecaPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$this->display_column_right = false;
		parent::initContent();

		$cart = $this->context->cart;
		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');
		
		$idCart = $cart->id;
		//$secureKey = $cart->secure_key;
		$products = $cart->getProducts();
		$locale = Language::getIsoById($this->context->cookie->id_lang);
		
		$cartProducts = '';
		foreach ($products as $product)
		{
			$cartProducts .= '- '.$product['name'];
			if (isset($product['attributes']) && $product['attributes'] != '')
			{
				$arrayAttributes = preg_split('/, /', $product['attributes']);
				foreach ($arrayAttributes as $attribute)
					$cartProducts .= ' - '.$attribute;
			}
		
			$cartProducts .= '<br/><br/>';
		}
		
		//Language Virtual POS
		$tpvLang = '1';
		switch ($locale)
		{
			case 'es':
				$tpvLang = '1';
				break;
			case 'gb':
			case 'en':
			case 'nl':
				$tpvLang = '6';
				break;
			case 'ca':
				$tpvLang = '2';
				break;
			case 'fr':
				$tpvLang = '7';
				break;
			case 'de':
				$tpvLang = '8';
				break;
			case 'it':
				$tpvLang = '10';
				break;
			case 'pt':
				$tpvLang = '9';
				break;
			case 'gl':
				$tpvLang = '4';
				break;
		}
		
		if (Tools::getIsset('method'))
			$tpvElem = $this->module->getTpvElemById(Tools::getValue('method'));
		else
		{
			$tpvList = $this->module->getTpvElemsList(true, false);
			if ($tpvList && count($tpvList) > 0)
				$tpvElem = reset($tpvList);
			else
				return;
		}
			

		if($tpvElem['sandbox_mode'] == '1')
			$url_tpvv = Configuration::get('CECA_URL_TPVV_SIMULADOR');
		else
			$url_tpvv = Configuration::get('CECA_URL_TPVV_PROD');
		
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
			$tpvCurrencyId = (int)Currency::getIdByIsoCode($tpvElem['currency_code']);
		else
			$tpvCurrencyId = (int)Currency::getIdByIsoCodeNum($tpvElem['currency_code']);
		
		//Moneda del contexto
		$oldCurrency = $this->context->currency;
		if((int)$cart->id_currency != $tpvCurrencyId)
		{
			if($tpvCurrencyId)
				$tpvCurrency = new Currency($tpvCurrencyId);
			else
				$tpvCurrency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

			//Cambiamos la moneda en el contexto por la del TPV
			$this->context->currency = $tpvCurrency;

			//Descudra decimales NO USAR
			//$amount = (float) Tools::convertPriceFull($cart->getOrderTotal(true, Cart::BOTH), $cartCurrency, $tpvCurrency) * 100;
		}

		//Calculamos el total del carrito
		$amount = $cart->getOrderTotal(true, Cart::BOTH) * 100;

		//Volvemos a dejar la moneda del contexto incial
		if((int)$cart->id_currency != $tpvCurrencyId)
			$this->context->currency = $oldCurrency;

		$exponente = Configuration::get('CECA_EXPONENT');
		$urlOK = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/ceca/resultRedirect.php?result=0&cartId='.$idCart.($tpvElem['iframe_mode']==1?'&content_only=1':'');
		$urlNOK = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/ceca/resultRedirect.php?result=1&cartId='.$idCart.($tpvElem['iframe_mode']==1?'&content_only=1':'');
		$num_operacion = $idCart.'_'.$tpvElem['id'].'_payment_'.date('His');

		// Display all and exit
		$this->context->smarty->assign(array(
			'url_tpvv' => $url_tpvv,
			'MerchantID' => $tpvElem['merchant_id'],
			'AcquirerBIN' => $tpvElem['acquirer_bin'],
			'TerminalID' => $tpvElem['terminal_id'],
			'Num_operacion' => $num_operacion,
			'Importe' => $amount,
			'TipoMoneda' => $tpvElem['currency_code'],
			'Exponente' => $exponente,
			'url_OK' => $urlOK,
			'url_NOK' => $urlNOK,
			'Firma' => Ceca::getFirmaSHA2($num_operacion, $amount, $tpvElem, $exponente, $urlOK, $urlNOK),
			'frameWidth' => $tpvElem['iframe_width'],
			'locale' => $tpvLang,
			'cart_products' => $cartProducts,
			'showInIframe' => ($tpvElem['iframe_mode']==1),
			'ps_module_dir' => _PS_MODULE_DIR_
		));
		
		$this->context->smarty->caching = false;
		Tools::clearCache($this->context->smarty);
		
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
			$this->setTemplate('module:'.$this->module->name.'/views/templates/front/redirect17.tpl');
		else
			$this->setTemplate('redirect.tpl');
	}
}
