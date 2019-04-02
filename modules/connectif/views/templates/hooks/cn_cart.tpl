{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{if $cart_exist}
    <div class="cn_cart" style="display:none">
        <span class="cart_id">{$cart_id|escape:'htmlall':'UTF-8'}</span>
        <span class="total_quantity">{$total_quantity|escape:'htmlall':'UTF-8'}</span>
        <span class="total_price">{$total_price|escape:'htmlall':'UTF-8'}</span>

        {foreach from=$products item=product}
            <div class="product_basket_item">
                <span class="quantity">{$product['quantity']|escape:'htmlall':'UTF-8'}</span>
                <span class="price">{$product['price']|escape:'htmlall':'UTF-8'}</span>
                {include file="{$module_templates}hooks/cn_product.tpl"}
            </div>
        {/foreach}

    </div>
{/if}