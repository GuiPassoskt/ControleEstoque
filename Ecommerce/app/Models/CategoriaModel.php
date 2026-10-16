<?php

namespace App\Models;

use App\Entities\Categoria;
use App\Models\BaseModel;

class CategoriaModel extends BaseModel
{
	protected $DBGroup              = 'default';
	protected $table                = 'categorias';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = Categoria::class;
	protected $useSoftDeletes       = true;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'nome',
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
		'nome' => 'required|min_length[5]|max_length[255]'
	];
	protected $validationMessages   = [
		'nome' => [
			'required' => 'Informe o nome da categoria',
			'min_length' => 'O nome da categoria deve ter no mínimo 5 caracteres',
			'max_length' => 'O nome da categoria deve ter no máximo 255 caracteres'
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

	public function ativas(): array
	{
		return $this->where('ativo', 1)->orderBy('nome')->findAll();
	}
}
