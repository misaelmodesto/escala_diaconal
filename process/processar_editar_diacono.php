<?php
// process/processar_editar_diacono.php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $cant_odd_days = isset($_POST['cant_odd_days']) ? 1 : 0;
    $cant_even_days = isset($_POST['cant_even_days']) ? 1 : 0;
    $unavailable_days = $_POST['unavailable_days'] ?? []; // Recebe o array de IDs dos dias

    if ($id) {
        try {
            $pdo->beginTransaction();

            // 1. Atualiza os dados principais do diácono
            $stmt = $pdo->prepare("UPDATE deacons SET 
                name = :name, 
                cant_odd_days = :cant_odd_days, 
                cant_even_days = :cant_even_days 
                WHERE id = :id");
            
            $stmt->execute([
                ':name' => $name,
                ':cant_odd_days' => $cant_odd_days,
                ':cant_even_days' => $cant_even_days,
                ':id' => $id
            ]);

            // 2. Remove os impedimentos atuais do diácono para limpar as alterações antigas
            $stmtDelete = $pdo->prepare("DELETE FROM deacon_unavailable_days WHERE deacon_id = :deacon_id");
            $stmtDelete->execute([':deacon_id' => $id]);

            // 3. Insere os novos dias selecionados na tabela de relacionamento
            if (!empty($unavailable_days)) {
                $stmtInsert = $pdo->prepare("INSERT INTO deacon_unavailable_days (deacon_id, day_id) VALUES (:deacon_id, :day_id)");
                foreach ($unavailable_days as $day_id) {
                    $stmtInsert->execute([
                        ':deacon_id' => $id,
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
}
header('Location: ../cadastrar_diacono.php');
exit();