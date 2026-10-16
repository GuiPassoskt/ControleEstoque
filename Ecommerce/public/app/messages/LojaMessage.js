import { Notifier } from '../helpers/ui/Notifier.js';

export class LojaMessage {
    constructor() {
        throw new Error('A classe LojaMessage não deve ser instanciada');
    }

    // Mensagens flash do servidor (success / error)
    static handlerMessage({ mensagem, sucesso }) {
        if (sucesso) {
            Notifier.sucesso(mensagem);
        } else {
            Notifier.erro(mensagem);
        }
    }
}
