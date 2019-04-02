<section class="featured-products clearfix">
	<div class="addon-title fix-title">
		<h3>
			{l s='Popular Products' d='Shop.Theme.Catalog'}
		</h3>
	</div>
  
  <div class="products propular-carousel">
    {foreach from=$products item="product"}
      {include file="catalog/_partials/miniatures/product.tpl" product=$product}
    {/foreach}
  </div>
</section>