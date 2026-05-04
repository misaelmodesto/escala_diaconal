<?php
// process/processar_salvar_escala.php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpa a escala anterior para salvar a nova versão
    try {
        $pdo->exec("TRUNCATE TABLE service_scale");

        // Pega os dados enviados pelo formulário
        $datas = $_POST['data'] ?? [];
        $dias_semana = $_POST['dia_semana'] ?? [];
        $periodos = $_POST['periodo'] ?? [];
        $cultos = $_POST['culto'] ?? [];
        $diaconos = $_POST['escalado'] ?? [];
        $notas = $_POST['notas'] ?? [];

        $stmt = $pdo->prepare("INSERT INTO service_scale (scale_date, period, program, deacon_name, notes) VALUES (:scale_date, :period, :program, :deacon_name, :notes)");

        for ($i = 0; $i < count($datas); $i++) {
            // Converte a data de d/m/Y para Y-m-d
            $data_formatada = DateTime::createFromFormat('d/m/Y', $datas[$i])->format('Y-m-d');
            
            $stmt->execute([
                ':scale_date' => $data_formatada,
                ':period' => $periodos[$i] ?? '',
                ':program' => $cultos[$i] ?? '',
                ':deacon_name' => $diaconos[$i] ?? '',
                ':notes' => $notas[$i] ?? ''
            ]);
        }

        header('Location: ../gerar_escala.php?save_success=1');
        exit();
    } catch (PDOException $e) {
        header('Location: ../gerar_escala.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: ../gerar_escala.php');
    exit();
}