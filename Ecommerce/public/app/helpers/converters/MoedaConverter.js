export class MoedaConverter {
    constructor() {
        throw new Error('A classe MoedaConverter não deve ser instanciada');
    }

    static _formatador = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

    static paraReal(valor) {
        return MoedaConverter._formatador.format(Number(valor) || 0);
    }
}
