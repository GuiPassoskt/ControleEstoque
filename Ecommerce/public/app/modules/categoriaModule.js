import { CategoriaController } from "../controllers/CategoriaController.js";

(function() {
    'use strict';

    const categoriaController = new CategoriaController();

    document.querySelector('#tabela-categorias')?.addEventListener('click', (evento) => {
        const botao = evento.target.closest('[data-acao="excluir"]');
        if (botao) {
            categoriaController.confirmaExclusao(Number.parseInt(botao.dataset.id));
        }
    });

    // Contrato antigo: views que ainda chamam onclick="confirmaExclusao(id)"
    globalThis.Categoria = {
        confirmaExclusao: (idCategoria) => categoriaController.confirmaExclusao(idCategoria)
    }

    globalThis.confirmaExclusao = function(idCategoria) {
        categoriaController.confirmaExclusao(idCategoria)
    }
})();
