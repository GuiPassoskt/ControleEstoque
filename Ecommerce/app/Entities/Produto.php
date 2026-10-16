<?php

namespace App\Entities;

use App\Models\ProdutoModel;
use CodeIgniter\Entity\Entity;

class Produto extends Entity
{
    protected $datamap = [];
    protected $dates   = ['criado_em', 'atualizado_em', 'excluido_em'];
    protected $casts   = [
        'id'           => 'integer',
        'preco'        => 'float',
        'estoque'      => 'integer',
        'peso'         => 'float',
        'altura'       => 'integer',
        'largura'      => 'integer',
        'comprimento'  => 'integer',
        'categoria_id' => 'integer',
        'ativo'        => 'boolean',
    ];

    public function esgotado(): bool
    {
        return $this->estoque <= 0;
    }

    public function estoqueBaixo(): bool
    {
        return ! $this->esgotado() && $this->estoque <= ProdutoModel::ESTOQUE_BAIXO;
    }
}
