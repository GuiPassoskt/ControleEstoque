<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\Categoria;
use App\Models\CategoriaModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class CategoriaController extends BaseController
{
	private $categoriaModel;

	public function __construct()
	{
		$this->categoriaModel = new CategoriaModel();
	}

	public function index()
	{
		if (session()->isLoggedId) {
			$data = [
				'title' => 'Categorias',
				'subtitle' => 'Lista de Categorias',
				'categorias' => $this->categoriaModel->paginate(10),
				'pager' => $this->categoriaModel->pager
			];
			return view('Admin/Estoque/categorias', $data);
		}

		return redirect()->route('login');
	}

	public function create()
	{
		if (session()->isLoggedId) {
			$data = [
				'title' => 'Categorias'
			];
			return view('Admin/Estoque/categoria_form', $data);
		}
		return redirect()->route('login');
	}

	public function store()
	{
		if (session()->isLoggedId) {
			$categoriaData = $this->request->getPost(['nome', 'ativo']);

			$categoria = new Categoria();
			$categoria->fill($categoriaData);

			if ($this->categoriaModel->save($categoria)) {
				session()->setFlashdata('success', 'Categoria cadastrada com sucesso');
				return redirect()->route('categorias');
			} else {
				session()->setFlashdata('error', implode(' ', $this->categoriaModel->errors()) ?: 'Falha ao registrar categoria');
				return redirect()->route('categoria_create')->withInput();
			}
		}

		return redirect()->route('login');
	}

	public function edit($id)
	{
		if (session()->isLoggedId) {
			$categoria = $this->categoriaModel->find($id);
			if ($categoria === null) {
				throw PageNotFoundException::forPageNotFound('Categoria não encontrada');
			}

			$data = [
				'title' => 'Categorias',
				'subtitle' => 'Lista de Categorias',
				'categoria' => $categoria
			];
			return view('Admin/Estoque/categoria_form', $data);
		}
		return redirect()->route('login');
	}

	public function update($id)
	{
		if (session()->isLoggedId) {
			if ($this->categoriaModel->update($id, $this->request->getPost(['nome', 'ativo']))) {
				session()->setFlashdata('success', 'Categoria atualizada com sucesso');
				return redirect()->route('categorias');
			}

			session()->setFlashdata('error', implode(' ', $this->categoriaModel->errors()) ?: 'Falha ao atualizar categoria');
			return redirect()->back()->withInput();
		}
		return redirect()->route('login');
	}

	public function delete($id)
	{
		if (session()->isLoggedId) {
			return $this->response->setJSON(['sucesso' => (bool) $this->categoriaModel->delete($id)]);
		}
		return $this->response->setStatusCode(401)->setJSON(['sucesso' => false]);
	}
}
