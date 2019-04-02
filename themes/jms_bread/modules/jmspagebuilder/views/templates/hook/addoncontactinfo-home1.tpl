{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $addon_title}
<div class="addon-title">
	<h3>{$addon_title|escape:'htmlall':'UTF-8'}</h3>
</div>
{/if}
{if $addon_desc}
<p class="addon-desc">{$addon_desc|escape:'htmlall':'UTF-8'}</p>
{/if}
<div class="contact-info{if $box_class} {$box_class|escape:'htmlall':'UTF-8'}{/if}">
    <div class="left-content">
		<ul>
	       	{if $ci_address != ''}
	       		<li>
	       			<span>{l s='Address' mod='jmspagebuilder'}:</span> {$ci_address|escape:'html':'UTF-8'}
	       		</li>
	       	{/if}
		   	{if $email != ''}
		   		<li>
		   			<span>{l s='Email' mod='jmspagebuilder'}:</span> {$email|escape:'html':'UTF-8'}
		   		</li>
		   	{/if}
		   	{if $opentime != ''}
		   		<li>
		   			<span>{l s='Opening' mod='jmspagebuilder'}:</span> {$opentime|escape:'html':'UTF-8'}
		   		</li>
		   	{/if}
   	 	</ul>
	</div>
	{if $phone != ''}
		<div class="right-content">
			<div class="icon">
			</div>
			<div class="desc-text">
				<span class="phone-number">{$phone|escape:'html':'UTF-8'}</span>
			<span class="text">{l s='We support online 24/24 on day' mod='jmspagebuilder'}</span>
			</div>	
		</div>
	{/if}
</div>
