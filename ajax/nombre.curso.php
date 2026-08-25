<?php
declare(strict_types=1);

http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'ok' => false,
    'error' => 'ENDPOINT_RETIRADO',
    'mensaje' => 'El endpoint histórico de nombres de Curso ya no está disponible.',
], JSON_UNESCAPED_UNICODE);
