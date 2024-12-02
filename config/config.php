<?php
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'db_usuario';
$user = getenv('DB_USER') ?: 'root';     
$pass = getenv('DB_PASSWORD') ?: 'root';         

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
    
    // Debug da conexão
    error_log("Conectado ao banco de dados em: $host");
} catch(PDOException $e) {
    error_log("Erro na conexão com o banco: " . $e->getMessage());
    die("Erro na conexão: " . $e->getMessage());
}