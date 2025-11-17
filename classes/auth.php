<?php
if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/usuarioManager.php';

class Auth {
    private $usuarioManager;

    public function __construct() {
        $this->usuarioManager = new UsuarioManager();
    }

    public function login($login, $senha) {
        $user = $this->usuarioManager->login($login, $senha);
        $_SESSION['usuario'] = $user;
        return $user;
    }

    public function logado() {
        return isset($_SESSION['usuario']);
    }

    public function logout() {
        unset($_SESSION['usuario']);
        session_destroy();
        header("Location: ../login.php");
        exit;
    }

    public function eAdmin() {
        return $this->logado() && ($_SESSION['usuario']['tipo'] ?? '') === 'admin';
    }

    public function eGarcom() {
        return $this->logado() && ($_SESSION['usuario']['tipo'] ?? '') === 'garcom';
    }

    public function exigirLogin() {
        if (!$this->logado()) {
            header("Location: ../login.php");
            exit;
        }
    }

    public function exigirAdmin() {
        if (!$this->eAdmin()) {
            header("Location: ../login.php");
            exit;
        }
    }

    public function exigirGarcom() {
        if (!$this->eGarcom()) {
            header("Location: ../login.php");
            exit;
        }
    }

    public function getUsuario() {
        return $_SESSION['usuario'] ?? null;
    }
}