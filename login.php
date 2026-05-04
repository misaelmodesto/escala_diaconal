<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Junta Diaconal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-top: 4px solid var(--ipb-gold);
            border-radius: 8px;
        }
    </style>
</head>
<body class="bg-light">

    <div class="card shadow-sm login-card">
        <div class="card-body p-4 text-center">
            <img src="assets/img/logo_ipb.jpg" alt="Logo IPB" width="70" height="70" class="mb-3 rounded shadow-sm">
            
            <h4 class="card-title mb-1" style="color: var(--ipb-blue);">Junta Diaconal</h4>
            <p class="text-muted mb-4">Acesso ao Sistema IPB</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="process/process_login.php" method="POST" class="text-start">
                <div class="mb-3">
                    <label for="username" class="form-label">Usuário</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-ipb-primary w-100 py-2">Entrar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>