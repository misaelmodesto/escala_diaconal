<?php
// visualizar_escala.php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

// Pega o mês selecionado (ou o atual, por padrão)
$mes_ano = isset($_GET['mes_ano']) ? $_GET['mes_ano'] : date('Y-m');
list($ano, $mes) = explode('-', $mes_ano);

try {
    $stmt = $pdo->prepare("SELECT * FROM service_scale WHERE YEAR(scale_date) = ? AND MONTH(scale_date) = ? ORDER BY scale_date ASC");
    $stmt->execute([$ano, $mes]);
    $escala = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar a escala: " . $e->getMessage());
}

$dias_semana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
$nomes_meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Escala - Junta Diaconal IPB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .alerta { background-color: #fff3cd; }
        @media print {
            .no-print { display: none; }
            body { background-color: #fff; }
            .card { border: none; }
        }
        .card-ipb { border-top: 4px solid var(--ipb-gold); }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-ipb mb-4 no-print shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/logo_ipb.png" alt="Logo IPB" width="35" height="35" class="me-2 rounded shadow-sm">
                <strong>Junta Diaconal</strong> - IPB
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm">Voltar ao Painel</a>
                <a href="logout.php" class="btn btn-warning btn-sm text-dark ms-2">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="row mb-4 no-print">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3>Visualizar Escala Atual</h3>
                <div class="d-flex align-items-center">
                    
                    <form method="GET" class="d-flex align-items-center me-3">
                        <label for="mes_ano" class="form-label mb-0 me-2 text-muted">Mês:</label>
                        <input type="month" class="form-control form-control-sm me-2" id="mes_ano" name="mes_ano" value="<?= $mes_ano; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrar</button>
                    </form>

                    <a href="gerar_escala.php?mes_ano=<?= $mes_ano; ?>" class="btn btn-warning text-dark btn-sm px-3">
                        <i class="bi bi-arrow-repeat"></i> Gerar / Editar Escala
                    </a>
                    <button onclick="window.print()" class="btn btn-primary btn-sm px-3 ms-2">
                        <i class="bi bi-printer"></i> Imprimir / PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="card card-ipb shadow-sm border-0 mb-5">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h4 class="text-primary fw-bold">Escala de Trabalho - Junta Diaconal</h4>
                    <p class="text-muted">Igreja Presbiteriana do Brasil em Camaçari — <strong><?= $nomes_meses[(int)$mes] . ' de ' . $ano; ?></strong></p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-uppercase fs-7">
                                <th>Data</th>
                                <th>Dia da Semana</th>
                                <th>Período</th>
                                <th>Culto / Programação</th>
                                <th>Diácono Escalado</th>
                                <th>Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($escala)): ?>
                                <?php foreach ($escala as $item): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($item['scale_date'])); ?></td>
                                        <td><?= $dias_semana[date('w', strtotime($item['scale_date']))]; ?></td>
                                        <td><?= htmlspecialchars($item['period']); ?></td>
                                        <td><?= htmlspecialchars($item['program']); ?></td>
                                        <td><strong class="text-primary"><?= htmlspecialchars($item['deacon_name']); ?></strong></td>
                                        <td><?= htmlspecialchars($item['notes']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        Nenhuma escala salva encontrada para o mês selecionado. Acesse a área de geração para salvar os dados.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-4 text-muted border-top no-print">
        <small>&copy; <?= date('Y'); ?> Junta Diaconal - IPB. Todos os direitos reservados.</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>