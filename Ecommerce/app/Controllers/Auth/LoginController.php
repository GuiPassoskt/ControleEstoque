<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class LoginController extends BaseController {

    public function index() {
        if (!session()->isLoggedId) {
            return view('Auth/login');
        }
        return redirect()->route('home');
    }

    public function signIn() {
        $email = trim($this->request->getPost('inputEmail'));
        $password = trim($this->request->getPost('inputPassword'));

        $userModel = new UsuarioModel();
        $dataUser = $userModel->getByEmail($email);
        
        if(!is_null($dataUser)) {
            $hashUser = $dataUser->password;
            if (password_verify($password, $hashUser)) {
                session()->set('isLoggedId', true);
                session()->set('name', $dataUser->nome);
                return redirect()->route('home');
            } else {
                session()->setFlashData('msg', 'Usuário ou senha inválido');
                return redirect()->to('auth/login');
            }
        } else {
            session()->setFlashData('msg', 'Usuário ou senha inválido');
            return redirect()->to('auth/login');
        }
    }

    public function signOut() {
        session()->destroy();
		return redirect()->route('login');
    }

}