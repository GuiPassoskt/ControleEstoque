export class Notifier {
    constructor() {
        throw new Error('A classe Notifier não deve ser instanciada');
    }

    static sucesso(mensagem) {
        Notifier._exibir('success', mensagem);
    }

    static erro(mensagem) {
        Notifier._exibir('error', mensagem);
    }

    static aviso(mensagem) {
        Notifier._exibir('warning', mensagem);
    }

    static info(mensagem) {
        Notifier._exibir('info', mensagem);
    }

    // Confirmação com SweetAlert2; resolve true quando o usuário confirma
    static async confirmar({ titulo, texto, textoConfirmar = 'Confirmar', perigo = false }) {
        if (!globalThis.Swal) {
            return confirm(`${titulo}\n\n${texto ?? ''}`);
        }

        const resultado = await Swal.fire({
            title: titulo,
            text: texto,
            icon: perigo ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: textoConfirmar,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: perigo ? '#dc3545' : '#111111',
            reverseButtons: true,
            focusCancel: perigo
        });
        return resultado.isConfirmed;
    }

    // Único ponto que conhece a biblioteca de notificação (SweetAlert2, já usada no projeto)
    static _exibir(tipo, mensagem) {
        if (!globalThis.Swal) {
            alert(mensagem);
            return;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: tipo,
            title: mensagem,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }
}
