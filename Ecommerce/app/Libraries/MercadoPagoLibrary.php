<?php

namespace App\Libraries;

use Config\MercadoPago;
use RuntimeException;

/**
 * Integração com o Mercado Pago Checkout Pro pela API REST.
 * https://www.mercadopago.com.br/developers/pt/reference
 */
class MercadoPagoLibrary
{
	public const FORMAS_PAGAMENTO = [
		'pix'    => 'Pix',
		'cartao' => 'Cartão',
	];

	private MercadoPago $config;

	public function __construct()
	{
		$this->config = config(MercadoPago::class);
	}

	public function configurado(): bool
	{
		return $this->config->configurado();
	}

	/**
	 * Cria a preferência de pagamento e devolve o id e a URL do checkout.
	 *
	 * @return array{id: string, init_point: string}
	 */
	public function gerarPagamento(array $pedido, array $itens, array $cliente, string $formaPagamento): array
	{
		$itensPreferencia = [];
		foreach ($itens as $item) {
			$itensPreferencia[] = [
				'id'          => $item['produto']->sku,
				'title'       => $item['produto']->nome,
				'quantity'    => $item['quantidade'],
				'unit_price'  => $item['produto']->preco,
				'currency_id' => 'BRL',
			];
		}

		if ((float) $pedido['frete_valor'] > 0) {
			$itensPreferencia[] = [
				'id'          => 'frete',
				'title'       => 'Frete - ' . $pedido['frete_servico'],
				'quantity'    => 1,
				'unit_price'  => (float) $pedido['frete_valor'],
				'currency_id' => 'BRL',
			];
		}

		$retorno = base_url(route_to('checkout_retorno'));
		$preferencia = [
			'items'              => $itensPreferencia,
			'payer'              => [
				'name'  => $cliente['nome'],
				'email' => $cliente['email'],
			],
			'external_reference' => (string) $pedido['id'],
			'back_urls'          => [
				'success' => $retorno,
				'pending' => $retorno,
				'failure' => $retorno,
			],
			'payment_methods'    => [
				'excluded_payment_types' => $this->tiposExcluidos($formaPagamento),
				'installments'           => $this->config->maxParcelas,
			],
		];

		// O Mercado Pago só aceita retorno automático e webhook em URLs públicas (https)
		if (str_starts_with($retorno, 'https://')) {
			$preferencia['auto_return'] = 'approved';
			$preferencia['notification_url'] = base_url(route_to('notificacao_pagamento')) . '?source_news=webhooks';
		}

		$resposta = $this->requisitar('post', '/checkout/preferences', $preferencia);

		if (! isset($resposta['id'], $resposta['init_point'])) {
			throw new RuntimeException('Não foi possível iniciar o pagamento no Mercado Pago.');
		}

		return ['id' => $resposta['id'], 'init_point' => $resposta['init_point']];
	}

	public function buscarPagamento(string $idPagamento): ?array
	{
		if (! ctype_digit($idPagamento)) {
			return null;
		}

		try {
			return $this->requisitar('get', '/v1/payments/' . $idPagamento);
		} catch (RuntimeException) {
			return null;
		}
	}

	/**
	 * Valida o cabeçalho x-signature do webhook. Sem segredo configurado, aceita
	 * (o pagamento é sempre reconsultado na API antes de qualquer alteração).
	 */
	public function assinaturaValida(string $assinatura, string $idRequisicao, string $idDado): bool
	{
		if ($this->config->webhookSecret === '') {
			return true;
		}

		$partes = [];
		foreach (explode(',', $assinatura) as $parte) {
			[$chave, $valor] = array_map('trim', explode('=', $parte, 2) + [1 => '']);
			$partes[$chave] = $valor;
		}

		if (empty($partes['ts']) || empty($partes['v1'])) {
			return false;
		}

		$manifesto = 'id:' . strtolower($idDado) . ';request-id:' . $idRequisicao . ';ts:' . $partes['ts'] . ';';

		return hash_equals(hash_hmac('sha256', $manifesto, $this->config->webhookSecret), $partes['v1']);
	}

	/**
	 * Nome amigável do meio de pagamento devolvido pelo Mercado Pago.
	 */
	public static function descreverMetodo(array $pagamento): string
	{
		if (($pagamento['payment_method_id'] ?? '') === 'pix') {
			return 'Pix';
		}

		return [
			'credit_card'   => 'Cartão de crédito',
			'debit_card'    => 'Cartão de débito',
			'ticket'        => 'Boleto',
			'bank_transfer' => 'Pix',
			'account_money' => 'Saldo Mercado Pago',
		][$pagamento['payment_type_id'] ?? ''] ?? 'Mercado Pago';
	}

	private function tiposExcluidos(string $formaPagamento): array
	{
		$excluidos = $formaPagamento === 'pix'
			? ['credit_card', 'debit_card', 'ticket']
			: ['bank_transfer', 'ticket'];

		return array_map(static fn ($tipo) => ['id' => $tipo], $excluidos);
	}

	private function requisitar(string $metodo, string $caminho, ?array $corpo = null): array
	{
		if (! $this->configurado()) {
			throw new RuntimeException('Mercado Pago não configurado.');
		}

		$opcoes = [
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $this->config->accessToken,
			],
		];
		if ($corpo !== null) {
			$opcoes['json'] = $corpo;
		}

		$cliente = single_service('curlrequest', ['timeout' => 20, 'http_errors' => false]);
		$resposta = $cliente->request($metodo, $this->config->apiUrl . $caminho, $opcoes);

		if ($resposta->getStatusCode() >= 300) {
			log_message('error', 'Mercado Pago {caminho} respondeu {status}: {corpo}', [
				'caminho' => $caminho,
				'status'  => $resposta->getStatusCode(),
				'corpo'   => $resposta->getBody(),
			]);

			throw new RuntimeException('Falha na comunicação com o Mercado Pago.');
		}

		return json_decode($resposta->getBody(), true) ?? [];
	}
}
