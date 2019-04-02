{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<script type="text/javascript">
	var check_masonry = {$gallery_masonry};
	var check_fancybox = {$gallery_fancybox};
	var g_items = {if $cols}{$cols}{else}4{/if},
			g_itemsDesktop = {if $cols}{$cols}{else}4{/if},
			g_itemsDesktopSmall = {if $cols_md}{$cols_md}{else}3{/if},
			g_itemsTablet = {if $cols_sm}{$cols_sm}{else}2{/if},
			g_itemsMobile = {if $cols_xs}{$cols_xs}{else}1{/if};
</script>
<div class="addon-title">
	<h3>{l s='Gallery' mod='jmspagebuilder'}</h3>
</div>
{if isset($items) AND $items}
	{if $gallery_masonry == 1}
	<div class="masonry-gallery row">
		{foreach from=$items item=item}		
			<div class="item">
				<div class="gallery-masonry-img gallery_img">
						<a class="grouped_elements" data-fancybox-group="gallery" href="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}">
							<img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}" />
						</a>
				</div>
				<div class="title-disable gallery-title">
					<span>{$item.title}</span>
				</div>	
			</div>	
		{/foreach}
	</div>
	{else}
	<div class="gallery-carousel">
		{foreach from = $items item = images_slide}
			{foreach from = $images_slide item=item key=k}
				{if $k % 2 == 0}
				<div class="gallery-item">
				{/if}
					<div class="box">
						<a class="grouped_elements" data-fancybox-group="gallery" href="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}">
							<img src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}" />
						</a>
					</div>		
				{if $k %2 ==1}			
				</div>
				{/if}
			{/foreach}
		{/foreach}
	</div>
	{/if}
{else}	
{l s='Sorry, dont have any image in this section' mod='jmspagebuilder'}
{/if}


