<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarClienteEndereco extends Migration
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
			'cliente_id'       => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true,
			],
			'endreco_id'       => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true,
			],
			'criado_em' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
			'atualizado_em' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
			'excluido_en' => [
				'type'           => 'DATETIME',
				'null'           => true,
			],
           
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('cliente_enderecos');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('cliente_enderecos');
	}
}
