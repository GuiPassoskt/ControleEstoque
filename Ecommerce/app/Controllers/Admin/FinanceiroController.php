<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransacaoModel;

class FinanceiroController extends BaseController
{
	private $transacaoModel;

	public function __construct()
	{
		$this->transacaoModel = new TransacaoModel();
	}

	public function index()
	{
		if (session()->isLoggedId) {
			[$inicio, $fim] = $this->periodo();
			$tipo = (string) $this->request->getGet('tipo');

			$data = [
				'title' => 'Financeiro',
				'subtitle' => 'Lançamentos',
				'resumo' => $this->transacaoModel->resumo($inicio, $fim),
				'lancamentos' => $this->transacaoModel->doPeriodo($inicio, $fim, $tipo)->paginate(15),
				'pager' => $this->transacaoModel->pager,
				'inicio' => $inicio,
				'fim' => $fim,
				'tipo' => $tipo
			];
			return view('Admin/Financeiro/lancamentos', $data);
		}

		return redirect()->route('login');
	}

	public function create()
	{
		if (session()->isLoggedId) {
			$data = [
				'title' => 'Financeiro',
				'subtitle' => 'Novo lançamento'
			];
			return view('Admin/Financeiro/lancamento_form', $data);
		}

		return redirect()->route('login');
	}

	public function store()
	{
		if (session()->isLoggedId) {
			$post = $this->request->getPost(['tipo', 'valor', 'descricao', 'data_movimento']);
			$valor = trim((string) $post['valor']);
			if (str_contains($valor, ',')) {
				$valor = str_replace(['.', ','], ['', '.'], $valor);
			}

			$dados = [
				'tipo' => $post['tipo'],
				'valor' => $valor,
				'descricao' => trim((string) $post['descricao']),
				'data_movimento' => $post['data_movimento'] ? date('Y-m-d H:i:s', strtotime($post['data_movimento'])) : ''
			];

			if ($this->transacaoModel->insert($dados) === false) {
				return redirect()->back()->withInput()->with('errors', $this->transacaoModel->errors());
			}

			session()->setFlashdata('success', 'Lançamento registrado com sucesso');
			return redirect()->route('financeiro');
		}

		return redirect()->route('login');
	}

	public function delete($id)
	{
		if (session()->isLoggedId) {
			$lancamento = $this->transacaoModel->find($id);

			// Lançamentos gerados por pedidos só mudam pelo próprio pedido
			if ($lancamento === null || $lancamento['id_pedido'] !== null) {
				return $this->response->setStatusCode(422)->setJSON(['sucesso' => false]);
			}

			return $this->response->setJSON(['sucesso' => (bool) $this->transacaoModel->delete($id)]);
		}
		return $this->response->setStatusCode(401)->setJSON(['sucesso' => false]);
	}

	public function fluxoCaixa()
	{
		if (session()->isLoggedId) {
			$ano = (int) ($this->request->getGet('ano') ?: date('Y'));

			$data = [
				'title' => 'Financeiro',
				'subtitle' => 'Fluxo de caixa',
				'ano' => $ano,
				'meses' => $this->transacaoModel->fluxoMensal($ano)
			];
			return view('Admin/Financeiro/fluxo_caixa', $data);
		}

		return redirect()->route('login');
	}

	/**
	 * Período do filtro (Y-m-d); padrão: mês atual.
	 */
	private function periodo(): array
	{
		$valido = static fn ($data) => is_string($data) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data);

		$inicio = $this->request->getGet('inicio');
		$fim = $this->request->getGet('fim');

		return [
			$valido($inicio) ? $inicio : date('Y-m-01'),
			$valido($fim) ? $fim : date('Y-m-t'),
		];
	}
}
