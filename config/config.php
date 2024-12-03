<?php
// Função para obter variável de ambiente com fallback
function getEnvVar($name, $default = null) {
    $value = getenv($name);
    return $value !== false ? $value : $default;
}

// Detectar ambiente
$isProduction = getEnvVar('RENDER', 'false') === 'true';

// Configurações de erro
if ($isProduction) {
    error_reporting(E_ALL);
    ini_set('display_errors', getEnvVar('DISPLAY_ERRORS', '0'));
    ini_set('log_errors', '1');
    ini_set('error_log', '/dev/stderr');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/php-error.log');
}

// Configurações do banco de dados
$dbConfig = [
    'host' => getEnvVar('DB_HOST', 'localhost'),
    'port' => getEnvVar('DB_PORT', '3306'),
    'name' => getEnvVar('DB_NAME', 'db_usuario'),
    'user' => getEnvVar('DB_USER', 'root'),
    'pass' => getEnvVar('DB_PASSWORD', '12345678')
];

// Função para criar conexão PDO
function createPDOConnection($config) {
    try {
        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
            $config['host'],
            $config['port'],
            $config['name']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        $pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
        $pdo->setAttribute(PDO::ATTR_TIMEOUT, 5);
        
        // Testar conexão
        $pdo->query("SELECT 1");
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erro PDO: [{$e->getCode()}] {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
        throw $e;
    }
}

// Criar conexão global
try {
    $pdo = createPDOConnection($dbConfig);
} catch (PDOException $e) {
    // Em produção, não mostrar detalhes do erro
    if ($isProduction) {
        http_response_code(500);
        die('Erro de conexão com o banco de dados');
    } else {
        throw $e;
    }
}