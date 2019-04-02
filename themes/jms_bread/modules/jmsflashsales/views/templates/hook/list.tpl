{*
* 2007-2015 PrestaShop
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
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Item list' d='Modules.JmsFlashsales'}
	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsflashsales&addItem=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add New" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="slidesContent">
		<div id="slides">
			{foreach from=$items item=item}
				<div id="slides_{$item.id_item|escape:'html':'UTF-8'}" class="panel">
					<div class="row">
						<div class="col-lg-1">
							<span><i class="icon-arrows"></i></span>
						</div>						
						<div class="col-md-4">
							<h4 class="pull-left">#{$item.id_item|escape:'html':'UTF-8'} - {$item.productname|escape:'html':'UTF-8'}</h4>							
						</div>	
						<div class="col-md-3">
							{$item.catname|escape:'html':'UTF-8'}
						</div>
						<div class="col-md-4">
							<div class="btn-group-action pull-right">								
								<a class="btn {if $item.active}btn-success{else}btn-danger{/if}"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsflashsales&changeStatus&id_item={$item.id_item|escape:'html':'UTF-8'}" title="title="{if $item.active}Enabled{else}Disabled{/if}"">
									<i class="{if $item.active}icon-check{else}icon-remove{/if}"></i>{if $item.active}Enabled{else}Disabled{/if}
								</a>		
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsflashsales&id_item={$item.id_item|escape:'html':'UTF-8'}">
									<i class="icon-edit"></i>
									{l s='Edit' d='Modules.JmsFlashsales'}
								</a>
								<a class="btn btn-default"
									href="{$link->getAdminLink('AdminModules')|escape:'html':'UTF-8'}&configure=jmsflashsales&delete_id_item={$item.id_item|escape:'html':'UTF-8'}">
									<i class="icon-trash"></i>
									{l s='Delete' d='Modules.JmsFlashsales'}
								</a>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</div>