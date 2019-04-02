{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
    <div id="hook-display-before-carrier">
        {$hookDisplayBeforeCarrier nofilter}
    </div>
    
    
    {if $cart.products|count}       
        {assign var=first_types_product value=$cart.products[0].id_category_default}
        {assign var=count_diferent_types_products value=1}
        {assign var=cats value=''}
        {foreach from=$cart.products item=product}
            
            {if $product.id_category_default!= $first_types_product} 
                {assign var=count_diferent_types_products value=$count_diferent_types_products+1}
            {/if}            
        {/foreach}    
    {/if} 
    <div class="delivery-options-list">
        
        
        {if $delivery_options|count}  
            <form 
                    class="clearfix "
                    id="js-delivery"
                    data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}"
                    method="post"
            >
                <div class="form-fields" {if $count_diferent_types_products>1} style="display: none"{/if}>
                    {block name='delivery_options'}
                        <div class="delivery-options">
                            {foreach from=$delivery_options item=carrier key=carrier_id}
                                <div class="row delivery-option">
                                    <div class="row">
                                    <div class="col-sm-1">
                      <span class="custom-radio float-xs-left">
                        <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}"
                               value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if} class="shipping-radio">
                        <span></span>
                      </span>
                                    </div>
                                    <label for="delivery_option_{$carrier.id}" class="col-sm-11 delivery-option-2">
                                        <div class="row">
                                            <div class="col-sm-5 col-xs-12">
                                                <div class="row">
                                                    {if $carrier.logo}
                                                        <div class="col-xs-3">
                                                            <img src="{$carrier.logo}" alt="{$carrier.name}"/>
                                                        </div>
                                                    {/if}
                                                    <div class="{if $carrier.logo}col-xs-9{else}col-xs-12{/if}">
                                                        <span class="h6 carrier-name">{$carrier.name}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 col-xs-12">
                                                {if !$aceites }
                                                    <span class="carrier-delay">{$carrier.delay}</span>
                                                {/if}
                                            </div>
                                            <div class="col-sm-3 col-xs-12">
                                                {if !$aceites}
                                                    <span class="carrier-price">{$carrier.price nofilter}</span>
                                                {/if}
                                            </div>
                                        </div>
                                    </label>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            {hook h="displayEnvialiaOptions" carrier=$carrier}
                                        </div>
                                    </div>
                                </div>
                                <div class="row carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>
                                    {$carrier.extraContent nofilter}
                                </div>
                                <div class="clearfix"></div>

                            {/foreach}
                        </div>
                    {/block}
                    <div class="order-options">
                        {if !$aceites }
                            <div id="delivery">
                                <label for="delivery_message">{l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Checkout'}</label>
                                <textarea rows="2" cols="120" id="delivery_message"
                                          name="delivery_message">{$delivery_message}</textarea>
                            </div>
                        {/if}
                        {if $recyclablePackAllowed}
                            <span class="custom-checkbox">
                <input type="checkbox" id="input_recyclable" name="recyclable" value="1" {if $recyclable} checked {/if}>
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                <label for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</label>
              </span>
                        {/if}

                        {if $gift.allowed}
                            <span class="custom-checkbox">
                <input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox" value="1"
                       {if $gift.isGift}checked="checked"{/if}>
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                <label for="input_gift">{$gift.label}</label>
              </span>
                            <div id="gift" class="collapse{if $gift.isGift} in{/if}">
                                <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
                                <textarea rows="2" cols="120" id="gift_message"
                                          name="gift_message">{$gift.message}</textarea>
                            </div>
                        {/if}

                    </div>
                </div>
                                 
                <div class="delivery-option no-delivery-option ">  
                    {if $count_diferent_types_products > 1} 
                    {foreach from=$cart.subtotals item="subtotal"}
                        {if $subtotal && $subtotal.type !== 'tax' && $subtotal.type=='shipping' }
                            <p class="tax-shipping">{$subtotal.value} {l s='tax_incl' d='Shop.Theme.Checkout'}</p>
                        {/if}
                    {/foreach}
                    {/if} 
                    {l s='Message for more one delivery options' sprintf=['[break]' => '<br/>'] d='Shop.Theme.Checkout'}
                </div>
                       
                <button type="submit" class="continue btn btn-primary float-xs-right" name="confirmDeliveryOption"
                        value="1">
                    {l s='Continue' d='Shop.Theme.Actions'}
                </button>
            </form>
            
        {else}
            {if $weightExceeded}
                <p class="alert alert-danger">{l s='El peso del pedido no puede exceder los 90kg.' d='Shop.Theme.Checkout'}</p>
                <p class="alert alert-info">{l s='Le recomendamos que realice varios pedidos con un peso menor a 90 kg para obtener los productos que desea.' d='Shop.Theme.Checkout'}</p>
            {else}
                <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
            {/if}
        {/if}
    </div>
    <div id="hook-display-after-carrier">
        {$hookDisplayAfterCarrier nofilter}
    </div>
    <div id="extra_carrier"></div>
    <script>
        var envialia_charge = '{$envialia_charge}';
    </script>
{/block}
