{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
	<td width="40%"><span class="bold">{l s='Sender Address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
                <table>
                    <tbody>
                    <tr>
                        {*<th>{l s='Name' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>Juan Luis Rivas Anoro</td>
                    </tr>
                    <tr>
                        {*<th>{l s='Address' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>C/ de la Cabeza Molar 10</td>
                    </tr>
                    <tr>
                        {*<th>{l s='CP Municipality' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>19160 Chiloeches</td>
                    </tr>
                    <tr>
                        {*<th>{l s='State Country' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>Guadalajara-ESPAÑA</td>
                    </tr>
                    <tr>
                        {*<th>{l s='CIF/NIF' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>01935014R</td>
                    </tr>
                    <tr>
                        {*<th>{l s='Phone' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td>+34-949 874 055/ +34-949 874 074</td>
                    </tr>
                    <tr>
                        {*<th>{l s='Email1' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td><a style="color: #00A4E7;text-decoration: underline">tienda@11tesorosibericos.com</a></td>
                    </tr>
                    <tr>
                        {*<th>{l s='Email2' d='Shop.Pdf' pdf='true'}:</th>*}
                        <td><a style="color: #00A4E7;text-decoration: underline">www.11tesorosibericos.com</a></td>
                    </tr>
                    </tbody>
                </table>
            </td>
		<!--<td width="33%"><span class="bold"> </span><br/><br/>
			{$order_invoice->shop_address}
		</td>-->
		{if !empty($invoice_address)}
			<td width="30%">{if $delivery_address}<span class="bold">{l s='Delivery Address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
					{$delivery_address}
				{/if}
			</td>
			<td width="30%"><span class="bold">{l s='Billing Address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
				{$invoice_address}
			</td>
		{else}
			<td width="60%">{if $delivery_address}<span class="bold">{l s='Billing & Delivery Address' d='Shop.Pdf' pdf='true'}</span><br/><br/>
					{$delivery_address}
				{/if}
			</td>
		{/if}
	</tr>
</table>
