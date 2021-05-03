<?php

namespace App\Controllers;
use App\Models\ProductosModel;
use App\Models\VentasModel;
class Inicio extends BaseController
{
	protected $productoModel,$ventasModel,$session;
	public function __construct(){
		$this->productosModel = new ProductosModel();
		$this->ventasModel = new VentasModel();
		$this->session = session();

	}
	public function index()
	{
		if(!isset($this->session->id_usuario)){
			return redirect()->to(base_url());
		}else{
		$total=$this->productosModel->totalProductos();
		$minimos= $this->productosModel->productosMinimo();
		$totalVentas = $this->ventasModel->totalDia(date('Y-m-d'));//año-mes-día
		$datos=['total'=>$total, 'totalVentas'=> $totalVentas, 'minimos' => $minimos];

		echo view('header');
		echo view('inicio',$datos);
		echo view ('fooder');
		}
	}
}
