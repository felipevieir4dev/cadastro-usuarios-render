<?php
// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração de headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Em caso de requisição OPTIONS (preflight), retornar apenas os headers
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/config.php';

$response = array();

function validarSenha($senha) {
    if (strlen($senha) < 8) {
        return "A senha deve ter no mínimo 8 caracteres";
    }
    if (!preg_match('/[0-9]/', $senha)) {
        return "A senha deve conter pelo menos um número";
    }
    if (!preg_match('/[A-Z]/', $senha)) {
        return "A senha deve conter pelo menos uma letra maiúscula";
    }
    if (!preg_match('/[a-z]/', $senha)) {
        return "A senha deve conter pelo menos uma letra minúscula";
    }
    if (!preg_match('/[!@#$%^&*]/', $senha)) {
        return "A senha deve conter pelo menos um caractere especial (!@#$%^&*)";
    }
    return null;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim(htmlspecialchars($_POST['nome'] ?? ''));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            throw new Exception("Todos os campos são obrigatórios!");
        }

        if (strlen($nome) > 100) {
            throw new Exception("Nome deve ter no máximo 100 caracteres!");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido!");
        }

        if (strlen($email) > 100) {
            throw new Exception("Email deve ter no máximo 100 caracteres!");
        }

        // Validação adicional de senha
        $erroSenha = validarSenha($senha);
        if ($erroSenha !== null) {
            throw new Exception($erroSenha);
        }

        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, data_cadastro) VALUES (?, ?, ?, NOW())");

        if ($stmt->execute([$nome, $email, $senha_hash])) {
            $response['status'] = 'success';
            $response['message'] = 'Usuário cadastrado com sucesso!';
        } else {
            throw new Exception("Erro ao cadastrar usuário!");
        }
    } else {
        throw new Exception("Método não permitido!");
    }
} catch (PDOException $e) {
    $response['status'] = 'error';
    if (strpos($e->getMessage(), '1062 Duplicate entry') !== false) {
        $response['message'] = 'Este email já está cadastrado!';
    } else {
        $response['message'] = 'Erro no banco de dados: ' . $e->getMessage();
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
