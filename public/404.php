<?php
http_response_code(404);
header('Content-Type: application/json');
echo json_encode([
    'error' => 'Not Found',
    'message' => 'A página solicitada não foi encontrada.',
    'status' => 404
]);
