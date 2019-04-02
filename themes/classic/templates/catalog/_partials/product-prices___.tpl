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
{if $product.show_price}
  <div class="product-prices">
    {block name='product_discount'}
      {if $product.has_discount and $product.id_category_default != 14}
        <div class="product-discount">
          {hook h='displayProductPriceBlock' product=$product type="old_price"}
          <span class="regular-price">{$product.regular_price}</span>
        </div>
      {/if}
    {/block}

    {block name='product_price'}
      <div
        class="product-price h5 {if $product.has_discount}has-discount{/if}"
        itemprop="offers"
        itemscope
        itemtype="https://schema.org/Offer"
      >
        <link itemprop="availability" href="https://schema.org/InStock"/>
        <meta itemprop="priceCurrency" content="{$currency.iso_code}">
        <div class="current-price">           
	 {if $product.price_amount > 0 and $product.id_category_default != 14}
          <span itemprop="price" content="{$product.price_amount}">{$product.price}</span>
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


                            <br/>
                            <span class="unit_price_discount" >{$price_unit|string_format:"%.2f"|replace:'.':','}</span>
                            <span class="unit_price" >{$price_unit_final|string_format:"%.2f"|replace:'.':','} €/{l s='Unit' d='Shop.Theme.Catalog'}</span>
                        {else}
                            <br/>
                            <span class="unit_price" >{$price_unit|string_format:"%.2f"|replace:'.':','} €/{l s='Unit' d='Shop.Theme.Catalog'}</span>
                        {/if}
                    {/if}
                    {if $feature.id_feature==28}
                        {assign var=kg value="€"|explode:$feature.value}
                        {assign var=str_description value="."|explode:$kg[1]}
                        {if $product.has_discount }

                            {assign var=regular_price_val value= $product.regular_price|replace:',':'.'}
                            {assign var=kg_referencia value= ($regular_price_val|replace:'€':'')/ ($kg[0]|replace:',':'.')}
                            <br/><br/>
                            <span class="unit_price_discount" >{$kg[0]}  €/Kg </span>
                            <br/><br/>
                            <span class="unit_price" >{($product.price_amount/$kg_referencia)|string_format:"%.2f"|replace:'.':','} €/Kg</span>
                            {if $str_description[1] }
                                {l s='See Description' d='Shop.Theme.Catalog'}
                            {/if}
                        {else}
                            <br/><br/>
                            <span class="unit_price" >{$kg[0]} €/Kg </span>
                            {if $str_description[1] }
                                {l s='See Description' d='Shop.Theme.Catalog'}
                            {/if}
                        {/if}
                    {/if}
                {/foreach}              
          {/if}

          {if $product.has_discount}
            {if $product.discount_type === 'percentage'}
              <span class="discount discount-percentage">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
            {else}
              <span class="discount discount-amount">
                  {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
              </span>
            {/if}
          {/if}
         {/if}
	 {if $product.price_amount == 0 or $product.id_category_default == 14}
		<span itemprop="price" content="{$product.price_amount}">{l s='(Add to the cart to request a budget)' d='Shop.Theme.Catalog'}</span>
	 {/if} 
        </div>
        {block name='product_unit_price'}
          {if $displayUnitPrice}
            <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}</p>
          {/if}
        {/block}
      </div>
    {/block}

    {block name='product_without_taxes'}
      {if $priceDisplay == 2}
        <p class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
      {/if}
    {/block}

    {block name='product_pack_price'}
      {if $displayPackPrice}
        <p class="product-pack-price"><span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span></p>
      {/if}
    {/block}

    {block name='product_ecotax'}
      {if $product.ecotax.amount > 0}
        <p class="price-ecotax">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
          {if $product.has_discount}
            {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
          {/if}
        </p>
      {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

    <div class="tax-shipping-delivery-label">
      {if $configuration.display_taxes_label}
        {$product.labels.tax_long}
      {/if}
      {hook h='displayProductPriceBlock' product=$product type="price"}
      {hook h='displayProductPriceBlock' product=$product type="after_price"}
    </div>
  </div>
{/if}
