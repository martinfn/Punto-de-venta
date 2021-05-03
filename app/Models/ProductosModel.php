<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table      = 'productos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['codigo', 'nombre', 'precio_venta',
                                'precio_compra', 'existencias','unidad_p','stock_minimo',
                                'unidad_p','inventariable', 'id_unidad', 'id_categoria', 'activo','unidad_t'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function actualizaStock($id_producto,$cantidad,$unidad_p,$operador='+'){
        $this->set('existencias',"existencias $operador $cantidad",FALSE);
        $this->set('unidad_p',"unidad_p $operador $unidad_p",FALSE);
        $this->where('id',$id_producto);
        $this->update();
    }
    public function actualizaStock2($id_producto,$unidad_p,$operador='+'){
        
        $data = $this->where('id',$id_producto)->first();
       
       // foreach($data as $dato){
        $dato = json_decode($data['unidad_t'], true);
            $cantidad= $unidad_p/$dato;
        //}
        $this->set('unidad_p',"unidad_p $operador $unidad_p",FALSE);
        $this->set('existencias',"existencias $operador $cantidad",FALSE);
        $this->where('id',$id_producto);
        $this->update();
    }
    public function totalProductos(){
        return $this->where('activo',1)->countAllResults();
    }
    public function productosMinimo(){
        $where="stock_minimo >= existencias AND inventariable=1 AND activo=1";
        $this->where($where);
        $sql = $this->countAllResults();
        return $sql;

    }
    public function getproductosMinimo(){
        $where="stock_minimo >= existencias AND inventariable=1 AND activo=1";
       return $this->where($where)->findAll();
        
    }
    public function obtenerP($id_producto){
       return $this->where('id',$id_producto)->findAll();
        
    }
    public function getjoin()
    {
        $this->select('productos.*, u.id_producto AS id_producto');
        $this->join('menu AS u','productos.id = u.id_producto');//INNER JOIN
        $this->where('productos.activo',1);
        
        $datos=$this->findAll();
        
        //print_r($this->getlastQuery());
        return $datos;
    }
}
