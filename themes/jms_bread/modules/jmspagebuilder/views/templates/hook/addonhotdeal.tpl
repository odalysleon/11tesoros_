{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	var h_items = {if $items_show}{$items_show|escape:'htmlall':'UTF-8'}{else}1{/if},
	    h_itemsDesktop = {if $items_show}{$items_show|escape:'htmlall':'UTF-8'}{else}1{/if},
	    h_itemsDesktopSmall = {if $items_show_md}{$items_show_md|escape:'htmlall':'UTF-8'}{else}1{/if},
	    h_itemsTablet = {if $items_show_sm}{$items_show_sm|escape:'htmlall':'UTF-8'}{else}1{/if},
	    h_itemsMobile = {if $items_show_xs}{$items_show_xs|escape:'htmlall':'UTF-8'}{else}1{/if};
</script>
{if $addon_title}
<div class="addon-title">
	<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
</div>
{/if}
{if $addon_desc}
<p class="addon-desc">{$addon_desc|escape:'htmlall':'UTF-8'}</p>
{/if}
<div class="jms-hotdeal">
<div class="hotdeal-carousel item-hover">
		{foreach from=$products item=product key=k}	
			<div class="item">
			 	<div class="product-miniature js-product-miniature product-box" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
					<div class="preview">
						{block name='product_thumbnail'}
						  <a href="{$product.url}" class="product-image {if isset($jpb_phover) && $jpb_phover == 'image_swap'}image_swap{else}image_blur{/if}">
							<img class="img-responsive product-img1"
							  src = "{$product.cover.bySize.home_default.url}"
							  alt = "{$product.cover.legend}"
							  data-full-size-image-url = "{$product.cover.large.url}"
							/>
							{if isset($jpb_phover) && $jpb_phover == 'image_swap' && $product.images.1}
								<img class="img-responsive product-img2"
								  src = "{$product.images.1.bySize.home_default.url}"
								  alt = "{$product.images.1.legend}"
								  data-full-size-image-url = "{$product.images.1.large.url}"
								/>
							{/if}
						  </a>
							{block name='product_flags'}
								{foreach from=$product.flags item=flag}
									{if $flag.label == 'New'}
									<span class="label label-new">{$flag.label}</span>
									{/if}
									{if $flag.label == 'On sale!'}
									<span class="label label-sale">{l s='Sale' d='Modules.JmsPagebuilder'}</span>
									{/if}
								{/foreach}
							{/block}
						{/block}
						{if isset($configuration.is_catalog) && !$configuration.is_catalog}
							<button {if $product.quantity < 1 && !$product.add_to_cart_url}disabled{/if} data-button-action="add-to-cart" class="ajax-add-to-cart product-btn cart-button" data-id-product="{$product.id}" data-minimal-quantity="{$product.minimal_quantity}" data-token="{if isset($static_token) && $static_token}{$static_token}{/if}">
								<span class="icon-basket"></span>
								<span class="fa fa-refresh fa-spin" aria-hidden="true"></span>
								<span class="fa fa-check" aria-hidden="true"></span>
							</button>
						{/if}
							{block name='product_variants'}
								{if $product.main_variants && isset($jpb_pcolor) && $jpb_pcolor == 1}
									<div class="color_to_pick_list">
										{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
									</div>
								{/if}
							{/block}
					</div>
				    <div class="product-info">
				    	<div class="product-cat">
							<a href="{url entity='category' id=$product.id_category_default}" title="{$product.category_name}">{$product.category_name}</a>
						</div>
					    {block name='product_name'}
							<a href="{$product.link|escape:'html'}" class="product-name">{$product.name|escape:'html':'UTF-8'}</a>
					    {/block}
					    {if isset($configuration.is_catalog) && !$configuration.is_catalog}
						    {block name='product_price_and_shipping'}
						        {if $product.show_price}
						          <div class="content_price">
						            {if $product.has_discount}
						              {hook h='displayProductPriceBlock' product=$product type="old_price"}
						              <span class="old price">{$product.regular_price}</span>
						            {/if}
						            {hook h='displayProductPriceBlock' product=$product type="before_price"}
						            <span class="price new">{$product.price}</span>

						            {hook h='displayProductPriceBlock' product=$product type='unit_price'}

						            {hook h='displayProductPriceBlock' product=$product type='weight'}
						          </div>
						        {/if}
						    {/block}
					    {/if}
					    <div class="product-sold">
							<div class="proces-bars">
								<span style="width:{($product.sold/$product.quantity)*100}%;" class="sold-quantity"></span>
								<span class="text">
									{if $product.sold > 0}{l s='Sold: ' d='Modules.JmsPagebuilder'}{$product.sold} - {/if} {l s='In Stock: ' d='Modules.JmsPagebuilder'}
									{if ($product.quantity-$product.sold) > 0}
										{$product.quantity-$product.sold}
									{else}
										0
									{/if}
								</span>
							</div>
						</div>
					    <div class="countdown" id="countdown-{$hotdeals[$k].id_hotdeals|escape:'html'}">
							{$hotdeals[$k].deals_time|escape:'html'}
						</div>
					    <div class="product-description">
							{$product.description_short nofilter}
						</div>
				    </div>
				    <div class="action-btn">
						<div class="box">
								<a href="#" data-link-action="quickview" class="quick-view product-btn hidden-xs">
									<i class="icon-eye"></i>
									{l s='Quick view' d='Modules.Jmspagebuilder'}
							</a>
						</div>
					</div>
</div>
  			</div>
		{/foreach}
</div>
{if ($showall_link == '1')} 
<div class="hotdeal-viewall">
<a href="{$link->getModuleLink('hotdeals','allproduct')|escape:'htmlall':'UTF-8'}">{l s='View All Product' d='Modules.JmsPagebuilder'}</a>
</div>
{/if}
</div>