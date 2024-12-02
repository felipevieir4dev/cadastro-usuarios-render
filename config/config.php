<?php
$host = 'localhost';  // Use localhost para conexão local
$dbname = 'db_usuario';
$user = 'root';     
$pass = '12345678';         

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
    error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
    die("Erro na conexão: " . $e->getMessage());
}