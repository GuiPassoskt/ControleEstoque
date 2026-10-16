<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarUsuario extends Migration
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
				'constraint'     => '255'
			],
			'email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'password'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'ativo'       => [
				'type'           => 'TINYINT',
				'constraint'	 => 1,
				'default'        => 1
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
		$this->forge->createTable('usuarios');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('usuarios');
	}
}
