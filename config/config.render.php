<?php
// Configurações específicas para o Render.com

// Forçar HTTPS
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurar charset
ini_set('default_charset', 'UTF-8');

// Configurações de erro para produção
error_reporting(E_ALL);
ini_set('display_errors', '1'); // Temporariamente habilitado para debug
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', '/var/log/php/error.log');

// Aumentar limite de memória se necessário
ini_set('memory_limit', '256M');

// Configurar tempo máximo de execução
ini_set('max_execution_time', '30');

// Configurar tempo máximo de upload
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');

// Debug temporário
error_log('Arquivo config.render.php carregado');
error_log('SERVER variables: ' . print_r($_SERVER, true));
?>
