<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;
use App\Models\ArqueoCajaModel;
use App\Models\VentasModel;
use App\Models\RolesModel;
use App\Controllers\BaseController;
class Configuracion extends BaseController
{
    protected $configuracion,$roles,$session,$arqueoModel,$ventasModel;
    protected $reglas;
    public function __construct()
    {
        $this-> configuracion = new ConfiguracionModel();
        $this-> roles = new RolesModel();
        $this-> ventasModel = new VentasModel();
        $this-> arqueoModel = new ArqueoCajaModel();
        $this->session = session();
        helper(['form','upload']);

        $this->reglas = ['nombre'=>'required','nombre_corto'=>'required'];
    }
    //Asiganar parametros
	public function index($activo = 1)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $nombre = $this->configuracion->where('nombre','tienda_nombre')->first();
        $rfc = $this->configuracion->where('nombre','tienda_rfc')->first();
        $telefono = $this->configuracion->where('nombre','tienda_telefono')->first();
        $email = $this->configuracion->where('nombre','tienda_email')->first();
        $direccion = $this->configuracion->where('nombre','tienda_direccion')->first();
        $leyenda = $this->configuracion->where('nombre','ticket_leyenda')->first();
        $data = ['titulo'=>'Configuracion','nombre'=>$nombre,'rfc'=>$rfc
        ,'telefono'=>$telefono,'email'=>$email,'direccion'=>$direccion,'leyenda'=>$leyenda];
		echo view('header');
		echo view('configuracion/configuracion',$data);
		echo view ('fooder');
        }
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
    public function arqueo(){
        $arqueos =$this->arqueoModel->where('estatus',1)->findAll();
        $data = ['titulo'=>'Cierres de caja', 'datos'=>$arqueos];
        echo view('header');
		echo view('configuracion/arqueos', $data);
		echo view ('fooder');
    }
    public function nuevo_arqueo(){
        $session = session();
        $existe = 0;
        $existe =$this->arqueoModel->where(['estatus' => 1])->countAllResults();
        if($existe > 0){
            echo 'La caja ya está abierta';
            exit;
        }
        if($this->request->getMethod()=="post"){
            $fecha = date('Y-m-d H:i:s');
            
            
            $this->arqueoModel->save([
                'id_usuario' => $session->id_usuario,
                'fecha_inicio'=> $fecha,
                'monto_inicial'=> $this->request->getPost('monto_inicial'),
                'estatus'=> 1
            ]);
            return redirect()->to(base_url().'/configuracion/arqueo');
        }else{
            $data = ['titulo'=>'Apertura de caja'];
            echo view('header');
            echo view('configuracion/nuevo_arqueo', $data);
            echo view ('fooder');  
        }
    }
    public function cierre(){
        $session = session();
        $existe = 0;
       
        if($this->request->getMethod()=="post"){
            $fecha = date('Y-m-d H:i:s');
            $fecha_hora=$this->request->getPost('fecha_fin');
            
            $this->arqueoModel->update($this->request->getPost('id_arqueo'),[
                'id_usuario' => $session->id_usuario,
                'fecha_fin'=> $fecha,
                'monto_final'=> $this->request->getPost('monto_final'),
                'total_ventas'=> $this->request->getPost('total_ventas'),
                'estatus'=> 0
            ]);
            return redirect()->to(base_url().'/configuracion/arqueo');
        }else{
           // $date= $this->arqueoModel->select('fecha_inicio',10)->where('estatus', 1)->first();
            $date= $this->fechayhora();
            
            $montoTotal = $this->ventasModel->totalDia($date);
            $arqueo = $this->arqueoModel->where(['estatus' => 1])->first();
            $data = ['titulo'=>'Cerrar caja','arqueo' => $arqueo,'monto'=> $montoTotal ];
            echo view('header');
            echo view('configuracion/cerrar', $data);
            echo view ('fooder');  
        }
    }
    public function fechayhora($activo = 1){
        $date2= $this->arqueoModel->select('(SELECT left(fecha_inicio,10) FROM arqueo_caja WHERE estatus=1)')->first();
        return implode($date2);
    }

}
