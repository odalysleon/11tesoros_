{*
 * @package Jms Vertical Megamenu
 * @version 1.0
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}
<a class="ver-btn" id="ver-btn1">
	<i class="fa fa-bars"></i>
	{if $jpb_homepage == 2 || $jpb_homepage == 4}
		{l s='DEPARTMENT' d='Modules.JmsVermegamenu'}
		<i class="fa fa-caret-down" aria-hidden="true"></i>
	{else}
		{l s='CATEGORIES' d='Modules.JmsVermegamenu'}
	{/if}
</a> 
<div id="mobile-vermegamenu" class="ver-menu-mobile">
	{$vermenu_html nofilter}
</div>