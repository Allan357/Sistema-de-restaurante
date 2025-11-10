<?php
require_once 'usuarioManager.php';

class auth{
    private $usuarioManager;

    public function __construct(){
        session_start();
        $this->usuarioManager = new usuarioManager();
    }
    public function login($login, $senha){
        $user = $this->usuarioManager->login($login, $senha);
        $_SESSION ['usuario'] = $user;
        return $user;
    }
    public function logado(){
        return isset($_SESSION ['usuario']); 
    }
    public function logout(){
        unset($_SESSION ['usuario']);
        session_destroy();
    }
    public function eAdmin(){
        return $this->logado() && $_SESSION ['usuario']['tipo']==='admin';
    }
    public function eGarcom(){
        return $this->logado() && $_SESSION ['usuario']['tipo']==='garcom';
    }
    public function exigirGarcom(){
        if (!$this->eGarcom()){
            die('Acesso NEGADO');
        }
    }
    public function exigirAdmin(){
        if (!$this->eAdmin()){
            die('Acesso NEGADO');
        }
    }
    public function exigirLogin(){
  if (!$this->logado()){
            die('faca login para entrar');
        }
    }
    public function getUsuario(){
        return $_SESSION['usuario']?? null;
    }
}
?>