<?php
if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../classes/auth.php';
require_once '../classes/pedidoManager.php';
require_once '../classes/cardapioManager.php';
require_once '../classes/pagamentoManager.php';

$auth = new Auth();
$auth->exigirLogin();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restaurante - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: url('../assets/imagem/o-melhor-da-gastronomia.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        .overlay {
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-bemvindo {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            text-align: center;
            max-width: 900px;
            width: 90%;
        }
        .card-bemvindo h1 {
            font-size: 3.8rem;
            font-weight: 800;
            color: #212529;
            margin-bottom: 20px;
        }
        .btn-dash {
            font-size: 1.4rem;
            padding: 18px 50px;
            border-radius: 50px;
            margin: 15px;
            min-width: 300px;
            font-weight: 600;
        }
        .btn-dash:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
        }
        @media (max-width: 768px) {
            .card-bemvindo h1 { font-size: 2.8rem; }
            .btn-dash { min-width: 260px; font-size: 1.2rem; }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="overlay">
        <div class="card-bemvindo">
            <h1>Bem-vindo<?= $auth->eAdmin() ? ', Admin' : '' ?>!</h1>
            <p class="lead fs-2 text-muted mb-5">
                <?= htmlspecialchars($auth->getUsuario()['nome']) ?>
            </p>

            <div class="d-flex flex-wrap justify-content-center gap-4">
                <?php if ($auth->eAdmin()): ?>
                    <a href="cardapio.php" class="btn btn-primary btn-dash">Gerenciar Cardápio</a>
                <?php endif; ?>

                <a href="pedidos.php" class="btn btn-success btn-dash">
                    <?= $auth->eAdmin() ? 'Mesas & Pedidos' : 'Pedidos' ?>
                </a>

                <?php if ($auth->eAdmin()): ?>
                    <a href="relatorio.php" class="btn btn-info btn-dash text-white">Relatório de Vendas</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>