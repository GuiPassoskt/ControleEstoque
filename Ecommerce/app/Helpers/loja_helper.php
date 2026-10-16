<?php

/**
 * Funções de apresentação compartilhadas entre a loja e o painel.
 */

if (! function_exists('moeda')) {
	function moeda($valor): string
	{
		return 'R$ ' . number_format((float) $valor, 2, ',', '.');
	}
}

if (! function_exists('data_br')) {
	function data_br($data, bool $comHora = true): string
	{
		if (empty($data)) {
			return '-';
		}

		$timestamp = $data instanceof DateTimeInterface ? $data->getTimestamp() : strtotime((string) $data);

		return date($comHora ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
	}
}

if (! function_exists('produto_imagem')) {
	function produto_imagem(?string $imagem): string
	{
		return $imagem
			? base_url('uploads/produtos/' . $imagem)
			: base_url('assets/loja/img/sem-imagem.svg');
	}
}

if (! function_exists('status_pedido_cor')) {
	/**
	 * Cor do Bootstrap para o status do pedido.
	 */
	function status_pedido_cor(string $status): string
	{
		return [
			'Pendente'  => 'warning',
			'Pago'      => 'success',
			'Enviado'   => 'info',
			'Cancelado' => 'danger',
		][$status] ?? 'secondary';
	}
}

if (! function_exists('apenas_digitos')) {
	function apenas_digitos(?string $valor): string
	{
		return preg_replace('/\D/', '', (string) $valor);
	}
}

if (! function_exists('formata_cep')) {
	function formata_cep(?string $cep): string
	{
		$cep = apenas_digitos($cep);

		return strlen($cep) === 8 ? substr($cep, 0, 5) . '-' . substr($cep, 5) : $cep;
	}
}
