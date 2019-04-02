{*
 * @package Jms Drop Megamenu
 * @version 1.0
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}
<a class="ver-btn" id="ver-btn">
	<i class="fa fa-bars"></i>
	{if $jpb_homepage == 2 || $jpb_homepage == 4}
		{l s='DEPARTMENT' d='Modules.JmsVermegamenu'}
		<i class="fa fa-caret-down" aria-hidden="true"></i>
	{else}
		{l s='CATEGORIES' d='Modules.JmsVermegamenu'}
	{/if}
</a> 
<div class="ver-menu-box open">
	{$vermenu_html nofilter}
	<a class="all-cat-btn btn-default" href="#">
		{l s='ALL CATEGORIES' d='Modules.JmsVermegamenu'}
		<i class="fa fa-angle-right"></i>
	</a>
</div>
<script type="text/javascript">
	var jvmmm_event = '{$JMSVMM_MOUSEEVENT}',
		jvmmm_duration = '{$JMSVMM_DURATION}';
</script>
