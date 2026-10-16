<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CriarTransacoes extends Migration
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
			'id_pedido'       => [
                'type'           => 'INT',
                'null'           => true
            ],
            'tipo'            => [
                'type'            => 'ENUM("Entrada","Saída")'
            ],
            'valor'           => [
                'type'            => 'DECIMAL',
                'constraint'      => '10,2'
            ],
            'descricao'       => [
                'type'            => 'VARCHAR',
                'constraint'      => 255
            ],
            'data_movimento'  => [
                'type'            => 'DATETIME'
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
		$this->forge->createTable('transacoes');
		$this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('transacoes');
    }
}
