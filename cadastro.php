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
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('America/Campo_Grande');
        }
        $this->dataHora = $dataHora ?: date('c');
        $this->status = $status;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'mesa' => $this->mesa,
            'garcom' => $this->garcom,
            'pratos' => $this->pratos,
            'dataHora' => $this->dataHora,
            'status' => $this->status
        ];
    }
}

class Menu {
    private $cardapioFile = 'cardapioTeste.json';
    private $pedidosFile = 'pedidos.json';
    private $menuItems = [];

    public function __construct() {
        date_default_timezone_set('America/Campo_Grande');
        if (file_exists($this->cardapioFile)) {
            $this->menuItems = json_decode(file_get_contents($this->cardapioFile), true) ?: [];
        }
    }

    public function registrarPedido($mesa, $garcom, $pratos) {
        if (empty($mesa) || empty($garcom) || empty($pratos)) {
            return ['aviso' => 'Mesa, garçom ou pratos inválidos'];
        }

        foreach ($pratos as $prato) {
            if (!isset($this->menuItems[$prato['item_id']])) {
                return ['aviso' => "Item com ID {$prato['item_id']} não encontrado no cardápio"];
            }
            if (!isset($prato['quantidade']) || $prato['quantidade'] <= 0) {
                return ['aviso' => 'Quantidade inválida para um ou mais pratos'];
            }
        }

        $pedidos = [];
        if (file_exists($this->pedidosFile)) {
            $pedidos = json_decode(file_get_contents($this->pedidosFile), true) ?: [];
        }

        $novoId = empty($pedidos) ? 1 : max(array_keys($pedidos)) + 1;

        $novoPedido = new Pedido($novoId, $mesa, $garcom, $pratos);

        $pedidos[$novoId] = $novoPedido->toArray();

        if (file_put_contents($this->pedidosFile, json_encode($pedidos, JSON_PRETTY_PRINT))) {
            return ['aviso' => 'Pedido registrado com sucesso', 'id' => $novoId];
        } else {
            return ['aviso' => 'Erro ao salvar o pedido'];
        }
    }
}
?>