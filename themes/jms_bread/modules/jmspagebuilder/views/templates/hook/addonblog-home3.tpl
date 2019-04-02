<script type="text/javascript">
	var items = 3,
	blog_itemsDesktop = {if $items_show}{$items_show|escape:'htmlall':'UTF-8'}{else}3{/if},
	blog_itemsDesktopSmall = {if $items_show_md}{$items_show_md|escape:'htmlall':'UTF-8'}{else}2{/if},
	blog_itemsTablet = {if $items_show_sm}{$items_show_sm|escape:'htmlall':'UTF-8'}{else}2{/if},
	blog_itemsMobile = {if $items_show_xs}{$items_show_xs|escape:'htmlall':'UTF-8'}{else}1{/if};
</script>
{if $addon_title}
	<div class="addon-title">
		<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
	</div>
{/if}
{if $addon_desc}
	<p class="addon-desc">{$addon_desc|escape:'htmlall':'UTF-8'}</p>
{/if}

{if $posts|@count gt 0}
<div class="blog-carousel">	
	{foreach from=$posts item=post}				
		{assign var=params value=['post_id' => $post.post_id, 'category_slug' => $post.category_alias, 'slug' => $post.alias]}
		{assign var=catparams value=['category_id' => $post.category_id, 'slug' => $post.category_alias]}	
		<div class="blog-item">
			{if $post.link_video && ($show_media == '1')}
				<div class="post-thumb">
					{$post.link_video|escape:'htmlall':'UTF-8'}
				</div>
			{elseif $post.image && ($show_media == '1')}
				<div class="post-thumb">
					<a href="{jmsblog::getPageLink('jmsblog-post', $params)|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}">
						<img src="{$image_url|escape:'html':'UTF-8'}{$post.image|escape:'html':'UTF-8'}" alt="{$post.title|escape:'htmlall':'UTF-8'}" class="img-responsive" />
						{if $show_time == '1'}
								<span class="post-created">
									{$post.created|escape:'html':'UTF-8'|date_format:'%e %b'}
								</span>
					{/if}
					</a>
				</div>
			{/if}	
			<div class="post-info">
				<h4 class="post-title">
					<a href="{jmsblog::getPageLink('jmsblog-post', $params)|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}">{$post.title|escape:'htmlall':'UTF-8'}</a>
				</h4>
				<ul class="post-meta">
					{if $show_category == '1'}
						<li>
							{l s='In' d='Modules.JmsPagebuilder'}: 
								<a href="{jmsblog::getPageLink('jmsblog-category', $catparams)|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}">
									{$post.category_name|escape:'html':'UTF-8'}
								</a>
						</li>
					{/if}
					{if $show_nviews == '1'}
						<li>
							<span>
								{$post.views|escape:'html':'UTF-8'} {l s='views' d='Modules.JmsPagebuilder'}
							</span>
						</li>
					{/if}
					{if $show_ncomments == '1'}		
						<li>
							<span>
								{$post.comment_count|escape:'html':'UTF-8'} {l s='comments' d='Modules.JmsPagebuilder'}
							</span>
						</li>
					{/if}
				</ul>
				{if $show_introtext == '1'}	
					<div class="post-intro">{$post.introtext}</div>	
				{/if}
				{if $show_readmore == '1'}	
					<a class="post-readmore btn-hover" href="{jmsblog::getPageLink('jmsblog-post', $params)|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}">{l s='Read more' d='Modules.JmsPagebuilder'}</a>	
				{/if}
			</div>
		</div>	
	{/foreach}	
</div>	
{/if}