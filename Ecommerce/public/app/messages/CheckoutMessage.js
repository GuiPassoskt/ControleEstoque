import { Notifier } from '../helpers/ui/Notifier.js';

export class CheckoutMessage {
    constructor() {
        throw new Error('A classe CheckoutMessage não deve ser instanciada');
    }

    static cepNaoEncontrado() {
        Notifier.aviso('CEP não encontrado. Confira o número ou preencha o endereço manualmente.');
    }

    static falhaAoCalcularFrete(mensagem) {
        Notifier.erro(mensagem || 'Não foi possível calcular o frete. Tente novamente.');
    }

    static semOpcoesDeFrete() {
        Notifier.aviso('Não há opções de entrega para este CEP.');
    }

    static escolhaOFrete() {
        Notifier.aviso('Escolha uma opção de entrega para continuar.');
    }

    static possuiCamposInvalidos() {
        Notifier.aviso('Confira os campos destacados antes de continuar.');
    }

    // Textos exibidos dentro do bloco de frete
    static textoCalculando() {
        return 'Calculando opções de entrega...';
    }

    static textoInformeCep() {
        return 'Informe o CEP para ver as opções de entrega.';
    }

    static textoPrazo(dias) {
        return dias > 0 ? `Chega em até ${dias} dia${dias > 1 ? 's' : ''} úte${dias > 1 ? 'is' : 'il'}` : 'Prazo informado após o envio';
    }
}
