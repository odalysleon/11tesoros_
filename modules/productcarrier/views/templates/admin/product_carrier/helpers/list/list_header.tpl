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

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
    {if isset($category_tree)}
        <div class="bloc-leadin">
            <div id="container_category_tree">
                {$category_tree}{* Cannot parse variable because it contains html *}
            </div>
        </div>
    {/if}
    <div id="confirm_true" class="bootstrap" style="display:none;">
        <div class="text-center">
            <p>{l s='Are you sure you want to enable carrier' mod='productcarrier'}<span class="fancybox_name"></span>{l s='for all products' mod='productcarrier'} ?</p>
            <input type="button" class="confirm no btn btn-default" value="{l s='No' mod='productcarrier'}" /> <input type="button" class="confirm yes btn btn-default" value="{l s='Yes' mod='productcarrier'}" />
        </div>
    </div>
    <div id="confirm_false" class="bootstrap" style="display:none;">
        <div class="text-center">
            <p>{l s='Are you sure you want to disable carrier' mod='productcarrier'}<span class="fancybox_name"></span>{l s='for all products' mod='productcarrier'} ?</p>
            <input type="button" class="confirm no btn btn-default" value="{l s='No' mod='productcarrier'}" /> <input type="button" class="confirm yes btn btn-default" value="{l s='Yes' mod='productcarrier'}" />
        </div>
    </div>
{/block}

{block name="override_form_extra"}
    <input id="all_carriers" type="hidden" value="{$all_carriers|escape:'html':'UTF-8'}" />
    <input id="all_products" type="hidden" value="" />
    <input id="success_message_product" type="hidden" value="{l s='Carriers updated for product' mod='productcarrier'}" />
    <input id="error_message_product" type="hidden" value="{l s='An error occured while updating carriers for product' mod='productcarrier'}" />
    <input id="success_message_carrier_1" type="hidden" value="{l s='Carrier' mod='productcarrier'}" />
    <input id="success_message_carrier_2" type="hidden" value="{l s='enabled for all products' mod='productcarrier'}" />
    <input id="success_message_carrier_3" type="hidden" value="{l s='disabled for all products' mod='productcarrier'}" />
    <input id="error_message_carrier" type="hidden" value="{l s='An error occured while updating products for carrier' mod='productcarrier'}" />
{/block}
