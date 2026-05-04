<?php
// cadastrar_diacono.php
session_start();
require_once 'config/database.php';

// Busca os dias da semana do banco para preencher os checkboxes
try {
    $stmtDays = $pdo->query("SELECT * FROM days_of_week");
    $days = $stmtDays->fetchAll();
} catch (PDOException $e) {
    $days = [];
}

// Busca os diáconos e seus respectivos dias de impedimento com JOIN
try {
    $stmt = $pdo->query("
        SELECT d.id, d.name, d.cant_odd_days, d.cant_even_days, 
               GROUP_CONCAT(dow.name SEPARATOR ', ') as unavailable_days,
               GROUP_CONCAT(dow.id) as unavailable_day_ids
        FROM deacons d
        LEFT JOIN deacon_unavailable_days dud ON d.id = dud.deacon_id
        LEFT JOIN days_of_week dow ON dud.day_id = dow.id
        GROUP BY d.id
        ORDER BY d.name ASC
    ");
    $deacons = $stmt->fetchAll();
} catch (PDOException $e) {
    $deacons = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Diáconos - Junta Diaconal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .card-ipb {
            border-top: 4px solid var(--ipb-gold);
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
        }
        .form-check-input:checked {
            background-color: var(--ipb-blue);
            border-color: var(--ipb-blue);
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark navbar-ipb mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/logo_ipb.png" alt="Logo IPB" width="35" height="35" class="me-2 rounded shadow-sm">
                <strong>Junta Diaconal</strong> - IPB
            </a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="bi bi-arrow-left"></i> Voltar ao Painel
                </a>
                <a href="logout.php" class="btn btn-warning btn-sm text-dark px-3">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Operação realizada com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Erro: <?= htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card card-ipb bg-white border-0 h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded p-2 me-3 text-primary">
                                <i class="bi bi-person-plus-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="card-title text-dark mb-0 fw-bold">Novo Diácono</h5>
                                <small class="text-muted">Cadastrar novo membro da junta</small>
                            </div>
                        </div>
                        
                        <form action="process/processar_diacono.php" method="POST">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-secondary">Nome Completo</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: João da Silva">
                            </div>

                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-semibold text-secondary">Restrições Especiais:</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="cant_odd_days" value="1" id="cant_odd_days">
                                    <label class="form-check-label" for="cant_odd_days">Não pode em dias ÍMPARES</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="cant_even_days" value="1" id="cant_even_days">
                                    <label class="form-check-label" for="cant_even_days">Não pode em dias PARES</label>
                                </div>
                            </div>

                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-semibold text-secondary">Dias de Impedimento (Semana):</label>
                                <div class="row g-1">
                                    <?php foreach ($days as $d): ?>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="unavailable_days[]" value="<?= $d['id']; ?>" id="day_new_<?= $d['id']; ?>">
                                                <label class="form-check-label" for="day_new_<?= $d['id']; ?>">
                                                    <?= htmlspecialchars($d['name']); ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-ipb-primary w-100 mt-4 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-save me-1"></i> Salvar Diácono
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-ipb bg-white border-0 shadow-sm h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded p-2 me-3 text-primary">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="card-title text-dark mb-0 fw-bold">Diáconos Cadastrados</h5>
                                <small class="text-muted">Lista e controle de escala</small>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase fs-7">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Restrições</th>
                                        <th>Impedimentos</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($deacons)): ?>
                                        <?php foreach ($deacons as $d): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars($d['name']); ?></td>
                                                <td>
                                                    <?php 
                                                    $restricoes = [];
                                                    if ($d['cant_odd_days']) $restricoes[] = 'Dias ímpares';
                                                    if ($d['cant_even_days']) $restricoes[] = 'Dias pares';
                                                    echo !empty($restricoes) 
                                                        ? '<span class="badge bg-secondary bg-opacity-10 text-dark border">' . implode(', ', $restricoes) . '</span>' 
                                                        : '<span class="text-muted">Nenhuma</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($d['unavailable_days'] ?? 'Nenhum'); ?>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#editModal<?= $d['id']; ?>">
                                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                                    </button>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="editModal<?= $d['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header border-bottom-0">
                                                            <h5 class="modal-title fw-bold text-primary">
                                                                <i class="bi bi-person-gear me-2"></i> Editar Diácono
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="process/processar_editar_diacono.php" method="POST">
                                                            <div class="modal-body py-0">
                                                                <input type="hidden" name="id" value="<?= $d['id']; ?>">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label text-secondary fw-semibold">Nome Completo</label>
                                                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($d['name']); ?>" required>
                                                                </div>

                                                                <div class="mb-3 border-top pt-3">
                                                                    <label class="form-label text-secondary fw-semibold">Restrições Especiais:</label>
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input" type="checkbox" name="cant_odd_days" value="1" <?= $d['cant_odd_days'] ? 'checked' : ''; ?> id="odd_<?= $d['id']; ?>">
                                                                        <label class="form-check-label" for="odd_<?= $d['id']; ?>">Não pode em dias ÍMPARES</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="cant_even_days" value="1" <?= $d['cant_even_days'] ? 'checked' : ''; ?> id="even_<?= $d['id']; ?>">
                                                                        <label class="form-check-label" for="even_<?= $d['id']; ?>">Não pode em dias PARES</label>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3 border-top pt-3 pb-3">
                                                                    <label class="form-label text-secondary fw-semibold">Dias de Impedimento (Semana):</label>
                                                                    <div class="row g-2">
                                                                        <?php 
                                                                        $assigned_days = explode(',', $d['unavailable_day_ids'] ?? '');
                                                                        foreach ($days as $day): 
                                                                        ?>
                                                                            <div class="col-6">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" name="unavailable_days[]" value="<?= $day['id']; ?>" <?= in_array($day['id'], $assigned_days) ? 'checked' : ''; ?> id="day_edit_<?= $d['id']; ?>_<?= $day['id']; ?>">
                                                                                    <label class="form-check-label" for="day_edit_<?= $d['id']; ?>_<?= $day['id']; ?>">
                                                                                        <?= htmlspecialchars($day['name']); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0">
                                                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Salvar Alterações</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-5">
                                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                                Nenhum diácono cadastrado ainda.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>