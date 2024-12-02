<?php
require_once __DIR__ . '/../../config/config.php';

try {
    // Verificar conexão com o banco
    if (!isset($pdo)) {
        throw new Exception("Erro: Conexão com o banco de dados não estabelecida");
    }

    // Verificar se a tabela existe
    $checkTable = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    if ($checkTable->rowCount() == 0) {
        // Criar a tabela se não existir
        $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    // Buscar usuários
    $stmt = $pdo->query("SELECT nome, email, data_cadastro FROM usuarios ORDER BY data_cadastro DESC");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Usuários encontrados: " . count($usuarios));
    
} catch (Exception $e) {
    error_log("Erro ao listar usuários: " . $e->getMessage());
    $erro = $e->getMessage();
    $usuarios = [];
}

// Incluir a view
require_once BASE_DIR . '/src/views/listar.view.php';
