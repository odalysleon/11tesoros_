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

<script type="text/javascript">
	$(document).ready(function(){
		$('#filter-by-category').click(function() {
			if ($(this).is(':checked')) {
				$('#block_category_tree').show();
				$('#category-tree-toolbar').show();
			} else {
				$('#block_category_tree').hide();
				$('#category-tree-toolbar').hide();
				location.href = '{$base_url}{* Cannot parse variable because it contains html *}&reset_filter_category=1';
			}
		});
	});
</script>

<div class="tree-panel-heading-controls clearfix">
	<div id="category-tree-toolbar" {if !$is_category_filter}style="display:none;"{/if}>
		{if isset($toolbar)}{$toolbar}{/if}{* Cannot parse variable because it contains html *}
	</div>
	<label class="tree-panel-label-title">
		<input type="checkbox" id="filter-by-category" name="filter-by-category" {if $is_category_filter}checked="checked"{/if} />
		<i class="icon-tags"></i>
		{$title|escape:'htmlall':'UTF-8'}
	</label>
</div>
