<?php
// Desativar o buffer de saída
ob_end_clean();

// Configurar cabeçalhos
header('Content-Type: text/plain');

// Informações básicas
echo "=== Teste Básico do PHP ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";

// Verificar permissões
echo "\n=== Permissões ===\n";
echo "Current script permissions: " . substr(sprintf('%o', fileperms(__FILE__)), -4) . "\n";
echo "Document root permissions: " . substr(sprintf('%o', fileperms($_SERVER['DOCUMENT_ROOT'])), -4) . "\n";

// Verificar diretórios
echo "\n=== Diretórios ===\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Can write to current directory: " . (is_writable(getcwd()) ? 'Yes' : 'No') . "\n";

// Verificar extensões
echo "\n=== Extensões PHP ===\n";
$extensions = get_loaded_extensions();
echo "Loaded extensions: " . implode(", ", $extensions) . "\n";

// Verificar variáveis de ambiente
echo "\n=== Variáveis de Ambiente ===\n";
$env_vars = ['RENDER', 'DB_HOST', 'DB_NAME', 'DB_USER', 'PORT'];
foreach ($env_vars as $var) {
    echo "$var: " . (getenv($var) ?: 'não definido') . "\n";
}

// Verificar logs
echo "\n=== Configurações de Log ===\n";
echo "error_log: " . ini_get('error_log') . "\n";
echo "log_errors: " . ini_get('log_errors') . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";

// Tentar escrever no log
error_log("Teste de escrita no log from test.php");

// Verificar conexão com banco
echo "\n=== Teste de Conexão com Banco ===\n";
try {
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s",
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_NAME') ?: 'db_usuario'
    );
    
    $pdo = new PDO(
        $dsn,
        getenv('DB_USER') ?: 'root',
        getenv('DB_PASSWORD') ?: '12345678'
    );
    echo "Conexão com banco de dados: Sucesso\n";
} catch (PDOException $e) {
    echo "Erro na conexão com banco de dados: " . $e->getMessage() . "\n";
}
?>
