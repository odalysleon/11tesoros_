{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from EURL ébewè - www.ebewe.net
* Use, copy, modification or distribution of this source file without written
* license agreement from the EURL ébewè is strictly forbidden.
* In order to obtain a license, please contact us: contact@ebewe.net
* ...........................................................................
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe EURL ébewè - www.ebewe.net
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la EURL ébewè est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la EURL ébewè a l'adresse: contact@ebewe.net
* ...........................................................................
*
*  @package   ProductCarrier
*  @author    Paul MORA
*  @copyright Copyright (c) 2011-2017 EURL ébewè - www.ebewe.net - Paul MORA
*  @license   Commercial license
*  Support by mail  :  contact@ebewe.net
*}

<div class="panel">
	{if isset($header)}{$header}{* Cannot parse variable because it contains html *}{/if}
	<div id="block_category_tree"{if !$is_category_filter} style="display:none"{/if}>
		{if isset($nodes)}
		<ul id="{$id|escape:'html':'UTF-8'}" class="tree">
			{$nodes}{* Cannot parse variable because it contains html *}
		</ul>
		{/if}
	</div>
</div>
<script type="text/javascript">
	{if isset($use_checkbox) && $use_checkbox == true}
		function checkAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", true);
					$(this).parent().addClass("tree-selected");
				}
			);
		}

		function uncheckAllAssociatedCategories($tree)
		{
			$tree.find(":input[type=checkbox]").each(
				function()
				{
					$(this).prop("checked", false);
					$(this).parent().removeClass("tree-selected");
				}
			);
		}
	{/if}
	{if isset($use_search) && $use_search == true}
		$("#{$id|escape:'html':'UTF-8'}-categories-search").bind("typeahead:selected", function(obj, datum) {
		    $("#{$id|escape:'html':'UTF-8'}").find(":input").each(
				function()
				{
					if ($(this).val() == datum.id_category)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents("ul.tree").each(
							function()
							{
								$(this).children().children().children(".icon-folder-close")
									.removeClass("icon-folder-close")
									.addClass("icon-folder-open");
								$(this).show();
							}
						);
					}
				}
			);
		});
	{/if}
	$(document).ready(function () {
		var tree = $("#{$id|escape:'html':'UTF-8'}").tree("collapseAll");

		tree.on('collapse', function() {
			$('#expand-all-{$id|escape:'html':'UTF-8'}').show();
		});

		tree.on('expand', function() {
			$('#collapse-all-{$id|escape:'html':'UTF-8'}').show();
		});

		$('#collapse-all-{$id|escape:'html':'UTF-8'}').hide();
		$("#{$id|escape:'html':'UTF-8'}").find(":input[type=radio]").click(
			function()
			{
				location.href = location.href.replace(
					/&id_category=[0-9]*/, "")+"&id_category="
					+$(this).val();
			}
		);

		{if isset($selected_categories)}
			{assign var=imploded_selected_categories value='","'|implode:$selected_categories}
			var selected_categories = new Array("{$imploded_selected_categories|escape:'htmlall':'UTF-8'}");

			$("#{$id|escape:'html':'UTF-8'}").find(":input").each(
				function()
				{
					if ($.inArray($(this).val(), selected_categories) != -1)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
						$(this).parents("ul.tree").each(
							function()
							{
								$(this).children().children().children(".icon-folder-close")
									.removeClass("icon-folder-close")
									.addClass("icon-folder-open");
								$(this).show();
							}
						);
					}
				}
			);
		{/if}
	});
</script>
