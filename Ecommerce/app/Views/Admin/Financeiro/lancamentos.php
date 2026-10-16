<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="kpi kpi-verde">
                    <div class="kpi-icone"><i class="fas fa-arrow-down"></i></div>
                    <div>
                        <div class="kpi-rotulo">Entradas</div>
                        <div class="kpi-valor"><?php echo moeda($resumo['entradas']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="kpi kpi-vermelho">
                    <div class="kpi-icone"><i class="fas fa-arrow-up"></i></div>
                    <div>
                        <div class="kpi-rotulo">Saídas</div>
                        <div class="kpi-valor"><?php echo moeda($resumo['saidas']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="kpi <?php echo $resumo['saldo'] >= 0 ? 'kpi-azul' : 'kpi-vermelho' ?>">
                    <div class="kpi-icone"><i class="fas fa-balance-scale"></i></div>
                    <div>
                        <div class="kpi-rotulo">Saldo do período</div>
                        <div class="kpi-valor"><?php echo moeda($resumo['saldo']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <form method="get" class="form-row align-items-end">
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <label for="inicio" class="small text-muted mb-1">De</label>
                        <input type="date" class="form-control" id="inicio" name="inicio" value="<?php echo esc($inicio, 'attr') ?>">
                    </div>
                    <div class="col-6 col-md-3 mb-2 mb-md-0">
                        <label for="fim" class="small text-muted mb-1">Até</label>
                        <input type="date" class="form-control" id="fim" name="fim" value="<?php echo esc($fim, 'attr') ?>">
                    </div>
                    <div class="col-8 col-md-2">
                        <label for="tipo" class="small text-muted mb-1">Tipo</label>
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="Entrada" <?php echo $tipo === 'Entrada' ? 'selected' : '' ?>>Entradas</option>
                            <option value="Saída" <?php echo $tipo === 'Saída' ? 'selected' : '' ?>>Saídas</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-1">
                        <button type="submit" class="btn btn-default btn-block" title="Filtrar"><i class="fas fa-filter"></i></button>
                    </div>
                    <div class="col-12 col-md-3 text-md-right mt-2 mt-md-0">
                        <a href="<?php echo base_url(route_to('fluxo_caixa')) ?>" class="btn btn-outline-dark"><i class="fas fa-chart-bar mr-1"></i>Fluxo</a>
                        <a href="<?php echo base_url(route_to('financeiro_create')) ?>" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Lançamento</a>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <?php if ($lancamentos === []): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-wallet fa-3x mb-3"></i>
                        <p class="mb-0">Nenhum lançamento no período.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tabela-lancamentos">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-right">Valor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lancamentos as $lancamento): ?>
                                    <?php $entrada = $lancamento['tipo'] === 'Entrada'; ?>
                                    <tr>
                                        <td class="text-nowrap"><?php echo data_br($lancamento['data_movimento']) ?></td>
                                        <td>
                                            <?php echo esc($lancamento['descricao']) ?>
                                            <?php if ($lancamento['id_pedido']): ?>
                                                <a href="<?php echo base_url(route_to('pedido_show', $lancamento['id_pedido'])) ?>" class="badge badge-light ml-1">Pedido #<?php echo sprintf('%06d', $lancamento['id_pedido']) ?></a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-<?php echo $entrada ? 'success' : 'danger' ?> badge-status"><?php echo esc($lancamento['tipo']) ?></span>
                                        </td>
                                        <td class="text-right text-nowrap <?php echo $entrada ? 'valor-entrada' : 'valor-saida' ?>">
                                            <?php echo ($entrada ? '+ ' : '- ') . moeda($lancamento['valor']) ?>
                                        </td>
                                        <td class="text-right">
                                            <?php if (! $lancamento['id_pedido']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir" data-acao="excluir" data-id="<?php echo $lancamento['id'] ?>"><i class="fas fa-trash"></i></button>
                                            <?php endif; ?>
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
<script type="module" src="<?php echo base_url('app/modules/financeiroModule.js') ?>"></script>
<?php echo $this->endSection() ?>
