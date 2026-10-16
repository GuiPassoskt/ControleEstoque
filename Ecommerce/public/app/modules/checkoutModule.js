import { CheckoutController } from '../controllers/CheckoutController.js';

(function() {
    'use strict';

    const formulario = document.querySelector('#form-checkout');
    if (!formulario) {
        return;
    }

    const checkoutController = new CheckoutController(formulario);
    const campoCep = formulario.querySelector('#cep');

    formulario.addEventListener('input', (evento) => {
        if (evento.target.dataset.mascara) {
            checkoutController.aplicarMascara(evento.target);
        }
        if (evento.target === campoCep) {
            checkoutController.aoInformarCep(campoCep);
        }
    });

    formulario.addEventListener('change', (evento) => {
        if (evento.target.name === 'frete') {
            checkoutController.selecionarFrete(evento.target.value);
        }
    });

    formulario.addEventListener('submit', (evento) => {
        if (!checkoutController.validarEnvio()) {
            evento.preventDefault();
        }
    });

    // Volta de erro de validação: recalcula o frete do CEP já preenchido
    formulario.querySelectorAll('[data-mascara]').forEach(campo => checkoutController.aplicarMascara(campo));
    if (campoCep.value) {
        checkoutController.aoInformarCep(campoCep);
    }
})();
