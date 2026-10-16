<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdicionarCamposPedido extends Migration
{
	public function up()
	{
		$this->forge->addColumn('pedidos', [
			'id_endereco'     => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true,
				'null'           => true,
				'after'          => 'id_cliente',
			],
			'frete_servico'   => [
				'type'           => 'VARCHAR',
				'constraint'     => 100,
				'null'           => true,
				'after'          => 'frete_codigo',
			],
			'codigo_rastreio' => [
				'type'           => 'VARCHAR',
				'constraint'     => 50,
				'null'           => true,
				'after'          => 'frete_servico',
			],
			// URL do Checkout Pro, para o cliente retomar o pagamento
			'link_pagamento'  => [
				'type'           => 'VARCHAR',
				'constraint'     => 255,
				'null'           => true,
				'after'          => 'codigo_transacao',
			],
		]);
	}

	public function down()
	{
		$this->forge->dropColumn('pedidos', ['id_endereco', 'frete_servico', 'codigo_rastreio', 'link_pagamento']);
	}
}
