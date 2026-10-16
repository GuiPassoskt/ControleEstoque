import { BaseController } from './BaseController.js';
import { LojaMessage } from '../messages/LojaMessage.js';

/**
 * Comportamentos comuns às telas da loja: mensagens flash, seletor de quantidade
 * e formulários que se enviam sozinhos ao mudar um campo.
 */
export class LojaController extends BaseController {
    constructor() {
        super();
    }

    exibirMensagensDaSessao(elemento) {
        if (!elemento) {
            return;
        }
        if (elemento.dataset.success) {
            LojaMessage.handlerMessage({ mensagem: elemento.dataset.success, sucesso: true });
        }
        if (elemento.dataset.error) {
            LojaMessage.handlerMessage({ mensagem: elemento.dataset.error, sucesso: false });
        }
    }

    alterarQuantidade(seletor, passo) {
        const campo = seletor.querySelector('input');
        const minimo = Number.parseInt(campo.min || '0');
        const maximo = Number.parseInt(campo.max || '999');
        const atual = Number.parseInt(campo.value) || minimo;
        const novo = Math.min(maximo, Math.max(minimo, atual + passo));

        if (novo === atual) {
            return;
        }
        campo.value = novo;
        campo.dispatchEvent(new Event('change', { bubbles: true }));
    }

    enviarSeAutomatico(campo) {
        const formulario = campo.closest('form[data-envio-automatico]');
        if (formulario) {
            clearTimeout(this._temporizador);
            // Pequeno atraso: vários cliques no +/- viram um único envio
            this._temporizador = setTimeout(() => formulario.requestSubmit(), 450);
        }
    }
}
