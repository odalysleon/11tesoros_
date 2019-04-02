<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


class PickeoOilOrderModuleFrontController extends ModuleFrontController
{
    public $name = 'pickeo';

    public function postProcess()
    {

        if ( Tools::isSubmit( 'module' ) ) {
            $cart = new Cart( $this->context->cart->id );
            $customer = $this->context->customer;
            $param = $this->getOrderData( $cart, $customer );
            Mail::Send( $cart->id_lang, 'oil-order', 'Pedido de aceites', $param, 'jlrivas@pickeo.net', 'Pickeo Administrador', ( isset( $param[ 'email' ] ) ) ? $param[ 'email' ] : 'noreply@pickeo.net', ( isset( $param[ 'client_name' ] ) ) ? $param[ 'client_name' ] : '', null, null, _PS_MAIL_DIR_ );
            Tools::redirect( 'index.php?controller=order-confirmation?free_order=1' );
        }
        parent::initContent();
    }


    public function getOrderData( Cart $cart, Customer $client )
    {
        $data = [];
        $products = $cart->getProducts();
        if ( !empty( $products ) ) {
            $data[ '{client_name}' ] = $client->firstname . ' ' . $client->lastname;
            $data[ '{email}' ] = $client->email;
            $address = new Address( $cart->id_address_delivery );
            $data[ '{address}' ] = $address->address1 . '; ' . $address->city . ', ' . $address->country . '. Código Postal: ' . $address->postcode;
            $data[ '{quantity}' ] = count( $products );
            $data['{reference}'] = Order::generateReference();
            $productsHtml = "";
            foreach ( $products as $product ) {
                $image = Product::getCover( (int) $product[ 'id_product' ] );
                $image = new Image( $image[ 'id_image' ] );
                $product_data[ 'product_image' ] = _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
                $product_data[ "product_name" ] = $product[ 'name' ];
                $product_data[ 'price' ] = $product[ 'price_with_reduction' ];
                $product_data[ 'quantity' ] = $product[ 'cart_quantity' ];
                $productsHtml .= '
                <div>
                <div style="display: inline-block; margin-right: 15px">
                  <img src="'.$product_data[ 'product_image' ].'"  style="max-width: 150px;">
                </div>
            <div  style="display: inline-block;">
                <div>
                    <strong>Nombre del producto:</strong>
                    <label style="display: inline">'.$product_data[ "product_name" ].'</label>
                </div>   
                <div>
                    <strong>Cantidad:</strong>
                    <label style="display: inline">'.$product_data[ 'quantity' ].'</label>
                </div>
            </div>
            <div style="border-bottom: 2px solid #1c8ca3; margin: 10px 0px"></div>            
            </div>';
            }
            $data['{products}'] = $productsHtml;
        }
        return $data;
    }
}
