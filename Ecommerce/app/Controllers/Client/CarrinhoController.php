<?php

namespace App\Controllers\Client;

use App\Libraries\CarrinhoLibrary;

class CarrinhoController extends LojaController {

    private CarrinhoLibrary $carrinho;

    public function __construct() {
        $this->carrinho = new CarrinhoLibrary();
    }

    public function index() {
        $itens = $this->carrinho->itens();

        return $this->render('Client/carrinho', [
            'title' => 'Carrinho',
            'itens' => $itens,
            'subtotal' => $this->carrinho->subtotal($itens),
        ]);
    }

    public function adicionar() {
        $resultado = $this->carrinho->adicionar(
            (int) $this->request->getPost('id_produto'),
            (int) ($this->request->getPost('quantidade') ?: 1)
        );

        session()->setFlashdata($resultado['sucesso'] ? 'success' : 'error', $resultado['mensagem']);

        if ($resultado['sucesso'] && $this->request->getPost('comprar_agora')) {
            return redirect()->route('checkout');
        }

        return redirect()->back();
    }

    public function atualizar() {
        foreach ((array) $this->request->getPost('quantidades') as $idProduto => $quantidade) {
            $this->carrinho->atualizar((int) $idProduto, (int) $quantidade);
        }

        return redirect()->route('carrinho');
    }

    public function remover($idProduto) {
        $this->carrinho->remover((int) $idProduto);
        session()->setFlashdata('success', 'Item removido do carrinho.');

        return redirect()->route('carrinho');
    }
}
