<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\TransacaoModel;

class HomeController extends BaseController
{
	public function index()
	{
		if (session()->isLoggedId) {
			$pedidoModel = new PedidoModel();
			$transacaoModel = new TransacaoModel();

			$data = [
				'title' => 'Dashboard',
				'totais' => $pedidoModel->totaisPorStatus(),
				'resumoMes' => $transacaoModel->resumo(date('Y-m-01'), date('Y-m-t')),
				'vendasPorDia' => $transacaoModel->vendasPorDia(30),
				'ultimosPedidos' => $pedidoModel->comCliente()->findAll(6),
				'estoqueBaixo' => (new ProdutoModel())->estoqueBaixo(6)
			];
			return view('Admin/Home/home', $data);
		}

		return redirect()->route('login');
	}
}
