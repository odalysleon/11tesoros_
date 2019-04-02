{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block user information module HEADER -->
<div id="_desktop_user_info" class="btn-group compact-hidden user-info">
			{if $logged}
				<a class="btn-xs dropdown-toggle login account" data-toggle="dropdown" href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}" rel="nofollow">
						<span class="text">
							<i class="icon-head"></i>
						</span>
				</a>
				<ul role="menu" class="dropdown-menu">
					<li>
						<a class="account" href="{$my_account_url}" title="{l s='View my customer account' d='Shop.Theme.CustomerAccount'}" rel="nofollow"> 
							{l s='My account' d='Shop.Theme.CustomerAccount'}
						</a>
					</li>
					<li>
						<a class="logout hidden-sm-down" href="{$logout_url}" rel="nofollow">
							{l s='Log out' d='Shop.Theme.Actions'}
						</a>
					</li>
				</ul>
			{else}
				<a href="{$my_account_url}" title="{l s='Log in to your customer account' d='Shop.Theme.CustomerAccount'}" rel="nofollow">
					<span class="text">
						<i class="icon-head"></i>
					</span>
				</a>
			{/if}
	</div>