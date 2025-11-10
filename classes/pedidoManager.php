<?php
require_once 'jsonStorage.php';
require_once 'prato.php';
require_once 'cardapioManager.php';
require_once 'pedido.php';

class PedidoManager {
    private $storage;
    private $menuManager;

    public function __construct() {
        $this->storage = new JsonStorage('data/pedidos.json');
        $this->menuManager = new CardapioManager();
    }

    // Fazer pedido (fluxo principal para cliente/garçom)
    public function placeOrder($items, $mesa, $garcom) {
        $menu = $this->menuManager->getMenu();
        // Verifica disponibilidade (fluxo secundário: produto indisponível)
        foreach ($items as $itemId) {
            $found = false;
            foreach ($menu as $menuItem) {
                if ($menuItem['id'] == $itemId) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new Exception("Produto ID $itemId indisponível no cardápio.");
            }
        }
        // Gera ID único
        $orders = $this->storage->read();
        $id = count($orders) + 1;
        $newOrder = new Pedido($id, $items, $mesa, $garcom);
        $orders[] = $newOrder->toArray();
        $this->storage->write($orders);
        // "Envia" para cozinha: Atualiza status
        $this->sendToKitchen($id);
        return "Pedido ID $id registrado e enviado para a cozinha.";
    }

    // Enviar para cozinha (simulado: atualiza status)
    private function sendToKitchen($id) {
        $orders = $this->storage->read();
        foreach ($orders as &$order) {
            if ($order['id'] == $id) {
                $order['status'] = 'enviado';
                break;
            }
        }
        $this->storage->write($orders);
    }

    // Listar pedidos (para cozinha ou admin)
    public function getOrders() {
        return $this->storage->read();
    }
}