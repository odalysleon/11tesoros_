<?php
/**
 * Created by PhpStorm.
 * User: alcides
 * Date: 4/6/2018
 * Time: 3:26 p.m.
 */

if ( !defined( '_PS_VERSION_' ) ) {
    exit;
}

class Aporvino extends Module
{

    public function __construct()
    {
        $this->name = 'aporvino';
        $this->tab = 'Integración con Aporvino';
        $this->version = '1.0.0';
        $this->author = 'Alcides Rodriguez';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array( 'min' => '1.7', 'max' => _PS_VERSION_ );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l( 'Aporvino' );
        $this->description = $this->l( 'Modulo para la integración con Aporvino' );

        $this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' );

        if ( !Configuration::get( 'aporvino' ) )
            $this->warning = $this->l( 'Problemas con el modulo Aporvino' );
    }

    public function install()
    {
        if ( !parent::install() || !$this->registrationHook() )
            return false;
        return true;
    }

    public function uninstall()
    {
        if ( !parent::uninstall() )
            return false;
        return true;
    }

    private function registrationHook()
    {
        if ( $this->registerHook( 'actionPaymentConfirmation' ) )
            return true;
        return false;
    }

    public function hookActionPaymentConfirmation( $params )
    {

        if ( isset( $params[ 'id_order' ] ) && isset( $params[ 'cart' ] ) ) {
            $cart = $params[ 'cart' ];
            $products = $cart->getProducts();
            $product = new Product( $products[ 0 ][ 'id_product' ] );
            $cat = self::getCategoryLevel2( $product );

            if ( $cat == 'vinos' ) {
                $order = new Order( $params[ 'id_order' ] );
                $customer = new Customer( $order->id_customer );
                $addressDelivery = new Address( $order->id_address_delivery );
                $country = $addressDelivery->country;
                if ( $country != 'España' ) {
                    $shipping = $order->getShipping();
                    foreach ( $shipping as $value ) {
                        $pos = strpos( strtolower( $value[ 'carrier_name' ] ), 'aporvino' );
                        if ( $pos || $pos === 0 ) {
                            $noReturn = true;
                            break;
                        }
                    }
                    if ( !isset( $noReturn ) )
                        return false;
                    $xml = $this->getAporvinoXml();
                    $xml = str_replace( 'id_order', $order->id, $xml );
                    $date = new DateTime();
                    $date = $date->format( 'Y-m-d H:i:s' );
                    $xml = str_replace( 'date_now', $date, $xml );
                    $xml = str_replace( 'name_address', $addressDelivery->firstname . ' ' . $addressDelivery->lastname, $xml );
                    $phone = ( $addressDelivery->phone_mobile ) ? $addressDelivery->phone_mobile : $addressDelivery->phone;
                    $xml = str_replace( 'phone_address', $phone, $xml );
                    $xml = str_replace( 'email_address', $customer->email, $xml );
                    $xml = str_replace( 'address_address', $addressDelivery->address1, $xml );
                    $xml = str_replace( 'cp_address', $addressDelivery->postcode, $xml );
                    $xml = str_replace( 'city_address', $addressDelivery->city, $xml );
                    $state = new State( $addressDelivery->id_state );
                    $xml = str_replace( 'province_address', $state->name, $xml );
                    $xml = str_replace( 'country_address', $addressDelivery->country, $xml );

                    $addressInvoice = new Address( $order->id_address_invoice );
                    $xml = str_replace( 'name2_address', $addressInvoice->firstname . ' ' . $addressInvoice->lastname, $xml );
                    $phone = ( $addressInvoice->phone_mobile ) ? $addressInvoice->phone_mobile : $addressInvoice->phone;
                    $xml = str_replace( 'phone2_address', $phone, $xml );
                    $xml = str_replace( 'email2_address', $customer->email, $xml );
                    $xml = str_replace( 'address2_address', $addressInvoice->address1, $xml );
                    $xml = str_replace( 'cp2_address', $addressInvoice->postcode, $xml );
                    $xml = str_replace( 'city2_address', $addressInvoice->city, $xml );
                    $state = new State( $addressInvoice->id_state );
                    $xml = str_replace( 'province2_address', $state->name, $xml );
                    $xml = str_replace( 'country2_address', $addressInvoice->country, $xml );
                    $xml = str_replace( 'vat_address', "", $xml );

                    $xml = str_replace( 'amount_discounts', $order->total_discounts, $xml );
                    $xml = str_replace( 'amount_affiliate_data', 0, $xml );
                    $xml = str_replace( 'comment_text', "", $xml );
                    $xml = str_replace( 'shippping_price_value', $order->total_shipping, $xml );

                    $dom = new DOMDocument( "1.0", "UTF-8" );
                    $dom->loadXML( $xml );

                    foreach ( $products as $product ) {
                        $productNode = $dom->createElement( 'product' );
                        $node = $dom->createElement( "id" );
                        $node->appendChild($dom->createCDATASection($product[ 'id_product' ]));
                        $productNode->appendChild( $node );
                        $node = $dom->createElement( 'name' );
                        $node->appendChild($dom->createCDATASection($product[ 'name' ]));
                        $productNode->appendChild( $node );
                        $node = $dom->createElement( 'quantity');
                        $node->appendChild($dom->createCDATASection($product[ 'cart_quantity' ]));
                        $productNode->appendChild( $node );
                        $node = $dom->createElement( 'price');
                        $node->appendChild($dom->createCDATASection($product[ 'price' ]));
                        $productNode->appendChild( $node );
                        $dom->getElementsByTagName( 'products' )->item( 0 )->appendChild( $productNode );
                    }

//                    if ( $dom->save( __DIR__.'/aporvino.xml' ) ){
//                        Mail::Send( $cart->id_lang, 'oil-order', 'Prueba, ejecutando modulo aporvino xml gurdado', [], [ 'alcides_rodriguez@unah.edu.cu', 'alcides@arena-park.ch', 'angel.diaz@tidinternationalgroup.com' ], 'Prueba', 'noreply@pickeo.net', 'prueba', null, null, _PS_MAIL_DIR_ );
//                        Mail::Send( $cart->id_lang, 'oil-order', 'Correo de prueba, Xml para Aporvino', [], [ 'alcidesrh@gmail.com', 'angel.diaz@tidinternationalgroup.com' ], 'Correo de prueba', 'noreply@pickeo.net', 'noreply', [__DIR__ .'/aporvino.xml'], null, _PS_MAIL_DIR_ );
//                    }
                    $dom->save( __DIR__.'/aporvino.xml' );
                    $xml = $dom->saveXML();

                   return $this->sendXmlOverPost( $xml );
                }
            }
        }
        return true;
    }

