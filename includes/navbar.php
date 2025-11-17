<?php 
require_once __DIR__ . '/../classes/auth.php'; 
$auth = new Auth();
$auth->exigirLogin();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../pages/index.php">Restaurante</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarContent" aria-controls="navbarContent" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if ($auth->eAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="cardapio.php">Cardápio</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="pedidos.php">
                        <?= $auth->eAdmin() ? 'Mesas / Pedidos' : 'Pedidos' ?>
                    </a>
                </li>

                <?php if ($auth->eAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="relatorio.php">Relatório</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link text-danger" href="../logout.php">
                        Sair (<?= htmlspecialchars($auth->getUsuario()['nome'] ?? '') ?>)
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>