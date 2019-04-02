{*
 * @package Jms Adv Search
 * @version 1.1
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}
<div class="jms-adv-search">
<a href="#" class="ic-search dropdown-toggle" data-toggle="dropdown">	
	<span class="icon-magnifier"></span>
</a>
<form method="get" action="{$link->getModuleLink('jmsadvsearch', 'search')|escape:'html'}" class="input-group" id="searchbox">
	<div class="keyword-group">
		<input type="text" id="ajax_search" name="search_query" placeholder="{l s='Search' d='Modules.JmsAdvsearch'}" class="input-search" />
	</div>
	<div class="cat-list">
		<select name="selector_cat" id="selector_cat">
			<option value="0">{l s='All Category' d='Modules.JmsAdvsearch'}</option>
			{foreach from=$jmsCategTree.children item=child name=jmsCategTree}
				{if $smarty.foreach.jmsCategTree.last}
					{include file="$branche_tpl_path" node=$child last='true'}
				{else}
					{include file="$branche_tpl_path" node=$child}
				{/if}
			{/foreach}
		</select>
	</div>
	<div class="input-group-addon input-group-search">
            <button class="icon-magnifier"></button>
    </div>
	<input type="hidden" name="cat_id" value="" />
	<input type="hidden" name="controller" value="search" />
	<input type="hidden" name="fc" value="module" />
	<input type="hidden" name="module" value="jmsadvsearch" />			
</form>
<div id="search_result">
</div>
</div>
