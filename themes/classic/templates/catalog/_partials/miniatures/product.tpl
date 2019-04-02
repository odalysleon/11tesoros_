{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='product_miniature_item'}
  <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
    <div class="thumbnail-container">
      {block name='product_thumbnail'}
        <a href="{$product.url}" class="thumbnail product-thumbnail">
          <img
            src = "{$product.cover.bySize.home_default.url}"
            alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
            data-full-size-image-url = "{$product.cover.large.url}"
          >
        </a>
      {/block}

      <div class="product-description">
        {block name='product_name'}
          <h1 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h1>
        {/block}

        {block name='product_price_and_shipping'}
         {if $product.price_amount > 0 and $product.id_category_default != 14}
          {if $product.show_price}
            <div class="product-price-and-shipping">
              {if $product.has_discount}
                {hook h='displayProductPriceBlock' product=$product type="old_price"}

                <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                <span class="regular-price">{$product.regular_price}</span>
                {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage">{$product.discount_percentage}</span>
                {/if}
              {/if}

              {hook h='displayProductPriceBlock' product=$product type="before_price"}

              <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
              <span itemprop="price" class="price">{$product.price}
                  {if $product.features}            
                    {foreach from=$product.features item=feature}
                        
                        {if $feature.id_feature==10}  
                            {assign var=price_unit value=((($product.price|replace:'€':'' )|replace:',':'.')/ $feature.value)}
                            {if $product.has_discount }
                                {if $product.discount_type === 'percentage'}
                                    {assign var=price_unit_final value=($price_unit- (($price_unit * $product.discount_percentage_absolute )/ 100))|string_format:"%.2f" }
                                {else}   
                                    {assign var=price_unit_percentage_referencia value= $product.discount_to_display * 100 / $product.price}
                                    {assign var=price_unit_final value=($price_unit- (($price_unit * $price_unit_percentage_referencia )/ 100))|string_format:"%.2f" }
                                {/if}       
                                <span class="unit_price_discount" >{$price_unit|string_format:"%.2f"|replace:'.':','}</span>
                                <span class="unit_price" >{$price_unit_final|string_format:"%.2f"|replace:'.':','} €/{l s='Unit' d='Shop.Theme.Catalog'}</span>
                            {else} 
                                <span class="unit_price" >{$price_unit|string_format:"%.2f"|replace:'.':','} €/{l s='Unit' d='Shop.Theme.Catalog'}</span>
                            {/if} 
                        {/if}
                        {if $feature.id_feature==28}
                            {assign var=kg value="€"|explode:$feature.value}
                            {if $product.has_discount }
                                {if $product.discount_type === 'percentage'}
                                    {assign var=kg_final value=($kg[0]- (($kg[0] * $product.discount_percentage_absolute )/ 100))|string_format:"%.2f" }
                                {else} 
                                    {assign var=kg_percentage_referencia value= $product.discount_to_display * 100 / $product.price}
                                    {assign var=kg_final value=($kg[0]- (($kg[0] * $kg_percentage_referencia ))/ 100)|string_format:"%.2f" }
                                {/if} 
                                     
                                <span class="unit_price_discount" >{$kg[0]}</span>
                                <span class="unit_price" >{$kg_final} €/Kg</span>
                            {else} 
                                <span class="unit_price" >{$kg[0]} €/Kg</span>
                            {/if} 
                        {/if} 
                    {/foreach}              
              {/if}
            </span>

              {hook h='displayProductPriceBlock' product=$product type='unit_price'}

              {hook h='displayProductPriceBlock' product=$product type='weight'}
            </div>
          {/if}
         {/if}
	 {if $product.price_amount == 0 || $product.id_category_default == 14}
             {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage">{$product.discount_percentage}</span>
                {/if}
	      <div class="product-price-and-shipping">
              	<span itemprop="price" class="price" style="color:#2fb5d2 !important">{l s='REQUEST A BUDGET' d='Shop.Theme.Catalog'}</span>
            </div>
	 {/if}  
        {/block}

        {block name='product_reviews'}
          {hook h='displayProductListReviews' product=$product}
        {/block}
      </div>

      {block name='product_flags'}
        <ul class="product-flags">
          {foreach from=$product.flags item=flag}
            <li class="product-flag {$flag.type}">{$flag.label}</li>
          {/foreach}
        </ul>
      {/block}

      <div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-sm-down">
        {block name='quick_view'}
          <a class="quick-view" href="#" data-link-action="quickview">
            <i class="material-icons search">&#xE8B6;</i> {l s='Quick view' d='Shop.Theme.Actions'}
          </a>
        {/block}

        {block name='product_variants'}
          {if $product.main_variants}
            {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
          {/if}
        {/block}
      </div>

    </div>
  </article>
{/block}
