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

class Pickeo_Envialia extends Module
{

    public function __construct()
    {
        $this->name = 'pickeo_envialia';
        $this->tab = 'Pickeo Envialia';
        $this->version = '1.0.0';
        $this->author = 'Alcides Rodriguez';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array( 'min' => '1.7', 'max' => _PS_VERSION_ );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l( 'Pickeo Envialia' );
        $this->description = $this->l( 'Modulos para la implementación de las características particulares de Envialia' );

        $this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' );

        if ( !Configuration::get( 'pickeo_envialia' ) )
            $this->warning = $this->l( 'Problemas con el modulo Envialia Horario' );
    }

    public function install()
    {
        if ( !parent::install() || !$this->registrationHook())
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
        if ( $this->registerHook( 'displayHeader' ) &&
            $this->registerHook( 'displayEnvialiaOptions' ))
            return true;
        return false;
    }

    public function hookDisplayHeader($params){
        $quantity = 0;
        if($this->context->controller->php_self == 'order'){
            $cart = $params[ 'cart' ];
            $products = $cart->getProducts();
            if ( !empty( $products ) ) {
                $flag = false;
                $quantity = 0;
                foreach ($products as $product){
                    $productAux = new Product( $product[ 'id_product' ] );
                    $cat = self::getCategoryLevel2( $productAux );
                    if (  $cat == 'vinos' || $cat ==  'aceites' ){
                        $quantity += $product['quantity'];
                        $flag = true;
                    }
                }
                if ( $flag ){
                    $options = $cart->getDeliveryOptionList();
                    if ( is_array( $options ) )
                        foreach ( $options as $array ) {
                            if ( is_array( $array ) )
                                foreach ( $array as $array2 ) {
                                    if ( isset($array2['carrier_list']) && is_array( $array2['carrier_list'] ) ){
                                        foreach ( $array2['carrier_list'] as $item ){
                                            $carrie = $item[ 'instance' ];
                                            if(strpos(strtolower($carrie->name) , 'envialia' ) !== false && strpos(strtolower($carrie->name) , 'internacional' ) === false){
                                                $this->context->controller->registerJavascript(
                                                    'modules-envialia',
                                                    'modules/'.$this->name.'/js/envialia.js',
                                                    ['position' => 'bottom', 'priority' => 150]
                                                );
                                                $break = true;
                                                break;
                                            }
                                        }
                                        if(isset($break))break;
                                    }
                                }
                        }
                }
            }
        }
        $charge = ceil($quantity/2)*2*1.21;
//        $charge += $charge*0.21;
        $this->context->smarty->assign( 'envialia_charge', $charge);
    }

    public function hookDisplayEnvialiaOptions( $params )
    {
        if ( strpos( strtolower( $params[ 'carrier' ][ 'name' ] ), 'envialia' ) !== false  && strpos(strtolower($params[ 'carrier' ][ 'name' ]) , 'internacional' ) === false) {
            $cart = $params[ 'cart' ];
            $products = $cart->getProducts();
            if ( !empty( $products ) ) {
                foreach ($products as $product) {
                    $productAux = new Product( $product[ 'id_product' ] );
                    $cat = self::getCategoryLevel2( $productAux );
                    if ( $cat == 'vinos' || $cat == 'aceites' ) {
                        return $this->display( __FILE__, 'views/templates/envialia-options.tpl' );
                    }
                }
            }
        }
    }
    public function getBoxCant($products){
        $cant = 0;
        foreach ($products as $product){
            $cant += $product['cart_quantity'];
        }
        return $cant;
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


}