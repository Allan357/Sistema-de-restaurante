<?php
require_once '../classes/auth.php';
require_once '../classes/cardapioManager.php';

$auth = new Auth();
$auth->exigirLogin();        
$auth->exigirAdmin();       

$manager = new CardapioManager();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $manager->addItem($_POST['nome'], $_POST['preco'], $_POST['ingredientes']);
                    $mensagem = 'Prato adicionado com sucesso!';
                    break;
                case 'edit':
                    $manager->editItem($_POST['id'], $_POST['nome'], $_POST['preco'], $_POST['ingredientes']);
                    $mensagem = 'Prato editado com sucesso!';
                    break;
                case 'delete':
                    $manager->removeItem($_POST['id']);
                    $mensagem = 'Prato removido com sucesso!';
                    break;
            }
        }
    } catch (Exception $e) {
        $mensagem = '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}

$pratos = $manager->getMenu();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cardápio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gerenciar Cardápio</h1>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovo">Novo Prato</button>
        </div>

        <?php if ($mensagem && !str_contains($mensagem, 'alert')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
        <?php elseif ($mensagem): ?>
            <?= $mensagem ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Ingredientes</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pratos as $prato): ?>
                        <tr>
                            <td><?= $prato['id'] ?></td>
                            <td><?= htmlspecialchars($prato['name']) ?></td>
                            <td>R$ <?= number_format($prato['price'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars(implode(', ', $prato['ingredients'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning editar" 
                                        data-id="<?= $prato['id'] ?>"
                                        data-nome="<?= htmlspecialchars($prato['name']) ?>"
                                        data-preco="<?= $prato['price'] ?>"
                                        data-ingredientes="<?= htmlspecialchars(implode(', ', $prato['ingredients'])) ?>">
                                    Editar
                                </button>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $prato['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Remover este prato?')">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalNovo">
        <div class="modal-dialog">
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Prato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><input type="text" name="nome" class="form-control" placeholder="Nome do prato" required></div>
                        <div class="mb-3"><input type="number" step="0.01" name="preco" class="form-control" placeholder="Preço" required></div>
                        <div class="mb-3"><input type="text" name="ingredientes" class="form-control" placeholder="Ingredientes (separados por vírgula)" required></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalEditar">
        <div class="modal-dialog">
            <form method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Prato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><input type="text" name="nome" id="edit-nome" class="form-control" required></div>
                        <div class="mb-3"><input type="number" step="0.01" name="preco" id="edit-preco" class="form-control" required></div>
                        <div class="mb-3"><input type="text" name="ingredientes" id="edit-ingredientes" class="form-control" required></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.editar').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit-id').value = this.dataset.id;
                document.getElementById('edit-nome').value = this.dataset.nome;
                document.getElementById('edit-preco').value = this.dataset.preco;
                document.getElementById('edit-ingredientes').value = this.dataset.ingredientes;
                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            });
        });
    </script>
</body>
</html>