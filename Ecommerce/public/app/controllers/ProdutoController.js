import { BaseController } from './BaseController.js';
import { Produto } from '../models/produto/Produto.js';
import { ProdutoService } from '../services/ProdutoService.js';
import { ProdutoMessage } from '../messages/ProdutoMessage.js';

export class ProdutoController extends BaseController {
  static TIPOS_IMAGEM = ['image/jpeg', 'image/png', 'image/webp'];
  static TAMANHO_MAXIMO = 2 * 1024 * 1024;

  constructor() {
    super();
    this._produtoService = new ProdutoService();
  }

  async listarProdutos() {
    try {
      const produtos = await this._produtoService.listar();
      return Array.isArray(produtos) ? produtos.map(produto => new Produto(produto)) : produtos;
    } catch (error) {
      throw new Error('Falha ao tentar listar produtos');
    }
  }

  async confirmaExclusao(idProduto, nomeProduto) {
    if (!await ProdutoMessage.confirmarExclusao(nomeProduto)) {
      return false;
    }

    const excluido = await this._produtoService.excluir(idProduto);
    if (excluido) {
      this.recarregarPagina();
    }
    return excluido;
  }

  previsualizarImagem(input, container) {
    const arquivo = input.files?.[0];
    if (!arquivo) {
      return;
    }

    if (!ProdutoController.TIPOS_IMAGEM.includes(arquivo.type) || arquivo.size > ProdutoController.TAMANHO_MAXIMO) {
      ProdutoMessage.imagemInvalida();
      input.value = '';
      return;
    }

    const imagem = document.createElement('img');
    imagem.alt = '';
    imagem.src = URL.createObjectURL(arquivo);
    imagem.onload = () => URL.revokeObjectURL(imagem.src);
    container.replaceChildren(imagem);
  }

  enviarFormulario(formulario) {
    formulario.requestSubmit();
  }
}
