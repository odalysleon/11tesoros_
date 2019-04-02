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

class Pickeo extends Module
{

    public function __construct()
    {
        $this->name = 'pickeo';
        $this->tab = 'Pickeo Opciones';
        $this->version = '1.0.0';
        $this->author = 'Alcides Rodriguez';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array( 'min' => '1.7', 'max' => _PS_VERSION_ );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l( 'Pickeo' );
        $this->description = $this->l( 'Modulos para la implementación de las características particulares de Pickeo' );

        $this->confirmUninstall = $this->l( 'Are you sure you want to uninstall?' );

        if ( !Configuration::get( 'pickeo' ) )
            $this->warning = $this->l( 'Problemas con el modulo Pickeo' );
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
        if ( $this->registerHook( 'actionCartSave' ) && $this->registerHook( 'actionCarrierProcess' ))
            return true;
        return false;
    }

    public function hookActionCartSave( $params )
    {
        if ( isset( $params[ 'cart' ] ) && !is_null( $params[ 'cart' ] ) ) {
            $cart = $params[ 'cart' ];
            $products = $cart->getProducts();
            if ( count( $products ) > 1 ) {
                $product = new Product( $products[ 0 ][ 'id_product' ] );
                $categoryCompare1 = self::getCategoryLevel2( $product );
                $index = count( $products ) - 1;
                $product = new Product( $products[ $index ][ 'id_product' ] );
                $categoryCompare2 = self::getCategoryLevel2( $product );
                if ( $categoryCompare1 != $categoryCompare2 ) {
                    $cart->deleteProduct( $products[ $index ][ 'id_product' ], $products[ $index ][ 'id_product_attribute' ] );
                    return false;
                }
            }
        }
    }

    public function hookActionCarrierProcess( $params )
    {
        $this->context->smarty->assign( 'aceites', false );
        if ( isset( $params[ 'cart' ] ) && !is_null( $params[ 'cart' ] ) ) {
            $products = $params[ 'cart' ]->getProducts();
            if ( !empty( $products ) ) {
                $product = new Product( $products[ 0 ][ 'id_product' ] );
                if ( self::getCategoryLevel2( $product ) == 'aceites' )
                    $this->context->smarty->assign( 'aceites', true );
            }
        }
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