<?php

namespace App\Libraries;

use App\Models\ClienteModel;
use App\Models\EnderecoModel;
use App\Models\ItemPedidoModel;
use App\Models\PedidoModel;
use App\Models\TransacaoModel;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;
use RuntimeException;

/**
 * Ciclo de vida do pedido: criação no checkout, confirmação do pagamento
 * (baixa de estoque + entrada no financeiro), envio e cancelamento (estorno).
 */
class PedidoLibrary
{
	private BaseConnection $db;
	private PedidoModel $pedidoModel;
	private ItemPedidoModel $itemPedidoModel;
	private TransacaoModel $transacaoModel;

	public function __construct()
	{
		$this->db = db_connect();
		$this->pedidoModel = new PedidoModel();
		$this->itemPedidoModel = new ItemPedidoModel();
		$this->transacaoModel = new TransacaoModel();
	}

	/**
	 * Cria cliente, endereço, pedido "Pendente" e itens. Retorna o id do pedido.
	 *
	 * @param list<array{produto: \App\Entities\Produto, quantidade: int, subtotal: float}> $itens
	 * @param array{id: string, nome: string, empresa: string, valor: float, prazo: int} $frete
	 */
	public function criarPedido(array $cliente, array $endereco, array $itens, array $frete, string $formaPagamento): int
	{
		if ($itens === []) {
			throw new RuntimeException('Seu carrinho está vazio.');
		}

		$subtotal = round(array_sum(array_column($itens, 'subtotal')), 2);

		try {
			$this->db->transException(true)->transStart();

			$idCliente = (new ClienteModel())->salvarPorEmail($cliente);
			$idEndereco = (new EnderecoModel())->salvarDoCliente($idCliente, $endereco);

			$idPedido = (int) $this->pedidoModel->insert([
				'id_cliente'       => $idCliente,
				'id_endereco'      => $idEndereco,
				'valor_total'      => round($subtotal + $frete['valor'], 2),
				'status'           => PedidoModel::STATUS_PENDENTE,
				'metodo_pagamento' => MercadoPagoLibrary::FORMAS_PAGAMENTO[$formaPagamento] ?? 'Mercado Pago',
				'frete_valor'      => $frete['valor'],
				'frete_codigo'     => $frete['id'],
				'frete_servico'    => trim($frete['empresa'] . ' ' . $frete['nome']),
			]);

			foreach ($itens as $item) {
				$this->itemPedidoModel->insert([
					'id_pedido'      => $idPedido,
					'id_produto'     => $item['produto']->id,
					'quantidade'     => $item['quantidade'],
					'preco_unitario' => $item['produto']->preco,
				]);
			}

			$this->db->transComplete();
		} catch (DatabaseException $e) {
			log_message('error', 'Falha ao criar pedido: {erro}', ['erro' => $e->getMessage()]);

			throw new RuntimeException('Não foi possível registrar o pedido. Tente novamente.');
		}

		return $idPedido;
	}

	/**
	 * Marca o pedido como pago, baixa o estoque e lança a entrada no financeiro.
	 * Idempotente: só age sobre pedidos "Pendente" (webhook e retorno podem chegar juntos).
	 */
	public function confirmarPagamento(int $idPedido, string $codigoTransacao, string $metodo): bool
	{
		try {
			$this->db->transException(true)->transStart();

			$this->db->table('pedidos')
				->set([
					'status'           => PedidoModel::STATUS_PAGO,
					'codigo_transacao' => $codigoTransacao,
					'metodo_pagamento' => $metodo,
					'atualizado_em'    => date('Y-m-d H:i:s'),
				])
				->where('id', $idPedido)
				->where('status', PedidoModel::STATUS_PENDENTE)
				->update();

			if ($this->db->affectedRows() !== 1) {
				$this->db->transComplete();

				return false;
			}

			$pedido = $this->pedidoModel->find($idPedido);
			$this->movimentarEstoque($idPedido, -1);

			$this->transacaoModel->insert([
				'id_pedido'      => $idPedido,
				'tipo'           => TransacaoModel::ENTRADA,
				'valor'          => $pedido['valor_total'],
				'descricao'      => sprintf('Pedido #%06d - pagamento via %s', $idPedido, $metodo),
				'data_movimento' => date('Y-m-d H:i:s'),
			]);

			$this->db->transComplete();
		} catch (DatabaseException $e) {
			log_message('error', 'Falha ao confirmar pagamento do pedido {id}: {erro}', ['id' => $idPedido, 'erro' => $e->getMessage()]);

			return false;
		}

		return true;
	}

