<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<?php
$grafico = [
  'rotulos' => array_map(static fn ($dia) => date('d/m', strtotime($dia)), array_keys($vendasPorDia)),
  'valores' => array_values($vendasPorDia),
];
$vendas30Dias = array_sum($vendasPorDia);
?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 col-xl-3 mb-3">
        <a href="<?= base_url(route_to('pedidos')) ?>?status=Pendente" class="kpi kpi-amarelo">
          <div class="kpi-icone"><i class="far fa-clock"></i></div>
          <div>
            <div class="kpi-rotulo">Aguardando pagamento</div>
            <div class="kpi-valor"><?= $totais['Pendente'] ?></div>
          </div>
        </a>
      </div>
      <div class="col-sm-6 col-xl-3 mb-3">
        <a href="<?= base_url(route_to('pedidos')) ?>?status=Pago" class="kpi kpi-azul">
          <div class="kpi-icone"><i class="fas fa-box"></i></div>
          <div>
            <div class="kpi-rotulo">Pagos para enviar</div>
            <div class="kpi-valor"><?= $totais['Pago'] ?></div>
          </div>
        </a>
      </div>
      <div class="col-sm-6 col-xl-3 mb-3">
        <a href="<?= base_url(route_to('financeiro')) ?>" class="kpi kpi-verde">
          <div class="kpi-icone"><i class="fas fa-dollar-sign"></i></div>
          <div>
            <div class="kpi-rotulo">Entradas no mês</div>
            <div class="kpi-valor"><?= moeda($resumoMes['entradas']) ?></div>
          </div>
        </a>
      </div>
      <div class="col-sm-6 col-xl-3 mb-3">
        <a href="<?= base_url(route_to('fluxo_caixa')) ?>" class="kpi <?= $resumoMes['saldo'] >= 0 ? 'kpi-laranja' : 'kpi-vermelho' ?>">
          <div class="kpi-icone"><i class="fas fa-balance-scale"></i></div>
          <div>
            <div class="kpi-rotulo">Saldo do mês</div>
            <div class="kpi-valor"><?= moeda($resumoMes['saldo']) ?></div>
          </div>
        </a>
      </div>
    </div>

    <div class="row">
      <section class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Vendas dos últimos 30 dias <small class="text-muted">(líquidas de estornos)</small></h3>
            <span class="ml-auto font-weight-bold"><?= moeda($vendas30Dias) ?></span>
          </div>
          <div class="card-body">
            <div style="position: relative; height: 300px;">
              <canvas id="grafico-vendas" data-grafico="<?= esc(json_encode($grafico), 'attr') ?>"></canvas>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Últimos pedidos</h3>
            <a href="<?= base_url(route_to('pedidos')) ?>" class="ml-auto small">Ver todos</a>
          </div>
          <div class="card-body p-0">
            <?php if ($ultimosPedidos === []): ?>
              <p class="text-muted text-center py-4 mb-0">Nenhum pedido ainda. Assim que a loja vender, eles aparecem aqui.</p>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <tbody>
                    <?php foreach ($ultimosPedidos as $pedido): ?>
                      <tr>
                        <td class="font-weight-bold text-nowrap">
                          <a href="<?= base_url(route_to('pedido_show', $pedido['id'])) ?>">#<?= sprintf('%06d', $pedido['id']) ?></a>
                        </td>
                        <td><?= esc($pedido['cliente_nome'] ?? '-') ?></td>
                        <td class="d-none d-md-table-cell text-muted text-nowrap"><?= data_br($pedido['criado_em']) ?></td>
                        <td class="text-right text-nowrap"><?= moeda($pedido['valor_total']) ?></td>
                        <td class="text-right"><span class="badge badge-<?= status_pedido_cor($pedido['status']) ?> badge-status"><?= esc($pedido['status']) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="col-lg-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title"><i class="fas fa-shopping-cart mr-2"></i>Pedidos por status</h3></div>
          <div class="card-body">
            <?php $totalPedidos = max(1, array_sum($totais)); ?>
            <?php foreach ($totais as $nomeStatus => $quantidade): ?>
              <div class="d-flex justify-content-between small mb-1">
                <span><?= esc($nomeStatus) ?></span><strong><?= $quantidade ?></strong>
              </div>
              <div class="progress progress-sm mb-3" style="border-radius: 999px;">
                <div class="progress-bar bg-<?= status_pedido_cor($nomeStatus) ?>" style="width: <?= round($quantidade / $totalPedidos * 100) ?>%"></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Estoque baixo</h3>
            <a href="<?= base_url(route_to('produtos')) ?>" class="ml-auto small">Produtos</a>
          </div>
          <div class="card-body p-0">
            <?php if ($estoqueBaixo === []): ?>
              <p class="text-muted text-center py-4 mb-0"><i class="fas fa-check-circle text-success mr-1"></i>Estoque em dia.</p>
            <?php else: ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($estoqueBaixo as $produto): ?>
                  <li class="list-group-item d-flex align-items-center">
                    <img src="<?= produto_imagem($produto->imagem) ?>" alt="" class="miniatura-admin mr-3" style="width: 40px; height: 40px;">
                    <a href="<?= base_url(route_to('produto_edit', $produto->id)) ?>" class="text-dark flex-grow-1 small font-weight-bold"><?= esc($produto->nome) ?></a>
                    <span class="badge badge-<?= $produto->esgotado() ? 'danger' : 'warning' ?> badge-status"><?= $produto->estoque ?> un.</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </div>
  </div>
</section>
<!-- /.content -->
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<?php echo script_tag('assets/plugins/chart.js/Chart.min.js') ?>
<script type="module" src="<?= base_url('app/modules/dashboardModule.js') ?>"></script>
<?php echo $this->endSection() ?>
