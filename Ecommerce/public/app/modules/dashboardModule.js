import { DashboardController } from '../controllers/DashboardController.js';

(function() {
    'use strict';

    const dashboardController = new DashboardController();

    const grafico = document.querySelector('#grafico-vendas');
    if (grafico) {
        dashboardController.renderizarVendas(grafico);
    }
})();
