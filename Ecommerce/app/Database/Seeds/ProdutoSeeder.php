<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Produtos de exemplo para a vitrine: php spark db:seed ProdutoSeeder
 */
class ProdutoSeeder extends Seeder
{
	public function run()
	{
		$categorias = $this->db->table('categorias')
			->select('id, nome')
			->where('ativo', 1)
			->where('excluido_em', null)
			->orderBy('id')
			->get()
			->getResultArray();

		if ($categorias === []) {
			return;
		}

		// Procura a categoria pelo nome; sem correspondência, usa a primeira
		$categoriaPorNome = static function (string $trecho) use ($categorias): int {
			foreach ($categorias as $categoria) {
				if (stripos($categoria['nome'], $trecho) !== false) {
					return (int) $categoria['id'];
				}
			}
			return (int) $categorias[0]['id'];
		};
		$agora = date('Y-m-d H:i:s');

		$produtos = [
			['Mochila Urban Explorer 25L', 'Mochila resistente à água, compartimento acolchoado para notebook de até 15,6" e alças ergonômicas.', 189.90, 18, 'MOC-URB-25', 'Juvenil'],
			['Mochila Escolar Galáxia', 'Estampa exclusiva, dois compartimentos amplos, bolsos laterais para garrafinha e costas acolchoadas.', 129.90, 25, 'MOC-GAL-01', 'Juvenil'],
			['Mochila Infantil Dino', 'Leve e colorida, ideal para a educação infantil. Tecido lavável e zíperes reforçados.', 89.90, 4, 'MOC-DIN-01', 'Infantil'],
			['Mochila Executiva Slim', 'Design minimalista, porta USB externo e bolso antifurto nas costas.', 249.90, 10, 'MOC-EXE-SL', 'Juvenil'],
			['Lancheira Térmica Kids', 'Revestimento térmico que mantém a temperatura por até 6 horas.', 59.90, 30, 'LAN-TER-01', 'Lancheira'],
			['Mochila Trilha 40L', 'Para aventuras de fim de semana: barrigueira, capa de chuva e diversos bolsos.', 299.90, 0, 'MOC-TRI-40', 'Juvenil'],
		];

		$linhas = [];
		foreach ($produtos as [$nome, $descricao, $preco, $estoque, $sku, $categoria]) {
			$linhas[] = [
				'nome'          => $nome,
				'descricao'     => $descricao,
				'preco'         => $preco,
				'estoque'       => $estoque,
				'sku'           => $sku,
				'ativo'         => 1,
				'categoria_id'  => $categoriaPorNome($categoria),
				'criado_em'     => $agora,
				'atualizado_em' => $agora,
			];
		}

		$this->db->table('produtos')->ignore(true)->insertBatch($linhas);
	}
}
