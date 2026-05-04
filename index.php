<?php
// index.php
require_once 'includes/auth.php';
require_once 'config/database.php';

// Obtém estatísticas rápidas para o painel
$stmtDeacons = $pdo->query("SELECT COUNT(*) as total FROM deacons");
$totalDeacons = $stmtDeacons->fetch()['total'];

$stmtExceptions = $pdo->query("SELECT COUNT(*) as total FROM service_exceptions");
$totalExceptions = $stmtExceptions->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Junta Diaconal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-top: 4px solid var(--ipb-gold);
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(10, 54, 99, 0.15);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-ipb mb-5">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/logo_ipb.jpg" alt="Logo IPB" width="35" height="35" class="me-2 rounded">
                Junta Diaconal - IPB
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <span class="nav-link active">Olá, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário'); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastrar_diacono.php">Diáconos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cadastrar_excecao.php">Exceções</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerar_escala.php">Gerar Escala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visualizar_escala.php">Visualizar Escala</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-warning btn-sm text-dark px-3" href="logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h3>Painel Administrativo</h3>
                <p class="text-muted">Gerencie a escala e os diáconos da igreja.</p>
                <hr>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary"><i class="bi bi-person-badge"></i> Diáconos</h5>
                        <p class="card-text flex-grow-1">Cadastre novos diáconos, gerencie restrições e adicione observações mensais.</p>
                        <span class="badge bg-secondary mb-3 align-self-start">Total: <?= $totalDeacons; ?></span>
                        <a href="cadastrar_diacono.php" class="btn btn-ipb-primary mt-auto">Acessar Diáconos</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary"><i class="bi bi-calendar-event"></i> Exceções e Agenda</h5>
                        <p class="card-text flex-grow-1">Lançar alterações ou exceções na programação padrão dos cultos para o mês atual.</p>
                        <span class="badge bg-secondary mb-3 align-self-start">Total de Exceções: <?= $totalExceptions; ?></span>
                        <a href="cadastrar_excecao.php" class="btn btn-ipb-primary mt-auto">Gerenciar Exceções</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm dashboard-card">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary"><i class="bi bi-file-earmark-pdf"></i> Gerar Escala</h5>
                        <p class="card-text flex-grow-1">Gere a escala final consolidada em PDF ou formato Excel para distribuição.</p>
                        <div class="mb-3">&nbsp;</div>
                        <a href="gerar_escala.php" class="btn btn-ipb-primary mt-auto">Criar / Exportar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5 py-4 text-muted border-top">
        <small>&copy; <?= date('Y'); ?> Junta Diaconal - IPB. Todos os direitos reservados.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>