<?php

namespace App\Controllers;

use App\Models\CategoriasModel;
use App\Controllers\BaseController;

class Categorias extends BaseController
{
    protected $categorias,$session;
    public function __construct()
    {
        $this->session = session();
        $this->categorias = new CategoriasModel();
    }
    //Asiganar parametros
    public function index($activo = 1)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $categorias = $this->categorias->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Categorias', 'datos' => $categorias];
        echo view('header');
        echo view('categorias/categorias', $data);
        echo view('fooder');
        }
    }
    public function nuevo()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $data = ['titulo' => 'Agregar categoria'];
        echo view('header');
        echo view('categorias/nuevo', $data);
        echo view('fooder');
        }
    }
    //Agregar unidad
    public function insertar()
    {
        $this->categorias->save(['nombre' => $this->request->getPost('nombre')]);
        return redirect()->to(base_url() . '/categorias');
    }
    //editar unidad
    public function editar($id)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $unidad = $this->categorias->where('id', $id)->first();
        $data = ['titulo' => 'Editar categoria', 'datos' => $unidad];
        echo view('header');
        echo view('categorias/editar', $data);
        echo view('fooder');
        }
    }
    public function actualizar()
    {
        $this->categorias->update($this->request->getPost('id'), ['nombre' =>
        $this->request->getPost('nombre')]);
        return redirect()->to(base_url() . '/categorias');
    }
    //Eliminar unidad
    public function eliminar($id)
    {
        $this->categorias->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/categorias');
    }
    //Eliminados
    public function eliminados($activo = 0)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $categorias = $this->categorias->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Categorias', 'datos' => $categorias];
        echo view('header');
        echo view('categorias/eliminados', $data);
        echo view('fooder');
        }
    }
    public function reingresar($id)
    {
        $this->categorias->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/categorias');
    }
}
