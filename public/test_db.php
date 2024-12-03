<?php
header('Content-Type: text/plain');

echo "=== Teste de Conexão com Banco de Dados ===\n\n";

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Exibir variáveis de ambiente relevantes
echo "Variáveis de Ambiente:\n";
echo "DB_HOST: " . getEnvVar('DB_HOST', 'db') . "\n";
echo "DB_PORT: " . getEnvVar('DB_PORT', '3306') . "\n";
echo "DB_NAME: " . getEnvVar('DB_NAME', 'db_usuario') . "\n";
echo "DB_USER: " . getEnvVar('DB_USER', 'root') . "\n";
echo "RENDER: " . getEnvVar('RENDER', 'false') . "\n\n";

try {
    echo "Tentando conectar ao banco de dados...\n";
    echo "Host: {$dbConfig['host']}\n";
    echo "Port: {$dbConfig['port']}\n";
    echo "Database: {$dbConfig['name']}\n";
    echo "User: {$dbConfig['user']}\n\n";
    
    // Usar a função do config.php para criar conexão
    $pdo = createPDOConnection($dbConfig);
    echo "CONEXÃO PDO BEM SUCEDIDA!\n\n";
    
    // Testar query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Versão do MySQL: " . $result['version'] . "\n";
    
} catch (PDOException $e) {
    echo "ERRO DE CONEXÃO!\n\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    
    if (!$isProduction) {
        echo "\nStack Trace:\n";
        echo $e->getTraceAsString() . "\n";
        
        // Testar se o host está acessível
        echo "\nTestando conexão com o host...\n";
        $socket = @fsockopen($dbConfig['host'], intval($dbConfig['port']), $errno, $errstr, 5);
        if (!$socket) {
            echo "Erro ao conectar em {$dbConfig['host']}:{$dbConfig['port']}\n";
            echo "Erro $errno: $errstr\n";
        } else {
            echo "Host acessível!\n";
            fclose($socket);
        }
    }
    
    http_response_code(500);
}
