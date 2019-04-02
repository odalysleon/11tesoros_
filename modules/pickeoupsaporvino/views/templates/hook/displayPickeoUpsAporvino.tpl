
<section class="card"> 
    <div class="card-block">
        <div class="row">
            <div class="col-xs-12">
                {if $tipo_area == "r"}<h3>{l s="UPS surcharge for sending to remote address"} </h3>{/if}
                {if $tipo_area == "nm"}<h3>{l s="UPS surcharge for shipping to non-metropolitan address"} </h3>{/if}
                <ul>
                    <li>{l s="Order Weight"}: <span>{$peso|string_format:"%.2f"|replace:'.':','}</span> Kg</li>
                    <li>{l s="Tax / Weight"}: <span>{$importe_peso|string_format:"%.2f"|replace:'.':','}</span> €/Kg</li>
                    <li>{l s="Total Surcharge"}: <span>{$sobrecargo|string_format:"%.2f"|replace:'.':','}</span> €</li>
                    <li style="display: none;" id="importe_real">
                        {$importe_real|string_format:"%.2f"|replace:'.':','}
                    </li>
                </ul>
            </div>
        </div>        
    </div>    
</section>