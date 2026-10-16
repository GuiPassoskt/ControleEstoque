<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Credenciais do Mercado Pago (Checkout Pro).
 * Sobrescreva pelo .env: mercadopago.accessToken, mercadopago.webhookSecret
 */
class MercadoPago extends BaseConfig
{
	public string $accessToken = '';

	// Assinatura secreta do webhook (Suas integrações > Webhooks). Opcional.
	public string $webhookSecret = '';

	public string $apiUrl = 'https://api.mercadopago.com';

	public int $maxParcelas = 12;

	public function configurado(): bool
	{
		return $this->accessToken !== '';
	}
}
