<?php

namespace App\Models;

use App\Models\BaseModel;

class ItemPedidoModel extends BaseModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'pedido_itens';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_pedido',
		'id_produto',
		'quantidade',
		'preco_unitario'
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
	 * Itens do pedido com nome, SKU e imagem do produto (inclusive produtos já excluídos).
	 */
	public function doPedido(int $idPedido): array
	{
		return $this->select('pedido_itens.*, produtos.nome, produtos.sku, produtos.imagem')
			->join('produtos', 'produtos.id = pedido_itens.id_produto', 'left')
			->where('pedido_itens.id_pedido', $idPedido)
			->findAll();
	}
}
