<?php
require_once 'jsonStorage.php';

class UsuarioManager {
    private $storage;

    public function __construct() {
        $this->storage = new JsonStorage('../data/usuarios.json');
        $this->criarAdmin();
    }

    private function criarAdmin() {
        $usuarios = $this->storage->read();
        if (empty($usuarios)) {
            $admin = [
                'id' => 1,
                'nome' => 'Admin',
                'login' => 'admin',
                'senha' => password_hash('admin123', PASSWORD_DEFAULT),
                'tipo' => 'admin'
            ];
            $this->storage->write([$admin]);
        }
    }

    public function login($login, $senha) {
        $usuarios = $this->storage->read();
        foreach ($usuarios as $user) {
            if ($user['login'] === $login && password_verify($senha, $user['senha'])) {
                return [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'tipo' => $user['tipo']
                ];
            }
        }
        throw new Exception('Login ou senha inválidos.');
    }

    public function cadastroGarcom($nome, $login, $senha) {
        $usuarios = $this->storage->read();
        foreach ($usuarios as $user) {
            if ($user['login'] === $login) {
                throw new Exception('Login já existente.');
            }
        }
        $id = count($usuarios) + 1;
        $usuarios[] = [
            'id' => $id,
            'nome' => $nome,
            'login' => $login,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'tipo' => 'garcom'
        ];
        $this->storage->write($usuarios);
        return "Garçom '$nome' cadastrado com sucesso.";
    }
}
?>