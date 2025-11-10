<?php
require_once('../classes/pedidoManager.php');
require_once('../classes/cardapioManager.php');
require_once('../classes/pagamentoManager.php');

$mesa = isset($_GET['mesa']) ? htmlspecialchars($_GET['mesa']) : null;

if (!$mesa) {
    $erro = 'Nenhuma mesa foi especificada.';
} else {
    $pedidoManager = new PedidoManager();
    $cardapioManager = new CardapioManager();
    $pedidos = $pedidoManager->getOrders();
    $total = 0.0;
    $pedidosMesa = [];

    foreach ($pedidos as $pedido) {
        if ($pedido['mesa'] == $mesa && $pedido['status'] == 'enviado') {
            $pedidosMesa[] = $pedido;
            foreach ($pedido['pratos'] as $idPrato) {
                $prato = $cardapioManager->getMenu();
                foreach ($prato as $item) {
                    if ($item['id'] == $idPrato) {
                        $total += (float)$item['price'];
                    }
                }
            }
        }
    }

    if (empty($pedidosMesa)) {
        $erro = 'Nenhum pedido pendente para esta mesa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mesa = isset($_POST['mesa']) ? htmlspecialchars($_POST['mesa']) : null;
    $formaPagamento = isset($_POST['forma_pagamento']) ? htmlspecialchars($_POST['forma_pagamento']) : 'dinheiro';

    try {
        $pagamentoManager = new PagamentoManager();
        $resultado = $pagamentoManager->FinalizarConta($mesa, 'Garçom Padrão', $formaPagamento); // Substitua 'Garçom Padrão' pelo nome do garçom real
        $sucesso = "Pagamento realizado com sucesso! Total: R$ " . number_format($resultado['total'], 2, ',', '.');
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos da Mesa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?php echo $erro; ?></div>
        <?php else: ?>
            <h1 class="mb-4">Pedidos da Mesa: <?php echo $mesa; ?></h1>
            <div class="card">
                <div class="card-header">
                    <h4>Detalhes dos Pedidos</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($pedidosMesa as $pedido): ?>
                            <li class="list-group-item">
                                <strong>Pedido ID:</strong> <?php echo $pedido['id']; ?><br>
                                <strong>Garçom:</strong> <?php echo $pedido['garcom']; ?><br>
                                <strong>Pratos:</strong>
                                <ul>
                                    <?php foreach ($pedido['pratos'] as $idPrato): ?>
                                        <?php
                                        $prato = $cardapioManager->getMenu();
                                        foreach ($prato as $item) {
                                            if ($item['id'] == $idPrato) {
                                                echo '<li>' . htmlspecialchars($item['name']) . ' - R$ ' . number_format($item['price'], 2, ',', '.') . '</li>';
                                            }
                                        }
                                        ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer">
                    <h5>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h5>
                </div>
            </div>
            <div class="mt-4">
                <form method="POST">
                    <input type="hidden" name="mesa" value="<?php echo $mesa; ?>">
                    <button type="submit" class="btn btn-success">Fazer Pagamento</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>