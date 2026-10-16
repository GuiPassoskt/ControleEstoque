<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class VendedorController extends BaseController
{
	public function index()
	{
		if (session()->isLoggedId) {
			$data = [
				'title' => 'Vendedores',
				'subtitle' => 'Lista de vendedores'
			];
			return view('Admin/Comercial/vendedores', $data);
		} 

		return redirect()->route('login');
	}
}
