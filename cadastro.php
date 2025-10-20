<?php
class Pedido {
    public $id;
    public $mesa;
    public $garcom;
    public $pratos;
    public $dataHora;
    public $status;

    public function __construct($id, $mesa, $garcom, $pratos, $dataHora = null, $status = 'Pendente') {
        $this->id = $id;
        $this->mesa = $mesa;
        $this->garcom = $garcom;
        $this->pratos = $pratos;
        $this->dataHora = $dataHora ?: date('c');
        $this->status = $status;
    }
}

class Menu {
    private $cardapioFile = 'cardapioTeste.json';
    private $pedidoFile = 'pedidos.json';
    private $itemMenu = [];

    public function __construct() {
        date_default_timezone_set('America/Campo_Grande');

        if (file_exists($this->cardapioFile)) {
            $this->itemMenu = json_decode(file_get_contents($this->cardapioFile), true) ?: [];
        }
    }

    public function registrarPedidos($mesa, $garcom, $pratos) {
        if (empty($mesa) || empty($garcom) || empty($pratos)) {
            return ['aviso' => 'Mesa, garçom ou pratos inválidos. Tente novamente.'];
        }

        foreach ($pratos as $prato) {
            if (!isset($this->itemMenu[$prato['item_id']])) {
                return ['aviso' => "Item com ID {$prato['item_id']} não encontrado no cardápio."];
            }
            if (!isset($prato['quantidade']) || $prato['quantidade'] <= 0) {
                return ['aviso' => 'Quantidade inválida para um ou mais pratos.'];
            }
        }
        
        $pedidos = [];
        if (file_exists($this->pedidoFile)) {
            $pedidos = json_decode(file_get_contents($this->pedidoFile), true) ?: [];
        }
        $novoId = empty($pedidos) ? 1 : max(array_keys($pedidos)) + 1;
        $novoPedido = new Pedido($novoId, $mesa, $garcom, $pratos);
        $pedidos[$novoId] = [
            'id' => $novoPedido->id,
            'mesa' => $novoPedido->mesa,
            'garcom' => $novoPedido->garcom,
            'pratos' => $novoPedido->pratos,
            'dataHora' => $novoPedido->dataHora,
            'status' => $novoPedido->status
        ];
        if (file_put_contents($this->pedidoFile, json_encode($pedidos, JSON_PRETTY_PRINT))) {
            return ['aviso' => 'O pedido foi registrado com sucesso.', 'id' => $novoId];
        }
        return ['aviso' => 'Erro ao salvar o pedido. Tente novamente.'];
    }
}
?>
