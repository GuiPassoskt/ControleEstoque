import { FinanceiroController } from '../controllers/FinanceiroController.js';

(function() {
    'use strict';

    const financeiroController = new FinanceiroController();

    document.querySelector('#tabela-lancamentos')?.addEventListener('click', (evento) => {
        const botao = evento.target.closest('[data-acao="excluir"]');
        if (botao) {
            financeiroController.confirmaExclusao(Number.parseInt(botao.dataset.id));
        }
    });

    const grafico = document.querySelector('#grafico-fluxo');
    if (grafico) {
        financeiroController.renderizarFluxoDeCaixa(grafico);
    }
})();
