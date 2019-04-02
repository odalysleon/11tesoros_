<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>Message from {shop_name}</title>


</head>
<body>
<div style="padding: 20px">
    <div style="margin: 40px 0px">
        <p>Datos del cliente:</p>
        <div style="margin: 10px 0px">
            <div>
                <strong>Nombre:</strong>
                <label style="display: inline">{$client_name}</label>
            </div>
            <div>
                <strong>Correo electrónico:</strong>
                <label style="display: inline">{$email}</label>
            </div>
            <div>
                <strong>Dirección de entrega:</strong>
                <label style="display: inline">{foreach $customer.addresses as $value}{if $value.id == $address}{$value.formatted nofilter}{/if}{/foreach}</label>
            </div>
        </div>
    </div>
    <div style="margin: 40px 0px">
        <p>Detalles del pedido:</p>
        <div style="margin: 10px 0px">
            <div>
                <strong>Cantidad de productos:</strong>
                <label style="display: inline">{$quantity}</label>
            </div>
            <div>
                <strong>Total a pagar:</strong>
                <label style="display: inline">{$amount}</label>
            </div>
        </div>
    </div>
    <div style="margin: 40px 0px">
        <p>Productos:</p>
        <div style="margin: 10px 0px">
            {foreach from=$products item="product"}
                <div>
                    <strong>Nombre del producto:</strong>
                    <label style="display: inline">{$product.product_name}</label>
                </div>
                <div>
                    <strong>Precio del producto:</strong>
                    <label style="display: inline">{$product.price}</label>
                </div>
                <div>
                    <strong>Cantidad:</strong>
                    <label style="display: inline">{$product.quantity}</label>
                </div>
                <div style="border-bottom: 2px solid #1c8ca3; margin: 10px 0px"></div>
            {/foreach}
        </div>
    </div>

</div>
</body>
</html>

