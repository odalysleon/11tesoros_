{*
* 2007-2013 PrestaShop
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

{capture name=path}{l s='Search' d='Modules.JmsAdvsearch'}{/capture}

<h1 {if isset($instantSearch) && $instantSearch}id="instant_search_results"{/if}  class="page-heading">
{l s='Search' d='Modules.JmsAdvsearch'}&nbsp;{if $nbProducts > 0}"{if isset($search_query) && $search_query}{$search_query|escape:'htmlall':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'htmlall':'UTF-8'}{elseif $ref}{$ref|escape:'htmlall':'UTF-8'}{/if}"{/if}
{if isset($instantSearch) && $instantSearch}<a href="#" class="close">{l s='Return to the previous page' d='Modules.JmsAdvsearch'}</a>{/if}
</h1>

{include file="$tpl_dir./errors.tpl"}
{if !$nbProducts}
	<p class="warning">
		{if isset($search_query) && $search_query}
			{l s='No results were found for your search' d='Modules.JmsAdvsearch'}&nbsp;"{if isset($search_query)}{$search_query|escape:'htmlall':'UTF-8'}{/if}"
		{elseif isset($search_tag) && $search_tag}
			{l s='No results were found for your search' d='Modules.JmsAdvsearch'}&nbsp;"{$search_tag|escape:'htmlall':'UTF-8'}"
		{else}
			{l s='Please enter a search keyword' d='Modules.JmsAdvsearch'}
		{/if}
	</p>
{else}
	<h3 class="nbresult page-heading"><span class="big">{if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval d='Modules.JmsAdvsearch'}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval d='Modules.JmsAdvsearch'}{/if}</span></h3>			
		{if !isset($instantSearch) || (isset($instantSearch) && !$instantSearch)}
		<div class="filters-panel">
			<div class="row">				
				<div class="col-sm-6 col-md-4 col-lg-4 product-sort">
					{include file="$tpl_dir./product-sort.tpl"}				
				</div>	
				<div class="col-md-4 col-lg-4 hidden-sm hidden-xs view-mode">
					<label>{l s='View as' d='Modules.JmsAdvsearch'}:</label>&nbsp; <a class="view-grid" href="#"><span class="fa fa-th"></span></a> <a class="view-list" href="#"><span class="fa fa-th-list"></span></a>
				</div>			
				<div class="col-sm-6 col-md-4 col-lg-4 hidden-xs compare-box">
					{include file="$tpl_dir./product-compare.tpl"}
				</div>
			</div>
		</div>
		{/if}
		{include file="$tpl_dir./product-list.tpl" products=$search_products}
		{if !isset($instantSearch) || (isset($instantSearch) && !$instantSearch)}
			<div class="filters-panel-bottom">
				<div class="row">
					{include file="$tpl_dir./pagination.tpl"}
				</div>	
			</div>
		{/if}
{/if}
