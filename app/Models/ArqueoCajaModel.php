<?php

namespace App\Models;

use CodeIgniter\Model;

class ArqueoCajaModel extends Model
{
    protected $table      = 'arqueo_caja';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_usuario', 'fecha_inicio', 'fecha_fin','monto_inicial'
                                ,'monto_final','total_ventas','estatus'];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    public function insertaCompra($id_compra,$total,$id_usuario){
        $this->insert([
            'folio' => $id_compra,
            'total' => $total,
            'id_usuario' => $id_usuario
        ]);
        return  $this->insertID();

    }
}
