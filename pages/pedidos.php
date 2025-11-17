<?php
require_once '../classes/auth.php';
require_once '../classes/pedidoManager.php';
require_once '../classes/cardapioManager.php';

$auth = new Auth();
$auth->exigirLogin(); 

$pedidoManager = new PedidoManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mesa   = htmlspecialchars($_POST['mesa']);
    $garcom = htmlspecialchars($_POST['garcom']);
    $pratos = array_map('intval', $_POST['pratos'] ?? []);

    if (!empty($pratos)) {
        $pedidoManager->placeOrder($pratos, $mesa, $garcom);
    }
}

$pedidosAgrupados = $pedidoManager->getOrdersGroupedByTable();
$cardapioManager  = new CardapioManager();
$pratos           = $cardapioManager->getMenu();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Pedidos por Mesa</h1>
            <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#pedidoModal">
                Novo Pedido
            </button>
        </div>

        <?php if (empty($pedidosAgrupados)): ?>
            <div class="alert alert-info text-center">
                Nenhum pedido enviado no momento.
            </div>
        <?php else: ?>
            <?php foreach ($pedidosAgrupados as $mesa => $pedidos): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Mesa <?= htmlspecialchars($mesa) ?></h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($pedidos as $pedido): ?>
                                <li class="list-group-item">
                                    <strong>Pedido #<?= $pedido['id'] ?></strong> 
                                    - Garçom: <?= htmlspecialchars($pedido['garcom']) ?>
                                    <br><small class="text-muted"><?= $pedido['dataHora'] ?></small>
                                    <ul class="mt-2">
                                        <?php 
                                        foreach ($pedido['pratos'] as $idPrato): 
                                            foreach ($pratos as $item) {
                                                if ($item['id'] == $idPrato) {
                                                    echo '<li>' . htmlspecialchars($item['name']) . '</li>';
                                                    break;
                                                }
                                            }
                                        endforeach; 
                                        ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="text-end mt-3">
                            <button class="btn btn-danger fechar-conta" data-mesa="<?= htmlspecialchars($mesa) ?>">
                                Fechar Conta
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="pedidoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mesa</label>
                                <select name="mesa" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <?php for ($i=1; $i<=10; $i++): ?>
                                        <option value="<?= $i ?>">Mesa <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Garçom</label>
                                <input type="text" name="garcom" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pratos (Ctrl+Clique para múltiplos)</label>
                            <select name="pratos[]" class="form-select" size="8" multiple required>
                                <?php foreach ($pratos as $prato): ?>
                                    <option value="<?= $prato['id'] ?>">
                                        <?= htmlspecialchars($prato['name']) ?> - R$ <?= number_format($prato['price'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Pedido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.fechar-conta').forEach(btn => {
        btn.addEventListener('click', function() {
            const mesa = this.getAttribute('data-mesa');
            window.location.href = 'pagamento.php?mesa=' + encodeURIComponent(mesa);
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>