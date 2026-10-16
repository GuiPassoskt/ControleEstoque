<?php

namespace App\Libraries;

use Config\MelhorEnvio;
use RuntimeException;

/**
 * Cotação de frete na API do Melhor Envio.
 * https://docs.melhorenvio.com.br/reference/calculo-de-fretes-por-produtos
 */
class MelhorEnvioLibrary
{
	private MelhorEnvio $config;

	public function __construct()
	{
		$this->config = config(MelhorEnvio::class);
	}

	/**
	 * @param list<array{produto: \App\Entities\Produto, quantidade: int}> $itens
	 *
	 * @return list<array{id: string, nome: string, empresa: string, valor: float, prazo: int}>
	 */
	public function cotar(string $cepDestino, array $itens): array
	{
		if (! $this->config->configurado()) {
			return [[
				'id'      => 'fixo',
				'nome'    => 'Entrega padrão',
				'empresa' => 'Transportadora',
				'valor'   => $this->config->freteFixo,
				'prazo'   => $this->config->prazoFixo,
			]];
		}

		$produtos = [];
		foreach ($itens as $item) {
			$produto = $item['produto'];
			$produtos[] = [
				'id'              => (string) $produto->id,
				'width'           => $produto->largura,
				'height'          => $produto->altura,
				'length'          => $produto->comprimento,
				'weight'          => $produto->peso,
				'insurance_value' => $produto->preco,
				'quantity'        => $item['quantidade'],
			];
		}

		$cliente = single_service('curlrequest', ['timeout' => 15, 'http_errors' => false]);
		$resposta = $cliente->post($this->config->apiUrl() . '/me/shipment/calculate', [
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $this->config->token,
				'User-Agent'    => 'Loja (' . $this->config->email . ')',
			],
			'json'    => [
				'from'     => ['postal_code' => $this->config->cepOrigem],
				'to'       => ['postal_code' => $cepDestino],
				'products' => $produtos,
			],
		]);

		if ($resposta->getStatusCode() !== 200) {
			log_message('error', 'Melhor Envio respondeu {status}: {corpo}', [
				'status' => $resposta->getStatusCode(),
				'corpo'  => $resposta->getBody(),
			]);

			throw new RuntimeException('Não foi possível calcular o frete agora. Tente novamente em instantes.');
		}

		$opcoes = [];
		foreach (json_decode($resposta->getBody(), true) ?? [] as $servico) {
			// Serviços que não atendem o CEP vêm com "error"
			if (! empty($servico['error']) || ! isset($servico['price'])) {
				continue;
			}

			$opcoes[] = [
				'id'      => (string) $servico['id'],
				'nome'    => $servico['name'],
				'empresa' => $servico['company']['name'] ?? '',
				'valor'   => (float) ($servico['custom_price'] ?? $servico['price']),
				'prazo'   => (int) ($servico['custom_delivery_time'] ?? $servico['delivery_time'] ?? 0),
			];
		}

		usort($opcoes, static fn ($a, $b) => $a['valor'] <=> $b['valor']);

		return $opcoes;
	}
}
