<?php

namespace App\Controllers\Client;

use App\Libraries\CarrinhoLibrary;
use App\Libraries\MelhorEnvioLibrary;
use App\Libraries\MercadoPagoLibrary;
use App\Libraries\PedidoLibrary;
use App\Models\PedidoModel;
use RuntimeException;

class CheckoutController extends LojaController {

    private CarrinhoLibrary $carrinho;

    public function __construct() {
        $this->carrinho = new CarrinhoLibrary();
    }

    public function index() {
        $itens = $this->carrinho->itens();

        if ($itens === []) {
            session()->setFlashdata('error', 'Adicione produtos ao carrinho para finalizar a compra.');
            return redirect()->route('carrinho');
        }

        return $this->render('Client/checkout', [
            'title' => 'Finalizar compra',
            'itens' => $itens,
            'subtotal' => $this->carrinho->subtotal($itens),
            'pagamentoOnline' => (new MercadoPagoLibrary())->configurado(),
            'formasPagamento' => MercadoPagoLibrary::FORMAS_PAGAMENTO,
        ]);
    }

    /**
     * GET checkout/frete?cep=00000000 — cota o frete do carrinho e guarda a cotação na sessão.
     */
    public function frete() {
        $cep = apenas_digitos($this->request->getGet('cep'));

        if (strlen($cep) !== 8) {
            return $this->response->setStatusCode(422)->setJSON(['mensagem' => 'Informe um CEP válido.']);
        }

        $itens = $this->carrinho->itens();
        if ($itens === []) {
            return $this->response->setStatusCode(422)->setJSON(['mensagem' => 'Seu carrinho está vazio.']);
        }

        try {
            $opcoes = (new MelhorEnvioLibrary())->cotar($cep, $itens);
        } catch (RuntimeException $e) {
            return $this->response->setStatusCode(502)->setJSON(['mensagem' => $e->getMessage()]);
        }

        // A escolha do cliente é validada contra esta cotação ao finalizar
        session()->set('frete_cotacao', [
            'cep' => $cep,
            'opcoes' => array_column($opcoes, null, 'id'),
        ]);

        return $this->response->setJSON([
            'opcoes' => $opcoes,
            'subtotal' => $this->carrinho->subtotal($itens),
        ]);
    }

    public function finalizar() {
        $regras = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'email' => 'required|valid_email|max_length[255]',
            'telefone' => 'required|min_length[10]|max_length[11]',
            'cep' => 'required|exact_length[8]',
            'endereco' => 'required|max_length[255]',
            'numero' => 'required|is_natural|max_length[5]',
            'complemento' => 'permit_empty|max_length[80]',
            'bairro' => 'required|max_length[255]',
            'cidade' => 'required|max_length[255]',
            'estado' => 'required|alpha|exact_length[2]',
            'frete' => 'required',
            'pagamento' => 'required|in_list[' . implode(',', array_keys(MercadoPagoLibrary::FORMAS_PAGAMENTO)) . ']',
        ];
        $mensagens = [
            'nome' => ['required' => 'Informe seu nome completo.'],
            'email' => ['required' => 'Informe seu e-mail.', 'valid_email' => 'Informe um e-mail válido.'],
            'telefone' => ['required' => 'Informe seu telefone.', 'min_length' => 'Telefone inválido.', 'max_length' => 'Telefone inválido.'],
            'cep' => ['required' => 'Informe o CEP.', 'exact_length' => 'CEP inválido.'],
            'endereco' => ['required' => 'Informe o endereço.'],
            'numero' => ['required' => 'Informe o número.', 'is_natural' => 'Número inválido.'],
            'bairro' => ['required' => 'Informe o bairro.'],
            'cidade' => ['required' => 'Informe a cidade.'],
            'estado' => ['required' => 'Informe a UF.', 'exact_length' => 'UF inválida.', 'alpha' => 'UF inválida.'],
            'frete' => ['required' => 'Escolha uma opção de frete.'],
            'pagamento' => ['required' => 'Escolha a forma de pagamento.', 'in_list' => 'Forma de pagamento inválida.'],
        ];

