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
<style>
.jms-nav{
	margin-bottom:10px;
}
.jms-menu{
	background: -moz-linear-gradient(center top , #fff 0%, #fff 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #e6e6e6;
    border-radius: 5px;
    box-shadow: 0 0 9px rgba(0, 0, 0, 0.15);
    list-style: none outside none;
    padding: 0 20px;
	height: 39px;
    position: relative;
}
.jms-menu li{
	float:left;
}
.jms-menu li:hover,.jms-menu li.active {
    background: -moz-linear-gradient(center top , #4f5964 0%, #5f6975 40%) repeat scroll 0 0 rgba(0, 0, 0, 0);
}
.jms-menu li a{
	color: #757575;
    display: block;
    padding: 10px 20px;
    text-decoration: none !important;
}
.jms-menu li:hover a,.jms-menu li.active a{
	color: #fff;
	text-dicoration:none;
}
</style>
<div class="jms-nav">
	<ul class="jms-menu">
		<li class="{if  $view == 'categories'}active{/if}"><a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsflashsales&categories=1">Categories</a></li>
		<li class="{if  $view == 'items'}active{/if}"><a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsflashsales">Items</a></li>		
		<li class="{if  $view == 'config'}active{/if}"><a href="{$link->getAdminLink('AdminModules')|escape:'html'}&configure=jmsflashsales&config=1">Config</a></li>
	</ul>
</div>