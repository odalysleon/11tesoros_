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

<option class="category_{$node.id}{if isset($last) && $last == 'true'} last{/if}" value="{$node.id}" style="padding-left:{$node.level_depth}0px">
	{$node.name|escape:'html':'UTF-8'} {if $number}({$node.numberproduct|escape:'html':'UTF-8'}){/if}		
</option>
	{if $node.children|@count > 0}		
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{if $smarty.foreach.categoryTreeBranch.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child last='false'}
			{/if}
		{/foreach}		
	{/if}

