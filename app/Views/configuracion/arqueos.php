<!DOCTYPE html>
<html lang="en">
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>
            <div>
                <p>
                    <a href="<?php echo base_url(); ?>/configuracion/nuevo_arqueo" class="btn btn-info">Agregar
                    </a>
                    <a href="<?php echo base_url(); ?>/menu/eliminados" class="btn btn-warning">Eliminados
                    </a>
                </p>

            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Fecha apertura</th>
                            <th>Fecha cierre</th>
                            <th>Monto inicial</th>                           
                            <th>Monto final</th> 
                            <th>Total Ventas</th>
                            <th>Estatus</th>

                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($datos as $dato) { ?>
                            <tr>
                                <td><?php echo $dato['id']; ?></td>
                                <td><?php echo $dato['fecha_inicio']; ?></td>
                                <td><?php echo $dato['fecha_fin']; ?></td>
                                <td><?php echo $dato['monto_inicial']; ?></td>
                                <td><?php echo $dato['monto_final']; ?></td>
                                <td><?php echo $dato['total_ventas']; ?></td>
                                <td><?php echo $dato['estatus']; ?></td>
                                <?php if($dato['estatus']==1){  ?>
                                  <td>Abierta</td>
                                  <td><a href="#" data-href="<?php echo base_url() . '/configuracion/cierre' 
                                  ; ?>" data-toggle="modal" data-target="#modal-confirma" 
                                  data-placement="top" title="Eliminar registro" class="btn btn-danger">
                                  <i class="fas fa-trash"></i></a>
                                  </td>
                                <?php }else{ ?>
                                  <td>Cerrada</td>
                                  <td><a href="#" data-href="<?php echo base_url() . '/configuracion/cierre/' 
                                  . $dato['id']; ?>" data-toggle="modal" data-target="#modal-confirma" 
                                  data-placement="top" title="Cerrar caja" class="btn btn-danger">
                                  <i class="btn btn-success"></i></a>
                                  </td>

                                  <?php }?>
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
        <h5 class="modal-title" id="exampleModalLongTitle">Cerrar Caja</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Desea Cerrar la caja? </p>
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