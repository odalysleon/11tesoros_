{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
<div class="alert alert-info" role="alert">
    <p class="alert-text">{l s='Active fields in this section will be added to cn_client_info tag and will be sent to Connectif.' mod='connectif'}</p>
    <p>{l s='Custom fields with the same ID as the one shown close to the activation switch will be automatically mapped.' mod='connectif'}</p>
</div>
<h2>{l s='Customer Fields' mod='connectif'}</h2>
<div class="alert alert-info" role="alert">
    <p class="alert-text">{l s='Fields in this section represents customer fields' mod='connectif'}</p>
</div>
<ps-switch id="firstname" name="firstname" label="firstname"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" active="true" disabled="true">
</ps-switch>
<ps-switch id="lastname" name="lastname" label="lastname"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" active="true" disabled="true">
</ps-switch>
<ps-switch id="birthday" name="birthday" label="birthday"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" active="true" disabled="true">
</ps-switch>
{foreach from=$CN_REGISTER_FIELDS item=cn_field key=index }
    {if cn_field !== ''}
        <ps-switch id="{$cn_field["name"]|escape:'htmlall':'UTF-8'}" name="{$cn_field["name"]|escape:'htmlall':'UTF-8'}"
                   label="{$cn_field["name"]|escape:'htmlall':'UTF-8'}"
                   yes="{l s='Yes' mod='connectif'}"
                   no="{l s='No' mod='connectif'}" {if $cn_field["isActive"]}active="true"{/if}>
        </ps-switch>
    {/if}
{/foreach}

<h2>{l s='Address Fields' mod='connectif'}</h2>
<div class="alert alert-info" role="alert">
    <p class="alert-text">{l s='Fields in this section represents address fields. If a customer have more than one address we will use the last address used in a purchase. If the customer hasn\'t bought anything yet, we will use the last updated address' mod='connectif'}</p>
</div>
{foreach from=$CN_REGISTER_FIELDS_ADDRESS item=cn_field key=index }
    {if cn_field !== ''}
        <ps-switch id="{$cn_field["name"]|escape:'htmlall':'UTF-8'}" name="{$cn_field["name"]|escape:'htmlall':'UTF-8'}"
                   label="{$cn_field["name"]|escape:'htmlall':'UTF-8'}"
                   yes="{l s='Yes' mod='connectif'}"
                   no="{l s='No' mod='connectif'}" {if $cn_field["isActive"]}active="true"{/if}>
        </ps-switch>
    {/if}
{/foreach}