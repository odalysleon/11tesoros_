{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{if $purchase_done}
<div class="cn_purchase " style="display:none">
    <span class="purchase_id">{$purchase_id|escape:'htmlall':'UTF-8'}</span>
    <span class="purchase_date">{$purchase_date|escape:'htmlall':'UTF-8'}</span>
    <span class="payment_method">{$payment_method|escape:'htmlall':'UTF-8'}</span>
    <span class="cart_id">{$purchase_cart_id|escape:'htmlall':'UTF-8'}</span>
    <span class="total_quantity">{$purchase_total_quantity|escape:'htmlall':'UTF-8'}</span>
    <span class="total_price">{$purchase_total_price|escape:'htmlall':'UTF-8'}</span>

    {foreach from=$purchase_products item=product}
        <div class="product_basket_item">
            <span class="quantity">{$product['quantity']|escape:'htmlall':'UTF-8'}</span>
            <span class="price">{$product['price']|escape:'htmlall':'UTF-8'}</span>
            <span class="url">{$product['url']|escape:'htmlall':'UTF-8'}</span>
            <span class="product_id">{$product['product_id']|escape:'htmlall':'UTF-8'}</span>
            <span class="name">{$product['name']|escape:'htmlall':'UTF-8'}</span>
            <span class="description">{$product['description']|escape:'htmlall':'UTF-8'}</span>
            <span class="image_url">{$product['image_url']|escape:'htmlall':'UTF-8'}</span>
            <span class="unit_price">{$product['unit_price']|escape:'htmlall':'UTF-8'}</span>
            <span class="availability">{$product['availability']|escape:'htmlall':'UTF-8'}</span>
            <span class="brand">{$product['brand']|escape:'htmlall':'UTF-8'}</span>
            {*<span class="rating_value">{$product['rating_value']|escape:'htmlall':'UTF-8'}</span>*}
            {*<span class="review_count">{$product['review_count']|escape:'htmlall':'UTF-8'}</span>*}
            {*<span class="thumbnail_url">{$product['thumbnail_url']|escape:'htmlall':'UTF-8'}</span>*}
            {*<span class="priority">6</span>*}

            {foreach from=$product['categories'] item=category}
                <span class="category">{$category|escape:'htmlall':'UTF-8'}</span>
            {/foreach}

            {foreach from=$product['relatedProductsArray'] item=productId}
                <span class="related_external_product_id">{$productId|escape:'htmlall':'UTF-8'}</span>
            {/foreach}


            {foreach from=$product['tagsArray'] item=tag}
                <span class="tag">{$tag|escape:'htmlall':'UTF-8'}</span>
            {/foreach}
        </div>
    {/foreach}
</div>
{/if}
