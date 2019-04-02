{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file='page.tpl'}
{block name="page_content"}
{capture name=path}{$post.title nofilter}{/capture}
<h3 class="title-blog">{$post.title nofilter}</h3>
<div class="blog-post">
	{assign var=catparams value=['category_id' => $post.category_id, 'slug' => $post.category_alias]}	
	<ul class="post-meta">
		{if $jmsblog_setting.JMSBLOG_SHOW_CATEGORY}
		<li class="post-category"><span>{l s='Category' d='Modules.JmsBlog'} :</span> <a href="{jmsblog::getPageLink('jmsblog-category', $catparams) nofilter}">{$post.category_name nofilter}</a></li>
		{/if}
		<li class="post-created"><span>{l s='Created' d='Modules.JmsBlog'} :</span> {$post.created|escape:'htmlall':'UTF-8'|date_format:"%B %e, %Y"}</li>
		{if $jmsblog_setting.JMSBLOG_SHOW_VIEWS}
		<li class="post-views"><span>{l s='Views' d='Modules.JmsBlog'} :</span> {$post.views nofilter}</li>		
		{/if}
		{if $jmsblog_setting.JMSBLOG_SHOW_COMMENTS}
		<li class="post-comment-count">{l s='Comments' d='Modules.JmsBlog'} :</span> <a title="Comment on {$post.title nofilter}" href="#comments"> {$comments|@count nofilter}</a></li>
		{/if}
	</ul>		
	{if $post.link_video && $jmsblog_setting.JMSBLOG_SHOW_MEDIA}
		<div class="post-video">
		{$post.link_video nofilter}
		</div>
	{elseif $post.image && $jmsblog_setting.JMSBLOG_SHOW_MEDIA}
		<div class="post-image">
		<img src="{$image_baseurl nofilter}{$post.image nofilter}" />
		</div>
	{/if}
	<div class="post-fulltext">
		{$post.fulltext nofilter}	
	</div>
</div>
{if $jmsblog_setting.JMSBLOG_SHOW_SOCIAL_SHARING}
<div class="social-sharing">
{literal}
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "a6f949b3-864b-44c5-b0ec-4140186ad958", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
{/literal}
<span class='st_sharethis_large' displayText='ShareThis'></span>
{if $jmsblog_setting.JMSBLOG_SHOW_FACEBOOK}
<span class='st_facebook_large' displayText='Facebook'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_TWITTER}
<span class='st_twitter_large' displayText='Tweet'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_GOOGLEPLUS}
<span class='st_googleplus_large' displayText='Google +'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_LINKEDIN}
<span class='st_linkedin_large' displayText='LinkedIn'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_PINTEREST}
<span class='st_pinterest_large' displayText='Pinterest'></span>
{/if}
{if $jmsblog_setting.JMSBLOG_SHOW_EMAIL}
<span class='st_email_large' displayText='Email'></span>
{/if}
</div>
{/if}
{if $jmsblog_setting.JMSBLOG_COMMENT_ENABLE}	
<div id="comments">
{if $jmsblog_setting.JMSBLOG_FACEBOOK_COMMENT == 0}
{include file="{$module_dir}comment_default.tpl"}		
{else}
{include file="{$module_dir}comment_facebook.tpl"}		
{/if}
</div>
{/if}
{/block}