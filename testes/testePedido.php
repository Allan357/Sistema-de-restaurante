<?php

require_once 'classes/pedidoManager.php';

$manager = new PedidoManager();

    $items = [1, 2];
    $mesa = 'Mesa 5';
    $garcom = 'Allan';
    echo $manager->placeOrder($items, $mesa, $garcom);

    $orders = $manager->getOrders();
    
    echo print_r($orders, true);
?>