<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarProduto extends Migration
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
			'descricao'       => [
				'type'           => 'TEXT',
				'constraint'     => 15.4,
				'null'           => true,
			],
			'estoque'   => [
				'type'           => 'INT',
				'constraint'     => 10,
				'default'		 => 0,
			],
			'sku'		=> [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 50
			],
			'imagem'      => [
				'type' 			=> 'VARCHAR',
				'constraint' 	=> 255,
				'null' 			=> true,
			],
            'ativo'       => [
				'type' 			=> 'BOOLEAN',
				'default' 		=> 1
			],
			'categoria_id'          => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true
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
		$this->forge->createTable('produtos');
		$this->db->enableForeignKeyChecks();
	}

	public function down()
	{
		$this->forge->dropTable('produtos');
	}
}
