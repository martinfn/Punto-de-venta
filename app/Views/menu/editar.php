<?php
$id_compra = uniqid();
?>

<html lang="en">
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">


            <form method="POST" id="form_compra" name="form_compra" action="" autocomplete="off">

               

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <label>Código</label>
                            <input class="form-control" id="id_m" name="id_m" value="<?php echo $producto['codigo']; ?>" type="text" disable>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label>Nombre</label>
                            <input class="form-control" id="nombrem" name="nombrem" value="<?php echo $producto['nombre']; ?>" type="text" disabled>
                        </div>

                        
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                           <input type="hidden" id="id_producto" name="id_producto" />
                            <input type="hidden" id="id_compra" name="id_compra" value="<?php //echo $id_compra; ?>" />
                            <label>Código</label>

                            <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Escribe el código y presiona enter" onkeyup="buscarProducto(event,this,this.value)" autofocus>

                            <label for="codigo" id="resultado_error" style="color: red"></label>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label>Nombre del producto</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" disabled>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label>Porción</label>
                            <input class="form-control" id="cantidad" name="cantidad" type="text" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        
                        <div class="col-12 col-sm-4">
                            
                            
                            <a href="#" data-href=" <?php echo base_url() . '/menu/editar/' . $producto['id']. '/' . $producto['codigo']. 
                                '/' . $producto['id'] ?>" data-toggle="modal" data-target="#modal-confirma" data-placement="top" title="Agregar ingrediente " class="btn btn-primary"
                                onclick="agregarProducto(id_producto.value,cantidad.value,nombrem.value,id_m.value)"
                                style=" width: 180px;">
                                Agregar ingrediente</a>
                             
                        </div>
                        
                    </div>  
                </div>


               
                    <br>
                <div class="row">
                    <table id="tablaProductos" class="table table-hover table-striped table-sm 
                    table-responsive tablaProductos" width="100%">
                        <thead class="thead-dark">
                            
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Producto</th>
                            <th>Proporcion</th>
                            <th></th>
                            
                            <th width="1%"></th>
                        </thead>
                        <tbody>
                        <?php foreach ($datos_menu as $dato) { ?>
                            <tr>
                                <td><?php echo $dato['codigo']; ?></td>
                                <td><?php echo $dato['nombre']; ?></td>
                                <td>
                                <?php foreach ($produc as $datos) { ?>
                                    <?php if($datos['id']==$dato['id_producto']){
                                        echo $datos['nombre'];
                                        break;
                                    } ?>
                                    <?php } ?>
                                </td>
                                
                                <td><?php echo $dato['proporcion']; ?></td>
                                                           
                                <td> <a href="#" data-href=" <?php echo base_url() . '/menu/eliminar/' . $dato['id']. '/' . $dato['codigo']. 
                                '/' . $producto['id'] ?>" data-toggle="modal" data-target="#modal-confirma" data-placement="top" title="Eliminar ingrediente " class="btn btn-danger"><i class="fas fa-trash"></i></a></td>
                            </tr>
                        <?php } ?>
                        
                       
                        
                        
                        
                        </tbody>

                    </table>
                </div>
                
            </form>

        </div>
        <!-- Modal -->
<div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Desea continuar ? </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
        <a  class="btn btn-danger btn-ok">Si</a>
      </div>
    </div>
  </div>
</div>
    </main>
    <script>
        $(document).ready(function() {
            $("#completa_compra").click(function() {
                let nFila = $("#tablaProductos tr").length;
                if (nFila < 2) {

                } else {
                    $("#form_compra").submit();
                }
            });
        });

        function buscarProducto(e, tagCodigo, codigo) {
            var enterKey = 13;
            if (codigo != '') {
                if (e.which == enterKey) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>/productos/buscarPorCodigo/' + codigo,
                        dataType: 'json',
                        success: function(resultado) {
                            if (resultado == 0) {
                                $(tagCodigo).val('');
                            } else {

                                $("#resultado_error").html(resultado.error);

                                if (resultado.existe) {

                                    $("#id_producto").val(resultado.datos.id);
                                    $("#nombre").val(resultado.datos.nombre);
                                   
                                   
                                } else {
                                    $("#id_producto").val('');
                                    $("#nombre").val('');
                                    
                                   
                                }

                            }
                        }
                    });
                }
            }
            
        }

        function agregarProducto(id_producto,proporcion,nombre,codigo) {

            if (id_producto != null && id_producto != 0 && proporcion > 0) {
                $.ajax({
                    url: '<?php echo base_url(); ?>/Menu/actualizarm/' + id_producto +
                           "/"+ proporcion + "/"+ nombre + "/" + codigo ,

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

                            }
                        }
                    }
                });
            }

            }

                

        function eliminaProducto(id_producto, id_compra) {

            $.ajax({
                url: '<?php echo base_url(); ?>/TemporalCompra/eliminar/' + id_producto +
                    "/" + id_compra,

                success: function(resultado) {
                    if (resultado == 0) {
                        $(tagCodigo).val('');
                    } else {

                        var resultado = JSON.parse(resultado);
                        $("#tablaProductos tbody").empty();
                        $("#tablaProductos tbody").append(resultado.datos);
                        $("#total").val(resultado.total);

                    }
                }
            });
        }
        
            $(document).ready(function() {
                $("form").keypress(function(e) {
                    if (e.which == 13) {
                        return false;
                    }
                });
            });

    </script>