<?php

namespace App\Models;

use App\Models\BaseModel;

class PedidoModel extends BaseModel
{
	public const STATUS_PENDENTE  = 'Pendente';
	public const STATUS_PAGO      = 'Pago';
	public const STATUS_ENVIADO   = 'Enviado';
	public const STATUS_CANCELADO = 'Cancelado';

	public const STATUS = [
		self::STATUS_PENDENTE,
		self::STATUS_PAGO,
		self::STATUS_ENVIADO,
		self::STATUS_CANCELADO,
	];

	protected $DBGroup              = 'default';
	protected $table                = 'pedidos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_cliente',
		'id_endereco',
		'valor_total',
		'status',
		'metodo_pagamento',
		'codigo_transacao',
		'link_pagamento',
		'frete_valor',
		'frete_codigo',
		'frete_servico',
		'codigo_rastreio'
	];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'criado_em';
	protected $updatedField         = 'atualizado_em';
	protected $deletedField         = 'excluido_em';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	/**
	 * Pedidos com os dados do cliente, filtrados por status e busca.
	 */
	public function comCliente(?string $status = null, ?string $busca = null): self
	{
		$this->select('pedidos.*, clientes.nome AS cliente_nome, clientes.email AS cliente_email, clientes.telefone AS cliente_telefone')
			->join('clientes', 'clientes.id = pedidos.id_cliente', 'left');

		if ($status && in_array($status, self::STATUS, true)) {
			$this->where('pedidos.status', $status);
		}

		if ($busca) {
			$this->groupStart()
				->like('clientes.nome', $busca)
				->orLike('clientes.email', $busca)
				->orWhere('pedidos.id', (int) $busca)
				->groupEnd();
		}

		return $this->orderBy('pedidos.id', 'DESC');
	}

	/**
	 * Pedido com cliente e endereço de entrega.
	 */
	public function detalhe(int $id): ?array
	{
		return $this->comCliente()
			->select('enderecos.cep, enderecos.endereco, enderecos.numero, enderecos.complemento, enderecos.bairro, enderecos.cidade, enderecos.estado, enderecos.referencia')
			->join('enderecos', 'enderecos.id = pedidos.id_endereco', 'left')
			->where('pedidos.id', $id)
			->first();
	}

	/**
	 * Quantidade de pedidos por status: ['Pendente' => 3, ...]
	 */
	public function totaisPorStatus(): array
	{
		$linhas = $this->builder()
			->select('status, COUNT(*) AS total')
			->groupBy('status')
			->get()
			->getResultArray();

		$totais = array_fill_keys(self::STATUS, 0);
		foreach ($linhas as $linha) {
			$totais[$linha['status']] = (int) $linha['total'];
		}

		return $totais;
	}
}
