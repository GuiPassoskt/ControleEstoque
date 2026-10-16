<?php

namespace App\Models;

use App\Models\BaseModel;

class TransacaoModel extends BaseModel
{
	public const ENTRADA = 'Entrada';
	public const SAIDA   = 'Saída';

	protected $DBGroup              = 'default';
	protected $table                = 'transacoes';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = true;
	protected $protectFields        = true;
	protected $allowedFields        = [
		'id_pedido',
		'tipo',
		'valor',
		'descricao',
		'data_movimento'
	];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'criado_em';
	protected $updatedField         = 'atualizado_em';
	protected $deletedField         = 'excluido_em';

	// Validation
	protected $validationRules      = [
		'tipo'           => 'required|in_list[Entrada,Saída]',
		'valor'          => 'required|decimal|greater_than[0]',
		'descricao'      => 'required|min_length[3]|max_length[255]',
		'data_movimento' => 'required|valid_date'
	];
	protected $validationMessages   = [
		'tipo' => [
			'in_list' => 'Tipo de lançamento inválido'
		],
		'valor' => [
			'required'     => 'Informe o valor',
			'decimal'      => 'Valor inválido',
			'greater_than' => 'O valor deve ser maior que zero'
		],
		'descricao' => [
			'required'   => 'Informe a descrição',
			'min_length' => 'A descrição deve ter no mínimo 3 caracteres'
		],
		'data_movimento' => [
			'required'   => 'Informe a data do movimento',
			'valid_date' => 'Data do movimento inválida'
		]
	];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	/**
	 * Lançamentos do período (datas no formato Y-m-d), opcionalmente por tipo.
	 */
	public function doPeriodo(string $inicio, string $fim, ?string $tipo = null): self
	{
		$this->where('data_movimento >=', $inicio . ' 00:00:00')
			->where('data_movimento <=', $fim . ' 23:59:59');

		if (in_array($tipo, [self::ENTRADA, self::SAIDA], true)) {
			$this->where('tipo', $tipo);
		}

		return $this->orderBy('data_movimento', 'DESC')->orderBy('id', 'DESC');
	}

	/**
	 * Totais de entradas, saídas e saldo do período.
	 */
	public function resumo(string $inicio, string $fim): array
	{
		$linhas = $this->builder()
			->select('tipo, SUM(valor) AS total, COUNT(*) AS quantidade')
			->where('excluido_em', null)
			->where('data_movimento >=', $inicio . ' 00:00:00')
			->where('data_movimento <=', $fim . ' 23:59:59')
			->groupBy('tipo')
			->get()
			->getResultArray();

		$resumo = ['entradas' => 0.0, 'saidas' => 0.0, 'quantidade' => 0];
		foreach ($linhas as $linha) {
			$resumo[$linha['tipo'] === self::ENTRADA ? 'entradas' : 'saidas'] = (float) $linha['total'];
			$resumo['quantidade'] += (int) $linha['quantidade'];
		}
		$resumo['saldo'] = $resumo['entradas'] - $resumo['saidas'];

		return $resumo;
	}

	/**
	 * Entradas e saídas por mês do ano: [1 => ['entradas' => x, 'saidas' => y], ...]
	 */
	public function fluxoMensal(int $ano): array
	{
		$linhas = $this->builder()
			->select('MONTH(data_movimento) AS mes, tipo, SUM(valor) AS total')
			->where('excluido_em', null)
			->where('data_movimento >=', $ano . '-01-01 00:00:00')
			->where('data_movimento <=', $ano . '-12-31 23:59:59')
			->groupBy(['mes', 'tipo'])
			->get()
			->getResultArray();

		$meses = array_fill(1, 12, ['entradas' => 0.0, 'saidas' => 0.0]);
		foreach ($linhas as $linha) {
			$chave = $linha['tipo'] === self::ENTRADA ? 'entradas' : 'saidas';
			$meses[(int) $linha['mes']][$chave] = (float) $linha['total'];
		}

		return $meses;
	}

	/**
	 * Vendas líquidas (pagamentos de pedidos menos estornos) por dia nos últimos N dias:
	 * ['2026-10-01' => 120.0, ...]
	 */
	public function vendasPorDia(int $dias = 30): array
	{
		$inicio = date('Y-m-d', strtotime('-' . ($dias - 1) . ' days'));

		$linhas = $this->builder()
			->select('DATE(data_movimento) AS dia')
			->select('SUM(CASE WHEN tipo = ' . $this->db->escape(self::ENTRADA) . ' THEN valor ELSE -valor END) AS total', false)
			->where('excluido_em', null)
			->where('id_pedido IS NOT NULL')
			->where('data_movimento >=', $inicio . ' 00:00:00')
			->groupBy('dia')
			->get()
			->getResultArray();

		$vendas = [];
		for ($i = 0; $i < $dias; $i++) {
			$vendas[date('Y-m-d', strtotime($inicio . " +{$i} days"))] = 0.0;
		}
		foreach ($linhas as $linha) {
			$vendas[$linha['dia']] = (float) $linha['total'];
		}

		return $vendas;
	}
}
