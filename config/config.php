<?php
// Função para obter variáveis de ambiente com fallback
function getenv_default($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// Configurações do banco de dados
$host = getenv_default('DB_HOST', 'localhost');
$dbname = getenv_default('DB_NAME', 'db_usuario');
$user = getenv_default('DB_USER', 'root');
$pass = getenv_default('DB_PASSWORD', '12345678');

// Configurações de erro
$display_errors = getenv_default('DISPLAY_ERRORS', '1');
$error_reporting = getenv_default('ERROR_REPORTING', 'E_ALL');

// Aplicar configurações de erro
ini_set('display_errors', $display_errors);
error_reporting(constant($error_reporting));

// Log das configurações (sem a senha)
error_log("Configurações de Conexão:");
error_log("Host: $host");
error_log("Database: $dbname");
error_log("User: $user");
error_log("Display Errors: $display_errors");
error_log("Error Reporting: $error_reporting");

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_TIMEOUT => 5, // timeout de 5 segundos
    ];

    // Tentativa de conexão com retry
    $maxRetries = 3;
    $retryDelay = 2; // segundos
    $connected = false;
    $lastError = null;

    for ($i = 0; $i < $maxRetries && !$connected; $i++) {
        try {
            if ($i > 0) {
                error_log("Tentativa de reconexão $i de $maxRetries...");
                sleep($retryDelay);
            }
            
            $pdo = new PDO($dsn, $user, $pass, $options);
            $connected = true;
            error_log("Conexão com o banco de dados estabelecida com sucesso!");
            
        } catch (PDOException $e) {
            $lastError = $e;
            error_log("Tentativa $i falhou: " . $e->getMessage());
        }
    }

    if (!$connected) {
        throw $lastError;
    }

} catch(PDOException $e) {
    $errorMessage = "Erro na conexão com o banco de dados: " . $e->getMessage();
    error_log($errorMessage);
    
    // Em produção, não mostrar detalhes do erro
    if (getenv_default('RENDER', 'false') === 'true') {
        die("Erro interno do servidor. Por favor, tente novamente mais tarde.");
    } else {
        die($errorMessage);
    }
}