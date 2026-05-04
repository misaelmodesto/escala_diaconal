<?php
// process/processar_excecao.php

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exception_date = filter_input(INPUT_POST, 'exception_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $period = filter_input(INPUT_POST, 'period', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $service_name = filter_input(INPUT_POST, 'service_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    $deacon_assigned = !empty($_POST['deacon_assigned']) ? intval($_POST['deacon_assigned']) : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO service_exceptions (exception_date, period, service_name, deacon_assigned) VALUES (:exception_date, :period, :service_name, :deacon_assigned)");
        
        $stmt->execute([
            ':exception_date' => $exception_date,
            ':period' => $period,
            ':service_name' => $service_name,
            ':deacon_assigned' => $deacon_assigned
        ]);
        
        header('Location: ../cadastrar_excecao.php?success=1');
        exit();
        
    } catch (PDOException $e) {
        header('Location: ../cadastrar_excecao.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}
?>