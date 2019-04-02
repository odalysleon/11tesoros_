{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{html_entity_decode($message|escape:'htmlall':'UTF-8')}


<div class="bootstrap panel connectif">
    <h3><i class="icon-puzzle-piece"> </i>{l s=' New banner' mod='connectif'}</h3>
    <form id="module_form" class="form-horizontal" action="" method="post">
            <h4 style="margin-left: 20%;">{l s='New Banner.' mod='connectif'}</h4>
    </form>

    <form id="module_form" class="form-horizontal" action="" method="post">

        <div class="" style="position: relative">

            <ps-input-text label="{l s='Banner ID:' mod='connectif'}" id="CN_BANNER_ID" name="CN_BANNER_ID"
                           value="{$CN_BANNER_ID|escape:'htmlall':'UTF-8'}" size="10" required-input="true"
                           fixed-width="xxl"></ps-input-text>
           {* <ps-input-text label="{l s='Name:' mod='connectif'}" id="CN_BANNER_NAME" name="CN_BANNER_NAME"
                           value="{$CN_BANNER_NAME|escape:'htmlall':'UTF-8'}" size="10" required-input="true"
                           fixed-width="xxl"></ps-input-text>*}

            <ps-select label="{l s='Type:' mod='connectif'}" id="CN_BANNER_TYPE" name="CN_BANNER_TYPE" chosen='true'
                                    fixed-width="xxl" size="10" required-input="true"
                                    value="{$CN_BANNER_TYPE|escape:'htmlall':'UTF-8'}">
                <option value="DisplayFooter">DisplayFooter</option>
                <option value="DisplayHeader">DisplayHeader</option>
                <option value="DisplayHome">DisplayHome</option>
                <option value="DisplayLeftColumn">DisplayLeftColumn</option>
                <option value="DisplayRigthColumn">DisplayRigthColumn</option>
                <option value="DisplayTopColumn">DisplayTopColumn</option>
                <option value="DisplayProductFooter">DisplayProductFooter</option>
            </ps-select>

            <ps-switch id="CN_BANNER_ACTIVE" name="CN_BANNER_ACTIVE"  label="{l s='Enabled:' mod='connectif'}"
                       yes="{l s='Yes' mod='connectif'}"
                       no="{l s='No' mod='connectif'}" {if $CN_BANNER_ACTIVE == '1'}active="true"{/if}></ps-switch>


        </div>

        <div class="panel-footer">
            <button id="submit_new_banner"
                    name="submit_new_banner" type="submit"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i>{l s='Save' mod='connectif'}
            </button>
        </div>


    </form>

</div>
