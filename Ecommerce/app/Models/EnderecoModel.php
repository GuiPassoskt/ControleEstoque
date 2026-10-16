<?php

namespace App\Models;

use App\Models\BaseModel;

class EnderecoModel extends BaseModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'enderecos';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'cep',
		'endereco',
		'numero',
		'complemento',
		'bairro',
		'cidade',
		'estado',
		'referencia',
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
	 * Cadastra o endereço e o vincula ao cliente. Retorna o id do endereço.
	 */
	public function salvarDoCliente(int $idCliente, array $dados): int
	{
		$idEndereco = (int) $this->insert($dados);

		// A coluna foi criada como "endreco_id" na migration original
		$this->db->table('cliente_enderecos')->insert([
			'cliente_id'    => $idCliente,
			'endreco_id'    => $idEndereco,
			'criado_em'     => date('Y-m-d H:i:s'),
			'atualizado_em' => date('Y-m-d H:i:s'),
		]);

		return $idEndereco;
	}
}
