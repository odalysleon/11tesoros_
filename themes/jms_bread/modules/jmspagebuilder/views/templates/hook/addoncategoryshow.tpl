{if isset($id_cat) AND $id_cat}
	{if $show_img == 1}
	{assign var='categoryLink' value=$link->getcategoryLink({$id_cat}, $category.link_rewrite)}
	<div class="thumb">
		<a href="{$categoryLink}" title="{$name}" class="category_image">
			<img src="{$img_cat_dir}{$id_cat}_thumb.jpg" alt="{$name}" title="{$name}" class="img-responsive"/>
		</a>
	</div>
	{/if}
	<div class="parent-category">
		<a href="{$categoryLink}" title="{$name}">{$name}</a>
	</div>
	{if $num_child != 0}
		<ul class="child-categories">
			{foreach from=$child item=c}
				{assign var='categoryLink' value=$link->getcategoryLink({$c.id_category}, $category.link_rewrite)}
				<li>			
					<a href="{$categoryLink}" title="{$c.name}">{$c.name}</a>
				</li>
			{/foreach}
		</ul>
	{/if}
{else}
	<p>{l s='No categories' d='Modules.JmsPagebuilder'}</p>
{/if}

