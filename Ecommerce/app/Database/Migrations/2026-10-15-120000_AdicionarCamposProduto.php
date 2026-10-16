<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionarCamposProduto extends Migration
{
	public function up()
	{
		$this->forge->addColumn('produtos', [
			'preco'       => [
				'type'           => 'DECIMAL',
				'constraint'     => '10,2',
				'null'           => false,
				'default'        => 0,
				'after'          => 'descricao',
			],
			// Dimensões usadas na cotação de frete (kg e cm)
			'peso'        => [
				'type'           => 'DECIMAL',
				'constraint'     => '6,3',
				'null'           => false,
				'default'        => 0.8,
				'after'          => 'estoque',
			],
			'altura'      => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'           => false,
				'default'        => 45,
				'after'          => 'peso',
			],
			'largura'     => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'           => false,
				'default'        => 30,
				'after'          => 'altura',
			],
			'comprimento' => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'           => false,
				'default'        => 15,
				'after'          => 'largura',
			],
		]);

		// A coluna foi criada como TINYTEXT (255 caracteres)
		$this->forge->modifyColumn('produtos', [
			'descricao' => [
				'type' => 'TEXT',
				'null' => true,
			],
		]);
	}

	public function down()
	{
		$this->forge->dropColumn('produtos', ['preco', 'peso', 'altura', 'largura', 'comprimento']);
	}
}
