

{if $update_success == 1}
<div class=" bootstrap">
    <div class="alert alert-success">La configuración ha sido actualizada correctamente.</div>
</div>
{/if}
    <h2>Configuración</h2>
    <p>Desde esta sección Ud. podrá configurar los valores a aplicar como sobrecargo de envíos a pedidos 
        para el transportista de APORVINO en áreas no metropolitana y remotas según el Código Postal de la dirección en envío seleccionada por los usuarios.</p>
    <form action="" method="post">
    <div class="panel">
        <div class="panel-body">
            <div class="row">                
                <div class="col-xs-12 col-sm-6">
                    <h3>Área no metropolitana</h3>
                    <div class="form-group">
                        <label>Valor mínimo del pedido:</label>
                        <div class="input-group">
                            <input id="valor_minimo_no_metropolitana" name="valor_minimo_no_metropolitana" value="{$valor_minimo_no_metropolitana}" class="form-control"  >
                            <span class="input-group-addon">
                                <i class="fa fa-user">&euro;</i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Valor de sobrecargo:</label>
                        <div class="input-group">
                            <input  id="sobrecargo_no_metropolitana" name="sobrecargo_no_metropolitana" value="{$sobrecargo_no_metropolitana}" class="form-control"  >
                            <span class="input-group-addon">
                            <i class="fa fa-user">Precio x Kg</i>
                            </span>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-xs-12 col-sm-6">
                    <h3>Área remota</h3>
                    <div class="form-group">
                        <label>Valor mínimo del pedido:</label>
                        <div class="input-group">
                            <input id="valor_minimo_remota" name="valor_minimo_remota" value="{$valor_minimo_remota}" class="form-control"  >
                            <span class="input-group-addon">
                                <i class="fa fa-user">&euro;</i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Valor de sobrecargo:</label>
                        <div class="input-group">
                            <input id="sobrecargo_remota" name="sobrecargo_remota" value="{$sobrecargo_remota}" class="form-control"  >
                            <span class="input-group-addon">
                            <i class="fa fa-user">Precio x Kg</i>
                            </span>
                        </div>
                    </div>
                </div>
                    
            </div>
        </div>
        <div class="panel-footer">
            <button name="pickeoupsaporvino_form" type="submit" class="btn btn-primary-outline " ><i class="material-icons" style="float: left; margin: 0 5px 0 0;">save</i> Salvar</button>
        </div>
    </div>    
    
    </form>




