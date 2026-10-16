<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Entities\Usuario;

class UsuarioModel extends BaseModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'usuarios';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Usuario::class;
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'nome',
		'email',
		'password',
		'ativo'
	];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'criado_em';
	protected $updatedField         = 'atualizado_em';
	protected $deletedField         = 'excluido_em';

	// Validation
	protected $validationRules      = [
		'email' => 'required'
	];
	protected $validationMessages   = [
		'email' => [
			'required' => 'Informe o email'
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

	public function getByEmail(string $email) {
		return $this->where('email', $email)->first();
	}
}
