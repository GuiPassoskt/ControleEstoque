import { ApiService } from "./ApiService.js";
import { Ambiente } from "../helpers/http/Ambiente.js";
import { Loading } from "../helpers/ui/Loading.js";
import { ProdutoMessage } from "../messages/ProdutoMessage.js";

export class ProdutoService extends ApiService {
  constructor() {
    super(Ambiente.baseUrl());
    Object.freeze(this);
  }

  async listar() {
    const response = await this.get('admin/produtos');
    return response;
  }

  async excluir(idProduto) {
    try {
      Loading.mostrar();
      const response = await this.delete(`admin/produto/${idProduto}/excluir`);
      if (response?.sucesso) {
        ProdutoMessage.excluidoComSucesso();
        return true;
      }
      ProdutoMessage.falhaAoExcluir();
      return false;
    } catch (error) {
      this.logger.log(`Erro ao excluir o produto "${idProduto}": ${error}`, 'error');
      ProdutoMessage.falhaAoExcluir();
      return false;
    } finally {
      Loading.esconder();
    }
  }
}
