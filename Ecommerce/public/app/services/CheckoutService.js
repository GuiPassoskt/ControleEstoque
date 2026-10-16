import { ApiService } from "./ApiService.js";
import { Ambiente } from "../helpers/http/Ambiente.js";
import { OpcaoFrete } from "../models/frete/OpcaoFrete.js";
import { Endereco } from "../models/endereco/Endereco.js";
import { CheckoutMessage } from "../messages/CheckoutMessage.js";

export class CheckoutService extends ApiService {
    constructor() {
        super(Ambiente.baseUrl());
        Object.freeze(this);
    }

    /**
     * Cota o frete do carrinho para o CEP. Devolve a lista de opções ou null em caso de falha.
     */
    async cotarFrete(cep) {
        const { ok, dados } = await this.getComStatus('checkout/frete', { cep });

        if (!ok) {
            CheckoutMessage.falhaAoCalcularFrete(dados?.mensagem);
            this.logger.log(`Falha ao cotar frete para ${cep}: ${dados?.mensagem ?? 'sem resposta'}`, 'error');
            return null;
        }

        return (dados.opcoes ?? []).map(opcao => new OpcaoFrete(opcao));
    }

    /**
     * Endereço do CEP pela API pública do ViaCEP (sem os headers da aplicação, para não gerar preflight).
     */
    async buscarEndereco(cep) {
        try {
            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            if (!response.ok) {
                return null;
            }
            const dados = await response.json();
            return dados.erro ? null : new Endereco(dados);
        } catch (error) {
            this.logger.log(`Erro ao consultar o CEP ${cep}: ${error}`, 'warn');
            return null;
        }
    }
}
