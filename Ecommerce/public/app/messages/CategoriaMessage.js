import { Notifier } from '../helpers/ui/Notifier.js';

export class CategoriaMessage {
    constructor() {
        throw new Error('A classe CategoriaMessage não deve ser instanciada');
    }

    static confirmarExclusao() {
        return Notifier.confirmar({
            titulo: 'Excluir categoria?',
            texto: 'Os produtos desta categoria deixarão de aparecer na loja.',
            textoConfirmar: 'Excluir',
            perigo: true
        });
    }

    static excluidaComSucesso() {
        Notifier.sucesso('Categoria excluída com sucesso!');
    }

    static falhaAoExcluir() {
        Notifier.erro('Não foi possível excluir a categoria.');
    }
}
