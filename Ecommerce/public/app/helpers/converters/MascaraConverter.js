export class MascaraConverter {
    constructor() {
        throw new Error('A classe MascaraConverter não deve ser instanciada');
    }

    static digitos(valor) {
        return String(valor ?? '').replace(/\D/g, '');
    }

    static cep(valor) {
        const digitos = MascaraConverter.digitos(valor).slice(0, 8);
        return digitos.length > 5 ? `${digitos.slice(0, 5)}-${digitos.slice(5)}` : digitos;
    }

    static telefone(valor) {
        const digitos = MascaraConverter.digitos(valor).slice(0, 11);
        if (digitos.length <= 2) {
            return digitos;
        }
        if (digitos.length <= 6) {
            return `(${digitos.slice(0, 2)}) ${digitos.slice(2)}`;
        }
        const meio = digitos.length === 11 ? 7 : 6;
        return `(${digitos.slice(0, 2)}) ${digitos.slice(2, meio)}-${digitos.slice(meio)}`;
    }
}
