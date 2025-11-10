<?php

require_once '../classes/cardapioManager.php';

$manager = new CardapioManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $manager->addItem(htmlspecialchars_decode($_POST['nome']), htmlspecialchars_decode($_POST['preco']), htmlspecialchars_decode($_POST['ingredientes']));
        }
        elseif ($_POST['action'] === 'edit') {
            $manager->editItem($_POST['id'], htmlspecialchars_decode($_POST['nome']), htmlspecialchars_decode($_POST['preco']), htmlspecialchars_decode($_POST['ingredientes']));
        }
        elseif ($_POST['action'] === 'delete') {
            try {
                $manager->removeItem($_POST['id']);
            } catch (\Throwable $th) {
                echo $th->getMessage();
            }
        }
    }
}


$pratos = $manager->getMenu();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <title>Cardápio</title>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container">
        <div class="modal" id="novoPrato">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="" method="post">
                        <input type="text" id="action" name="action" value="add" hidden>
                        <div class="modal-header">
                            <h1>Novo Prato</h1>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label" for="nome">Nome</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="preco">Preço</label>
                                <input type="number" class="form-control" id="preco" name="preco" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="ingredientes">Ingreditentes</label>
                                <input type="text" class="form-control" id="ingredientes" name="ingredientes" required>
                                <small class="form-text text-muted">Separe cada ingrediente com uma vírgula (,)</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-target="#novoPrato">Fechar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal" id="editarPrato">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h1>Editar Prato</h1>
                        </div>
                        <div class="modal-body">
                            <input type="text" id="edit-id" name="id" hidden>
                            <input type="text" id="action" name="action" value="edit" hidden>
                            <div class="mb-3">
                                <label class="form-label" for="edit-nome">Nome</label>
                                <input type="text" class="form-control" id="edit-nome" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="preco">Preço</label>
                                <input type="number" class="form-control" id="edit-preco" name="preco" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="ingredientes">Ingreditentes</label>
                                <input type="text" class="form-control" id="edit-ingredientes" name="ingredientes" required>
                                <small class="form-text text-muted">Separe cada ingrediente com uma vírgula (,)</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-bs-target="#editarPrato">Fechar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Preço</th>
                    <th scope="col">Ingredientes</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pratos as $prato) : ?>
                    <tr>
                        <th scope="row"><?= $prato['id'] ?></th>
                        <td><?= htmlspecialchars($prato['name'])  ?></td>
                        <td>R$ <?= htmlspecialchars($prato['price'])  ?></td>
                        <td><?= htmlspecialchars(implode(', ', $prato['ingredients']))  ?></td>
                        <td>
                            <div class="d-flex flex-direction-column gap-2">
                                <button class="btn btn-secondary edit-prato" data-bs-toggle="modal" data-bs-target="#editarPrato" data-id="<?= $prato['id'] ?>" data-nome="<?= $prato['name'] ?>" data-preco="<?= $prato['price'] ?>" data-ingredientes="<?= implode(', ', $prato['ingredients']) ?>">Editar</button>
                                <form method="post">
                                    <input type="text" id="action" name="action" value="delete" hidden>
                                    <input type="text" id="id" name="id" value="<?= $prato['id'] ?>" hidden>
                                    <button type="submit" class="btn btn-danger">Remover</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoPrato">Novo Prato</button>
    </div>
    <script>
        const editPrato = document.querySelectorAll('.edit-prato');

        editPrato.forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const nome = btn.getAttribute('data-nome');
                const preco = btn.getAttribute('data-preco');
                const ingredientes = btn.getAttribute('data-ingredientes');
    
                document.getElementById('edit-id').value = id;
                document.getElementById('edit-nome').value = nome;
                document.getElementById('edit-preco').value = preco;
                document.getElementById('edit-ingredientes').value = ingredientes;
            });
        });
    </script>

</body>
</html>