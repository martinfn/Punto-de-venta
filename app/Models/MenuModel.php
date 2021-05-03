<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table      = 'menu';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['codigo', 'nombre', 'precio_venta',
                                'id_producto', 'id_unidad', 'proporcion',
                                'activo','activo2'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    
    public function totalProductos(){
        return $this->where('activo',1)->countAllResults();
    }
    public function porCompra2($id_menu){
        $this->select('*');
        $this->where('codigo',$id_menu);
        $this->where('activo',1);
        
        $datos2 = $this->findAll();
        return $datos2;

    }
    public function getjoin()
    {
        $this->select('menu.*, u.id AS id_producto');
        $this->join('productos AS u','menu.id_producto = u.id');//INNER JOIN
        $this->where('menu.activo',1);
        
        $datos=$this->findAll();
        
        //print_r($this->getlastQuery());
        return $datos;
    }
    
    public function ingredientes($codigo, $activo){
        
        $this->where('codigo' ,$codigo );
        $this->where('activo2', $activo);
        $datos=$this->findAll();
        return $datos;
    }
    
}
