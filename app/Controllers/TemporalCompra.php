<?php

namespace App\Controllers;

use App\Models\TemporalCompraModel;
use App\Models\ProductosModel;
use App\Models\MenuModel;
use App\Controllers\BaseController;

class TemporalCompra extends BaseController
{
    protected $temporal_compra,$productos,$menu;
    
    public function __construct()
    {
        $this->temporal_compra = new TemporalCompraModel();
        $this->productos = new ProductosModel();
        $this->menu = new MenuModel();
      

       
    }
   
    //Agregar unidad
    public function insertar($id_producto,$cantidad,$id_compra,$pago_recibido,$cambio,$id_menu)
    {
        $error ='';
        $producto= $this->menu->where('codigo',$id_menu)->first();
        if($producto){
           $datosExiste = $this->temporal_compra->porIdProductoCompra($id_producto,$id_compra);
           if($datosExiste){
               
               $cantidad =$datosExiste->cantidad + $cantidad;
               $subtotal =$cantidad * $datosExiste->precio;
               $this->temporal_compra->actualizarProductoCompra($id_producto,$id_compra,$cantidad,$subtotal,$pago_recibido,$cambio,$id_menu);
           }else{
               $subtotal = $cantidad * $producto['precio_venta'];
               $this->temporal_compra->save([
                'folio'=> $id_compra,
                'id_producto'=>$id_producto,
                'codigo'=> $producto['codigo'],
                'nombre'=> $producto['nombre'],
                'precio'=> $producto['precio_venta'],
                'cantidad'=> $cantidad,
                'subtotal'=> $subtotal,
                'pago_recibido'=>$pago_recibido,
                'cambio'=>$cambio,
                'id_menu'=>$id_menu
                
               ]);
           }
        }else{
            $error = 'No existe el producto';
        }
        $res['datos'] = $this->cargaProductos($id_compra);
        $res['total'] = number_format($this->totalProductos($id_compra),2,'.',',');
        $res['error'] = $error;
        echo json_encode($res);
      
        
    }
    public function insertar2($id_producto,$cantidad,$id_compra,$proporcion,$precio)
    {
        $this->productos->update(
            $id_producto,
            [
                
                'precio_compra' =>  $precio
                
            ]
        );
        $error ='';
        $producto= $this->productos->where('id',$id_producto)->first();
        if($producto){
           $datosExiste = $this->temporal_compra->porIdProductoCompra($id_producto,$id_compra);
           if($datosExiste){
               
               $cantidad =$datosExiste->cantidad + $cantidad;
               $subtotal =$cantidad * $datosExiste->precio;
               $this->temporal_compra->actualizarProductoCompra2($id_producto,$id_compra,$cantidad,$subtotal);
           }else{

           

               $subtotal = $cantidad * $producto['precio_compra'];
               $this->temporal_compra->save([
                'folio'=> $id_compra,
                'id_producto'=>$id_producto,
                'codigo'=> $producto['codigo'],
                'nombre'=> $producto['nombre'],
                'precio'=> $producto['precio_compra'],
                'cantidad'=> $cantidad,
                'unidad_p'=> $proporcion,
                'subtotal'=> $subtotal
                
               ]);
           }
        }else{
            $error = 'No existe el producto';
        }
        $res['datos'] = $this->cargaProductos2($id_compra);
        $res['total'] = number_format($this->totalProductos($id_compra),2,'.',',');
        $res['error'] = $error;
        echo json_encode($res);
      
        
    }
    public function cargaProductos($id_compra){
        $resultado = $this->temporal_compra->porCompra($id_compra);
        $fila = '';
        $numFila =0;
        foreach($resultado as $row){
            $numFila++;
            $fila .="<tr id= 'fila".$numFila."'>";
            $fila .="<td>".$numFila."</td>";
            $fila .="<td>".$row['codigo']."</td>";
            $fila .="<td>".$row['nombre']."</td>";
            $fila .="<td>".$row['precio']."</td>";
            $fila .="<td>".$row['cantidad']."</td>";
            $fila .="<td>".$row['subtotal']."</td>";
            $fila .="<td><a onclick=\"eliminaProducto(". $row['id_producto'].", '". $id_compra . "')\"
        class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
            $fila .="</tr>";
        }
        return $fila;

    }
    public function cargaProductos2($id_compra){
        $resultado = $this->temporal_compra->porCompra($id_compra);
        $fila = '';
        $numFila =0;
        foreach($resultado as $row){
            $numFila++;
            $fila .="<tr id= 'fila".$numFila."'>";
            $fila .="<td>".$numFila."</td>";
            $fila .="<td>".$row['codigo']."</td>";
            $fila .="<td>".$row['nombre']."</td>";
            $fila .="<td>".$row['precio']."</td>";
            $fila .="<td>".$row['cantidad']."</td>";
            $fila .="<td>".$row['subtotal']."</td>";
            $fila .="<td><a onclick=\"eliminaProducto(". $row['id_producto'].", '". $id_compra . "')\"
        class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
            $fila .="</tr>";
        }
        return $fila;

    }
    public function pago($pago){
        
    }

    public function totalProductos($id_compra){
        $resultado = $this->temporal_compra->porCompra($id_compra);
        $total = 0;
        
        foreach($resultado as $row){
         $total += $row['subtotal'];
        }
        return $total;

    }
    public function eliminar($id_producto,$id_compra){
        $datosExiste = $this->temporal_compra->porIdProductoCompra($id_producto,$id_compra);
        if($datosExiste){
            if($datosExiste->cantidad > 1){
                $cantidad = $datosExiste->cantidad -1;
                $subtotal = $cantidad*$datosExiste->precio;
                $pago_recibido = $datosExiste->pago_recibido;
                $cambio = $datosExiste->cambio;
                $id_menu = $datosExiste->id_menu;
                $this->temporal_compra->actualizarProductoCompra($id_producto,$id_compra,$cantidad,$subtotal,$pago_recibido,$cambio,$id_menu);
            }else{
                $this->temporal_compra->eliminarProductoCompra($id_producto,$id_compra);
                
            }
        }
        $res['datos'] = $this->cargaProductos($id_compra);
        $res['total'] = number_format($this->totalProductos($id_compra),2,'.',',');
        $res['error'] = '';
        echo json_encode($res);
    }


}
