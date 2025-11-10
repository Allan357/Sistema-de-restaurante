<?php
require_once('jsonStorage.php');
require_once('cardapioManager.php');
require_once('pedidoManager.php');

class PagamentoManager{
    private $armazenamento;
    private $cardapio;
    private $pedido;

    public function __construct(){
        $this->armazenamento = new JsonStorage('../data/pagamentos.json');
        $this->cardapio = new CardapioManager();
        $this->pedido = new pedidoManager();
    }
    public function FinalizarConta($mesa, $garcom, $formaPagamento ='dinheiro'){
        $todosPedidos = $this->pedido->getOrders();
        $pedidoMesa =[];
        $total = 0.0;

        if(!in_array($formaPagamento, ['dinheiro', 'cartao', 'pix'])){
            throw new Exception('Só aceitamos dinheiro, cartao ou pix');
        }
        foreach($todosPedidos as $pedido){
            if($pedido['mesa'] == $mesa && $pedido['status']== 'enviado'){
                $pedidoMesa[] = $pedido;

                foreach($pedido['pratos'] as $idPrato){
                    $prato = $this->pegarPrato($idPrato);
                    if($prato){
                        $total += (float)$prato['price'];
                    }
                }
            }
        }
        if(Empty($pedidoMesa)){
            throw new Exception('Nenhum pedido pendente nessa mesa');
        }
        $pagamento = $this->armazenamento->read();
        $novoId = count ($pagamento)+1;

        $novoPagamento = [
            'id' => $novoId,
            'mesa' => $mesa,
            'garcom' => $garcom,
            'itens' => $pedidoMesa,
            'total' => $total,
            'forma_pagamento' => $formaPagamento,
            'data' =>date('Y-m-d H:i:s')
        ];
        $pagamento[''] = $novoPagamento;
        $this->armazenamento->write ($pagamento);

        $this->marcarPago($mesa);
        return $novoPagamento;
}
private function pegarPrato($id){
    $cardapio = $this->cardapio->getMenu();
    foreach($cardapio as $prato){
        if($prato['id'] == $id){
            return $prato;
        }
    }
    return null;
}
private function marcarPago($mesa){
    $arquivoPedidos = new JsonStorage('../data/pedidos.json');
    $lista = $arquivoPedidos->read();

    foreach($lista as &$pedido){
        if($pedido['mesa'] == $mesa && $pedido['status']=='enviado'){
            $pedido['status'] = 'pago';
        }
    }
    $arquivoPedidos->write($lista);
}
public function listar(){
    return $this->armazenamento->read();
}
public function buscar($id){
    $pagamentos = $this->listar();
    foreach($pagamentos as &$pagamento){
        if($pagamento['id'] == $id){
            return $pagamento;
        }
    }
    return null;
}
    public function relatorio($data = null){
        $pagamentos = $this->listar();
        $total = 0;
        $contas = 0;
        $forma= ['dinheiro'=>0, 'cartao'=>0, 'pix'=>0];
        foreach($pagamentos as &$pagamento){
            if(strpos($pagamento['data'],$data) === 0){
                 $total += $pagamento['total'];
                 $contas ++;
                 $forma[$pagamento['forma']] = $pagamento['total'];
            }
        }
        return[
             'total'=> $total,
             'contas_fechadas'=> $contas,
             'forma'=> $forma
        ];
    }   
}
?>