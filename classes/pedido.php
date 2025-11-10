<?php

class Pedido {
    public $id;
    public $pratos; // Array de IDs de itens do cardápio
    public $mesa;
    public $garcom;
    public $status;
    public $dataHora;

    public function __construct($id, $items, $mesa, $garcom) {
        $this->id = $id;
        $this->pratos = $items;
        $this->mesa = $mesa;
        $this->garcom = $garcom;
        $this->status = 'pendente';
        $this->dataHora = date('Y-m-d H:i:s');
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'pratos' => $this->pratos,
            'mesa' => $this->mesa,
            'garcom' => $this->garcom,
            'status' => $this->status,
            'dataHora' => $this->dataHora
        ];
    }
}