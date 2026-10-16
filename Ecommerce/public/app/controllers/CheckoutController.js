import { BaseController } from './BaseController.js';
import { CheckoutService } from '../services/CheckoutService.js';
import { CheckoutMessage } from '../messages/CheckoutMessage.js';
import { MascaraConverter } from '../helpers/converters/MascaraConverter.js';
import { MoedaConverter } from '../helpers/converters/MoedaConverter.js';

export class CheckoutController extends BaseController {
    constructor(formulario) {
        super();
        this._service = new CheckoutService();
        this._form = formulario;
        this._subtotal = Number.parseFloat(formulario.dataset.subtotal) || 0;
        this._freteSelecionado = formulario.dataset.freteSelecionado || '';
        this._opcoes = [];
        this._ultimoCep = '';
    }

    aplicarMascara(campo) {
        const mascaras = {
            cep: (valor) => MascaraConverter.cep(valor),
            telefone: (valor) => MascaraConverter.telefone(valor)
        };
        const mascara = mascaras[campo.dataset.mascara];
        if (mascara) {
            campo.value = mascara(campo.value);
        }
    }

    async aoInformarCep(campoCep) {
        const cep = MascaraConverter.digitos(campoCep.value);

        if (cep.length !== 8) {
            this._ultimoCep = '';
            this._renderizarMensagem(CheckoutMessage.textoInformeCep());
            this._atualizarResumo(null);
            return;
        }
        if (cep === this._ultimoCep) {
            return;
        }
        this._ultimoCep = cep;

        this._renderizarCarregando();
        const [endereco, opcoes] = await Promise.all([
            this._service.buscarEndereco(cep),
            this._service.cotarFrete(cep)
        ]);

        // O cliente pode ter digitado outro CEP enquanto a consulta rodava
        if (cep !== this._ultimoCep) {
            return;
        }

        if (endereco) {
            this._preencherEndereco(endereco);
        } else {
            CheckoutMessage.cepNaoEncontrado();
        }

        if (opcoes === null) {
            this._ultimoCep = '';
            this._renderizarMensagem(CheckoutMessage.textoInformeCep());
            this._atualizarResumo(null);
            return;
        }
        if (opcoes.length === 0) {
            CheckoutMessage.semOpcoesDeFrete();
            this._renderizarMensagem(CheckoutMessage.textoInformeCep());
            this._atualizarResumo(null);
            return;
        }

        this._opcoes = opcoes;
        const selecionada = opcoes.find(opcao => opcao.id === this._freteSelecionado) ?? opcoes[0];
        this._renderizarOpcoes(opcoes, selecionada.id);
        this.selecionarFrete(selecionada.id);
    }

    selecionarFrete(idOpcao) {
        this._freteSelecionado = idOpcao;
        this._atualizarResumo(this._opcoes.find(opcao => opcao.id === idOpcao) ?? null);
    }

    /**
     * Valida antes do envio; devolve false para o Module cancelar o submit.
     */
    validarEnvio() {
        for (const campo of this._form.querySelectorAll('input[required]')) {
            if (!campo.checkValidity()) {
                campo.classList.add('is-invalid');
                campo.focus();
                CheckoutMessage.possuiCamposInvalidos();
                return false;
            }
            campo.classList.remove('is-invalid');
        }

        if (!this._form.querySelector('input[name="frete"]:checked')) {
            CheckoutMessage.escolhaOFrete();
            document.querySelector('#cep')?.focus();
            return false;
        }

        const botao = this._form.querySelector('#botao-finalizar');
        botao.disabled = true;
        botao.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processando...';
        return true;
    }

    _preencherEndereco(endereco) {
        const campos = {
            '#endereco': endereco.logradouro,
            '#bairro': endereco.bairro,
            '#cidade': endereco.cidade,
            '#estado': endereco.estado
        };
        for (const [seletor, valor] of Object.entries(campos)) {
            const campo = this._form.querySelector(seletor);
            if (campo && valor) {
                campo.value = valor;
                campo.classList.remove('is-invalid');
            }
        }

        const numero = this._form.querySelector('#numero');
        if (numero && !numero.value) {
            numero.focus();
        }
    }

    _atualizarResumo(opcao) {
        const frete = document.querySelector('#resumo-frete');
        const total = document.querySelector('#resumo-total');

        if (!opcao) {
            frete.textContent = '—';
            frete.className = 'text-cinza';
            total.textContent = MoedaConverter.paraReal(this._subtotal);
            return;
        }

        frete.textContent = opcao.gratis ? 'Grátis' : MoedaConverter.paraReal(opcao.valor);
        frete.className = opcao.gratis ? 'text-success fw-semibold' : '';
        total.textContent = MoedaConverter.paraReal(this._subtotal + opcao.valor);
    }

    _container() {
        return document.querySelector('#opcoes-frete');
    }

    _renderizarCarregando() {
        const container = this._container();
        const texto = document.createElement('p');
        texto.className = 'small text-cinza mb-2';
        texto.textContent = CheckoutMessage.textoCalculando();
        const esqueletos = [1, 2].map(() => {
            const bloco = document.createElement('div');
            bloco.className = 'esqueleto';
            return bloco;
        });
        container.replaceChildren(texto, ...esqueletos);
    }

    _renderizarMensagem(mensagem) {
        const paragrafo = document.createElement('p');
        paragrafo.className = 'text-cinza mb-0';
        const icone = document.createElement('i');
        icone.className = 'fas fa-map-marker-alt me-2';
        paragrafo.append(icone, mensagem);
        this._container().replaceChildren(paragrafo);
    }

    _renderizarOpcoes(opcoes, idSelecionado) {
        const itens = opcoes.map(opcao => {
            const rotulo = document.createElement('label');
            rotulo.className = 'opcao';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'frete';
            radio.value = opcao.id;
            radio.className = 'form-check-input';
            radio.checked = opcao.id === idSelecionado;

            const icone = document.createElement('span');
            icone.className = 'icone-opcao';
            icone.innerHTML = '<i class="fas fa-truck"></i>';

            const descricao = document.createElement('span');
            const nome = document.createElement('strong');
            nome.className = 'd-block';
            nome.textContent = [opcao.empresa, opcao.nome].filter(Boolean).join(' · ');
            const prazo = document.createElement('span');
            prazo.className = 'small text-cinza';
            prazo.textContent = CheckoutMessage.textoPrazo(opcao.prazo);
            descricao.append(nome, prazo);

            const valor = document.createElement('span');
            valor.className = 'valor-opcao';
            valor.textContent = opcao.gratis ? 'Grátis' : MoedaConverter.paraReal(opcao.valor);

            rotulo.append(radio, icone, descricao, valor);
            return rotulo;
        });

        this._container().replaceChildren(...itens);
    }
}
