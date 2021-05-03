<?php

namespace App\Controllers;


use App\Models\RolesModel;
use App\Controllers\BaseController;
class Usuarios extends BaseController
{
    protected  $roles;
    protected $reglas, $reglaslogin,$reglasCambia,$session;
    public function __construct()
    {
        
        $this-> roles = new RolesModel();
        $this->session = session();
        helper(['form']);

        $this->reglas = [
            'usuario'=>[
                'rules' =>'required|is_unique[usuarios.usuario]',
                'errors'=>[
                    'required'=>'El campo {field} es obligatorio.',
                    'is_unique'=> 'El campo {field} debe ser unico.'
                ]
            ],
            'password'=>[
                'rules'=>'required',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.'
                ]
            ],
            'repassword'=>[
                'rules'=> 'required|matches[password]',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.',
                    'matches'=> 'Las contraseñas no coinciden.'
                ]
            ],
            'nombre'=>[
                'rules'=>'required',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.'
                ]
            ],
            'id_rol'=>[
                'rules'=>'required',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.'
                ]
            ]
        ];
        $this->reglaslogin = [
            'usuario'=>[
                'rules' =>'required',
                'errors'=>[
                    'required'=>'El campo {field} es obligatorio.'
                ]
            ],
            'password'=>[
                'rules'=>'required',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.'
                ]
            ]
        ];
        $this->reglasCambia = [
            'password'=>[
                'rules' =>'required',
                'errors'=>[
                    'required'=>'El campo {field} es obligatorio.'
                ]
            ],
            'repassword'=>[
                'rules'=> 'required|matches[password]',
                'errors'=>[
                    'required'=> 'El campo {field} es obligatorio.',
                    'matches'=> 'Las contraseñas no coinciden.'
                ]
            ]
        ];


    }
    //Asiganar parametros
    public function index($activo = 1)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $roles = $this->roles->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Roles', 'datos'=>$roles];
		echo view('header');
		echo view('configuracion/roles', $data);
		echo view ('fooder');
        }
	}
    public function nuevo()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $roles = $this->roles->where('activo',1)->findAll();
        $data = ['titulo'=>'Agregar usuario','roles'=>$roles];
        echo view('header');
		echo view('usuarios/nuevo', $data);
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
                $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                $this->usuarios->save([
                    'usuario'=> $this->request->getPost('usuario'),
                    'password'=> $hash,
                    'nombre'=> $this->request->getPost('nombre'),
                    'id_rol'=> $this->request->getPost('id_rol'),
                    'activo'=> 1]);

                return redirect()->to(base_url().'/usuarios');
            }else{

                $roles = $this->roles->where('activo',1)->findAll();
                $data =['titulo'=>'Agregar unidad','roles'=>$roles,'validation'=>$this->validator];
                echo view('header');
                echo view('usuarios/nuevo', $data);
                echo view ('fooder'); 
            }
        }
    
        
    }
    //editar unidad
    public function editar($id,$valid=null)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $unidad = $this->usuarios->where('id',$id)->first();
        $data = ['titulo'=>'Editar unidad', 'datos'=> $unidad];
        echo view('header');
		echo view('usuarios/editar', $data);
		echo view ('fooder');
        } 
    }
    public function actualizar()
    {
        $this->usuarios->update($this->request->getPost('id'),['nombre'=> 
        $this->request->getPost('nombre'),'nombre_corto'=> $this->request->getPost
        ('nombre_corto')]);
        return redirect()->to(base_url().'/usuarios');
    }
    //Eliminar unidad
    public function eliminar($id)
    {
        $this->usuarios->update($id,['activo'=> 0]);
        return redirect()->to(base_url().'/usuarios');
    }
    //Eliminados
    public function eliminados($activo = 0)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $usuarios = $this->usuarios->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Usuarios', 'datos'=>$usuarios];
		echo view('header');
		echo view('usuarios/eliminados', $data);
		echo view ('fooder');
        }
	}
    public function reingresar($id)
    {
        $this->usuarios->update($id,['activo'=> 1]);
        return redirect()->to(base_url().'/usuarios');
    }

    public function login(){
        echo view ('login');
    }
    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to(base_url());
    }
    public function cambia_password(){
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $usuario = $this->usuarios->where('id',$session->id_usuario)->first();
        $data = ['titulo'=>'Cambiar contraseña','usuario'=>$usuario];
        echo view('header');
		echo view('usuarios/cambia_password', $data);
		echo view ('fooder');
        } 
    }
    public function actualizar_password(){
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            if($this->request->getMethod()=="post" && $this->validate( $this->reglasCambia)) {
                $session = session();
                $idUsuario =$session->id_usuario;
                $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        
                $this->usuarios->update($idUsuario, ['password' =>$hash]);
        
                $usuario = $this->usuarios->where('id',$session->id_usuario)->first();
                $data = ['titulo'=>'Cambiar contraseña','usuario'=>$usuario,'mensaje'=>'Contraseña actualizada.'];
                echo view('header');
                echo view('usuarios/cambia_password', $data);
                echo view ('fooder'); 
            }else{
        
            $session =session();
            $usuario = $this->usuarios->where('id',$session->id_usuario)->first();
            $data = ['titulo'=>'Cambiar contraseña','usuario'=>$usuario,'validation'=>$this->validator];
            echo view('header');
            echo view('usuarios/cambia_password', $data);
            echo view ('fooder');
            }
        }
    }

    public function valida(){
        if($this->request->getMethod()=="post" && $this->validate( $this->reglaslogin)) {
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $datosUsuario = $this->usuarios->where('usuario',$usuario)->first();
            if($datosUsuario != null){
                if(password_verify($password,$datosUsuario['password'])){
                    $datosSesion =[
                        'id_usuario'=>$datosUsuario['id'],
                        'nombre'=>$datosUsuario['nombre'],
                        'id_rol'=>$datosUsuario['id_rol']
                    ];
                    $session = session();
                    $session->set($datosSesion);
                    return redirect()->to(base_url() . '/inicio');
                }else{
                    $data['error']="Las contraseñas no coinciden.";
                    echo view('login',$data);
            }
                
            }else{
                $data['error']="El usuario no existe";
                echo view('login',$data);
            }
        }else{
            $data=['validation'=>$this->validator];
            echo view('login',$data);
        }
    }
    public function detalles($idRol){
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $data = ['titulo' => 'Asignar permisos']
        echo view('header');
        echo view('roles/detalles');
        echo view('fooder');
        }
    }
}
