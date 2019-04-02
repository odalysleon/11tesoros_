<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Connectif
 * @copyright Copyright (c) 2017 Connectif
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class CnProductUtils
{

    const IN_STOCK = 'instock';
    const OUT_OF_STOCK = 'outofstock';

    public static function getProductReference($p)
    {
        return ($p['reference']);
    }
    public static function getProductId($p)
    {
        return ($p['id_product']);
    }

    public static function getProductDetails($productId, Context $context, $isProductReference)
    {
        $product = new Product($productId, true, $context->language->id);
        $productUrl = self::getProductUrl($product);
        $productTags = $product->getTags($context->language->id);
        $tagsArray = array();
        $cnProduct = array();
        if ($productTags != "") {
            $tagsArray = explode(', ', $productTags);
        }

        $productAccessories = $product->getAccessories($context->language->id);

        if (!$productAccessories) {
            $productAccessories = array();
        }

        if ($isProductReference) {
            $relatedProducts = array_map(
                'CnProductUtils::getProductReference',
                $productAccessories
            );
        } else {
            $relatedProducts = array_map(
                'CnProductUtils::getProductId',
                $productAccessories
            );
        }

        $imagePath = self::getProductImage($product);

        if ($product->reference) {
            $productId = $product->reference;
        } else {
            $productId = $product->id;
        }

        if (!$product->active || $product->visibility === 'none') {
            $availability = self::OUT_OF_STOCK;
        } else {
            $availability = ($product->checkQty(1)) ? self::IN_STOCK : self::OUT_OF_STOCK;
        }

        if (empty($product->manufacturer_name) && !empty($product->id_manufacturer)) {
            $brand = Manufacturer::getNameById($product->id_manufacturer);
        } else {
            $brand = $product->manufacturer_name;
        }

        $cnProduct['discounted_percentage'] = 0;
        if ($product->specificPrice && $product->specificPrice['reduction_type'] == 'percentage') {
            $cnProduct['discounted_percentage'] = $product->specificPrice['reduction'] * 100;
        }

        $isRoundUnitPriceEnabled = Configuration::get('CN_ROUND_PRICE');
        if($isRoundUnitPriceEnabled) {
            $cnProduct['unit_price'] = Tools::ps_round(self::getProductPrice($product->id, $context), 2);
            $cnProduct['unit_price_original'] = Tools::ps_round(self::getProductOriginalPrice($product->id, $context), 2);
            $cnProduct['unit_price_without_vat'] = Tools::ps_round(self::getProductPriceWithoutVat($product->id, $context), 2);
            $cnProduct['discounted_amount'] = Tools::ps_round(self::getProductDiscountedAmount($product->id, $context), 2);
        } else {
            $cnProduct['unit_price'] = self::getProductPrice($product->id, $context);
            $cnProduct['unit_price_original'] = self::getProductOriginalPrice($product->id, $context);
            $cnProduct['unit_price_without_vat'] = self::getProductPriceWithoutVat($product->id, $context);
            $cnProduct['discounted_amount'] = self::getProductDiscountedAmount($product->id, $context);
        }

        $cnProduct['name'] = $product->name;
        $cnProduct['product_id'] = $isProductReference ? $productId : $product->id;
        /*WARNING it have html tags*/
        $cnProduct['description'] = $product->description_short;
        $cnProduct['availability'] = $availability;
        $cnProduct['categories'] = self::getProductCategories($product);
        //TODO find this param on product object
        //$cnProduct['rating_value'] =  $pro['rate'];/*is this parameter product rating value?*/
        $cnProduct['image_url'] = $imagePath;
        $cnProduct['url'] = $productUrl;
        $cnProduct['brand'] = $brand;
        $cnProduct['tagsArray'] = $tagsArray;
        $cnProduct['relatedProductsArray'] = $relatedProducts;
        return $cnProduct;
    }

    public static function getProductCategories($product)
    {
        $categories = array();
        foreach ($product->getCategories() as $categoryId) {
            $category = self::concatCategoryPath($categoryId, $product);
            if($category != '') {
                array_push($categories, $category);
            }
        }

        return $categories;
    }

    private static function concatCategoryPath($categoryId, $product)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $category_list = array();

        $category = new Category((int) $categoryId, $id_lang);

        if (Validate::isLoadedObject($category) && (int) $category->active === 1) {
            foreach ($category->getParentsCategories($id_lang) as $parent_category) {
                $isInProductCat = in_array($parent_category['id_category'], $product->getCategories());
                if (isset($parent_category['name'], $parent_category['active'])
                    && (int) $parent_category['active'] === 1 && $isInProductCat
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

    public static function getProductUrl($product)
    {
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        if (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $link = new Link('https://', 'https://');
        } else {
            $link = new Link('http://', 'http://');
        }

        $url = $link->getProductLink($product, null, null, null, $id_lang, $id_shop);

        return $url;
    }

    public static function getProductPrice($id_product, $context)
    {
        $options = array(
            'include_tax' => true,
            'id_product_attribute' => null,
            'decimals' => 4,
            'divisor' => null,
            'only_reduction' => false,
            'user_reduction' => true,
            'quantity' => 1,
            'force_associated_tax' => false,
            'id_customer' => null,
            'id_cart' => null,
            'id_address' => null,
            'with_eco_tax' => true,
            'use_group_reduction' => true,
            'use_customer_price' => true,
        );
        $spo = null;

        $value = Product::getPriceStatic(
            (int) $id_product,
            $options['include_tax'],
            $options['id_product_attribute'],
            $options['decimals'],
            $options['divisor'],
            $options['only_reduction'],
            $options['user_reduction'],
            $options['quantity'],
            $options['force_associated_tax'],
            $options['id_customer'],
            $options['id_cart'],
            $options['id_address'],
            $spo,
            $options['with_eco_tax'],
            $options['use_group_reduction'],
            $context,
            $options['use_customer_price']
        );
        return $value;
    }

    public static function getProductPriceWithoutVat($id_product, $context)
    {
        $options = array(
            'include_tax' => false,
            'id_product_attribute' => null,
            'decimals' => 4,
            'divisor' => null,
            'only_reduction' => false,
            'user_reduction' => true,
            'quantity' => 1,
            'force_associated_tax' => false,
            'id_customer' => null,
            'id_cart' => null,
            'id_address' => null,
            'with_eco_tax' => false,
            'use_group_reduction' => true,
            'use_customer_price' => true,
        );
        $spo = null;

        $value = Product::getPriceStatic(
            (int) $id_product,
            $options['include_tax'],
            $options['id_product_attribute'],
            $options['decimals'],
            $options['divisor'],
            $options['only_reduction'],
            $options['user_reduction'],
            $options['quantity'],
            $options['force_associated_tax'],
            $options['id_customer'],
            $options['id_cart'],
            $options['id_address'],
            $spo,
            $options['with_eco_tax'],
            $options['use_group_reduction'],
            $context,
            $options['use_customer_price']
        );
        return $value;
    }

    public static function getProductDiscountedAmount($id_product, $context)
    {
        $options = array(
            'include_tax' => false,
            'id_product_attribute' => null,
            'decimals' => 4,
            'divisor' => null,
            'only_reduction' => true,
            'user_reduction' => true,
            'quantity' => 1,
            'force_associated_tax' => false,
            'id_customer' => null,
            'id_cart' => null,
            'id_address' => null,
            'with_eco_tax' => false,
            'use_group_reduction' => true,
            'use_customer_price' => true,
        );
        $spo = null;

        $value = Product::getPriceStatic(
            (int) $id_product,
            $options['include_tax'],
            $options['id_product_attribute'],
            $options['decimals'],
            $options['divisor'],
            $options['only_reduction'],
            $options['user_reduction'],
            $options['quantity'],
            $options['force_associated_tax'],
            $options['id_customer'],
            $options['id_cart'],
            $options['id_address'],
            $spo,
            $options['with_eco_tax'],
            $options['use_group_reduction'],
            $context,
            $options['use_customer_price']
        );
        return $value;
    }

    public static function getProductOriginalPrice($id_product, $context)
    {
        $options = array(
            'include_tax' => true,
            'id_product_attribute' => null,
            'decimals' => 4,
            'divisor' => null,
            'only_reduction' => false,
            'user_reduction' => false,
            'quantity' => 1,
            'force_associated_tax' => false,
            'id_customer' => null,
            'id_cart' => null,
            'id_address' => null,
            'with_eco_tax' => true,
            'use_group_reduction' => false,
            'use_customer_price' => false,
        );
        $spo = null;

        $value = Product::getPriceStatic(
            (int) $id_product,
            $options['include_tax'],
            $options['id_product_attribute'],
            $options['decimals'],
            $options['divisor'],
            $options['only_reduction'],
            $options['user_reduction'],
            $options['quantity'],
            $options['force_associated_tax'],
            $options['id_customer'],
            $options['id_cart'],
            $options['id_address'],
            $spo,
            $options['with_eco_tax'],
            $options['use_group_reduction'],
            $context,
            $options['use_customer_price']
        );
        return $value;
    }

    public static function getProductImage($product)
    {
        $image = Image::getCover($product->id);
        if (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {
            $link = new Link('https://', 'https://');
        } else {
            $link = new Link('http://', 'http://');
        }

        $selectedImageTypeName = Configuration::get('CN_PRODUCT_IMAGE_SIZE');

        $productImageTypesActive = array();
        $productImageSizesAvailable = ImageType::getImagesTypes();

        foreach ($productImageSizesAvailable as $key => $productImageSize) {
            if ($productImageSize['products'] == '1') {
                array_push($productImageTypesActive, $productImageSize['name']);
            }
        }

        $isSelectedImageTypeAvailable = array_search($selectedImageTypeName, $productImageTypesActive);
        if ($isSelectedImageTypeAvailable === false) {
            $selectedImageTypeName = ImageType::getFormatedName('home');
            $isHomeImageTypeAvailable = array_search($selectedImageTypeName, $productImageTypesActive);
            if ($isHomeImageTypeAvailable === false) {
                if (count($productImageTypesActive) > 0) {
                    $selectedImageTypeName = $productImageTypesActive[0];
                } else {
                    $selectedImageTypeName = null;
                }
            }
        }
        $imagePath = $link->getImageLink($product->link_rewrite, $image['id_image'], $selectedImageTypeName);
        return $imagePath;
    }
}
