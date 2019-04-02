{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2019 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{foreach from=$categories item=category}
    <span style="display:none" class="cn_page_category">{$category|escape:'htmlall':'UTF-8'}</span>
{/foreach}
