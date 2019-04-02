{*
 * @package Jms Ajax Search
 * @version 1.1
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}
	<div id="jms_ajax_search" class="btn-group compact-hidden">
		<a href="#" class="ic-search dropdown-toggle" data-toggle="dropdown">	
			<span class="icon-search"></span>
		</a>
		<div class="search-box">	
			<form method="get" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" id="searchbox">
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input type="text" id="ajax_search" name="search_query" placeholder="{l s='Search' d='Modules.JmsAjaxsearch'}" class="form-control" />	
			<a href="#" class="btn-xs ic-search2">	
				<span class="icon-search"></span>
			</a>		
			</form>
			<div id="search_result">
			</div>
		</div>	
	</div>

