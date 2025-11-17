<?php
require_once('../classes/pedidoManager.php');
require_once('../classes/cardapioManager.php');
require_once('../classes/pagamentoManager.php');
require_once('../classes/auth.php');

$auth = new Auth();
$auth->exigirLogin();

$mesa = $_GET['mesa'] ?? $_POST['mesa'] ?? null;
$erro = '';
$sucesso = '';
$pedidosMesa = [];
$total = 0.0;

if (!$mesa) {
    $erro = 'Nenhuma mesa especificada.';
} else {
    $pedidoManager = new PedidoManager();
    $cardapioManager = new CardapioManager();
    $todosPedidos = $pedidoManager->getOrders();
    $cardapio = $cardapioManager->getMenu();
    $cardapioMap = [];

    foreach ($cardapio as $item) {
        $cardapioMap[$item['id']] = $item;
    }

    foreach ($todosPedidos as $pedido) {
        if ($pedido['mesa'] == $mesa && $pedido['status'] == 'enviado') {
            $pedidosMesa[] = $pedido;
            foreach ($pedido['pratos'] as $idPrato) {
                if (isset($cardapioMap[$idPrato])) {
                    $total += (float)$cardapioMap[$idPrato]['price'];
                }
            }
        }
    }

    if (empty($pedidosMesa)) {
        $erro = 'Nenhum pedido pendente para esta mesa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$erro) {
    $formaPagamento = $_POST['forma_pagamento'] ?? 'dinheiro';
    try {
        $pagamentoManager = new PagamentoManager();
        $usuario = $auth->getUsuario();
        $garcom = $usuario['nome'] ?? 'Garçom Padrão';
        $resultado = $pagamentoManager->FinalizarConta($mesa, $garcom, $formaPagamento);
        $sucesso = "Conta fechada com sucesso! Total: R$ " . number_format($resultado['total'], 2, ',', '.');

        $pedidosMesa = [];
        $total = 0;
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
    <title>Fechar Conta - Mesa <?= htmlspecialchars($mesa) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Fechar Conta - Mesa <?= htmlspecialchars($mesa) ?></h2>

        <?php if ($sucesso): ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
            <a href="pedidos.php" class="btn btn-primary">Voltar aos Pedidos</a>

        <?php elseif ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <a href="pedidos.php" class="btn btn-secondary">Voltar</a>

        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h4>Itens Pedidos</h4>
                </div>
                <div class="card-body">
                    <?php foreach ($pedidosMesa as $pedido): ?>
                        <div class="mb-3 p-3 border rounded">
                            <strong>Pedido #<?= $pedido['id'] ?></strong> - Garçom: <?= htmlspecialchars($pedido['garcom']) ?>
                            <ul>
                                <?php foreach ($pedido['pratos'] as $idPrato): ?>
                                    <?php if (isset($cardapioMap[$idPrato])): ?>
                                        <?php $item = $cardapioMap[$idPrato]; ?>
                                        <li><?= htmlspecialchars($item['name']) ?> - R$ <?= number_format($item['price'], 2, ',', '.') ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                    <h4 class="text-end text-danger">Total: R$ <?= number_format($total, 2, ',', '.') ?></h4>
                </div>
            </div>

            <form method="POST" class="mt-4">
                <input type="hidden" name="mesa" value="<?= htmlspecialchars($mesa) ?>">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Forma de Pagamento</label>
                        <select name="forma_pagamento" class="form-select">
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-success btn-lg w-100">Confirmar Pagamento</button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>