<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;
use App\Models\ArqueoCajaModel;
use App\Models\RolesModel;
use App\Controllers\BaseController;
class Configuracion extends BaseController
{
    protected $configuracion,$roles,$session,$arqueoModel;
    protected $reglas;
    public function __construct()
    {
        $this-> configuracion = new ConfiguracionModel();
        $this-> roles = new RolesModel();
        $this-> $arqueoModel = new ArqueoCajaModel();
        $this->session = session();
        helper(['form','upload']);

        $this->reglas = ['nombre'=>'required','nombre_corto'=>'required'];
    }
    //Asiganar parametros
	public function index()
	{
        $arqueos =$this->arqueoModel->where('estatus',1)->findAll();
        $data = ['tituo'=>'Cierres de caja', 'datos'=>$arqueos];
        echo view('header');
		echo view('configuracion/arqueos', $data);
		echo view ('fooder');
	}
    
 
    public function actualizar()
    {

      /*  $this->configuracion->update(['nombre' =>'tienda_nombre'],['valor'=> 
        $this->request->getPost('tienda_nombre')]);*/
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $this->configuracion->whereIn('nombre',['tienda_nombre'])->set(['valor'=> 
        $this->request->getPost('tienda_nombre')])->update();
        $this->configuracion->whereIn('nombre',['tienda_rfc'])->set(['valor'=> 
        $this->request->getPost('tienda_rfc')])->update();
        $this->configuracion->whereIn('nombre',['tienda_telefono'])->set(['valor'=> 
        $this->request->getPost('tienda_telefono')])->update();
        $this->configuracion->whereIn('nombre',['tienda_email'])->set(['valor'=> 
        $this->request->getPost('tienda_email')])->update();
        $this->configuracion->whereIn('nombre',['tienda_direccion'])->set(['valor'=> 
        $this->request->getPost('tienda_direccion')])->update();
        $this->configuracion->whereIn('nombre',['ticket_leyenda'])->set(['valor'=> 
        $this->request->getPost('ticket_leyenda')])->update();


        $validacion = $this->validate([
            'tienda_logo' => [
                'uploaded[tienda_logo]',
                'mime_in[tienda_logo,image/jpg,image/jpeg]',
                'max_size[tienda_logo,4096]'
            ]
        ]);
        if($validacion){
            $ruta_logo = "images/logo.JPG";
            if(file_exists($ruta_logo)){
                unlink($ruta_logo);
            }
            $img = $this->request->getFile('tienda_logo');
            $img->move('./images','logo.JPG');
        }else{
            echo 'Verificar validación de logotipo';
            exit;
        }
        //$img->move(WRITEPATH. '/uploads');
        return redirect()->to(base_url().'/configuracion');
        }
    }
   
   
    public function eliminados($activo = 0)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $roles = $this->roles->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Roles', 'datos'=>$roles];
		echo view('header');
		echo view('configuracion/roles_eliminados', $data);
		echo view ('fooder');
        }
	}
    
}
