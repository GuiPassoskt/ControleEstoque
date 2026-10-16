export class BaseController {
    constructor() {
        if (new.target === BaseController) {
            throw new Error('BaseController é abstrata; estenda-a');
        }
    }

    valor(seletor) {
        return document.querySelector(seletor)?.value ?? '';
    }

    recarregarPagina() {
        location.reload();
    }

    // Lê o JSON gravado pelo PHP em data-grafico (ou outro atributo data-*)
    lerDados(elemento, atributo = 'grafico') {
        try {
            return JSON.parse(elemento?.dataset[atributo] ?? 'null');
        } catch {
            return null;
        }
    }
}
