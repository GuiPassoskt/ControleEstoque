<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity {

    protected $dates = ['criado_em', 'atualizado_em'];

    protected function setPassword(string $password) {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    protected function getPassword() {
        return $this->attributes['password'];
    }
}
