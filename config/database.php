<?php

// Verifica se está em ambiente de desenvolvimento (local) ou produção
$isLocal = !getenv('RENDER');

if ($isLocal) {
    // Configuração local
    define('DB_HOST', 'db'); // nome do serviço no docker-compose
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'db_usuario');
} else {
    // Configuração do Render (produção)
    define('DB_HOST', getenv('DB_HOST'));
    define('DB_USER', getenv('DB_USER'));
    define('DB_PASSWORD', getenv('DB_PASSWORD'));
    define('DB_NAME', getenv('DB_NAME'));
}
