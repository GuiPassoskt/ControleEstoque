<?php

namespace App\Models;

use App\Entities\Produto;
use App\Models\BaseModel;

class ProdutoModel extends BaseModel
{
	public const ESTOQUE_BAIXO = 5;

	protected $DBGroup              = 'default';
	protected $table                = 'produtos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Produto::class;
	protected $useSoftDeletes       = true;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'nome',
		'descricao',
		'preco',
		'estoque',
		'peso',
		'altura',
		'largura',
		'comprimento',
		'sku',
		'imagem',
		'ativo',
		'categoria_id'
	];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'criado_em';
	protected $updatedField         = 'atualizado_em';
	protected $deletedField         = 'excluido_em';

	// Validation
	protected $validationRules      = [
		'id'           => 'permit_empty|is_natural_no_zero',
		'nome'         => 'required|min_length[3]|max_length[255]',
		'sku'          => 'required|max_length[50]|is_unique[produtos.sku,id,{id}]',
		'preco'        => 'required|decimal|greater_than[0]',
		'estoque'      => 'required|is_natural',
		'categoria_id' => 'required|is_natural_no_zero',
		'peso'         => 'required|decimal|greater_than[0]',
		'altura'       => 'required|is_natural_no_zero',
		'largura'      => 'required|is_natural_no_zero',
		'comprimento'  => 'required|is_natural_no_zero',
	];
	protected $validationMessages   = [
		'nome' => [
			'required'   => 'Informe o nome do produto',
			'min_length' => 'O nome do produto deve ter no mínimo 3 caracteres',
			'max_length' => 'O nome do produto deve ter no máximo 255 caracteres'
		],
		'sku' => [
			'required'  => 'Informe o SKU',
			'is_unique' => 'Já existe um produto com este SKU'
		],
		'preco' => [
			'required'     => 'Informe o preço',
			'decimal'      => 'Preço inválido',
			'greater_than' => 'O preço deve ser maior que zero'
		],
		'estoque' => [
			'required'   => 'Informe o estoque',
			'is_natural' => 'O estoque deve ser um número inteiro positivo'
		],
		'categoria_id' => [
			'required'           => 'Selecione a categoria',
			'is_natural_no_zero' => 'Selecione a categoria'
		],
		'peso' => [
			'greater_than' => 'O peso deve ser maior que zero'
		]
	];
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
	 * Produtos com o nome da categoria, filtrados por busca/categoria.
	 */
	public function comCategoria(?string $busca = null, ?int $categoriaId = null): self
	{
		$this->select('produtos.*, categorias.nome AS categoria')
			->join('categorias', 'categorias.id = produtos.categoria_id', 'left');

		if ($busca) {
			$this->groupStart()
				->like('produtos.nome', $busca)
				->orLike('produtos.sku', $busca)
				->groupEnd();
		}

		if ($categoriaId) {
			$this->where('produtos.categoria_id', $categoriaId);
		}

		return $this;
	}

	/**
	 * Vitrine: só produtos ativos de categorias ativas.
	 */
	public function vitrine(?string $busca = null, ?int $categoriaId = null, string $ordem = 'recentes'): self
	{
		$this->comCategoria($busca, $categoriaId)
			->where('produtos.ativo', 1)
			->where('categorias.ativo', 1)
			->where('categorias.excluido_em', null);

		$ordens = [
			'menor_preco' => ['produtos.preco', 'ASC'],
			'maior_preco' => ['produtos.preco', 'DESC'],
			'nome'        => ['produtos.nome', 'ASC'],
			'recentes'    => ['produtos.id', 'DESC'],
		];
		[$campo, $direcao] = $ordens[$ordem] ?? $ordens['recentes'];

		// Esgotados vão para o fim da lista
		return $this->orderBy('produtos.estoque > 0', 'DESC', false)->orderBy($campo, $direcao);
	}

	public function estoqueBaixo(int $limite = 5): array
	{
		return $this->where('ativo', 1)
			->where('estoque <=', self::ESTOQUE_BAIXO)
			->orderBy('estoque', 'ASC')
			->findAll($limite);
	}

	/**
	 * Produtos ativos indexados pelo id, para montar carrinho e pedido.
	 */
	public function ativosPorIds(array $ids): array
	{
		if ($ids === []) {
			return [];
		}

		$indexados = [];
		foreach ($this->whereIn('id', $ids)->where('ativo', 1)->findAll() as $produto) {
			$indexados[$produto->id] = $produto;
		}

		return $indexados;
	}
}
