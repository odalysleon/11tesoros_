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
{extends file='page.tpl'}
{block name="page_content"}
{capture name=path}{l s='Categories'}{/capture}
<h1 class="page-heading">{l s='Categories'}</h1>
{if isset($categories) AND $categories}		
	<div class="categories-list row">
		{foreach from=$categories item=category}			
			{assign var=catparams value=['category_id' => $category.category_id, 'slug' => $category.alias]}				
			<div class="blog-category">
				{if $category.image && $jmsblog_setting.JMSBLOG_SHOW_MEDIA}
					<div class="post-thumb">
						<a href="{jmsblog::getPageLink('jmsblog-category', $catparams)}"><img src="{$image_baseurl|escape:'html':'UTF-8'}{$category.image|escape:'html':'UTF-8'}" alt="{$category.title|escape:'htmlall':'UTF-8'}" class="img-responsive" /></a>			 		
					</div>
				{/if}
				<div class="category-info">
					<h4 class="post-title"><a href="{jmsblog::getPageLink('jmsblog-category', $catparams)}">{$category.title|escape:'htmlall':'UTF-8'}</a></h4>	
					<div class="blog-intro">{$category.introtext nofilter}</div>
				</div>			
			</div>
		{/foreach}
	</div>
{else}	
{l s='Sorry, dont have any category in this section' d='Modules.JmsBlog'}
{/if}
{/block}

