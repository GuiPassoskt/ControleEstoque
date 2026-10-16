<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\PedidoLibrary;
use App\Models\ItemPedidoModel;
use App\Models\PedidoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PedidoController extends BaseController
{
	private $pedidoModel;

	public function __construct()
	{
		$this->pedidoModel = new PedidoModel();
	}

	public function index()
	{
		if (session()->isLoggedId) {
			$status = (string) $this->request->getGet('status');
			$busca = trim((string) $this->request->getGet('busca'));

			$data = [
				'title' => 'Pedidos',
				'subtitle' => 'Lista de pedidos',
				'totais' => $this->pedidoModel->totaisPorStatus(),
				'pedidos' => $this->pedidoModel->comCliente($status, $busca)->paginate(15),
				'pager' => $this->pedidoModel->pager,
				'status' => $status,
				'busca' => $busca
			];
			return view('Admin/Venda/pedidos', $data);
		}

		return redirect()->route('login');
	}

	public function show($id)
	{
		if (session()->isLoggedId) {
			$pedido = $this->pedidoModel->detalhe((int) $id);
			if ($pedido === null) {
				throw PageNotFoundException::forPageNotFound('Pedido não encontrado');
			}

			$data = [
				'title' => 'Pedidos',
				'subtitle' => sprintf('Pedido #%06d', $pedido['id']),
				'pedido' => $pedido,
				'itens' => (new ItemPedidoModel())->doPedido((int) $id)
			];
			return view('Admin/Venda/pedido_detalhe', $data);
		}

		return redirect()->route('login');
	}

	public function status($id)
	{
		if (session()->isLoggedId) {
			$pedidoLibrary = new PedidoLibrary();
			$id = (int) $id;

			$sucesso = match ($this->request->getPost('acao')) {
				'pago' => $pedidoLibrary->confirmarPagamento($id, 'MANUAL-' . date('YmdHis'), 'Confirmação manual'),
				'enviado' => $pedidoLibrary->marcarEnviado($id, trim((string) $this->request->getPost('codigo_rastreio'))),
				'cancelado' => $pedidoLibrary->cancelar($id, 'cancelado pelo painel'),
				default => false,
			};

			if ($sucesso) {
				session()->setFlashdata('success', 'Status do pedido atualizado');
			} else {
				session()->setFlashdata('error', 'Esta alteração não é permitida para o status atual do pedido');
			}
			return redirect()->route('pedido_show', [$id]);
		}

		return redirect()->route('login');
	}
}
