<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Libraries\CarrinhoLibrary;
use App\Models\CategoriaModel;

/**
 * Base das telas da loja: injeta no layout o contador do carrinho e o menu de categorias.
 */
abstract class LojaController extends BaseController
{
	protected function render(string $view, array $data = []): string
	{
		$data['carrinhoQuantidade'] = (new CarrinhoLibrary())->quantidade();
		$data['menuCategorias'] = (new CategoriaModel())->ativas();

		return view($view, $data);
	}

	/**
	 * Pedidos feitos nesta sessão — só eles podem ser consultados pelo cliente.
	 */
	protected function pedidosDaSessao(): array
	{
		return session('meus_pedidos') ?? [];
	}
}
