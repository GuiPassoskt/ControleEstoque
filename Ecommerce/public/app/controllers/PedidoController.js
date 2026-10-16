import { BaseController } from './BaseController.js';
import { PedidoMessage } from '../messages/PedidoMessage.js';

export class PedidoController extends BaseController {
    constructor() {
        super();
    }

    /**
     * Pede confirmação antes de enviar um formulário de mudança de status.
     */
    async confirmarAlteracao(formulario) {
        if (await PedidoMessage.confirmarAlteracao(formulario.dataset.confirmar)) {
            formulario.submit();
        }
    }
}