	public function marcarEnviado(int $idPedido, ?string $codigoRastreio): bool
	{
		$this->db->table('pedidos')
			->set([
				'status'          => PedidoModel::STATUS_ENVIADO,
				'codigo_rastreio' => $codigoRastreio ?: null,
				'atualizado_em'   => date('Y-m-d H:i:s'),
			])
			->where('id', $idPedido)
			->where('status', PedidoModel::STATUS_PAGO)
			->update();

		return $this->db->affectedRows() === 1;
	}

	/**
	 * Cancela pedidos pendentes ou pagos. Se já estava pago, devolve o estoque
	 * e lança a saída do estorno no financeiro.
	 */
	public function cancelar(int $idPedido, string $motivo = 'Cancelamento'): bool
	{
		$pedido = $this->pedidoModel->find($idPedido);

		if (! $pedido || ! in_array($pedido['status'], [PedidoModel::STATUS_PENDENTE, PedidoModel::STATUS_PAGO], true)) {
			return false;
		}

		try {
			$this->db->transException(true)->transStart();

			$this->db->table('pedidos')
				->set(['status' => PedidoModel::STATUS_CANCELADO, 'atualizado_em' => date('Y-m-d H:i:s')])
				->where('id', $idPedido)
				->where('status', $pedido['status'])
				->update();

			if ($this->db->affectedRows() !== 1) {
				$this->db->transComplete();

				return false;
			}

			if ($pedido['status'] === PedidoModel::STATUS_PAGO) {
				$this->movimentarEstoque($idPedido, 1);

				$this->transacaoModel->insert([
					'id_pedido'      => $idPedido,
					'tipo'           => TransacaoModel::SAIDA,
					'valor'          => $pedido['valor_total'],
					'descricao'      => sprintf('Estorno do pedido #%06d - %s', $idPedido, $motivo),
					'data_movimento' => date('Y-m-d H:i:s'),
				]);
			}

			$this->db->transComplete();
		} catch (DatabaseException $e) {
			log_message('error', 'Falha ao cancelar pedido {id}: {erro}', ['id' => $idPedido, 'erro' => $e->getMessage()]);

			return false;
		}

		return true;
	}

	/**
	 * Aplica ao pedido o pagamento consultado na API do Mercado Pago.
	 */
	public function processarPagamentoMercadoPago(array $pagamento): void
	{
		$idPedido = (int) ($pagamento['external_reference'] ?? 0);
		$pedido = $idPedido ? $this->pedidoModel->find($idPedido) : null;

		if ($pedido === null) {
			log_message('warning', 'Pagamento {id} do Mercado Pago sem pedido correspondente', ['id' => $pagamento['id'] ?? '?']);

			return;
		}

		$codigo = (string) $pagamento['id'];

		switch ($pagamento['status'] ?? '') {
			case 'approved':
				if ((float) $pagamento['transaction_amount'] + 0.01 < (float) $pedido['valor_total']) {
					log_message('critical', 'Pagamento {codigo} com valor menor que o pedido {id}', ['codigo' => $codigo, 'id' => $idPedido]);

					return;
				}
				$this->confirmarPagamento($idPedido, $codigo, MercadoPagoLibrary::descreverMetodo($pagamento));
				break;

			case 'refunded':
			case 'charged_back':
				$this->cancelar($idPedido, 'pagamento devolvido no Mercado Pago');
				break;

			default:
				if ($pedido['status'] === PedidoModel::STATUS_PENDENTE) {
					$this->pedidoModel->update($idPedido, ['codigo_transacao' => $codigo]);
				}
		}
	}

	/**
	 * Soma (+1) ou subtrai (-1) do estoque as quantidades dos itens do pedido.
	 */
	private function movimentarEstoque(int $idPedido, int $sentido): void
	{
		$operador = $sentido > 0 ? '+' : '-';

		foreach ($this->itemPedidoModel->where('id_pedido', $idPedido)->findAll() as $item) {
			$this->db->table('produtos')
				->set('estoque', 'estoque ' . $operador . ' ' . (int) $item['quantidade'], false)
				->where('id', (int) $item['id_produto'])
				->update();
		}
	}
}
