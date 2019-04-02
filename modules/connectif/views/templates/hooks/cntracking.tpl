{**
* NOTICE OF LICENSE
*
* @author    Connectif
* @copyright Copyright (c) 2017 Connectif
* @license   https://opensource.org/licenses/MIT The MIT License (MIT)
*}


{include file="{$module_templates}hooks/cn_dynamic_content.tpl"}
<!-- Connectif tracking code -->
<script type="text/javascript" async>
    var _cnid = "{$client_id|escape:'htmlall':'UTF-8'}";
    (function(w, r, a, cn, s ) {
        {literal}
        w['ConnectifObject'] = r;
        w[r] = w[r] || function () {( w[r].q = w[r].q || [] ).push(arguments)};
        cn = document.createElement('script'); cn.type = 'text/javascript'; cn.async = true; cn.src = a; cn.id = '__cn_client_script_' + _cnid;
        s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(cn, s);
        {/literal}
    })(window, 'cn', '{$cdn_client_script_path|escape:'htmlall':'UTF-8'}' + '{$client_id|escape:'htmlall':'UTF-8'}');
</script>
<!-- end Connectif tracking code -->