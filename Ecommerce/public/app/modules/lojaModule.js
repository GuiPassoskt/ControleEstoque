import { LojaController } from '../controllers/LojaController.js';

(function() {
    'use strict';

    const lojaController = new LojaController();

    lojaController.exibirMensagensDaSessao(document.querySelector('#mensagens-sessao'));

    document.addEventListener('click', (evento) => {
        const menos = evento.target.closest('[data-quantidade-menos]');
        const mais = evento.target.closest('[data-quantidade-mais]');
        if (menos || mais) {
            lojaController.alterarQuantidade((menos ?? mais).closest('[data-quantidade]'), mais ? 1 : -1);
        }
    });

    document.addEventListener('change', (evento) => {
        lojaController.enviarSeAutomatico(evento.target);
    });
})();
