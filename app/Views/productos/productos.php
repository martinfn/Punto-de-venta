<?php $session = session(); ?>
<!DOCTYPE html>
<html lang="en">
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <div>
            <?php if($session->nombre=='administrador'){?>
                <p>
                    <a href="<?php echo base_url(); ?>/productos/nuevo" class="btn btn-info">Agregar
                    </a>
                    <a href="<?php echo base_url(); ?>/productos/eliminados" class="btn btn-warning">Eliminados
                    </a>
                </p>
            <?php  }?>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio de compra</th>
                            <th>Existencias</th>
                            
                            <th></th>
                            <th> </th>

                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($datos as $dato) { ?>
                            <tr>
                               
                                <td><?php echo $dato['codigo']; ?></td>
                                <td><?php echo $dato['nombre']; ?></td>
                                <td><?php echo $dato['precio_compra']; ?></td>
                                <td><?php echo $dato['unidad_p'] / 1000;?></td>
                                <?php if($session->nombre=='administrador'){?>
                                <td> <a href="<?php echo base_url() . '/productos/editar/' . $dato['id']; ?>" class="btn btn-warning"><i class="fas fa-pencil-alt"></i>
                                    </a></td>
                                  
                                <td> <a href="#" data-href="<?php echo base_url() . '/productos/eliminar/' . $dato['id']; ?>" data-toggle="modal" data-target="#modal-confirma" data-placement="top" title="Eliminar registro" class="btn btn-danger"><i class="fas fa-trash"></i></a></td>
                                <?php  }?>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
<!-- Modal -->
<div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Eliminar registro</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Desea eliminar este registro? </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
        <a  class="btn btn-danger btn-ok">Si</a>
      </div>
    </div>
  </div>
</div>

</html>