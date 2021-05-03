<?php

namespace App\Controllers;

use App\Models\UnidadesModel;
use App\Models\CategoriasModel;
use App\Models\ProductosModel;
use App\Models\MenuModel;
use App\Models\ConfiguracionModel;
use App\Controllers\BaseController;

class Menu extends BaseController
{
    protected $productos,$session,$configuracion,$menu;
    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->unidades = new UnidadesModel();
        $this->categorias = new CategoriasModel();
        $this->configuracion = new ConfiguracionModel();
        $this->menu = new MenuModel();
        $this->session = session();
        helper(['form']);
        $this->reglas = [
            'codigo'=>[
                'rules' =>'required|is_unique[productos.codigo]',
                'errors'=>[
                    'required'=>'El campo {field} es obligatorio.',
                    'is_unique'=> 'Ya existe un producto con este Código.'
                ]
            ]
        ];
        $this->validacion = [
            'img_producto' => [
                'uploaded[img_producto]',
                'mime_in[img_producto,image/jpg,image/jpeg]',
                'max_size[img_producto,4096]'
            ]
        ];
    }
    //Asiganar parametros
    public function index($activo = 1)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $menu = $this->menu->where('activo2', $activo)->findAll();
        $data = ['titulo' => 'Menú', 'datos' => $menu];
        echo view('header');
        echo view('menu/menu', $data);
        echo view('fooder');
        }
    }
    public function nuevo()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $unidades = $this->unidades->where('activo', 1)->findAll();
        $categorias = $this->categorias->where('activo', 1)->findAll();
        $data = ['titulo' => 'Agregar producto', 'unidades' => $unidades, 'categorias' => $categorias];
        echo view('header');
        echo view('menu/nuevo', $data);
        echo view('fooder');
        }
    }
    //Agregar producto
    public function insertar()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {

                $this->menu->save([
                    'codigo' => $this->request->getPost('codigo'),
                    'nombre' => $this->request->getPost('nombre'),
                    'precio_venta' => $this->request->getPost('precio_venta'),
                    'inventariable' => $this->request->getPost('inventariable'),
                    'activo'=>0,
                    'activo2'=>1
                ]);

                $id = $this->menu->insertID();

                
               // if ($this->validacion) {
                //    $ruta_logo = "images/menu" . $id . ".jpg";
                //    if (file_exists($ruta_logo)) {
                //        unlink($ruta_logo);
                //    }
                //    $img = $this->request->getFile('img_menu');
                 //   $img->move('./images/menu', $id . ".jpg");
    
               // } else {
               //     echo 'Ingrese una imagen del producto';
                //    exit;
               // }

                return redirect()->to(base_url() . '/menu');
            } else {
                $unidades = $this->unidades->where('activo', 1)->findAll();
                $categorias = $this->categorias->where('activo', 1)->findAll();
                $data = ['titulo' => 'Agregar producto', 'unidades' => $unidades, 'categorias' => $categorias, 'validation' => $this->validator];
                echo view('header');
                echo view('menu/nuevo', $data);
                echo view('fooder');
            }
        }
    }
    //editar producto
    public function editar($id,$codigo)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            $menu = $this->menu->where('id', $id)->first();
           // $menu2 = $this->menu->where('codigo', $codigo)->findAll();
           $menu2 = $this->menu->ingredientes($codigo,0);
            $unidades = $this->unidades->where('activo', 1)->findAll();
            $categorias = $this->categorias->where('activo', 1)->findAll();
            $producto = $this->productos->where('activo', 1)->findAll();
           // $producto = $this->productos->getjoin();
          // $producto2 = [''];
          //  foreach($menu2 as $row){
            //    $producto = $this->BuscarporId($row['id_producto']);
              
           // }
           // $dato = json_decode($producto['nombre'], true);
            $data = [
                'titulo' => 'Editar producto', 'produc' => $producto, 'categorias' => $categorias,
                'producto' => $menu,'datos_menu' => $menu2 
            ];
            echo view('header');
            echo view('menu/editar', $data);
            echo view('fooder');
        }
    }
    public function eliminar($id,$codigo,$id_menu){
        $this->menu->delete($id);
        return redirect()->to(base_url() . '/menu/editar/'.$id_menu.'/'.$codigo);
    }
    public function agregar($id){
        
            $menu = $this->menu->where('id', $id)->first();
           
            $data = ['datos_menu'=> $menu,'titulo'=> 'Editar'];
            
                echo view('header');
                echo view('menu/agregar', $data);
                echo view('fooder');
            
        
    }
    public function actualizarm($id_producto,$proporcion,$nombre,$codigo){
         
         $this->menu->save([
            'codigo'=> $codigo,
            'nombre'=> $nombre,
            'id_producto'=> $id_producto,
            'proporcion'=> $proporcion,
            'activo2' => 0
           ]);
           $res['datos'] = $this->cargaProductos($codigo);
           //$res['total'] = number_format($this->totalProductos($id_compra),2,'.',',');
           //$res['error'] = $error;
           echo json_encode($res);
    }
    public function actualizar()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            $this->productos->update(
                $this->request->getPost('id'),
                [
                    'codigo' => $this->request->getPost('codigo'),
                    'nombre' => $this->request->getPost('nombre'),
                    'precio_venta' => $this->request->getPost('precio_venta'),
                    'precio_compra' => $this->request->getPost('precio_compra'),
                    'stock_minimo' => $this->request->getPost('stock_minimo'),
                    'inventariable' => $this->request->getPost('inventariable'),
                    'proporcion' => $this->request->getPost('proporcion'),
                    'id_categoria' => $this->request->getPost('id_categoria')
                ]
            );
            return redirect()->to(base_url() . '/productos');
        }
    }
  
    //Eliminados
    public function eliminados($activo = 0)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $menu = $this->menu->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Productos', 'datos' => $menu];
        echo view('header');
        echo view('menu/eliminados', $data);
        echo view('fooder');
        }
    }
    public function reingresar($id)
    {
        $this->menu->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/menu');
    }
    public function buscarPorCodigo($codigo)
    {
        $this->productos->select('*');
        $this->productos->where('codigo', $codigo);
        $this->productos->where('activo', 1);
        $datos = $this->productos->get()->getRow();

        $res['existe'] = false;
        $res['datos'] = '';
        $res['error'] = '';
        if ($datos) {
            $res['datos'] = $datos;
            $res['existe'] = true;
        } else {
            $res['error'] = 'No existe el producto';
            $res['existe'] = false;
        }
        echo json_encode($res);
    }
    public function autocompleteData()
    {
        $returnData = array();
        $valor = $this->request->getGet('term');

        $productos = $this->productos->like('codigo', $valor)->where('activo', 1)->findAll();
        if (!empty($productos)) {
            foreach ($productos as $row) {
                $data['id'] = $row['id'];
                $data['value'] = $row['codigo'];
                $data['label'] = $row['codigo'] . ' - ' . $row['nombre'];
                array_push($returnData, $data);
            }
        }
        echo json_encode($returnData);
    }
  

    public function cargaProductos($id){
    $menu2 = $this->menu->where('codigo', $id)->findAll();
    $fila = '';
    $numFila =0;
    foreach($menu2 as $row){
        $numFila++;
        $fila .="<tr id= 'fila".$numFila."'>";
        $fila .="<td>".$numFila."</td>";
        $fila .="<td>".$row['codigo']."</td>";
        $fila .="<td>".$row['nombre']."</td>";
        $fila .="<td>".$row['id_producto']."</td>";
        $fila .="<td>".$row['proporcion']."</td>";
        $fila .="<td><a onclick=\"eliminaProducto(". $row['id_producto'].", '')\"
    class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
        $fila .="</tr>";
    }
    return $fila;
}

}