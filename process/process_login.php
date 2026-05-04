<?php
// process/process_login.php

session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Salvar informações na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['username'] = $user['username'];

            // Redirecionar para o painel ou cadastro
            header('Location: ../index.php');
            exit();
        } else {
            header('Location: ../login.php?error=' . urlencode('Usuário ou senha incorretos.'));
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../login.php?error=' . urlencode('Erro no sistema. Tente novamente mais tarde.'));
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}