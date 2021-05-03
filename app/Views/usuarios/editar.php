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

            <form method="POST" action="<?php echo base_url(); ?>/usuarios/actualizar_usuario" autocomplete="off">
            <?php csrf_field();?>
            <input type="hidden" id="id" name ="id" value="<?php echo $usuario['id']; ?>"/>
                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Usuario</label>
                            <input class="form-control" id="usuario" name="usuario" type="text" 
                            value = "<?php echo $usuario['usuario']; ?>"  disable/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Nombre </label>
                            <input class="form-control" id="nombre" name="nombre" type="text" 
                            value = "<?php echo $usuario ['nombre']; ?>" disable/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Contraseña</label>
                            <input class="form-control" id="password" name="password" type="password" 
                            required/>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label>Confirma contraseña</label>
                            <input class="form-control" id="repassword" name="repassword" type="password" 
                            required />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Rol</label>
                                <select class = "form-control" id= "id_rol" name = "id_rol" requiredd>
                                    <option value="">Seleccionar rol</option>
                                        <?php foreach($roles as $rol) {?>
                                            <option value ="<?php echo $rol['id']; ?>"><?php echo $rol
                                            ['nombre']; ?></option>


                                        <?php }?>
                                </select>
                        </div>
                    </div>
                </div>
                <a href="<?php echo base_url(); ?>/usuarios" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>
                <?php if(isset($mensaje)){?>
                    <div class="alert alert-success">
                         <?php echo $mensaje; ?>
                    </div>
                <?php } ?>

            </form>

        </div>
    </main>

</html>