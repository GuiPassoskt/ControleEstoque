import { MoedaConverter } from '../converters/MoedaConverter.js';

/**
 * Monta gráficos com o Chart.js 2.9 já incluído no AdminLTE (assets/plugins/chart.js).
 */
export class GraficoFactory {
    constructor() {
        throw new Error('A classe GraficoFactory não deve ser instanciada');
    }

    static CORES = {
        preto: '#111111',
        acento: '#ff5a1f',
        acentoSuave: 'rgba(255, 90, 31, .12)',
        entrada: '#28a745',
        saida: '#dc3545',
        grade: 'rgba(0, 0, 0, .05)'
    };

    static linhaDeVendas(canvas, rotulos, valores) {
        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: rotulos,
                datasets: [{
                    label: 'Vendas',
                    data: valores,
                    borderColor: GraficoFactory.CORES.acento,
                    backgroundColor: GraficoFactory.CORES.acentoSuave,
                    pointBackgroundColor: GraficoFactory.CORES.acento,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    lineTension: 0.3,
                    fill: true
                }]
            },
            options: GraficoFactory._opcoesMoeda({ legenda: false })
        });
    }

    static barrasDeFluxo(canvas, { rotulos, entradas, saidas, saldo }) {
        return new Chart(canvas, {
            type: 'bar',
            data: {
                labels: rotulos,
                datasets: [
                    {
                        type: 'line',
                        label: 'Saldo acumulado',
                        data: saldo,
                        borderColor: GraficoFactory.CORES.preto,
                        backgroundColor: 'transparent',
                        pointRadius: 3,
                        borderWidth: 2,
                        lineTension: 0.25,
                        fill: false
                    },
                    {
                        label: 'Entradas',
                        data: entradas,
                        backgroundColor: GraficoFactory.CORES.entrada,
                        maxBarThickness: 26
                    },
                    {
                        label: 'Saídas',
                        data: saidas,
                        backgroundColor: GraficoFactory.CORES.saida,
                        maxBarThickness: 26
                    }
                ]
            },
            options: GraficoFactory._opcoesMoeda({ legenda: true })
        });
    }

    static _opcoesMoeda({ legenda }) {
        return {
            maintainAspectRatio: false,
            responsive: true,
            legend: { display: legenda, position: 'bottom' },
            tooltips: {
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: (item, dados) => `${dados.datasets[item.datasetIndex].label}: ${MoedaConverter.paraReal(item.yLabel)}`
                }
            },
            hover: { mode: 'index', intersect: false },
            scales: {
                xAxes: [{ gridLines: { display: false } }],
                yAxes: [{
                    gridLines: { color: GraficoFactory.CORES.grade, zeroLineColor: GraficoFactory.CORES.grade },
                    ticks: {
                        beginAtZero: true,
                        maxTicksLimit: 6,
                        callback: (valor) => MoedaConverter.paraReal(valor)
                    }
                }]
            }
        };
    }
}
