import { BaseController } from "./BaseController.js";
import { CategoriaService } from "../services/CategoriaService.js";
import { CategoriaMessage } from "../messages/CategoriaMessage.js";

export class CategoriaController extends BaseController {
    constructor() {
        super();
        this._categoriaService = new CategoriaService();
    }

    async confirmaExclusao(idCategoria) {
        if (!await CategoriaMessage.confirmarExclusao()) {
            return false;
        }

        const excluida = await this._categoriaService.excluir(idCategoria);
        if (excluida) {
            this.recarregarPagina();
        }
        return excluida;
    }
}
