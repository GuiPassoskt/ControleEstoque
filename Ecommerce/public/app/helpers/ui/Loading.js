export class Loading {
    constructor() {
        throw new Error('A classe Loading não deve ser instanciada');
    }

    static _contador = 0;

    // O contador evita que a primeira requisição a terminar esconda o overlay das outras
    static mostrar() {
        Loading._contador++;
        document.body.classList.add('is-loading');
    }

    static esconder() {
        Loading._contador = Math.max(0, Loading._contador - 1);
        if (Loading._contador === 0) {
            document.body.classList.remove('is-loading');
        }
    }
}
