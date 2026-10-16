import { BaseController } from './BaseController.js';
import { FinanceiroService } from '../services/FinanceiroService.js';
import { FinanceiroMessage } from '../messages/FinanceiroMessage.js';
import { GraficoFactory } from '../helpers/factorys/GraficoFactory.js';

export class FinanceiroController extends BaseController {
    constructor() {
        super();
        this._financeiroService = new FinanceiroService();
    }

    async confirmaExclusao(idLancamento) {
        if (!await FinanceiroMessage.confirmarExclusao()) {
            return false;
        }

        const excluido = await this._financeiroService.excluirLancamento(idLancamento);
        if (excluido) {
            this.recarregarPagina();
        }
        return excluido;
    }

    renderizarFluxoDeCaixa(canvas) {
        const dados = this.lerDados(canvas);
        if (dados) {
            GraficoFactory.barrasDeFluxo(canvas, dados);
        }
    }
}
