<?php
class Pedido{
    public $id;
    public $mesa;
    public $garcom;
    public $pratos;
    public $dataHora;
    public $status;
    public function __construct($id, $mesa, $garcom, $pratos, $dataHora= null, $status = 'Pendente'){
        $this-> $id;
        $this-> $mesa;
        $this-> $garcom;
        $this-> $pratos;
        $this-> $dataHora?: date('c');
        $this-> $status;
    }
}
class menu{
    private $cardapioFile = 'cardapioTeste.json';
    private $pedidoFile = 'pedidos.json';
    private $itemMenu = [];

    public function __construct(){
        date_default_timezone_get('America/Campo_Grande');
        if(file_exists($this->cardapioFile)){
            $this->itemMenu = json_decode(file_get_contents($this->cardapioFile),true)?: [];
        }
    }
    public function registrarPedidos($mesa, $garcom, $pratos){
        if(empty($mesa)||empty($garcom)||empty($pratos)){
            return ['aviso'=>'mesa, garcom ou pratos invalidos, tente novamente'];
        }
        foreach($pratos as $prato){
            if(!isset($this->itemMenu[$prato['item_id']])){
                 return ['aviso'=> "Item com ID {$prato['item_id']} nao encontrado no cardapio"];
            }
            if(!isset($prato['quantidade'])||$prato['quantidade']<= 0){
                return ['aviso'=> 'Quantidade invalida para um ou mais pratos'];
            }
        }
        $pedidos = [];
        if(file_exists($this->pedidoFile)){
            $pedidos = json_decode(file_get_contents($pedidoFile), true) ?:[];
        }
        $novoId = empty($pedidos) ? 1: max(array_keys($pedidos))+1;
        $novoPedido = new Pedido ($novoId, $mesa, $garcom, $pratos);

        $Pedidos[$novoId] = [
            'id'=> $novoId->id,
            'mesa'=> $novoPedido->mesa,
            'garcom'=> $novoPedido-> garcom,
            'pratos'=> $novoPedido-> pratos,
            'dataHora'=> $novoPedido-> dataHora,
            'status'=> $novoPedido-> status
        ];
        if(file_put_contents($this->pedidosFile, json_encode($pedidos, JSON_PRETTY_PRINT))){
            return['Aviso'=> 'O pedido foi registrado com sucesso', 'id'=>$novoId];
        }
        return['Aviso'=> 'erro ao salvar o pedido, tente novamente'];
    }
}
?>
