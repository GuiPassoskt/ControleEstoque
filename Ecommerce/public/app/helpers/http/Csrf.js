export class Csrf {
    constructor() {
        throw new Error('A classe Csrf não deve ser instanciada');
    }

    // Token e nome do header vêm das metas csrf-token / csrf-header dos layouts
    static token() {
        return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    }

    static nomeHeader() {
        return document.querySelector('meta[name="csrf-header"]')?.content ?? 'X-CSRF-TOKEN';
    }

    static headers(extras = {}) {
        return { ...extras, [Csrf.nomeHeader()]: Csrf.token() };
    }
}
