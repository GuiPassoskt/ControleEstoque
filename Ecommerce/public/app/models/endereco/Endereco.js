/**
 * Endereço retornado pela consulta de CEP (ViaCEP).
 */
export class Endereco {
    constructor({ logradouro, bairro, localidade, uf }) {
        this._logradouro = logradouro?.trim() ?? '';
        this._bairro = bairro?.trim() ?? '';
        this._cidade = localidade?.trim() ?? '';
        this._estado = uf?.trim().toUpperCase() ?? '';
        Object.freeze(this);
    }

    get logradouro() {
        return this._logradouro;
    }

    get bairro() {
        return this._bairro;
    }

    get cidade() {
        return this._cidade;
    }

    get estado() {
        return this._estado;
    }

    toString() {
        return JSON.stringify(this);
    }
}
