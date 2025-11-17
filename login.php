<?php
require_once 'classes/auth.php';
$auth = new Auth();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $auth->login($_POST['login'], $_POST['senha']);
        header('Location: pages/index.php');
        exit;
    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Login</h3>
                        <?php if ($erro): ?>
                            <div class="alert alert-danger"><?= $erro ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <input type="text" name="login" class="form-control mb-3" placeholder="Login" required>
                            <input type="password" name="senha" class="form-control mb-3" placeholder="Senha" required>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        <small class="text-muted d-block text-center mt-3">admin / admin123</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>