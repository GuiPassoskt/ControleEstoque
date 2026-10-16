<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<?php
$filtroUrl = static fn (string $valor) => base_url(route_to('pedidos')) . ($valor !== '' ? '?status=' . urlencode($valor) : '');
?>
<section class="content">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <nav class="filtro-status mb-2" aria-label="Filtrar por status">
                <a href="<?php echo $filtroUrl('') ?>" class="<?php echo $status === '' ? 'ativo' : '' ?>">
                    Todos <span class="badge badge-light"><?php echo array_sum($totais) ?></span>
                </a>
                <?php foreach ($totais as $nomeStatus => $quantidade): ?>
                    <a href="<?php echo $filtroUrl($nomeStatus) ?>" class="<?php echo $status === $nomeStatus ? 'ativo' : '' ?>">
                        <?php echo esc($nomeStatus) ?>
                        <span class="badge badge-<?php echo status_pedido_cor($nomeStatus) ?>"><?php echo $quantidade ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
            <form method="get" class="mb-2" style="min-width: 260px;">
                <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?php echo esc($status, 'attr') ?>"><?php endif; ?>
                <div class="input-group">
                    <input type="search" name="busca" class="form-control" placeholder="Nº do pedido, cliente ou e-mail" value="<?php echo esc($busca, 'attr') ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <?php if ($pedidos === []): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-receipt fa-3x mb-3"></i>
                        <p class="mb-0">Nenhum pedido encontrado.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th class="d-none d-md-table-cell">Data</th>
                                    <th class="d-none d-lg-table-cell">Pagamento</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr>
                                        <td class="font-weight-bold text-nowrap">#<?php echo sprintf('%06d', $pedido['id']) ?></td>
                                        <td>
                                            <div class="font-weight-bold"><?php echo esc($pedido['cliente_nome'] ?? '-') ?></div>
                                            <small class="text-muted"><?php echo esc($pedido['cliente_email'] ?? '') ?></small>
                                        </td>
                                        <td class="d-none d-md-table-cell text-nowrap"><?php echo data_br($pedido['criado_em']) ?></td>
                                        <td class="d-none d-lg-table-cell"><?php echo esc($pedido['metodo_pagamento']) ?></td>
                                        <td class="text-right text-nowrap font-weight-bold"><?php echo moeda($pedido['valor_total']) ?></td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo status_pedido_cor($pedido['status']) ?> badge-status"><?php echo esc($pedido['status']) ?></span>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo base_url(route_to('pedido_show', $pedido['id'])) ?>" class="btn btn-sm btn-outline-dark">Ver <i class="fas fa-chevron-right ml-1"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer clearfix">
                <?php echo $pager->links('default', 'bootstrap'); ?>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<?php echo $this->endSection() ?>
