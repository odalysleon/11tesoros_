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

{extends file="helpers/list/list_content.tpl"}

{block name="open_td"}
    <td class="{if isset($params.class)} {$params.class|escape:'htmlall':'UTF-8'}{/if}{if isset($params.align)} {$params.align|escape:'htmlall':'UTF-8'}{/if}">
{/block}
{block name="td_content"}
    {if isset($tr.$key)}
        {if isset($params.active)}
            {$tr.$key}{* Cannot parse variable because it contains html *}
        {elseif isset($params.image)}
            {$tr.$key}{* Cannot parse variable because it contains html *}
        {elseif isset($params.type) && $params.type == 'price'}
            {displayPrice price=$tr.$key}
        {else}
            {$tr.$key|escape:'html':'UTF-8'}
        {/if}

    {* Added for Product Carrier *}
    {elseif isset($params.type) && $params.type == 'checkbox'}
        {assign var=carriers value=AdminProductCarrierController::getProductsCarriers($tr.$identifier)}
        {if isset($params.ind)}<input id="carrier_product_{$tr.$identifier|escape:'html':'UTF-8'}" type="hidden" value="{if isset($carriers[$tr.$identifier|escape:'html':'UTF-8'] )}{foreach from=$carriers[$tr.$identifier|escape:'html':'UTF-8'] item=carrier name=carriers}{$carrier|escape:'htmlall':'UTF-8'},{/foreach}{else}{$params.all_carriers|escape:'htmlall':'UTF-8'}{/if}" />{/if}
        {if !isset($carriers[$tr.$identifier|escape:'html':'UTF-8']) OR (isset($carriers[$tr.$identifier|escape:'html':'UTF-8']) AND $params.carrier|in_array:$carriers[$tr.$identifier|escape:'html':'UTF-8']) }
            <a id="product_carrier_{$tr.$identifier|escape:'html':'UTF-8'}_{$params.carrier|escape:'htmlall':'UTF-8'}" href="." class="update_carrier list-action-enable action-enabled" data-carrier="{$params.carrier|escape:'htmlall':'UTF-8'}" data-product="{$tr.$identifier|escape:'html':'UTF-8'}">
                <i class="icon-check"></i>
            </a>
        {else}
            <a id="product_carrier_{$tr.$identifier|escape:'html':'UTF-8'}_{$params.carrier|escape:'htmlall':'UTF-8'}" href="." class="update_carrier list-action-enable action-disabled" data-carrier="{$params.carrier|escape:'htmlall':'UTF-8'}" data-product="{$tr.$identifier|escape:'html':'UTF-8'}">
                <i class="icon-remove"></i>
            </a>
        {/if}
    {* Added for Product Carrier *}

    {else}
        {block name="default_field"}--{/block}
    {/if}
{/block}