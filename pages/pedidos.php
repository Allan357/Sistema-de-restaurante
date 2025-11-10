<?php
    require_once '../classes/PedidoManager.php';
    require_once '../classes/CardapioManager.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mesa = htmlspecialchars($_POST['mesa']);
        $garcom = htmlspecialchars($_POST['garcom']);
        $pratos = array_map('intval', $_POST['pratos']);
        
        try {
            $pedidoManager->placeOrder($pratos, $mesa, $garcom);
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    $pedidoManager = new PedidoManager();
    $pedidosAgrupados = $pedidoManager->getOrdersGroupedByTable();
    $cardapioManager = new CardapioManager();
    $pratos = $cardapioManager->getMenu();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Pedidos</title>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Pedidos Agrupados por Mesa</h1>
        <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#pedidoModal">Novo Pedido</button>
        
        <?php if (empty($pedidosAgrupados)): ?>
            <div class="alert alert-warning text-center" role="alert">
                Nenhum pedido foi encontrado.
            </div>
        <?php else: ?>
            <?php foreach ($pedidosAgrupados as $mesa => $pedidos): ?>
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <?= htmlspecialchars($mesa) ?>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($pedidos as $pedido): ?>
                                <li class="list-group-item">
                                    <strong>Pedido ID:</strong> <?= htmlspecialchars($pedido['id']) ?> <br>
                                    <strong>Pratos:</strong> <?= implode(', ', $pedido['pratos']) ?> <br>
                                    <strong>Garçom:</strong> <?= htmlspecialchars($pedido['garcom']) ?> <br>
                                    <strong>Status:</strong> <?= htmlspecialchars($pedido['status']) ?> <br>
                                    <strong>Data/Hora:</strong> <?= htmlspecialchars($pedido['dataHora']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-3">
                            <button class="btn btn-danger fechar-conta" data-mesa="<?= htmlspecialchars($mesa) ?>">Fechar Conta</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal para Novo Pedido -->
    <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pedidoModalLabel">Novo Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="pedidoForm" method="post">
                        <div class="mb-3">
                            <label for="mesa" class="form-label">Mesa</label>
                            <select class="form-select" id="mesa" name="mesa" required>
                                <option value="" disabled selected>Selecione a mesa</option>
                                <option value="1">Mesa 1</option>
                                <option value="2">Mesa 2</option>
                                <option value="3">Mesa 3</option>
                                <option value="4">Mesa 4</option>
                                <option value="5">Mesa 5</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="garcom" class="form-label">Garçom</label>
                            <input type="text" class="form-control" id="garcom" name="garcom" required>
                        </div>
                        <div class="mb-3">
                            <label for="pratos" class="form-label">Pratos</label>
                            <select class="form-select" id="pratos" name="pratos[]" multiple required>
                                <option disabled>Segure Ctrl (ou Cmd no Mac) para selecionar vários pratos</option>
                                <?php foreach ($pratos as $prato): ?>
                                    <option value="<?= htmlspecialchars($prato['id']) ?>"><?= htmlspecialchars($prato['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Pedido</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    </script>
</body>
</html>