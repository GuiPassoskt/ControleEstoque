import { ApiService } from "./ApiService.js";
import { Ambiente } from "../helpers/http/Ambiente.js";
import { Loading } from "../helpers/ui/Loading.js";
import { CategoriaMessage } from "../messages/CategoriaMessage.js";

export class CategoriaService extends ApiService {

    constructor() {
        super(Ambiente.baseUrl());
        Object.freeze(this);
    }

    async listar() {
      const response = await this.get('admin/categorias');
      return response;
    }

    incluir(categoria) {}

    async excluir(idCategoria) {
        try {
            Loading.mostrar();
            const response = await this.delete(`admin/categoria/${idCategoria}/excluir`);
            if (response?.sucesso) {
                CategoriaMessage.excluidaComSucesso();
                return true;
            }
            CategoriaMessage.falhaAoExcluir();
            return false;
        } catch (error) {
            this.logger.log(`Erro ao excluir a categoria "${idCategoria}": Erro: ${error}`, 'error');
            CategoriaMessage.falhaAoExcluir();
            return false;
        } finally {
            Loading.esconder();
        }
    }
}
