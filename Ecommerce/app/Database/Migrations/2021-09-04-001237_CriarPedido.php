<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarPedido extends Migration
{
	public function up()
	{
		$this->db->disableForeignKeyChecks();
		$this->forge->addField([
			'id'          => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'id_cliente'       => [
				'type' 			=> 'INT'
			],
            'valor_total'      => [
				'type' 			=> 'DECIMAL',
				'constraint' 	=> '10,2',
			],
            'status'           => [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 30,
				'default' 		=> 'Pendente'
			],
            'metodo_pagamento' => [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 30,
			],
            'codigo_transacao' => [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 100,
				'null' 			=> true
			],
            'frete_valor'      => [
				'type' 			=> 'DECIMAL',
				'constraint' 	=> '10,2',
				'null' 			=> true
			],
            'frete_codigo'     => [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 50,
				'null' 			=> true
			],
			'criado_em' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
			'atualizado_em' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
			'excluido_em' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
           
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('pedidos');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('pedidos');
	}
}
