<?= $this->extend('Client/layout') ?>

<?= $this->section('content') ?>
<?php
$status = $pedido['status'];
$cancelado = $status === 'Cancelado';
$ordem = ['Pendente' => 0, 'Pago' => 1, 'Enviado' => 2];
$posicao = $ordem[$status] ?? -1;
// "proxima" é como a etapa aparece enquanto ainda é a próxima a acontecer
$etapas = [
    ['icone' => 'fas fa-receipt', 'rotulo' => 'Pedido recebido', 'proxima' => ['fas fa-receipt', 'Pedido recebido']],
    ['icone' => 'fas fa-check', 'rotulo' => 'Pagamento aprovado', 'proxima' => ['far fa-clock', 'Aguardando pagamento']],
    ['icone' => 'fas fa-truck', 'rotulo' => 'Enviado', 'proxima' => ['fas fa-box', 'Preparando envio']],
];
$subtotal = (float) $pedido['valor_total'] - (float) $pedido['frete_valor'];
?>
<div class="container py-4 py-lg-5" style="max-width: 980px;">
    <div class="text-center mb-4 mb-lg-5">
        <?php if ($status === 'Pendente'): ?>
            <div class="estado-vazio border-0 bg-transparent p-0">
                <div class="icone" style="background: var(--u-acento-suave); color: var(--u-acento);"><i class="far fa-clock"></i></div>
            </div>
            <h1 class="h2 mb-2">Pedido recebido!</h1>
            <p class="text-cinza mb-0">Assim que o pagamento for confirmado, começamos a separar seus produtos.</p>
        <?php elseif ($cancelado): ?>
            <div class="estado-vazio border-0 bg-transparent p-0">
                <div class="icone" style="background: #fde8e8; color: #a12020;"><i class="fas fa-times"></i></div>
            </div>
            <h1 class="h2 mb-2">Pedido cancelado</h1>
            <p class="text-cinza mb-0">Se você já havia pago, o valor será estornado pelo Mercado Pago.</p>
        <?php else: ?>
            <div class="estado-vazio border-0 bg-transparent p-0">
                <div class="icone" style="background: #e3f6ea; color: #136b39;"><i class="fas fa-check"></i></div>
            </div>
            <h1 class="h2 mb-2">Obrigado pela compra!</h1>
            <p class="text-cinza mb-0"><?= $status === 'Enviado' ? 'Seu pedido já está a caminho.' : 'Pagamento confirmado. Seu pedido está sendo preparado.' ?></p>
        <?php endif ?>
    </div>

    <div class="painel mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div>
                <div class="small text-cinza">Pedido</div>
                <div class="h4 mb-0">#<?= sprintf('%06d', $pedido['id']) ?></div>
            </div>
            <div class="text-sm-end">
                <span class="status-pill status-<?= status_pedido_cor($status) ?>"><?= esc($status) ?></span>
                <div class="small text-cinza mt-1">Realizado em <?= data_br($pedido['criado_em']) ?></div>
            </div>
        </div>

        <?php if (! $cancelado): ?>
            <div class="linha-tempo">
                <?php foreach ($etapas as $indice => $etapa): ?>
                    <?php $proxima = $indice === $posicao + 1; ?>
                    <?php [$icone, $rotulo] = $proxima ? $etapa['proxima'] : [$etapa['icone'], $etapa['rotulo']]; ?>
                    <div class="etapa <?= $indice <= $posicao ? 'concluida' : '' ?> <?= $proxima ? 'atual' : '' ?>">
                        <div class="marcador"><i class="<?= $icone ?>"></i></div>
                        <div class="rotulo"><?= $rotulo ?></div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($status === 'Pendente' && ! empty($pedido['link_pagamento'])): ?>
            <div class="text-center mt-4">
                <a href="<?= esc($pedido['link_pagamento'], 'attr') ?>" class="btn btn-acento btn-lg">
                    <i class="fas fa-lock me-2"></i>Concluir pagamento
                </a>
                <div class="small text-cinza mt-2">Total de <?= moeda($pedido['valor_total']) ?> via <?= esc($pedido['metodo_pagamento']) ?></div>
            </div>
        <?php endif ?>

        <?php if ($status === 'Enviado' && ! empty($pedido['codigo_rastreio'])): ?>
            <div class="alert alert-light border mt-4 mb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <span><i class="fas fa-truck me-2"></i>Código de rastreio: <strong><?= esc($pedido['codigo_rastreio']) ?></strong></span>
                <a href="https://rastreamento.correios.com.br/app/index.php" target="_blank" rel="noopener" class="btn btn-sm btn-preto">Rastrear</a>
            </div>
        <?php endif ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="painel h-100">
                <h2 class="h6 mb-3">Itens</h2>
                <div class="d-grid gap-3">
                    <?php foreach ($itens as $item): ?>
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= produto_imagem($item['imagem']) ?>" alt="" class="miniatura miniatura-sm">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small"><?= esc($item['nome'] ?? 'Produto removido') ?></div>
                                <div class="small text-cinza"><?= (int) $item['quantidade'] ?> × <?= moeda($item['preco_unitario']) ?></div>
                            </div>
                            <div class="fw-semibold small text-nowrap"><?= moeda($item['quantidade'] * $item['preco_unitario']) ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
                <hr>
                <div class="resumo-linha"><span>Subtotal</span><span><?= moeda($subtotal) ?></span></div>
                <div class="resumo-linha"><span>Frete <span class="small text-cinza">(<?= esc($pedido['frete_servico'] ?? '') ?>)</span></span><span><?= moeda($pedido['frete_valor']) ?></span></div>
                <div class="resumo-total"><span class="fw-semibold">Total</span><span class="valor"><?= moeda($pedido['valor_total']) ?></span></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="painel">
                <h2 class="h6 mb-3"><i class="fas fa-map-marker-alt me-2 text-acento"></i>Entrega</h2>
                <p class="mb-0 small">
                    <strong><?= esc($pedido['cliente_nome']) ?></strong><br>
                    <?= esc($pedido['endereco']) ?>, <?= esc($pedido['numero']) ?><?= $pedido['complemento'] ? ' - ' . esc($pedido['complemento']) : '' ?><br>
                    <?= esc($pedido['bairro']) ?> · <?= esc($pedido['cidade']) ?>/<?= esc($pedido['estado']) ?><br>
                    CEP <?= formata_cep($pedido['cep']) ?>
                </p>
            </div>
            <div class="painel">
                <h2 class="h6 mb-3"><i class="far fa-credit-card me-2 text-acento"></i>Pagamento</h2>
                <p class="small mb-0"><?= esc($pedido['metodo_pagamento']) ?></p>
            </div>
            <div class="painel">
                <h2 class="h6 mb-2"><i class="far fa-envelope me-2 text-acento"></i>Dúvidas?</h2>
                <p class="small text-cinza mb-0">Escreva para mochilasutopia@gmail.com informando o número do pedido.</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="<?= base_url(route_to('loja')) ?>" class="btn btn-contorno">Voltar para a loja</a>
    </div>
</div>
<?= $this->endSection() ?>
