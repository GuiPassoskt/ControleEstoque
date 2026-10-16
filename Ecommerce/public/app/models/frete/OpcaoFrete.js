export class OpcaoFrete {
    constructor({ id, nome, empresa, valor, prazo }) {
        this._id = String(id);
        this._nome = nome ?? '';
        this._empresa = empresa ?? '';
        this._valor = Number.parseFloat(valor) || 0;
        this._prazo = Number.parseInt(prazo) || 0;
        Object.freeze(this);
    }

    get id() {
        return this._id;
    }

    get nome() {
        return this._nome;
    }

    get empresa() {
        return this._empresa;
    }

    get valor() {
        return this._valor;
    }

    get prazo() {
        return this._prazo;
    }

    get gratis() {
        return this._valor === 0;
    }

    toString() {
        return JSON.stringify(this);
    }
}
