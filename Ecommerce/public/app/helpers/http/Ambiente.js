export class Ambiente {
    constructor() {
        throw new Error('A classe Ambiente não deve ser instanciada');
    }

    // URL base da aplicação (meta base-url do layout), sem barra final.
    // Funciona tanto com "php spark serve" quanto em subpasta do Apache.
    static baseUrl() {
        const meta = document.querySelector('meta[name="base-url"]')?.content;
        return (meta || `${location.origin}/`).replace(/\/+$/, '');
    }
}
