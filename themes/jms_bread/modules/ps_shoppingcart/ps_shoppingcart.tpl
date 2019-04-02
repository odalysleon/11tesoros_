<div class="cart_block btn-group compact-hidden blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if} dropdown js-dropdown" id="cart_block" data-refresh-url="{$refresh_url}">
			<a href="#" class="dropdown-toggle cart-icon" data-toggle="dropdown">
				<img src="{if isset($urls)}{$urls.base_url}{/if}themes/jms_bread/assets/img/icon/icon_cart.svg" style="width: 21px; height: 20px;" alt="Cart Icon" />
				<span class="ajax_cart_quantity">{$cart.products_count}</span>
			</a>
	<div class="dropdown-menu shoppingcart-box">
        <span class="ajax_cart_no_product" {if $cart.products_count != 0}style="display:none"{/if}>{l s='There is no product' d='Shop.Theme.Actions'}</span>
			<ul class="list products cart_block_list">
				{foreach from=$cart.products item=product}
					<li>{include 'module:ps_shoppingcart/ps_shoppingcart-product-line.tpl' product=$product}</li>
				{/foreach}
			</ul>
			<div class="checkout-info">
					{foreach from=$cart.subtotals item="subtotal"}
						<div class="{$subtotal.type}">
							<span class="label">{$subtotal.label}</span>
							<span class="value pull-right">{$subtotal.value}</span>
						</div>
					{/foreach}
				<div class="cart-button">
					<a id="button_order_cart" class="btn-efect" href="{$cart_url}" title="{l s='Check out' d='Shop.Theme'}" rel="nofollow">
						{l s='Check out' d='Shop.Theme'}
					</a> 
				</div>
			</div>
	</div>
</div>