    static public function getCategoryLevel2( $product )
    {
        $categories = $product->getCategories();
        foreach ( $categories as $id ) {
            $category = new Category( $id );
            if ( $category->level_depth != 1 ) {
                if ( $category->level_depth > 2 ) {
                    $categories2 = $category->getParentsCategories();
                    foreach ( $categories2 as $category ) {
                        $category = new Category( $category[ 'id_category' ] );
                        if ( $category->level_depth == 2 ) {
                            return preg_replace( '/\s+/', '', strtolower( $category->getName() ) );
                        }
                    }

                }
                return preg_replace( '/\s+/', '', strtolower( $category->getName() ) );
            }
        }
    }

    public function sendXmlOverPost( $xml, $url = "https://www.aporvino.com/pickeo/comunicar-pedido.php" )
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );

        // For xml, change the content-type.
        curl_setopt( $ch, CURLOPT_HTTPHEADER, Array( "Content-Type: text/xml" ) );

        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $xml );

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // ask for results to be returned

        // Send to remote and return data to caller.
        $result = curl_exec( $ch );
        curl_close( $ch );
        return $result;
    }

    public function getAporvinoXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<order currency="EUR">
    <id>id_order</id>
    <date>date_now</date>
    <address>
        <name><![CDATA[name_address]]></name>
        <telf><![CDATA[phone_address]]></telf>
        <email><![CDATA[email_address]]></email>
        <address><![CDATA[address_address]]></address>
        <cp><![CDATA[cp_address]]></cp>
        <city><![CDATA[city_address]]></city>
        <province><![CDATA[province_address]]></province>
        <country><![CDATA[country_address]]></country>
    </address>
    <billing_address>
        <name><![CDATA[name2_address]]></name>
        <phone><![CDATA[phone2_address]]></phone>
        <email><![CDATA[email2_addressg]]></email>
        <address><![CDATA[address2_address]]></address>
        <cp><![CDATA[cp2_address]]></cp>
        <city><![CDATA[city2_address]]></city>
        <province_name><![CDATA[province2_address]]></province_name>
        <country><![CDATA[country2_address]]></country>
        <vat_number><![CDATA[vat_address]]></vat_number>
    </billing_address>
    <discounts>
        <amount><![CDATA[amount_discounts]]></amount>
        <amount_affiliate><![CDATA[amount_affiliate_data]]></amount_affiliate>
    </discounts>
    <comments><![CDATA[comment_text]]></comments>
    <products>        
    </products>
    <shipping_price>shippping_price_value</shipping_price>
</order>
        ';
    }

}