import { ApiService } from "./ApiService.js";
import { Ambiente } from "../helpers/http/Ambiente.js";
import { Loading } from "../helpers/ui/Loading.js";
import { FinanceiroMessage } from "../messages/FinanceiroMessage.js";

export class FinanceiroService extends ApiService {
    constructor() {
        super(Ambiente.baseUrl());
        Object.freeze(this);
    }

    async excluirLancamento(idLancamento) {
        try {
            Loading.mostrar();
            const response = await this.delete(`admin/financeiro/lancamento/${idLancamento}/excluir`);
            if (response?.sucesso) {
                FinanceiroMessage.excluidoComSucesso();
                return true;
            }
            FinanceiroMessage.falhaAoExcluir();
            return false;
        } catch (error) {
            this.logger.log(`Erro ao excluir o lançamento "${idLancamento}": ${error}`, 'error');
            FinanceiroMessage.falhaAoExcluir();
            return false;
        } finally {
            Loading.esconder();
        }
    }
}
