<?php
// Definir o diretório base do projeto
define('BASE_DIR', dirname(__DIR__));

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir o arquivo de configuração
require_once BASE_DIR . '/config/config.php';

// Debug
error_log("BASE_DIR: " . BASE_DIR);
error_log("Script Name: " . $_SERVER['SCRIPT_NAME']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);

// Obter a URL requisitada
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Garantir que o path comece com /
if ($path[0] !== '/') {
    $path = '/' . $path;
}

// Debug do path
error_log("Path final: " . $path);

// Roteamento básico
switch ($path) {
    case '/':
    case '/index.php':
        // Página de cadastro
        error_log("Carregando página de cadastro");
        
        // Verificar se é uma submissão de formulário
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require BASE_DIR . '/src/controllers/cadastrar_controller.php';
        } else {
            require BASE_DIR . '/src/views/cadastro.view.php';
        }
        break;
        
    case '/listar.php':
    case '/listar':
        // Página de listagem
        error_log("Carregando página de listagem");
        require BASE_DIR . '/src/controllers/listar_controller.php';
        break;
        
    case '/limpar':
        // Limpar registros
        error_log("Limpando registros");
        require BASE_DIR . '/src/controllers/limpar_controller.php';
        break;
        
    default:
        // Página não encontrada
        http_response_code(404);
        echo "Página não encontrada";
        break;
}
