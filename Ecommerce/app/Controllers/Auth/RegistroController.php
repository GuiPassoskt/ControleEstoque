<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Entities\Usuario;

class RegistroController extends BaseController {

    public function index() {
        return view('Auth/register');
    }

    public function store() {

        $userModel = new UsuarioModel();
        $userData = $this->request->getPost();

        $user = new Usuario();
        $user->fill($userData);

        if ($userModel->save($user)) {
            echo "Usuário registrado com sucesso!";
        } else {
            echo "Falha ao registrar usuário";
        }
    }
}
