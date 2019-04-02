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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var tes_items = 3,
	    tes_itemsDesktop = {if $items_show}{$items_show|escape:'htmlall':'UTF-8'}{else}1{/if},
	    tes_itemsDesktopSmall = {if $items_show_md}{$items_show_md|escape:'htmlall':'UTF-8'}{else}1{/if},
	    tes_itemsTablet = {if $items_show_sm}{$items_show_sm|escape:'htmlall':'UTF-8'}{else}1{/if},
	    tes_itemsMobile = {if $items_show_xs}{$items_show_xs|escape:'htmlall':'UTF-8'}{else}1{/if};
	
</script>

<div class="addon-title">
{if $addon_title}
	<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
{/if}
{if $addon_desc}
<p class="addon-desc">{$addon_desc|escape:'htmlall':'UTF-8'}</p>
{/if}	
</div>
<div class="testimonial-carousel">
{foreach from=$testimonials item=testimonial}	
<div class="testimonial-box">
	{if $show_image}
			<div class="testimonial-img">
				<img class="img-responsive" src="{$image_url|escape:'html':'UTF-8'}resized_{$testimonial.image|escape:'html':'UTF-8'}" alt="{$testimonial.author|escape:'html':'UTF-8'}" />
			</div>
	{/if}
	{if $show_time}
		<div class="testimonial-date">
			{$testimonial.posttime|date_format:"%b %e, %Y"|escape:'html':'UTF-8'}
		</div>
	{/if}
	<div class="testimonial-comment" >
		<span>
			{$testimonial.comment|truncate:180:'...'|escape:'html':'UTF-8'}
		</span>
	</div>
	<div class="testimonial-info">
		<div class="right">
			<span class="testimonial-author">
				{$testimonial.author|escape:'html':'UTF-8'}
			</span>
			{if $show_office}
				<span class="testimonial-office">
					- {$testimonial.office|escape:'html':'UTF-8'}
				</span>
			{/if}
		</div>
	</div>
</div>	
{/foreach}
</div>