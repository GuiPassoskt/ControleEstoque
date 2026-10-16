<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Cotação de frete pelo Melhor Envio.
 * Sobrescreva pelo .env: melhorenvio.token, melhorenvio.sandbox, melhorenvio.cepOrigem...
 */
class MelhorEnvio extends BaseConfig
{
	public string $token = '';

	public bool $sandbox = true;

	// CEP de onde as mercadorias são despachadas
	public string $cepOrigem = '01001000';

	// Exigido pelo Melhor Envio no User-Agent
	public string $email = 'contato@sualoja.com.br';

	// Usado quando não há token configurado
	public float $freteFixo = 25.00;

	public int $prazoFixo = 7;

	public function configurado(): bool
	{
		return $this->token !== '';
	}

	public function apiUrl(): string
	{
		return $this->sandbox
			? 'https://sandbox.melhorenvio.com.br/api/v2'
			: 'https://melhorenvio.com.br/api/v2';
	}
}
