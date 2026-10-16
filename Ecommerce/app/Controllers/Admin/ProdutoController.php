<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\ProdutoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class ProdutoController extends BaseController
{
	private const PASTA_IMAGENS = FCPATH . 'uploads/produtos';

	private $produtoModel;
	private $categoriaModel;

	public function __construct()
	{
		$this->produtoModel = new ProdutoModel();
		$this->categoriaModel = new CategoriaModel();
	}

	public function index()
	{
		if (session()->isLoggedId) {
			$busca = trim((string) $this->request->getGet('busca'));
			$categoriaId = (int) $this->request->getGet('categoria') ?: null;

			$data = [
				'title' => 'Produtos',
				'subtitle' => 'Lista de Produtos',
				'produtos' => $this->produtoModel->comCategoria($busca, $categoriaId)->orderBy('produtos.id', 'DESC')->paginate(10),
				'pager' => $this->produtoModel->pager,
				'categorias' => $this->categoriaModel->orderBy('nome')->findAll(),
				'busca' => $busca,
				'categoriaId' => $categoriaId
			];
			return view('Admin/Estoque/produtos', $data);
		}

		return redirect()->route('login');
	}

	public function create()
	{
		if (session()->isLoggedId) {
			$data = [
				'title' => 'Produtos',
				'subtitle' => 'Novo Produto',
				'produto' => null,
				'categorias' => $this->categoriaModel->ativas()
			];
			return view('Admin/Estoque/produto_form', $data);
		}
		return redirect()->route('login');
	}

	public function store()
	{
		if (session()->isLoggedId) {
			if (! $this->validateData([], $this->regrasImagem())) {
				return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
			}

			$dados = $this->dadosDoFormulario();
			$imagem = $this->salvarImagem();
			if ($imagem) {
				$dados['imagem'] = $imagem;
			}

			if ($this->produtoModel->insert($dados) === false) {
				$this->removerImagem($imagem);
				return redirect()->back()->withInput()->with('errors', $this->produtoModel->errors());
			}

			session()->setFlashdata('success', 'Produto cadastrado com sucesso');
			return redirect()->route('produtos');
		}

		return redirect()->route('login');
	}

	public function edit($id)
	{
		if (session()->isLoggedId) {
			$produto = $this->produtoModel->find($id);
			if ($produto === null) {
				throw PageNotFoundException::forPageNotFound('Produto não encontrado');
			}

			$data = [
				'title' => 'Produtos',
				'subtitle' => 'Editar Produto',
				'produto' => $produto,
				'categorias' => $this->categoriaModel->ativas()
			];
			return view('Admin/Estoque/produto_form', $data);
		}
		return redirect()->route('login');
	}

	public function update($id)
	{
		if (session()->isLoggedId) {
			$produto = $this->produtoModel->find($id);
			if ($produto === null) {
				throw PageNotFoundException::forPageNotFound('Produto não encontrado');
			}

			if (! $this->validateData([], $this->regrasImagem())) {
				return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
			}

			// O id entra nos dados para a regra is_unique do SKU ignorar o próprio produto
			$dados = ['id' => (int) $id] + $this->dadosDoFormulario();
			$imagem = $this->salvarImagem();
			if ($imagem) {
				$dados['imagem'] = $imagem;
			}

			if ($this->produtoModel->update($id, $dados) === false) {
				$this->removerImagem($imagem);
				return redirect()->back()->withInput()->with('errors', $this->produtoModel->errors());
			}

			if ($imagem) {
				$this->removerImagem($produto->imagem);
			}

			session()->setFlashdata('success', 'Produto atualizado com sucesso');
			return redirect()->route('produtos');
		}
		return redirect()->route('login');
	}

	public function delete($id)
	{
		if (session()->isLoggedId) {
			return $this->response->setJSON(['sucesso' => (bool) $this->produtoModel->delete($id)]);
		}
		return $this->response->setStatusCode(401)->setJSON(['sucesso' => false]);
	}

	private function dadosDoFormulario(): array
	{
		$post = $this->request->getPost();

		return [
			'nome'         => trim((string) ($post['nome'] ?? '')),
			'descricao'    => trim((string) ($post['descricao'] ?? '')),
			'sku'          => strtoupper(trim((string) ($post['sku'] ?? ''))),
			'preco'        => $this->decimal($post['preco'] ?? ''),
			'estoque'      => $post['estoque'] ?? '',
			'categoria_id' => $post['categoria_id'] ?? '',
			'peso'         => $this->decimal($post['peso'] ?? ''),
			'altura'       => $post['altura'] ?? '',
			'largura'      => $post['largura'] ?? '',
			'comprimento'  => $post['comprimento'] ?? '',
			'ativo'        => empty($post['ativo']) ? 0 : 1,
		];
	}

	/**
	 * Aceita "1.234,56" e "1234.56".
	 */
	private function decimal(string $valor): string
	{
		$valor = trim($valor);
		if (str_contains($valor, ',')) {
			$valor = str_replace(['.', ','], ['', '.'], $valor);
		}
		return $valor;
	}

	private function regrasImagem(): array
	{
		return [
			'imagem' => [
				'rules' => 'is_image[imagem]|mime_in[imagem,image/jpg,image/jpeg,image/png,image/webp]|max_size[imagem,2048]',
				'errors' => [
					'is_image' => 'O arquivo enviado não é uma imagem',
					'mime_in' => 'A imagem deve ser JPG, PNG ou WEBP',
					'max_size' => 'A imagem deve ter no máximo 2 MB'
				]
			]
		];
	}

	/**
	 * Move a imagem enviada para public/uploads/produtos e reduz fotos muito grandes.
	 */
	private function salvarImagem(): ?string
	{
		$arquivo = $this->request->getFile('imagem');
		if ($arquivo === null || ! $arquivo->isValid() || $arquivo->hasMoved()) {
			return null;
		}

		$nome = $arquivo->getRandomName();
		$arquivo->move(self::PASTA_IMAGENS, $nome);

		try {
			$caminho = self::PASTA_IMAGENS . '/' . $nome;
			$imagem = service('image')->withFile($caminho);
			if ($imagem->getWidth() > 1200 || $imagem->getHeight() > 1200) {
				$imagem->resize(1200, 1200, true)->save($caminho, 85);
			}
		} catch (Throwable $e) {
			log_message('warning', 'Não foi possível redimensionar {nome}: {erro}', ['nome' => $nome, 'erro' => $e->getMessage()]);
		}

		return $nome;
	}

	private function removerImagem(?string $nome): void
	{
		if ($nome && is_file(self::PASTA_IMAGENS . '/' . $nome)) {
			unlink(self::PASTA_IMAGENS . '/' . $nome);
		}
	}
}