        $post = $this->request->getPost();
        $post['cep'] = apenas_digitos($post['cep'] ?? '');
        $post['telefone'] = apenas_digitos($post['telefone'] ?? '');
        $post['estado'] = strtoupper(trim((string) ($post['estado'] ?? '')));

        if (! $this->validateData($post, $regras, $mensagens)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dados = $this->validator->getValidated();

        $cotacao = session('frete_cotacao');
        $frete = ($cotacao['cep'] ?? null) === $dados['cep'] ? ($cotacao['opcoes'][$dados['frete']] ?? null) : null;
        if ($frete === null) {
            return redirect()->back()->withInput()->with('errors', ['frete' => 'Recalcule o frete para o CEP informado.']);
        }

        $itens = $this->carrinho->itens();
        $pedidoLibrary = new PedidoLibrary();

        try {
            $idPedido = $pedidoLibrary->criarPedido(
                ['nome' => $dados['nome'], 'email' => strtolower($dados['email']), 'telefone' => $dados['telefone']],
                [
                    'cep' => $dados['cep'],
                    'endereco' => $dados['endereco'],
                    'numero' => $dados['numero'],
                    'complemento' => $dados['complemento'] ?? null,
                    'bairro' => $dados['bairro'],
                    'cidade' => $dados['cidade'],
                    'estado' => $dados['estado'],
                ],
                $itens,
                $frete,
                $dados['pagamento']
            );
        } catch (RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $this->carrinho->limpar();
        session()->remove('frete_cotacao');
        session()->set('meus_pedidos', array_merge($this->pedidosDaSessao(), [$idPedido]));

        $mercadoPago = new MercadoPagoLibrary();
        if (! $mercadoPago->configurado()) {
            session()->setFlashdata('success', 'Pedido recebido! Entraremos em contato para combinar o pagamento.');
            return redirect()->route('pedido_cliente', [$idPedido]);
        }

        $pedidoModel = new PedidoModel();
        try {
            $pagamento = $mercadoPago->gerarPagamento(
                $pedidoModel->find($idPedido),
                $itens,
                ['nome' => $dados['nome'], 'email' => $dados['email']],
                $dados['pagamento']
            );
        } catch (RuntimeException $e) {
            session()->setFlashdata('error', 'Seu pedido foi registrado, mas não conseguimos abrir o pagamento agora. Tente novamente por esta página.');
            return redirect()->route('pedido_cliente', [$idPedido]);
        }

        $pedidoModel->update($idPedido, ['link_pagamento' => $pagamento['init_point']]);

        return redirect()->to($pagamento['init_point']);
    }

    /**
     * Volta do Checkout Pro (back_urls). O status é sempre reconsultado na API.
     */
    public function retorno() {
        $idPedido = (int) $this->request->getGet('external_reference');
        $idPagamento = (string) ($this->request->getGet('payment_id') ?? $this->request->getGet('collection_id') ?? '');

        if (! in_array($idPedido, $this->pedidosDaSessao(), true)) {
            return redirect()->route('loja');
        }

        $mercadoPago = new MercadoPagoLibrary();
        $pagamento = ($idPagamento !== '' && $mercadoPago->configurado()) ? $mercadoPago->buscarPagamento($idPagamento) : null;

        if ($pagamento !== null && (int) ($pagamento['external_reference'] ?? 0) === $idPedido) {
            (new PedidoLibrary())->processarPagamentoMercadoPago($pagamento);
        }

        $mensagens = [
            'approved' => ['success', 'Pagamento aprovado! Obrigado pela compra.'],
            'pending' => ['success', 'Pagamento em processamento. Avisaremos assim que for confirmado.'],
            'in_process' => ['success', 'Pagamento em análise. Avisaremos assim que for confirmado.'],
        ];
        [$tipo, $mensagem] = $mensagens[$pagamento['status'] ?? $this->request->getGet('status')]
            ?? ['error', 'O pagamento não foi concluído. Você pode tentar novamente por esta página.'];

        session()->setFlashdata($tipo, $mensagem);
        return redirect()->route('pedido_cliente', [$idPedido]);
    }
}
