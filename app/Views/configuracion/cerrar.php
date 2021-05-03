<?php $user_session = session(); ?>
<!DOCTYPE html>
<html lang="en">
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <?php if(isset($validation)){?>
                    <div class="alert alert-danger">
                         <?php echo $validation->listErrors(); ?>
                    </div>
                <?php } ?>
            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>/configuracion/cierre" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" id="id_arqueo" name="id_arqueo" value="<?php echo $arqueo['id']; ?>">
            
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Monto inicial</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $user_session->nombre; ?>" disabled required >
                        </div>
                        <div class="col-12 col-sm-6">
                            <label></label>
                            <input class="form-control" id="" name="" value="" type="text" disabled required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Monto inicial</label>
                            <input class="form-control" id="monto_inicial" name="monto_inicial" type="text" 
                            value="<?php echo $arqueo['monto_inicial']  ?>" autofocus required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Monto final</label>
                            <input class="form-control" id="monto_final" name="monto_final" type="text"
                            value="<?php  ?>" autofocus required>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Monto de ventas</label>
                            <input class="form-control" id="monto_ventas" name="monto_ventas" type="text" 
                            value="<?php echo $monto['total'] ?>" autofocus required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Total de ventas</label>
                            <input class="form-control" id="total_ventas" name="total_ventas" type="text"
                            value="<?php  ?>" autofocus required>
                        </div>
                    </div>
                </div>

               

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Fecha</label>
                            <input class="form-control" id="fecha_fin" name="fecha_fin" type="text" 
                            value="<?php echo date('Y-m-d'); ?>" maxlength="2" autofocus required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Hora</label>
                            <input class="form-control" id="hora" name="hora" type="text"
                            value="<?php echo date('H:i:s'); ?>" autofocus required>
                        </div>
                    </div>
                </div>

                

                

                


                <!--<div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Imagen</label> <br>
                            <input type="file" id="img_producto" name="img_producto" accept="image/*" required/>
                           <p class="text-danger"> Cargar imagen en formato png de 150x150 pixeles</p>
                        </div>  
                    </div>
                </div>-->
                <a href="<?php echo base_url(); ?>/menu" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>

            </form>

        </div>
    </main>

</html>
<script>

function comprobar(obj)
{   
    if (obj.checked){
      
document.getElementById('boton').style.display = "";
   } else{
      
document.getElementById('boton').style.display = "none";
   }     
}
</script>