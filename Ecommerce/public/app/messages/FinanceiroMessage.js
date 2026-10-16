import { Notifier } from '../helpers/ui/Notifier.js';

export class FinanceiroMessage {
    constructor() {
        throw new Error('A classe FinanceiroMessage não deve ser instanciada');
    }

    static confirmarExclusao() {
        return Notifier.confirmar({
            titulo: 'Excluir lançamento?',
            texto: 'O valor deixará de compor o saldo e o fluxo de caixa.',
            textoConfirmar: 'Excluir',
            perigo: true
        });
    }

    static excluidoComSucesso() {
        Notifier.sucesso('Lançamento excluído.');
    }

    static falhaAoExcluir() {
        Notifier.erro('Não foi possível excluir o lançamento.');
    }
}
