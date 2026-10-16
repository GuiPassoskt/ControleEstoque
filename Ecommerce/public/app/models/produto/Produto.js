export class Produto {
    constructor({ id, nome, sku, preco, estoque, ativo }) {
        this._id = Number.parseInt(id);
        this._nome = nome;
        this._sku = sku;
        this._preco = Number.parseFloat(preco) || 0;
        this._estoque = Number.parseInt(estoque) || 0;
        this._ativo = Boolean(Number(ativo));
        Object.freeze(this);
    }

    get id() {
        return this._id;
    }

    get nome() {
        return this._nome;
    }

    get sku() {
        return this._sku;
    }

    get preco() {
        return this._preco;
    }

    get estoque() {
        return this._estoque;
    }

    get ativo() {
        return this._ativo;
    }

    toString() {
        return JSON.stringify(this);
    }
}
