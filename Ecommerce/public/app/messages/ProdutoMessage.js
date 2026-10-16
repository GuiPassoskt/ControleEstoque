import { Notifier } from '../helpers/ui/Notifier.js';

export class ProdutoMessage {
    constructor() {
        throw new Error('A classe ProdutoMessage não deve ser instanciada');
    }

    static confirmarExclusao(nomeProduto) {
        return Notifier.confirmar({
            titulo: 'Excluir produto?',
            texto: `"${nomeProduto}" deixará de aparecer na loja. Pedidos antigos não são afetados.`,
            textoConfirmar: 'Excluir',
            perigo: true
        });
    }

    static excluidoComSucesso() {
        Notifier.sucesso('Produto excluído com sucesso!');
    }

    static falhaAoExcluir() {
        Notifier.erro('Não foi possível excluir o produto.');
    }

    static imagemInvalida() {
        Notifier.aviso('Escolha uma imagem JPG, PNG ou WEBP de até 2 MB.');
    }
}
