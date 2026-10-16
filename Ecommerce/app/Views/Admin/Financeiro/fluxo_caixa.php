<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<?php
$nomesMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$acumulado = 0.0;
$totalEntradas = 0.0;
$totalSaidas = 0.0;
$grafico = ['rotulos' => $nomesMeses, 'entradas' => [], 'saidas' => [], 'saldo' => []];
foreach ($meses as $mes) {
    $acumulado += $mes['entradas'] - $mes['saidas'];
    $totalEntradas += $mes['entradas'];
    $totalSaidas += $mes['saidas'];
    $grafico['entradas'][] = round($mes['entradas'], 2);
    $grafico['saidas'][] = round($mes['saidas'], 2);
    $grafico['saldo'][] = round($acumulado, 2);
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="btn-group mb-2" role="group" aria-label="Ano">
                <a href="?ano=<?php echo $ano - 1 ?>" class="btn btn-default"><i class="fas fa-chevron-left"></i></a>
                <span class="btn btn-dark disabled font-weight-bold"><?php echo $ano ?></span>
                <a href="?ano=<?php echo $ano + 1 ?>" class="btn btn-default"><i class="fas fa-chevron-right"></i></a>
            </div>
            <a href="<?php echo base_url(route_to('financeiro')) ?>" class="btn btn-outline-dark mb-2"><i class="fas fa-list mr-1"></i>Lançamentos</a>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="kpi kpi-verde"><div class="kpi-icone"><i class="fas fa-arrow-down"></i></div><div><div class="kpi-rotulo">Entradas no ano</div><div class="kpi-valor"><?php echo moeda($totalEntradas) ?></div></div></div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="kpi kpi-vermelho"><div class="kpi-icone"><i class="fas fa-arrow-up"></i></div><div><div class="kpi-rotulo">Saídas no ano</div><div class="kpi-valor"><?php echo moeda($totalSaidas) ?></div></div></div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="kpi <?php echo $acumulado >= 0 ? 'kpi-azul' : 'kpi-vermelho' ?>"><div class="kpi-icone"><i class="fas fa-balance-scale"></i></div><div><div class="kpi-rotulo">Resultado do ano</div><div class="kpi-valor"><?php echo moeda($acumulado) ?></div></div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Entradas × saídas por mês</h3></div>
            <div class="card-body">
                <div style="position: relative; height: 320px;">
                    <canvas id="grafico-fluxo" data-grafico="<?php echo esc(json_encode($grafico), 'attr') ?>"></canvas>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mês</th>
                                <th class="text-right">Entradas</th>
                                <th class="text-right">Saídas</th>
                                <th class="text-right">Resultado</th>
                                <th class="text-right">Saldo acumulado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $acumulado = 0.0; ?>
                            <?php foreach ($meses as $numero => $mes): ?>
                                <?php $resultado = $mes['entradas'] - $mes['saidas']; $acumulado += $resultado; ?>
                                <tr>
                                    <td class="font-weight-bold"><?php echo $nomesMeses[$numero - 1] ?></td>
                                    <td class="text-right valor-entrada"><?php echo moeda($mes['entradas']) ?></td>
                                    <td class="text-right valor-saida"><?php echo moeda($mes['saidas']) ?></td>
                                    <td class="text-right font-weight-bold <?php echo $resultado < 0 ? 'text-danger' : '' ?>"><?php echo moeda($resultado) ?></td>
                                    <td class="text-right <?php echo $acumulado < 0 ? 'text-danger' : '' ?>"><?php echo moeda($acumulado) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td class="border-top">Total</td>
                                <td class="text-right border-top valor-entrada"><?php echo moeda($totalEntradas) ?></td>
                                <td class="text-right border-top valor-saida"><?php echo moeda($totalSaidas) ?></td>
                                <td class="text-right border-top"><?php echo moeda($totalEntradas - $totalSaidas) ?></td>
                                <td class="border-top"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<?php echo script_tag('assets/plugins/chart.js/Chart.min.js') ?>
<script type="module" src="<?php echo base_url('app/modules/financeiroModule.js') ?>"></script>
<?php echo $this->endSection() ?>
