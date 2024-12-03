<?php
// Função para obter variável de ambiente com fallback
function getEnvVar($name, $default = null) {
    $value = getenv($name);
    error_log("Lendo variável de ambiente: $name = " . ($value !== false ? $value : 'não definida, usando default: ' . $default));
    return $value !== false ? $value : $default;
}

// Debug inicial
error_log("Iniciando config.php");
error_log("BASE_DIR: " . (defined('BASE_DIR') ? BASE_DIR : 'não definido'));

// Detectar ambiente
$isProduction = getEnvVar('RENDER', 'false') === 'true';
error_log("Ambiente: " . ($isProduction ? 'Produção' : 'Desenvolvimento'));

// Configurações do banco de dados
$dbConfig = [
    'host' => getEnvVar('DB_HOST', 'localhost'),
    'port' => getEnvVar('DB_PORT', '3306'),
    'name' => getEnvVar('DB_NAME', 'db_usuario'),
    'user' => getEnvVar('DB_USER', 'root'),
    'pass' => getEnvVar('DB_PASSWORD', '12345678')
];

// Debug das configurações do banco
error_log("Configurações do banco de dados:");
error_log("Host: " . $dbConfig['host']);
error_log("Port: " . $dbConfig['port']);
error_log("Database: " . $dbConfig['name']);
error_log("User: " . $dbConfig['user']);

// Configurações de erro
if ($isProduction) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1'); // Temporariamente ativado para debug
    ini_set('log_errors', '1');
    ini_set('error_log', '/dev/stderr');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('log_errors', '1');
    ini_set('error_log', BASE_DIR . '/logs/php-error.log');
}

// Debug do PHP
error_log("Versão do PHP: " . PHP_VERSION);
error_log("Configurações de erro:");
error_log("display_errors: " . ini_get('display_errors'));
error_log("error_reporting: " . ini_get('error_reporting'));
error_log("error_log path: " . ini_get('error_log'));

try {
    // Construir DSN com porta
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['name']
    );
    
    error_log("DSN construído: " . $dsn);

    // Opções do PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    // Criar conexão PDO com timeout
    error_log("Tentando conectar ao banco de dados...");
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
    $pdo->setAttribute(PDO::ATTR_TIMEOUT, 5); // 5 segundos de timeout
    error_log("Conexão PDO estabelecida com sucesso!");

    // Testar a conexão
    $pdo->query("SELECT 1");
    error_log("Teste de conexão realizado com sucesso!");
    
} catch (PDOException $e) {
    error_log("Erro PDO: [{$e->getCode()}] {$e->getMessage()} in {$e->getFile()} on line {$e->getLine()}");
    if ($isProduction) {
        http_response_code(500);
        die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
    } else {
        throw $e;
    }
}