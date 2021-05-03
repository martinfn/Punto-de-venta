<?php

namespace App\Controllers;

use App\Models\UnidadesModel;
use App\Models\MenuModel;
use App\Models\CategoriasModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use App\Controllers\BaseController;

class Productos extends BaseController
{
    protected $productos,$session,$configuracion,$menu;
    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->menu = new MenuModel();
        $this->unidades = new UnidadesModel();
        $this->categorias = new CategoriasModel();
        $this->configuracion = new ConfiguracionModel();
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
        $productos = $this->productos->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Productos', 'datos' => $productos];
        echo view('header');
        echo view('productos/productos', $data);
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
        echo view('productos/nuevo', $data);
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
                
                $this->productos->save([
                    'codigo' => $this->request->getPost('codigo'),
                    'nombre' => $this->request->getPost('nombre'),
                    'precio_compra' => $this->request->getPost('precio_compra'),
                    'stock_minimo' => $this->request->getPost('stock_minimo'),
                    'inventariable' => $this->request->getPost('inventariable'),
                    'id_unidad' => $this->request->getPost('id_unidad'),
                    'unidad_t' => $this->request->getPost('id_categoria')
                    
                ]);

                $id = $this->productos->insertID();

                
              //  if ($this->validacion) {
                //    $ruta_logo = "images/productos" . $id . ".jpg";
               //     if (file_exists($ruta_logo)) {
                 //       unlink($ruta_logo);
                //    }
               //     $img = $this->request->getFile('img_producto');
                //    $img->move('./images/productos', $id . ".jpg");
    
              //  } else {
              //      echo 'Ingrese una imagen del producto';
             //       exit;
              //  }

                return redirect()->to(base_url() . '/productos');
            } else {
                $unidades = $this->unidades->where('activo', 1)->findAll();
                $categorias = $this->categorias->where('activo', 1)->findAll();
                $data = ['titulo' => 'Agregar producto', 'unidades' => $unidades, 'categorias' => $categorias, 'validation' => $this->validator];
                echo view('header');
                echo view('productos/nuevo', $data);
                echo view('fooder');
            }
        }
    }
    //editar producto
    public function editar($id)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            $unidades = $this->unidades->where('activo', 1)->findAll();
            $categorias = $this->categorias->where('activo', 1)->findAll();
            $producto = $this->productos->where('id', $id)->first();
            $data = [
                'titulo' => 'Editar producto', 'unidades' => $unidades, 'categorias' => $categorias,
                'producto' => $producto
            ];
            echo view('header');
            echo view('productos/editar', $data);
            echo view('fooder');
        }
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
                    'id_unidad' => $this->request->getPost('id_unidad'),
                    'unidad_t' => $this->request->getPost('id_categoria')
                ]
            );
            return redirect()->to(base_url() . '/productos');
        }
    }
    //Eliminar producto
    public function eliminar($id)
    {
        $this->productos->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/productos');
    }
    //Eliminados
    public function eliminados($activo = 0)
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $productos = $this->productos->where('activo', $activo)->findAll();
        $data = ['titulo' => 'Productos', 'datos' => $productos];
        echo view('header');
        echo view('productos/eliminados', $data);
        echo view('fooder');
        }
    }
    public function reingresar($id)
    {
        $this->productos->update($id, ['activo' => 1]);
        return redirect()->to(base_url() . '/productos');
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

        $productos = $this->menu->like('codigo', $valor)->where('activo2', 1)->findAll();
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
    public function mostrarMinimos()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        echo view('header');
        echo view('productos/ver_minimos');
        echo view('fooder');
        }
    }
    public function generaMinimospdf()
    {
       
        
        $nombreTienda = $this->configuracion->select('valor')->where('nombre','tienda_nombre')
        ->get()->getRow()->valor;
        $direccionTienda = $this->configuracion->select('valor')->where('nombre','tienda_direccion')
        ->get()->getRow()->valor;

        $pdf = new \FPDF('P', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Productos con stock mínimo");
        $pdf->setFont("Arial", 'B', 10);

      

        $pdf->Cell(0, 5, utf8_decode("Reporte de producto con stock mínimo"), 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->image(base_url().'/images/logo.JPG',175,10,25,20,'JPG');
        $pdf->Ln();
        $pdf->Cell(50,5, $nombreTienda , 0, 1, 'L');
        $pdf->Cell(20,5, utf8_decode('Dirección: ') , 0, 0, 'L');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(50,5, $direccionTienda , 0, 1, 'L');

        $pdf->Ln();
        
        
        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(190,4,'Detalle de productos',1,1,'C',1);
        $pdf->SetTextColor(0,0,0);


        $pdf->Cell(30, 5, utf8_decode("Codigo"), 1, 0, "C");
        $pdf->Cell(100, 5, utf8_decode("Nombre"), 1, 0, "C");
        $pdf->Cell(30, 5, utf8_decode("Existencias"), 1, 0, "C");
        $pdf->Cell(30, 5, utf8_decode("Stock mínimo"), 1, 1, "C");

        $datosProductos = $this->productos->getproductosMinimo();
        foreach ($datosProductos as $producto) {
            $pdf->Cell(30, 5, $producto['codigo'], 1, 0, "C");
            $pdf->Cell(100, 5, utf8_decode($producto['nombre']), 1, 0, "C");
            $pdf->Cell(30, 5, $producto['existencias'], 1, 0, "C");
            $pdf->Cell(30, 5, $producto['stock_minimo'], 1, 1, "C");
        }

        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("ProductoMinimo.pdf", "I");
    }
}
