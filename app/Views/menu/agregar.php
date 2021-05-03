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
            <form method="POST" enctype="multipart/form-data" action="<?php echo base_url(); ?>/menu/insertar" autocomplete="off">
            <?php echo csrf_field(); ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Código</label>
                            <input class="form-control" id="codigo" name="codigo" type="text"
                            value="<?php echo $datos_menu['codigo']; ?>" autofocus required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" 
                            value="<?php echo $datos_menu['nombre']; ?>" required>
                        </div>
                    </div>
                </div>

               

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Precio venta</label>
                            <input class="form-control" id="precio_venta" name="precio_venta" type="text" 
                            value="<?php echo $datos_menu['precio_venta']; ?>" required>
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