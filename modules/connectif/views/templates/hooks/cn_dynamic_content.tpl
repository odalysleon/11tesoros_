{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{foreach from=$cn_banners item=cn_banner key=index }
    {if $cn_banner->banner_is_active == "1"}
        <div class="cn_banner_placeholder" id="{$cn_banner->banner_id|escape:'htmlall':'UTF-8'}" style="{$content_div_styles|escape:'htmlall':'UTF-8'}"></div>
    {/if}
{/foreach}