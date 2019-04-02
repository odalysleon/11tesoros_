{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
<div class="cn_client_info" style="display:none">
    <span class="primary_key">{$client_email|escape:'htmlall':'UTF-8'}</span>
    <span class="_name">{$client_name|escape:'htmlall':'UTF-8'}</span>
    <span class="_surname">{$client_surname|escape:'htmlall':'UTF-8'}</span>
    <span class="_birthdate">{$client_birthday|escape:'htmlall':'UTF-8'}</span>
    <span class="_newsletter_subscription_status">{$client_email_status|escape:'htmlall':'UTF-8'}</span>
    {foreach from=$client_extended_properties item=extended_prop key=index }
            <span class="{$extended_prop["id"]|escape:'htmlall':'UTF-8'}">{$extended_prop["value"]|escape:'htmlall':'UTF-8'}</span>
    {/foreach}
</div>