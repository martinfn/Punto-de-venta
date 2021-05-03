<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasModel extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio', 'total', 'id_usuario','forma_pago','activo','pago_recibido','cambio','id_menu'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    public function insertaVenta($id_venta,$total,$id_usuario,$forma_pago,$pago_recibido,$cambio){
        $this->insert([
            'folio' => $id_venta,
            'total' => $total,
            'id_usuario' => $id_usuario,
            'forma_pago' => $forma_pago,
            'pago_recibido' => $pago_recibido,
            'cambio' => $cambio,
            
        ]);
        return  $this->insertID();

    }

    public function obtener($activo =1){
        $this->select('ventas.*, u.usuario AS cajero');
        $this->join('usuarios AS u','ventas.id_usuario = u.id');//INNER JOIN
        $this->where('ventas.activo',$activo);
        $this->orderBy('ventas.fecha_alta','DESC');
        $datos=$this->findAll();
        
        //print_r($this->getlastQuery());
        return $datos;
    }
    public function totalDia($fecha){
        $this->select("sum(total) AS total");
        $where ="activo= 1 AND DATE(fecha_alta) = '$fecha'";
        //return $this->where($where)->countAllResults();
        
        return $this->where($where)->first();
         //print_r($this->getlastQuery());
       
    }
}
