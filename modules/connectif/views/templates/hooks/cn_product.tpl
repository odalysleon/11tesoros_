{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
<span class="url">{$product['url']|escape:'htmlall':'UTF-8'}</span>
<span class="product_id">{$product['product_id']|escape:'htmlall':'UTF-8'}</span>
<span class="name">{$product['name']|escape:'htmlall':'UTF-8'}</span>
<span class="description">{$product['description']|escape:'htmlall':'UTF-8'}</span>
<span class="image_url">{$product['image_url']|escape:'htmlall':'UTF-8'}</span>
<span class="unit_price">{$product['unit_price']|escape:'htmlall':'UTF-8'}</span>
<span class="availability">{$product['availability']|escape:'htmlall':'UTF-8'}</span>
<span class="brand">{$product['brand']|escape:'htmlall':'UTF-8'}</span>
<span class="unit_price_original">{$product['unit_price_original']|escape:'htmlall':'UTF-8'}</span>
<span class="unit_price_without_vat">{$product['unit_price_without_vat']|escape:'htmlall':'UTF-8'}</span>
<span class="discounted_percentage">{$product['discounted_percentage']|escape:'htmlall':'UTF-8'}</span>
<span class="discounted_amount">{$product['discounted_amount']|escape:'htmlall':'UTF-8'}</span>
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