<?php
require_once __DIR__ . '/../../config/config.php';

try {
    // Verificar conexão com o banco
    if (!isset($pdo)) {
        throw new Exception("Erro: Conexão com o banco de dados não estabelecida");
    }

    // Limpar a tabela
    $stmt = $pdo->prepare("TRUNCATE TABLE usuarios");
    $stmt->execute();
    
    // Redirecionar para a página de listagem
    header('Location: /listar');
    exit;
    
} catch (Exception $e) {
    error_log("Erro ao limpar registros: " . $e->getMessage());
    header('Location: /listar?erro=' . urlencode($e->getMessage()));
    exit;
}
