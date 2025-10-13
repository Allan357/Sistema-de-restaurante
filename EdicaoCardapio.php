<?php
class MenuItem{
    public $id;
    public $nome;
    public $descricao;
    public $valor;

    public function __construct($id,$nome,$descricao,$valor) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->valor = floatval($valor);
    }
}
class Menu{
    private $jsonFile = 'cardapioTeste.json';
    private $menuItems = [];

    public function __construct(){
        if(file_exists($this->jsonFile)){
            $this->json_decode(file_get_contents($this->jsonFile), true) ?:[];
        }
    }
    public function EdicaoMenu($id,$nome,$descricao,$valor){
        if (isset($this->menuItems[$id])){
            $this->menuItems[$id]= [
                'nome' => $nome,
                'descricao'=> $descricao,
                'valor'=> floatval($valor)
            ];
            file_put_contents($this->jsonFile, json_encode($this->menuItems, JSON_PRETTY_PRINT));
            return['aviso'=>'item foi atualizado com sucesso'];
        }
        return['aviso'=>'Item nao foi encontrado! Tente novamente'];
    }
}
?>