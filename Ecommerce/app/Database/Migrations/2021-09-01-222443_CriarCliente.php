<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarCliente extends Migration
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
			'nome'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'           => true,
			],
			'email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'           => true,
			],
			'telefone'     => [
				'type'           => 'VARCHAR',
				'constraint'     => '22',
				'null'			 => true,
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
		$this->forge->createTable('clientes');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('clientes');
	}
}
