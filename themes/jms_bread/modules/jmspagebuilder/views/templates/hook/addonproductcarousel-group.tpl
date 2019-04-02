<script type="text/javascript">
	var p1_items = {if $cols}{$cols|escape:'htmlall':'UTF-8'}{else}4{/if};
	var p1_itemsDesktop = {if $cols}{$cols|escape:'htmlall':'UTF-8'}{else}4{/if};
	var p1_itemsDesktopSmall = {if $cols_md}{$cols_md|escape:'htmlall':'UTF-8'}{else}3{/if};
	var p1_itemsTablet = {if $cols_sm}{$cols_sm|escape:'htmlall':'UTF-8'}{else}2{/if};
	var p1_itemsMobile = {if $cols_xs}{$cols_xs|escape:'htmlall':'UTF-8'}{else}1{/if};
</script>
{if $addon_title}
<div class="addon-title">
	{if $icon_class}
		<i class="{$icon_class|escape:'htmlall':'UTF-8'}"></i>
	{/if}
	<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
</div>
{/if}
{if $addon_desc}
<p class="addon-desc">{$addon_desc|escape:'htmlall':'UTF-8'}</p>
{/if}	
{assign var="box_template" "{$addon_tpl_dir}productbox.tpl"}
	<div class="group-carousel">	
		{foreach from = $products_slides item = products_slide}
			<div class="item">
				{foreach from = $products_slide item = product}
					{include file="catalog/_partials/miniatures/product.tpl" product=$product}
				{/foreach}
			</div>
		{/foreach}
	</div>
