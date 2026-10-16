<?php

namespace App\Controllers\Client;

use App\Models\ProdutoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProdutoController extends LojaController {

    public function show($id) {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->vitrine()->where('produtos.id', (int) $id)->first();

        if ($produto === null) {
            throw PageNotFoundException::forPageNotFound('Produto não encontrado');
        }

        $relacionados = (new ProdutoModel())->vitrine(null, $produto->categoria_id)
            ->where('produtos.id !=', $produto->id)
            ->findAll(4);

        return $this->render('Client/produto', [
            'title' => $produto->nome,
            'produto' => $produto,
            'relacionados' => $relacionados,
        ]);
    }
}
