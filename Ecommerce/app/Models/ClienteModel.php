<?php

namespace App\Models;

use App\Models\BaseModel;

class ClienteModel extends BaseModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'clientes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'nome',
		'email',
		'telefone',
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
	 * Localiza o cliente pelo e-mail ou cadastra; atualiza nome e telefone. Retorna o id.
	 */
	public function salvarPorEmail(array $dados): int
	{
		$cliente = $this->where('email', $dados['email'])->first();

		if ($cliente) {
			$this->update($cliente['id'], [
				'nome'     => $dados['nome'],
				'telefone' => $dados['telefone'],
			]);

			return (int) $cliente['id'];
		}

		return (int) $this->insert($dados);
	}
}
