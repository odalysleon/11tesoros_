{*
 * 2011-2017 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2016 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 *}
{extends file='page.tpl'}
{block name="page_content"}
<h1 id="cart_title">{l s='Credit card payment' mod='ceca'}</h1>

<div class="pasatDiv">
	<div class="passatBody">
		<div class="separador"></div>
		<iframe src="" name="tpv" width="100%" height="650" scrolling="auto" frameborder="0" transparency>
			<p>{l s='Your browser does not support iframes.' mod='ceca'}</p>
		</iframe>
		<form action="{$url_tpvv|escape:'htmlall':'UTF-8'}" name="compra" method="post" enctype="application/xwww-form-urlencoded" {if $showInIframe}target="tpv"{/if}>
		<input name="MerchantID" type="hidden" value="{$MerchantID|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="AcquirerBIN" type="hidden" value="{$AcquirerBIN|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="TerminalID" type="hidden" value="{$TerminalID|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="URL_OK" type="hidden" value="{$url_OK|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="URL_NOK" type="hidden" value="{$url_NOK|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="Firma" type="hidden" value="{$Firma|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="Cifrado" type="hidden" value="SHA2">
		<input name="Num_operacion" type="hidden" value="{$Num_operacion|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="Importe" type="hidden" value="{$Importe|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="TipoMoneda" type="hidden" value="{$TipoMoneda|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="Exponente" type="hidden" value="{$Exponente|escape:'htmlall':'UTF-8'}" autocomplete="off">
		<input name="Pago_soportado" type="hidden" value="SSL" autocomplete="off">
		<input name="Idioma" type="hidden" value="{$locale|escape:'htmlall':'UTF-8'}" autocomplete="off">
		</form>
		<script>document.compra.submit();</script>
	</div>
</div>
<p><a href="javascript:history.go(-1);">{l s='Go back' mod='ceca'}</a>
<div class="clear"></div>
{/block}