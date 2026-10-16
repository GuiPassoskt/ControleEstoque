<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAddress extends Migration
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
			'cep'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '12',
				'null'           => true,
			],
			'endereco'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'           => true,
			],
			'numero'       => [
				'type'           => 'INT',
				'constraint'     => 5,
				'null'           => true,
			],
			'complemento'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '80',
				'null'           => true,
			],
			'cidade'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'			 => true
			],
			'bairro'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'estado'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'referencia'    => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'           => true,
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
		$this->forge->createTable('enderecos');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('enderecos');
	}
}
