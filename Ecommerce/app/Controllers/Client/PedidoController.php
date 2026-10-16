<?php

namespace App\Controllers\Client;

use App\Models\ItemPedidoModel;
use App\Models\PedidoModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PedidoController extends LojaController {

    public function show($id) {
        $id = (int) $id;

        // Sem login de cliente: só quem fez o pedido nesta sessão pode vê-lo
        $pedido = in_array($id, $this->pedidosDaSessao(), true) ? (new PedidoModel())->detalhe($id) : null;

        if ($pedido === null) {
            throw PageNotFoundException::forPageNotFound('Pedido não encontrado');
        }

        return $this->render('Client/pedido', [
            'title' => sprintf('Pedido #%06d', $id),
            'pedido' => $pedido,
            'itens' => (new ItemPedidoModel())->doPedido($id),
        ]);
    }
}
