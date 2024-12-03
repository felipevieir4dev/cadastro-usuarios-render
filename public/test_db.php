<?php
header('Content-Type: text/plain');

echo "=== Teste de Conexão com Banco de Dados ===\n\n";

// Debug do ambiente
echo "Ambiente:\n";
echo "RENDER: " . (getenv('RENDER') ? 'true' : 'false') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

// Debug de todas as variáveis de ambiente
echo "Todas as variáveis de ambiente:\n";
$env_vars = getenv();
foreach ($env_vars as $key => $value) {
    // Oculta senhas e informações sensíveis
    if (stripos($key, 'pass') !== false || stripos($key, 'key') !== false || stripos($key, 'secret') !== false) {
        echo "$key: ********\n";
    } else {
        echo "$key: $value\n";
    }
}
echo "\n";

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Debug das configurações do banco
echo "Configurações do Banco:\n";
echo "Host: " . $dbConfig['host'] . "\n";
echo "Port: " . $dbConfig['port'] . "\n";
echo "Database: " . $dbConfig['name'] . "\n";
echo "User: " . $dbConfig['user'] . "\n";
echo "Password: " . (empty($dbConfig['pass']) ? 'VAZIO!' : '********') . "\n\n";

try {
    echo "Tentando conectar ao banco...\n";
    
    // Construir DSN para debug
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['name']
    );
    echo "DSN: $dsn\n\n";
    
    // Usar a função do config.php para criar conexão
    $pdo = createPDOConnection($dbConfig);
    echo "CONEXÃO PDO BEM SUCEDIDA!\n\n";
    
    // Testar query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Versão do MySQL: " . $result['version'] . "\n";
    
} catch (PDOException $e) {
    echo "ERRO DE CONEXÃO!\n\n";
    echo "Código do Erro: " . $e->getCode() . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n\n";
    
    // Testes adicionais de conectividade
    echo "Testes de Conectividade:\n";
    
    // Teste 1: DNS lookup
    echo "1. DNS Lookup do host:\n";
    $dns = gethostbyname($dbConfig['host']);
    echo "IP resolvido: $dns\n";
    if ($dns === $dbConfig['host']) {
        echo "ERRO: Não foi possível resolver o hostname!\n";
    }
    
    // Teste 2: Porta
    echo "\n2. Teste de porta TCP:\n";
    $socket = @fsockopen($dbConfig['host'], intval($dbConfig['port']), $errno, $errstr, 5);
    if (!$socket) {
        echo "ERRO ao conectar na porta {$dbConfig['port']}: $errno - $errstr\n";
    } else {
        echo "Porta {$dbConfig['port']} está acessível!\n";
        fclose($socket);
    }
    
    // Teste 3: Credenciais
    echo "\n3. Verificação de credenciais:\n";
    echo "- User está definido: " . (!empty($dbConfig['user']) ? 'Sim' : 'NÃO!') . "\n";
    echo "- Password está definida: " . (!empty($dbConfig['pass']) ? 'Sim' : 'NÃO!') . "\n";
    echo "- Database está definida: " . (!empty($dbConfig['name']) ? 'Sim' : 'NÃO!') . "\n";
    
    http_response_code(500);
}
