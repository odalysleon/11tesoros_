{*
 * @package Jms Drop Megamenu
 * @version 1.0
 * @Copyright (C) 2009 - 2015 Joommasters.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
*}

<div class="jms-megamenu-wrap">
	<button class="menu-button hidden-lg hidden-md" id="open-button">
		<span class="fa fa-bars"></span>
	</button>
	{$menu_html|escape:'' nofilter}
</div>
<script type="text/javascript">
	var jmmm_event = '{$JMSMM_MOUSEEVENT}';
	var jmmm_duration = {$JMSMM_DURATION};	
</script>