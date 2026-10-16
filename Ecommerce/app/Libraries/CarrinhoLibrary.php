<?php

namespace App\Libraries;

use App\Models\ProdutoModel;

/**
 * Carrinho de compras guardado na sessão: [idProduto => quantidade].
 * Preço e estoque são sempre lidos do banco, nunca da sessão.
 */
class CarrinhoLibrary
{
	private const CHAVE = 'carrinho';

	private ProdutoModel $produtoModel;

	public function __construct()
	{
		$this->produtoModel = new ProdutoModel();
	}

	/**
	 * @return array{sucesso: bool, mensagem: string}
	 */
	public function adicionar(int $idProduto, int $quantidade): array
	{
		$produto = $this->produtoModel->ativosPorIds([$idProduto])[$idProduto] ?? null;

		if ($produto === null) {
			return ['sucesso' => false, 'mensagem' => 'Produto indisponível.'];
		}

		if ($produto->esgotado()) {
			return ['sucesso' => false, 'mensagem' => 'Produto esgotado.'];
		}

		$carrinho = $this->bruto();
		$desejada = ($carrinho[$idProduto] ?? 0) + max(1, $quantidade);
		$carrinho[$idProduto] = min($desejada, $produto->estoque);
		$this->gravar($carrinho);

		if ($desejada > $produto->estoque) {
			return ['sucesso' => true, 'mensagem' => "Temos apenas {$produto->estoque} unidade(s) de {$produto->nome}; ajustamos a quantidade."];
		}

		return ['sucesso' => true, 'mensagem' => "{$produto->nome} adicionado ao carrinho."];
	}

	public function atualizar(int $idProduto, int $quantidade): void
	{
		$carrinho = $this->bruto();

		if (! isset($carrinho[$idProduto])) {
			return;
		}

		if ($quantidade <= 0) {
			unset($carrinho[$idProduto]);
		} else {
			$carrinho[$idProduto] = $quantidade;
		}

		// itens() ajusta a quantidade ao estoque disponível
		$this->gravar($carrinho);
	}

	public function remover(int $idProduto): void
	{
		$carrinho = $this->bruto();
		unset($carrinho[$idProduto]);
		$this->gravar($carrinho);
	}

	public function limpar(): void
	{
		session()->remove(self::CHAVE);
	}

	/**
	 * Itens com o produto atualizado do banco. Remove produtos indisponíveis
	 * e limita a quantidade ao estoque.
	 *
	 * @return list<array{produto: \App\Entities\Produto, quantidade: int, subtotal: float}>
	 */
	public function itens(): array
	{
		$carrinho = $this->bruto();
		$produtos = $this->produtoModel->ativosPorIds(array_keys($carrinho));

		$itens = [];
		$sincronizado = [];
		foreach ($carrinho as $idProduto => $quantidade) {
			$produto = $produtos[$idProduto] ?? null;
			if ($produto === null || $produto->esgotado()) {
				continue;
			}

			$quantidade = min((int) $quantidade, $produto->estoque);
			$sincronizado[$idProduto] = $quantidade;
			$itens[] = [
				'produto'    => $produto,
				'quantidade' => $quantidade,
				'subtotal'   => round($produto->preco * $quantidade, 2),
			];
		}

		if ($sincronizado !== $carrinho) {
			$this->gravar($sincronizado);
		}

		return $itens;
	}

	public function subtotal(array $itens): float
	{
		return round(array_sum(array_column($itens, 'subtotal')), 2);
	}

	/**
	 * Total de unidades, para o contador do cabeçalho (sem consultar o banco).
	 */
	public function quantidade(): int
	{
		return (int) array_sum($this->bruto());
	}

	private function bruto(): array
	{
		return session(self::CHAVE) ?? [];
	}

	private function gravar(array $carrinho): void
	{
		session()->set(self::CHAVE, $carrinho);
	}
}
