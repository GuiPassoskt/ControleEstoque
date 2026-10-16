import { Notifier } from '../helpers/ui/Notifier.js';

export class PedidoMessage {
    constructor() {
        throw new Error('A classe PedidoMessage não deve ser instanciada');
    }

    static confirmarAlteracao(texto) {
        return Notifier.confirmar({
            titulo: 'Alterar status do pedido?',
            texto,
            textoConfirmar: 'Sim, continuar',
            perigo: /cancel/i.test(texto)
        });
    }
}
