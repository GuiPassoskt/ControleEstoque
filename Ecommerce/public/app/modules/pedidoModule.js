import { PedidoController } from '../controllers/PedidoController.js';

(function() {
    'use strict';

    const pedidoController = new PedidoController();

    document.querySelectorAll('form[data-confirmar]').forEach(formulario => {
        formulario.addEventListener('submit', (evento) => {
            evento.preventDefault();
            pedidoController.confirmarAlteracao(formulario);
        });
    });
})();
