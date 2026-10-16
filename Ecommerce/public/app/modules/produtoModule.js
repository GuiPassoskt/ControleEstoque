import { ProdutoController } from '../controllers/ProdutoController.js';

(function() {
    'use strict';

    const produtoController = new ProdutoController();

    // Listagem
    document.querySelector('#tabela-produtos')?.addEventListener('click', (evento) => {
        const botao = evento.target.closest('[data-acao="excluir"]');
        if (botao) {
            produtoController.confirmaExclusao(Number.parseInt(botao.dataset.id), botao.dataset.nome);
        }
    });

    document.querySelectorAll('select[data-envio-automatico]').forEach(select => {
        select.addEventListener('change', () => produtoController.enviarFormulario(select.form));
    });

    // Formulário
    const campoImagem = document.querySelector('#imagem');
    campoImagem?.addEventListener('change', () => {
        produtoController.previsualizarImagem(campoImagem, document.querySelector('#preview-imagem'));
    });

    globalThis.Produto = {
        listarProdutos: async () => await produtoController.listarProdutos()
    }
})();
