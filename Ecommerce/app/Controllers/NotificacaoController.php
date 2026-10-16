<?php

namespace App\Controllers;

use App\Libraries\MercadoPagoLibrary;
use App\Libraries\PedidoLibrary;

/**
 * Webhook do Mercado Pago (POST /notificacoes/pagamento).
 * Aceita o formato Webhooks ({"type":"payment","data":{"id":"..."}}) e o IPN (?topic=payment&id=...).
 */
class NotificacaoController extends BaseController {

    public function pagamento() {
        $corpo = $this->request->getJSON(true) ?? [];

        $tipo = $corpo['type'] ?? $this->request->getGet('type') ?? $this->request->getGet('topic');
        // O PHP converte "data.id" da query string em "data_id"
        $idPagamento = (string) ($this->request->getGet('data_id') ?? $corpo['data']['id'] ?? $this->request->getGet('id') ?? '');

        if ($tipo !== 'payment' || $idPagamento === '') {
            return $this->response->setJSON(['recebido' => true]);
        }

        $mercadoPago = new MercadoPagoLibrary();

        $assinaturaOk = $mercadoPago->assinaturaValida(
            $this->request->getHeaderLine('x-signature'),
            $this->request->getHeaderLine('x-request-id'),
            $idPagamento
        );
        if (! $assinaturaOk) {
            log_message('warning', 'Webhook do Mercado Pago com assinatura inválida para o pagamento {id}', ['id' => $idPagamento]);
            return $this->response->setStatusCode(401)->setJSON(['recebido' => false]);
        }

        $pagamento = $mercadoPago->buscarPagamento($idPagamento);
        if ($pagamento === null) {
            // 500 faz o Mercado Pago reenviar a notificação mais tarde
            return $this->response->setStatusCode(500)->setJSON(['recebido' => false]);
        }

        (new PedidoLibrary())->processarPagamentoMercadoPago($pagamento);

        return $this->response->setJSON(['recebido' => true]);
    }
}
