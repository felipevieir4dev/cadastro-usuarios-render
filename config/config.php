<?php
// Carregar variáveis de ambiente
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'db_usuario';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '12345678';

// Configurar exibição de erros com base nas variáveis de ambiente
$displayErrors = getenv('DISPLAY_ERRORS') ?: '1';
$errorReporting = getenv('ERROR_REPORTING') ?: E_ALL;
ini_set('display_errors', $displayErrors);
error_reporting($errorReporting);

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
    
    // Log de debug para conexão bem-sucedida
    error_log("Conexão com o banco de dados estabelecida com sucesso!");
    error_log("Host: $host, Database: $dbname, User: $user");
    
} catch(PDOException $e) {
    // Log de erro mais detalhado
    error_log("Erro na conexão com o banco de dados:");
    error_log("Message: " . $e->getMessage());
    error_log("Code: " . $e->getCode());
    error_log("File: " . $e->getFile());
    error_log("Line: " . $e->getLine());
    
    // Em produção, não mostrar detalhes sensíveis do erro
    if (getenv('RENDER') === 'true') {
        die("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
    } else {
        die("Erro na conexão: " . $e->getMessage());
    }
}