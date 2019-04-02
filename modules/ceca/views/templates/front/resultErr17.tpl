{*
 * 2011-2017 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2016 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 *}
{extends file='page.tpl'}

{block name="page_content"}
<h1 id="cart_title">{l s='Credit card payment result' mod='ceca'}</h1>

<div style="margin: 20px;">
	<div style="float:left; margin-right: 10px;"><img style="margin-right: 10px;" src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ko.png" alt="KO" /></div>
	<div>
		<p>{l s='There has been an error in the payment process.' mod='ceca'}</p>
		<p>
		{if version_compare($smarty.const._PS_VERSION_,'1.5','>')}
			<a href="{$link->getPageLink('order', true, NULL, 'multi-shipping=')|escape:'html':'UTF-8'}" title="{l s='Please try again clicking here' mod='ceca'}" class="button-exclusive btn btn-default">
		{else}
			<a href="{$link->getPageLink('order.php', true, NULL, '')|escape:'html':'UTF-8'}?step=1" title="{l s='Please try again clicking here' mod='ceca'}" class="button-exclusive btn btn-default">
		{/if}
							{l s='Please try again clicking here' mod='ceca'}
						</a>
		</p>
	</div>
</div>
{/block}
