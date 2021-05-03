<?php

namespace App\Controllers;
use App\Models\UnidadesModel;
use App\Models\CategoriasModel;
use App\Models\ComprasModel;
use App\Models\TemporalCompraModel;
use App\Models\DetalleCompraModel;
use App\Models\ProductosModel;
use App\Models\ConfiguracionModel;
use App\Controllers\BaseController;
class Compras extends BaseController
{
    protected $compras,$temporal_compra,$detalle_compra,$productos,$configuracion,$session,$unidades,$categorias;
    protected $reglas;
    public function __construct()
    {
        $this->categorias = new CategoriasModel();
        $this->unidades = new UnidadesModel();
        $this-> compras = new ComprasModel();
        $this-> detalle_compra = new DetalleCompraModel();
        $this-> configuracion = new ConfiguracionModel();
        $this->session = session();
        helper(['form']);

    }
    //Asiganar parametros
	public function index($activo = 1)
	{
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $compras = $this->compras->where('activo',$activo)->findAll();
        $data = ['titulo'=>'Compras','compras'=>$compras];
		echo view('header');
		echo view('compras/compras', $data);
		echo view ('fooder');
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
		echo view('compras/nuevo',$data);
		echo view ('fooder');
        }
    }
    //Agregar unidad
    public function guarda()
    {

        $id_compra =  $this->request->getPost('id_compra');
        
        $total = preg_replace('/[\$,]/','',$this->request->getPost('total'));
        $session = session();
        
        $resultadoId = $this->compras->insertaCompra($id_compra,$total,$session->id_usuario);
        
        $this->temporal_compra = new TemporalCompraModel();
        if($resultadoId){
            $resultadoCompra = $this->temporal_compra->porCompra($id_compra);
            foreach($resultadoCompra as $row){
                $this->detalle_compra->save([
                    'id_compra' =>$resultadoId,
                    'id_producto' => $row['id_producto'],
                    'nombre' => $row['nombre'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio']

                ]);
                $this->productos = new ProductosModel();
                $this->productos->actualizaStock($row['id_producto'],$row['cantidad'],$row['unidad_p']);

            }
            
            $this->temporal_compra->eliminarCompra($id_compra);
        }
        return redirect()->to(base_url()."/compras/muestracomprapdf/" .$resultadoId);
    
        
    }

    function muestracomprapdf($id_compra){
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $data['id_compra'] = $id_compra;

        echo view('header');
        echo view ('compras/ver_compra_pdf' , $data);
        echo view ('fooder'); 
        }

    }

     function generacomprapdf($id_compra){
         
        $datosCompra =$this->compras->where('id',$id_compra)->first();
        $detalleCompra = $this->detalle_compra->select('*')->where('id_compra',$id_compra)->findAll();
        $nombreTienda = $this->configuracion->select('valor')->where('nombre','tienda_nombre')
        ->get()->getRow()->valor;
        $direccionTienda = $this->configuracion->select('valor')->where('nombre','tienda_direccion')
        ->get()->getRow()->valor;

        $pdf = new \FPDF('P','mm','letter');
        $pdf->AddPage();
        $pdf->SetMargins(10,10,10);
        $pdf->SetTitle('Compra');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(195,5,"Entrada de productos",0,1, 'C');
        $pdf->SetFont('Arial','B',9);

        $pdf->image(base_url().'/images/logo.JPG',180,10,25,20,'JPG');
        $pdf->Cell(50,5, $nombreTienda , 0, 1, 'L');
        $pdf->Cell(20,5, utf8_decode('Dirección: ') , 0, 0, 'L');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(50,5, $direccionTienda , 0, 1, 'L');

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(25,5, utf8_decode('Fecha y hora: ') , 0, 0, 'L');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(50,5, $datosCompra['fecha_alta'] , 0, 1, 'L');

        $pdf->Ln();

        $pdf->SetFont('Arial','B',8);
        $pdf->SetFillColor(0,0,0);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(195,4,'Detalle de productos',1,1,'C',1);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(14,5,'No ',1,0,'L');
        $pdf->Cell(25,5,utf8_decode('Código '),1,0,'L');
        $pdf->Cell(77,5,'Nombre ',1,0,'L');
        $pdf->Cell(25,5,'Precio ',1,0,'L');
        $pdf->Cell(25,5,'Cantidad ',1,0,'L');
        $pdf->Cell(29,5,'Importe ',1,1,'L');

        $pdf->SetFont('Arial','',8);
        $contador =1;
        foreach($detalleCompra as $row){
            $pdf->Cell(14,5,$contador,1,0,'L');
            $pdf->Cell(25,5,$row['id_producto'],1,0,'L');
            $pdf->Cell(77,5,$row['nombre'],1,0,'L');
            $pdf->Cell(25,5,$row['precio'],1,0,'L');
            $pdf->Cell(25,5,$row['cantidad'],1,0,'L');
            $importe = number_format( $row['precio'] * $row['cantidad'],2,'.',',');
            $pdf->Cell(29,5, '$' . $importe,1,1,'R');
            $contador++;
        }
        $pdf->Ln();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(195,5, 'Total $' . number_format($datosCompra['total'],2,'.',','),0,1,'R');

        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("compra_".$datosCompra['fecha_alta'].".pdf","I");

    }

}
