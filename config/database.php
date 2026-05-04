<?php
// config/database.php

$config_file = __DIR__ . '/config.local.php';

if (file_exists($config_file)) {
    $db_config = require $config_file;
} else {
    // Fallback se não encontrar o arquivo local
    $db_config = [
        'DB_HOST' => 'localhost',
        'DB_USER' => 'root',
        'DB_PASS' => '',
        'DB_NAME' => 'u513876884_escala_diac',
    ];
}

try {
    $dsn = "mysql:host=" . $db_config['DB_HOST'] . ";dbname=" . $db_config['DB_NAME'] . ";charset=utf8mb4";
    $pdo = new PDO($dsn, $db_config['DB_USER'], $db_config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}