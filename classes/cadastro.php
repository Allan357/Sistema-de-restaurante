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

    public function adicionarItem($item, $quantidade = 1) {
        if ($item instanceof ItemPedido) {
            $this->pratos[] = $item->toArray();
            return true;
        }

        if (is_array($item) && isset($item['item_id'])) {
            $q = isset($item['quantidade']) ? $item['quantidade'] : $quantidade;
            $this->pratos[] = [
                'item_id' => $item['item_id'],
                'quantidade' => $q
            ];
            return true;
        }

        if (is_int($item) || is_string($item)) {
            $this->pratos[] = [
                'item_id' => $item,
                'quantidade' => $quantidade
            ];
            return true;
        }

        throw new InvalidArgumentException('Item inválido para adicionar ao pedido.');
    }
}

class ItemPedido {
    public $item_id;
    public $quantidade;
    public $nome;
    public $preco;

    public function __construct($item_id, $quantidade = 1, $nome = null, $preco = null) {
        $this->item_id = $item_id;
        $this->quantidade = $quantidade;
        $this->nome = $nome;
        $this->preco = $preco;
    }

    public function toArray() {
        $arr = [
            'item_id' => $this->item_id,
            'quantidade' => $this->quantidade
        ];
        if ($this->nome !== null) {
            $arr['nome'] = $this->nome;
        }
        if ($this->preco !== null) {
            $arr['preco'] = $this->preco;
        }
        return $arr;
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

    public function criarEConfirmarPedido($mesa, $garcom, $pratos) {
        // Registra o pedido primeiro
        $resultado = $this->registrarPedido($mesa, $garcom, $pratos);
        if (!isset($resultado['id'])) {
            // retornou erro/aviso do registro
            return $resultado;
        }

        $id = $resultado['id'];

        // Carrega pedidos atuais
        $pedidos = [];
        if (file_exists($this->pedidosFile)) {
            $pedidos = json_decode(file_get_contents($this->pedidosFile), true) ?: [];
        }

        if (!isset($pedidos[$id])) {
            return ['aviso' => 'Pedido não encontrado após registro', 'id' => $id];
        }

        // Atualiza status e marca o envio
        $pedidos[$id]['status'] = 'Enviado';
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('America/Campo_Grande');
        }
        $pedidos[$id]['enviadoEm'] = date('c');

        // Salva alterações
        if (file_put_contents($this->pedidosFile, json_encode($pedidos, JSON_PRETTY_PRINT))) {
            return [
                'aviso' => 'Pedido enviado e confirmado',
                'id' => $id,
                'enviadoEm' => $pedidos[$id]['enviadoEm']
            ];
        } else {
            return [
                'aviso' => 'Pedido registrado, mas falha ao confirmar envio',
                'id' => $id
            ];
        }
    }
}
?>