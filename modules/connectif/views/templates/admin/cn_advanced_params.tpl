{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
<ps-switch id="CN_PRODUCT_REFERENCE" name="CN_PRODUCT_REFERENCE" label="{l s='Use product SKU:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_PRODUCT_REFERENCE == '1'}active="true"{/if}></ps-switch>

{*SELECT PRODUCT IMAGE SIZE*}
<div class="" style="position: relative">
    <ps-select label="{l s='Select Product Image Size:' mod='connectif'}" id="CN_PRODUCT_IMAGE_SIZE" name="CN_PRODUCT_IMAGE_SIZE" chosen='false'
        fixed-width="xxl" size="10" required-input="true" value="{$CN_PRODUCT_IMAGE_SIZE|escape:'htmlall':'UTF-8'}">
        {foreach from=$CN_IMAGE_SIZES_AVAILABLE item=cn_image_size key=index }
            {if ($cn_image_size["products"] == '1')}
                <option value="{$cn_image_size["name"]|escape:'htmlall':'UTF-8'}"
                    {if ($cn_image_size["name"] == $CN_PRODUCT_IMAGE_SIZE)}selected="true"{/if}>
                    {$cn_image_size["name"]|escape:'htmlall':'UTF-8'}
                </option>
            {/if}
        {/foreach}
    </ps-select>
</div>
{*ENABLE PURCHASES EVENTS*}
<ps-switch id="CN_PURCHASE_EVENT" name="CN_PURCHASE_EVENT" label="{l s='Purchase events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_PURCHASE_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE REGISTER EVENTS*}
<ps-switch id="CN_REGISTER_EVENT" name="CN_REGISTER_EVENT" label="{l s='Register account events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_REGISTER_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE LOGIN EVENTS*}
<ps-switch id="CN_LOGIN_EVENT" name="CN_LOGIN_EVENT" label="{l s='Login account events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_LOGIN_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE NEWSLETTER SUBSCRIBE EVENTS*}
<ps-switch id="CN_NEWSLETTER_EVENT" name="CN_NEWSLETTER_EVENT"
           label="{l s='Subscribe newsletter events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_NEWSLETTER_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE CART STATUS EVENTS*}
<ps-switch id="CN_CART_STATUS_EVENT" name="CN_CART_STATUS_EVENT" label="{l s='Cart status events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_CART_STATUS_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE SEARCH EVENTS*}
<ps-switch id="CN_SEARCH_EVENT" name="CN_SEARCH_EVENT" label="{l s='Search events:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_SEARCH_EVENT == '1'}active="true"{/if}></ps-switch>

{*ENABLE ROUNDED UNIT_PRICE*}
<ps-switch id="CN_ROUND_PRICE" name="CN_ROUND_PRICE" label="{l s='Round unit prices:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_ROUND_PRICE == '1'}active="true"{/if}></ps-switch>

{*ENABLE LOGS*}
<ps-switch id="CN_LOGS_STATUS" name="CN_LOGS_STATUS" label="{l s='Logs enabled:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_LOGS_STATUS == '1'}active="true"{/if}></ps-switch>
{*ENABLE MULTISHOP*}
<ps-switch id="CN_MULTILANG_STATUS" name="CN_MULTILANG_STATUS" label="{l s='Multi-language enabled:' mod='connectif'}"
           yes="{l s='Yes' mod='connectif'}"
           no="{l s='No' mod='connectif'}" {if $CN_MULTILANG_STATUS == '1'}active="true"{/if}></ps-switch>