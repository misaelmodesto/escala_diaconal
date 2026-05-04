<?php
// process/processar_observacao.php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deacon_id = filter_input(INPUT_POST, 'deacon_id', FILTER_VALIDATE_INT);
    $month_year = filter_input(INPUT_POST, 'month_year', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $observations = filter_input(INPUT_POST, 'observations', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if ($deacon_id && $month_year) {
        try {
            $stmt = $pdo->prepare("INSERT INTO deacon_monthly_notes (deacon_id, month_year, observations) 
                VALUES (:deacon_id, :month_year, :observations)
                ON DUPLICATE KEY UPDATE observations = :observations");
            
            $stmt->execute([
                ':deacon_id' => $deacon_id,
                ':month_year' => $month_year,
                ':observations' => $observations
            ]);

            header('Location: ../cadastrar_diacono.php?success=1');
            exit();
        } catch (PDOException $e) {
            header('Location: ../cadastrar_diacono.php?error=' . urlencode($e->getMessage()));
            exit();
        }
    }
}
header('Location: ../cadastrar_diacono.php');
exit();