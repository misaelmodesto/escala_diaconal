<?php
// cadastrar_usuario.php
require_once 'includes/auth.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - Junta Diaconal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php include 'includes/header.php'; ?>

    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="card-title text-center mb-4">Cadastrar Novo Usuário</h4>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Usuário cadastrado com sucesso!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Erro: <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="process/processar_usuario.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: João Silva">
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuário de Login</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Ex: joao.silva">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Cadastrar Usuário</button>
                    
                    <a href="cadastrar_diacono.php" class="btn btn-secondary w-100 mt-2">Voltar ao Painel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>