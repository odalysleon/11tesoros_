{*
 * @package Jms Ajax Search
 * @version 1.1
 * @Copyright (C) 2009 - 2017 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}

<div id="jms_ajax_search" class="btn-group compact-hidden">	
	<form method="get" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" id="searchbox">
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
		<input type="text" id="ajax_search" name="search_query" placeholder="{l s='Search' d='Modules.jmsajaxsearch'}" class="form-control" />		
	</form>
	<div id="search_result">
	</div>	
</div>