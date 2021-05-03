<?php

namespace App\Controllers;

use App\Models\VentasModel;
use App\Models\UsuariosModel;
use App\Models\ArqueoCajaModel;
use App\Models\TemporalCompraModel;
use App\Models\DetalleVentaModel;
use App\Models\DetalleMenuModel;
use App\Models\ProductosModel;
use App\Models\MenuModel;
use App\Models\ConfiguracionModel;
use App\Controllers\BaseController;

class Ventas extends BaseController
{
    protected $ventas, $temporal_compra, $detalle_venta, $productos, $configuracion, $session,$menu,$arqueoModel,$usuarios;
    public $resultado;
    public function __construct()
    {
        $this->ventas = new VentasModel();
        $this->usuarios = new UsuariosModel();
        $this->arqueoModel = new ArqueoCajaModel();
        $this->menu = new MenuModel();
        $this->productos = new ProductosModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->detalle_menu = new DetalleMenuModel();
        $this->configuracion = new ConfiguracionModel();
        $this->session = session();
        helper(['form']);
    }
    //Asiganar parametros
    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        } else {
           
            $datos = $this->ventas->obtener(1);
            $data = ['titulo' => 'Ventas', 'datos' => $datos];
            echo view('header');
            echo view('ventas/ventas', $data);
            echo view('fooder');
        }
    }
    public function eliminados()
    {
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
        $datos = $this->ventas->obtener(0);
        $data = ['titulo' => 'Ventas eliminadas', 'datos' => $datos];
        echo view('header');
        echo view('ventas/eliminados', $data);
        echo view('fooder');
        }
    }
    public function venta($activo = 1)
    {
        $existe =$this->arqueoModel->where(['estatus' => 1])->countAllResults();
        if(!$existe > 0){
            echo 'No se ha abierto la caja';
            exit;
        }
        if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
            $menu = $this->menu->where('activo2', $activo)->findAll();
            $productos = $this->productos->where('activo', $activo)->findAll();
            $data = ['datos' => $productos, 'id' => $productos,'datos_menu'=> $menu];
            if (!isset($this->session->id_usuario)) {
                return redirect()->to(base_url());
            } else {
                echo view('header');
                echo view('ventas/caja', $data);
                echo view('fooder');
            }
        }
    }

    
    //Agregar unidad
    public function guarda()
    {

        $id_venta =  $this->request->getPost('id_venta');
        $forma_pago =  $this->request->getPost('forma_pago');
        $pago_recibido =  $this->request->getPost('pago_recibido');
        $cambio =  $this->request->getPost('cambio');
        

        $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));

        $resultadoId = $this->ventas->insertaVenta($id_venta, $total, $this->session->id_usuario, $forma_pago,$pago_recibido,$cambio);
        
        $this->temporal_compra = new TemporalCompraModel();
        if ($resultadoId) {
            
            $resultadoCompra = $this->temporal_compra->porCompra($id_venta);
            

            foreach ($resultadoCompra as $row) {
                $this->detalle_venta->save([
                    'id_venta' => $resultadoId,
                    'id_producto' => $row['id_producto'],
                    'nombre' => $row['nombre'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio'],
                    'id_menu' => $row['id_menu'],
                    'cambio' => $cambio
                ]);
               //$resultadomenu = $this->temporal_compra->porMenu($row['id_menu']);
                
                $this->detalle_menu->save([
                    'id_venta' => $resultadoId,
                    'id_producto' => $row['id_producto'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio'],
                    
                    
                ]);
                $resultadop = $this->menu->porCompra2($row['id_menu']);
                
                foreach($resultadop as $row2){
                    
                    $this->productos->actualizaStock2($row2['id_producto'], $row2['proporcion']*$row['cantidad'], '-');
               }
   
                $this->productos = new ProductosModel();
                
                
                //$this->productos->actualizaStock($row['id_producto'], $row['cantidad'], '-');
            }

           //foreach($resultadomenu as $roww){
                
           //}
           
            $this->temporal_compra->eliminarCompra($id_venta);
        }
        return redirect()->to(base_url() . "/ventas/muestraticket/" . $resultadoId);
    }

    function muestraticket($id_venta)
    {
        $data['id_venta'] = $id_venta;

        echo view('header');
        echo view('ventas/ver_ticket', $data);
        echo view('fooder');
    }

    function generaticket($id_venta)
    {
     
        $datosVenta = $this->ventas->where('id', $id_venta)->first();
        $detalleVenta = $this->detalle_venta->select('*')->where('id_venta', $id_venta)->findAll();
        $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')
            ->get()->getRow()->valor;
        $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')
            ->get()->getRow()->valor;
        $leyendaticket = $this->configuracion->select('valor')->where('nombre', 'ticket_leyenda')
            ->get()->getRow()->valor;
        $usuario = $this->usuarios->where('id', $datosVenta['id_usuario'])->first();

        $pdf = new \FPDF('P', 'mm', array(80, 200));
        $pdf->AddPage();
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetTitle('Venta');
        
        $pdf->image(base_url() . '/images/logo.JPG', 27, 10, 25, 20, 'JPG');
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, '', 0, 1, 'C');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        
        $pdf->Cell(70, 5, $nombreTienda, 0, 0, 'C');
        $pdf->Ln();
        $pdf->Cell(70, 5, '==================================', 0, 0, 'C');

        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(7, 5, 'Cant. ', 0, 0, 'L');
        $pdf->Cell(40, 5, 'Nombre ', 0, 0, 'L');
        $pdf->Cell(13, 5, 'Precio ', 0, 0, 'L');
        $pdf->Cell(15, 5, 'Importe ', 0, 1, 'L');

        $pdf->SetFont('Arial', '', 7);
        $contador = 1;
        foreach ($detalleVenta as $row) {
            $pdf->Cell(7, 5, $row['cantidad'], 0, 0, 'L');
            $pdf->Cell(40, 5, $row['nombre'], 0, 0, 'L');
            $pdf->Cell(9, 5, $row['precio'], 0, 0, 'L');
            $importe = number_format($row['precio'] * $row['cantidad'], 2, '.', ',');
            $pdf->Cell(15, 5, '$' . $importe, 0, 1, 'R');
            $contador++;
        }
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 5, '==================================', 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(70, 5, 'Total $' . number_format($datosVenta['total'], 2, '.', ','), 0, 0, 'R');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(70, 5, 'Pago recibido $' . number_format($datosVenta['pago_recibido'], 2, '.', ','), 0, 0, 'R');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(70, 5, 'Cambio $' . number_format($datosVenta['cambio'], 2, '.', ','), 0, 1, 'R');

        

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 5, utf8_decode('Cajero:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5,$usuario['nombre'] , 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(20, 5, utf8_decode('Dirección:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5,$direccionTienda, 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, 5, utf8_decode('Fecha y hora: '), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(50, 5, $datosVenta['fecha_alta'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(25, 5, utf8_decode('Folio: '), 0, 0, 'L');
        $pdf->Cell(50, 5, $datosVenta['folio'], 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(70, 5, '==================================', 0, 1, 'C');

        $pdf->Ln();
        $pdf->Multicell(70, 4, $leyendaticket, 0, 'C', 0);
        
        
        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("ticket.pdf", "I");
    }
    public function eliminar($id)
    {
        $productos = $this->detalle_venta->where('id_venta', $id)->findAll();
        foreach ($productos as $producto) {
            $this->productos->actualizaStock($producto['id_producto'], $producto['cantidad'], '+');
        }
        $this->ventas->update($id, ['activo' => 0]);
        return redirect()->to(base_url() . '/ventas');
    }
}
