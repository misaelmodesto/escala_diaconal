<?php
// process/processar_diacono.php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cant_odd_days = isset($_POST['cant_odd_days']) ? 1 : 0;
    $cant_even_days = isset($_POST['cant_even_days']) ? 1 : 0;
    $unavailable_days = $_POST['unavailable_days'] ?? []; // Recebe um array de IDs

    try {
        $pdo->beginTransaction();

        // 1. Insere o Diácono
        $stmt = $pdo->prepare("INSERT INTO deacons (name, cant_odd_days, cant_even_days) VALUES (:name, :cant_odd_days, :cant_even_days)");
        $stmt->execute([
            ':name' => $name,
            ':cant_odd_days' => $cant_odd_days,
            ':cant_even_days' => $cant_even_days
        ]);
        $deacon_id = $pdo->lastInsertId();

        // 2. Insere os impedimentos na tabela de relacionamento
        if (!empty($unavailable_days)) {
            $stmtRel = $pdo->prepare("INSERT INTO deacon_unavailable_days (deacon_id, day_id) VALUES (:deacon_id, :day_id)");
            foreach ($unavailable_days as $day_id) {
                $stmtRel->execute([
                    ':deacon_id' => $deacon_id,
                    ':day_id' => intval($day_id)
                ]);
            }
        }

        $pdo->commit();
        header('Location: ../cadastrar_diacono.php?success=1');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: ../cadastrar_diacono.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}