{if $mostrar_sobrecargo}
    <div class="cart-summary-line cart-summary-subtotals" id="cart-subtotal-{$subtotal.type}">
        <br>
        <span class="label">{l s='Aporvino shipping surcharge' d='Shop.Theme.Actions'}</span>
        <span class="value">&euro;{$sobrecargo|string_format:"%.2f"}</span>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#valor_total').html('&euro;' + '{$ordertotal|string_format:"%.2f"}');
    });
    </script>
{/if}
