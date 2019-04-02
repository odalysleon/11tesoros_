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
{if $jpb_homepage == 1}
<script type="text/javascript">
	
</script>
<div class="addon-title">
	<div class="flex-box">
		<h3>
			{l s="Week's Hot Deals" mod="jmsflashsale"}
		</h3>
		<div class="desc-text">
			{l s="Unbox new offers every single week" mod="jmsflashsale"}
		</div>
		<div class="flashsales-countdown">
			{$expiretime|escape:'htmlall':'UTF-8'}
		</div>
		<a href="#" title="view all product" class="btn-effect">{l s="View all" mod="jmsflashsale"}</a>
	</div>
</div>
{elseif $jpb_homepage == 2}
<div class="addon-title">
	<div class="flex-box">
		<h3>
			{l s="Special Deals" mod="jmsflashsale"}
		</h3>
		<div class="flashsales-countdown">
			{$expiretime|escape:'htmlall':'UTF-8'}
		</div>
	</div>
</div>
{elseif $jpb_homepage == 4}
	<div class="addon-title">
		<div class="flex-box">
			<h3>
				{l s="Hot Deals" mod="jmsflashsale"}
			</h3>
			<div class="flashsales-countdown">
				{$expiretime|escape:'htmlall':'UTF-8'}
			</div>
			<a href="#" class="btn-view-all btn-hover">{l s="View All" mod="jmsflashsale"}</a>
		</div>
	</div>
{/if}

<div class="jmsflashsales">
	{if $jpb_homepage==1}<div class="row">{/if}
		<div class="flashsales-carousel item-hover">	
			{foreach from=$products item=product key=k}	
				{if $jpb_homepage==4}
					<div class="product-miniature js-product-miniature product-box" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
					<div class="preview">
						{block name='product_thumbnail'}
						  <a href="{$product.url}" class="product-image {if isset($jpb_phover) && $jpb_phover == 'image_swap'}image_swap{else}image_blur{/if}">
							<img class="img-responsive product-img1"
							  src = "{$product.cover.bySize.large_default.url}"
							  alt = "{$product.cover.legend}"
							  data-full-size-image-url = "{$product.cover.large.url}"
							/>
							{if isset($jpb_phover) && $jpb_phover == 'image_swap' && $product.images.1}
								<img class="img-responsive product-img2"
								  src = "{$product.images.1.bySize.large_default.url}"
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
									<span class="label label-sale">{l s='Sale' d='Shop.Theme'}</span>
									{/if}
								{/foreach}
							{/block}
						{/block}
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
					    {if isset($configuration.is_catalog) && !$configuration.is_catalog}
							<button {if $product.quantity < 1 && !$product.add_to_cart_url}disabled{/if} data-button-action="add-to-cart" class="ajax-add-to-cart product-btn cart-button {if $product.quantity < 1}disabled{/if}" data-id-product="{$product.id}" data-minimal-quantity="{$product.minimal_quantity}" data-token="{if isset($static_token) && $static_token}{$static_token}{/if}">
								<span class="icon-basket"></span>
								{l s='Add To Cart' d='Modules.JmsFlashsale'}
							</button>
						{/if}
				    </div>
			</div>
				{else}
					<div class="product-miniature js-product-miniature product-box" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
					<div class="preview">
						{block name='product_thumbnail'}
						  <a href="{$product.url}" class="product-image {if isset($jpb_phover) && $jpb_phover == 'image_swap'}image_swap{else}image_blur{/if}">
							<img class="img-responsive product-img1"
							  src = "{$product.cover.bySize.large_default.url}"
							  alt = "{$product.cover.legend}"
							  data-full-size-image-url = "{$product.cover.large.url}"
							/>
							{if isset($jpb_phover) && $jpb_phover == 'image_swap' && $product.images.1}
								<img class="img-responsive product-img2"
								  src = "{$product.images.1.bySize.large_default.url}"
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
									<span class="label label-sale">{l s='Sale' d='Shop.Theme'}</span>
									{/if}
								{/foreach}
							{/block}
						{/block}
						{if isset($configuration.is_catalog) && !$configuration.is_catalog}
							<button {if $product.quantity < 1 && !$product.add_to_cart_url}disabled{/if} data-button-action="add-to-cart" class="ajax-add-to-cart product-btn cart-button {if $product.quantity < 1}disabled{/if}" data-id-product="{$product.id}" data-minimal-quantity="{$product.minimal_quantity}" data-token="{if isset($static_token) && $static_token}{$static_token}{/if}">
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
				    </div>
				    <div class="action-btn">
						<div class="box">
								<a href="#" data-link-action="quickview" class="quick-view product-btn hidden-xs">
									<i class="icon-eye"></i>
									{l s='Quick view' d='Modules.JmsFlashsale'}
							</a>
						</div>
					</div>
			</div>
				{/if}			 
			{/foreach}
		</div>
		{if $jpb_homepage==1}</div>{/if}
</div>
