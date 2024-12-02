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
    'name' => getEnvVar('DB_NAME', 'db_usuario'),
    'user' => getEnvVar('DB_USER', 'root'),
    'pass' => getEnvVar('DB_PASSWORD', '12345678')
];

// Debug das configurações do banco
error_log("Configurações do banco de dados:");
error_log("Host: " . $dbConfig['host']);
error_log("Database: " . $dbConfig['name']);
error_log("User: " . $dbConfig['user']);

// Configurações de erro (temporariamente mostrando todos os erros em produção para debug)
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', $isProduction ? '/var/log/php/error.log' : BASE_DIR . '/logs/php-error.log');

// Debug do PHP
error_log("Versão do PHP: " . PHP_VERSION);
error_log("Configurações de erro:");
error_log("display_errors: " . ini_get('display_errors'));
error_log("error_reporting: " . ini_get('error_reporting'));
error_log("error_log path: " . ini_get('error_log'));

// Garantir que o diretório de logs existe em desenvolvimento
if (!$isProduction && !is_dir(BASE_DIR . '/logs')) {
    mkdir(BASE_DIR . '/logs', 0755, true);
}

try {
    // Construir DSN
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=utf8",
        $dbConfig['host'],
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

    // Criar conexão PDO
    error_log("Tentando conectar ao banco de dados...");
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
    error_log("Conexão PDO estabelecida com sucesso!");
    
    // Log de debug para conexão bem-sucedida
    if (!$isProduction) {
        error_log(sprintf(
            "Conexão estabelecida com sucesso! Host: %s, Database: %s, User: %s",
            $dbConfig['host'],
            $dbConfig['name'],
            $dbConfig['user']
        ));
    }
    
} catch(PDOException $e) {
    // Log detalhado do erro
    $errorMessage = sprintf(
        "Erro PDO: [%s] %s in %s on line %d",
        $e->getCode(),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    error_log($errorMessage);
    
    // Resposta apropriada baseada no ambiente
    if ($isProduction) {
        http_response_code(500);
        die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
    } else {
        http_response_code(500);
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
} catch(Exception $e) {
    // Log para outros tipos de erro
    error_log("Erro não-PDO: " . $e->getMessage());
    
    if ($isProduction) {
        http_response_code(500);
        die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
    } else {
        http_response_code(500);
        die("Erro inesperado: " . $e->getMessage());
    }
}