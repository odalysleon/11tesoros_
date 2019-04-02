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
	var items = {if $cols}{$cols|escape:'htmlall':'UTF-8'}{else}4{/if},
	    itemsDesktop3 = {if $cols}{$cols|escape:'htmlall':'UTF-8'}{else}4{/if},
	    itemsDesktopSmall3 = {if $cols_md}{$cols_md|escape:'htmlall':'UTF-8'}{else}3{/if},
	    itemsTablet3 = {if $cols_sm}{$cols_sm|escape:'htmlall':'UTF-8'}{else}2{/if},
	    itemsMobile3 = {if $cols_xs}{$cols_xs|escape:'htmlall':'UTF-8'}{else}1{/if};
</script>

<div class="flex-box top">
	<div class="left-content">
		{if $addon_title}
			<div class="addon-title">
				{if $icon_class}
					<i class="{$icon_class|escape:'htmlall':'UTF-8'}"></i>
				{/if}
				<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
				<a class="cat-btn" title="Show Categories">
					<i class="fa fa-angle-double-down" aria-hidden="true"></i>
				</a>
			</div>
		{/if}
		<div class="categories-list">
			<ul>
				{foreach from=$list_categories item=category }
		            {assign var='categoryLink' value=$link->getcategoryLink($category.id_category, '#')}
					<li><a href="{$categoryLink}" title="{$category.name}">{$category.name}</a></li>
				{/foreach}
			</ul>
			{if $allcat_id}
			 	{assign var='allcategoryLink' value=$link->getcategoryLink($allcat_id, $allcat_link_rewrite)}
			 	<a href="{$allcategoryLink}" title="allcat" class="view-all-btn btn-hover">
			 		{l s='View All' d='Modules.JmsPagebuilder'} 
			 		<i class="fa fa-angle-right" aria-hidden="true"></i>
				</a>
			{/if}
		</div>
	</div>
	<div class="right-content">
		<div class="flex-box">
			<div class="html-content html-content-third">
				{if $banner_html1}
					<div class="content-first item active">
							{$banner_html1 nofilter}
					</div>
				{/if}
				{if $banner_html2}
					<div class="content-second item">
						{$banner_html2 nofilter}
					</div>
				{/if}
				{if $banner_html3}
					<div class="content-third item">
						{$banner_html3 nofilter}
					</div>
				{/if}
			</div>
			{if $banner1 || $banner2 || $banner3 }
			<div class="img-banner img-banner-third">
				{if $banner1}
					<div class="item small_img1 active">
						<img src="{$root_url|escape:'html':'UTF-8'}{$banner1|escape:'html':'UTF-8'}" alt="Image" />
					</div>
				{/if}
				{if $banner2}
					<div class="item small_img2">
						<img src="{$root_url|escape:'html':'UTF-8'}{$banner2|escape:'html':'UTF-8'}" alt="Image" />
					</div>
				{/if}
				{if $banner3}
					<div class="item small_img3">
						<img src="{$root_url|escape:'html':'UTF-8'}{$banner3|escape:'html':'UTF-8'}" alt="Image" />
					</div>
				{/if}
			</div>
			{/if}
		</div>
	</div>
</div>
<div class="product-carousel3 item-hover">	
	{foreach from = $products_slides item = products_slide}
		<div class="item">
			{foreach from = $products_slide item = product}
				{include file="catalog/_partials/miniatures/product.tpl" product=$product}
			{/foreach}
		</div>
	{/foreach}
</div>