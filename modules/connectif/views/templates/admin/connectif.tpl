{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}
{html_entity_decode($message|escape:'htmlall':'UTF-8')}


<div class="bootstrap panel connectif">
    <h3><i class="icon-cogs"> </i>{l s=' Settings' mod='connectif'}</h3>
    {if $is_plugin_active==1}
        <h4 style="margin-left: 20%; color: green;">{l s='The plugin is active.' mod='connectif'}</h4>
    {else}
        <h4 style="margin-left: 20%; color: red;">{l s='The plugin is not active.' mod='connectif'}</h4>
    {/if}

    <form id="module_form" class="form-horizontal" action="" method="post">
        <div class="" style="position: relative">
            <ps-tabs position="left">
                <ps-tab label="{l s='Basic config' mod='connectif'}" active="true" id="tab1" icon="icon-AdminAdmin">
                    {if $multilang_enabled == false}
                        {*CONFIG PANEL FOR ONLY ONE CONNECTIF ACCOUNT*}
                        <ps-switch id="CN_ACTIVE" name="CN_ACTIVE"  label="{l s='Enabled:' mod='connectif'}"
                                   yes="{l s='Yes' mod='connectif'}"
                                   no="{l s='No' mod='connectif'}" {if $CN_ACTIVE == '1'}active="true"{/if}></ps-switch>
                        <ps-input-text label="{l s='Client ID:' mod='connectif'}" id="CN_CLIENT_ID" name="CN_CLIENT_ID"
                                       value="{$CN_CLIENT_ID|escape:'htmlall':'UTF-8'}" size="10" required-input="false"
                                       fixed-width="xxl"></ps-input-text>
                        <ps-input-text label="{l s='Secret KEY:' mod='connectif'}" id="CN_API" name="CN_API"
                                       value="{$CN_API|escape:'htmlall':'UTF-8'}" size="10" required-input="false"
                                       fixed-width="xxl"></ps-input-text>
                    {else}
                        {*CONFIG PANEL FOR MULTIPLE CONNECTIF ACCOUNT*}
                        {include file="{$module_templates}admin/cn_multilang_table.tpl"}
                    {/if}
                </ps-tab>
                <ps-tab label="{l s='Banners' mod='connectif'}" id="tab2" icon="icon-AdminParentModules">
                    {include file="{$module_templates}admin/cn_banners_table.tpl"}
                </ps-tab>
                <ps-tab label="{l s='Public URLs' mod='connectif'}" id="tab3" icon="icon-envelope">
                    {l s='Opt-out URL:' mod='connectif'}<br/>
                    {$CN_OPT_OUT_URL|escape:'htmlall':'UTF-8'}<br>
                    {l s='Service Worker URL:' mod='connectif'}<br/>
                    {$CN_SERVICE_WORKER_URL|escape:'htmlall':'UTF-8'}<br>
                </ps-tab>
                <ps-tab label="{l s='Register form fields' mod='connectif'}" id="tab4" icon="icon-user">
                    {include file="{$module_templates}admin/cn_register_form_fields.tpl"}
                </ps-tab>
                <ps-tab label="{l s='Advanced params' mod='connectif'}" id="tab5" icon="icon-AdminTools">
                    {include file="{$module_templates}admin/cn_advanced_params.tpl"}
                </ps-tab>
            </ps-tabs>
        </div>
        {if $logs_enabled}
            <div style="position: relative">
                <button id="submit_{$module_name|escape:'htmlall':'UTF-8'}"
                        name="downloadLogs_{$module_name|escape:'htmlall':'UTF-8'}" type="submit"
                        class="btn btn-default col-lg-offset-3" style="margin-bottom: 5px;">
                    {l s='Download Connectif Logs' mod='connectif'}
                </button>
            </div>
        {/if}
        <div class="panel-footer">
            <button id="submit_{$module_name|escape:'htmlall':'UTF-8'}"
                    name="submit_{$module_name|escape:'htmlall':'UTF-8'}" type="submit"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i>{l s='Save' mod='connectif'}
            </button>
        </div>
    </form>
</div>

<script>

    function onDeleteBanner(banner_id) {
        if (confirm("{l s='Delete selected banner?' mod='connectif'}" + '\n\n' + "{l s='ID:' mod='connectif'}" + banner_id)) {
            return true
        } else {
            event.stopPropagation();
            event.preventDefault();
        }
    }

    function onDeleteAccount(client_id) {
        if (confirm("{l s='Delete selected connectif account? (This acction only will delete the config from prestashop, not the original account on Connectif platform)' mod='connectif'}" + '\n\n' + "{l s='Client ID:' mod='connectif'}" + client_id)) {
            return true
        } else {
            event.stopPropagation();
            event.preventDefault();
        }
    }

</script>


