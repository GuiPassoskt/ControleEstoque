<?php

namespace App\Controllers\Client;

use App\Models\ProdutoModel;

class HomeController extends LojaController {

    public function index() {
        $busca = trim((string) $this->request->getGet('busca'));
        $categoriaId = (int) $this->request->getGet('categoria') ?: null;
        $ordem = (string) ($this->request->getGet('ordem') ?: 'recentes');

        $produtoModel = new ProdutoModel();

        return $this->render('Client/home', [
            'title' => 'Mochilas e acessórios',
            'produtos' => $produtoModel->vitrine($busca, $categoriaId, $ordem)->paginate(12),
            'pager' => $produtoModel->pager,
            'busca' => $busca,
            'categoriaId' => $categoriaId,
            'ordem' => $ordem,
        ]);
    }
}
