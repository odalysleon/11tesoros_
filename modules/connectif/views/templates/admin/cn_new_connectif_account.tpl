{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{html_entity_decode($message|escape:'htmlall':'UTF-8')}


<div class="bootstrap panel connectif">
    <h3><i class="icon-puzzle-piece"> </i>{l s=' New connectif account' mod='connectif'}</h3>
    <form id="module_form" class="form-horizontal" action="" method="post">
        <h4 style="margin-left: 20%;">{l s='New connectif account.' mod='connectif'}</h4>
    </form>

    <form id="module_form" class="form-horizontal" action="" method="post">

        <div class="" style="position: relative">

            <ps-input-text label="{l s='Client ID:' mod='connectif'}" id="CN_NEW_CLIENT_ID" name="CN_NEW_CLIENT_ID"
                           value="{$CN_NEW_CLIENT_ID|escape:'htmlall':'UTF-8'}" size="10" required-input="false"
                           fixed-width="xxl"></ps-input-text>
            <ps-input-text label="{l s='Secret KEY:' mod='connectif'}" id="CN_NEW_API" name="CN_NEW_API"
                           value="{$CN_NEW_API|escape:'htmlall':'UTF-8'}" size="10" required-input="false"
                           fixed-width="xxl"></ps-input-text>

            <ps-select label="{l s='Language:' mod='connectif'}" id="CN_SHOP_LANG_SELECTOR" name="CN_SHOP_LANG_SELECTOR" chosen='true'
                       fixed-width="xxl" size="10" required-input="true"
                       value="{$CN_SHOP_LANG_SELECTOR|escape:'htmlall':'UTF-8'}">
                {foreach from=$CN_LANGUAGES item=cn_lang key=index }
                    <option value="{$cn_lang["id_lang"]|escape:'htmlall':'UTF-8'}">{$cn_lang["name"]|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </ps-select>

            <ps-select label="{l s='Currency:' mod='connectif'}" id="CN_SHOP_CURRENCY_SELECTOR" name="CN_SHOP_CURRENCY_SELECTOR" chosen='true'
                       fixed-width="xxl" size="10" required-input="true"
                       value="{$CN_SHOP_CURRENCY_SELECTOR|escape:'htmlall':'UTF-8'}">
                {foreach from=$CN_CURRENCIES item=cn_currency key=index }
                    <option value="{$cn_currency["id_currency"]|escape:'htmlall':'UTF-8'}">{$cn_currency["name"]|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </ps-select>

            <ps-switch id="CN_NEW_ACCOUNT_ACTIVE" name="CN_NEW_ACCOUNT_ACTIVE"  label="{l s='Enabled:' mod='connectif'}"
                       yes="{l s='Yes' mod='connectif'}"
                       no="{l s='No' mod='connectif'}" {if $CN_NEW_ACCOUNT_ACTIVE == '1'}active="true"{/if}></ps-switch>


        </div>

        <div class="panel-footer">
            <button id="submit_new_connectif_account"
                    name="submit_new_connectif_account" type="submit"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i>{l s='Save' mod='connectif'}
            </button>
        </div>


    </form>

</div>
