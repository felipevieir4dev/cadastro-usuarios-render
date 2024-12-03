<?php
header('Content-Type: text/plain');

echo "=== Teste de Conexão com Banco de Dados ===\n\n";

// Exibir todas as variáveis de ambiente
echo "Todas as Variáveis de Ambiente:\n";
print_r($_ENV);
echo "\n";
print_r($_SERVER);
echo "\n";

// Exibir variáveis específicas do banco
echo "\nVariáveis do Banco de Dados:\n";
echo "DB_HOST: " . getenv('DB_HOST') . "\n";
echo "DB_PORT: " . getenv('DB_PORT') . "\n";
echo "DB_NAME: " . getenv('DB_NAME') . "\n";
echo "DB_USER: " . getenv('DB_USER') . "\n";
echo "RENDER: " . getenv('RENDER') . "\n\n";

// Configurações de conexão
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'db_usuario';
$user = getenv('DB_USER') ?: 'admin';
$pass = getenv('DB_PASSWORD') ?: 'admin123';

echo "Configurações de Conexão:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $dbname\n";
echo "User: $user\n\n";

// Tentar conexão PDO
echo "Tentando conexão PDO...\n";
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
echo "DSN: $dsn\n";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "CONEXÃO PDO BEM SUCEDIDA!\n";
    
    // Testar query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Versão do MySQL: " . $result['version'] . "\n";
    
} catch (PDOException $e) {
    echo "ERRO NA CONEXÃO PDO:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . "\n";
    echo "Linha: " . $e->getLine() . "\n\n";
}

// Tentar conexão mysqli
echo "\nTentando conexão mysqli...\n";
$mysqli = new mysqli($host, $user, $pass, $dbname, intval($port));

if ($mysqli->connect_errno) {
    echo "ERRO mysqli:\n";
    echo $mysqli->connect_error . "\n\n";
} else {
    echo "CONEXÃO MYSQLI BEM SUCEDIDA!\n";
    $result = $mysqli->query("SELECT VERSION() as version");
    $row = $result->fetch_assoc();
    echo "Versão do MySQL: " . $row['version'] . "\n\n";
    $mysqli->close();
}

// Testar DNS
echo "\nTestando DNS lookup do host...\n";
$dns = dns_get_record($host, DNS_A);
echo "Registros DNS: " . json_encode($dns, JSON_PRETTY_PRINT) . "\n\n";

// Testar porta
echo "Testando conexão na porta...\n";
$socket = @fsockopen($host, intval($port), $errno, $errstr, 5);
if (!$socket) {
    echo "Erro ao conectar na porta $port: $errno - $errstr\n";
} else {
    echo "Conexão na porta $port bem sucedida!\n";
    fclose($socket);
}
