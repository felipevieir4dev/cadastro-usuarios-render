<?php
http_response_code(500);
header('Content-Type: application/json');
echo json_encode([
    'error' => 'Internal Server Error',
    'message' => 'Ocorreu um erro interno no servidor. Por favor, tente novamente mais tarde.',
    'status' => 500
]);
