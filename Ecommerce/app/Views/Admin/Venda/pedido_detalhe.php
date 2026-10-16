<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<?php
$status = $pedido['status'];
$subtotal = (float) $pedido['valor_total'] - (float) $pedido['frete_valor'];
$passos = [
    'Pendente' => 'Pedido recebido',
    'Pago' => 'Pagamento confirmado',
    'Enviado' => 'Enviado ao cliente',
];
$posicao = array_search($status, array_keys($passos), true);
$urlStatus = base_url(route_to('pedido_status', $pedido['id']));
?>
<section class="content">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center mb-3">
            <a href="<?php echo base_url(route_to('pedidos')) ?>" class="btn btn-sm btn-default mr-3"><i class="fas fa-arrow-left mr-1"></i>Pedidos</a>
            <span class="badge badge-<?php echo status_pedido_cor($status) ?> badge-status" style="font-size: .9rem;"><?php echo esc($status) ?></span>
            <span class="text-muted ml-3">Criado em <?php echo data_br($pedido['criado_em']) ?></span>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-box mr-2"></i>Itens do pedido</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Qtd.</th>
                                        <th class="text-right">Unitário</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itens as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo produto_imagem($item['imagem']) ?>" alt="" class="miniatura-admin mr-3">
                                                    <div>
                                                        <div class="font-weight-bold"><?php echo esc($item['nome'] ?? 'Produto removido') ?></div>
                                                        <small class="text-muted">SKU <?php echo esc($item['sku'] ?? '-') ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo (int) $item['quantidade'] ?></td>
                                            <td class="text-right text-nowrap"><?php echo moeda($item['preco_unitario']) ?></td>
                                            <td class="text-right text-nowrap font-weight-bold"><?php echo moeda($item['quantidade'] * $item['preco_unitario']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right text-muted border-top">Subtotal</td>
                                        <td class="text-right border-top"><?php echo moeda($subtotal) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right text-muted border-0">Frete (<?php echo esc($pedido['frete_servico'] ?? '-') ?>)</td>
                                        <td class="text-right border-0"><?php echo moeda($pedido['frete_valor']) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold border-0">Total</td>
                                        <td class="text-right font-weight-bold border-0" style="font-size: 1.2rem;"><?php echo moeda($pedido['valor_total']) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-user mr-2"></i>Cliente</h3></div>
                            <div class="card-body">
                                <div class="font-weight-bold"><?php echo esc($pedido['cliente_nome']) ?></div>
                                <div><a href="mailto:<?php echo esc($pedido['cliente_email'], 'attr') ?>"><?php echo esc($pedido['cliente_email']) ?></a></div>
                                <?php $telefone = apenas_digitos($pedido['cliente_telefone']); ?>
                                <?php if ($telefone): ?>
                                    <a href="https://wa.me/55<?php echo $telefone ?>" target="_blank" rel="noopener" class="btn btn-sm btn-success mt-2">
                                        <i class="fab fa-whatsapp mr-1"></i><?php echo esc($pedido['cliente_telefone']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-2"></i>Entrega</h3></div>
                            <div class="card-body">
                                <?php echo esc($pedido['endereco']) ?>, <?php echo esc($pedido['numero']) ?><?php echo $pedido['complemento'] ? ' - ' . esc($pedido['complemento']) : '' ?><br>
                                <?php echo esc($pedido['bairro']) ?> · <?php echo esc($pedido['cidade']) ?>/<?php echo esc($pedido['estado']) ?><br>
                                CEP <?php echo formata_cep($pedido['cep']) ?>
                                <?php if ($pedido['codigo_rastreio']): ?>
                                    <div class="mt-2"><span class="text-muted">Rastreio:</span> <strong><?php echo esc($pedido['codigo_rastreio']) ?></strong></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-stream mr-2"></i>Andamento</h3></div>
                    <div class="card-body">
                        <?php if ($status === 'Cancelado'): ?>
                            <div class="alert alert-danger mb-0"><i class="fas fa-ban mr-2"></i>Pedido cancelado</div>
                        <?php else: ?>
                            <ul class="passos-pedido">
                                <?php foreach (array_values($passos) as $indice => $rotulo): ?>
                                    <li class="<?php echo $indice <= $posicao ? 'feito' : '' ?>"><i></i><?php echo $rotulo ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php if (in_array($status, ['Pendente', 'Pago'], true)): ?>
                        <div class="card-footer bg-white">
                            <?php if ($status === 'Pendente'): ?>
                                <form action="<?php echo $urlStatus ?>" method="post" class="mb-2" data-confirmar="Confirmar o pagamento deste pedido? O estoque será baixado e a entrada lançada no financeiro.">
                                    <?php echo csrf_field() ?>
                                    <input type="hidden" name="acao" value="pago">
                                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-check mr-1"></i>Confirmar pagamento</button>
                                </form>
                            <?php endif; ?>

                            <?php if ($status === 'Pago'): ?>
                                <form action="<?php echo $urlStatus ?>" method="post" class="mb-2">
                                    <?php echo csrf_field() ?>
                                    <input type="hidden" name="acao" value="enviado">
                                    <label for="codigo_rastreio" class="small text-muted mb-1">Código de rastreio</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-uppercase" id="codigo_rastreio" name="codigo_rastreio" placeholder="AA123456789BR" maxlength="50">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-info"><i class="fas fa-truck mr-1"></i>Enviado</button>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>

                            <form action="<?php echo $urlStatus ?>" method="post" data-confirmar="<?php echo $status === 'Pago' ? 'Cancelar este pedido pago? O estoque será devolvido e o estorno lançado no financeiro.' : 'Cancelar este pedido?' ?>">
                                <?php echo csrf_field() ?>
                                <input type="hidden" name="acao" value="cancelado">
                                <button type="submit" class="btn btn-outline-danger btn-block"><i class="fas fa-times mr-1"></i>Cancelar pedido</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="far fa-credit-card mr-2"></i>Pagamento</h3></div>
                    <div class="card-body">
                        <dl class="mb-0">
                            <dt class="small text-muted font-weight-normal">Forma</dt>
                            <dd><?php echo esc($pedido['metodo_pagamento']) ?></dd>
                            <dt class="small text-muted font-weight-normal">Transação</dt>
                            <dd class="text-break"><?php echo esc($pedido['codigo_transacao'] ?: '-') ?></dd>
                            <dt class="small text-muted font-weight-normal">Atualizado em</dt>
                            <dd class="mb-0"><?php echo data_br($pedido['atualizado_em']) ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<script type="module" src="<?php echo base_url('app/modules/pedidoModule.js') ?>"></script>
<?php echo $this->endSection() ?>
