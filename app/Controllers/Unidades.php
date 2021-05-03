<?php

namespace App\Controllers;

use App\Models\UnidadesModel;
use App\Controllers\BaseController;
class Unidades extends BaseController
{
    protected $unidades,$session;
    protected $reglas;
    public function __construct()
    {
        $this-> unidades = new UnidadesModel();
        helper(['form']);
        $this->session = session();
        $this->reglas = ['nombre'=>'required','nombre_corto'=>'required'];
    }
    //Asiganar parametros
	public function index($activo = 1)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $unidades = $this->unidades->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Unidades', 'datos'=>$unidades];
		echo view('header');
		echo view('unidades/unidades', $data);
		echo view ('fooder');
        }
	}
    public function nuevo()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $data = ['titulo'=>'Agregar unidad'];
        echo view('header');
		echo view('unidades/nuevo', $data);
		echo view ('fooder');
        } 
    }
    //Agregar unidad
    public function insertar()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            if($this->request->getMethod()=="post" && $this->validate( $this->reglas)) {
                $this->unidades->save(['nombre'=> $this->request->getPost('nombre'),
                'nombre_corto'=> $this->request->getPost('nombre_corto')]);
                return redirect()->to(base_url().'/unidades');
            }else{
                $data =['titulo'=>'Agregar unidad','validation'=>$this->validator];
                echo view('header');
                echo view('unidades/nuevo', $data);
                echo view ('fooder'); 
            }
        }
    
        
    }
    //editar unidad
    public function editar($id)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{   
        $unidad = $this->unidades->where('id',$id)->first();
        $data = ['titulo'=>'Editar unidad', 'datos'=> $unidad];

        echo view('header');
		echo view('unidades/editar', $data);
		echo view ('fooder');
        } 
    }
    public function actualizar()
    {
        if($this->request->getMethod()=="POST" && $this->validate( $this->reglas)) {
        $this->unidades->update($this->request->getPost('id'),['nombre'=> 
        $this->request->getPost('nombre'),'nombre_corto'=> $this->request->getPost
        ('nombre_corto')]);
        return redirect()->to(base_url().'/unidades');
        }else{
            return $this->editar($this->request->getPost('id'),$this->validator);
        }
    }
    //Eliminar unidad
    public function eliminar($id)
    {
        $this->unidades->update($id,['activo'=> 0]);
        return redirect()->to(base_url().'/unidades');
    }
    //Eliminados
    public function eliminados($activo = 0)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $unidades = $this->unidades->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Unidades', 'datos'=>$unidades];
		echo view('header');
		echo view('unidades/eliminados', $data);
		echo view ('fooder');
        }
	}
    public function reingresar($id)
    {
        $this->unidades->update($id,['activo'=> 1]);
        return redirect()->to(base_url().'/unidades');
    }

}
