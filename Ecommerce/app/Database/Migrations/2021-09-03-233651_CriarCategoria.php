<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarCategoria extends Migration
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
			'nome'     => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'ativo'       => [
				'type' 			=> 'BOOLEAN',
				'default' 		=> 1
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
		$this->forge->createTable('categorias');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('categorias');
	}
}
