<?php
header('Content-Type: text/plain');

echo "=== Teste de Conexão com Banco de Dados ===\n\n";

// Exibir variáveis de ambiente
echo "Variáveis de Ambiente:\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_PORT: " . getenv('DB_PORT') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "RENDER: " . getenv('RENDER') . "\n\n";

// Tentar conexão direta
echo "Tentando conexão PDO...\n";
try {
    $host = getenv('DB_HOST') ?: 'containers-us-west-207.railway.app';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'db_usuario';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '12345678';

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    echo "DSN: $dsn\n";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexão estabelecida com sucesso!\n\n";

    // Testar consulta
    echo "Testando consulta simples...\n";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Resultado da consulta: " . json_encode($result) . "\n";

} catch (PDOException $e) {
    echo "ERRO NA CONEXÃO:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n";
}

// Testar conexão via mysqli
echo "\nTentando conexão mysqli...\n";
try {
    $mysqli = new mysqli($host, $user, $pass, $dbname, $port);
    
    if ($mysqli->connect_errno) {
        echo "Erro mysqli: " . $mysqli->connect_error . "\n";
    } else {
        echo "Conexão mysqli estabelecida com sucesso!\n";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "ERRO mysqli:\n";
    echo $e->getMessage() . "\n";
}

// Testar DNS lookup
echo "\nTestando DNS lookup do host...\n";
$dns = dns_get_record($host, DNS_A);
echo "Registros DNS: " . json_encode($dns, JSON_PRETTY_PRINT) . "\n";

// Testar porta
echo "\nTestando conexão na porta...\n";
$fp = @fsockopen($host, intval($port), $errno, $errstr, 5);
if (!$fp) {
    echo "Erro ao conectar na porta $port: $errno - $errstr\n";
} else {
    echo "Porta $port está acessível!\n";
    fclose($fp);
}
