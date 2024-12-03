<?php
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'checks' => [
        'php' => [
            'status' => 'ok',
            'version' => PHP_VERSION
        ],
        'environment' => [
            'status' => 'ok',
            'render' => getenv('RENDER') === 'true'
        ]
    ]
];

// Verificar conexão com o banco
try {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASSWORD');

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3
    ]);
    
    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();
    
    $status['checks']['database'] = [
        'status' => 'ok',
        'connected' => true,
        'host' => $host,
        'port' => $port
    ];
} catch (PDOException $e) {
    $status['checks']['database'] = [
        'status' => 'error',
        'connected' => false,
        'message' => $e->getMessage(),
        'host' => $host,
        'port' => $port
    ];
    $status['status'] = 'error';
}

// Verificar permissões de diretório
$publicDir = __DIR__;
$status['checks']['filesystem'] = [
    'status' => is_writable($publicDir) ? 'ok' : 'error',
    'public_writable' => is_writable($publicDir)
];

if (!is_writable($publicDir)) {
    $status['status'] = 'error';
}

// Verificar extensões necessárias
$requiredExtensions = ['pdo_mysql', 'mysqli', 'json'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

$status['checks']['extensions'] = [
    'status' => empty($missingExtensions) ? 'ok' : 'error',
    'loaded' => get_loaded_extensions(),
    'missing' => $missingExtensions
];

if (!empty($missingExtensions)) {
    $status['status'] = 'error';
}

http_response_code($status['status'] === 'ok' ? 200 : 500);
echo json_encode($status, JSON_PRETTY_PRINT);
