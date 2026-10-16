import { BaseController } from './BaseController.js';
import { GraficoFactory } from '../helpers/factorys/GraficoFactory.js';

export class DashboardController extends BaseController {
    constructor() {
        super();
    }

    renderizarVendas(canvas) {
        const dados = this.lerDados(canvas);
        if (dados) {
            GraficoFactory.linhaDeVendas(canvas, dados.rotulos, dados.valores);
        }
    }
}
