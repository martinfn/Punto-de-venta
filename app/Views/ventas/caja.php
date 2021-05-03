<div id="layoutSidenav_content">
    <input type="hidden" id="id_producto" name="id_producto" />
    <main>
        <div class="container-fluid">
            <?php $idVentaTmp = uniqid(); 
                    ?>
            <br>
            <form id="form_venta" name="form_venta" class="form-horizontal" method="POST" action="<?php echo base_url(); ?>/ventas/guarda" autocomplete="off">
                <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $idVentaTmp; ?>" />
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <div clas="ui-widget">
                                <label>Cliente:</label>
                                <input type="hidden" id="id_cliente" name="id_cliente" value="1/">
                                <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Escribe el nombre del cliente" value="Público en general" 
                                onkeyup="agregarProducto()" autocomplete="off" required />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label>Forma de pago:</label>
                            <select name="forma_pago" id="forma_pago" class="form-control" required>
                                <option value="001">Efectivo</option>
                                <option value="002">Tarjeta Débito</option>
                                <option value="003">Tarjeta Crédito</option>
                                <option value="004">Transferencia</option>
                                <option value="005">Efectivo/tarjeta</option>
                            </select>


                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-4">

                            <label>Código de barras</label>

                            <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Escribe el código y presiona enter" 
                            onkeyup="agregarProducto(event,this.value,1,'<?php echo $idVentaTmp; ?>','1212');" autofocus>

                        </div>
                        <div class="col-sm-2">
                            <label for="codigo" id="resultado_error" style="color:red"></label>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label style="font-weight: bold; font-size: 20px; text-align: center;">
                                Total $</label>
                            <input type="text" id="total" name="total" size="15" readonly="true" value="0.00" style="font-weight: bold; font-size: 20px; text-align: center;" />
                            <br>  
                            <label style="font-weight: bold; font-size: 20px; text-align: center;">
                                Pago $</label>
                            <input type="text" id="pago_recibido" placeholder ="presione enter" name="pago_recibido" size="15"  style="font-weight: bold; font-size: 20px; text-align: center;" required/>
                            <br>  
                            <span id="segundop" name="segundop"> 
                            <label style="font-weight: bold; font-size: 20px; text-align: center;">
                               Tarjeta $</label>
                            <input type="text" id="pago_recibido2" placeholder ="presione enter" value="0.00" name="pago_recibido2" size="12"  style="font-weight: bold; font-size: 20px; text-align: center;" required/>
                            </span disabled>
                            <br>
                            <label style="font-weight: bold; font-size: 20px; text-align: center;">
                            Cambio $</label>
                            <input type="text" id="cambio" name="cambio" size="7"  style="font-weight: bold; font-size: 20px; text-align: center;" required />
                        </div>
                        
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" id="completa_venta" class="btn btn-success">
                        Completar venta</button>
                </div>

                <div class="row">
                    <table id="tablaProductos" class="table table-hover table-striped table-sm 
                    table-responsive tablaProductos" width="100%">
                        <thead class="thead-dark">
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th width="1%"></th>
                        </thead>
                        <tbody></tbody>

                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>

                                <th></th>


                            </tr>
                        </thead>

                        <tbody>
                           
                            <?php foreach ($datos_menu as $dato_menu) { ?>
                                <tr>
                                    
                                    <td><?php echo $dato_menu['codigo']; ?></td>
                                    <td><?php echo $dato_menu['nombre']; ?></td>
                                    <td><?php echo $dato_menu['precio_venta']; ?></td>
                                    <td>
                                        <button id="agregar_producto" name="agregar_producto" type="button" 
                                        class="btn btn-primary" onclick="agregarProducto2(<?php echo $dato_menu['id']; ?>,1
                                        ,'<?php echo $idVentaTmp; ?>','<?php echo $dato_menu['codigo']; ?>')">Agregar producto</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </main>


    <script>
    $(function() {
        $('#segundop').hide()
    $("#forma_pago").change( function() {
        if ($(this).val() === "005") {
            $('#segundop').show();
        } else {
            $("#segundop").hide();
        }
    });
});
        
        let total = document.getElementById("total");
        let efectivo = document.getElementById("pago_recibido");
        let cambio = document.getElementById("cambio");
        let tarjeta = document.getElementById("pago_recibido2");
        
    
            
        efectivo.addEventListener("change", () => {
            cambio.value = parseFloat(efectivo.value) - parseFloat(total.value)
            
        });
            
        
        tarjeta.addEventListener("change", () => {
            cambio.value = (parseFloat(efectivo.value)+ parseFloat(tarjeta.value)) - parseFloat(total.value)
            
       });
        $(function() {
            $("#codigo").autocomplete({
                source: "<?php echo base_url(); ?>/productos/autocompleteData",
                minLength: 3,
                select: function(event, ui) {
                    event.preventDefault();
                    $("#codigo").val(ui.item.value);
                    setTimeout(
                        function() {
                            e = jQuery.Event("keypress");
                            e.wich = 13;
                            agregarProducto2(ui.item.id, 1, '<?php echo $idVentaTmp; ?>',codigo.value);
                        }
                    )
                }
            });
        });

        var boton = document.getElementById('agregar_producto');
        var resultado = document.getElementById('codigo');
        var pago_r = document.getElementById('pago_recibido');

        boton.onclick = function(e) {
            resultado.value += this.value;
            pago_r.value += this.value;
        }

        function agregarProducto(e, id_producto, cantidad, id_venta, $id_menu) {
            let enterKey = 13;
            if (codigo != '') {
                if (e.wich == enterKey) {
                    if (id_producto != null && id_producto != 0 && cantidad > 0) {
                        $.ajax({
                            url: '<?php echo base_url(); ?>/TemporalCompra/insertar/' + id_producto +
                                "/" + cantidad + "/" + id_venta + "/" + parseFloat(efectivo.value + tarjeta.value)
                                + "/" + parseFloat(cambio.value)+"/"+ $id_menu,

                            success: function(resultado) {
                                if (resultado == 0) {

                                } else {
                                    var resultado = JSON.parse(resultado);
                                    
                                    if (resultado.error == '') {
                                        $("#tablaProductos tbody").empty();
                                        $("#tablaProductos tbody").append(resultado.datos);
                                        $("#total").val(resultado.total);
                                        $("#id_producto").val('');
                                        $("#codigo").val('');
                                        $("#nombre").val('');
                                        $("#cantidad").val('');
                                        $("#precio_compra").val('');
                                        $("#subtotal").val('');
                                        $("#pago_recibido").val('');
                                        $("#pago_recibido2").val('0.00');
                                        $("#cambio").val('');

                                    }
                                }
                            }
                        });
                    }
                }
            }
        }



        function eliminaProducto(id_producto, id_venta) {

            $.ajax({
                url: '<?php echo base_url(); ?>/TemporalCompra/eliminar/' + id_producto +
                    "/" + id_venta,

                success: function(resultado) {
                    if (resultado == 0) {
                        $(tagCodigo).val('');
                        
                    } else {

                        var resultado = JSON.parse(resultado);
                        $("#tablaProductos tbody").empty();
                        $("#tablaProductos tbody").append(resultado.datos);
                        $("#total").val(resultado.total);
                        $("#pago_recibido").val('');
                        $("#pago_recibido2").val('0.00');
                        $("#cambio").val('');

                    }
                }
            });
        }

        $(function() {
            $("#completa_venta").click(function() {
               // if(!efectivo.value ===''){
                let nFilas = $("#tablaProductos tr").length;
                if (nFilas < 2 ) {
                    alert("Debe agregar un producto");
                } else {
                    $("#form_venta").submit();
                }
               // }else{
                 //   alert("Ingrese Pago");
               // }
            });
        });

        function agregarProducto2(id_producto, cantidad, id_compra, $id_menu ) {

            if (id_producto != null && id_producto != 0 && cantidad > 0) {
                $.ajax({
                    url: '<?php echo base_url(); ?>/TemporalCompra/insertar/' + id_producto +
                        "/" + cantidad + "/" + id_compra + "/" + parseFloat(efectivo.value + tarjeta.value)
                        + "/" + parseFloat(cambio.value)+"/"+ $id_menu,

                    success: function(resultado) {
                        if (resultado == 0) {

                        } else {
                            var resultado = JSON.parse(resultado);
                            if (resultado.error == '') {
                                $("#tablaProductos tbody").empty();
                                $("#tablaProductos tbody").append(resultado.datos);
                                $("#total").val(resultado.total);
                                $("#id_producto").val('');
                                $("#codigo").val('');
                                $("#nombre").val('');
                                $("#cantidad").val('');
                                $("#precio_compra").val('');
                                $("#subtotal").val('');
                                $("#pago_recibido").val('');
                                $("#pago_recibido2").val('0.00');
                                $("#cambio").val('');
                                
                            }
                        }
                    }
                });
            }
        }
    </script>