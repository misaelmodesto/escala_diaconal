<?php
// process/processar_usuario.php

session_start();
require_once __DIR__ . '/../config/database.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validação básica se as senhas coincidem
    if ($password !== $confirm_password) {
        header('Location: ../cadastrar_usuario.php?error=' . urlencode('As senhas não coincidem.'));
        exit();
    }

    // Criptografia da senha
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Verifica se o username já existe
        $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $check_stmt->execute([':username' => $username]);
        
        if ($check_stmt->rowCount() > 0) {
            header('Location: ../cadastrar_usuario.php?error=' . urlencode('Este nome de usuário já existe.'));
            exit();
        }

        // Insere o novo usuário
        $stmt = $pdo->prepare("INSERT INTO users (username, password, name) VALUES (:username, :password, :name)");
        
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':name' => $name
        ]);
        
        header('Location: ../cadastrar_usuario.php?success=1');
        exit();
        
    } catch (PDOException $e) {
        header('Location: ../cadastrar_usuario.php?error=' . urlencode('Erro ao cadastrar usuário. Tente novamente.'));
        exit();
    }
} else {
    header('Location: ../cadastrar_usuario.php');
    exit();
}