<?php
// gerar_escala.php
session_start();
require_once 'includes/auth.php';
require_once 'config/database.php';

date_default_timezone_set('America/Bahia');

// Pega o mês/ano do parâmetro ou usa o mês atual
$mes_ano = isset($_GET['mes_ano']) ? $_GET['mes_ano'] : date('Y-m');
list($ano, $mes) = explode('-', $mes_ano);

$nomes_meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// MAPA DIAS PT-BR
$dias_semana_map = [
    'Sunday' => 'domingo',
    'Monday' => 'segunda-feira',
    'Tuesday' => 'terça-feira',
    'Wednesday' => 'quarta-feira',
    'Thursday' => 'quinta-feira',
    'Friday' => 'sexta-feira',
    'Saturday' => 'sábado'
];

try {
    $stmt = $pdo->query("
        SELECT d.id, d.name, d.cant_odd_days, d.cant_even_days, 
               GROUP_CONCAT(dow.name) as unavailable_days
        FROM deacons d
        LEFT JOIN deacon_unavailable_days dud ON d.id = dud.deacon_id
        LEFT JOIN days_of_week dow ON dud.day_id = dow.id
        GROUP BY d.id
    ");
    $deacons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    shuffle($deacons);

    // Busca todos os diáconos para popular o dropdown
    $stmtAll = $pdo->query("SELECT id, name FROM deacons ORDER BY name ASC");
    $all_deacons = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro no banco: " . $e->getMessage());
}

$dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$escala_final = [];
$used_in_cycle = [];
$ultimo_diacono_por_data = [];

$contador = [];
foreach ($deacons as $d) {
    $contador[$d['id']] = 0;
}

for ($dia = 1; $dia <= $dias_no_mes; $dia++) {

    $data_atual = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
    $dia_semana_num = date('w', strtotime($data_atual));

    $programacoes = [];

    if ($dia_semana_num == 0) {
        $programacoes[] = ['periodo' => 'Manhã', 'culto' => 'EBD - Escola Bíblica Dominical'];
        $programacoes[] = ['periodo' => 'Noite', 'culto' => 'Culto Solene'];
    } elseif ($dia_semana_num == 2) {
        $programacoes[] = ['periodo' => 'Noite', 'culto' => 'Culto de Doutrina'];
    } elseif ($dia_semana_num == 4) {
        $programacoes[] = ['periodo' => 'Noite', 'culto' => 'Culto de Oração'];
    }

    foreach ($programacoes as $prog) {

        $escalado = 'Aguardando escala';
        $notas = '';
        $alerta = false;
        $encontrado = false;
        $tentativas = 0;

        while (!$encontrado && $tentativas < 2) {

            usort($deacons, function($a, $b) use ($contador) {
                return $contador[$a['id']] <=> $contador[$b['id']];
            });

            foreach ($deacons as $deacon) {

                if (in_array($deacon['id'], $used_in_cycle)) continue;

                $pode = true;

                if ($deacon['cant_odd_days'] == 1 && $dia % 2 != 0) $pode = false;
                if ($deacon['cant_even_days'] == 1 && $dia % 2 == 0) $pode = false;

                $nome_en = date('l', strtotime($data_atual));
                $nome_dia = $dias_semana_map[$nome_en];

                $unavail = !empty($deacon['unavailable_days']) 
                    ? explode(',', $deacon['unavailable_days']) 
                    : [];

                foreach ($unavail as $u) {
                    if (trim(strtolower($u)) === trim(strtolower($nome_dia))) {
                        $pode = false;
                    }
                }

                if (isset($ultimo_diacono_por_data[$data_atual]) &&
                    $ultimo_diacono_por_data[$data_atual] === $deacon['name']) {
                    $pode = false;
                }

                $dia_anterior = date('Y-m-d', strtotime($data_atual . ' -2 days'));
                if (isset($ultimo_diacono_por_data[$dia_anterior]) &&
                    $ultimo_diacono_por_data[$dia_anterior] === $deacon['name']) {
                    $pode = false;
                }

                if ($pode) {
                    $escalado = $deacon['name'];
                    $used_in_cycle[] = $deacon['id'];
                    $ultimo_diacono_por_data[$data_atual] = $deacon['name'];
                    $contador[$deacon['id']]++;
                    $encontrado = true;
                    break;
                }
            }

            if (!$encontrado) {
                $used_in_cycle = [];
                shuffle($deacons);
                $tentativas++;
            }
        }

        if (!$encontrado) {
            $notas = 'Alerta: Escala forçada (conflito de regras)';
            $alerta = true;
        }

        if (count($used_in_cycle) >= count($deacons)) {
            $used_in_cycle = [];
            shuffle($deacons);
        }

        $escala_final[] = [
            'data' => $data_atual,
            'dia_semana' => ucfirst($dias_semana_map[date('l', strtotime($data_atual))]),
            'periodo' => $prog['periodo'],
            'culto' => $prog['culto'],
            'escalado' => $escalado,
            'notas' => $notas,
            'alerta' => $alerta
        ];
    }
}

// Exportar CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=escala_'.strtolower($nomes_meses[$mes]).'_'.$ano.'.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['Data', 'Dia da Semana', 'Período', 'Programação', 'Diácono Escalado', 'Observações']);
    
    foreach ($escala_final as $item) {
        fputcsv($output, [
            date('d/m/Y', strtotime($item['data'])),
            $item['dia_semana'],
            $item['periodo'],
            $item['culto'],
            $item['escalado'],
            $item['notas']
        ]);
    }
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Escala - Junta Diaconal IPB</title>
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
                <h3>Gerar Escala (<?= $nomes_meses[(int)$mes] . ' de ' . $ano; ?>)</h3>
                <div class="d-flex align-items-center">
                    
                    <form method="GET" class="d-flex align-items-center me-3">
                        <label for="mes_ano" class="form-label mb-0 me-2 text-muted">Mês:</label>
                        <input type="month" class="form-control form-control-sm me-2" id="mes_ano" name="mes_ano" value="<?= $mes_ano; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Gerar para outro mês</button>
                    </form>
                    
                    <a href="gerar_escala.php?mes_ano=<?= $mes_ano; ?>" class="btn btn-warning text-dark px-3 btn-sm">
                        <i class="bi bi-arrow-repeat"></i> Regerar
                    </a>
                    <a href="?mes_ano=<?= $mes_ano; ?>&export=csv" class="btn btn-success px-3 ms-2 btn-sm">
                        <i class="bi bi-file-earmark-excel"></i> Exportar CSV
                    </a>
                    <button onclick="window.print()" class="btn btn-primary px-3 ms-2 btn-sm">
                        <i class="bi bi-printer"></i> Imprimir / PDF
                    </button>
                </div>
            </div>
        </div>

        <form action="process/processar_salvar_escala.php" method="POST">
            <input type="hidden" name="mes_ano" value="<?= $mes_ano; ?>">
            
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
                                <?php if (!empty($escala_final)): ?>
                                    <?php foreach ($escala_final as $item): ?>
                                        <tr <?= $item['alerta'] ? 'class="alerta"' : ''; ?>>
                                            <td>
                                                <?= date('d/m/Y', strtotime($item['data'])); ?>
                                                <input type="hidden" name="data[]" value="<?= date('d/m/Y', strtotime($item['data'])); ?>">
                                            </td>
                                            <td>
                                                <?= $item['dia_semana']; ?>
                                                <input type="hidden" name="dia_semana[]" value="<?= $item['dia_semana']; ?>">
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($item['periodo']); ?>
                                                <input type="hidden" name="periodo[]" value="<?= htmlspecialchars($item['periodo']); ?>">
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($item['culto']); ?>
                                                <input type="hidden" name="culto[]" value="<?= htmlspecialchars($item['culto']); ?>">
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm fw-semibold text-primary" name="escalado[]">
                                                    <option value="Aguardando escala" <?= $item['escalado'] == 'Aguardando escala' ? 'selected' : '' ?>>
                                                        Aguardando escala
                                                    </option>
                                                    <?php foreach ($all_deacons as $d): ?>
                                                        <option value="<?= htmlspecialchars($d['name']); ?>" <?= $item['escalado'] == $d['name'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($d['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="notas[]" value="<?= htmlspecialchars($item['notas']); ?>" placeholder="Observações...">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            Nenhuma programação encontrada para este mês.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-4 no-print">
                        <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">