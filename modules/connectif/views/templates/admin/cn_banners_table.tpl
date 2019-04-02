{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
<div class="panel-heading" style="height: 0px; border: none; margin-bottom: 24px;">
                        <span class="panel-heading-action">
                            <a id="desc-banner-new" class="list-toolbar-btn"
                               href="{$update_banner_url|escape:'htmlall':'UTF-8'}">
                                <span title="" data-toggle="tooltip" class="label-tooltip"
                                      data-original-title="{l s='Add new banner' mod='connectif'}" data-html="true">
                                    <i class="process-icon-new "></i>
                                </span>
                            </a>

                        </span>
</div>

<div class="table-responsive clearfix">
    <table class="table  banners">
        <thead>
        <tr class="nodrag nodrop">
            <th class="center fixed-width-xs">
            </th>
            <th class="">
                <span class="title_box ">{l s='ID' mod='connectif'}</span>
            </th>
            <th class="">
                <span class="title_box "> {l s='Type' mod='connectif'}</span>
            </th>
            <th class="">
                <span class="title_box "> {l s='Status' mod='connectif'}</span>
            </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$CN_BANNERS item=cn_banner key=index }
            <tr class=" odd">
                <td class="text-center"></td>

                <td class="pointer">
                    {$cn_banner->banner_id|escape:'htmlall':'UTF-8'}
                </td>

                <td class="pointer">
                    {$cn_banner->banner_type|escape:'htmlall':'UTF-8'}
                </td>

                <td class="pointer">
                    {if $cn_banner->banner_is_active == "1"}
                        <a class="list-action-enable  action-enabled"
                           href="{$enable_banner_url|escape:'htmlall':'UTF-8'}{$index|escape:'htmlall':'UTF-8'}"
                           title="{l s='Enabled' mod='connectif'}">
                            <i class="icon-check"></i>
                            <i class="icon-remove hidden"></i>
                        </a>
                    {/if}


                    {if $cn_banner->banner_is_active == "0"}
                        <a class="list-action-enable  action-disabled"
                           href="{$enable_banner_url|escape:'htmlall':'UTF-8'}{$index|escape:'htmlall':'UTF-8'}"
                           title="{l s='Disabled' mod='connectif'}">
                            <i class="icon-check hidden"></i>
                            <i class="icon-remove"></i>
                        </a>
                    {/if}
                </td>

                <td class="text-right">
                    <div class="btn-group pull-right">
                        <a href="{$delete_banner_url|escape:'htmlall':'UTF-8'}{$index|escape:'htmlall':'UTF-8'}"
                           onclick="onDeleteBanner('{$cn_banner->banner_id|escape:'htmlall':'UTF-8'}')"
                           title="Delete" class="delete btn btn-default">
                            <i class="icon-trash"></i>  {l s='Delete' mod='connectif'}
                        </a>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>

